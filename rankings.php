<?php
declare(strict_types=1);
require __DIR__.'/runtime.php';
$slug=trim((string)($_GET['vertical']??'real-estate'));
$v=$verticals->find($slug);
if(!$v){http_response_code(404);page_header('Category not found');echo '<main id="content" class="shell section"><h1>Category not found</h1></main>';page_footer();exit;}
$list=$ranking->rank(published_for_vertical($slug));
page_header('Best '.$v['name'].' in '.$config['city']);
?>
<main id="content">
<section class="page-hero" style="--accent:<?=e($v['accent'])?>"><div class="shell page-hero-inner">
  <div data-reveal><span class="icon-tile"><?=icon_svg($v['icon'])?></span><span class="eyebrow">ExCompass ranking · <?=e($config['city'])?></span><h1>Best <?=e($v['name'])?></h1><p><?=e($v['prompt'])?>. This working edition uses populated demo data so ranking, profile, search and editorial flows can be tested before production evidence is loaded.</p></div>
  <div class="page-stat"><strong><?=count($list)?></strong><span>Profiles in this shortlist</span></div>
</div></section>
<section class="shell section" style="padding-top:24px">
  <div class="rank-list" data-reveal>
  <?php foreach($list as $x): ?>
    <a class="rank-row" style="--accent:<?=e($v['accent'])?>" href="<?=e(entity_url($x['vertical'],$x['slug']))?>">
      <span class="rank-number">#<?=e((string)$x['rank'])?></span>
      <span class="rank-body"><b><?=e($x['name'])?></b><small><?=e($x['location'])?> · <?=e($x['highlight'])?></small><span class="rank-tags"><?php foreach(array_slice($x['tags'],0,3) as $tag):?><span><?=e($tag)?></span><?php endforeach;?></span></span>
      <span class="rank-score"><b><?=e((string)$x['score'])?></b><small><?=e($ranking->band($x['score']))?></small></span><i class="rank-arrow">→</i>
    </a>
  <?php endforeach; ?>
  <?php if(!$list):?><div class="empty">This category is ready for editorial data import.</div><?php endif;?>
  </div>
</section>
</main>
<?php page_footer(); ?>
