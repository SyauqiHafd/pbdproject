<?php

class Model
{
    protected $conn;
    protected $table;                 
    protected $primaryKey = 'id';     
    protected $orderDir   = 'ASC';
    protected $entity     = '';
    protected $label      = '';

    protected $writeTable = null;     
    protected $writePk    = [];       
    protected $columnMap  = [];       

    private static $schemaCache = [];

    public function __construct()
    {
        $db         = new Database();
        $this->conn = $db->connect();
    }

    public function entity(): string     { return $this->entity; }
    public function label(): string      { return $this->label; }
    public function primaryKey(): string { return $this->primaryKey; }

    public static function transaksiSiap(): bool
    {
        static $siap = null;
        if ($siap !== null) {
            return $siap;
        }
        try {
            $conn = (new Database())->connect();
            $rutin = ['fn_persen_ppn', 'fn_hitung_ppn', 'fn_stok_barang', 'fn_id_margin_aktif', 'fn_harga_jual',
                      'sp_hitung_pengadaan', 'sp_hitung_penjualan', 'sp_buat_pengadaan', 'sp_tambah_detail_pengadaan',
                      'sp_proses_penerimaan', 'sp_buat_penjualan', 'sp_tambah_detail_penjualan'];
            $trigger = ['trg_detail_pengadaan_bi', 'trg_detail_pengadaan_ai', 'trg_detail_penerimaan_ai',
                        'trg_detail_penjualan_bi', 'trg_detail_penjualan_ai'];

            $q = $conn->prepare('SELECT COUNT(*) FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_NAME IN ('
                . implode(',', array_fill(0, count($rutin), '?')) . ')');
            $q->execute($rutin);
            $nRutin = (int) $q->fetchColumn();

            $q = $conn->prepare('SELECT COUNT(*) FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA = DATABASE() AND TRIGGER_NAME IN ('
                . implode(',', array_fill(0, count($trigger), '?')) . ')');
            $q->execute($trigger);
            $nTrigger = (int) $q->fetchColumn();

            $siap = $nRutin === count($rutin) && $nTrigger === count($trigger);
        } catch (PDOException $e) {
            $siap = false;
        }
        return $siap;
    }

