<?php
declare(strict_types=1);

require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_layout.php';
require_once dirname(__DIR__) . '/app/Infrastructure/Storage/JsonAuditLog.php';

use ExCompass\Infrastructure\Storage\JsonAuditLog;
use ExCompass\Support\Csrf;

function admin_list(string $value): array
{
    return array_values(array_filter(array_map('trim', preg_split('/[\r\n,]+/', $value) ?: [])));
}
function admin_lines(string $value): array
{
    return array_values(array_filter(array_map('trim', preg_split('/\R+/', $value) ?: [])));
}
function admin_pairs(string $value, bool $numeric = false): array
{
    $out = [];
    foreach (preg_split('/\R+/', $value) ?: [] as $line) {
        $line = trim($line);
        if ($line === '') continue;
        [$key,$val] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');
        if ($key === '' || $val === '') continue;
        $out[$key] = $numeric ? max(0, min(100, (int)$val)) : $val;
    }
    return $out;
}
function admin_https_url(string $value): ?string
{
    $value = trim($value);
    if ($value === '') return null;
    $parts = parse_url($value);
    if (!is_array($parts) || strtolower((string)($parts['scheme'] ?? '')) !== 'https' || empty($parts['host'])) {
        return null;
    }
    return $value;
}
function admin_nearby(string $value): array
{
    $out = [];
    foreach (preg_split('/\R+/', $value) ?: [] as $line) {
        $line = trim($line);
        if ($line === '') continue;
        [$name,$time] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');
        if ($name !== '' && $time !== '') $out[] = ['name'=>$name,'time'=>$time];
    }
    return $out;
}

$user = require_admin('entities.view');
$audit = new JsonAuditLog($store);
$isNew = ($_GET['new'] ?? '') === '1';
$verticalSlug = trim((string) ($_GET['vertical'] ?? ''));
$slug = trim((string) ($_GET['slug'] ?? ''));
$rawEntity = $isNew ? null : $entities->find($verticalSlug, $slug);
$entity = $rawEntity;

if (!$isNew && !$entity) {
    http_response_code(404);
    exit('Entity not found');
}
if ($isNew && !$access->allows($user, 'entities.edit')) {
    http_response_code(403);
    exit('Forbidden');
}

