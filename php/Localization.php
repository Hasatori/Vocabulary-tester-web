<?php

/**
 * Bude upraveno...  
 *
 *  @author Oldřich Hradil 
 * 
 **/
$locale ='en_US';
 if(isset($_COOKIE['language'])){
    $locale=$_COOKIE['language'];    
}

 if(isset($_POST['language'])){
  $locale=$_POST['language'];
  
 setcookie('language', $locale, time() + (10 * 365 * 24 * 60 * 60), "/",'',true,true);
}





putenv("LC_ALL=".$locale);
setlocale(LC_ALL, $locale);
$domain='messages';
$currentDirectory= explode('/', getcwd());
$currentDirectory[count($currentDirectory)-1]==='member'|$currentDirectory[count($currentDirectory)-1]==='test'?
       
        bindtextdomain($domain, realpath("../locale")):
     bindtextdomain($domain, realpath("./locale"));

bind_textdomain_codeset($domain, "UTF-8");
textdomain($domain);
