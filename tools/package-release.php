<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$out = $root . '/release';
$public = $out . '/public';
$private = $out . '/private';

$remove = function (string $path) use (&$remove): void {
    if (!file_exists($path) && !is_link($path)) return;
    if (is_file($path) || is_link($path)) { @unlink($path); return; }
    foreach (array_diff(scandir($path) ?: [], ['.','..']) as $name) $remove($path . '/' . $name);
    @rmdir($path);
};
$copy = function (string $src, string $dst) use (&$copy): void {
    if (is_dir($src)) {
        if (!is_dir($dst) && !mkdir($dst, 0755, true) && !is_dir($dst)) throw new RuntimeException("mkdir failed: {$dst}");
        foreach (array_diff(scandir($src) ?: [], ['.','..']) as $name) {
            if (in_array($name, ['.git','.github','release'], true)) continue;
            $copy($src . '/' . $name, $dst . '/' . $name);
        }
        return;
    }
    if (!is_dir(dirname($dst)) && !mkdir(dirname($dst), 0755, true) && !is_dir(dirname($dst))) throw new RuntimeException("mkdir failed: " . dirname($dst));
    if (!copy($src, $dst)) throw new RuntimeException("copy failed: {$src}");
};

$remove($out);
mkdir($public, 0755, true);
mkdir($private . '/build', 0755, true);

$publicFiles = ['.htaccess','runtime.php','index.php','rankings.php','entity.php','search.php','methodology.php','health.php','compare.php','report.php','brochure.php','badge.php','lead.php'];
$publicDirs = ['assets','admin'];
$privateDirs = ['app','config','bin','database'];

foreach ($publicFiles as $name) {
    if (!is_file($root . '/' . $name)) throw new RuntimeException("required file missing: {$name}");
    $copy($root . '/' . $name, $public . '/' . $name);
}
foreach ($publicDirs as $name) {
    if (!is_dir($root . '/' . $name)) throw new RuntimeException("required directory missing: {$name}");
    $copy($root . '/' . $name, $public . '/' . $name);
}
foreach ($privateDirs as $name) {
    if (!is_dir($root . '/' . $name)) throw new RuntimeException("required directory missing: {$name}");
    $copy($root . '/' . $name, $private . '/' . $name);
}

$sourceSha = (string) (getenv('GITHUB_SHA') ?: 'local');
$build = [
    'schema'=>'DIGIOPS-RELEASE/1','name'=>'ExCompass','version'=>'1.2.0','builtAt'=>date(DATE_ATOM),'sourceSha'=>$sourceSha,
    'branch'=>(string)(getenv('GITHUB_REF_NAME')?:'local'),'ciRunNumber'=>(string)(getenv('GITHUB_RUN_NUMBER')?:''),
    'ciRunId'=>(string)(getenv('GITHUB_RUN_ID')?:''),'ciRunAttempt'=>(string)(getenv('GITHUB_RUN_ATTEMPT')?:''),
    'public'=>'public','private'=>'private','publicPath'=>'public_html/excompass/','privatePath'=>'private_html/excompass/','persistentPaths'=>['storage/'],
];
$json=json_encode($build,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES).PHP_EOL;
file_put_contents($out.'/RELEASE.json',$json);
file_put_contents($private.'/build/release.json',$json);
echo "ExCompass DigiOps release built for {$sourceSha}".PHP_EOL;
