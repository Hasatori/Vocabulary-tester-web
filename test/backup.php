<?php
require_once '../php/Libraries.php';
session_start();
if(getClient()['role']!='ADMIN'){
header('location:'.BASE);
exit();
}
if(isset($_POST['backup'])){
 
try{
    $dictionaries=getDictionaries(getClient()['id']);
   $name=getClient()['clientName'];
    foreach ($dictionaries as $dictionary ){
        $file=__DIR__.'/../backup/'.$name.'_'.$dictionary["dictionary_name"].'.json';
   
         file_put_contents($file,getDictionaryContent($dictionary['id_dictionary']));
         
    }
 file_put_contents(__DIR__.'/../backup/'.$name.'_'.'dictionaries.json', json_encode($dictionaries));

$_SESSION['success']=array(true, gettext('TESTING_LOAD_BACKUP_SUCCESS')); 

exit();
} catch (Exception $ex){
    $_SESSION['error']=array(true, gettext('SQL_ERROR')); 
    exit();
}
}else{
  header('location:'.BASE.'test/');  
}


