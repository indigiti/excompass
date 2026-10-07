<?php
declare(strict_types=1);

require_once dirname(__DIR__).'/app/Domain/Catalog/EntityRepositoryInterface.php';
require_once dirname(__DIR__).'/app/Domain/Catalog/MutableEntityRepositoryInterface.php';
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
$checks['seed fallback']=count($entities->all())>=17;
$entities->save(['vertical'=>'schools','slug'=>'test-school','name'=>'Test School','location'=>'Pune','score'=>0,'highlight'=>'Test','tags'=>['CBSE'],'status'=>'draft']);
$checks['entity persisted']=($entities->find('schools','test-school')['name']??'')==='Test School';

$users=new JsonUserRepository($store);
$user=$users->save(['name'=>'Editor','email'=>'editor@example.com','password_hash'=>password_hash('temporary-test-password',PASSWORD_DEFAULT),'roles'=>['editor'],'status'=>'active']);
$checks['user persisted']=($users->findByEmail('editor@example.com')['id']??0)===$user['id'];

$workflow=new EntityWorkflow(new Access());
$checks['editor can approve']=$workflow->canTransition($user,'review','approved');
$checks['editor cannot submit draft']=!$workflow->canTransition($user,'draft','review');

foreach($checks as $label=>$ok) echo($ok?'PASS':'FAIL').'  '.$label.PHP_EOL;

foreach(glob($dir.'/*')?:[] as $file) @unlink($file);
@rmdir($dir);
exit(in_array(false,$checks,true)?1:0);
