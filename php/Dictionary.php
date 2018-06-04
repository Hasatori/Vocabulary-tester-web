<?php

/**
 * Knihovna pro manipulaci se slovníky klienta.  
 *
 *  @author Oldřich Hradil 
 * 
 * */

/**
 * Poskytuje informace o slovníku. 
 * 
 * @param int $dictionaryId Identifikační číslo slovníku.
 * @return array|bool Vrací pole obsahující základní informace o slovníku.
 * Při neuspěchu vrací false.
 */
function getDictionary($dictionaryId) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }
    $client = getClient();
    $id = $client['id'];


    $query = $db->prepare("SELECT dictionary_name,first_lang,second_lang FROM dictionary "
            . "where id_dictionary=:id_dictionary and id=:id");
    $query->execute([':id_dictionary' => $dictionaryId, ':id' => $id]);
    $result = $query->fetchAll(PDO::FETCH_ASSOC);
    if (count($result) === 1) {


        $dictionary = array('dicName' => $result[0]['dictionary_name'],
            'firstLang' => $result[0]['first_lang'],
            'secondLang' => $result[0]['second_lang']);

        return $dictionary;
    } else {

        return false;
    }
}

/**
 * Posktuje seznam slovníků určitého klienta. 
 * 
 * @param int $id id klienta.
 * 
 * @return bool|tr Vrací seznam všech slovníků a to v podobě html kódu, 
 * který je vkládán přímo do příslušné stránky jež jej potřebuje. Pokud nastane 
 * chyba je vráceno false.
 */
function getDictionaries($id, $offset = null) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }
    if ($offset != null) {
        $query = $db->prepare("SELECT id_dictionary,dictionary_name,"
                . "first_lang,second_lang FROM dictionary where id=? "
                . " ORDER BY timestamp DESC LIMIT 10 OFFSET ? ");

        $query->bindValue(1, $id, PDO::PARAM_INT);
        $query->bindValue(2, (int) $offset, PDO::PARAM_INT);
        $query->execute();
    } else {
        $query = $db->prepare("SELECT id_dictionary,dictionary_name,"
                . "first_lang,second_lang FROM dictionary where id=:id "
                . "ORDER BY timestamp DESC");


        $query->execute([':id' => $id]);
    }
    $result = $query->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) > 0) {


        return $result;
    } else {

        return false;
    }
}

/**
 * Pokytuje obsah jednoho konkrétního slovníku.
 * @param int $dictionaryId id konkrétního slovníku.
 * @param int $offset Offset vyhledávání. Defaultně je null pokud je nastaven, tak se aplikuje
 * @return bool|string Vrací obsah slovníku v podobě naformátovaného stringu, 
 * pro jeho snadnou převoditelnost na pole pomocí AJAXU, který jej bude zpraco-
 * vávat. Pokud nastane chyba vrací false. 
 */
function getDictionaryContent($dictionaryId, $offset = null) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }
    if ($offset != null) {
        $query = $db->prepare("SELECT first_value,second_value FROM contains_of where id_dictionary=? ORDER BY first_value ASC LIMIT 10 OFFSET ?");
        $query->bindValue(1, $dictionaryId, PDO::PARAM_INT);
        $query->bindValue(2, (int) $offset, PDO::PARAM_INT);
        $query->execute();
    } else {
        $query = $db->prepare("SELECT first_value,second_value FROM contains_of where id_dictionary=:id_dictionary");
        $query->execute([':id_dictionary' => $dictionaryId]);
    }
    $result = $query->fetchAll(PDO::FETCH_ASSOC);
    if (count($result) > 0) {


        return json_encode($result);
    } else {

        return false;
    }
}

/**
 * Vymaže slovník na základě jeho id.
 * 
 * @param int $dictionaryId id konkrétního slovníku.
 * @return bool Vrací true při úspěchu a false při neúspechu. 
 */
function deleteDictionary($dictionaryId) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }

    $query = $db->prepare("DELETE FROM dictionary where id_dictionary=:id_dictionary");

    return ($query->execute([':id_dictionary' => $dictionaryId]) == true);
}

