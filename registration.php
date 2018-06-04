<?php
require_once 'php/Libraries.php';
if (checkValidUser()) {
    header("location:" . BASE . 'member/');
    exit;
}
if (isset($_POST['username']) && isset($_POST['pwdR']) && isset($_POST['pwdRC']) && isset($_POST['email'])) {
    $username = $_POST['username'];
    $password = $_POST['pwdR'];
    $passwordConfirm = $_POST['pwdRC'];
    $email = $_POST['email'];
    if ($username == '') {
        $_SESSION['error'] = array(true, gettext("USERNAME_LABEL") . ' ' . gettext("NOT_FILLED"));
    } else
    if (!checkName($username)) {
        $_SESSION['error'] = array(true, gettext('VALIDATE_NAME'));
    } else if ($password == '') {
        $_SESSION['error'] = array(true, gettext("PWD_LABEL") . ' ' . gettext("NOT_FILLED"));
    } else if (!checkPassword($password)) {
        $_SESSION['error'] = array(true, gettext('R_PWD_ERROR_MSG1'));
    } else if ($passwordConfirm == '') {
        $_SESSION['error'] = array(true, gettext("PWDC_LABEL") . ' ' . gettext("NOT_FILLED"));
    } else if (!checkPasswordsMatch($password, $passwordConfirm)) {
        $_SESSION['error'] = array(true, gettext('R_PWD_ERROR_MSG2'));
    } else if ($email == '') {
        $_SESSION['error'] = array(true, gettext("EMAIL_LABEL") . ' ' . gettext("NOT_FILLED"));
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = array(true, gettext('VALIDATE_EMAIL'));
    } else if (!checkUsernameUnique($username)) {
        $_SESSION['error'] = array(true, gettext('R_NAMECHECK'));
    } else if (!checkEmailUnique($email)) {
        
    } else {
        $result = sendRegistrationForm($username, $password, $email);
        if ($result !== true) {
            $_SESSION['error'] = array(true, $result);
        } else {
            $_SESSION['success'] = array(true, gettext("R_SUCCESS_MSG"));
            header('location:' . BASE);
            exit();
        }
    }
}
buildHeader(gettext("R_HEADING"));
buildNavigationBar(false, gettext("R_HEADING"));
?>





<div class="container formWrapper">

    <form class="form-horizontal"  method="post" id="registrationForm"  >
        <input type="text" style="display:none">
        <input type="password" style="display:none">
        <div class="form-group">
            <label class="control-label col-sm-5" for="username"><?php echo gettext("USERNAME_LABEL") ?></label>
            <div class="col-sm-7">


                <input class="form-control"  placeholder="<?php echo gettext("USERNAME_PLC") ?>" id="username" 
                       value="<?php echo @htmlspecialchars($_POST['username'], ENT_QUOTES) ?>" autocomplete="off" name="username" required >

            </div>
        </div>

        <div class="form-group">
            <span class="glyphicon glyphicon-warning-sign warningMessage"></span>
            <label class="control-label col-sm-5" for="pwdR"><?php echo gettext("PWD_LABEL") ?></label>
            <div class="col-sm-7 ">

                <input type="password" class="form-control" id="pwdR" placeholder="<?php echo gettext("PWD_PLC") ?>" 
                       value="<?php echo @htmlspecialchars($_POST['pwdR'], ENT_QUOTES) ?>" autocomplete="off" name="pwdR"  required >


            </div>

        </div>
        <div class="form-group">
            <label class="control-label col-sm-5" for="pwdRC"><?php echo gettext("PWDC_LABEL") ?></label>
            <div class="col-sm-7">
                <input type="password" class="form-control" id="pwdRC" placeholder="<?php echo gettext("PWD_PLC") ?>" 
                       value="<?php echo @htmlspecialchars($_POST['pwdRC'], ENT_QUOTES) ?>" autocomplete="off" name="pwdRC"   required >

            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-5" for="email" ><?php echo gettext("EMAIL_LABEL") ?></label>
            <div class="col-sm-7">
                <input type="email" class="form-control" id="email" placeholder="<?php echo gettext("EMAIL_PLC") ?>" value="<?php echo @htmlspecialchars($_POST['email'], ENT_QUOTES) ?>" 
                       autocomplete="off" name="email" required>

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
?>


