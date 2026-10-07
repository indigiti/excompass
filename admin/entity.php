<?php
declare(strict_types=1);

require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_layout.php';
require_once dirname(__DIR__) . '/app/Infrastructure/Storage/JsonAuditLog.php';

use ExCompass\Infrastructure\Storage\JsonAuditLog;
use ExCompass\Support\Csrf;

$user = require_admin('entities.view');
$audit = new JsonAuditLog($store);
$isNew = ($_GET['new'] ?? '') === '1';
$verticalSlug = trim((string) ($_GET['vertical'] ?? ''));
$slug = trim((string) ($_GET['slug'] ?? ''));
$entity = $isNew ? null : $entities->find($verticalSlug, $slug);

if (!$isNew && !$entity) {
    http_response_code(404);
    exit('Entity not found');
}
if ($isNew && !$access->allows($user, 'entities.edit')) {
    http_response_code(403);
    exit('Forbidden');
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::valid($_POST['_token'] ?? null)) {
        http_response_code(419);
        $error = 'Your session expired. Please retry.';
    } else {
        $action = (string) ($_POST['action'] ?? 'save');
        try {
            if ($action === 'save') {
                if (!$access->allows($user, 'entities.edit')) {
                    throw new DomainException('You cannot edit entities.');
                }

                $before = $entity;
                $verticalValue = trim((string) ($_POST['vertical'] ?? ($entity['vertical'] ?? '')));
                if (!$verticals->find($verticalValue)) {
                    throw new InvalidArgumentException('Choose a valid vertical.');
                }

                $newSlug = strtolower(trim((string) ($_POST['slug'] ?? ($entity['slug'] ?? ''))));
                $score = $entity['score'] ?? 0;
                if ($access->allows($user, 'scores.edit')) {
                    $score = (int) ($_POST['score'] ?? $score);
                }

                $payload = [
                    'id' => $entity['id'] ?? null,
                    'vertical' => $verticalValue,
                    'slug' => $newSlug,
                    'name' => trim((string) ($_POST['name'] ?? '')),
                    'location' => trim((string) ($_POST['location'] ?? '')),
                    'score' => $score,
                    'highlight' => trim((string) ($_POST['highlight'] ?? '')),
                    'tags' => array_values(array_filter(array_map('trim', explode(',', (string) ($_POST['tags'] ?? ''))))),
                    'status' => $entity['status'] ?? 'draft',
                ];
                $payload = array_filter($payload, static fn($value,$key): bool => !($key === 'id' && $value === null), ARRAY_FILTER_USE_BOTH);
                $entity = $entities->save($payload);
                $audit->record($user, $before ? 'entity.updated' : 'entity.created', 'entity', $entity['vertical'].'/'.$entity['slug'], $before, $entity);
                admin_flash('Entity saved.');
                admin_redirect('entity.php?vertical='.rawurlencode($entity['vertical']).'&slug='.rawurlencode($entity['slug']));
            }

            if ($action === 'transition') {
                if (!$entity) {
                    throw new DomainException('Save the entity before changing workflow state.');
                }
                $to = trim((string) ($_POST['to'] ?? ''));
                $from = (string) ($entity['status'] ?? 'draft');
                $workflow->assertTransition($user, $from, $to);
                $before = $entity;
                $entity = $entities->setStatus($entity['vertical'], $entity['slug'], $to);
                $audit->record($user, 'entity.status_changed', 'entity', $entity['vertical'].'/'.$entity['slug'], $before, $entity);
                admin_flash("Status changed to {$to}.");
                admin_redirect('entity.php?vertical='.rawurlencode($entity['vertical']).'&slug='.rawurlencode($entity['slug']));
            }
        } catch (Throwable $exception) {
            $error = $exception->getMessage();
        }
    }
}

if ($isNew && !$entity) {
    $entity = ['vertical'=>'real-estate','slug'=>'','name'=>'','location'=>'','score'=>0,'highlight'=>'','tags'=>[],'status'=>'draft'];
}
$transitions = $isNew ? [] : $workflow->available($user, (string) $entity['status']);
admin_header($isNew ? 'New entity' : $entity['name'], $user);
?>
<div class="admin-page-head"><div><span>ENTITY</span><h1><?=e($isNew?'New entity':$entity['name'])?></h1></div><a href="<?=e(u('admin/entities.php'))?>">← All entities</a></div>
<?php if($flash=admin_flash()):?><div class="admin-alert success"><?=e($flash)?></div><?php endif;?>
<?php if($error):?><div class="admin-alert error"><?=e($error)?></div><?php endif;?>
<div class="admin-grid entity-editor">
<section class="admin-panel">
<form class="admin-form" method="post">
<input type="hidden" name="_token" value="<?=e(Csrf::token())?>">
<input type="hidden" name="action" value="save">
<label>Vertical<select name="vertical" <?=$isNew?'':'disabled'?>><?php foreach($verticals->all() as $v):?><option value="<?=e($v['slug'])?>" <?=$entity['vertical']===$v['slug']?'selected':''?>><?=e($v['name'])?></option><?php endforeach;?></select></label>
<?php if(!$isNew):?><input type="hidden" name="vertical" value="<?=e($entity['vertical'])?>"><?php endif;?>
<label>Slug<input name="slug" value="<?=e($entity['slug'])?>" pattern="[a-z0-9-]+" required <?=$isNew?'':'readonly'?>></label>
<label>Name<input name="name" value="<?=e($entity['name'])?>" required></label>
<label>Location<input name="location" value="<?=e($entity['location'])?>" required></label>
<label>Highlight<textarea name="highlight" rows="4"><?=e($entity['highlight'])?></textarea></label>
<label>Tags <small>comma separated</small><input name="tags" value="<?=e(implode(', ', $entity['tags']))?>"></label>
<?php if($access->allows($user,'scores.edit')):?><label>Current score <small>temporary seed score until structured scoring is enabled</small><input type="number" name="score" min="0" max="100" value="<?=e((string)$entity['score'])?>"></label><?php else:?><div class="read-only-field"><span>Current score</span><b><?=e((string)$entity['score'])?></b></div><?php endif;?>
<button class="admin-primary" type="submit">Save entity</button>
</form>
</section>
<aside class="admin-panel"><div class="admin-panel-head"><h2>Editorial workflow</h2><span class="status status-<?=e($entity['status'])?>"><?=e($entity['status'])?></span></div><p class="admin-muted">Only permitted role transitions are shown. Commercial access cannot publish or alter editorial scores.</p><?php if(!$transitions):?><p>No workflow action available for your role at this state.</p><?php else:?><?php foreach($transitions as $to):?><form class="transition-form" method="post"><input type="hidden" name="_token" value="<?=e(Csrf::token())?>"><input type="hidden" name="action" value="transition"><input type="hidden" name="to" value="<?=e($to)?>"><button type="submit"><?=e(ucfirst($entity['status']))?> → <?=e(ucfirst($to))?></button></form><?php endforeach;?><?php endif;?></aside>
</div>
<?php admin_footer(); ?>
