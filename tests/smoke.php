<?php
declare(strict_types=1);

require dirname(__DIR__).'/app/bootstrap.php';

$counts=[];
foreach($entities->all() as $entity){
    $counts[$entity['vertical']]=($counts[$entity['vertical']]??0)+1;
}
$godrej=find_published_entity('real-estate','godrej-emerald-waters','pune');
$puneAreas=areas_for_city('pune');
$banerArea=array_values(array_filter($puneAreas,static fn(array $area):bool=>$area['slug']==='baner'));

$checks=[
    '17 verticals'=>count($verticals->all())===17,
    'one active city only'=>count($cities->active())===1,
    'Pune is default city'=>($cities->default()['slug']??'')==='pune',
    '105 demo entities'=>count($entities->all())===105,
    '25 real estate entities'=>($counts['real-estate']??0)===25,
    '5 per secondary vertical'=>count(array_filter($verticals->all(),static function(array $v)use($counts):bool{
        return $v['slug']==='real-estate'||($counts[$v['slug']]??0)===5;
    }))===17,
    'all demo data published'=>count(array_filter($entities->all(),static fn(array $e):bool=>($e['status']??'')==='published'))===105,
    'all entities city normalized'=>count(array_filter($entities->all(),static fn(array $e):bool=>($e['city_slug']??'')==='pune'))===105,
    'all entities have area slug'=>count(array_filter($entities->all(),static fn(array $e):bool=>trim((string)($e['area_slug']??''))!==''))===105,
    'Baner area drilldown exists'=>count($banerArea)===1,
    'area scoped dataset'=>count(published_entities('pune','baner'))>=1,
    'ranking order'=>($ranking->rank(published_for_vertical('real-estate','pune'))[0]['score']??0)>=90,
    'rich profile category'=>($godrej['category']??'')==='Apartment',
    'rich profile budget'=>($godrej['tier']??'')==='₹1–2 Cr',
    'rich profile breakdown'=>count($godrej['breakdown']??[])===7,
    'rich profile evidence'=>count($godrej['evidence']??[])>=4,
    'remote hero image'=>str_starts_with((string)($godrej['hero_image_url']??''),'https://images.unsplash.com/'),
    'remote gallery images'=>count($godrej['gallery_image_urls']??[])>=2,
    'remote image marked representative'=>($godrej['image_verified']??true)===false,
    'reject http image'=>$remoteImages->sanitize('http://images.unsplash.com/photo.jpg')===null,
    'reject unapproved host'=>$remoteImages->sanitize('https://example.com/photo.jpg')===null,
    'search Baner'=>count($search->search(published_entities('pune'),$verticals->all(),'Baner'))>=5,
    'subdirectory URL'=>u('search.php')===(($config['base_path']?:'').'/search.php'),
    'city URL'=>city_url('pune')===(($config['base_path']?:'').'/city/pune/'),
    'area URL'=>area_url('pune','baner')===(($config['base_path']?:'').'/city/pune/area/baner/'),
    'city vertical URL'=>vertical_url('real-estate','pune')===(($config['base_path']?:'').'/city/pune/real-estate/'),
];

foreach($checks as $label=>$ok) echo($ok?'PASS':'FAIL').'  '.$label.PHP_EOL;
exit(in_array(false,$checks,true)?1:0);
