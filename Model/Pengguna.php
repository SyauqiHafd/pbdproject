<?php
class Pengguna extends Model
{
    protected $table  = 'pengguna';
    protected $entity = 'pengguna';
    protected $label  = 'Pengguna';

    protected $writeTable = 'user';
    protected $writePk    = ['iduser', 'id'];
    protected $columnMap  = [
        'username' => ['username'],
        'passw'    => ['password', 'passw', 'pass'],
        'role'     => ['idrole', 'role', 'nama_role'],
    ];

    protected function valueForTable(string $field, $value, string $column)
    {
        if ($field === 'role' && preg_match('/^id/i', $column)) {
            $id = array_search((string) $value, array_map('strval', (new Role())->options()), true);
            if ($id === false) {
                throw new InvalidArgumentException('Role tidak ditemukan.');
            }
            return $id;
        }
        return $value;
    }

    public function all()
    {
        $stmt = $this->conn->query("SELECT id, username, role FROM {$this->table} ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $stmt = $this->conn->prepare("SELECT id, username, role FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function findForLogin(string $username)
    {
        $stmt = $this->conn->prepare("SELECT id, username, passw, role FROM {$this->table} WHERE username = :u LIMIT 1");
        $stmt->execute([':u' => $username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function fields(): array
    {
        return [
            ['name' => 'username', 'label' => 'Username', 'type' => 'text', 'required' => true, 'max' => 45],
            ['name' => 'passw', 'label' => 'Password', 'type' => 'password', 'required' => true, 'max' => 72],
            ['name' => 'role',     'label' => 'Role', 'type' => 'select', 'required' => true,
             'options' => (new Role())->names()],
        ];
    }

    protected function prepare(string $mode, array $clean, array $ctx, $id): array
    {
        $sql    = "SELECT COUNT(*) FROM {$this->table} WHERE username = :u" . ($id !== null ? ' AND id <> :id' : '');
        $params = [':u' => $clean['username']];
        if ($id !== null) {
            $params[':id'] = $id;
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        if ((int) $stmt->fetchColumn() > 0) {
            throw new InvalidArgumentException('Username sudah dipakai.');
        }

        if ($id !== null && (string) $id === (string) ($ctx['user_id'] ?? '')) {
            if (Role::kind($clean['role']) !== 'admin') {
                throw new InvalidArgumentException('Role akun yang sedang login harus tetap Admin.');
            }
        }

        if (isset($clean['passw'])) {
            $max = $this->passwordColumnLength();
            if ($max !== null && $max < 60) {
                throw new InvalidArgumentException(
                    "Kolom password hanya {$max} karakter, terlalu pendek untuk hash bcrypt (60). Ubah kolom menjadi VARCHAR(255) dahulu."
                );
            }
            $clean['passw'] = password_hash($clean['passw'], PASSWORD_DEFAULT);
        }
        return [$clean, []];
    }

    protected function guardDelete($id, array $ctx): void
    {
        if ((string) $id === (string) ($ctx['user_id'] ?? '')) {
            throw new InvalidArgumentException('Akun yang sedang login tidak dapat dihapus.');
        }
    }

    private function passwordColumnLength(): ?int
    {
        try {
            $table  = $this->targetTable();
            $column = $this->colFor('passw');
            $stmt = $this->conn->prepare(
                "SELECT CHARACTER_MAXIMUM_LENGTH FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :t AND COLUMN_NAME = :c"
            );
            $stmt->execute([':t' => $table, ':c' => $column]);
            $len = $stmt->fetchColumn();
            return $len === false || $len === null ? null : (int) $len;
        } catch (PDOException $e) {
            return null;
        }
    }
}