if ($entity) {
    $index = 0;
    foreach ($entities->forVertical((string)$entity['vertical']) as $i => $candidate) {
        if (($candidate['slug'] ?? '') === ($entity['slug'] ?? '')) { $index = $i; break; }
    }
    $entity = $profiles->enrich($entity, $index);
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

                $before = $rawEntity;
                $verticalValue = trim((string) ($_POST['vertical'] ?? ($entity['vertical'] ?? '')));
                if (!$verticals->find($verticalValue)) {
                    throw new InvalidArgumentException('Choose a valid vertical.');
                }
                $newSlug = strtolower(trim((string) ($_POST['slug'] ?? ($entity['slug'] ?? ''))));
                $score = (int)($entity['score'] ?? 0);
                if ($access->allows($user, 'scores.edit')) {
                    $score = (int) ($_POST['score'] ?? $score);
                }

                $payload = $entity ?: [];
                $payload['id'] = $rawEntity['id'] ?? null;
                $payload['vertical'] = $verticalValue;
                $payload['slug'] = $newSlug;
                $payload['name'] = trim((string) ($_POST['name'] ?? ''));
                $payload['location'] = trim((string) ($_POST['location'] ?? ''));
                $payload['locality'] = trim((string) ($_POST['locality'] ?? $payload['location']));
                $payload['score'] = $score;
                $payload['highlight'] = trim((string) ($_POST['highlight'] ?? ''));
                $payload['tags'] = admin_list((string)($_POST['tags'] ?? ''));
                $payload['status'] = $rawEntity['status'] ?? 'draft';
                $payload['category'] = trim((string)($_POST['category'] ?? ''));
                $payload['tier'] = trim((string)($_POST['tier'] ?? ''));
                $payload['availability'] = trim((string)($_POST['availability'] ?? ''));
                $payload['intent'] = trim((string)($_POST['intent'] ?? ''));
                $payload['primary'] = trim((string)($_POST['primary'] ?? ''));
                $payload['secondary'] = trim((string)($_POST['secondary'] ?? ''));
                $payload['tertiary'] = trim((string)($_POST['tertiary'] ?? ''));
                $payload['description'] = trim((string)($_POST['description'] ?? ''));
                $payload['observation'] = trim((string)($_POST['observation'] ?? ''));
                $payload['best_for'] = admin_list((string)($_POST['best_for'] ?? ''));
                $payload['badges'] = admin_list((string)($_POST['badges'] ?? ''));
                $payload['standout'] = admin_lines((string)($_POST['standout'] ?? ''));
                $payload['liked'] = admin_lines((string)($_POST['liked'] ?? ''));
                $payload['consider'] = admin_lines((string)($_POST['consider'] ?? ''));
                $payload['evidence'] = admin_lines((string)($_POST['evidence'] ?? ''));
                $payload['reviewer'] = trim((string)($_POST['reviewer'] ?? 'ExCompass Research Desk'));
                $payload['review_date'] = trim((string)($_POST['review_date'] ?? date('Y-m-d')));
                $payload['score_version'] = trim((string)($_POST['score_version'] ?? '1.2-demo'));

                $heroInput = trim((string)($_POST['hero_image_url'] ?? ''));
                $heroUrl = $remoteImages->sanitize($heroInput);
                if ($heroInput !== '' && $heroUrl === null) {
                    throw new InvalidArgumentException('Hero image must be an HTTPS URL on an approved image host.');
                }
                $galleryInput = admin_lines((string)($_POST['gallery_image_urls'] ?? ''));
                $galleryUrls = $remoteImages->sanitizeMany($galleryInput, 8);
                if (count($galleryUrls) !== count($galleryInput)) {
                    throw new InvalidArgumentException('Every gallery image must be an HTTPS URL on an approved image host.');
                }
                $sourceInput = trim((string)($_POST['image_source_url'] ?? ''));
                $sourceUrl = admin_https_url($sourceInput);
                if ($sourceInput !== '' && $sourceUrl === null) {
                    throw new InvalidArgumentException('Image source URL must use HTTPS.');
                }
                $payload['hero_image_url'] = $heroUrl;
                $payload['gallery_image_urls'] = $galleryUrls;
                $payload['image_alt'] = trim((string)($_POST['image_alt'] ?? ''));
                $payload['image_source_name'] = trim((string)($_POST['image_source_name'] ?? ''));
                $payload['image_source_url'] = $sourceUrl;
                $payload['image_credit'] = trim((string)($_POST['image_credit'] ?? ''));
                $payload['image_verified'] = ($_POST['image_verified'] ?? '') === '1';

                $nearby = admin_nearby((string)($_POST['nearby'] ?? ''));
                if ($nearby) $payload['nearby'] = $nearby;
                $localityScores = admin_pairs((string)($_POST['locality_scores'] ?? ''), true);
                if ($localityScores) $payload['locality_scores'] = $localityScores;
                $lat = filter_var($_POST['lat'] ?? null, FILTER_VALIDATE_FLOAT);
                $lng = filter_var($_POST['lng'] ?? null, FILTER_VALIDATE_FLOAT);
                if ($lat !== false && $lng !== false && $lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180) {
                    $payload['coordinates'] = [(float)$lat,(float)$lng];
                }
                unset($payload['rating'], $payload['breakdown']);

                if ($payload['id'] === null) unset($payload['id']);
                $rawEntity = $entities->save($payload);
                $entity = $rawEntity;
                $audit->record($user, $before ? 'entity.updated' : 'entity.created', 'entity', $rawEntity['vertical'].'/'.$rawEntity['slug'], $before, $rawEntity);
                admin_flash('Entity and rich profile data saved.');
                admin_redirect('entity.php?vertical='.rawurlencode($rawEntity['vertical']).'&slug='.rawurlencode($rawEntity['slug']));
            }

            if ($action === 'transition') {
                if (!$rawEntity) {
                    throw new DomainException('Save the entity before changing workflow state.');
                }
                $to = trim((string) ($_POST['to'] ?? ''));
                $from = (string) ($rawEntity['status'] ?? 'draft');
                $workflow->assertTransition($user, $from, $to);
                $before = $rawEntity;
                $rawEntity = $entities->setStatus($rawEntity['vertical'], $rawEntity['slug'], $to);
                $audit->record($user, 'entity.status_changed', 'entity', $rawEntity['vertical'].'/'.$rawEntity['slug'], $before, $rawEntity);
                admin_flash("Status changed to {$to}.");
                admin_redirect('entity.php?vertical='.rawurlencode($rawEntity['vertical']).'&slug='.rawurlencode($rawEntity['slug']));
            }
        } catch (Throwable $exception) {
            $error = $exception->getMessage();
        }
    }
}

