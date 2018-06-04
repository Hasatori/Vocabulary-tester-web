<?php
require_once '../php/Libraries.php';
session_start();
if(getClient()['role']!='ADMIN'){
header('location:'.BASE);
exit();
}
if(isset($_POST['loadBackup'])){
    
$report = '';
  $dir='../backup';
   $result = array();

   $cdir = scandir($dir);
   foreach ($cdir as $key => $value)
   {
      if (!in_array($value,array(".","..")))
      {
         if (is_dir($dir . DIRECTORY_SEPARATOR . $value))
         {
            $result[$value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value);
         
         }
         else
         {
            $result[] = $value;
         }
      }
   }
$key= array_search('Hasatori_dictionaries.json', $result);
$json= json_decode(file_get_contents('../backup/Hasatori_dictionaries.json'),TRUE);

$id= getClient()['id'];
foreach ($json as $item){
    $name=$item['dictionary_name'];
   addDictionary($name, $item['first_lang'],$item['second_lang']);
   $json2= json_decode(file_get_contents('../backup/Hasatori_'.$name.'.json'),TRUE);
   $report = $report . '<br/>' .'<p class="info">'.htmlspecialchars($name,ENT_QUOTES).'</p>';
   foreach ($json2 as $item2){
     $db = connectToDatabase();
  
    $query = $db->prepare("SELECT id_dictionary FROM dictionary where "
            . "id=$id AND dictionary_name='$name'");
    $query->execute();

       addVocabulary(   $query->fetchAll()[0]['id_dictionary'],$item2['first_value'],$item2['second_value']);  
        $report = $report . '<br/> <p class="default">' .htmlspecialchars($item2['first_value'],ENT_QUOTES).'->'.htmlspecialchars($item2['second_value'],ENT_QUOTES). '</p>';
   }
}
echo $report;
unset($_SESSION['error']);
exit();
}else{
    header('location:'.BASE);
}