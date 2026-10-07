<?php
declare(strict_types=1);
require __DIR__.'/app/bootstrap.php';
$all=$verticals->all();
$tops=[];
foreach($all as $v){$r=$ranking->rank(published_for_vertical($v['slug']));if($r)$tops[$v['slug']]=$r[0];}
page_header('Know Pune’s best before you decide');
?>
<main>
<section class="hero"><div class="shell hero-grid"><div><span class="eyebrow">EXCOMPASS · PUNE</span><h1>Know Pune’s best <em>before you decide.</em></h1><p>Rankings, comparisons and locality intelligence that turn “where should I go?” into a confident shortlist.</p><form class="hero-search" action="<?=e(u('search.php'))?>" method="get"><input name="q" placeholder="Try “CBSE school”, “Baner”, “cardiology” or “weekend”"><button>Explore</button></form></div><aside><small>WHY EXCOMPASS</small><strong>Evidence before hype.</strong><p>Every published ranking is designed to be traceable to criteria, score and editorial review.</p><div><b>17</b> decision categories</div><div><b>100</b> point scoring model</div></aside></div></section>

<section class="shell section"><div class="section-head"><div><span class="eyebrow dark">START WITH THE DECISION</span><h2>What are you deciding today?</h2></div><p>Choose the intent, not the menu.</p></div><div class="intent-grid">
<?php $intents=[['real-estate','Find a home worth shortlisting'],['hospitals','Choose the right hospital'],['schools','Shortlist the right school'],['restaurants','Pick somewhere worth eating'],['weekend','Plan your next weekend'],['localities','Decode where to live']]; foreach($intents as [$slug,$label]):$v=$verticals->find($slug); ?>
<a class="intent" style="--accent:<?=e($v['accent'])?>" href="<?=e(vertical_url($slug))?>"><span><?=ExCompassSupportIcon::svg($v['icon'])?></span><b><?=e($label)?></b><i>→</i></a>
<?php endforeach; ?></div></section>

<section class="shell section"><div class="section-head"><div><span class="eyebrow dark">EDITOR’S SHORTLIST</span><h2>Start with the current #1s.</h2></div><p>See the score, then open the reasoning.</p></div><div class="short-grid">
<?php foreach(array_slice($all,0,6) as $v):$x=$tops[$v['slug']]??null;if(!$x)continue; ?>
<a class="short" style="--accent:<?=e($v['accent'])?>" href="<?=e(entity_url($x['vertical'],$x['slug']))?>"><div><span><?=ExCompassSupportIcon::svg($v['icon'])?></span><small>#1 <?=e($v['name'])?></small></div><h3><?=e($x['name'])?></h3><p><?=e($x['location'])?></p><strong><?=e((string)$x['score'])?><small>/100 · <?=e($ranking->band($x['score']))?></small></strong><b>Why it ranks #1 →</b></a>
<?php endforeach; ?></div></section>

<section class="shell section"><div class="section-head"><div><span class="eyebrow dark">EXPLORE PUNE</span><h2>Every category has a reason to click.</h2></div><p>Open a category to see its ranked shortlist.</p></div><div class="vertical-grid">
<?php foreach($all as $v):$x=$tops[$v['slug']]??null; ?>
<a class="vertical-card" style="--accent:<?=e($v['accent'])?>" href="<?=e(vertical_url($v['slug']))?>"><span><?=ExCompassSupportIcon::svg($v['icon'])?></span><div><b><?=e($v['name'])?></b><small><?=e($v['prompt'])?></small><?php if($x):?><em>Leading now: <?=e($x['name'])?></em><?php endif;?></div><i>→</i></a>
<?php endforeach; ?></div></section>

<section class="trust"><div class="shell"><div><span class="eyebrow">THE TRUST LAYER</span><h2>A ranking should explain itself.</h2><p>Evidence → scoring → editorial review → published ranking version.</p></div><div class="steps"><span><b>01</b>Evidence</span><span><b>02</b>Scoring</span><span><b>03</b>Review</span><span><b>04</b>Rank</span></div></div></section>
</main>
<?php page_footer(); ?>
