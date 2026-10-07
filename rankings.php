<?php
declare(strict_types=1);
require __DIR__.'/runtime.php';

$slug=trim((string)($_GET['vertical']??'real-estate'));
$citySlug=current_city_slug();
$areaSlug=current_area_slug();
$city=$cities->find($citySlug);
$v=$verticals->find($slug);
if(!$v){http_response_code(404);page_header('Category not found');echo '<main id="content" class="shell section"><h1>Category not found</h1></main>';page_footer();exit;}
$detail=vertical_detail($slug);
$ranked=$ranking->rank(published_for_vertical($slug,$citySlug,$areaSlug));

$filters=[
    'area'=>trim((string)($_GET['area']??'')),
    'category'=>trim((string)($_GET['category']??'')),
    'tier'=>trim((string)($_GET['tier']??'')),
    'availability'=>trim((string)($_GET['availability']??'')),
    'intent'=>trim((string)($_GET['intent']??'')),
    'q'=>trim((string)($_GET['q']??'')),
];
$list=array_values(array_filter($ranked,static function(array $x)use($filters):bool{
    if($filters['area']!==''&&($x['area_slug']??'')!==$filters['area']) return false;
    if($filters['category']!==''&&$x['category']!==$filters['category']) return false;
    if($filters['tier']!==''&&$x['tier']!==$filters['tier']) return false;
    if($filters['availability']!==''&&$x['availability']!==$filters['availability']) return false;
    if($filters['intent']!==''&&$x['intent']!==$filters['intent']) return false;
    if($filters['q']!==''){
        $hay=mb_strtolower(implode(' ',[$x['name'],$x['locality'],$x['highlight'],$x['primary'],$x['secondary'],$x['tertiary']]));
        if(!str_contains($hay,mb_strtolower($filters['q']))) return false;
    }
    return true;
}));
$cityAreas=areas_for_city($citySlug);
$featured=$list[0]??($ranked[0]??null);
page_header($detail['headline']);
?>
<main id="content">
<section class="page-hero" style="--accent:<?=e($v['accent'])?>"><div class="shell page-hero-inner">
  <div data-reveal><span class="icon-tile"><?=icon_svg($v['icon'])?></span><span class="eyebrow"><?=e($v['name'])?> rankings · <?=e((string)$city['name'])?><?=$areaSlug?' · '.e((string)($areas->find($entities->all(),$citySlug,$areaSlug)['name']??$areaSlug)):''?></span><h1><?=e($detail['headline'])?></h1><p><?=e($detail['subline'])?> The current edition uses populated demonstration data so the full decision experience can be tested before approved research is published.</p></div>
  <div class="page-stat"><strong><?=count($ranked)?></strong><span>Profiles evaluated</span></div>
</div></section>

