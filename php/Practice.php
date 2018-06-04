<?php

/**
 * Knihovna pro zpracování jednotlivých relací zkoušení.  
 *
 *  @author Oldřich Hradil 
 * 
 * */

/**
 * Vytvoří relaci zkoušení podle zadanýc parametrů
 * @param type $practiceName Název relace zkoušení
 * @param type $acceptAfter Po kolika psrávných odpovědích vyřadit z relace zkoušení
 * @param type $dictionaries Seznam identifikačních čísel slovníku pro relaci zkoušení
 * @return boolean
 */
function createPractice($practiceName, $acceptAfter, $dictionaries) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }
    $id = getClient()['id'];
    $db->beginTransaction();
    try {
        $query = $db->prepare("INSERT INTO practice (`practice_name`,`id`,"
                . "`accept_after`) VALUES(:practice_name,:id,"
                . ":accept_after)");
        $query->execute([':practice_name' => $practiceName, ':id' => $id, ':accept_after' => $acceptAfter]);

        $query = $db->prepare("SELECT id_practice from practice where id=:id and practice_name=:practice_name LIMIT 1");
        $query->execute([':practice_name' => $practiceName, ':id' => $id]);
        $practiceId = $query->fetchAll()[0]['id_practice'];


        foreach ($dictionaries as $dictionaryId) {
            if (!getDictionaryContent($dictionaryId)) {
                
            } else {
                $dictionaryContent = json_decode(getDictionaryContent($dictionaryId), true);

                foreach ($dictionaryContent as $row) {

                    $query = $db->prepare("INSERT INTO practice_content (`id_practice`,`id_dictionary`,"
                            . "`first_value`,`second_value`) VALUES(:id_practice,:id_dictionary,"
                            . ":first_value,:second_value)");

                    $query->execute([':id_practice' => $practiceId, ':id_dictionary' => $dictionaryId,
                        ':first_value' => $row['first_value'], ':second_value' => $row['second_value']]);
                };
            }
        }

        if (!$db->commit()) {
            $db->rollBack();

            return false;
        } else {

            return true;
        }
    } catch (PDOException $e) {

        if ($e->errorInfo[1] == 1062) {
            $_SESSION['error'] = array(true, gettext('PRAC_CREATE_PRAC_NAME_ALEXISTS'));
        } else {
            $_SESSION['error'] = array(true, gettext('SQL_ERROR'));
        }
        return false;
    }
}

/**
 * Získá seznam zkoušení uživatele
 * @param type $offset 
 * @return boolean
 */
function getClientsPractices($offset = null) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }
    $client = getClient();
    if ($client == null) {

        return false;
    }
    if ($offset !== null) {
        $query = $db->prepare("SELECT practice_name,id_practice,"
                . "accept_after FROM practice "
                . "where id=? ORDER BY timestamp DESC LIMIT 10 OFFSET ?");
        $query->bindValue(1, $client['id'], PDO::PARAM_INT);
        $query->bindValue(2, (int) $offset, PDO::PARAM_INT);
        $query->execute();
    } else {
        $query = $db->prepare("SELECT practice_name,id_practice,"
                . "accept_after FROM practice "
                . "where id=:id");

        $query->execute([':id' => $client['id']]);
    }
    $result = $query->fetchAll(PDO::FETCH_ASSOC);
    if (count($result) > 0) {

        return $result;
    } else {

        return false;
    }
}

/**
 * Získá obsah slovníku
 * @param type $practiceId Identifikační číslo relace zkoušení
 * @return boolean
 */
function getPracticeContent($practiceId) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }
    $query = $db->prepare("SELECT id_dictionary, first_value,"
            . " second_value,right_answers,wrong_answers FROM practice_content "
            . "where id_practice=:PRACTICE_id ORDER BY wrong_answers DESC");
    $query->execute([':PRACTICE_id' => $practiceId]);
    $result = $query->fetchAll(PDO::FETCH_ASSOC);
    if (count($result) > 0) {

        return $result;
    } else {

        return false;
    }
}

/**
 * Získá nastavení příslušné relace zkoušení
 * @param type $practiceId Identifikační číslo relace zkoušení
 * @return boolean
 */
function getPracticeSetting($practiceId) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }
    $query = $db->prepare("SELECT accept_after,MODE "
            . "FROM practice where id_practice=:PRACTICE_id");
    $query->execute([':PRACTICE_id' => $practiceId]);
    $result = $query->fetchAll(PDO::FETCH_ASSOC);
    if (count($result) > 0) {

        return $result;
    } else {

        return false;
    }
}

/**
 * Získá název relace zkoušení
 * @param type $practiceId Identifikační číslo relace zkoušení
 * @return boolean
 */
