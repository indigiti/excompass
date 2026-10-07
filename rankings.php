<?php
declare(strict_types=1);
require __DIR__.'/runtime.php';
$slug=trim((string)($_GET['vertical']??'real-estate'));
$v=$verticals->find($slug);
if(!$v){http_response_code(404);page_header('Category not found');echo '<main class="shell section"><h1>Category not found</h1></main>';page_footer();exit;}
$list=$ranking->rank(published_for_vertical($slug));
page_header('Best '.$v['name'].' in '.$config['city']);
?>
<main class="shell section"><div class="ranking-hero" style="--accent:<?=e($v['accent'])?>"><span><?=ExCompassSupportIcon::svg($v['icon'])?></span><div><span class="eyebrow dark">EXCOMPASS RANKING</span><h1>Best <?=e($v['name'])?> in <?=e($config['city'])?></h1><p><?=e($v['prompt'])?> — scored for decision usefulness, not advertising spend.</p></div></div>
<div class="rank-list"><?php foreach($list as $x):?><a class="rank-row" href="<?=e(entity_url($x['vertical'],$x['slug']))?>"><strong>#<?=e((string)$x['rank'])?></strong><div><b><?=e($x['name'])?></b><small><?=e($x['location'])?> · <?=e($x['highlight'])?></small></div><span><b><?=e((string)$x['score'])?></b><small><?=e($ranking->band($x['score']))?></small></span><i>→</i></a><?php endforeach;?><?php if(!$list):?><div class="empty">This category is ready for editorial data import.</div><?php endif;?></div></main>
<?php page_footer(); ?>
