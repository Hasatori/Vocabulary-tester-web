<?php
require_once 'php/Libraries.php';
if(checkValidUser()){
  header("location:".BASE.'member');
  exit();
}
if(isset($_POST['email'])) {
   
    $email=$_POST['email']; 
if(!checkEmailExists($email)){
$_SESSION['error']= array(true, gettext('FPWD_EMAIL_ERROR_MSG'));
}else{

if(sendForgottenPasswordForm($email)){
$_SESSION['success']=array(true,gettext('FPWD_SUCCESS_MSG'));
header('location:'.BASE);
exit();
}else{
  $_SESSION['error']= array(true, gettext('SQL_ERROR')); 
}
}

}


buildHeader(gettext('FPWD_HEADING'));
buildNavigationBar(false,gettext('FPWD_HEADING'));
?>




<div class="container formWrapper">

     <div class="row"><p><?php echo gettext('FPWD_MSG') ?></p></div>
     <form class="form-horizontal" method="post" id="forgottenPasswordForm">

  <div class="form-group">
    <label class="control-label col-sm-2" for="email"><?php echo gettext('EMAIL_LABEL') ?></label>
    <div class="col-sm-10">
        <input type="email" class="form-control" id="email" placeholder="<?php echo gettext('EMAIL_PLC') ?>" 
                required autocomplete="off" name="email"
               value="<?php echo @htmlspecialchars($_POST['email']) ?>">
          
            
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-4">
      <button type="submit" class="btn btn-default"><?php echo gettext('FPWD_SUBMIT') ?></button>
   
    </div>
  </div>
        <div class="form-group">
            <label><a href="<?php echo BASE ?>" ><?php echo gettext('L_BACK_TO') ?>  </a></label>
    </div>
    
</form> 
</div>    

<?php
buildFooter();
?>


