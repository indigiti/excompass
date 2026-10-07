<?php
declare(strict_types=1);

require_once __DIR__.'/Support/Icon.php';
require_once __DIR__.'/Domain/Catalog/VerticalRepository.php';
require_once __DIR__.'/Domain/Catalog/EntityRepository.php';
require_once __DIR__.'/Domain/Ranking/RankingService.php';
require_once __DIR__.'/Domain/Search/SearchService.php';

use ExCompass\Domain\Catalog\VerticalRepository;
use ExCompass\Domain\Catalog\EntityRepository;
use ExCompass\Domain\Ranking\RankingService;
use ExCompass\Domain\Search\SearchService;
use ExCompass\Support\Icon;

$config=require dirname(__DIR__).'/config/app.php';
$verticals=new VerticalRepository();
$entities=new EntityRepository();
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

function page_header(string $title): void {
    global $config,$verticals;
    ?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=e($title)?> · <?=e($config['name'])?></title><link rel="stylesheet" href="<?=e(u('assets/css/app.css'))?>"></head><body>
    <header class="site-header"><div class="shell nav"><a class="brand" href="<?=e(u())?>"><span>Ex</span>Compass</a><nav><a href="<?=e(u())?>">Discover</a><a href="<?=e(vertical_url('localities'))?>">Localities</a><a href="<?=e(u('methodology.php'))?>">How we rank</a></nav><form action="<?=e(u('search.php'))?>" method="get"><input name="q" placeholder="Search Pune"><button>⌕</button></form></div></header>
    <div class="rail"><div class="rail-inner"><?php foreach($verticals->all() as $v): ?><a style="--accent:<?=e($v['accent'])?>" href="<?=e(vertical_url($v['slug']))?>"><span><?=Icon::svg($v['icon'])?></span><small><?=e($v['name'])?></small></a><?php endforeach; ?></div></div>
    <?php
}
function page_footer(): void {
    ?><footer><div class="shell footer"><div><b class="brand"><span>Ex</span>Compass</b><p>Independent signals for better local decisions.</p></div><p>Editorial ranking and commercial placement remain separate.</p><p>Seed records are illustrative until evidence-backed production data is approved.</p></div></footer></body></html><?php
}