<section class="ranking-stage">
<div class="shell ranking-workspace">
  <div class="ranking-intro" data-reveal>
    <div><span class="eyebrow">Decision workspace</span><h2>Filter the shortlist. Then open the evidence.</h2></div>
    <p>Ranking score and commercial visibility remain separate. Demo values below are designed to exercise the full ExCompass workflow.</p>
  </div>

  <form class="filter-console" method="get" data-rank-filter>
    <input type="hidden" name="vertical" value="<?=e($slug)?>">
    <input type="hidden" name="city" value="<?=e($citySlug)?>">
    <label><span>Area</span><select name="area"><option value="">All <?=e((string)$city['name'])?></option><?php foreach($cityAreas as $x):?><option value="<?=e($x['slug'])?>" <?=$filters['area']===$x['slug']?'selected':''?>><?=e($x['name'])?></option><?php endforeach;?></select></label>
    <label><span><?=e($detail['tier_label'])?></span><select name="tier"><option value="">All</option><?php foreach($detail['tiers'] as $x):?><option value="<?=e($x)?>" <?=$filters['tier']===$x?'selected':''?>><?=e($x)?></option><?php endforeach;?></select></label>
    <label><span><?=e($detail['category_label'])?></span><select name="category"><option value="">All</option><?php foreach($detail['categories'] as $x):?><option value="<?=e($x)?>" <?=$filters['category']===$x?'selected':''?>><?=e($x)?></option><?php endforeach;?></select></label>
    <label><span><?=e($detail['status_label'])?></span><select name="availability"><option value="">All</option><?php foreach($detail['statuses'] as $x):?><option value="<?=e($x)?>" <?=$filters['availability']===$x?'selected':''?>><?=e($x)?></option><?php endforeach;?></select></label>
    <label><span><?=e($detail['intent_label'])?></span><select name="intent"><option value="">All</option><?php foreach($detail['intents'] as $x):?><option value="<?=e($x)?>" <?=$filters['intent']===$x?'selected':''?>><?=e($x)?></option><?php endforeach;?></select></label>
    <label class="filter-query"><span>Search</span><input name="q" value="<?=e($filters['q'])?>" placeholder="Name, locality or signal"></label>
    <button class="premium-btn primary" type="submit">Apply</button>
    <a class="premium-btn ghost" href="<?=e(vertical_url($slug,$citySlug,$areaSlug))?>">Reset</a>
  </form>

  <div class="ranking-subnav">
    <div><a class="active" href="<?=e(vertical_url($slug,$citySlug,$areaSlug))?>">Top ranked</a><a href="#filters">By location</a><a href="#filters">By <?=e(strtolower($detail['tier_label']))?></a><a href="#filters">By need</a><a href="<?=e(u('report.php?city='.rawurlencode($citySlug).'&area='.rawurlencode((string)$areaSlug).'&vertical='.rawurlencode($slug)))?>">Ranking report</a></div>
    <span><b><?=count($list)?></b> results</span>
  </div>

  <div class="ranking-layout-rich" id="filters">
    <section class="rank-list rich-list" data-reveal>
      <?php foreach($list as $x): ?>
      <article class="rank-row-rich <?=$x['rank']===1?'is-leader':''?>" style="--accent:<?=e($v['accent'])?>">
        <span class="rank-number"><small>Rank</small>#<?=e((string)$x['rank'])?></span>
        <a class="rank-visual-mini skin-<?=($x['rank']%4)+1?>" href="<?=e(entity_url($x['vertical'],$x['slug'],$citySlug))?>"><?php if($x['hero_image_url']):?><img class="remote-cover" data-remote-image src="<?=e($x['hero_image_url'])?>" alt="" loading="lazy" decoding="async" referrerpolicy="no-referrer"><?php endif;?><span><?=e(strtoupper($v['name']))?></span><b><?=e($x['name'])?></b></a>
        <div class="rank-body">
          <div class="rank-title-line"><a href="<?=e(entity_url($x['vertical'],$x['slug'],$citySlug))?>"><?=e($x['name'])?></a><?php if($x['editorial_status']==='featured'):?><span class="featured-pill">Featured · rank unaffected</span><?php endif;?></div>
          <small><?=e($x['locality'])?> · <?=e($x['primary'])?> · <?=e($x['secondary'])?></small>
          <div class="rank-meta-rich"><b><?=e($x['tertiary'])?></b><span><?=e($x['category'])?></span><span><?=e($x['intent'])?></span></div>
        </div>
        <div class="rank-score"><small>ExCompass score</small><b><?=e((string)$x['score'])?></b><em><?=e($x['rating'])?></em><label class="compare-check"><input type="checkbox" data-compare data-slug="<?=e($x['slug'])?>" data-name="<?=e($x['name'])?>"> Compare</label></div>
      </article>
      <?php endforeach;?>
      <?php if(!$list):?><div class="empty">No matches. Reset the filters to see the complete shortlist.</div><?php endif;?>
    </section>

    <aside class="ranking-insight" data-reveal>
      <?php if($featured): ?>
      <section class="insight-card feature">
        <div class="insight-visual skin-1"><?php if($featured['hero_image_url']):?><img class="remote-cover" data-remote-image src="<?=e($featured['hero_image_url'])?>" alt="<?=e($featured['image_alt'])?>" loading="lazy" decoding="async" referrerpolicy="no-referrer"><?php endif;?><span>#<?=e((string)$featured['rank'])?> <?=e($detail['singular'])?></span><b><?=e($featured['name'])?></b></div>
        <div class="insight-content"><div class="insight-score"><div><span class="eyebrow">Spotlight</span><h3><?=e($featured['name'])?></h3><p><?=e($featured['locality'])?></p></div><strong><?=e((string)$featured['score'])?></strong></div><p><?=e($featured['observation'])?></p><div class="insight-actions"><a class="premium-btn primary" href="<?=e(entity_url($slug,$featured['slug'],$citySlug))?>">Full profile</a><button class="premium-btn ghost" data-open-lead data-vertical="<?=e($slug)?>" data-entity="<?=e($featured['slug'])?>" data-type="enquiry">Request info</button></div></div>
      </section>
      <section class="insight-card"><span class="eyebrow">Score framework</span><h3>What drives the score</h3><div class="mini-score-list"><?php foreach(array_slice($featured['breakdown'],0,5) as $row):$pct=(int)round(100*$row['score']/$row['max']);?><div><span><?=e($row['label'])?></span><i><b style="width:<?=$pct?>%"></b></i></div><?php endforeach;?></div><a class="text-link dark" href="<?=e(u('methodology.php'))?>">Full methodology →</a></section>
      <?php endif;?>
      <section class="insight-card sponsored"><span class="sponsored-pill">Sponsored</span><h3>Partner spotlight</h3><p>Commercial placement lives beside editorial ranking, never inside the score calculation.</p><button class="premium-btn ghost" data-open-lead data-vertical="<?=e($slug)?>" data-entity="partner-spotlight" data-type="sponsored-enquiry">Enquire</button></section>
    </aside>
  </div>
</div>
</section>

<div class="compare-dock" data-compare-dock data-vertical="<?=e($slug)?>" data-city="<?=e($citySlug)?>" data-area="<?=e((string)$areaSlug)?>" aria-hidden="true"><div><small><span data-compare-count>0</span> selected</small><b data-compare-names>Choose up to three profiles</b></div><button class="premium-btn ghost" data-clear-compare>Clear</button><button class="premium-btn primary" data-go-compare>Compare selected</button></div>
</main>
<?php page_footer(); ?>
