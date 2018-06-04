<?php
require_once '../php/Libraries.php';
if (!checkValidUser()) {
    header("location:" . BASE);
    exit();
}
if (isset($_GET['dicOffset']) && isset($_GET['forPracOffset']) && isset($_GET['pracOffset'])) {
    $dicOffset = (int) $_GET['dicOffset'];
    $forPracOffset = (int) $_GET['forPracOffset'];
    $pracOffset = (int) $_GET['pracOffset'];

    if ($dicOffset <= 0) {
        $dicOffset = '0';
    }
    if ($pracOffset <= 0) {
        $pracOffset = '0';
    }
    if ($forPracOffset <= 0) {
        $forPracOffset = '0';
    }
} else {
    $dicOffset = '0';
    $pracOffset = '0';
    $forPracOffset = '0';
}

if (isset($_POST['type'])) {
    processPracRequest($_POST);
}
buildHeader(gettext('PRAC_HEADING'));
buildNavigationBar(true, gettext('PRAC_HEADING'));
?>



<div id="myCarousel" class="carousel slide " data-ride="carousel"
     data-interval="false">
    <!-- Wrapper for slides -->
    <div class="carousel-inner">
        <div class="item <?php
if (!isset($_SESSION['practiceId'])) {
    echo 'active';
}
?>">

            <div class="row" >

                <div class="col-sm-4"></div>

                <div class="col-sm-4">
                    <table class="table table-striped ">
                        <caption class="tableCaption" ><strong><?php echo gettext('PRAC_LIST_HEADING') ?></strong></caption>
                        <thead>
                            <tr>
                                <th scope="col" class="add"><p></p></th>
                                <th scope="col"><?php echo gettext('PRAC_NAME') ?></th>
                                <th scope="col" class="add">

                                </th>
                                <th scope="col"  class="remove">

                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $practices = getClientsPractices($pracOffset);

                            $count = !$practices ? 0 : count($practices);
                            for ($i = 0; $i < $count; $i++) {
                                $practiceName = $practices[$i]['practice_name'];
                                $practiceId = $practices[$i]['id_practice'];
                                $acceptAfter = $practices[$i]['accept_after'];
                                ?>
                                <tr>
                                    <td class="externalCell2" onclick="
                                                $('#formModal').modal();
                                                $('#formModalHeading > *').css('display', 'none');
                                                $('#formModal .modal-body > *').css('display', 'none');
                                                $('#editDicHeading').css('display', 'block');
                                                $('#practiceForm').css('display', 'block');
                                                $('#practiceNameE').val('<?php echo $practiceName; ?>');
                                                $('#acceptAfterE').val('<?php echo $acceptAfter; ?>');
                                                $('#practiceIdE').val('<?php echo $practiceId; ?>');
                                                $('#typeE').val('editPrac');
                                        "
                                        > <span class="glyphicon glyphicon-pencil"></span></td>
                                    <td><?php echo htmlspecialchars($practiceName, ENT_QUOTES) ?></td>

                                    <td class="externalCell add"  title="pokračovat" onclick="continuePractice('<?php echo htmlspecialchars($practiceId,ENT_QUOTES) . '\',\'' . htmlspecialchars($practiceName,ENT_QUOTES) ?>')"><span class="glyphicon glyphicon-share-alt "></span>
                                    </td>
                                    <td class="externalCell" title="Smazat" onclick="deletePractice('<?php echo htmlspecialchars($practiceId,ENT_QUOTES) . '\',\'' . htmlspecialchars($practiceName,ENT_QUOTES) ?>')" ><span class="glyphicon glyphicon-remove"></span></td>

                                </tr>

                                <?php
                            }
                            ?>



                        </tbody>
                    </table>
                    <nav aria-label="Page navigation example">
                        <ul class="pagination dictionariesListPagination">
                            <?php for ($i = 1; $i <= @ceil(count(getClientsPractices()) / 10); $i++) { ?>

                                <li class="page-item <?= ($pracOffset / 10 + 1) == $i ? " active" : "" ?>"><a class="page-link " href="<?php echo BASE . 'member/practice?pracOffset=' . ($i - 1) * 10 . '&dicOffset=' . $dicOffset ?>&forPracOffset=<?= $forPracOffset ?>"><?= $i ?></a></li>

                            <?php } ?>
                        </ul>
                    </nav>
                </div>

                <div class="col-sm-4"></div>
            </div>



            <div id="createPractice"> 
                <h3 class="text-center"> <?php echo gettext('PRAC_CREATE_HEADING') ?></h3>
                <div class="row " >
                    <div class="col-sm-4"></div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="practiceName"><?php echo gettext('PRAC_CREATE_NAME_LBL') ?></label>
                            <input type="text" id="practiceName" class="form-control" value="" placeholder="<?php echo gettext('PRAC_CREATE_NAME_LBL') ?>">


                        </div>

                        <div class="form-group">
                            <label for="acceptAfter"><?php echo gettext('PRAC_CREATE_HOW_MANY_TO_ACCEPT') ?></label>
                            <input type="number" id="acceptAfter" class="form-control" min="1" max="15">


                        </div>
                    </div>
                    <div class="col-sm-4"></div>
                </div>
                <div class="row prepareSection">
                    <div class="col-sm-1"></div>
                    <div class="col-sm-4">

                        <table class="table table-striped dictionariesListTable">
                            <caption class="tableCaption" ><strong><?php echo gettext('DL_LIST_TAB_CAPTION') ?></strong></caption>
                            <thead>
                                <tr>
                                    <th scope="col"><?php echo gettext('DL_LIST_DIC_NAME') ?></th>
                                    <th scope="col"><?php echo gettext('DL_LIST_TAB_LANGUAGES') ?></th>
                                    <th scope="col" class="add">
                                    </th>

                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                $client = getClient();

                                $dictionaries = getDictionaries($client['id'], $dicOffset);
                                $count = !$dictionaries ? 0 : count($dictionaries);

                                for ($i = 0; $i < $count; $i++) {
                                    $dictionaryId = $dictionaries[$i]["id_dictionary"];
                                    $dictionaryName = $dictionaries[$i]["dictionary_name"];
                                    $dictionaryFirstLanguage = gettext($dictionaries[$i]["first_lang"]);
                                    $dictionarySecondLanguage = gettext($dictionaries[$i]["second_lang"]);
                                    ?>
                                    <tr  <?php
                                if (isset($_SESSION['creationDictionaries']) && !empty($_SESSION['creationDictionaries'])) {
                                    if (array_key_exists($dictionaryId, $_SESSION['creationDictionaries'])) {
                                        echo 'class="selectedDictionary"';
                                    }
                                }
                                    ?>
                                        onclick="addForPractice('<?= 'dic' . htmlspecialchars($dictionaryId,ENT_QUOTES) ?>', '<?= htmlspecialchars($dictionaryId,ENT_QUOTES) ?>')" id="dic<?= htmlspecialchars($dictionaryId,ENT_QUOTES) ?>">
                                        <td><?php echo htmlspecialchars($dictionaryName, ENT_QUOTES) ?></td>
                                        <td><?php echo htmlspecialchars($dictionaryFirstLanguage) . '-' . htmlspecialchars($dictionarySecondLanguage) ?></td>
                                        <td class="externalCell add"  title="<?php echo gettext('DL_ADD_ICON')
                                ?>"><span class="glyphicon glyphicon-share-alt "></span>
                                        </td>

                                    </tr>

                                    <?php
                                }
                                ?>

                            </tbody>
                        </table>
                        <nav aria-label="Page navigation example">
                            <ul class="pagination dictionariesListPagination">
                                <?php for ($i = 1; $i <= @ceil(count(getDictionaries($client['id'])) / 10); $i++) { ?>

                                    <li class="page-item <?= ($dicOffset / 10 + 1) == $i ? " active" : "" ?>"><a class="page-link " href="<?php echo BASE . 'member/practice?pracOffset=' . $pracOffset . '&dicOffset=' . ($i - 1) * 10 ?>&forPracOffset=<?= $forPracOffset ?>"><?= $i ?></a></li>

                                <?php } ?>
                            </ul>
                        </nav>

                    </div>
                    <div class="col-sm-1"></div>
                    <div class="col-sm-4">

                        <table class="table table-striped supplementaryTable">
                            <caption class="tableCaption" id="supplementaryTableCaption"><strong>
                                    <?php echo gettext('PRAC_DIC_TO_PRACTICE') ?>
                                </strong></caption>
                            <thead>
                                <tr>
                                    <th scope="col" class="add">
                                    <th scope="col"><?php echo gettext('DL_LIST_DIC_NAME') ?></th>
                                    <th scope="col"><?php echo gettext('DL_LIST_TAB_LANGUAGES') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (isset($_SESSION['creationDictionaries']) && !empty($_SESSION['creationDictionaries'])) {
                                    $creationDictionaries = $_SESSION['creationDictionaries'];
                                    $i = 0;
                                    foreach ($creationDictionaries as $id) { ?>
                                        <?php
                                        $dictionary = getDictionary($id);


                                        $dictionaryName = $dictionary['dicName'];
                                        $dictionaryFirstLanguage = gettext($dictionary['firstLang']);
                                        $dictionarySecondLanguage = gettext($dictionary['secondLang']);
                                        ?>
                                        <tr  <?php
                                if (($i < $forPracOffset) || ($i > ($forPracOffset + 9))) {
                                    echo 'style=display:none;';
                                }
                                        ?>onclick="remove(<?= $id ?>)" id="<?= $id ?>">
                                            <td class="externalCell add"  title="<?php echo gettext('DL_ADD_ICON')
                                ?>"><span class="glyphicon glyphicon-share-alt " style="transform:rotateY(180deg);"></span>
                                            </td>
                                            <td><?php echo htmlspecialchars($dictionaryName, ENT_QUOTES) ?></td>
                                            <td><?php echo htmlspecialchars($dictionaryFirstLanguage) . '-' . htmlspecialchars($dictionarySecondLanguage) ?></td>
                                        </tr>
                                        <?php
                                        $i++;
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                        <nav>
                            <ul class="pagination supplementaryTablePagination">
                                <?php
                                if (isset($_SESSION['creationDictionaries']) && !empty($_SESSION['creationDictionaries'])) {

                                    for ($i = 1; $i <= @ceil(count($creationDictionaries) / 10); $i++) {
                                        ?>

                                        <li class="page-item <?= ($forPracOffset / 10 + 1) == $i ? " active" : "" ?>"><a class="page-link " href="<?php echo BASE . 'member/practice?pracOffset=' . $pracOffset . '&dicOffset=' . $dicOffset ?>&forPracOffset=<?= ($i - 1) * 10 ?>"><?= $i ?></a></li>

                                        <?php
                                    }
                                }
                                ?>
                            </ul>
                        </nav>
                        <button class="btn btn-success"
                                onclick="createPractice();"
                                >
                                    <?php echo gettext('PRAC_CREATE_BTN') ?>
                        </button>
                    </div>
                    <div class="col-sm-2"></div>
                </div>
            </div>
        </div>

        <div style="margin-top:10rem;" class="item <?php
                                    if (isset($_SESSION['practiceId'])) {
                                        echo 'active';
                                    }
                                    ?>">
            <div class="row">

                <div class="col-sm-2">
                    <button class="btn btn-success"
                            onclick="$('#myCarousel').carousel('prev');
                                    clearSessions();">
                        <span class="glyphicon glyphicon-arrow-left"></span> <?php echo gettext('PRAC_SESSION_BACK_TO_BTN') ?>
                    </button>
                </div>    
                <div class="col-sm-6">
                    <h3 class="text-center text-info" id="practiceHeading">

                        <?php
                        if (isset($_SESSION['practiceId'])) {
                            echo htmlspecialchars(getPracticeName($_SESSION['practiceId']), ENT_QUOTES);
                        } else {
                            echo 'heading';
                        }
                        ?>
                    </h3>
                </div>   

                <div class="col-sm-4">
                    <button type="button" class="btn btn-primary" id="practiceDirection" onclick="changePDirection(<?php echo @$_SESSION['practiceId'] ?>)">Změnit směr zkoušení</button>
                    <button type="button" class="btn btn-danger" id="restartPractice2"  onclick="restartPractice(<?php echo @$_SESSION['practiceId'] ?>)">Restartovat zkoušení</button>

                </div>   

            </div>

            <div class="row practiceSection">

                <div class="col-sm-2">

                </div>
                <div class="col-sm-6">

                    <form class="form-horizontal" method="post" id="answerForm">


                        <div class="form-group">
                            <label class="control-label col-sm-2" for="toTranslate"><?php echo gettext('PRAC_SESSION_TO_TRANSLATE_LBL') ?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="toTranslate" name="toTranslate"
                                       value="<?php
                        $wordToKnow = @getVocForPractice($_SESSION['practiceId']);
                        echo htmlspecialchars($wordToKnow, ENT_QUOTES);
                        ?>" readonly required>
                            </div>
                            <div class="col-sm-2">
                                <span class="glyphicon glyphicon-question-sign rightAnswerTooltip"
                                      data-toggle="tooltip" data-placement="right" title="
                                      <?php
                                      if (isset($_SESSION['practiceId'])) {
                                          echo htmlspecialchars(getRightAnswer($_SESSION['practiceId'], $wordToKnow), ENT_QUOTES);
                                      } ?>"
                                      ></span>   </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="translation"><?php echo gettext('PRAC_SESSION_TRANSLATION_LBL') ?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="translation" name="translation" placeholder="<?php echo gettext('PRAC_SESSION_TRANSLATION_PLC') ?>" 
                                       autocomplete="off"  autofocus

                                       >

                            </div>

                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-4">
                                <button type="submit" class="btn btn-default" name="sendAnswer" id="sendAnswer"><?php echo gettext('PRAC_SESSION_SEND_BTN') ?></button>
                                <button type="submit" class="btn btn-default" name="tryAgain" id="tryAgain"><?php echo gettext('PRAC_SESSION_TRY_AGAIN_BTN') ?></button>
                                <button  onclick="document.location.reload()" name="continue" type="button" class="btn btn-default" id="continue"><?php echo gettext('PRAC_SESSION_CONTINUE') ?></button>

                            </div>
                        </div>
                    </form>     

                </div>



                <div class="col-sm-2">
                    <h3 class="text-center text-info text-bold"><?php echo gettext('PRAC_SESSION_SUCCESS_RATE_HEADING') ?></h3>
                    <div class="successCircle">
                        <div class="succesRateValue"><?php
                                      echo @htmlspecialchars(getSuccessRate($_SESSION['practiceId']), ENT_QUOTES)
                                      ?>%</div>
                    </div>

                </div>
                <div class="col-sm-2"></div>  
            </div>
        </div>     </div>



</div>

<?php
buildYesNoDialog();
buildResultsDialog();
buildFormModal();
buildFooter();
?>




