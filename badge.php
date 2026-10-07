<?php
declare(strict_types=1);
require __DIR__.'/runtime.php';
$vs=trim((string)($_GET['vertical']??''));$slug=trim((string)($_GET['slug']??''));$citySlug=current_city_slug();
$v=$verticals->find($vs);$x=find_published_entity($vs,$slug,$citySlug);
if(!$v||!$x){http_response_code(404);exit('Not found');}
$name=preg_replace('/[^a-z0-9-]+/i','-',$x['slug']).'-excompass-badge.svg';
header('Content-Type: image/svg+xml; charset=UTF-8');header('Content-Disposition: attachment; filename="'.$name.'"');
$title=e($x['name']);$score=e((string)$x['score']);$category=e($v['name']);
?><svg xmlns="http://www.w3.org/2000/svg" width="720" height="360" viewBox="0 0 720 360"><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#090b0d"/><stop offset="1" stop-color="#1a2028"/></linearGradient></defs><rect width="720" height="360" rx="28" fill="url(#g)"/><rect x="18" y="18" width="684" height="324" rx="22" fill="none" stroke="#d6b77a" stroke-opacity=".35" stroke-width="2"/><text x="48" y="78" fill="#fff" font-family="Arial" font-weight="900" font-size="32"><tspan fill="#ef3d4f">Ex</tspan>Compass</text><text x="48" y="118" fill="#d6b77a" font-family="Arial" font-size="18" font-weight="700">EDITORIAL INDEX · <?= $category ?></text><circle cx="590" cy="175" r="72" fill="#111820"/><circle cx="590" cy="175" r="64" fill="none" stroke="#d6b77a" stroke-width="8"/><text x="590" y="190" text-anchor="middle" fill="#fff" font-family="Arial" font-weight="900" font-size="48"><?= $score ?></text><text x="48" y="190" fill="#fff" font-family="Arial" font-weight="900" font-size="30"><?= $title ?></text><text x="48" y="230" fill="#cbd5e1" font-family="Arial" font-size="18">ExCompass Score · Demo badge</text><text x="48" y="302" fill="#94a3b8" font-family="Arial" font-size="14">Editorial score remains independent of commercial placement.</text></svg>
