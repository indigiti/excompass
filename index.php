<?php
declare(strict_types=1);
require __DIR__.'/runtime.php';

$all=$verticals->all();
$tops=[];
foreach($all as $v){
    $r=$ranking->rank(published_for_vertical($v['slug']));
    if($r){$tops[$v['slug']]=$r[0];}
}
$featuredSlugs=['real-estate','hospitals','schools','restaurants','hotels','localities'];
$featured=[];
foreach($featuredSlugs as $slug){
    if(isset($tops[$slug])){$featured[]=['vertical'=>$verticals->find($slug),'entity'=>$tops[$slug]];}
}
$localities=$ranking->rank(published_for_vertical('localities'));
page_header('Pune, edited for better decisions');
?>
<main id="content">
<section class="hero-premium">
  <div class="shell hero-layout">
    <div class="hero-copy-block" data-reveal>
      <span class="kicker">Independent city intelligence</span>
      <h1>Pune,<br><em>edited.</em></h1>
      <p class="hero-deck">A sharper way to decide where to live, learn, dine, stay and spend your time. ExCompass turns crowded choices into ranked, explainable shortlists.</p>
      <form class="hero-search" action="<?=e(u('search.php'))?>" method="get" role="search">
        <input name="q" aria-label="Search ExCompass" placeholder="Search a neighbourhood, school, hospital or need">
        <button>Explore Pune</button>
      </form>
      <div class="hero-trends"><span>Trending now</span><a href="<?=e(vertical_url('real-estate'))?>">Homes in West Pune</a><a href="<?=e(u('search.php?q=Baner'))?>">Baner</a><a href="<?=e(vertical_url('schools'))?>">Schools</a><a href="<?=e(vertical_url('weekend'))?>">Weekend escapes</a></div>
    </div>
    <div class="hero-visual" data-reveal data-tilt>
      <div class="visual-frame">
        <div class="visual-gridline"></div>
        <div class="visual-index">
          <div class="visual-index-head">
            <div><small>THE EXCOMPASS INDEX · THIS EDIT</small><h3><?=e($featured[0]['entity']['name']??'Pune shortlist')?></h3></div>
            <div class="visual-index-score"><?=e((string)($featured[0]['entity']['score']??94))?><span>/100</span></div>
          </div>
          <div class="visual-ranks">
            <?php foreach(array_slice($featured,1,3) as $item): ?>
            <a href="<?=e(entity_url($item['entity']['vertical'],$item['entity']['slug']))?>"><b><?=e($item['entity']['name'])?></b><?=e($item['vertical']['name'])?> · <?=e((string)$item['entity']['score'])?></a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="hero-signal-strip"><div class="shell signal-grid">
    <div><strong>105</strong><span>Working profiles</span></div>
    <div><strong>17</strong><span>Decision verticals</span></div>
    <div><strong>100</strong><span>Point scoring scale</span></div>
    <div><strong>1</strong><span>Editorial standard</span></div>
  </div></div>
</section>

<section class="shell section" data-reveal>
  <div class="section-head"><div><span class="eyebrow">Start with intent</span><h2>What are you deciding?</h2></div><p>Skip the directory. Begin with the decision you actually need to make.</p></div>
  <div class="intent-grid">
    <?php
    $intents=[
      ['real-estate','Find a home worth shortlisting','Value, location, delivery and lifestyle'],
      ['hospitals','Choose the right hospital','Capability, access and care depth'],
      ['schools','Shortlist the right school','Curriculum, environment and fit'],
      ['restaurants','Pick somewhere worth the table','Experience, consistency and occasion'],
      ['weekend','Plan a better weekend','Drives, nature and quick escapes'],
      ['localities','Decode where to live','Daily convenience beyond property prices'],
    ];
    foreach($intents as $i=>$row):
      [$slug,$label,$copy]=$row;$v=$verticals->find($slug);
    ?>
    <a class="intent" style="--accent:<?=e($v['accent'])?>" href="<?=e(vertical_url($slug))?>">
      <div class="intent-top"><span class="icon-tile"><?=icon_svg($v['icon'])?></span><span class="intent-number">0<?=$i+1?></span></div>
      <div><b><?=e($label)?></b><small><?=e($copy)?></small></div><span class="intent-arrow">→</span>
    </a>
    <?php endforeach; ?>
  </div>
