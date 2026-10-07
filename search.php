<?php
declare(strict_types=1);
require __DIR__.'/runtime.php';

$q=trim((string)($_GET['q']??''));
$filter=trim((string)($_GET['vertical']??''));
$citySlug=current_city_slug();
$areaSlug=current_area_slug();
$city=$cities->find($citySlug)??$cities->default();
$cityAreas=areas_for_city($citySlug);
$results=$search->search(published_entities($citySlug,$areaSlug),$verticals->all(),$q,$filter?:null);
page_header($q?'Search: '.$q:'Explore ExCompass');
?>
<main id="content" class="shell search-shell">
  <div class="search-hero" data-reveal><div><span class="eyebrow">Discover <?=e((string)$city['name'])?></span><h1><?=$q?'Results for “'.e($q).'”':'What are you looking for?'?></h1><p><?=count($results)?> matching working profiles<?=$areaSlug?' in this area':''?> across active verticals.</p></div></div>
  <form class="search-panel geo-search-panel" method="get" role="search">
    <input type="hidden" name="city" value="<?=e($citySlug)?>">
    <input name="q" value="<?=e($q)?>" aria-label="Search term" placeholder="Search by name, area, need or attribute">
    <select name="area" aria-label="Area"><option value="">All <?=e((string)$city['name'])?></option><?php foreach($cityAreas as $area):?><option value="<?=e($area['slug'])?>" <?=$areaSlug===$area['slug']?'selected':''?>><?=e($area['name'])?></option><?php endforeach;?></select>
    <select name="vertical" aria-label="Category"><option value="">All categories</option><?php foreach($verticals->all() as $v):?><option value="<?=e($v['slug'])?>" <?=$filter===$v['slug']?'selected':''?>><?=e($v['name'])?></option><?php endforeach;?></select>
    <button>Search</button>
  </form>
  <div class="results" data-reveal>
    <?php foreach($results as $x):$v=$verticals->find($x['vertical']);?>
      <a class="result" style="--accent:<?=e($v['accent'])?>" href="<?=e(entity_url($x['vertical'],$x['slug'],$citySlug))?>"><span class="icon-tile"><?=icon_svg($v['icon'])?></span><div><small><?=e($v['name'])?> · <?=e((string)$city['name'])?></small><b><?=e($x['name'])?></b><p><?=e($x['area_name'])?> · <?=e($x['highlight'])?></p></div><strong><?=e((string)$x['score'])?></strong></a>
    <?php endforeach;?>
    <?php if(!$results):?><div class="empty">No match yet. Try another area, category or broader need.</div><?php endif;?>
  </div>
</main>
<?php page_footer(); ?>
