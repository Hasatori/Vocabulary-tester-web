<?php
require_once 'php/Libraries.php';
buildHeader(gettext("AA_HEADING"));

if(isset($_GET['accessToken'])){

           $accessToken=$_GET['accessToken'];

     
 if(activateAccount(@$accessToken)){
     buildSuccess(gettext("AA_SUCCESS_MSG"));
   
 }else{
     buildError($_SESSION['error'][1]);
       
     unset($_SESSION['error']);
 
}

 }else{
     header("location:".BASE);
     exit();
 }

?>
</body>
</html>