</section>

<section class="section section-soft">
  <div class="shell" data-reveal>
    <div class="section-head"><div><span class="eyebrow">Editor’s selection</span><h2>Six places to start.</h2></div><p>Our current working #1s across high-intent categories. Open the profile to see why each one leads its shortlist.</p></div>
    <div class="selection-grid">
      <?php foreach($featured as $item):$v=$item['vertical'];$x=$item['entity']; ?>
      <a class="selection-card" data-tilt style="--accent:<?=e($v['accent'])?>" href="<?=e(entity_url($x['vertical'],$x['slug']))?>">
        <?php if($x['hero_image_url']):?><img class="remote-cover selection-photo" data-remote-image src="<?=e($x['hero_image_url'])?>" alt="" loading="lazy" decoding="async" referrerpolicy="no-referrer"><?php endif;?>
        <div class="selection-meta"><span><i></i><?=e($v['name'])?></span><span>#1 current</span></div>
        <div class="selection-score"><strong><?=e((string)$x['score'])?></strong><span>/100</span></div>
        <div class="selection-copy"><h3><?=e($x['name'])?></h3><p><?=e($x['location'])?> · <?=e($x['highlight'])?></p></div>
        <div class="selection-link"><span>Open the rationale</span><span>→</span></div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="shell section" data-reveal>
  <div class="section-head"><div><span class="eyebrow">The city, organised</span><h2>Explore all 17 verticals.</h2></div><p>Each category carries its own ranking context, score language and shortlist.</p></div>
  <div class="vertical-grid">
    <?php foreach($all as $v):$x=$tops[$v['slug']]??null; ?>
    <a class="vertical-card" style="--accent:<?=e($v['accent'])?>" href="<?=e(vertical_url($v['slug']))?>">
      <span class="icon-tile"><?=icon_svg($v['icon'])?></span>
      <div><b><?=e($v['name'])?></b><small><?=e($v['prompt'])?></small><?php if($x):?><em>Leading now · <?=e($x['name'])?></em><?php endif;?></div><i>→</i>
    </a>
    <?php endforeach; ?>
  </div>
</section>

<section class="section section-dark">
  <div class="shell neighbourhood-feature" data-reveal>
    <div class="neighbourhood-copy"><span class="eyebrow">Neighbourhood intelligence</span><h2>A city is really a collection of daily decisions.</h2><p>Where you live changes commute, schools, healthcare, food, work and weekend rhythm. ExCompass connects those signals into locality-level context instead of treating every category in isolation.</p><a class="text-link" href="<?=e(vertical_url('localities'))?>">Explore neighbourhoods <span>→</span></a></div>
    <div class="locality-stack">
      <?php foreach(array_slice($localities,0,5) as $x): ?>
      <a class="locality-row" href="<?=e(entity_url('localities',$x['slug']))?>"><strong><?=e((string)$x['score'])?></strong><div><b><?=e($x['name'])?></b><small><?=e($x['highlight'])?></small></div><i>→</i></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="shell section" data-reveal>
  <div class="manifesto">
    <div><span class="eyebrow">The trust layer</span><h2>A ranking should explain itself.</h2><div class="demo-note">Development mode: all 105 current profiles and scores are dummy working data, intentionally populated so design, search, ranking, admin and deployment flows can be tested end-to-end.</div></div>
    <div class="manifesto-copy">
      <div class="manifesto-step"><b>01</b><div><h3>Evidence</h3><p>Structured facts and source material create the base layer.</p></div></div>
      <div class="manifesto-step"><b>02</b><div><h3>Scoring</h3><p>Vertical-specific criteria turn evidence into comparable signals.</p></div></div>
      <div class="manifesto-step"><b>03</b><div><h3>Editorial review</h3><p>Humans review context, conflicts and any justified override.</p></div></div>
      <div class="manifesto-step"><b>04</b><div><h3>Published rank</h3><p>Versioned rankings remain traceable and separate from commercial placement.</p></div></div>
    </div>
  </div>
</section>
</main>
<?php page_footer(); ?>
