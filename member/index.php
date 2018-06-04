<?php
require_once '../php/Libraries.php';
if(!checkValidUser()){
     header("location:".BASE);
 exit();    
}
buildHeader(gettext('HOME_HEADING'));
buildNavigationBar(true,gettext('HOME_HEADING'));
?>
<div class="row homeIconGallery" >
    <div class="col-sm-1"></div> 
 
    <a class="col-sm-4 homeImage" href="<?php echo BASE.'member/dictionariesList'?>">
        <h2 class="text-center homeSectionHeading"><?php echo  gettext('DL_HEADING') ?></h2>
        <!--<input type="color" class="colorPicker"  value="#ff0080">-->
        <img src="img/home/dictionariesList.svg" alt="<?php echo  gettext('H_DIC_LIST_ALT') ?>" height="150" >
         <ul class="list-group">
  <li class="list-group-item"><?php echo gettext('MHOME_DIC_TAG_1') ?> </li>
  <li class="list-group-item"><?php echo  gettext('MHOME_DIC_TAG_2') ?></li>
    
</ul> 
      
    </a>
       <div class="col-sm-2"></div>   

       <a class="col-sm-4 homeImage" href="<?php echo BASE.'member/practice' ?>">
         <h2  class="text-center homeSectionHeading"><?php echo  gettext('PRAC_HEADING') ?></h2>
        <!--<input type="color" class="colorPicker" value="#ff0080">-->
        <img src="img/home/practice.svg" alt="<?php echo  gettext('H_PLAY_BUTTON_ALT') ?>" height="150" >
     
              <ul class="list-group">
  <li class="list-group-item"><?php echo gettext('MHOME_PRAC_TAG_1') ?></li>
  <li class="list-group-item"><?php echo  gettext('MHOME_PRAC_TAG_2') ?></li>
    
</ul>
    </a>

        <div class="col-sm-1"></div> 
    
</div>

<?php
buildFooter();
?>


