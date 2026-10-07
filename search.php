<?php
declare(strict_types=1);
require __DIR__.'/app/bootstrap.php';
$q=trim((string)($_GET['q']??''));$filter=trim((string)($_GET['vertical']??''));
$results=$search->search($entities->all(),$verticals->all(),$q,$filter?:null);
page_header($q?'Search: '.$q:'Explore ExCompass');
?>
<main class="shell section"><div class="section-head"><div><span class="eyebrow dark">SEARCH</span><h1><?=$q?'Results for “'.e($q).'”':'Explore ExCompass'?></h1></div><p><?=count($results)?> matching profiles</p></div><form class="search-panel" method="get"><input name="q" value="<?=e($q)?>" placeholder="Search by name, locality, need or attribute"><select name="vertical"><option value="">All categories</option><?php foreach($verticals->all() as $v):?><option value="<?=e($v['slug'])?>" <?=$filter===$v['slug']?'selected':''?>><?=e($v['name'])?></option><?php endforeach;?></select><button>Search</button></form>
<div class="results"><?php foreach($results as $x):$v=$verticals->find($x['vertical']);?><a class="result" style="--accent:<?=e($v['accent'])?>" href="<?=e(entity_url($x['vertical'],$x['slug']))?>"><span><?=\ExCompass\Support\Icon::svg($v['icon'])?></span><div><small><?=e($v['name'])?></small><b><?=e($x['name'])?></b><p><?=e($x['location'])?> · <?=e($x['highlight'])?></p></div><strong><?=e((string)$x['score'])?></strong></a><?php endforeach;?><?php if(!$results):?><div class="empty">No match yet. Try a locality, category or broader need.</div><?php endif;?></div></main>
<?php page_footer(); ?>
