<?php
require_once 'php/Libraries.php';
buildHeader( gettext('NPWDA_HEADING'));

if(isset($_GET['accessToken'])){

           $accessToken=$_GET['accessToken'];

 if(activateNewPassword(@$accessToken)){
     buildSuccess(gettext('NPWDA_SUCCESS_MSG'));

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
