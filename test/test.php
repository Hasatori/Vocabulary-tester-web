<?php
require_once '../php/Libraries.php';
session_start();
if(getClient()['role']!='ADMIN'){
header('location:'.BASE);
exit();
}
if (isset($_POST['test'])) {

    $report = null;
    define('NAME', 'Hasatori25');
    define('PASSWORD', '123456');
    define('EMAIL', 'hasatori1@gmail.com');
    $report = $report . '' . '<p class="info">PŘÍSTUP</p>';
    try {
        if (sendRegistrationForm(NAME, PASSWORD, EMAIL)) {
           
            $report = $report . '<p class="right">' . "Registrace funguje" . '</p>';
            
            
        } else {
            $report = $report . '<p class="wrong">' . $_SESSION['error'][1] . '</p>';
        }
        if (activateAccount(getAtivationAccessToken()['accessToken'])) {
            $report = $report . '<p class="right">' . "Aktivate účtu funguje " . '</p>';
        } else {
            $report = $report . '<p class="wrong">' . $_SESSION['error'][1] . '</p>';
        }
        if (login(EMAIL, PASSWORD)) {
            $report = $report . '<p class="right">' . "Přihlášení funguje" . '</p>';
            
        } else {
            $report = $report . '<p class="wrong">' . $_SESSION['error'][1] . '</p>';
        }
        if (sendForgottenPasswordForm(EMAIL)) {
            if (activateNewPassword(getNewPasswordAccessToken()['accessToken'])) {
                $report = $report . '<p class="right">' . 'Odeslání a aktivate nového hesla funguje.' . '</p>';
            } else {
                $report = $report . '<p class="wrong">' . $_SESSION['error'][1] . '</p>';
            }
        } else {
            $report = $report .'<p class="wrong">Odeslání zapomenutého hesla nefunguje</p>';
        }

        $report = $report . '<p class="info"> SLOVNÍKY A ZKOUŠENÍ</p>';
        define('DICNAME', 'dictionary');
        define('DICNAMEEDIT', 'dictionaryEdited');
        define('first_lang', 'Angličtina (Spojené státy)');
        define('second_lang', 'Čeština');
        define('FIRST_VAL', 'car');
        define('SECOND_VAL', 'auto');
        define('PRACNAME', 'practiceSession');
        define('PRACNAMEEDIT', 'practiceSessionEdited');

        if (addDictionary(DICNAME, first_lang, second_lang)) {
            $dictionaryName = @getTestingDic()[0]['dictionary_name'];
            $dictionryId = @getTestingDic()[0]['id_dictionary'];
            $firstLanguage = @getTestingDic()[0]['first_lang'];
            $secondLanguage = @getTestingDic()[0]['second_lang'];
            $report = $report . '<p class="right">' . 'Přidávání slovníků funguje - ' . $dictionaryName . ',' . $firstLanguage . ',' . $secondLanguage . '</p>';
            if (!addDictionary(DICNAME, first_lang, second_lang)) {
                $report = $report . '<p class="right">' . 'Kontrola duplicity u slovníků funguje' . '</p>';
            } else {
                $report = $report . '<p class="wrong">' . 'Kontrola duplicity u slovníků nefunguje' . '</p>';
            }
        } else {
            $report = $report . '' . addDictionary(DICNAME, first_lang, second_lang) . '';
        }
        $dictionaryId = @getTestingDic()[0]['id_dictionary'];

        if (editDictionary($dictionaryId, DICNAMEEDIT, second_lang, first_lang)) {
            $dictionaryName = @getTestingDic()[0]['dictionary_name'];
            $dictionryId = @getTestingDic()[0]['id_dictionary'];
            $firstLanguage = @getTestingDic()[0]['first_lang'];
            $secondLanguage = @getTestingDic()[0]['second_lang'];
            $report = $report . '<p class="right">' . 'Editace slovníku funguje - ' . $dictionaryName . ',' . $firstLanguage . ',' . $secondLanguage . '</p>';
        } else {

            $report = $report . '<p class="wrong">' . 'Editace slovníku nefunguje - ' . $dictionaryName . ',' . $firstLanguage . ',' . $secondLanguage . '</p>';
        }

        if (addVocabulary($dictionryId, FIRST_VAL, SECOND_VAL) === true) {
            $report = $report . '<p class="right">' . 'Přidávání slovíčěk funguje' . '</p>';
            if (!addVocabulary($dictionryId, FIRST_VAL, SECOND_VAL) === true) {
                $report = $report . '<p class="right">' . 'Kontrola duplicity u slovíček funguje' . '</p>';
            } else {
                $report = $report . '<p class="wrong">' . 'Kontrola duplicity u slovíček nefunguje' . '</p>';
            }
        } else {
            $report = $report . '<p class="wrong">' . $_SESSION['error'][1] . '</p>';
        }




        if (createPractice(PRACNAME, 5, array_column(getDictionaries(getTestingClient()[0]['id']), 'id_dictionary'))) {
            $practiceId = getTestingPrac()[0]['id_practice'];
            $practiceName = getTestingPrac()[0]['practice_name'];
            $report = $report . '<p class="right">' . 'Tvorba relace zkoušení funguje - ' . $practiceName . '</p>';
            if (!createPractice(PRACNAME, 5, array_column(getDictionaries(getTestingClient()[0]['id']), 'id_dictionary'))) {
                $report = $report . '<p class="right">' . 'Kontrola duplicity u relace zkoušení funguje' . '</p>';
            } else {
                $report = $report . '<p class="wrong">' . 'Kontrola duplicity u relace zkoušení nefunguje' . '</p>';
            }
        } else {

            $report = $report . '<p class="wrong">' . $_SESSION['error'][1] . '</p>';
        }

        if (editPractice($practiceId, PRACNAMEEDIT, false, 4)) {
            $report = $report . '<p class="right">' . 'Editace relace zkoušení funguje - ' . $practiceName . '</p>';
        } else {
            $report = $report . '<p class="wrong">' . $_SESSION['error'][1] . '</p>';
        }

        if (checkAnswer(FIRST_VAL, SECOND_VAL, $practiceId)) {
            $report = $report . '<p class="right">' . 'Kontrola správné odpovědi funguje' . '</p>';
        } else {
            $report = $report . '<p class="wrong">' . 'Kontrola správné odpovědi nefunguje' . '</p>';
        }
        if (!checkAnswer(FIRST_VAL, 'dům', $practiceId)) {
            $report = $report . '<p class="right">' . 'Kontrola špatné odpovědi funguje' . '</p>';
        } else {
            $report = $report . '<p class="wrong">' . 'Kontrola špatné odpovědi nefunguje' . '</p>';
        }
        if (changePracticeDirection($practiceId)) {
            if (checkAnswer(SECOND_VAL, FIRST_VAL, $practiceId)) {
                $report = $report . '<p class="right">' . 'Změna směru zkoušení funguje' . '</p>';
            } else {
                $report = $report . '<p class="wrong">' . 'Změna směru zkoušení nefunguje' . '</p>';
            }
        } else {
            $report = $report . '<p class="wrong">' . 'Změna směru zkoušení nefunguje' . '</p>';
        }
        if (deletePractice($practiceId)) {
            $report = $report . '<p class="right">' . 'Mazání relace zkoušení funguje' . '</p>';
        } else {
            $report = $report . '<p class="wrong">' . $_SESSION['error'][1] . '</p>';
        }

        if (deleteVocabulary($dictionryId, FIRST_VAL, SECOND_VAL) === true) {
            $report = $report . '<p class="right">' . 'Mazání slovíček funguje' . '</p>';
        } else {
            $report = $report . '<p class="wrong">' . $_SESSION['error'][1] . '</p>';
        }
        if (deleteDictionary($dictionaryId)) {
            $report = $report . '<p class="right">' . 'Mazání slovníku funguje' . '</p>';
        } else {
            $report = $report . '<p class="wrong">' . $_SESSION['error'][1] . '</p>';
        }

        $report = $report . '<p class="info"> ODHLAŠOVÁNÍ </p>';
        $report = logout() ? $report . '<p class="right">' . 'Odhlášení funguje' : $report . '</p><p class="wrong">' . 'Odhlášení nefunguje' . '</p>';

        destroyTestingUser();
        login('hradil.o@email.cz', '96696996');
        echo $report;
        unset($_SESSION['error']);
        exit();
    } catch (Exception $ex) {
       
        $report = $report . $ex . '';
        destroyTestingUser();
        login('hradil.o@email.cz', '96696996');
        echo $report;
        exit();
    }
} else {
    header('location:' . BASE);
}

