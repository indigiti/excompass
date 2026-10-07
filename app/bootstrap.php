<?php
declare(strict_types=1);

require_once __DIR__.'/Support/Icon.php';
require_once __DIR__.'/Domain/Catalog/EntityRepositoryInterface.php';
require_once __DIR__.'/Domain/Catalog/MutableEntityRepositoryInterface.php';
require_once __DIR__.'/Domain/Catalog/VerticalRepository.php';
require_once __DIR__.'/Domain/Catalog/EntityRepository.php';
require_once __DIR__.'/Domain/Ranking/RankingService.php';
require_once __DIR__.'/Domain/Search/SearchService.php';
require_once __DIR__.'/Infrastructure/Storage/JsonStore.php';
require_once __DIR__.'/Infrastructure/Storage/JsonEntityRepository.php';

use ExCompass\Domain\Catalog\EntityRepository;
use ExCompass\Domain\Catalog\VerticalRepository;
use ExCompass\Domain\Ranking\RankingService;
use ExCompass\Domain\Search\SearchService;
use ExCompass\Infrastructure\Storage\JsonEntityRepository;
use ExCompass\Infrastructure\Storage\JsonStore;
use ExCompass\Support\Icon;

$config=require dirname(__DIR__).'/config/app.php';
$storageConfig=require dirname(__DIR__).'/config/storage.php';

if (($storageConfig['driver'] ?? 'json') !== 'json') {
    throw new RuntimeException('Only the JSON runtime adapter is enabled. Database schemas are compatibility assets only.');
}

$verticals=new VerticalRepository();
$store=new JsonStore((string)$storageConfig['path']);
$seedEntities=new EntityRepository();
$entities=new JsonEntityRepository($store,$seedEntities);
$ranking=new RankingService();
$search=new SearchService();

function e(string $v): string { return htmlspecialchars($v,ENT_QUOTES,'UTF-8'); }
function u(string $path=''): string {
    global $config;
    $base=$config['base_path']?:'';
    return $base.'/'.ltrim($path,'/');
}
function vertical_url(string $slug): string { return u(rawurlencode($slug).'/'); }
function entity_url(string $vertical,string $slug): string { return u(rawurlencode($vertical).'/'.rawurlencode($slug).'/'); }

function published_entities(): array {
    global $entities;
    return array_values(array_filter($entities->all(), static fn(array $entity): bool => ($entity['status'] ?? 'published') === 'published'));
}
function published_for_vertical(string $vertical): array {
    return array_values(array_filter(published_entities(), static fn(array $entity): bool => $entity['vertical'] === $vertical));
}
function find_published_entity(string $vertical,string $slug): ?array {
    foreach(published_entities() as $entity) {
        if($entity['vertical']===$vertical && $entity['slug']===$slug) return $entity;
    }
    return null;
}

function page_header(string $title): void {
    global $config,$verticals;
    ?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=e($title)?> · <?=e($config['name'])?></title><link rel="stylesheet" href="<?=e(u('assets/css/app.css'))?>"></head><body>
    <header class="site-header"><div class="shell nav"><a class="brand" href="<?=e(u())?>"><span>Ex</span>Compass</a><nav><a href="<?=e(u())?>">Discover</a><a href="<?=e(vertical_url('localities'))?>">Localities</a><a href="<?=e(u('methodology.php'))?>">How we rank</a></nav><form action="<?=e(u('search.php'))?>" method="get"><input name="q" placeholder="Search Pune"><button>⌕</button></form></div></header>
    <div class="rail"><div class="rail-inner"><?php foreach($verticals->all() as $v): ?><a style="--accent:<?=e($v['accent'])?>" href="<?=e(vertical_url($v['slug']))?>"><span><?=Icon::svg($v['icon'])?></span><small><?=e($v['name'])?></small></a><?php endforeach; ?></div></div>
    <?php
}
function page_footer(): void {
    ?><footer><div class="shell footer"><div><b class="brand"><span>Ex</span>Compass</b><p>Independent signals for better local decisions.</p></div><p>Editorial ranking and commercial placement remain separate.</p><p>Runtime storage is database-free and repository-backed.</p></div></footer></body></html><?php
}