function getPracticeName($practiceId) {
    $db = connectToDatabase();
    if ($db == null) {
        return gettext('DB_NO_CONNECTION');
    }
    $query = $db->prepare("SELECT practice_name "
            . "FROM practice where id_practice=:PRACTICE_id");
    $query->execute([':PRACTICE_id' => $practiceId]);
    $result = $query->fetchAll(PDO::FETCH_ASSOC);
    if (count($result) === 1) {

        return $result[0]['practice_name'];
    } else {

        return false;
    }
}

/**
 * Zkontroluje zda je překlad správně
 * @param type $wordToKnow Slovíčko, které máme přeložit
 * @param type $answer Překlad
 * @param type $practiceId Identifikační číslo relace zkoušení
 * @return boolean
 */
function checkAnswer($wordToKnow, $answer, $practiceId) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }

    $mode = getPracticeSetting($practiceId)[0]['MODE'];
    $toSelect = $mode == 'LR' ? 'first_value' : 'second_value';
    $query = $db->prepare("SELECT first_value, second_value,right_answers,"
            . "wrong_answers FROM practice_content where id_practice=:PRACTICE_id "
            . "and $toSelect=:VALUE");

    $query->execute([':PRACTICE_id' => $practiceId, ':VALUE' => $wordToKnow]);
    $result = $query->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) > 0) {

        switch ($mode) {
            case 'LR':
                $foundAnswer = $result[0]['second_value'];
                break;
            case 'RL':
                $foundAnswer = $result[0]['first_value'];
                break;
            default :

                return false;
        }
        $foundAnswer = mb_convert_case($foundAnswer, MB_CASE_LOWER, "UTF-8");
        $answer = mb_convert_case($answer, MB_CASE_LOWER, "UTF-8");
        if (trim($foundAnswer) === trim($answer)) {
            $query = $db->prepare("UPDATE `practice_content` SET
`right_answers` =`right_answers`+'1'
WHERE `id_practice` = :id_practice AND `first_value` = :first_value AND `second_value` = :second_value");
            $success = $query->execute([':id_practice' => $practiceId,
                ':first_value' => $result[0]['first_value'], ':second_value' => $result[0]['second_value']]);

            return $success;
        } else {
            $query = $db->prepare("UPDATE `practice_content` SET
`wrong_answers` =`wrong_answers`+'1'
WHERE `id_practice` = :id_practice AND `first_value` = :first_value AND `second_value` = :second_value");
            $query->execute([':id_practice' => $practiceId,
                ':first_value' => $result[0]['first_value'], ':second_value' => $result[0]['second_value']]);

            return false;
        }
    } else {

        return false;
    }
}

/**
 * Náhodně získá slovíčko pro zkoušení
 * @param type $practiceId Identifikační číslo relace zkoušení
 * @return boolean
 */
function getVocForPractice($practiceId) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }
    $settings = getPracticeSetting($practiceId);
    $practiceContent = getPracticeContent($practiceId);

    if ($practiceContent === false) {


        return false;
    } else {
        $randomRow = $practiceContent[rand(0, count($practiceContent) - 1)];


        while ($randomRow['right_answers'] >= $settings[0]['accept_after']) {
            $randomRow = $practiceContent[rand(0, count($practiceContent) - 1)];
        }



        return $randomRow[$settings[0]['MODE'] == 'LR' ? 'first_value' : 'second_value'];
    }
}

/**
 * Zjistí zda už je daná relace zkoušení ukončena
 * @param type $practiceId Identifikační číslo relace zkoušení
 * @return boolean
 */
function isOver($practiceId) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }
    $settings = getPracticeSetting($practiceId);
    $query = $db->prepare("SELECT *  FROM practice_content "
            . "where id_practice=:PRACTICE_id AND right_answers < "
            . ":RIGHT_COUNT");
    $query->execute([':PRACTICE_id' => $practiceId, ':RIGHT_COUNT' =>
        $settings[0]['accept_after']]);
    $result = $query->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) > 0) {

        return false;
    } else {


        return true;
    }
}

/**
 * Získá úspěšnost zadané relace zkoušení
 * @param type $practiceId Identifikační číslo relace zkoušení
 * @return boolean|int
 */
function getSuccessRate($practiceId) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }
    $query = $db->prepare("SELECT sum(right_answers),sum(wrong_answers) FROM practice_content "
            . "where id_practice=:PRACTICE_id");
    $query->execute([':PRACTICE_id' => $practiceId]);
    $result = $query->fetchAll(PDO::FETCH_ASSOC);
    if (count($result) > 0) {
        $right = $result[0]['sum(right_answers)'];
        $wrong = $result[0]['sum(wrong_answers)'];
        if ($right == 0 && $wrong == 0) {

            return 0;
        }
        $successRate = round(($right / ($right + $wrong)) * 100, 2);

        return $successRate;
    } else {

        return false;
    }
}

