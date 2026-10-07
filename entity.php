<?php
declare(strict_types=1);
require __DIR__.'/app/bootstrap.php';
$vs=trim((string)($_GET['vertical']??''));$slug=trim((string)($_GET['slug']??''));
$v=$verticals->find($vs);$x=find_published_entity($vs,$slug);
if(!$v||!$x){http_response_code(404);page_header('Profile not found');echo '<main class="shell section"><h1>Profile not found</h1></main>';page_footer();exit;}
$list=$ranking->rank(published_for_vertical($vs));$rank=1;foreach($list as $item)if($item['slug']===$slug)$rank=$item['rank'];
page_header($x['name']);
?>
<main class="shell section profile"><a class="back" href="<?=e(vertical_url($vs))?>">← Back to <?=e($v['name'])?></a><div class="profile-hero"><div><span class="eyebrow dark">#<?=e((string)$rank)?> · <?=e($v['name'])?></span><h1><?=e($x['name'])?></h1><b><?=e($x['location'])?></b><p><?=e($x['highlight'])?></p><div class="tags"><?php foreach($x['tags'] as $tag):?><span><?=e($tag)?></span><?php endforeach;?></div></div><aside><small>EXCOMPASS SCORE</small><strong><?=e((string)$x['score'])?></strong><b><?=e($ranking->band($x['score']))?></b><em>Illustrative seed score</em></aside></div>
<div class="profile-grid"><section><h2>Why it stands out</h2><p><?=e($x['highlight'])?>. Production profiles will expand this into verified strengths, trade-offs, suitability and supporting evidence.</p></section><section><h2>How to read this score</h2><p>The production layer will retain criterion scores, evidence references, reviewer, version and publication date so every ranking is reproducible.</p><a href="<?=e(u('methodology.php'))?>">See methodology →</a></section></div></main>
<?php page_footer(); ?>
