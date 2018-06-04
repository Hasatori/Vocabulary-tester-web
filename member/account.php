<?php
require_once '../php/Libraries.php';
if(!checkValidUser()){
     header("location:".BASE);
 exit();    
}
if(!empty($_POST)){
   $currentPassword=@$_POST['currentPassword']; 
      $newPassword=@$_POST['newPassword'];
      $newPasswordAgain=@$_POST['newPasswordAgain'];
      $email= getClient()['email'];
      if($currentPassword===null || $currentPassword==''){
           $_SESSION['error'] = array(true,'Současné heslo není vyplněno');
      }else if($newPassword===null || $newPassword==''){
           $_SESSION['error'] = array(true,'Nové heslo není vyplněno');
      } else if($newPasswordAgain==null || $newPasswordAgain==''){
           $_SESSION['error'] = array(true,'Nové heslo znovu není vyplněno');
      } else if(!checkPasswordsMatch($newPassword, $newPasswordAgain)){
          $_SESSION['error'] = array(true, gettext('R_PWD_ERROR_MSG2'));
           
      }else if (!checkPassword($currentPassword)) {
        $_SESSION['error'] = array(true, gettext('R_PWD_ERROR_MSG1'));
        
        
    } else if (!checkPassword($newPassword)) {
        $_SESSION['error'] = array(true, gettext('R_PWD_ERROR_MSG1'));
        
        
    }else if (!checkPassword($newPasswordAgain)) {
        $_SESSION['error'] = array(true, gettext('R_PWD_ERROR_MSG1'));
        
        
    }else if(!login($email,$currentPassword)){
          
      }else if(!changePassword($newPassword, $email)){
           $_SESSION['error'] = array(true, gettext('Nastala chyba při změně hesla!'));
      }else{
           $_SESSION['success'] = array(true, gettext('Heslo bylo úspěšně změněno'));
          
      }
     
}
buildHeader(gettext('STNGS_ACCOUNT_LB'));
buildNavigationBar(true,gettext('STNGS_ACCOUNT_LB'));
$client= getClient();
?>

<div class="row clientData">
       <div class="col-sm-3"></div>
       <div class="col-sm-2">
           <img src="<?php echo BASE?>img/account/user.svg" class="rounded center-block" width="300" height="300" alt="user image">
       </div>
              <div class="col-sm-1"></div>
    <div class="col-sm-3 ">
     
        <div class="text-center"> <?php echo gettext('CLIENT_NAME_LBL').'   '. htmlspecialchars($client['clientName'],ENT_QUOTES) ?></div>
   
       
        <div class="text-center">Email: <?php echo gettext('CLIENT_EMAIL_LBL').'    '. htmlspecialchars($client['email'],ENT_QUOTES) ?></div>
  
    </div>
    <div class="col-sm-3"></div>
    
</div>
<div class="container formWrapper">

    <form class="form-horizontal"  method="post" id="changePasswordForm"  >
        <input type="text" style="display:none">
        <input type="password" style="display:none">
        <h3 class="h3">Změna hesla</h3>
        <br/>
        <br/>
     
        <div class="form-group">
            <label class="control-label col-sm-5" for="currentPassword">Současné heslo:</label>
            <div class="col-sm-7">


                <input type="password" class="form-control"  placeholder="<?php echo gettext("PWD_PLC") ?>" id="currentPassword" 
                       value="" autocomplete="off" name="currentPassword" required >

            </div>
        </div>

        <div class="form-group">
            <span class="glyphicon glyphicon-warning-sign warningMessage"></span>
            <label class="control-label col-sm-5" for="newPassword">Nové heslo:</label>
            <div class="col-sm-7 ">

                <input type="password" class="form-control" id="newPassword" placeholder="<?php echo gettext("PWD_PLC") ?>" 
                       value="<?php echo @htmlspecialchars($_POST['newPassword'], ENT_QUOTES) ?>" autocomplete="off" name="newPassword"  required >


            </div>

        </div>
        <div class="form-group">
            <label class="control-label col-sm-5" for="newPasswordAgain">Nové heslo znovu:</label>
            <div class="col-sm-7">
                <input type="password" class="form-control" id="newPasswordAgain" placeholder="<?php echo gettext("PWD_PLC") ?>" 
                       value="<?php echo @htmlspecialchars($_POST['newPasswordAgain'], ENT_QUOTES) ?>" autocomplete="off" name="newPasswordAgain"   required >

            </div>
        </div>
       

        <div class="form-group">
            <div class="col-sm-offset-7 col-sm-7">
                <button type="submit" class="btn btn-default"><?php echo gettext("R_SUBMIT") ?></button>
            </div>

        </div>
    </form> 
</div>    

<?php 
buildFooter();
