<?php 
require_once '../php/Libraries.php';
session_start();
if(getClient()['role']!='ADMIN'){
    header('location:'.BASE);
}else{
 buildHeader(gettext('TESTING_HEADING'));
buildNavigationBar(true,gettext('TESTING_HEADING'));
?>

    <div class="container-fluid" style="margin-top: 3rem">
    
        <div class="col-sm-6">
            <button class="btn btn-success center-block" onclick="test()"><?php echo gettext('TESTING_RUN_TESTS_BTN_LBL') ?></button>
        </div>
           <div class="col-sm-3">
               <button class="btn btn-success center-block" onclick="backup()"><?php echo gettext('TESTING_CREATE_BACKUP_BTN_LBL') ?></button>
        </div>
         <div class="col-sm-3">
             <button class="btn btn-success center-block" onclick="loadBackup()"><?php echo gettext('TESTING_LOAD_BACKUP_BTN_LBL') ?></button>
        </div>
          
    </div>
       <div class="container-fluid" style="margin: 3rem 0">
        <div class="col-sm-6">
            <label for="result"><?php echo gettext('TESTING_RESULT') ?></label>
 
             <div id="result" class="result">
        

             </div>
        </div>
       <div class="col-sm-6">
                       <label for="loadBackup"><?php echo  gettext('TESTING_BACKUP_LOAD') ?></label>
    <div id="loadBackup"  class="result">
           </div>
    </div>
       </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->

<script src="<?php echo BASE?>test/index.js"></script>
<?php 
buildFooter();
}