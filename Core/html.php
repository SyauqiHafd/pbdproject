<?php
class Html
{
    public static function e($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    /** JSON aman untuk ditaruh di dalam atribut HTML (data-row='...') */
    public static function json($value): string
    {
        return self::e(json_encode($value, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG));
    }

    /** Sel "Aksi": tombol Edit (buka modal ubah) dan Hapus (buka modal konfirmasi). */
    public static function rowActions(string $entity, array $formRow, $id, string $name): string
    {
        return '<td class="px-4 py-2 actions">'
             . '<button type="button" class="link-btn" data-edit="' . self::e($entity) . '" data-row="' . self::json($formRow) . '">Edit</button>'
             . '<button type="button" class="link-btn link-danger" data-delete="' . self::e($entity)
             . '" data-id="' . self::e($id) . '" data-name="' . self::e($name) . '">Hapus</button>'
             . '</td>';
    }
}
