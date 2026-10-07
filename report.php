<?php
declare(strict_types=1);
require __DIR__.'/runtime.php';
$slug=trim((string)($_GET['vertical']??'real-estate'));
$citySlug=current_city_slug();
$areaSlug=current_area_slug();
$city=$cities->find($citySlug);
if(!$city||empty($city['active'])){http_response_code(404);exit('City not available');}
$v=$verticals->find($slug);
if(!$v){http_response_code(404);exit('Category not found');}
$detail=vertical_detail($slug);
$items=$ranking->rank(published_for_vertical($slug,$citySlug,$areaSlug));
$locations=[];foreach($items as $x)$locations[$x['locality']]=($locations[$x['locality']]??0)+1;arsort($locations);
$avg=$items?(int)round(array_sum(array_column($items,'score'))/count($items)):0;
page_header($v['name'].' Ranking Report');
?>
<main id="content" class="shell section report-page">
  <div class="report-actions"><a class="back" href="<?=e(vertical_url($slug,$citySlug,$areaSlug))?>">← Rankings</a><button class="premium-btn primary" onclick="window.print()">Print / Save PDF</button></div>
  <header class="report-head"><span class="eyebrow">ExCompass report · demo edition</span><h1><?=e($v['name'])?> Ranking Report</h1><p>Working dataset · <?=e((string)$city['name'])?> · 7 October 2026 · Intended for product and editorial workflow testing.</p></header>
  <section class="report-kpis"><div><b><?=count($items)?></b><span>Profiles evaluated</span></div><div><b><?=$avg?></b><span>Average score</span></div><div><b><?=e((string)($items[0]['score']??0))?></b><span>Top score</span></div><div><b><?=count($locations)?></b><span>Locations covered</span></div></section>
  <section class="profile-panel"><h2>Ranking table</h2><div class="compare-table-wrap"><table class="compare-table"><thead><tr><th>Rank</th><th><?=e($detail['singular'])?></th><th>Location</th><th><?=e($detail['category_label'])?></th><th>Score</th><th>Rating</th></tr></thead><tbody><?php foreach($items as $x):?><tr><td>#<?=e((string)$x['rank'])?></td><td><b><?=e($x['name'])?></b></td><td><?=e($x['locality'])?></td><td><?=e($x['category'])?></td><td><?=e((string)$x['score'])?></td><td><?=e($x['rating'])?></td></tr><?php endforeach;?></tbody></table></div></section>
  <section class="profile-two-col"><article class="profile-panel"><h2>Coverage by location</h2><?php foreach($locations as $name=>$count):?><div class="report-location"><span><?=e($name)?></span><b><?=$count?></b></div><?php endforeach;?></article><article class="profile-panel"><h2>Methodology note</h2><p>The working report uses the same 100-point score model shown in the public ranking experience. Demo data must be replaced with approved research before publication.</p><a class="text-link dark" href="<?=e(u('methodology.php'))?>">Full methodology →</a></article></section>
</main>
<?php page_footer(); ?>
