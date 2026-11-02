<?php
class AuthController
{
    /** Pengguna yang sedang login: [id, username, role (admin|staff), role_name] atau null. */
    public function user(): ?array
    {
        return Session::get('user');
    }

    /** Halaman wajib login dengan role tertentu; selain itu dialihkan. */
    public function requireRole(string $role): array
    {
        $user = $this->user();
        if (!$user) {
            header('Location: login.php');
            exit;
        }
        if ($user['role'] !== $role) {
            header('Location: ' . self::homeFor($user));
            exit;
        }
        return $user;
    }

    public static function homeFor(array $user): string
    {
        return $user['role'] === 'admin' ? 'dashboard_admin.php' : 'dashboard_kasir.php';
    }

    /** @return array ['ok' => bool, 'error' => ?string] */
    public function login(string $username, string $password): array
    {
        if ($username === '' || $password === '') {
            return ['ok' => false, 'error' => 'Username dan password wajib diisi.'];
        }

        try {
            $row = (new Pengguna())->findForLogin($username);
        } catch (PDOException $e) {
            if (($e->errorInfo[1] ?? 0) === 1054) {
                return ['ok' => false, 'error' => "Kolom 'password' tidak ditemukan pada view/tabel pengguna. Lihat README (bagian Login)."];
            }
            return ['ok' => false, 'error' => 'Gagal membaca data pengguna: ' . $e->getMessage()];
        }

        if (!$row || !self::verifyPassword($password, (string) $row['passw'])) {
            usleep(400000); // perlambat tebakan password
            return ['ok' => false, 'error' => 'Username atau password salah.'];
        }

        $roles    = (new Role())->options();
        $roleName = (string) $row['role'];
        $kind     = Role::kind($roleName);
        if (ctype_digit($roleName) && isset($roles[$roleName])) {
            $roleName = $roles[$roleName];
        }
        $kind = Role::kind($roleName);

        Session::regenerate();
        Session::set('user', [
            'id'        => (int) $row['id'],
            'username'  => $row['username'],
            'role'      => $kind,
            'role_name' => $roleName,
        ]);
        return ['ok' => true, 'error' => null];
    }

    public function logout(): void
    {
        Session::destroy();
    }

    private static function verifyPassword(string $input, string $stored): bool
    {
        if ($stored === '') {
            return false;
        }
        if (preg_match('/^\$(2[abxy]|argon2i|argon2id)\$/', $stored)) {
            return password_verify($input, $stored);
        }
        if (ctype_xdigit($stored)) {
            $len = strlen($stored);
            if ($len === 32) {
                return hash_equals(strtolower($stored), md5($input));
            }
            if ($len === 40) {
                return hash_equals(strtolower($stored), sha1($input));
            }
            if ($len === 64) {
                return hash_equals(strtolower($stored), hash('sha256', $input));
            }
        }
        return hash_equals($stored, $input);
    }
}
