<?php
declare(strict_types=1);
require dirname(__DIR__).'/app/bootstrap.php';
$checks=[
    '17 verticals'=>count($verticals->all())===17,
    'seed entities'=>count($entities->all())>=17,
    'ranking order'=>($ranking->rank($entities->forVertical('real-estate'))[0]['score']??0)>=90,
    'search Baner'=>count($search->search($entities->all(),$verticals->all(),'Baner'))>=2,
    'subdirectory URL'=>u('search.php')===(($config['base_path']?:'').'/search.php'),
];
foreach($checks as $label=>$ok)echo($ok?'PASS':'FAIL').'  '.$label.PHP_EOL;
exit(in_array(false,$checks,true)?1:0);