/**
 * Vymaže slovíčko příslušného slovníku. 
 * 
 * @param int $dictionaryId id konkrétního slovníku.
 * @param string $firstValue První slovíčko slovníku. 
 * @param string $secondValue Překlad prvního slova do příslušného jazyka. 
 * @return bool Vrací true při úspěchu a false při neúspechu. 
 */
function deleteVocabulary($dictionaryId, $firstValue, $secondValue) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }

    $query = $db->prepare("DELETE FROM contains_of where id_dictionary=:id_dictionary and first_value=:first_value AND second_value=:second_value");

    return $query->execute([':id_dictionary' => $dictionaryId, ':first_value' => $firstValue, ':second_value' => $secondValue]);
}

/**
 * Vytvoří nový slovník klientovi. 
 * @param string $dicName id konkrétního slovníku.
 * @param string $firstLang První jazyk slovníku.
 * @param string $secondLang Druhý jazyk slovníku. 
 * @return bool Vrací true při úspěchu a false při neúspechu. 
 */
function addDictionary($dicName, $firstLang, $secondLang) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }
    $client = getClient();
    $id = $client['id'];
    try {
        $query = $db->prepare("INSERT INTO `dictionary` "
                . "( `id`, `dictionary_name`, "
                . "`first_lang`, `second_lang`) "
                . "VALUES ( :id, :DIC_NAME, :first_lang,:second_lang)");

        return $query->execute([':id' => $id, ':DIC_NAME' => $dicName, ':first_lang' => $firstLang, ':second_lang' => $secondLang]);
    } catch (PDOException $e) {

        if ($e->errorInfo[1] == 1062) {
            $_SESSION['error'] = array(true, gettext('DL_EDIT_DIC_ALEXISTS'));
        } else {
            $_SESSION['error'] = array(true, gettext('SQL_ERROR'));
        }
        return false;
    }
}

/**
 * Přidá nové slovíčko do příslušného slovníku. V průběhu této funkce je spouš-
 * těna transakce, v případě neúspěchu kterékoliv části jsou změni vráceny do 
 * původního stavu.
 * @param int $dictionaryId id konkrétního slovníku.
 * @param string $firstValue První slovíčko slovníku. 
 * @param string $secondValue Překlad prvního slova do příslušného jazyka. 
 * @return bool Vrací true při úspěchu a false při neúspechu. 
 */
function addVocabulary($dictionaryId, $firstValue, $secondValue) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }
try{

        $query = $db->prepare("SELECT * FROM vocabulary where first_value=:first_value AND second_value=:second_value");
        $query->execute([':first_value' => $firstValue, ':second_value' => $secondValue]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) === 1) {

            $query = $db->prepare("INSERT INTO `contains_of` "
                    . "( `id_dictionary`, `first_value`, `second_value`)"
                    . "VALUES (:DICTIONARY_id,:first_value,:second_value)");
            return $query->execute([':DICTIONARY_id' => $dictionaryId, ':first_value' => $firstValue, ':second_value' => $secondValue]);
        } else {
            $dictionary = getDictionary($dictionaryId);
            $firstLanguage = $dictionary['firstLang'];
            $secondLanguage = $dictionary['secondLang'];
            $db->beginTransaction();
            $query1 = $db->prepare("INSERT INTO `vocabulary` "
                    . "( `first_value`, `second_value`, `F_VAL_SOURCE`, `S_VAL_SOURCE`) "
                    . "VALUES ( :first_value,:second_value"
                    . ",:first_langUAGE,:second_langUAGE)");
            $query1->execute([':first_value' => $firstValue, ':second_value' => $secondValue, ':first_langUAGE' => $firstLanguage, ':second_langUAGE' => $secondLanguage]);
            $query2 = $db->prepare("INSERT INTO `contains_of` "
                    . "( `id_dictionary`,`first_value`, `second_value`) "
                    . "VALUES (:DICTIONARY_id,:first_value,:second_value)");
            $query2->execute([':DICTIONARY_id' => $dictionaryId, ':first_value' => $firstValue, ':second_value' => $secondValue]);
            if (!$db->commit()) {
                $db->rollback();

                return false;
            } else {

                return true;
            }
        }
    
    } catch (PDOException $e) {

        if ($e->errorInfo[1] == 1062) {
          $_SESSION['error'] = array(true, gettext('DL_EDIT_VOC_ALEXISTS'));
        } else {
            $_SESSION['error'] = array(true, gettext('SQL_ERROR'));
        }
        return false;
    }
}


