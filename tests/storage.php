<?php
declare(strict_types=1);

require_once dirname(__DIR__).'/app/Domain/Catalog/EntityRepositoryInterface.php';
require_once dirname(__DIR__).'/app/Domain/Catalog/MutableEntityRepositoryInterface.php';
require_once dirname(__DIR__).'/app/Domain/Catalog/DemoEntityCatalog.php';
require_once dirname(__DIR__).'/app/Domain/Catalog/EntityRepository.php';
require_once dirname(__DIR__).'/app/Domain/Auth/UserRepositoryInterface.php';
require_once dirname(__DIR__).'/app/Domain/Auth/Access.php';
require_once dirname(__DIR__).'/app/Domain/Editorial/EntityWorkflow.php';
require_once dirname(__DIR__).'/app/Infrastructure/Storage/JsonStore.php';
require_once dirname(__DIR__).'/app/Infrastructure/Storage/JsonEntityRepository.php';
require_once dirname(__DIR__).'/app/Infrastructure/Storage/JsonUserRepository.php';

use ExCompass\Domain\Auth\Access;
use ExCompass\Domain\Catalog\EntityRepository;
use ExCompass\Domain\Editorial\EntityWorkflow;
use ExCompass\Infrastructure\Storage\JsonEntityRepository;
use ExCompass\Infrastructure\Storage\JsonStore;
use ExCompass\Infrastructure\Storage\JsonUserRepository;

$dir=sys_get_temp_dir().'/excompass_test_'.bin2hex(random_bytes(4));
$store=new JsonStore($dir);
$entities=new JsonEntityRepository($store,new EntityRepository());

$checks=[];
$checks['105 seed fallback']=count($entities->all())===105;
$entities->save(['vertical'=>'schools','slug'=>'test-school','name'=>'Test School','location'=>'Baner','score'=>0,'highlight'=>'Test','tags'=>['CBSE'],'status'=>'draft']);
$savedSchool=$entities->findInCity('pune','schools','test-school');
$checks['entity persisted']=($savedSchool['name']??'')==='Test School';
$checks['entity city normalized']=($savedSchool['city_slug']??'')==='pune';
$checks['entity area normalized']=($savedSchool['area_slug']??'')==='baner';
$checks['seed retained after write']=count($entities->all())===106;

$users=new JsonUserRepository($store);
$user=$users->save(['name'=>'Editor','email'=>'editor@example.com','password_hash'=>password_hash('temporary-test-password',PASSWORD_DEFAULT),'roles'=>['editor'],'status'=>'active']);
$checks['user persisted']=($users->findByEmail('editor@example.com')['id']??0)===$user['id'];
$checks['editor is not admin']=!$users->hasAdmin();
$firstAdmin=$users->createFirstAdmin('Administrator','admin@example.com',password_hash('temporary-admin-password',PASSWORD_DEFAULT));
$checks['first admin created']=in_array('admin',$firstAdmin['roles']??[],true)&&$users->hasAdmin();
$duplicateBlocked=false;
try{$users->createFirstAdmin('Second Admin','admin2@example.com',password_hash('temporary-admin-password',PASSWORD_DEFAULT));}catch(InvalidArgumentException){$duplicateBlocked=true;}
$checks['second first-admin bootstrap blocked']=$duplicateBlocked;

$workflow=new EntityWorkflow(new Access());
$checks['editor can approve']=$workflow->canTransition($user,'review','approved');
$checks['editor cannot submit draft']=!$workflow->canTransition($user,'draft','review');

foreach($checks as $label=>$ok) echo($ok?'PASS':'FAIL').'  '.$label.PHP_EOL;

foreach(glob($dir.'/*')?:[] as $file) @unlink($file);
@rmdir($dir);
exit(in_array(false,$checks,true)?1:0);
