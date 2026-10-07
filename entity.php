<?php
declare(strict_types=1);
require __DIR__.'/runtime.php';

$vs=trim((string)($_GET['vertical']??''));
$slug=trim((string)($_GET['slug']??''));
$citySlug=current_city_slug();
$city=$cities->find($citySlug)??$cities->default();
$v=$verticals->find($vs);
$x=find_published_entity($vs,$slug,$citySlug);
if(!$v||!$x){http_response_code(404);page_header('Profile not found');echo '<main id="content" class="shell section"><h1>Profile not found</h1></main>';page_footer();exit;}
$detail=vertical_detail($vs);
$list=$ranking->rank(published_for_vertical($vs,$citySlug));
$rank=1;foreach($list as $item)if($item['slug']===$slug)$rank=$item['rank'];
$news=related_demo_news($vs,$x['locality']);
$gallery=array_values(array_filter((array)($x['gallery_image_urls']??[])));
page_header($x['name']);
?>
<main id="content" class="shell rich-profile">
  <nav class="profile-crumbs" aria-label="Breadcrumb"><a href="<?=e(u())?>">Discover</a><span>›</span><a href="<?=e(city_url($citySlug))?>"><?=e((string)$city['name'])?></a><span>›</span><a href="<?=e(area_url($citySlug,$x['area_slug']))?>"><?=e($x['area_name'])?></a><span>›</span><a href="<?=e(vertical_url($vs,$citySlug))?>"><?=e($v['name'])?></a><span>›</span><b><?=e($x['name'])?></b></nav>

  <section class="profile-showcase" data-reveal>
    <div class="media-gallery">
      <div class="entity-media-main skin-1" data-gallery-main><?php if($x['hero_image_url']):?><img class="remote-cover" data-gallery-image data-remote-image src="<?=e($x['hero_image_url'])?>" alt="<?=e($x['image_alt'])?>" decoding="async" fetchpriority="high" referrerpolicy="no-referrer"><?php endif;?><span><?=e(strtoupper($v['name']))?> · EXCOMPASS INDEX</span><strong><?=e($x['name'])?></strong><small><?=e($x['locality'])?> · <?=e($x['image_verified']?'verified media':'representative demo media')?></small></div>
      <div class="gallery-counter"><b data-gallery-count>1</b> / <?=max(1,count($gallery))?></div>
      <div class="gallery-thumbs"><?php foreach($gallery as $i=>$url):?><button class="visual-thumb skin-<?=($i%4)+1?> <?=$i===0?'active':''?>" data-gallery-thumb data-image-url="<?=e($url)?>" data-index="<?=$i+1?>" aria-label="Show visual <?=$i+1?>"><img data-remote-image src="<?=e($url)?>" alt="" loading="lazy" decoding="async" referrerpolicy="no-referrer"></button><?php endforeach;?></div>
      <div class="media-source-line"><span><?=e($x['image_disclaimer'])?></span><?php if($x['image_source_url']):?><a href="<?=e($x['image_source_url'])?>" target="_blank" rel="noopener noreferrer">Source: <?=e($x['image_source_name'])?> ↗</a><?php endif;?></div>
    </div>
    <div class="profile-summary">
      <div class="profile-labels"><?php if($x['editorial_status']==='featured'):?><span class="featured-pill">Featured · visibility only</span><?php else:?><span class="ranking-pill">Independent ranking</span><?php endif;?><span>Demo working profile</span></div>
      <div class="profile-title-grid"><div><span class="eyebrow">#<?=e((string)$rank)?> · <?=e($detail['singular'])?></span><h1><?=e($x['name'])?></h1><p class="profile-location"><?=e($x['area_name'])?> · <?=e((string)$city['name'])?></p></div><div class="score-medallion"><strong><?=e((string)$x['score'])?></strong><b><?=e($x['rating'])?></b><span>ExCompass score</span></div></div>
      <p class="profile-description"><?=e($x['description'])?></p>
      <div class="profile-fact-grid"><div><small><?=e($detail['category_label'])?></small><b><?=e($x['category'])?></b></div><div><small><?=e($detail['tier_label'])?></small><b><?=e($x['tier'])?></b></div><div><small><?=e($detail['status_label'])?></small><b><?=e($x['availability'])?></b></div><div><small>Key detail</small><b><?=e($x['tertiary'])?></b></div></div>
      <div class="profile-actions"><a class="premium-btn primary" href="<?=e(u('brochure.php?city='.rawurlencode($citySlug).'&vertical='.rawurlencode($vs).'&slug='.rawurlencode($slug)))?>">Get profile brief</a><button class="premium-btn ghost" data-open-lead data-city="<?=e($citySlug)?>" data-vertical="<?=e($vs)?>" data-entity="<?=e($slug)?>" data-type="visit">Request visit / appointment</button><button class="premium-btn ghost" data-open-lead data-city="<?=e($citySlug)?>" data-vertical="<?=e($vs)?>" data-entity="<?=e($slug)?>" data-type="callback">Request callback</button></div>
      <?php if($x['badges']):?><div class="profile-badges"><?php foreach($x['badges'] as $badge):?><span>✦ <?=e($badge)?></span><?php endforeach;?><a href="<?=e(u('badge.php?city='.rawurlencode($citySlug).'&vertical='.rawurlencode($vs).'&slug='.rawurlencode($slug)))?>">Download badge SVG</a></div><?php endif;?>
    </div>
  </section>

  <nav class="profile-tabs"><a href="#overview">Overview</a><a href="#score">Score</a><a href="#editorial">Editorial view</a><a href="#location">Location</a><a href="#news">News</a><a href="#evidence">Evidence</a></nav>

  <section id="overview" class="profile-two-col" data-reveal>
    <article class="profile-panel observation-card"><span class="eyebrow">ExCompass observation</span><blockquote>“<?=e($x['observation'])?>”</blockquote><small>— <?=e($x['reviewer'])?></small></article>
    <article class="profile-panel"><span class="eyebrow">Decision fit</span><h2>Best suited for</h2><div class="best-fit-grid"><?php foreach($x['best_for'] as $fit):?><div><span>◎</span><b><?=e($fit)?></b></div><?php endforeach;?></div></article>
  </section>

  <section id="score" class="profile-panel score-breakdown-panel" data-reveal>
    <div class="panel-head"><div><span class="eyebrow">Transparent scoring</span><h2>ExCompass score breakdown</h2></div><div class="score-medallion compact"><strong><?=e((string)$x['score'])?></strong><span>/100</span></div></div>
    <div class="score-table"><?php foreach($x['breakdown'] as $row):$pct=(int)round(100*$row['score']/$row['max']);?><div class="score-detail-row"><span><?=e($row['label'])?></span><i><b style="width:<?=$pct?>%"></b></i><strong><?=e((string)$row['score'])?> / <?=e((string)$row['max'])?></strong></div><?php endforeach;?></div>
  </section>

  <section id="editorial" class="editorial-cards" data-reveal>
    <article class="editorial-card standout"><span class="eyebrow">Why it stands out</span><h2>Signal strength</h2><ul><?php foreach($x['standout'] as $item):?><li>✓ <?=e($item)?></li><?php endforeach;?></ul></article>
    <article class="editorial-card liked"><span class="eyebrow">What we liked</span><h2>Positive signals</h2><ul><?php foreach($x['liked'] as $item):?><li>✓ <?=e($item)?></li><?php endforeach;?></ul></article>
    <article class="editorial-card consider"><span class="eyebrow">What to consider</span><h2>Trade-offs</h2><ul><?php foreach($x['consider'] as $item):?><li>△ <?=e($item)?></li><?php endforeach;?></ul></article>
  </section>

  <section id="location" class="profile-location-grid" data-reveal>
    <article class="profile-panel"><span class="eyebrow">Neighbourhood context</span><h2>Explore the area</h2><div class="nearby-grid"><?php foreach($x['nearby'] as $near):?><div><span>⌾</span><small><?=e($near['name'])?></small><b><?=e($near['time'])?></b></div><?php endforeach;?></div><h3>ExCompass locality score</h3><div class="locality-bars"><?php foreach($x['locality_scores'] as $key=>$value):?><div><span><?=e($key)?></span><i><b style="width:<?=e((string)$value)?>%"></b></i><strong><?=e((string)$value)?></strong></div><?php endforeach;?></div></article>
    <article class="profile-panel map-card"><div class="panel-head"><div><span class="eyebrow">Map context</span><h2>View on map</h2></div><a target="_blank" rel="noopener" href="https://www.openstreetmap.org/?mlat=<?=e((string)$x['coordinates'][0])?>&mlon=<?=e((string)$x['coordinates'][1])?>#map=14/<?=e((string)$x['coordinates'][0])?>/<?=e((string)$x['coordinates'][1])?>">Open map ↗</a></div><iframe title="Map for <?=e($x['name'])?>" loading="lazy" src="https://www.openstreetmap.org/export/embed.html?bbox=<?=e((string)($x['coordinates'][1]-.02))?>%2C<?=e((string)($x['coordinates'][0]-.015))?>%2C<?=e((string)($x['coordinates'][1]+.02))?>%2C<?=e((string)($x['coordinates'][0]+.015))?>&layer=mapnik&marker=<?=e((string)$x['coordinates'][0])?>%2C<?=e((string)$x['coordinates'][1])?>"></iframe><small class="demo-map-note">Approximate demo coordinate used for product testing.</small></article>
  </section>

  <section id="news" class="profile-panel newsroom-panel" data-reveal><div class="panel-head"><div><span class="eyebrow">Newsroom connection</span><h2>Latest around this <?=e(strtolower($detail['singular']))?> / locality</h2></div></div><div class="news-grid"><?php foreach($news as $n):?><article><span>EXCOMPASS BRIEF</span><b><?=e($n['title'])?></b><small><?=e($n['age'])?></small></article><?php endforeach;?></div></section>

  <section id="evidence" class="evidence-grid" data-reveal>
    <article class="profile-panel"><span class="eyebrow">Evidence & version</span><h2>Score provenance</h2><dl><div><dt>Reviewer</dt><dd><?=e($x['reviewer'])?></dd></div><div><dt>Review date</dt><dd><?=e($x['review_date'])?></dd></div><div><dt>Score version</dt><dd><?=e($x['score_version'])?></dd></div></dl><ul><?php foreach($x['evidence'] as $item):?><li><?=e($item)?></li><?php endforeach;?></ul><a class="text-link dark" href="<?=e(u('methodology.php'))?>">See full methodology →</a></article>
    <aside class="commercial-disclosure"><span class="sponsored-pill">Commercial layer</span><h2>Ranking stays independent.</h2><p>Featured and Sponsored inventory may affect visibility, but cannot alter ExCompass score or rank.</p></aside>
  </section>

  <section class="compare-callout" data-reveal><div><span class="eyebrow">Decision tool</span><h2>Compare <?=e($v['name'])?></h2><p>Select up to three profiles from the ranking page to compare score criteria and category attributes side by side.</p></div><a class="premium-btn primary" href="<?=e(vertical_url($vs,$citySlug))?>">Choose profiles to compare</a></section>
</main>
<?php page_footer(); ?>