/**
 * 
 * @param type $dictionaryId
 * @param type $dictionaryName
 * @param type $firstLanguage
 * @param type $secondLanguage
 * @return string
 */
function editDictionary($dictionaryId, $dictionaryName, $firstLanguage, $secondLanguage) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }

    $client = getClient();
    $id = $client['id'];

    try {

        $query = $db->prepare("UPDATE dictionary SET dictionary_name=:dictionary_name,"
                . " first_lang=:first_lang, second_lang=:second_lang WHERE id_dictionary=:id_dictionary");

        return $query->execute([':dictionary_name' => $dictionaryName, ':id_dictionary' => $dictionaryId
                    , ':first_lang' => $firstLanguage, ':second_lang' => $secondLanguage]) ? true : gettext('DB_NO_CONNECTION');
    } catch (PDOException $e) {

        if ($e->errorInfo[1] == 1062) {
            $_SESSION['error'] = array(true, gettext('DL_EDIT_DIC_ALEXISTS'));
        } else {
            $_SESSION['error'] = array(true, gettext('SQL_ERROR'));
        }
        return false;
    }
}

/**
 * 
 * @param type $firstValue
 * @param type $secondValue
 * @param type $dictionaryId
 * @param type $firstLanguage
 * @param type $secondLanguage
 * @return type
 */
function editVocabulary($firstValue, $secondValue, $firstValueS, $secondValueS, $dictionaryId, $firstLanguage, $secondLanguage) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }
    try {

        $query = $db->prepare("SELECT * FROM vocabulary  WHERE first_value=:FV AND second_value=:SV");
        $query->execute([':FV' => $firstValue, ':SV' => $secondValue]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) === 1) {

            $query = $db->prepare("UPDATE contains_of SET first_value=:FV,second_value=:SV WHERE id_dictionary=:id "
                    . "and first_value=:FVS and second_value=:SVS");

            return $query->execute([':FV' => $firstValue, ':SV' => $secondValue, ':FVS' => $firstValueS, ':SVS' => $secondValueS, ':id' => $dictionaryId]) ? true : gettext('DB_NO_CONNECTION');
        } else {
            $query = $db->prepare("INSERT INTO `vocabulary` "
                    . "( `first_value`, `second_value`, `F_VAL_SOURCE`, `S_VAL_SOURCE`) "
                    . "VALUES ( :first_value,:second_value"
                    . ",:first_langUAGE,:second_langUAGE)");
            $query->execute([':first_value' => $firstValue, ':second_value' => $secondValue, ':first_langUAGE' => $firstLanguage, ':second_langUAGE' => $secondLanguage]);
            return editVocabulary($firstValue, $secondValue, $firstValueS, $secondValueS, $dictionaryId, $firstLanguage, $secondLanguage);
        }
    } catch (PDOException $e) {

        if ($e->errorInfo[1] == 1062) {
            $_SESSION['error'] = array(true, gettext('DL_EDIT_VOC_ALEXISTS'));
        } else {
            $_SESSION['error'] = array(true, gettext('SQL_ERROR'));
        }
        return false;
    }
}

/**
 * Vrací obecné pojmenování pro jazyk.
 * @param type $lang Pojmenovní jazyka podle nstaveného jazyka aplikace
 * @return string|null
 */
