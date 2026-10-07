<?php
declare(strict_types=1);

require dirname(__DIR__).'/app/bootstrap.php';

$counts=[];
foreach($entities->all() as $entity){
    $counts[$entity['vertical']]=($counts[$entity['vertical']]??0)+1;
}
$godrej=find_published_entity('real-estate','godrej-emerald-waters');

$checks=[
    '17 verticals'=>count($verticals->all())===17,
    '105 demo entities'=>count($entities->all())===105,
    '25 real estate entities'=>($counts['real-estate']??0)===25,
    '5 per secondary vertical'=>count(array_filter($verticals->all(),static function(array $v)use($counts):bool{
        return $v['slug']==='real-estate'||($counts[$v['slug']]??0)===5;
    }))===17,
    'all demo data published'=>count(array_filter($entities->all(),static fn(array $e):bool=>($e['status']??'')==='published'))===105,
    'ranking order'=>($ranking->rank(published_for_vertical('real-estate'))[0]['score']??0)>=90,
    'rich profile category'=>($godrej['category']??'')==='Apartment',
    'rich profile budget'=>($godrej['tier']??'')==='₹1–2 Cr',
    'rich profile breakdown'=>count($godrej['breakdown']??[])===7,
    'rich profile evidence'=>count($godrej['evidence']??[])>=4,
    'search Baner'=>count($search->search(published_entities(),$verticals->all(),'Baner'))>=5,
    'subdirectory URL'=>u('search.php')===(($config['base_path']?:'').'/search.php'),
];

foreach($checks as $label=>$ok) echo($ok?'PASS':'FAIL').'  '.$label.PHP_EOL;
exit(in_array(false,$checks,true)?1:0);