/**
 * Vymaže zadanou relaci zkoušení 
 * @param type $practiceId Identifikační číslo relace zkoušení
 * @return boolean
 */
function deletePractice($practiceId) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }
    try {
        $query = $db->prepare("DELETE FROM practice "
                . "where id_practice=:PRACTICE_id and id=:id");
        return $query->execute([':PRACTICE_id' => $practiceId, ':id' => $_SESSION['id']]);
    } catch (PDOException $e) {
        $_SESSION['error'] = array(true, gettext('SQL_ERROR'));
        return false;
    }
}

/**
 * Získá správnou odpověd pro zadané slovíčko relace zkoušení
 * @param type $practiceId Identifikační číslo relace zkoušení
 * @param type Slovíčko, které jsme měli přeložit
 * @return boolean
 */
function getRightAnswer($practiceId, $wordToKnow) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }
    $mode = getPracticeSetting($practiceId)[0]['MODE'];

    switch ($mode) {
        case 'LR':
            $query = $db->prepare("SELECT second_value"
                    . " FROM practice_content where id_practice=:PRACTICE_id and first_value=:VALUE");
            break;
        case 'RL':
            $query = $db->prepare("SELECT first_value"
                    . " FROM practice_content where id_practice=:PRACTICE_id and second_value=:VALUE");
            break;
    }

    $query->execute([':PRACTICE_id' => $practiceId, ':VALUE' => $wordToKnow]);

    $result = $query->fetchAll();

    if (count($result) > 0) {
        $rightAnswer = $result[0][0];

        return $rightAnswer;
    } else {

        return false;
    }
}

/**
 * Restartuje relaci zkoušení
 * @param type $practiceId Identifikační číslo relace zkoušení
 * @return boolean
 */
function restartPractice($practiceId) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }
    $query = $db->prepare("UPDATE practice_content SET right_answers=0,
    wrong_answers=0 WHERE id_practice=:PRACTICE_id");
    return $query->execute([':PRACTICE_id' => $practiceId]);
}

/**
 * Upraví relaci zkoušení 
 * @param type $practiceId Identifikační číslo relace zkoušení
 * @param type $name Název relace zkoušení 
 * @param type $acceptAfter Po kolika psrávných odpovědích vyřadit z relace zkoušení
 * @return boolean
 */
function editPractice($practiceId, $name, $acceptAfter) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }
    $client = getClient();
    try {
        $query = $db->prepare("UPDATE practice SET practice_name=:PN, "
                . "accept_after=:AA WHERE id_practice=:Pid AND id=:id");
        return $query->execute([':PN' => $name, ':Pid' => $practiceId, ':AA' => $acceptAfter,
                    'id' => $client['id']]) ? true : gettext('DB_NO_CONNECTION');
    } catch (PDOException $e) {

        if ($e->errorInfo[1] == 1062) {
            $_SESSION['error'] = array(true, gettext('PRAC_CREATE_PRAC_NAME_ALEXISTS'));
        } else {
            $_SESSION['error'] = array(true, gettext('SQL_ERROR'));
        }
        return false;
    }
}

/**
 * Změni směr zkoušení relace zkoušení 
 * @param type $practiceId Identifikační číslo relace zkoušení
 * @return boolean
 */
function changePracticeDirection($practiceId) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }

    $mode = getPracticeSetting($practiceId)[0]['MODE'];
    $mode = $mode == 'RL' ? 'LR' : 'RL';

    $query = $db->prepare("UPDATE practice SET MODE=:M WHERE id_practice=:Pid");

    if ($query->execute([':Pid' => $practiceId, ':M' => $mode]) === true) {
        return true;
    } else {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));
        return false;
    }
}

/**
 * Zpracuje POST požadavek poslaný na stránku s relacemi zkoušení
 * @param type $post Pole hodnot poslaných v požadavku
 */