if ($isNew && !$entity) {
    $entity = [
        'vertical'=>'real-estate','slug'=>'','name'=>'','location'=>'','locality'=>'','score'=>0,'highlight'=>'','tags'=>[],'status'=>'draft',
        'category'=>'Apartment','tier'=>'₹1–2 Cr','availability'=>'2027','intent'=>'Family','primary'=>'','secondary'=>'','tertiary'=>'',
        'description'=>'','observation'=>'','best_for'=>[],'badges'=>[],'standout'=>[],'liked'=>[],'consider'=>[],'evidence'=>[],
        'reviewer'=>'ExCompass Research Desk','review_date'=>date('Y-m-d'),'score_version'=>'1.2-demo','nearby'=>[],'locality_scores'=>[],'coordinates'=>[18.5204,73.8567],
        'hero_image_url'=>null,'gallery_image_urls'=>[],'image_alt'=>'','image_source_name'=>'','image_source_url'=>null,'image_credit'=>'','image_verified'=>false,
    ];
}
if (!$isNew && $rawEntity) {
    $index = 0;
    foreach ($entities->forVertical((string)$rawEntity['vertical']) as $i => $candidate) {
        if (($candidate['slug'] ?? '') === ($rawEntity['slug'] ?? '')) { $index = $i; break; }
    }
    $entity = $profiles->enrich($rawEntity, $index);
}
$detail = vertical_detail((string)$entity['vertical']);
$transitions = $isNew ? [] : $workflow->available($user, (string) $rawEntity['status']);
$nearbyText = implode("\n", array_map(static fn(array $row): string => ($row['name']??'').'|'.($row['time']??''), (array)($entity['nearby']??[])));
$localityText = implode("\n", array_map(static fn($key,$value): string => $key.'|'.$value, array_keys((array)($entity['locality_scores']??[])), array_values((array)($entity['locality_scores']??[]))));
$galleryText = implode("\n", (array)($entity['gallery_image_urls']??[]));
admin_header($isNew ? 'New entity' : $entity['name'], $user);
?>
<div class="admin-page-head"><div><span>ENTITY · RICH PROFILE</span><h1><?=e($isNew?'New entity':$entity['name'])?></h1></div><div class="admin-head-actions"><?php if(!$isNew):?><a href="<?=e(entity_url($entity['vertical'],$entity['slug']))?>" target="_blank" rel="noopener">Preview ↗</a><?php endif;?><a href="<?=e(u('admin/entities.php'))?>">← All entities</a></div></div>
<?php if($flash=admin_flash()):?><div class="admin-alert success"><?=e($flash)?></div><?php endif;?>
<?php if($error):?><div class="admin-alert error"><?=e($error)?></div><?php endif;?>
<div class="admin-grid entity-editor">
<section class="admin-panel">
<form class="admin-form" method="post">
<input type="hidden" name="_token" value="<?=e(Csrf::token())?>">
<input type="hidden" name="action" value="save">

