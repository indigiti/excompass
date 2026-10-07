<?php
declare(strict_types=1);
require __DIR__.'/runtime.php';
$vs=trim((string)($_GET['vertical']??''));$slug=trim((string)($_GET['slug']??''));
$v=$verticals->find($vs);$x=find_published_entity($vs,$slug);
if(!$v||!$x){http_response_code(404);exit('Not found');}
$detail=vertical_detail($vs);
$filename=preg_replace('/[^a-z0-9-]+/i','-',$x['slug']).'-excompass-brief.html';
header('Content-Type: text/html; charset=UTF-8');
header('Content-Disposition: attachment; filename="'.$filename.'"');
?><!doctype html><html><head><meta charset="utf-8"><title><?=e($x['name'])?> — ExCompass Brief</title><style>body{font-family:Arial,sans-serif;max-width:900px;margin:40px auto;color:#12161d;line-height:1.55}h1{font-family:Georgia,serif;font-size:38px;margin-bottom:4px}.brand{font-size:20px;font-weight:900}.brand span{color:#ef3d4f}.score{font:700 58px Georgia,serif}.grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}.box{border:1px solid #ddd;border-radius:14px;padding:20px}small{color:#68717d}li{margin:7px 0}</style></head><body><div class="brand"><span>Ex</span>Compass</div><h1><?=e($x['name'])?></h1><p><?=e($v['name'])?> · <?=e($x['locality'])?> · Pune</p><div class="grid"><div class="box"><div class="score"><?=e((string)$x['score'])?></div><b><?=e($x['rating'])?></b><p>ExCompass Score · <?=e($x['score_version'])?></p></div><div class="box"><b><?=e($detail['category_label'])?></b><p><?=e($x['category'])?></p><b><?=e($detail['tier_label'])?></b><p><?=e($x['tier'])?></p></div></div><h2>ExCompass Observation</h2><p><?=e($x['observation'])?></p><h2>Score Breakdown</h2><ul><?php foreach($x['breakdown'] as $row):?><li><?=e($row['label'])?> — <b><?=e((string)$row['score'])?> / <?=e((string)$row['max'])?></b></li><?php endforeach;?></ul><h2>Disclosure</h2><p><small>Demo brief generated from working data. Replace with approved research and verified information before publication.</small></p></body></html>
