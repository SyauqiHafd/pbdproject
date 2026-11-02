<?php
/** Ikon SVG inline (gaya garis, 24x24). */
class Icon
{
    private static $paths = [
        'home'     => '<path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/>',
        'box'      => '<path d="M21 8 12 3 3 8v8l9 5 9-5z"/><path d="m3 8 9 5 9-5M12 13v8"/>',
        'ruler'    => '<path d="M3 17 17 3l4 4L7 21z"/><path d="m7 13 2 2M10 10l2 2M13 7l2 2"/>',
        'building' => '<path d="M4 21V5a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v16"/><path d="M14 9h5a1 1 0 0 1 1 1v11M2 21h20M8 8h2M8 12h2M8 16h2"/>',
        'percent'  => '<path d="M19 5 5 19"/><circle cx="7" cy="7" r="2.5"/><circle cx="17" cy="17" r="2.5"/>',
        'users'    => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M16 4.5a3.5 3.5 0 0 1 0 7M18 14a6.5 6.5 0 0 1 3.5 6"/>',
        'shield'   => '<path d="M12 3 4 6v6c0 4.5 3.2 7.8 8 9 4.8-1.2 8-4.5 8-9V6z"/><path d="m9 12 2 2 4-4"/>',
        'receipt'  => '<path d="M6 3h12v18l-3-2-3 2-3-2-3 2z"/><path d="M9 8h6M9 12h6"/>',
        'search'   => '<circle cx="11" cy="11" r="6.5"/><path d="m20 20-4-4"/>',
        'plus'     => '<path d="M12 5v14M5 12h14"/>',
        'logout'   => '<path d="M15 4h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-3"/><path d="m10 17-5-5 5-5M5 12h11"/>',
        'check'    => '<circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/>',
        'slash'    => '<circle cx="12" cy="12" r="9"/><path d="m5.6 5.6 12.8 12.8"/>',
        'activity' => '<path d="M3 12h4l3-8 4 16 3-8h4"/>',
        'edit'     => '<path d="M4 20h4L19 9l-4-4L4 16z"/>',
        'trash'    => '<path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13M10 11v6M14 11v6"/>',
        'x'        => '<path d="M6 6l12 12M18 6 6 18"/>',
    ];

    public static function svg(string $name, int $size = 18): string
    {
        $body = self::$paths[$name] ?? '';
        return '<svg class="icon" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" '
             . 'stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
             . $body . '</svg>';
    }
}