function getLanguageLabel($lang) {
    switch ($lang) {
        case gettext('CZECH'):
            return 'CZECH';
        case gettext('AMERICAN_ENGLISH'):
            return 'AMERICAN_ENGLISH';
        default :
            return null;
    }
}
/**
 * /**
 * Zpracuje POST požadavek poslaný na stránku se slovníky
 * @param type $post Pole hodnot poslaných v požadavku
 * @param string $url Adresa aktuální stránky, používáno z důvodu načtení správných offsetů
 */

function processDicRequest(array $post,string $url) {

    $type = @$post['type'];
    $dictionaryId = @$post['dictionaryId'];
    $dicName = @$post['dictionaryName'];
    $firstValue = @$post['firstValue'];
    $secondValue = @$post['secondValue'];
    $firstValueS = @$post['firstValueS'];
    $secondValueS = @$post['secondValueS'];
    $firstLang = @getLanguageLabel($post['firstLanguage']);
    $secondLang = @getLanguageLabel($post['secondLanguage']);

    if ($type == 'editDic' || $type == 'deleteD' || $type == 'addVoc') {
        checkUserPermitionOnId($dictionaryId, 'dictionary');
    } else if ($type == 'editVoc' || $type == 'deleteV') {
        checkUserPermitionOnId($dictionaryId, 'containsOf');
    }
    if ($type == 'editDic' || $type == 'addDic') {
        if ($dicName === null || $dicName === '') {
            $_SESSION['error'] = array(true, gettext("DL_LIST_DIC_NAME").' '.gettext("NOT_FILLED"));
            header('location:' . $url);
            exit();
        } else if ($firstLang === null) {
            $_SESSION['error'] = array(true,gettext("DL_ADD_DIC_FRST_LANG_PLC").' '.gettext("NOT_FILLED"));
            header('location:' . $url);
            exit();
        } else if ($secondLang === null) {
            $_SESSION['error'] = array(true, gettext("DL_ADD_DIC_SECOND_LANG_PLC").' '.gettext("NOT_FILLED"));
            header('location:' . $url);
            exit();
        }
    } else if ($type == 'editVoc' || $type == 'addVoc' || $type == 'deleteV') {
        if ($firstValue === null || $firstValue === '') {
            $_SESSION['error'] = array(true, gettext("DL_ADD_VOC_FRST_VAL").' '.gettext("NOT_FILLED"));
            header('location:' . $url);
            exit();
        } else if ($secondValue === null || $secondValue === '') {
            $_SESSION['error'] = array(true,gettext("DL_ADD_VOC_SECOND_VAL").' '.gettext("NOT_FILLED"));
            header('location:' . $url);
            exit();
        }
    }

    switch ($type) {
        case 'goTo':
            echo getDictionaryContent($dictionaryId);
            break;
        case 'editDic':
            editDictionary($dictionaryId, $dicName, $firstLang, $secondLang);
            header('location:' . $url);
            exit();
            break;
        case"editVoc":
            if ($firstLang === null) {
             $_SESSION['error'] = array(true,gettext("DL_ADD_DIC_FRST_LANG_PLC").' '.gettext("NOT_FILLED"));
            } else if ($secondLang === null) {
                 $_SESSION['error'] = array(true,gettext("DL_ADD_DIC_SECOND_LANG_PLC").' '.gettext("NOT_FILLED"));
            } else if ($firstValueS === null || $firstValueS === '' || $secondValueS === null || $secondValueS === '') {
                
            } else {
                editVocabulary($firstValue, $secondValue, $firstValueS, $secondValueS, $dictionaryId, $firstLang, $secondLang);
            }
            header("location:" . $url);
            exit();
            break;
        case "addDic":
            addDictionary($dicName, $firstLang, $secondLang);
            header('location:' . $url);
            exit;
            break;
        case "addVoc":
            addVocabulary($dictionaryId, $firstValue, $secondValue);
            header("location:" . $url);
            exit;
            break;
        case 'deleteD':
            echo deleteDictionary($dictionaryId);
            exit();
            break;
        case 'deleteV':
            echo deleteVocabulary($dictionaryId, $firstValue, $secondValue);

            exit();
            break;
    }
}
