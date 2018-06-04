<?php
/**
Vymaže ze SESSION hodnoty určující uživatele a poté SESSION zničí. 
 *
 *  @author Oldřich Hradil 
 * 
 **/
require_once 'Libraries.php';
@session_start();
if(!empty($_COOKIE['rememberMe'])){
        $db=connectToDatabase(); 

$series_id= $_COOKIE['rememberMe'];

  $query= $db->prepare("DELETE FROM remember_me "
. "where seriesId=:SERIES_id"); 

   $query->execute([':SERIES_id'=>$series_id]); 
setcookie('rememberMe',null, -1, '/');

}

unset($_SESSION);
session_regenerate_id();
session_destroy();
header('location:'.BASE);
exit();