<div class="admin-section-head"><span>Core identity</span><p>Required catalog fields and public ranking summary.</p></div>
<div class="admin-form-grid">
<label>Vertical<select name="vertical" <?=$isNew?'':'disabled'?>><?php foreach($verticals->all() as $v):?><option value="<?=e($v['slug'])?>" <?=$entity['vertical']===$v['slug']?'selected':''?>><?=e($v['name'])?></option><?php endforeach;?></select></label>
<?php if(!$isNew):?><input type="hidden" name="vertical" value="<?=e($entity['vertical'])?>"><?php endif;?>
<label>Slug<input name="slug" value="<?=e($entity['slug'])?>" pattern="[a-z0-9-]+" required <?=$isNew?'':'readonly'?>></label>
<label>Name<input name="name" value="<?=e($entity['name'])?>" required></label>
<label>Location<input name="location" value="<?=e($entity['location'])?>" required></label>
<label>Locality<input name="locality" value="<?=e((string)($entity['locality']??$entity['location']))?>"></label>
<?php if($access->allows($user,'scores.edit')):?><label>Current score <small>0–100; criterion rows are regenerated from this score.</small><input type="number" name="score" min="0" max="100" value="<?=e((string)$entity['score'])?>"></label><?php else:?><div class="read-only-field"><span>Current score</span><b><?=e((string)$entity['score'])?></b></div><?php endif;?>
</div>
<label>Highlight<textarea name="highlight" rows="3"><?=e((string)$entity['highlight'])?></textarea></label>
<label>Tags <small>comma separated</small><input name="tags" value="<?=e(implode(', ', (array)$entity['tags']))?>"></label>

<div class="admin-section-head"><span>Decision facts</span><p>These drive ranking filters, comparison tables and the profile fact grid.</p></div>
<div class="admin-form-grid">
<label><?=e($detail['category_label'])?><input name="category" value="<?=e((string)$entity['category'])?>"></label>
<label><?=e($detail['tier_label'])?><input name="tier" value="<?=e((string)$entity['tier'])?>"></label>
<label><?=e($detail['status_label'])?><input name="availability" value="<?=e((string)$entity['availability'])?>"></label>
<label><?=e($detail['intent_label'])?><input name="intent" value="<?=e((string)$entity['intent'])?>"></label>
<label>Primary fact<input name="primary" value="<?=e((string)$entity['primary'])?>"></label>
<label>Secondary fact<input name="secondary" value="<?=e((string)$entity['secondary'])?>"></label>
<label>Key detail<input name="tertiary" value="<?=e((string)$entity['tertiary'])?>"></label>
<label>Badges <small>comma separated</small><input name="badges" value="<?=e(implode(', ', (array)$entity['badges']))?>"></label>
</div>

<div class="admin-section-head"><span>Editorial profile</span><p>Long-form decision support shown on the entity page.</p></div>
<label>Description<textarea name="description" rows="5"><?=e((string)$entity['description'])?></textarea></label>
<label>Editorial observation<textarea name="observation" rows="5"><?=e((string)$entity['observation'])?></textarea></label>
<label>Best suited for <small>comma separated</small><input name="best_for" value="<?=e(implode(', ', (array)$entity['best_for']))?>"></label>
<div class="admin-form-grid rich-textareas">
<label>Why it stands out <small>one item per line</small><textarea name="standout" rows="6"><?=e(implode("\n", (array)$entity['standout']))?></textarea></label>
<label>What we liked <small>one item per line</small><textarea name="liked" rows="6"><?=e(implode("\n", (array)$entity['liked']))?></textarea></label>
<label>What to consider <small>one item per line</small><textarea name="consider" rows="6"><?=e(implode("\n", (array)$entity['consider']))?></textarea></label>
</div>