function processPracRequest($post) {
    $type = $post['type'];
    $practiceId = @$post['practiceId'];
    $wordToKnow = @$post['wordToKnow'];
    $translation = @$post['translation'];
    $practiceName = @$post['practiceName'];
    $acceptAfter = @$post['acceptAfter'];
    $dictionaries = @$post['dictionaries'];
    $dictionaryId = @$post['dictionaryId'];
    switch ($type) {
        case "clear":
            unset($_SESSION['creationDictionaries']);
            unset($_SESSION['practiceId']);

            break;
        case "addForCreation":
            if ($dictionaryId === null || $dictionaryId == '') {
                exit();
            }
            if (!isset($_SESSION['creationDictionaries'])) {
                $_SESSION['creationDictionaries'] = array();
            }
            $_SESSION['creationDictionaries'] = array($dictionaryId => $dictionaryId) + $_SESSION['creationDictionaries'];
            exit();
            break;
        case 'deleteForCreation':
            if ($dictionaryId === null || $dictionaryId == '') {
                exit();
            }
            if (isset($_SESSION['creationDictionaries'])) {
                unset($_SESSION['creationDictionaries'][$dictionaryId]);
            }
            exit();
            break;
        case 'create':
            unset($_SESSION['practiceId']);
            if ($practiceName === '') {
                echo json_encode(array(false, gettext('PRAC_CREATE_PRAC_NAME_MISSING')));
                exit();
            } else if ($acceptAfter == null) {
                echo json_encode(array(false, gettext('PRAC_CREATE_PRAC_ACCEPT_AFTER_MISSING')));
                exit();
            } else if (!is_numeric($acceptAfter)) {
              echo json_encode(array(false, gettext('PRAC_CREATE_WRONG_FORMAT_ACCEPTA')));
                exit();
            } else if ($dictionaries === 'empty') {
                echo json_encode(array(false, gettext('PRAC_CREATE_PRAC_DIC_MISSING')));
                exit();
            } else if (!createPractice($practiceName, $acceptAfter, $dictionaries)) {
                echo json_encode(array(false, $_SESSION['error'][1]));
                unset($_SESSION['error']);
                exit();
            } else {
                echo json_encode(array(true));
                unset($_SESSION['creationDictionaries']);
                exit();
            }
            break;
        case 'continue':
            checkUserPermitionOnId($practiceId, 'practice');
            $_SESSION['practiceId'] = $practiceId;
            if (isOver($practiceId)) {
                unset($_SESSION['practiceId']);
                echo json_encode(array('isOver', json_encode(getPracticeContent($practiceId)), $practiceId));
                exit();
            } else {
                $wordToKnow = getVocForPractice($practiceId);
                echo json_encode(array(true, $wordToKnow,
                    getSuccessRate($practiceId), getRightAnswer($practiceId, $wordToKnow), $practiceId));
                exit();
            }

            break;

        case 'delete':
            checkUserPermitionOnId($practiceId, 'practice');
            unset($_SESSION['practiceId']);
            if (deletePractice($practiceId)) {
                echo json_encode(array(true));
                exit();
            } else {
                echo json_encode(array(false, $_SESSION['error'][1]));
                unset($_SESSION['error']);
                exit();
            }

            break;

        case 'sendAnswer':
            $practiceId = $_SESSION['practiceId'];
            checkUserPermitionOnId($practiceId, 'practice');
            if ($wordToKnow === null || $wordToKnow === '') {
                $_SESSION['error'] = array(true, gettext("PRAC_SESSION_TO_TRANSLATE_LBL") . ' ' . gettext("NOT_FILLED"));
                header('location:' . BASE . $url);
                exit();
            } else if ($translation === null || $translation === '') {
                $_SESSION['error'] = array(true, gettext("PRAC_SESSION_TRANSLATION_LBL") . ' ' . gettext("NOT_FILLED"));
                header('location:' . BASE . $url);
                exit();
            }
            unset($_SESSION['creationDictionaries']);
            $result = checkAnswer($wordToKnow, $translation, $practiceId);
            if (isOver($practiceId)) {
                unset($_SESSION['practiceId']);
                echo json_encode(array('isOver', json_encode(getPracticeContent($practiceId)), $practiceId));
                exit();
            } else {

                echo json_encode(array($result, getSuccessRate($practiceId)));

                exit();
            }
            break;

        case 'restartPractice':
            checkUserPermitionOnId($practiceId, 'practice');
            if (restartPractice($practiceId)) {
                echo json_encode(array(true));
                exit();
            } else {
                echo json_encode(array(false, gettext('SQL_ERROR')));
                exit();
            }
        case "editPrac":
            checkUserPermitionOnId($practiceId, 'practice');

            if ($practiceName === '') {
                $_SESSION['error'] = array(true, gettext('PRAC_CREATE_PRAC_NAME_MISSING'));
                exit();
            } else if (empty($acceptAfter)) {
                $_SESSION['error'] = array(true, gettext('PRAC_CREATE_PRAC_ACCEPT_AFTER_MISSING'));
                exit();
            } else if (!is_numeric($acceptAfter)) {
                  $_SESSION['error'] = array(true, gettext('PRAC_CREATE_WRONG_FORMAT_ACCEPTA'));
                exit();
            } else {
                unset($_SESSION['practiceId']);

                unset($_SESSION['creationDictionaries']);
                editPractice($practiceId, $practiceName, $acceptAfter);
            }
            header('location:' . BASE . 'member/practice');
            exit();
            break;
        case "changeDirection":
            checkUserPermitionOnId($practiceId, 'practice');
            changePracticeDirection($practiceId);
            break;
        default :
            echo json_encode(array(false, gettext('SQL_ERROR')));
            exit();
    }
}