    public function all()
    {
        $stmt = $this->conn->query("SELECT * FROM {$this->table} ORDER BY {$this->primaryKey} {$this->orderDir}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function count(string $where = '1=1', array $params = []): int
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM {$this->table} WHERE {$where}");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function fields(): array
    {
        return [];
    }

    public function rowForForm(array $row): array
    {
        $out = [];
        foreach ($this->fields() as $f) {
            if ($f['type'] === 'password') {
                continue;
            }
            $out[$f['name']] = $row[$f['name']] ?? '';
        }
        $out['__id'] = $row[$this->primaryKey] ?? '';
        return $out;
    }


    public function save($id, array $input, array $ctx = []): void
    {
        $mode  = $id === null ? 'create' : 'update';
        $clean = $this->validate($input, $mode);
        [$data, $raw] = $this->prepare($mode, $clean, $ctx, $id);

        if ($mode === 'create') {
            $this->insertRow($data, $raw);
        } else {
            $this->updateRow($id, $data, $raw);
        }
    }

    public function remove($id, array $ctx = []): void
    {
        $this->guardDelete($id, $ctx);
        $sql  = 'DELETE FROM ' . $this->q($this->targetTable()) . ' WHERE ' . $this->q($this->targetPk()) . ' = :id';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        if ($stmt->rowCount() === 0) {
            throw new InvalidArgumentException('Data tidak ditemukan.');
        }
    }

    protected function prepare(string $mode, array $clean, array $ctx, $id): array
    {
        return [$clean, []];
    }

    protected function guardDelete($id, array $ctx): void
    {
    }

    protected function valueForTable(string $field, $value, string $column)
    {
        return $value;
    }

    protected function validate(array $input, string $mode): array
    {
        $errors = [];
        $clean  = [];

        foreach ($this->fields() as $f) {
            $name  = $f['name'];
            $label = $f['label'];
            $value = trim((string) ($input[$name] ?? ''));

            if ($f['type'] === 'password' && $mode === 'update' && $value === '') {
                continue; // password dikosongkan saat edit = tidak diubah
            }
            if ($value === '') {
                if (!empty($f['required'])) {
                    $errors[] = "$label wajib diisi.";
                }
                continue;
            }

            switch ($f['type']) {
                case 'select':
                    if (!in_array($value, array_map('strval', array_keys($f['options'])), true)) {
                        $errors[] = "$label tidak valid.";
                        continue 2;
                    }
                    break;
                case 'number':
                    if (!is_numeric($value)) {
                        $errors[] = "$label harus berupa angka.";
                        continue 2;
                    }
                    if (isset($f['min']) && $value < $f['min']) {
                        $errors[] = "$label minimal {$f['min']}.";
                        continue 2;
                    }
                    if (isset($f['max']) && $value > $f['max']) {
                        $errors[] = "$label maksimal {$f['max']}.";
                        continue 2;
                    }
                    $value = ($f['cast'] ?? '') === 'int' ? (int) round((float) $value) : (float) $value;
                    break;
                default: // text, password
                    if (isset($f['max']) && Str::len($value) > $f['max']) {
                        $errors[] = "$label maksimal {$f['max']} karakter.";
                        continue 2;
                    }
            }
            $clean[$name] = $value;
        }

        if ($errors) {
            throw new InvalidArgumentException(implode(' ', $errors));
        }
        return $clean;
    }


    protected function q(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }

    protected function targetTable(): string
    {
        return $this->writeTable ?: $this->table;
    }

    protected function schema(): array
    {
        $t = $this->targetTable();
        if (!isset(self::$schemaCache[$t])) {
            try {
                $rows = $this->conn->query('SHOW COLUMNS FROM ' . $this->q($t))->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                throw new InvalidArgumentException("Tabel '{$t}' tidak ditemukan di database. Periksa nama tabel pada properti \$writeTable.");
            }
            $cols = [];
            foreach ($rows as $r) {
                $cols[strtolower($r['Field'])] = [
                    'name' => $r['Field'],
                    'auto' => stripos((string) $r['Extra'], 'auto_increment') !== false,
                ];
            }
            self::$schemaCache[$t] = $cols;
        }
        return self::$schemaCache[$t];
    }

    private function pickColumn(array $candidates, string $what): string
    {
        $schema = $this->schema();
        foreach ($candidates as $c) {
            if (isset($schema[strtolower($c)])) {
                return $schema[strtolower($c)]['name'];
            }
        }
        $ada = implode(', ', array_column($schema, 'name'));
        throw new InvalidArgumentException(
            "Kolom untuk {$what} tidak ditemukan di tabel '{$this->targetTable()}' (dicari: " . implode('/', $candidates)
            . "). Kolom yang ada: {$ada}. Sesuaikan \$columnMap / \$writePk pada class " . static::class . '.'
        );
    }

    protected function colFor(string $field): string
    {
        if (!$this->writeTable) {
            return $field;
        }
        return $this->pickColumn($this->columnMap[$field] ?? [$field], "'{$field}'");
    }

    protected function targetPk(): string
    {
        if (!$this->writeTable) {
            return $this->primaryKey;
        }
        return $this->pickColumn($this->writePk ?: [$this->primaryKey], 'kunci utama');
    }


    private function insertRow(array $data, array $raw): void
    {
        $cols = [];   
        foreach ($data as $field => $value) {
            $col        = $this->colFor($field);
            $cols[$col] = $this->valueForTable($field, $value, $col);
        }
        $exprs = []; 
        foreach ($raw as $field => $expr) {
            $exprs[$this->colFor($field)] = $expr;
        }

        // kunci utama bukan AUTO_INCREMENT -> buat id berikutnya sendiri
        $needsId = false;
        if ($this->writeTable) {
            $pk = $this->targetPk();
            $needsId = empty($this->schema()[strtolower($pk)]['auto']);
        }

        $table = $this->q($this->targetTable());
        $run = function (?int $newId) use ($cols, $exprs, $table) {
            $names  = [];
            $marks  = [];
            $params = [];
            if ($newId !== null) {
                $names[]  = $this->q($this->targetPk());
                $marks[]  = '?';
                $params[] = $newId;
            }
            foreach ($cols as $c => $v) {
                $names[]  = $this->q($c);
                $marks[]  = '?';
                $params[] = $v;
            }
            foreach ($exprs as $c => $expr) {
                $names[] = $this->q($c);
                $marks[] = $expr;
            }
            $sql = "INSERT INTO {$table} (" . implode(', ', $names) . ') VALUES (' . implode(', ', $marks) . ')';
            $this->conn->prepare($sql)->execute($params);
        };

        if (!$needsId) {
            $run(null);
            return;
        }

        $this->conn->beginTransaction();
        try {
            $stmt = $this->conn->query('SELECT COALESCE(MAX(' . $this->q($this->targetPk()) . "), 0) + 1 FROM {$table} FOR UPDATE");
            $run((int) $stmt->fetchColumn());
            $this->conn->commit();
        } catch (Throwable $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    private function updateRow($id, array $data, array $raw): void
    {
        $set    = [];
        $params = [];
        foreach ($data as $field => $value) {
            $col      = $this->colFor($field);
            $set[]    = $this->q($col) . ' = ?';
            $params[] = $this->valueForTable($field, $value, $col);
        }
        foreach ($raw as $field => $expr) {
            $set[] = $this->q($this->colFor($field)) . ' = ' . $expr;
        }
        $params[] = $id;

        $sql  = 'UPDATE ' . $this->q($this->targetTable()) . ' SET ' . implode(', ', $set)
              . ' WHERE ' . $this->q($this->targetPk()) . ' = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        if ($stmt->rowCount() === 0) {
            throw new InvalidArgumentException('Data tidak ditemukan.');
        }
    }
}
