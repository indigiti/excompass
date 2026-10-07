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
$all=$ranking->rank(published_for_vertical($slug,$citySlug,$areaSlug));
$wanted=array_slice(array_values(array_filter(explode(',',(string)($_GET['items']??'')))),0,3);
$items=[];
foreach($wanted as $wantedSlug){foreach($all as $x){if($x['slug']===$wantedSlug){$items[]=$x;break;}}}
if(count($items)<2)$items=array_slice($all,0,3);
page_header('Compare '.$v['name']);
?>
<main id="content" class="shell section compare-page">
  <a class="back" href="<?=e(vertical_url($slug,$citySlug,$areaSlug))?>">← Back to rankings</a>
  <div class="section-head"><div><span class="eyebrow">Comparison workspace</span><h1>Compare <?=e($v['name'])?></h1></div><p>Up to three working profiles, aligned by category attributes and score criteria.</p></div>
  <div class="compare-cards"><?php foreach($items as $x):?><article class="compare-card"><div class="compare-visual skin-<?=($x['rank']%4)+1?>"><?php if($x['hero_image_url']):?><img class="remote-cover" data-remote-image src="<?=e($x['hero_image_url'])?>" alt="<?=e($x['image_alt'])?>" loading="lazy" decoding="async" referrerpolicy="no-referrer"><?php endif;?><span>#<?=e((string)$x['rank'])?></span><b><?=e($x['name'])?></b></div><h2><?=e($x['name'])?></h2><p><?=e($x['locality'])?></p><strong><?=e((string)$x['score'])?></strong><small><?=e($x['rating'])?></small><a class="premium-btn ghost" href="<?=e(entity_url($slug,$x['slug'],$citySlug))?>">View profile</a></article><?php endforeach;?></div>
  <div class="compare-table-wrap"><table class="compare-table"><thead><tr><th>Attribute</th><?php foreach($items as $x):?><th><?=e($x['name'])?></th><?php endforeach;?></tr></thead><tbody>
    <tr><td>ExCompass score</td><?php foreach($items as $x):?><td><b><?=e((string)$x['score'])?></b> / 100</td><?php endforeach;?></tr>
    <tr><td><?=e($detail['category_label'])?></td><?php foreach($items as $x):?><td><?=e($x['category'])?></td><?php endforeach;?></tr>
    <tr><td><?=e($detail['tier_label'])?></td><?php foreach($items as $x):?><td><?=e($x['tier'])?></td><?php endforeach;?></tr>
    <tr><td><?=e($detail['status_label'])?></td><?php foreach($items as $x):?><td><?=e($x['availability'])?></td><?php endforeach;?></tr>
    <tr><td><?=e($detail['intent_label'])?></td><?php foreach($items as $x):?><td><?=e($x['intent'])?></td><?php endforeach;?></tr>
    <?php foreach($detail['score_labels'] as $i=>$label):?><tr><td><?=e($label)?></td><?php foreach($items as $x):$row=$x['breakdown'][$i];?><td><b><?=e((string)$row['score'])?></b> / <?=e((string)$row['max'])?></td><?php endforeach;?></tr><?php endforeach;?>
  </tbody></table></div>
</main>
<?php page_footer(); ?>
