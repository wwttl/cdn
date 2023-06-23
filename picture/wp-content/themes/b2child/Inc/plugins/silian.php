<?php
$error_url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

$error_log = "silian.txt";  

$entries = file($error_log);  

$check=true;  

foreach($entries as $f){  

if($f == $error_url."\n")  

$check = false;  

}  

if($check){  

$fp = fopen($error_log,"a");  

flock ($fp, LOCK_EX) ;  

fwrite ($fp, $error_url."\n");  

flock ($fp, LOCK_UN);  

fclose ($fp);  

}