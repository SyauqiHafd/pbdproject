<?php
class Html
{
    public static function e($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    public static function json($value): string
    {
        return self::e(json_encode($value, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG));
    }

    public static function rowActions(string $entity, array $formRow, $id, string $name): string
    {
        return '<td class="px-4 py-2 actions">'
             . '<button type="button" class="link-btn" data-edit="' . self::e($entity) . '" data-row="' . self::json($formRow) . '">Edit</button>'
             . '<button type="button" class="link-btn link-danger" data-delete="' . self::e($entity)
             . '" data-id="' . self::e($id) . '" data-name="' . self::e($name) . '">Hapus</button>'
             . '</td>';
    }

    public static function detailAttr(string $title, string $sub, array $lines, array $totals): string
    {
        $rows = [];
        foreach ($lines as $l) {
            $rows[] = [(string) $l['nama'], Label::angka($l['jumlah']), Label::rupiah($l['harga']), Label::rupiah($l['subtotal'])];
        }
        return self::json(['title' => $title, 'sub' => $sub, 'lines' => $rows, 'totals' => $totals]);
    }
}