function destroyTestingUser() {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }
    $email = EMAIL;
    $query = $db->prepare("DELETE FROM client WHERE email='$email'");


    $query->execute();
}

function getTestingClient() {
    $db = connectToDatabase();
    $email = EMAIL;
    $query = $db->prepare("SELECT * FROM client WHERE email='$email'");

    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function getAtivationAccessToken() {
    $db = connectToDatabase();
    $id = getTestingClient()[0]['id'];
    $query = $db->prepare("SELECT accessToken FROM activate_account WHERE id='$id'");

    $query->execute();
    return $query->fetchAll()[0];
}

function getNewPasswordAccessToken() {
    $db = connectToDatabase();
    $id = getTestingClient()[0]['id'];
    $query = $db->prepare("SELECT accessToken FROM change_password WHERE id='$id'");

    $query->execute();
    return $query->fetchAll()[0];
}

function getTestingDic() {
    $db = connectToDatabase();
    $id = getTestingClient()[0]['id'];

    $query = $db->prepare("SELECT * FROM dictionary WHERE id='$id'");
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * 
 */
function getTestingPrac() {
    $db = connectToDatabase();
    $id = getTestingClient()[0]['id'];

    $query = $db->prepare("SELECT * FROM practice WHERE id='$id'");
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function logout() {
    @session_start();
    if (!empty($_COOKIE['rememberMe'])) {
        $db = connectToDatabase();

        $series_id = $_COOKIE['rememberMe'];

        $query = $db->prepare("DELETE FROM remember_me "
                . "where seriesId=:SERIES_id");

        $query->execute([':SERIES_id' => $series_id]);
        setcookie('rememberMe', null, -1, '/');
    }

    unset($_SESSION);
    unset($_COOKIE['rememberMe']);
    session_regenerate_id();
    session_destroy();

    if (!isset($_SESSION) && !isset($_COOKIE['rememberMe'])) {
        return true;
    } else {
        return false;
    }
}