<div class="admin-section-head"><span>Remote photography</span><p>Only URLs are stored. Image files are never copied into ExCompass.</p></div>
<div class="remote-media-admin-note">Allowed image hosts: <?=e(implode(', ', $remoteImages->allowedHosts()))?>. Demo photos are representative unless “verified entity imagery” is checked.</div>
<label>Hero image URL<input type="url" name="hero_image_url" value="<?=e((string)($entity['hero_image_url']??''))?>" placeholder="https://images.unsplash.com/..."></label>
<label>Gallery image URLs <small>one HTTPS URL per line, maximum 8</small><textarea name="gallery_image_urls" rows="6"><?=e($galleryText)?></textarea></label>
<div class="admin-form-grid">
<label>Image alt text<input name="image_alt" value="<?=e((string)($entity['image_alt']??''))?>"></label>
<label>Source name<input name="image_source_name" value="<?=e((string)($entity['image_source_name']??''))?>" placeholder="Official website, Unsplash, Wikimedia..."></label>
<label>Source page URL<input type="url" name="image_source_url" value="<?=e((string)($entity['image_source_url']??''))?>"></label>
<label>Credit / licence note<input name="image_credit" value="<?=e((string)($entity['image_credit']??''))?>"></label>
</div>
<label class="admin-check"><input type="checkbox" name="image_verified" value="1" <?=!empty($entity['image_verified'])?'checked':''?>> Verified as imagery of this specific entity</label>

<div class="admin-section-head"><span>Location intelligence</span><p>Working map coordinates, nearby travel times and locality scores.</p></div>
<div class="admin-form-grid">
<label>Latitude<input type="number" step="any" name="lat" value="<?=e((string)($entity['coordinates'][0]??''))?>"></label>
<label>Longitude<input type="number" step="any" name="lng" value="<?=e((string)($entity['coordinates'][1]??''))?>"></label>
</div>
<div class="admin-form-grid">
<label>Nearby <small>one per line: Label|Time</small><textarea name="nearby" rows="6"><?=e($nearbyText)?></textarea></label>
<label>Locality scores <small>one per line: Label|0-100</small><textarea name="locality_scores" rows="6"><?=e($localityText)?></textarea></label>
</div>

<div class="admin-section-head"><span>Evidence & version</span><p>Provenance displayed publicly and retained through later DB migration.</p></div>
<div class="admin-form-grid">
<label>Reviewer<input name="reviewer" value="<?=e((string)$entity['reviewer'])?>"></label>
<label>Review date<input type="date" name="review_date" value="<?=e((string)$entity['review_date'])?>"></label>
<label>Score version<input name="score_version" value="<?=e((string)$entity['score_version'])?>"></label>
</div>
<label>Evidence notes <small>one item per line</small><textarea name="evidence" rows="7"><?=e(implode("\n", (array)$entity['evidence']))?></textarea></label>

<button class="admin-primary" type="submit">Save complete profile</button>
</form>
</section>
<aside class="admin-panel workflow-sidebar"><div class="admin-panel-head"><h2>Editorial workflow</h2><span class="status status-<?=e($entity['status'])?>"><?=e($entity['status'])?></span></div><p class="admin-muted">Rich profile fields use the same role controls as the core entity. Commercial users cannot alter editorial scores or publish rankings.</p><div class="admin-profile-summary"><span>Public profile</span><b><?=e($detail['singular'])?></b><small><?=e((string)$entity['category'])?> · <?=e((string)$entity['tier'])?></small><strong><?=e((string)$entity['score'])?></strong></div><?php if(!$transitions):?><p>No workflow action available for your role at this state.</p><?php else:?><?php foreach($transitions as $to):?><form class="transition-form" method="post"><input type="hidden" name="_token" value="<?=e(Csrf::token())?>"><input type="hidden" name="action" value="transition"><input type="hidden" name="to" value="<?=e($to)?>"><button type="submit"><?=e(ucfirst($entity['status']))?> → <?=e(ucfirst($to))?></button></form><?php endforeach;?><?php endif;?></aside>
</div>
<?php admin_footer(); ?>
