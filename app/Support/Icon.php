<?php
declare(strict_types=1);
namespace ExCompass\Support;

final class Icon {
    public static function svg(string $key): string {
        $paths=[
            'home'=>'<path d="M3 11.5 12 4l9 7.5"/><path d="M5.5 10.5V20h13v-9.5"/><path d="M9.5 20v-6h5v6"/>',
            'hospital'=>'<path d="M9 3h6v6h6v6h-6v6H9v-6H3V9h6z"/>',
            'school'=>'<path d="m3 9 9-5 9 5-9 5z"/><path d="M7 12v5c3 2 7 2 10 0v-5"/>',
            'food'=>'<path d="M7 3v8M4 3v5c0 2 6 2 6 0V3M7 11v10"/><path d="M16 3c3 3 3 8 0 11v7"/>',
            'hotel'=>'<path d="M4 20V6h16v14M4 15h16"/><path d="M7 9h3v3H7zm7 0h3v3h-3z"/>',
            'college'=>'<path d="m2 8 10-5 10 5-10 5z"/><path d="M5 11v6h14v-6M3 20h18"/>',
            'mall'=>'<path d="M5 8h14l-1 13H6z"/><path d="M8 9a4 4 0 0 1 8 0"/>',
            'gym'=>'<path d="M3 9v6m4-9v12m10-12v12m4-9v6M7 12h10"/>',
            'salon'=>'<circle cx="6" cy="18" r="3"/><circle cx="18" cy="18" r="3"/><path d="m8.5 16.5 8-13M15.5 16.5l-8-13"/>',
            'auto'=>'<path d="M4 16v-5l2-5h12l2 5v5"/><path d="M3 16h18v3H3z"/><circle cx="7" cy="13" r="1"/><circle cx="17" cy="13" r="1"/>',
            'cowork'=>'<path d="M4 20v-9h16v9M8 11V7h8v4M3 20h18"/><path d="M9 15h6"/>',
            'locality'=>'<path d="M12 22s7-6 7-13a7 7 0 1 0-14 0c0 7 7 13 7 13z"/><circle cx="12" cy="9" r="2.5"/>',
            'preschool'=>'<path d="m12 3 2.2 4.5 5 .7-3.6 3.5.9 5-4.5-2.3-4.5 2.3.9-5L4.8 8.2l5-.7z"/>',
            'doctor'=>'<circle cx="12" cy="7" r="4"/><path d="M5 21c0-4 3-7 7-7s7 3 7 7M12 17v4M10 19h4"/>',
            'banquet'=>'<path d="M4 20h16M6 20v-8h12v8M8 12V7h8v5"/><path d="M10 7V4h4v3"/>',
            'cafe'=>'<path d="M4 8h13v8a5 5 0 0 1-5 5H9a5 5 0 0 1-5-5z"/><path d="M17 10h2a3 3 0 0 1 0 6h-2M7 3v2m4-2v2m4-2v2"/>',
            'weekend'=>'<path d="M4 19h16L14 8l-3 5-2-3z"/><circle cx="18" cy="5" r="2"/>',
        ];
        $body=$paths[$key]??$paths['locality'];
        return '<svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">'.$body.'</svg>';
    }
}
