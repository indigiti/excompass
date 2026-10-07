<?php
declare(strict_types=1);

$release=dirname(__DIR__).'/release';
function must(bool $ok,string $message):void{if(!$ok)throw new RuntimeException($message);}
$required=[
 'RELEASE.json','public/.htaccess','public/runtime.php','public/index.php','public/rankings.php','public/entity.php','public/search.php',
 'public/methodology.php','public/health.php','public/compare.php','public/report.php','public/brochure.php','public/badge.php','public/lead.php','public/city.php','public/area.php',
 'public/assets/css/app.css','public/assets/css/admin.css','public/assets/js/app.js','public/admin/index.php','public/admin/login.php','public/admin/leads.php','public/admin/entity.php',
 'private/app/bootstrap.php','private/app/Domain/Catalog/DemoEntityCatalog.php','private/app/Domain/Catalog/VerticalDetailRepository.php','private/app/Domain/Catalog/RemoteMediaCatalog.php','private/app/Domain/Media/RemoteImagePolicy.php',
 'private/app/Domain/Catalog/EntityProfileService.php','private/app/Domain/Geo/CityRepository.php','private/app/Domain/Geo/AreaDirectory.php','private/config/app.php','private/config/cities.php','private/config/storage.php','private/config/auth.php',
 'private/bin/create-admin.php','private/database/migrations/001_core.sql','private/build/release.json',
];
foreach($required as $path)must(is_file($release.'/'.$path),"payload missing: {$path}");
$meta=json_decode((string)file_get_contents($release.'/RELEASE.json'),true);
must(is_array($meta),'invalid RELEASE.json');
must(($meta['schema']??'')==='DIGIOPS-RELEASE/1','release schema mismatch');
must(($meta['name']??'')==='ExCompass','release name mismatch');
must(($meta['version']??'')==='1.4.0','release version mismatch');
must(($meta['publicPath']??'')==='public_html/excompass/','public path mismatch');
must(($meta['privatePath']??'')==='private_html/excompass/','private path mismatch');
must(($meta['persistentPaths']??[])===['storage/'],'persistent storage contract mismatch');
$runtime=(string)file_get_contents($release.'/public/runtime.php');
foreach(['private_html/excompass','EXCOMPASS_PRIVATE_ROOT','EXCOMPASS_PRIVATE_ROOT_PATH'] as $needle)must(str_contains($runtime,$needle),"runtime split contract missing: {$needle}");
$htaccess=(string)file_get_contents($release.'/public/.htaccess');
must(str_contains($htaccess,'RewriteBase /excompass/'),'RewriteBase /excompass/ missing');
must(str_contains($htaccess,'^city/'),'city drilldown routes missing');
$config=(string)file_get_contents($release.'/private/config/app.php');
must(str_contains($config,"defined('EXCOMPASS_BASE_PATH')"),'deploy base-path fallback missing');
foreach(['index.php','rankings.php','entity.php','search.php','methodology.php','health.php','compare.php','report.php','brochure.php','badge.php','lead.php','city.php','area.php'] as $page){
 $content=(string)file_get_contents($release.'/public/'.$page);must(str_contains($content,'runtime.php'),"public runtime bridge missing: {$page}");
}
$profile=(string)file_get_contents($release.'/private/app/Domain/Catalog/EntityProfileService.php');
must(str_contains($profile,'score_version'),'rich profile metadata missing');
must(str_contains($profile,'breakdown'),'rich scoring breakdown missing');
must(str_contains($profile,'media->enrich'),'remote media enrichment missing');
$media=(string)file_get_contents($release.'/private/app/Domain/Media/RemoteImagePolicy.php');
must(str_contains($media,"'https'"),'remote image HTTPS policy missing');
$entityPage=(string)file_get_contents($release.'/public/entity.php');
must(str_contains($entityPage,'image_disclaimer'),'image provenance disclosure missing');
must(str_contains($entityPage,'gallery_image_urls'),'remote gallery rendering missing');
$geo=(string)file_get_contents($release.'/private/app/Domain/Geo/CityRepository.php');
must(str_contains($geo,'__construct'),'config-backed city repository missing');
$cityConfig=(string)file_get_contents($release.'/private/config/cities.php');
must(str_contains($cityConfig,"'pune'"),'Pune city registry missing');
must(!str_contains($cityConfig,"'mumbai'"),'unexpected second city in registry');
$repository=(string)file_get_contents($release.'/private/app/Infrastructure/Storage/JsonEntityRepository.php');
must(str_contains($repository,'city_slug'),'city-scoped entity identity missing');
must(str_contains($repository,'area_slug'),'area-scoped entity identity missing');
foreach(['public/app','public/config','public/storage','public/database','public/.env','private/.env'] as $forbidden)must(!file_exists($release.'/'.$forbidden),"forbidden release path: {$forbidden}");
$iterator=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($release,FilesystemIterator::SKIP_DOTS));
foreach($iterator as $file){must(!$file->isLink(),'symlink not allowed: '.$file->getPathname());must(!str_ends_with($file->getFilename(),'.json')||!str_contains($file->getPathname(),'/storage/'),'runtime data must not ship: '.$file->getPathname());}
echo "ExCompass DigiOps release verification: PASS".PHP_EOL;
