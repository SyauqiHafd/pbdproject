<?php

class Model
{
    protected $conn;
    protected $table;
    protected $writeTable = null;
    protected $primaryKey = 'id';
    protected $entity     = '';
    protected $label      = '';

    public function __construct()
    {
        $db         = new Database();
        $this->conn = $db->connect();
    }

    public function entity(): string     { return $this->entity; }
    public function label(): string      { return $this->label; }
    public function primaryKey(): string { return $this->primaryKey; }

    protected function target(): string
    {
        return $this->writeTable ?: $this->table;
    }

    /* ------------------------------ READ ------------------------------ */

    public function all()
    {
        $stmt = $this->conn->query("SELECT * FROM {$this->table} ORDER BY {$this->primaryKey} ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /** SELECT COUNT(*) FROM view*/
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

    /* ------------------------------ WRITE ----------------------------- */

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
        $stmt = $this->conn->prepare("DELETE FROM {$this->target()} WHERE {$this->primaryKey} = :id");
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

    private function insertRow(array $data, array $raw): void
    {
        $cols   = array_merge(array_keys($data), array_keys($raw));
        $values = array_merge(array_map(function ($c) { return ':' . $c; }, array_keys($data)), array_values($raw));
        $sql    = "INSERT INTO {$this->target()} (" . implode(', ', $cols) . ') VALUES (' . implode(', ', $values) . ')';
        $params = [];
        foreach ($data as $k => $v) {
            $params[':' . $k] = $v;
        }
        $this->conn->prepare($sql)->execute($params);
    }

    private function updateRow($id, array $data, array $raw): void
    {
        $set    = [];
        $params = [':__id' => $id];
        foreach ($data as $k => $v) {
            $set[] = "$k = :$k";
            $params[':' . $k] = $v;
        }
        foreach ($raw as $k => $expr) {
            $set[] = "$k = $expr";
        }
        $sql  = "UPDATE {$this->target()} SET " . implode(', ', $set) . " WHERE {$this->primaryKey} = :__id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        if ($stmt->rowCount() === 0) {
            throw new InvalidArgumentException('Data tidak ditemukan.');
        }
    }
}
