<?php
declare(strict_types=1);

require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_layout.php';

$user = require_admin('entities.view');
$q = strtolower(trim((string) ($_GET['q'] ?? '')));
$status = trim((string) ($_GET['status'] ?? ''));
$cityFilter = strtolower(trim((string)($_GET['city'] ?? $cities->default()['slug'])));
$items = array_filter($entities->all(), static function (array $item) use ($q, $status, $cityFilter): bool {
    if (($item['city_slug'] ?? 'pune') !== $cityFilter) {
        return false;
    }
    if ($status !== '' && ($item['status'] ?? '') !== $status) {
        return false;
    }
    if ($q === '') {
        return true;
    }
    return str_contains(strtolower(implode(' ', [
        $item['name'] ?? '',
        $item['area_name'] ?? $item['location'] ?? '',
        $item['city_name'] ?? '',
        $item['vertical'] ?? ''
    ])), $q);
});
usort($items, static fn(array $a,array $b): int => strcmp((string)$a['name'],(string)$b['name']));
admin_header('Entities', $user);
?>
<div class="admin-page-head"><div><span>CATALOG · <?=e((string)($cities->find($cityFilter)['name']??'Pune'))?></span><h1>Entities</h1></div><?php if($access->allows($user,'entities.edit')):?><a class="admin-primary" href="<?=e(u('admin/entity.php?new=1&city='.rawurlencode($cityFilter)))?>">+ New entity</a><?php endif;?></div>
<form class="admin-filters" method="get">
  <input name="q" value="<?=e((string)($_GET['q']??''))?>" placeholder="Search entity, area or vertical">
  <select name="city"><?php foreach($cities->active() as $city):?><option value="<?=e($city['slug'])?>" <?=$cityFilter===$city['slug']?'selected':''?>><?=e($city['name'])?></option><?php endforeach;?></select>
  <select name="status"><option value="">All statuses</option><?php foreach(['draft','review','approved','published','archived'] as $s):?><option value="<?=e($s)?>" <?=$status===$s?'selected':''?>><?=e(ucfirst($s))?></option><?php endforeach;?></select>
  <button>Filter</button>
</form>
<div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Entity</th><th>City</th><th>Area</th><th>Vertical</th><th>Score</th><th>Status</th><th></th></tr></thead><tbody><?php foreach($items as $item):?><tr><td><b><?=e($item['name'])?></b></td><td><?=e($item['city_name'])?></td><td><?=e($item['area_name'])?></td><td><?=e($item['vertical'])?></td><td><?=e((string)$item['score'])?></td><td><span class="status status-<?=e($item['status'])?>"><?=e($item['status'])?></span></td><td><a href="<?=e(u('admin/entity.php?city='.rawurlencode($item['city_slug']).'&vertical='.rawurlencode($item['vertical']).'&slug='.rawurlencode($item['slug'])))?>">Open →</a></td></tr><?php endforeach;?></tbody></table></div>
<?php admin_footer(); ?>
