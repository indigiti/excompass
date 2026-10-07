<?php
declare(strict_types=1);
require __DIR__.'/runtime.php';

$citySlug=current_city_slug();
$areaSlug=current_area_slug()??'';
$city=$cities->find($citySlug);
$area=$areas->find($entities->all(),$citySlug,$areaSlug);
if(!$city||!$area){http_response_code(404);page_header('Area not found');echo '<main id="content" class="shell section"><h1>Area not found</h1></main>';page_footer();exit;}

$items=published_entities($citySlug,$areaSlug);
$byVertical=[];
foreach($items as $entity){$byVertical[$entity['vertical']][]=$entity;}
$top=$items;usort($top,static fn(array $a,array $b):int=>$b['score']<=>$a['score']);
page_header($area['name'].' · '.$city['name']);
?>
<main id="content">
<section class="page-hero area-hero"><div class="shell page-hero-inner">
  <div data-reveal><span class="eyebrow"><?=e($city['name'])?> · Area intelligence</span><h1><?=e($area['name'])?></h1><p>Drill into <?=e($area['name'])?> across every active ExCompass category without mixing results from other parts of <?=e($city['name'])?>.</p></div>
  <div class="page-stat"><strong><?=count($items)?></strong><span>Profiles in this area</span></div>
</div></section>
<section class="shell section">
  <div class="section-head"><div><span class="eyebrow">Categories in <?=e($area['name'])?></span><h2>Decisions available here.</h2></div><p>Only categories with working profiles in this area are shown.</p></div>
  <div class="vertical-grid" data-reveal><?php foreach($verticals->all() as $v):$count=count($byVertical[$v['slug']]??[]);if(!$count)continue;?><a class="vertical-card" style="--accent:<?=e($v['accent'])?>" href="<?=e(area_url($citySlug,$areaSlug,$v['slug']))?>"><span class="icon-tile"><?=icon_svg($v['icon'])?></span><div><b><?=e($v['name'])?></b><small><?=$count?> in <?=e($area['name'])?></small><em>Open area-specific ranking</em></div><i>→</i></a><?php endforeach;?></div>
</section>
<section class="section section-soft"><div class="shell">
  <div class="section-head"><div><span class="eyebrow">Highest current scores</span><h2>Across <?=e($area['name'])?>.</h2></div></div>
  <div class="results" data-reveal><?php foreach(array_slice($top,0,8) as $x):$v=$verticals->find($x['vertical']);?><a class="result" style="--accent:<?=e($v['accent'])?>" href="<?=e(entity_url($x['vertical'],$x['slug'],$citySlug))?>"><span class="icon-tile"><?=icon_svg($v['icon'])?></span><div><small><?=e($v['name'])?></small><b><?=e($x['name'])?></b><p><?=e($area['name'])?> · <?=e($x['highlight'])?></p></div><strong><?=e((string)$x['score'])?></strong></a><?php endforeach;?></div>
</div></section>
</main>
<?php page_footer(); ?>
