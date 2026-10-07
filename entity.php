<?php
declare(strict_types=1);
require __DIR__.'/runtime.php';
$vs=trim((string)($_GET['vertical']??''));$slug=trim((string)($_GET['slug']??''));
$v=$verticals->find($vs);$x=find_published_entity($vs,$slug);
if(!$v||!$x){http_response_code(404);page_header('Profile not found');echo '<main id="content" class="shell section"><h1>Profile not found</h1></main>';page_footer();exit;}
$list=$ranking->rank(published_for_vertical($vs));$rank=1;foreach($list as $item)if($item['slug']===$slug)$rank=$item['rank'];
page_header($x['name']);
?>
<main id="content" class="shell profile">
<a class="back" href="<?=e(vertical_url($vs))?>">← Back to <?=e($v['name'])?></a>
<section class="profile-hero" data-reveal>
  <div><span class="kicker">#<?=e((string)$rank)?> · <?=e($v['name'])?></span><h1><?=e($x['name'])?></h1><span class="profile-location"><?=e($x['location'])?></span><p class="profile-lead"><?=e($x['highlight'])?></p><div class="tags"><?php foreach($x['tags'] as $tag):?><span><?=e($tag)?></span><?php endforeach;?></div></div>
  <aside class="score-card"><small>EXCOMPASS SCORE</small><strong><?=e((string)$x['score'])?></strong><b><?=e($ranking->band($x['score']))?></b><em>Working demo score</em></aside>
</section>
<div class="profile-grid" data-reveal>
  <section class="content-card"><span class="eyebrow">Editorial view</span><h2>Why it stands out</h2><p><?=e($x['highlight'])?>. The production profile will expand this into evidence-backed strengths, trade-offs, suitability, source freshness and reviewer notes.</p><div class="profile-facts"><div><b>#<?=e((string)$rank)?></b><span>Current category rank</span></div><div><b><?=e((string)$x['score'])?>/100</b><span>Working score</span></div><div><b><?=count($x['tags'])?></b><span>Decision signals</span></div></div></section>
  <section class="content-card"><span class="eyebrow">Transparency</span><h2>How to read this score</h2><p>The production ranking layer will retain criterion scores, evidence references, reviewer, version and publication date so the final ranking is reproducible.</p><a href="<?=e(u('methodology.php'))?>">Read the methodology →</a><div class="demo-note">This profile is populated with dummy working data for product development and should not be treated as a factual consumer recommendation.</div></section>
</div>
</main>
<?php page_footer(); ?>
