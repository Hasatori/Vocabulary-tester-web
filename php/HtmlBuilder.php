<?php 
/**
 * Knihovna obsahující funkce pro generování jednotlivých části stránky.
  *
 *  @author Oldřich Hradil 
 * 
 **/

/**
 * Posktuje záklaví stránky. 
 * @param string $title Titulek příslušné stránky.
 * @param string $keywords Nepovinný parametr, který specifikuje klíčová slova,
 * jež mají být použita na příslušné stránkce. 
 * @return htmlcode 
 */
function buildHeader($title,$keywords=null){ 
    global $locale;
    ?><!DOCTYPE html>
<html lang="<?php echo preg_split("/_/",$locale)[0]?>">
<head>
<meta charset="UTF-8">
<title><?php echo gettext('APPLICATION_NAME') ?> - <?php echo $title ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="keywords" content="<?php $keywords ?>">
  <link href="<?php echo BASE ?>css/style.css" rel="stylesheet"/>
   <link href="<?php echo BASE ?>test/index.css" rel="stylesheet"/>
<link rel="stylesheet" href="<?php echo BASE ?>css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo BASE ?>css/bootstrap-social.css">
    <link href="<?php  echo BASE ?>css/font-awesome.css" rel="stylesheet">
  
   <!--<link href="<?php echo BASE ?>css/min.css" rel="stylesheet">-->
<base href="<?php echo BASE ?>"/>

</head>
<body>


<?php } 

/**
 * Poskytuje navigační lištu stránky. Rozlišuje zda je uživatel přihlášen nebo
 * ne a podle toho je tato lišta přizpůsobena. 
 * @param bool $logged Parametr určující zda je uživatel přihlášen nebo ne.
 * * @return htmlcode 
 */
 function buildNavigationBar($logged,$heading){?>
  
    <nav class="navbar navbar-inverse">
  <div class="container-fluid">
      <button type="button" id="nav-icon1" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>

      <div class="navbar-header">
      <p class="navbar-brand" onclick="move($('#index').position(),$('#index').width(),'<?php echo BASE ?>')"><?php echo gettext('APPLICATION_NAME') ?></p>
    </div>
     <div class="collapse navbar-collapse" id="myNavbar">

         <ul class="nav navbar-nav"  >
          
    <?php if($logged===true){?>
             <?php if(getClient()['role']==="ADMIN"){?>
    <li id="testing" ><a href="<?php echo BASE.'test' ?>"><?php echo gettext('TESTING_HEADING') ?></a></li>   
<?php }
?>
   
    <li id="home" ><a href="<?php echo BASE.'member/' ?>"><?php echo gettext('HOME_HEADING') ?></a></li>
<li id="dictionariesList"><a href="<?php echo BASE.'member/dictionariesList'?>"><?php echo gettext('DL_HEADING') ?></a></li>
<li id="practice"><a href="<?php echo BASE.'member/practice'?>"><?php echo gettext('PRAC_HEADING') ?></a></li>
<?php } ?>   
    </ul>
          <ul class="nav navbar-nav navbar-right">
                         <li class="dropdown languages">
                             <a class="dropdown-toggle" data-toggle="dropdown" href="#"><div class="glyphicon glyphicon-globe"></div>
        <div class="caret"></div></a>
        <ul class="dropdown-menu">
            <li onclick="changeLanguage('cs_CZ')"><img src="<?php echo BASE ?>img/flags/cz.svg" alt="<?php  echo gettext('CZECH_FLAG_IMG_DESCIPTION') ?>"  width="20"><?php echo gettext('CZECH') ?></li>
            <li  onclick="changeLanguage('en_US')"><img src="<?php echo BASE ?>img/flags/us.svg" alt="<?php echo gettext('AMERICAN_FLAG_IMG_DESCRIPTION') ?>" width="20"><?php echo gettext('AMERICAN_ENGLISH') ?></li> 
        </ul>
      </li>
<?php if($logged===true){?>
                                     <li class="dropdown languages" id="settings">
             <a class="dropdown-toggle" data-toggle="dropdown" href="#"><div class="glyphicon glyphicon-cog"></div>
        <div class="caret"></div></a>
        <ul class="dropdown-menu dropdown-menu-left">
            <li id="account"><a href="<?php echo BASE.'member/account'?>"><span class="glyphicon glyphicon-user"></span>    <?php  echo gettext('STNGS_ACCOUNT_LB') ?></a></li>
          
        </ul>
      </li>
           
      <li ><a href="<?php echo BASE.'php/Logout.php' ?>"  onclick="$('.loaderWrapper').attr('style','display:block;');" ><span class="glyphicon glyphicon-log-out" ></span>  <?php  echo gettext('LOGOUT_LB') ?></a></li>

          
  
<?php }
else { ?>
              <li id="registration"><a href="<?php echo BASE.'registration'?>"><span class="glyphicon glyphicon-user" ></span> <?php  echo gettext('R_HEADING') ?></a></li>
              <li id="login"><a href="<?php echo BASE?>"><span class="glyphicon glyphicon-log-in" ></span> <?php  echo gettext('L_HEADING') ?></a></li>
 <?php }

 ?>
    

    </ul>
          </div>
       
  </div>
      
</nav> 

  <div class="slider"></div>   
      <?php global $error,$success;?>  
  <div class="loaderWrapper"
       <?php if (@$error[0] || @$_SESSION['error'][0]){
           echo 'style="display:none;"';
       } ?>
       ><div class="loader"></div></div>
       
  <div class="mainContent">
      <div class="row heading"><h2 class="text-bold text-info text-center"><?php echo htmlspecialchars($heading,ENT_QUOTES) ?></h2></div>
 
<?php if(@$_SESSION['error'][0]){
     buildMessage('danger',$_SESSION['error'][1]);
     unset($_SESSION['error']);
} ?>
      
 
<?php if(@$_SESSION['info'][0]){
     buildMessage('info',$_SESSION['info'][1]);
     unset($_SESSION['info']);
} ?>
    
  <?php if(@$_SESSION['success'][0]){
     buildMessage('success',$_SESSION['success'][1]);
     unset($_SESSION['success']);
} ?>
      
    
    
    
    
    
 <?php   
 }

