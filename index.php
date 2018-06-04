<?php
require_once 'php/Libraries.php';

if (checkValidUser()) {
    header("location:" . BASE . 'member');
    exit();
}

if (isset($_GET['state'])) {

    if (loginFbUser()) {
        header("location:" . BASE . 'member/');
        exit();
    }
}
$url = getFbUserAuth();

if (isset($_POST['password']) && isset($_POST['email'])) {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $rememberMe = @$_POST['rememberMe'];
    $rememberMe = $rememberMe === 'yes' ? true : false;
    
    if ($email == '') {
        $_SESSION['error'] = array(true, gettext("EMAIL_LABEL") . ' ' . gettext("NOT_FILLED"));
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = array(true, gettext('VALIDATE_EMAIL'));
    } else
    if ($password == '') {
        $_SESSION['error'] = array(true, gettext("PWD_LABEL") . ' ' . gettext("NOT_FILLED"));
    } else if (!checkPassword($password)) {
        $_SESSION['error'] = array(true, gettext('R_PWD_ERROR_MSG1'));
    } else
    if (login($email, $password, $rememberMe)) {
        header('location:' . BASE . 'member/');
        exit();
    }
}


buildHeader(gettext('L_HEADING'));
buildNavigationBar(false, gettext('L_HEADING'));
?>
<div class="container formWrapper" >

    <span class="glyphicon glyphicon-remove errorMessage"><?php echo gettext('L_ERROR_MSG') ?></span>
    <form class="form-horizontal" id="loginForm" method="post" >

        <div class="form-group">

            <label class="control-label col-sm-2" for="email"><?php echo gettext('EMAIL_LABEL') ?></label>
            <div class="col-sm-10">
                <input type="text" name="email" class="form-control" id="email" placeholder="<?php echo gettext('EMAIL_PLC') ?>" autocomplete="off" required
                       value="<?php echo @htmlspecialchars($_POST['email'], ENT_QUOTES) ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-2" for="pwd"><?php echo gettext('PWD_LABEL') ?></label>
            <div class="col-sm-10">
                <input type="password" name="password" class="form-control" id="pwd" placeholder="<?php echo gettext('PWD_PLC') ?>" autocomplete="off" required
                       value="<?php echo @htmlspecialchars($_POST['password'], ENT_QUOTES) ?>">

            </div>

        </div>
        <div class="form-check">
            <div class="col-sm-offset-2 col-sm-10">
                <input class="form-check-input" type="checkbox" value="yes" id="rememberMe"
                       name="rememberMe">

                <label class="form-check-label" for="rememberMe">
<?php echo gettext('L_REMEMBER_ME') ?>
                </label>
            </div></div>


        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-4">
                <button type="submit" class="btn btn-default"><?php echo gettext('L_SUBMIT') ?></button>
            </div>
            <div class="col-sm-6">

                <label><a href="forgottenPassword" ><?php echo gettext('L_FORGOTTENPSWD') ?>  </a></label>

            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-8">
                <button type="button" class="btn btn-block btn-social btn-facebook"
                        onclick="fbLogin('<?= $url ?>')"

                        >
                    <span class="fa fa-facebook"></span> <?php echo gettext('L_FB_LOGIN_BTN') ?>
                </button>


            </div>

        </div>
    </form> 
</div>    


<?php
buildFooter();
?>

