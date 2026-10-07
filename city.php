<?php
declare(strict_types=1);
require __DIR__.'/runtime.php';

$citySlug=current_city_slug();
$city=$cities->find($citySlug);
if(!$city||empty($city['active'])){http_response_code(404);page_header('City not available');echo '<main id="content" class="shell section"><h1>City not available</h1></main>';page_footer();exit;}

$profilesInCity=published_entities($citySlug);
$cityAreas=areas_for_city($citySlug);
$verticalCounts=[];
foreach($profilesInCity as $entity){$verticalCounts[$entity['vertical']]=($verticalCounts[$entity['vertical']]??0)+1;}
page_header($city['name'].' city guide');
?>
<main id="content">
<section class="page-hero city-hero"><div class="shell page-hero-inner">
  <div data-reveal><span class="eyebrow">City intelligence</span><h1><?=e($city['name'])?></h1><p>Explore <?=e($city['name'])?> by area and decision category. The architecture is multi-city ready; <?=e($city['name'])?> is currently the only active city dataset.</p></div>
  <div class="page-stat"><strong><?=count($profilesInCity)?></strong><span>Profiles in <?=e($city['name'])?></span></div>
</div></section>
<section class="shell section">
  <div class="section-head"><div><span class="eyebrow">Drill down by area</span><h2>Neighbourhoods and localities.</h2></div><p>Area context stays inside the city boundary, so future cities can reuse the same locality names without data collisions.</p></div>
  <div class="area-grid" data-reveal>
    <?php foreach($cityAreas as $area):?>
      <a class="area-card" href="<?=e(area_url($citySlug,$area['slug']))?>"><div><span class="eyebrow"><?=e($city['name'])?></span><h3><?=e($area['name'])?></h3><p><?=e((string)$area['count'])?> working profiles · <?=count($area['verticals'])?> categories</p></div><i>→</i></a>
    <?php endforeach;?>
  </div>
</section>
<section class="section section-soft"><div class="shell">
  <div class="section-head"><div><span class="eyebrow">Explore by category</span><h2><?=e($city['name'])?> decision verticals.</h2></div></div>
  <div class="vertical-grid" data-reveal><?php foreach($verticals->all() as $v):$count=$verticalCounts[$v['slug']]??0;if(!$count)continue;?><a class="vertical-card" style="--accent:<?=e($v['accent'])?>" href="<?=e(vertical_url($v['slug'],$citySlug))?>"><span class="icon-tile"><?=icon_svg($v['icon'])?></span><div><b><?=e($v['name'])?></b><small><?=$count?> profiles in <?=e($city['name'])?></small><em><?=e($v['prompt'])?></em></div><i>→</i></a><?php endforeach;?></div>
</div></section>
</main>
<?php page_footer(); ?>
