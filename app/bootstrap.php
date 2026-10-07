<?php
declare(strict_types=1);

require_once __DIR__.'/Support/Icon.php';
require_once __DIR__.'/Support/Csrf.php';
require_once __DIR__.'/Domain/Catalog/EntityRepositoryInterface.php';
require_once __DIR__.'/Domain/Catalog/MutableEntityRepositoryInterface.php';
require_once __DIR__.'/Domain/Catalog/VerticalRepository.php';
require_once __DIR__.'/Domain/Geo/CityRepository.php';
require_once __DIR__.'/Domain/Geo/AreaDirectory.php';
require_once __DIR__.'/Domain/Media/RemoteImagePolicy.php';
require_once __DIR__.'/Domain/Catalog/RemoteMediaCatalog.php';
require_once __DIR__.'/Domain/Catalog/VerticalDetailRepository.php';
require_once __DIR__.'/Domain/Catalog/EntityProfileService.php';
require_once __DIR__.'/Domain/Catalog/DemoEntityCatalog.php';
require_once __DIR__.'/Domain/Catalog/EntityRepository.php';
require_once __DIR__.'/Domain/Ranking/RankingService.php';
require_once __DIR__.'/Domain/Search/SearchService.php';
require_once __DIR__.'/Infrastructure/Storage/JsonStore.php';
require_once __DIR__.'/Infrastructure/Storage/JsonEntityRepository.php';

use ExCompass\Domain\Catalog\EntityProfileService;
use ExCompass\Domain\Catalog\EntityRepository;
use ExCompass\Domain\Catalog\RemoteMediaCatalog;
use ExCompass\Domain\Catalog\VerticalDetailRepository;
use ExCompass\Domain\Catalog\VerticalRepository;
use ExCompass\Domain\Ranking\RankingService;
use ExCompass\Domain\Search\SearchService;
use ExCompass\Domain\Media\RemoteImagePolicy;
use ExCompass\Domain\Geo\CityRepository;
use ExCompass\Domain\Geo\AreaDirectory;
use ExCompass\Infrastructure\Storage\JsonEntityRepository;
use ExCompass\Infrastructure\Storage\JsonStore;
use ExCompass\Support\Csrf;
use ExCompass\Support\Icon;

$config=require dirname(__DIR__).'/config/app.php';
$storageConfig=require dirname(__DIR__).'/config/storage.php';
$authConfig=require dirname(__DIR__).'/config/auth.php';

