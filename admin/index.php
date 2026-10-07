<?php
declare(strict_types=1);

require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_layout.php';
require_once dirname(__DIR__) . '/app/Infrastructure/Storage/JsonAuditLog.php';

use ExCompass\Infrastructure\Storage\JsonAuditLog;

$user = require_admin();
$items = $entities->all();
$counts = ['draft'=>0,'review'=>0,'approved'=>0,'published'=>0,'archived'=>0];
foreach ($items as $item) {
    $status = (string) ($item['status'] ?? 'draft');
    if (isset($counts[$status])) {
        $counts[$status]++;
    }
}
$audit = new JsonAuditLog($store);
$recent = $audit->recent(8);
$leadCount = $access->allows($user,'leads.view') ? count($store->read('leads.json', [])) : 0;
admin_header('Dashboard', $user);
?>
<div class="admin-page-head"><div><span>WORKSPACE</span><h1>Editorial dashboard</h1></div><a class="admin-primary" href="<?=e(u('admin/entity.php?new=1'))?>">+ New entity</a></div>
<?php if($flash=admin_flash()):?><div class="admin-alert success"><?=e($flash)?></div><?php endif;?>
<div class="admin-stats">
  <a href="<?=e(u('admin/entities.php'))?>"><b><?=count($items)?></b><span>Total entities</span></a>
  <a href="<?=e(u('admin/entities.php?status=draft'))?>"><b><?=$counts['draft']?></b><span>Draft</span></a>
  <a href="<?=e(u('admin/entities.php?status=review'))?>"><b><?=$counts['review']?></b><span>Needs review</span></a>
  <a href="<?=e(u('admin/entities.php?status=published'))?>"><b><?=$counts['published']?></b><span>Published</span></a>
  <?php if($access->allows($user,'leads.view')):?><a href="<?=e(u('admin/leads.php'))?>"><b><?=$leadCount?></b><span>Enquiries</span></a><?php endif;?>
</div>
<div class="admin-grid">
<section class="admin-panel"><div class="admin-panel-head"><h2>Workflow</h2></div><div class="workflow-line"><span>Research</span><i>→</i><span>Review</span><i>→</i><span>Approve</span><i>→</i><span>Publish</span></div><p>Role-based transitions prevent commercial users or researchers from directly publishing rankings.</p></section>
<section class="admin-panel"><div class="admin-panel-head"><h2>Recent activity</h2></div><?php if(!$recent):?><p class="admin-muted">No editorial changes recorded yet.</p><?php else:?><div class="audit-list"><?php foreach($recent as $row):?><div><b><?=e($row['action'])?></b><span><?=e($row['subject_key'])?> · <?=e($row['user_email'])?></span></div><?php endforeach;?></div><?php endif;?></section>
</div>
<?php admin_footer(); ?>
