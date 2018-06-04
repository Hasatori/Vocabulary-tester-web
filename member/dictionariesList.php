<?php
require_once '../php/Libraries.php';
if (!checkValidUser()) {
    header("location:" . BASE);
    exit();
}
if (isset($_GET['dicOffset']) && isset($_GET['vocOffset']) && isset($_GET['selected'])) {
    $dicOffset = (int) $_GET['dicOffset'];
    $vocOffset = (int) $_GET['vocOffset'];
    $selected = (int) $_GET['selected'];
    if ($dicOffset <= 0) {
        $dicOffset = '0';
    }
    if ($vocOffset <= 0) {
        $vocOffset = '0';
    }
    if ($selected < 1 || $selected > 10) {
        $selected = 1;
    }
} else {
    $dicOffset = '0';
    $vocOffset = '0';
    $selected = 1;
}

if (isset($_POST['type'])) {

    $url = BASE . 'member/dictionariesList?dicOffset=' . $dicOffset . '&selected=' . $selected . '&vocOffset=' . $vocOffset;
    processDicRequest($_POST, $url);
}

buildHeader(gettext('DL_HEADING'));
buildNavigationBar(true, gettext('DL_HEADING'));
?>

<div class="row">
    <div class="col-sm-1"></div>
    <div class="col-sm-5">
        <table class="table table-striped dictionariesListTable">
            <caption class="tableCaption" ><strong><?php echo gettext('DL_LIST_TAB_CAPTION') ?></strong></caption>
            <thead>
                <tr>
                    <th scope="col" class="add"><p></p></th>
                    <th scope="col"><?php echo gettext('DL_LIST_DIC_NAME') ?></th>
                    <th scope="col"><?php echo gettext('DL_LIST_TAB_LANGUAGES') ?></th>
                    <th scope="col" class="add" onclick="addDictionary();
                        "

                        ><span 
                            class="glyphicon glyphicon-plus"></span>
                        <p> <?php echo gettext('DL_ADD_ICON') ?></p>
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

                    if ($selected === ($i + 1)) {
                        $selectedName = $dictionaryName;
                        $selectedFirstLang = $dictionaryFirstLanguage;
                        $selectedSecondLang = $dictionarySecondLanguage;
                        $selectedId = $dictionaryId;
                    }
                    $location = BASE . 'member/dictionariesList?dicOffset=' . $dicOffset . '&selected=' . ($i + 1) . '&vocOffset=0';
                    ?>
                    <tr 
                    <?php
                    if ($selected === ($i + 1)) {
                        echo 'class="selectedDictionary"';
                    }
                    ?>>
                        <td class="externalCell2" onclick="editDictionary('<?php echo $dictionaryName . '\',\'' . gettext($dictionaryFirstLanguage) . '\',\'' . gettext($dictionarySecondLanguage) . '\',\'' . $dictionaryId ?>');"
                            >  <span class="glyphicon glyphicon-pencil"></span></td>
                        <td onclick="document.location.href = '<?= htmlspecialchars($location) ?>'"><?php echo htmlspecialchars($dictionaryName, ENT_QUOTES) ?></td>
                        <td onclick="document.location.href = '<?= htmlspecialchars($location) ?>'"><?php echo htmlspecialchars($dictionaryFirstLanguage) . '-' . htmlspecialchars($dictionarySecondLanguage) ?></td>
                        <td class="externalCell" title="Smazat" onclick="deleteDictionary('<?php echo htmlspecialchars($dictionaryId, ENT_QUOTES) . '\',\'' . htmlspecialchars($dictionaryName,ENT_QUOTES) . ' ' . htmlspecialchars($dictionaryFirstLanguage,ENT_QUOTES) . ' ' . htmlspecialchars($dictionarySecondLanguage,ENT_QUOTES) ?>');">        <span class="glyphicon glyphicon-remove"></span></td>


                    </tr>

                    <?php
                }
                ?>

            </tbody>

        </table>
        <nav aria-label="Page navigation example">
            <ul class="pagination dictionariesListPagination">
                <?php for ($i = 1; $i <= @ceil(count(getDictionaries($client['id'])) / 10); $i++) { ?>

                    <li class="page-item <?= ($dicOffset / 10 + 1) == $i ? " active" : "" ?>"><a class="page-link " href="<?php echo BASE . 'member/dictionariesList?dicOffset=' . ($i - 1) * 10 . '&selected=1&vocOffset=0' ?>"><?= $i ?></a></li>

                <?php } ?>
            </ul>
        </nav>
    </div>

    <div class="col-sm-5">


        <table class="table table-striped supplementaryTable">
            <caption class="tableCaption" id="supplementaryTableCaption"><strong><?= @htmlspecialchars($selectedName, ENT_QUOTES) ?></strong></caption>
            <thead>
                <tr>
                    <th scope="col" class="add"><p></p></th>
                    <th scope="col"><?= @htmlspecialchars($selectedFirstLang) ?></th>
                    <th scope="col"><?= @htmlspecialchars($selectedSecondLang) ?></th>
                    <th scope="col" class="add" onclick="
                            $('#formModal').modal();
                            $('#formModalHeading > *').css('display', 'none');
                            $('#formModal .modal-body > *').css('display', 'none');
                            $('#addVocHeading').css('display', 'block');
                            $('#vocabularyForm').css('display', 'block');
                            $('#vocabularyForm input:not(#dictionaryIdV)').val('');

                            $('#firstLanguageV').val('<?= @htmlspecialchars($selectedFirstLang) ?>');
                            $('#secondLanguageV').val('<?= @htmlspecialchars($selectedSecondLang) ?>');
                            $('#dictionaryIdV').val('<?= @htmlspecialchars($selectedId) ?>');
                            $('#typeV').val('addVoc');

                        " >

                        <span class="glyphicon glyphicon-plus"></span>
                        <p> <?php echo gettext('DL_ADD_ICON') ?></p></th>


                </tr>
            </thead>
            <tbody>

                <?php
                $content = @json_decode(getDictionaryContent($selectedId, $vocOffset), true);
                if ($content != null) {
                    foreach ($content as $value) {
                        $fVal = $value['first_value'];
                        $sVal = $value['second_value']
                        ?>    
                        <tr>
                            <td class="externalCell2" onclick="editVocabulary('<?= htmlspecialchars(gettext($selectedFirstLang)) . '\',\'' . htmlspecialchars(gettext($selectedSecondLang)) . '\',\'' . htmlspecialchars($selectedId) . '\',\'' . htmlspecialchars($fVal) . '\',\'' . htmlspecialchars($sVal) ?>')">
                                <span class="glyphicon glyphicon-pencil"></span>
                            </td>
                            <td><?= htmlspecialchars($fVal) ?></td>
                            <td><?= htmlspecialchars($sVal) ?></td>
                            <td class="externalCell" onclick="deleteVocabulary(<?= htmlspecialchars($selectedId) . ',\'' . htmlspecialchars($fVal) . '\',\'' . htmlspecialchars($sVal) ?>')">
                                <span class="glyphicon glyphicon-remove"></span>

                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>


            </tbody>
        </table>
        <nav>
            <ul class="pagination supplementaryTablePagination">
                <?php for ($i = 1; $i <= @ceil(count(json_decode(getDictionaryContent($selectedId))) / 10); $i++) { ?>

                    <li class="page-item <?= ($vocOffset) / 10 + 1 == $i ? "active" : "" ?>"><a class="page-link " href="<?php echo BASE . 'member/dictionariesList?dicOffset=' . $dicOffset . '&selected=' . $selected . '&vocOffset=' . ($i - 1) * 10 ?>"><?= $i ?></a></li>

                <?php } ?>
            </ul>
        </nav>

    </div>
    <div class="col-sm-1"></div>
</div>



<?php
buildFormModal();
buildYesNoDialog();
buildFooter();



