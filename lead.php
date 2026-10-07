<?php
declare(strict_types=1);
require __DIR__.'/runtime.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Method not allowed');}
$return=safe_return_path($_POST['return']??null);
if(!\ExCompass\Support\Csrf::valid($_POST['_token']??null)){http_response_code(419);header('Location: '.$return);exit;}
$name=trim((string)($_POST['name']??''));$contact=trim((string)($_POST['contact']??''));$consent=($_POST['consent']??'')==='1';
if($name===''||$contact===''||!$consent){header('Location: '.$return);exit;}
$record=['id'=>bin2hex(random_bytes(8)),'created_at'=>gmdate('c'),'name'=>mb_substr($name,0,80),'contact'=>mb_substr($contact,0,120),'message'=>mb_substr(trim((string)($_POST['message']??'')),0,600),'city'=>mb_substr((string)($_POST['city']??current_city_slug()),0,50),'vertical'=>mb_substr((string)($_POST['vertical']??''),0,50),'entity'=>mb_substr((string)($_POST['entity']??''),0,100),'type'=>mb_substr((string)($_POST['type']??'enquiry'),0,30),'consent'=>true,'ip_hash'=>hash('sha256',($_SERVER['REMOTE_ADDR']??'').'|excompass')];
$store->update('leads.json',static function(array $items)use($record):array{$items[]=$record;return $items;});
header('Location: '.$return);
exit;