/**
 * Poskytuje potvrzovací dialog.
 *  @return htmlcode 
 */
 function buildYesNoDialog(){
     ?>
             <div class="modal fade" id="yesNoDialog" tabindex="-1" >
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary">
          <div class="modal-title text-bold text-center" id="yesNoDialogHeading">
              <span id="delDicHeading"><?php echo gettext("DL_DIC_DEL_DIAL_HEAD") ?></span>
              <p id="delVocHeading"><?php echo gettext("DL_VOC_DEL_DIAL_HEAD") ?></p>
             <p id="delPracHeading"><?php echo gettext("PRAC_DEL_DIAL_HEAD") ?></p>
          </div>
       
      </div>
        <div class="modal-body" id="yesNoDialogMessage">
         <p id="delDicMessage"><?php echo gettext("DL_DIC_DEL_DIAL_TXT") ?></p>
              <p id="delVocMessage"><?php echo gettext("DL_VOC_DEL_DIAL_TXT") ?></p>
               <p id="delPracMessage"><?php echo gettext("PRAC_DEL_DIAL_TXT") ?></p>
              <p class="text-info text-bold" id="dialogMessageExtra"></p>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal" id="dialogYes"><?php echo gettext('YES_NO_DIAL_POSITIVE') ?></button>
          <button type="button" class="btn btn-default" id="dialogNo"><?php echo gettext('YES_NO_DIAL_NEGATIVE') ?></button>
      </div>
    </div>
  </div>
</div>
      <?php 
 }
 
 function buildResultsDialog(){
     ?>
       <div class="modal fade" id="resultDialog" tabindex="-1">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary">

    
       <ul class="list-inline ">
  <li class="list-inline-item "><?php echo gettext("PRAC_RESULT_HEADING") ?></li>
  <li class="list-inline-item " id="resultPracticeName" ></li>

</ul>
      </div>
        <div class="modal-body" id="resultDialogMessage">
        
           <table class="table table-striped">
  <thead>
    <tr>
      <th scope="col"><?php echo gettext("PRAC_RESULT_FIRST_VAL_HEADING") ?></th>
      <th scope="col"><?php echo gettext("PRAC_RESULT_SECOND_VAL_HEADING") ?></th>
           <th scope="col"><?php echo gettext("PRAC_RESULT_RIGHT_HEADING") ?></th>
                <th scope="col"><?php echo gettext("PRAC_RESUTL_WRONG_HEADING") ?></th>
 <th scope="col"><?php echo gettext("PRAC_SESSION_SUCCESS_RATE_HEADING")?></th>
     
    </tr>
  </thead>
  <tbody id="resultTableBody">
   
  </tbody>
</table>
      </div>
      <div class="modal-footer">
           <!--<button type="button" class="btn btn-info" data-dismiss="modal" id="worstToDictionary">Z nejméně úspěšných vytvořit relaci zkoušení</button>-->          
            <button type="button" class="btn btn-info" data-dismiss="modal" id="restartPractice"><?php echo gettext("PRAC_RESULT_RESTART_BTN") ?></button>
          <button type="button" class="btn btn-danger" data-dismiss="modal" id="dialogClose"><?php echo gettext('DIALOG_CLOSE') ?></button>
          
      </div>
    </div>
  </div>
</div>
      <?php
 }

 function buildFormModal(){
     ?>
      <div class="modal fade" id="formModal" tabindex="-1" >
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary">
          <div class="modal-title text-bold text-center" id="formModalHeading">
  <p id="addDicHeading"><?php echo gettext('DL_ADD_DIC_HEADING') ?></p>
  <p id="addVocHeading"><?php echo gettext('DL_ADD_VOC_HEADING') ?></p>          
      <p id="editDicHeading"><?php echo gettext('DL_EDIT_DIC_HEADING') ?></p>   
  <p id="editVocHeading"><?php echo gettext('DL_EDIT_VOC_HEADING') ?></p> 
    <p id="editPracHeading"><?php echo gettext('PRAC_EDIT_PRAC_HEADING') ?></p> 
          </div>
      </div>
      <div class="modal-body">
       
          <form class="form-horizontal" id="dictionaryForm"   method="post">
     
     
        <input type="text" style="display:none">
<input type="password" style="display:none">
  <div class="form-group">
          <label class="control-label col-sm-5" for="dictionaryName">
              <?php echo gettext('DL_ADD_DIC_DCNAME') ?></label>
    <div class="col-sm-7">
       
        
        <input class="form-control" name="dictionaryName"  placeholder="<?php echo gettext('DL_ADD_DIC_DCNAME_PLC') ?>" id="dictionaryName" autocomplete="off" required>
        
    </div>
  </div>

  <div class="form-group">
          <label class="control-label col-sm-5" for="firstLanguage"><?php echo gettext('DL_ADD_DIC_FRST_LANG') ?></label>
    <div class="col-sm-7">
       
        
        <select class="form-control" name="firstLanguage"   id="firstLanguage">
            <option><?php echo gettext('AMERICAN_ENGLISH') ?></option>
            <option ><?php echo gettext('CZECH') ?></option>
        
        </select>
    </div>
  </div>
     <div class="form-group">
          <label class="control-label col-sm-5" for="secondLanguage"><?php echo gettext('DL_ADD_DIC_SCND_LANG') ?></label>
    <div class="col-sm-7">
       
        
         <select class="form-control"  name="secondLanguage"  id="secondLanguage">
              <option ><?php echo gettext('CZECH') ?></option>
              <option><?php echo gettext('AMERICAN_ENGLISH') ?></option>
       </select>
    </div>
         
  </div>
<input class="form-control"  name="type"  type="hidden"  id="type">
<input class="form-control"  name="dictionaryId"  type="hidden"  id="dictionaryId">
  <div class="form-group">
    <div class="col-sm-offset-7 col-sm-7">
         
     
             <ul class="list-inline ">

  <li class="list-inline-item " > <button type="submit" class="btn btn-success"><?php echo gettext('DL_ADD_DIC_BTN') ?></button></li>
  <li class="list-inline-item ">  <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo gettext('DIALOG_CLOSE') ?></button></li>
</ul>
    </div>

  </div>
</form> 
         <form class="form-horizontal"  method="post" id="vocabularyForm">
        <input type="text" style="display:none">
<input type="password" style="display:none">
  <div class="form-group">
          <label class="control-label col-sm-5" for="firstValue"><?php echo gettext('DL_ADD_VOC_FRST_VAL') ?></label>
    <div class="col-sm-7">
       
        
        <input class="form-control" name="firstValue"  placeholder="<?php echo gettext('DL_ADD_VOC_FRST_VAL_PLC') ?>" id="firstValue" value="" autocomplete="off" required>
        
    </div>
  </div>

  <div class="form-group">
          <label class="control-label col-sm-5"  for="secondValue"><?php echo gettext('DL_ADD_VOC_SCND_VAL') ?></label>
    <div class="col-sm-7">
       
        
        <input class="form-control" name="secondValue"  placeholder="<?php echo gettext('DL_ADD_VOC_SCND_VAL_PLC') ?>" id="secondValue" value="" autocomplete="off" required>
        
    </div>
  </div>
<input class="form-control"  name="dictionaryId"  type="hidden"  id="dictionaryIdV">
<input class="form-control"  name="type"  type="hidden"  id="typeV">
<input class="form-control"  name="firstValueS"  type="hidden"  id="firstValueVS">
<input class="form-control"  name="secondValueS"  type="hidden"  id="secondValueVS">
<input class="form-control"  name="firstLanguage"  type="hidden"  id="firstLanguageV">
<input class="form-control"  name="secondLanguage"  type="hidden"  id="secondLanguageV">
  <div class="form-group">
    <div class="col-sm-offset-7 col-sm-7">
                     <ul class="list-inline ">

  <li class="list-inline-item " > <button type="submit" class="btn btn-success">  <?php echo gettext('DIALOG_SUBMIT') ?></button></li>
 <li class="list-inline-item ">  <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo gettext('DIALOG_CLOSE') ?></button></li>
</ul>
       
         
    </div>

  </div>
</form> 
           <form class="form-horizontal"  method="post" id="practiceForm">
        <input type="text" style="display:none">
<input type="password" style="display:none">
  <div class="form-group">
          <label class="control-label col-sm-5" for="practiceNameE"><?php echo gettext('PRAC_CREATE_NAME_LBL') ?></label>
    <div class="col-sm-7">
       
        
        <input class="form-control" name="practiceName"  placeholder="<?php echo gettext('PRAC_CREATE_NAME_LBL') ?>" id="practiceNameE" value="" autocomplete="off" required>
        
    </div>
  </div>

 
  <div class="form-group">
          <label class="control-label col-sm-5" for="acceptAfterE"> <?php echo gettext('PRAC_CREATE_HOW_MANY_TO_ACCEPT') ?></label>
    <div class="col-sm-7">
       
        
        <input class="form-control" type="number" name="acceptAfter" id="acceptAfterE" step="1" min="1" max="8" required>
        
    </div>
  </div>
<input class="form-control"  name="practiceId"  type="hidden"  id="practiceIdE">
<input class="form-control"  name="type"  type="hidden"  id="typeE">
  <div class="form-group">
    <div class="col-sm-offset-7 col-sm-7">
                     <ul class="list-inline ">

  <li class="list-inline-item " > <button type="submit" class="btn btn-success"> <?php echo gettext('DIALOG_SUBMIT') ?></button></li>
 <li class="list-inline-item ">  <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo gettext('DIALOG_CLOSE') ?></button></li>
</ul>
       
         
    </div>

  </div>
</form>    
          
      </div>
      
    </div>
  </div>
  </div>
                  <?php 
 }
 
 function buildMessage($type,$msg){
     $class='text-'.$type.' text-center mainMessageWrapper';
     ?>
      
      <h4  class=" <?php echo htmlspecialchars($class,ENT_QUOTES) ?>">
            <?php
                  
                    
                    
                    echo htmlspecialchars($msg,ENT_QUOTES);
                 
                    ?>
         </h4> <?php
 }
