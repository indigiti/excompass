<?php
declare(strict_types=1);

require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_layout.php';

$user = require_admin('leads.view');
$q = strtolower(trim((string)($_GET['q'] ?? '')));
$type = trim((string)($_GET['type'] ?? ''));
$items = $store->read('leads.json', []);
$items = array_values(array_filter($items, static function(array $row) use ($q,$type): bool {
    if($type !== '' && ($row['type'] ?? '') !== $type) return false;
    if($q === '') return true;
    $hay = strtolower(implode(' ', [
        $row['name'] ?? '', $row['contact'] ?? '', $row['vertical'] ?? '',
        $row['entity'] ?? '', $row['type'] ?? '', $row['message'] ?? ''
    ]));
    return str_contains($hay,$q);
}));
usort($items, static fn(array $a,array $b): int => strcmp((string)($b['created_at']??''),(string)($a['created_at']??'')));
$types=[]; foreach($store->read('leads.json',[]) as $row){$t=(string)($row['type']??'enquiry');$types[$t]=true;}
admin_header('Enquiries', $user);
?>
<div class="admin-page-head"><div><span>COMMERCIAL INBOX</span><h1>Enquiries</h1></div><div class="admin-head-actions"><span class="admin-count"><?=count($items)?> visible</span></div></div>
<form class="admin-filters" method="get">
  <input name="q" value="<?=e((string)($_GET['q']??''))?>" placeholder="Search name, contact, entity or message">
  <select name="type"><option value="">All enquiry types</option><?php foreach(array_keys($types) as $t):?><option value="<?=e($t)?>" <?=$type===$t?'selected':''?>><?=e(ucwords(str_replace('-',' ',$t)))?></option><?php endforeach;?></select>
  <button>Filter</button>
</form>
<?php if(!$items):?>
<div class="admin-panel"><p class="admin-muted">No enquiries match this view yet.</p></div>
<?php else:?>
<div class="lead-grid">
<?php foreach($items as $row):?>
<article class="lead-card">
  <div class="lead-card-head"><div><span><?=e(strtoupper((string)($row['type']??'enquiry')))?></span><h2><?=e((string)($row['name']??'Unknown'))?></h2></div><time><?=e((string)($row['created_at']??''))?></time></div>
  <dl>
    <div><dt>Contact</dt><dd><?=e((string)($row['contact']??''))?></dd></div>
    <div><dt>City</dt><dd><?=e((string)($row['city']??'pune'))?></dd></div>
    <div><dt>Vertical</dt><dd><?=e((string)($row['vertical']??''))?></dd></div>
    <div><dt>Entity</dt><dd><?=e((string)($row['entity']??''))?></dd></div>
  </dl>
  <?php if(trim((string)($row['message']??''))!==''):?><p><?=e((string)$row['message'])?></p><?php endif;?>
  <small>Consent recorded · ID <?=e((string)($row['id']??''))?></small>
</article>
<?php endforeach;?>
</div>
<?php endif;?>
<?php admin_footer(); ?>