if (($storageConfig['driver'] ?? 'json') !== 'json') {
    throw new RuntimeException('Only the JSON runtime adapter is enabled. Database schemas are compatibility assets only.');
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name((string) $authConfig['session_name']);
    session_set_cookie_params([
        'httponly'=>true,
        'secure'=>(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'samesite'=>'Lax',
        'path'=>'/',
    ]);
    session_start();
}

$verticals=new VerticalRepository();
$cities=new CityRepository();
$areas=new AreaDirectory();
$defaultCity=$cities->default();
$verticalDetails=new VerticalDetailRepository();
$remoteImages=new RemoteImagePolicy();
$remoteMedia=new RemoteMediaCatalog($remoteImages);
$profiles=new EntityProfileService($verticalDetails,$remoteMedia);
$store=new JsonStore((string)$storageConfig['path']);
$seedEntities=new EntityRepository();
$entities=new JsonEntityRepository($store,$seedEntities,(string)$defaultCity['slug'],(string)$defaultCity['name']);
$ranking=new RankingService();
$search=new SearchService();

function e(string $v): string { return htmlspecialchars($v,ENT_QUOTES,'UTF-8'); }
function u(string $path=''): string {
    global $config;
    $base=$config['base_path']?:'';
    return $base.'/'.ltrim($path,'/');
}
function current_city_slug(): string {
    global $cities;
    $candidate=strtolower(trim((string)($_GET['city']??'')));
    $city=$candidate!==''?$cities->find($candidate):null;
    if($city && !empty($city['active'])) return (string)$city['slug'];
    return (string)$cities->default()['slug'];
}
function current_city(): array {
    global $cities;
    return $cities->find(current_city_slug()) ?? $cities->default();
}
function current_area_slug(): ?string {
    $area=strtolower(trim((string)($_GET['area']??'')));
    return $area!==''?$area:null;
}
function city_url(string $citySlug): string {
    return u('city/'.rawurlencode($citySlug).'/');
}
function area_url(string $citySlug,string $areaSlug,?string $vertical=null): string {
    $path='city/'.rawurlencode($citySlug).'/area/'.rawurlencode($areaSlug).'/';
    if($vertical!==null&&$vertical!=='') $path.=rawurlencode($vertical).'/';
    return u($path);
}
function vertical_url(string $slug,?string $citySlug=null,?string $areaSlug=null): string {
    if($citySlug!==null&&$citySlug!==''){
        if($areaSlug!==null&&$areaSlug!=='') return area_url($citySlug,$areaSlug,$slug);
        return u('city/'.rawurlencode($citySlug).'/'.rawurlencode($slug).'/');
    }
    return u(rawurlencode($slug).'/');
}
function entity_url(string $vertical,string $slug,?string $citySlug=null): string {
    if($citySlug!==null&&$citySlug!=='') return u('city/'.rawurlencode($citySlug).'/'.rawurlencode($vertical).'/'.rawurlencode($slug).'/');
    return u(rawurlencode($vertical).'/'.rawurlencode($slug).'/');
}
function icon_svg(string $key): string { return Icon::svg($key); }
function vertical_detail(string $slug): array { global $verticalDetails; return $verticalDetails->find($slug); }
function csrf_token(): string { return Csrf::token(); }

function enrich_entities(array $items): array {
    global $profiles;
    $counts=[];
    $out=[];
    foreach($items as $entity){
        $key=(string)($entity['city_slug']??'pune').'|'.(string)($entity['vertical']??'');
        $index=$counts[$key]??0;
        $out[]=$profiles->enrich($entity,$index);
        $counts[$key]=$index+1;
    }
    return $out;
}
function published_entities(?string $citySlug=null,?string $areaSlug=null): array {
    global $entities;
    $citySlug=$citySlug??current_city_slug();
    $items=array_values(array_filter($entities->all(), static function(array $entity)use($citySlug,$areaSlug):bool{
        if(($entity['status']??'published')!=='published') return false;
        if(($entity['city_slug']??'pune')!==$citySlug) return false;
        if($areaSlug!==null&&$areaSlug!==''&&($entity['area_slug']??'')!==$areaSlug) return false;
        return true;
    }));
    return enrich_entities($items);
}
function published_for_vertical(string $vertical,?string $citySlug=null,?string $areaSlug=null): array {
    return array_values(array_filter(published_entities($citySlug,$areaSlug), static fn(array $entity): bool => $entity['vertical'] === $vertical));
}
function find_published_entity(string $vertical,string $slug,?string $citySlug=null): ?array {
    foreach(published_entities($citySlug) as $entity) {
        if($entity['vertical']===$vertical && $entity['slug']===$slug) return $entity;
    }
    return null;
}
function areas_for_city(?string $citySlug=null): array {
    global $areas,$entities;
    return $areas->fromEntities($entities->all(),$citySlug??current_city_slug());
}
function related_demo_news(string $vertical,string $locality,int $limit=4): array {
    $news=[
        ['title'=>'Metro connectivity update: what it could mean for key Pune neighbourhoods','localities'=>['Hinjawadi','Baner','Kharadi'],'verticals'=>['real-estate','localities','coworking'],'age'=>'2 hours ago'],
        ['title'=>'Property registrations and buyer interest remain active across Pune growth corridors','localities'=>['Kharadi','Wakad','Hinjawadi'],'verticals'=>['real-estate','localities'],'age'=>'1 day ago'],
        ['title'=>'East Pune access improvements put focus on airport-side neighbourhoods','localities'=>['Viman Nagar','Kharadi','Kalyani Nagar'],'verticals'=>['localities','hotels','restaurants'],'age'=>'2 days ago'],
        ['title'=>'Pune admissions guide: questions families should ask before choosing a school','localities'=>['Baner','Kharadi','Viman Nagar'],'verticals'=>['schools','preschools'],'age'=>'3 days ago'],
        ['title'=>'How to compare hospitals: emergency readiness, specialists and access explained','localities'=>['Baner','Deccan','Sassoon Road'],'verticals'=>['hospitals','doctors'],'age'=>'4 days ago'],
        ['title'=>'Pune dining trends: neighbourhoods attracting new restaurant and café formats','localities'=>['Koregaon Park','Baner','Kalyani Nagar'],'verticals'=>['restaurants','cafes'],'age'=>'5 days ago'],
        ['title'=>'Weekend planning from Pune: travel-time and seasonal considerations','localities'=>['Mulshi','Maval','Bhor'],'verticals'=>['weekend'],'age'=>'6 days ago'],
        ['title'=>'Flexible offices expand across Pune technology and residential corridors','localities'=>['Baner','Kharadi','Hadapsar'],'verticals'=>['coworking'],'age'=>'1 week ago'],
    ];
    $out=[];
    foreach($news as $item){
        if(in_array($vertical,$item['verticals'],true)||in_array($locality,$item['localities'],true)) $out[]=$item;
        if(count($out)>=$limit) break;
    }
    return $out;
}
function safe_return_path(?string $path): string {
    if(!$path) return u();
    if(preg_match('/[\x00-\x1F\x7F]/',$path)) return u();
    if(str_starts_with($path,'//')||str_starts_with($path,'\\')) return u();
    $parts=parse_url($path);
    if($parts===false||isset($parts['scheme'])||isset($parts['host'])) return u();
    return str_starts_with($path,'/') ? $path : u($path);
}

function page_header(string $title): void {
    global $config,$verticals,$cities;
    $activeVertical=trim((string)($_GET['vertical']??''));
    if(!$verticals->find($activeVertical)){
        $activeVertical='';
        $requestPath=(string)(parse_url((string)($_SERVER['REQUEST_URI']??''),PHP_URL_PATH)??'');
        foreach($verticals->all() as $candidate){
            if(preg_match('~(?:^|/)'.preg_quote($candidate['slug'],'~').'(?:/|$)~',$requestPath)){
                $activeVertical=$candidate['slug'];
                break;
            }
        }
    }
    $city=current_city();
    ?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="theme-color" content="#090b0d"><meta name="color-scheme" content="light"><title><?=e($title)?> · <?=e($config['name'])?></title><meta name="description" content="Independent rankings, comparisons and local intelligence for <?=e($config['city'])?>."><link rel="stylesheet" href="<?=e(u('assets/css/app.css?v=20261007-geo-1'))?>"></head><body>
    <a class="skip-link" href="#content">Skip to content</a>
    <header class="site-header" data-header>
      <div class="shell nav">
        <a class="brand" href="<?=e(u())?>" aria-label="ExCompass home"><span class="brand-mark">Ex</span><span>Compass</span></a>
        <a class="city-chip" href="<?=e(city_url((string)$city['slug']))?>" aria-label="Current city <?=e((string)$city['name'])?>"><span>⌖</span><b><?=e((string)$city['name'])?></b><?php if(count($cities->active())>1):?><i>⌄</i><?php endif;?></a>
        <nav class="primary-nav" aria-label="Primary navigation">
          <a href="<?=e(u())?>">Discover</a>
          <a href="<?=e(vertical_url('localities'))?>">Neighbourhoods</a>
          <a href="<?=e(u('methodology.php'))?>">Methodology</a>
        </nav>
        <form class="nav-search" action="<?=e(u('search.php'))?>" method="get" role="search">
          <input type="hidden" name="city" value="<?=e((string)$city['slug'])?>">
          <span aria-hidden="true">⌕</span><input name="q" aria-label="Search ExCompass" placeholder="Search <?=e((string)$city['name'])?>"><button>Search</button>
        </form>
        <a class="nav-cta" href="<?=e(u('search.php'))?>">Explore</a>
        <button class="nav-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false" data-nav-toggle>☰</button>
      </div>
      <div class="mobile-nav" data-mobile-nav>
        <a href="<?=e(u())?>">Discover</a><a href="<?=e(vertical_url('localities'))?>">Neighbourhoods</a><a href="<?=e(u('methodology.php'))?>">Methodology</a><a href="<?=e(u('search.php'))?>">Search</a>
      </div>
    </header>
    <div class="rail" aria-label="Explore categories"><div class="rail-inner"><?php foreach($verticals->all() as $v):$isActive=$activeVertical===$v['slug']; ?><a class="<?=$isActive?'is-active':''?>" <?=$isActive?'aria-current="page"':''?> style="--accent:<?=e($v['accent'])?>" href="<?=e(vertical_url($v['slug']))?>"><span><?=Icon::svg($v['icon'])?></span><small><?=e($v['name'])?></small></a><?php endforeach; ?></div></div>
    <?php
}
function page_footer(): void {
    global $config;
    $city=current_city();
    ?><aside class="lead-modal" data-lead-modal aria-hidden="true">
      <div class="lead-dialog" role="dialog" aria-modal="true" aria-labelledby="lead-title">
        <button data-close-modal class="modal-close" type="button" aria-label="Close">×</button>
        <span class="eyebrow">ExCompass enquiry</span><h2 id="lead-title">Request information</h2>
        <p>Demo enquiries are stored in the database-free JSON runtime so the complete workflow can be tested.</p>
        <form action="<?=e(u('lead.php'))?>" method="post" class="lead-form">
          <input type="hidden" name="_token" value="<?=e(csrf_token())?>">
          <input type="hidden" name="vertical" data-lead-vertical value="">
          <input type="hidden" name="entity" data-lead-entity value="">
          <input type="hidden" name="type" data-lead-type value="enquiry">
          <input type="hidden" name="return" value="<?=e($_SERVER['REQUEST_URI']??u())?>">
          <label>Name<input name="name" required maxlength="80"></label>
          <label>Mobile / Email<input name="contact" required maxlength="120"></label>
          <label>Message<textarea name="message" rows="3" maxlength="600" placeholder="What would you like to know?"></textarea></label>
          <label class="consent"><input type="checkbox" name="consent" value="1" required> I agree to be contacted about this enquiry.</label>
          <button class="premium-btn primary" type="submit">Submit enquiry</button>
        </form>
      </div>
    </aside>
    <footer class="site-footer"><div class="shell footer">
      <div><b class="brand"><span class="brand-mark">Ex</span><span>Compass</span></b><p>Independent signals for better local decisions.</p></div>
      <div><small>EDITORIAL PRINCIPLE</small><p>Rankings and commercial presentation remain structurally separate.</p></div>
      <div><small>WORKING DATA</small><p>Current catalog entries and scores are demonstration data for product development.</p></div>
      <div><small>LOCATION</small><p><?=e((string)$city['name'])?> · <?=e((string)$city['state'])?></p></div>
    </div><div class="shell footer-bottom"><span>© <?=date('Y')?> ExCompass</span><a href="<?=e(u('methodology.php'))?>">How rankings work</a><a href="<?=e(u('health.php'))?>">System status</a></div></footer>
    <script src="<?=e(u('assets/js/app.js?v=20261007-remote-media-1'))?>" defer></script></body></html><?php
}