/**
 * Poskytuje zápatí pro příslušnou stránku. Zároven zahrnuje i potřebné JS skri
 * pty.
 * @return htmlcode 
 */
function buildFooter(){ ?>

</div>
<footer class="footer">


        
    

        
 
    <div class="row">
        <div class="col-sm-4">
            <div class="footerLastMod">
                <p> <?php echo gettext('LAST_MOD_LABEL')?></p><p>

 <?php
  global $locale;
if($locale=='en_US'){
     echo date('d/m/Y',getlastmod());   
}else{
  echo date('d.m.Y',getlastmod());  
}
 ?></p>  
            </div>
        </div>
       <div class="col-sm-4">
           <div class="footerCop">
              <?php echo 'Copyright Oldřich Hradil     '. date('Y')?>  
           </div></div>
       <div class="col-sm-4">
           <div class="footerMobileApp">
                    <h4><?php echo gettext('MOBILE_AP_LABEL') ?> </h4>
                    <a href="https://play.google.com/store" target="_blank"> <img src="<?php echo BASE ?>img/google_play_icon.png" alt="google play icon" width="150"></a>
           </div></div>
 </div>

    
           

</footer>

   <!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="<?php echo BASE ?>js/popper.min.js"></script> 
<script src="<?php echo BASE ?>js/bootstrap.min.js"></script> 
<script src="<?php echo BASE?>js/index.js"></script>
</body>
</html>
<?php }
/**
 * Poskytuje chybové hlášení ve vlastním formátu. 
 * @param string $msg Zpráva jež má být vypsána.
 * @return htmlcode 
 */
function buildError($msg){
?>
<div class="container error">
    <h1 class="errorHeading"><span class="glyphicon glyphicon-remove error"></span>    <b><?php echo $msg ?></b></h1><a class="text-center" href="<?php echo BASE ?>"><?php gettext('L_HEADING')?></a></div>
    
    
    
<?php } 
/**
 * Poskytuje úspěšné hlášení. 
 * @param string $msg Zpráva jež má být vypsána.
 * @return htmlcode 
 */
function buildSuccess($msg){
    ?>
<div class="container success"><h1 class="successHeading"> <span class="glyphicon glyphicon-ok success"></span>     <b><?php echo $msg ?></b></h1><a class="text-center" href="<?php echo BASE ?>"><?php echo gettext('L_HEADING') ?></a></div>
    
    
    
<?php } ?>
    
