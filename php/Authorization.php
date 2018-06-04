<?php

/*
 * Tato knihovna obsahuje veškeré funkce, které souvicí s ověřením uživatele,
 * registrací, přihlašování, kontrolou správnosti apod. 
 * 
 * @author Oldřich Hradil 
 */



/* * ****************** KONTROLA VSTUPU ************************ */

/**
 * Ověří zda je uživatel přihlášen, pokud není vrací false. Tato funkce je 
 * umístěna před veškeré skripty, címž je zajištěno že musí být uživatel vždy 
 * přihlášen, jinak je přesměrován na do sekce pro nepřihlášení klienty.
 * @return bool
 */
function checkValidUser() {
    @session_start();


    if (isset($_SESSION['id']) && isset($_SESSION['email']) && isset($_SESSION['lastAccess'])) {

        $id = $_SESSION['id'];

        $lastAccess = $_SESSION['lastAccess'];
        if ((time() - $lastAccess) >= 60 * 30) {
            include 'Logout.php';
        } elseif (!is_numeric($id)) {
            return false;
        }
        $_SESSION['lastAccess'] = time();
        return checkUserExists($id);
    } else if (isset($_COOKIE['rememberMe'])) {

        return checkValidAccessToken($_COOKIE['rememberMe']);
    } else {
        return false;
    }
}

/**
 * Ověří zda uživatel odpovádající kombinaci $id a $type. Pokud uživatel 
 * neexistuje vrací false.
 *  @param integer $id Identifikační číslo klienta
 *  @param string $type Typ klienta, jedná se buď o klienta z facebooku, 
 * který je označen zkratkou "FB" nebo bežného klienta označeného "REG". 
 * @return bool
 */
function checkUserExists($id) {

    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }
    $query = $db->prepare("SELECT * FROM client WHERE id=:id");
    $query->execute([':id' => $id]);
    $result = $query->fetchAll(PDO::FETCH_ASSOC);
    if (count($result) === 1) {
        return true;
    } else {

        return false;
    }
}

/**
 * Kontroluje zda má uživatel právo manipulovat s identifikačním číslem příslušné tabulky
 * @param type $elementId
 * @param type $tableName
 * @return boolean
 */
function checkUserPermitionOnId($elementId, $tableName) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }
    $id = getClient()['id'];
    switch ($tableName) {
        case 'dictionary':
            $query = $db->prepare("SELECT * FROM dictionary where id=:id and id_dictionary=:elementId");
            break;
        case 'practice' :
            $query = $db->prepare("SELECT * FROM practice where id=:id and id_practice=:elementId");
            break;
        case 'containsOf':
            $query = $db->prepare("SELECT * FROM  contains_of   WHERE id_dictionary = (SELECT id_dictionary FROM dictionary where id=:id and id_dictionary=:elementId ) LIMIT 1");

            break;
        default :

            include 'Logout.php';
            return false;
    }

    $query->execute([':id' => $id, ':elementId' => $elementId]);
    $result = $query->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) === 1) {
        return true;
    } else {

        include 'Logout.php';
        return false;
    }
}

/* ########################################################################## */

/* * ****************** PŘIHLÁŠENÍ ************************ */

/**
 * Na základě údajů odeslaných formulářem vybere uživatele a poté porovnává zda 
 * hodnota zadaného hesla odpovádí hash hodnotě, která je v databázi. 
 * 
 * @param string $email Emailová adresa klienta
 *  @param string $password Heslo klienta.
 * @param bool $fb  Nepovinný parametr, který udává zda se jedná o přihlásení 
 * uživatele přes facebook. Pokud není uveden jedná se o přihlášení běžného 
 * uživatele. 
 * @param bool $mobile Nepovinný parametr, který udává zda se uživatel přihlašuje 
 * přes mobilní aplikaci. Pokud není uvede uživatel se přihlašuje z webu.
 * 
 * @return bool|string Vrací true pokud byl klient úspešně přihlásen. String je 
 * vracen pouze v případě že nastala chyba, je poté zpracován ve skriptu, který
 * používá tuto funkci a vypisován uživateli. 
 * 
 */
function login($email, $password, $rememberMe = false) {

    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }

    $query = $db->prepare("SELECT * FROM client where email=:EMAIL");
    $query->execute([':EMAIL' => $email]);
    $result = $query->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) === 1) {

        $hash = @$result[0]['password'];
        $id = $result[0]['id'];
        $email = $result[0]['email'];
        $clientName = @$result[0]['name'];
        $role = $result[0]['role'];
        $active = $result[0]['active'];

        if ($hash === null) {
            $_SESSION['info'] = array(true, gettext('R_FB_INFO_MSG'));
            return false;
        } else
        if ($active == 0) {
            $_SESSION['error'] = array(true, gettext('LOGIN_NOT_ACTIVATED'));
            return false;
        }
    } else {

        $_SESSION['error'] = array(true, gettext('L_WRONG_EMAIL'));
        return false;
    }

    if (password_verify($password, $hash)) {

        @session_start();

        $_SESSION['id'] = $id;
        $_SESSION['email'] = $email;
        $_SESSION['clientName'] = $clientName;
        $_SESSION['role'] = $role;
        $_SESSION['lastAccess'] = time();
        session_regenerate_id();
        if ($rememberMe) {
            setAccessToken($db, $id);
        }

        return true;
    } else {
        $_SESSION['error'] = array(true, gettext('L_WRONG_PASSWORD'));
        return false;
    }
}

function setAccessToken($db, $id) {

    $seriesId = generateRandomString(100);

    $query = $db->prepare("SELECT * from remember_me where seriesId='$seriesId'");
    $query->execute();
    while (count($query->fetchAll()) === 1) {

        $seriesId = generateRandomString(10);
        $query->execute();
    }

    $options = [
        'cost' => 12,
    ];


    setcookie('rememberMe', $seriesId, time() + (60 * 60 * 24 * 14), "/", "", true, true);

    $query = $db->prepare("INSERT INTO remember_me (seriesId,"
            . "clientId) VALUES(:SERIES_id,:id)");

    $query->execute([':SERIES_id' => $seriesId
        , ':id' => $id]);
}

/**
 * Nastaví přístupový token pro aktuálně přihlášeného klienta.
 */
function checkValidAccessToken($seriesId) {

    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }



    $query = $db->prepare("SELECT * "
            . "from remember_me where seriesId=:SERIES_id");
    $query->execute([':SERIES_id' => $seriesId]);
    $result = $query->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) === 1) {


        $id = $result[0]['clientId'];

        $query = $db->prepare("SELECT * "
                . "from client where id=:id");
        $query->execute([':id' => $id]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $email = $result[0]['email'];
        $clientName = $result[0]['name'];
        $role = $result[0]['role'];
        $_SESSION['id'] = $id;
        $_SESSION['email'] = $email;
        $_SESSION['clientName'] = $clientName;
        $_SESSION['role'] = $role;

        return true;
    } else {

        return false;
    }
}

/* ########################################################################## */

/* * ****************** REGISTRACE ************************ */

/**
 * Ověří zda již nebylo zadané uživatelské jméno použito. 
 *  @param string $username id klienta.
 * @return bool|string String je vráce pokud nastala chyba nesouvisejíci s ově
 * řením, ta je pak zpracována příslušným skriptem a vypsána uživateli. 
 */
function checkUsernameUnique($username) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }

    $query = $db->prepare("SELECT * FROM client where name=:USERNAME");
    $query->execute([':USERNAME' => $username]);
    $result = $query->fetchAll(PDO::FETCH_ASSOC);
    if (count($result) === 1) {

        return false;
    } else {

        return true;
    }
}

/* Ověří zda již není email běžným uživatelem pužíván. Tato kontrola se nevzta-
 * huje na uživatele z Facebooku, jelikož může být uživatel, jež použil při re-
 * gistraci stejný email jako používá na Facebooku. 
 * @return bool
 */

function checkEmailUnique($email) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }

    $query = $db->prepare("SELECT * FROM client where email=:EMAIL");
    $query->execute([':EMAIL' => $email]);
    $result = $query->fetchAll(PDO::FETCH_ASSOC);
    if (count($result) === 1) {
        $passwordHash = @$result[0]['password'];
  
        if ($passwordHash == null) {
            $_SESSION['info'] = array(true, gettext('R_FB_INFO_MSG'));
        } else {
            $_SESSION['info'] = array(true, gettext('R_EMAILCHECK'));
        }
        return false;
    } else {

        return true;
    }
}

/* Ověří zda email běžného uživatele existuje. 
 * @return bool
 */

function checkEmailExists($email) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }

    $query = $db->prepare("SELECT * FROM client where email=:EMAIL");
    $query->execute([':EMAIL' => $email]);
    $result = $query->fetchAll(PDO::FETCH_ASSOC);
    if (count($result) === 1) {

        return true;
    } else {

        return false;
    }
}

/**
 * Vloží nového uživatele do databáze a poté odešle potvrzovací email. Zadané 
 * heslo je zahešováno.
 *  @param string $loginName Uživatelské jméno klienta.
 *  @param string $clientPassword Heslo uživatele.
 * @param string $email Email uživatele.
 * @return bool|string String je vráce pokud nastala chyba nesouvisejíci s 
 * ověřením, ta je pak zpracována příslušným skriptem a vypsána uživateli.
 */
function sendRegistrationForm($loginName, $clientPassword, $email) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }

    $options = [
        'cost' => 12,
    ];
    $hashedclientPassword = password_hash($clientPassword, PASSWORD_BCRYPT, $options);
    try {

        $query = $db->prepare("INSERT INTO client (name,email,password) "
                . "VALUES(:name,:email,:password)");
        if ($query->execute([':name' => $loginName, ':email' => $email,
                    ':password' => $hashedclientPassword]) == true) {
            $accessToken = generateRandomString(20);
            $query = $db->prepare("SELECT * from activate_account where accessToken='$accessToken'");
            $query->execute();
            while (count($query->fetchAll()) === 1) {
                $accessToken = generateRandomString(20);
                $query->execute();
            }
            $query = $db->prepare("SELECT id from client where email=:email");

            $query->execute([':email' => $email]);
            $time = time();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);

            $query = $db->prepare("INSERT INTO activate_account(accessToken,id,requestStart)"
                    . "values(:accessToken,:id,$time);");
            $query->execute([':accessToken' => $accessToken, ':id' => $result[0]['id']]);
            return sendConfirmRegistration($email, $clientPassword, $loginName, $accessToken);
        } else {
            return false;
        }
    } catch (PDOException $e) {


        return false;
    }
}

/**
 * Aktivuje uživateli účet, tj. změní hodnotu atributu "active" z 0 na 1.
 *  @param string $email Email klienta.
 *  @param string $hash Zahešované heslo uživatele, je použito pro výběr klienta.
 * @return bool|string String je vráce pokud nastala chyba nesouvisejíci s ově
 * řením, ta je pak zpracována příslušným skriptem a vypsána uživateli.
 */
function activateAccount($accessToken) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }
    $query = $db->prepare("SELECT client.*,requestStart FROM activate_account"
            . " join client on client.id=activate_account.id"
            . " where accessToken=:accessToken");

    $query->execute([':accessToken' => $accessToken]);
    $result = $query->fetchAll(PDO::FETCH_ASSOC);
    if (count($result) === 1) {
        if ((time() - $result[0]['requestStart']) > 60 * 60 * 24) {
            $_SESSION['error'] = array(true, "Platnost odkazu vypršela");
            return false;
        } else {

            $query = $db->prepare("DELETE FROM activate_account where id=:id");
            $query->execute([':id' => $result[0]['id']]);
            $query = $db->prepare("UPDATE client SET active=1 where id=:id");

            return $query->execute([':id' => $result[0]['id']]);
        }
    } else {
        $_SESSION['error'] = array(true, "Platnost odkazu vypršela");
        return false;
    }
}

/* ########################################################################## */

/* * ****************** ZAPOMENUTÉ HESLO ************************ */

/**
 * Ověří zda zadaný email existuje. Pokud ano, vygeneruje nové heslo, zahashuje
 *  jej,uloží do databáze pod atribut s novými hesli a odešle jej 
 * na tento email. Dokud není na email odpovezeno, tak zůstává aktivní původní 
 * heslo uživatele. Jelikož jsou hesla uživatelů zahashovaná, tak není možné 
 * uživateli poslat jeho původní heslo. 
 *  @param string $email Email klienta.
 *  @return bool|string String je vráce pokud nastala chyba nesouvisejíci s ově
 * řením, ta je pak zpracována příslušným skriptem a vypsána uživateli.
 */
function sendForgottenPasswordForm($email) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }
    global $error;
    $query = $db->prepare("SELECT * FROM client WHERE email=:EMAIL");


    $query->execute([':EMAIL' => $email]);
    $result = $query->fetchAll(PDO::FETCH_ASSOC);
    if (count($result) === 1) {

        $id = $result[0]['id'];


        $query = $db->prepare("SELECT * FROM change_password WHERE id=:id");


        $query->execute([':id' => $id]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        $newPassword = generateRandomString(6);
        $options = [
            'cost' => 12,
        ];
        $hashedclientPassword = password_hash($newPassword, PASSWORD_BCRYPT, $options);
        $accessToken = generateRandomString(20);
        $query = $db->prepare("SELECT * from change_password where accessToken='$accessToken'");
        $query->execute();
        while (count($query->fetchAll()) === 1) {
            $accessToken = generateRandomString(20);
            $query->execute();
        }

        $currentTime = time();

        if (count($result) > 0 && ((time() - $result[0]['requestStart']) > 60 * 60 * 24)) {

            $query = $db->prepare("UPDATE change_password set requestStart='$currentTime'"
                    . " where id=:id");
            $query->execute([':id' => $id]);
        } else if (count($result) > 0) {

            $_SESSION['error'] = array(true, "Žádost již byla odeslána.");
            return false;
        } else {
            $query = $db->prepare("INSERT INTO change_password(accessToken,id,newPassword,"
                    . "requestStart) VALUES('$accessToken','$id','$hashedclientPassword','$currentTime')");
            $query->execute();
        }
        return sendForgottenPasswordEmail($email, $newPassword, $accessToken);
    } else {
        $_SESSION['error'] = array(true, gettext('FPWD_ERROR_MSG'));
        return false;
    }
}

/**
 * Převede proměnnou @var $chars na pole, které poté náhodně promíchá. Poté z 
 * tohoto pole náhodně vybírá hodnoty a přiřazuje proměnné @var $newPassword a 
 * to dokud není heslo 10 znaků dlouhé.  
 *  @param Integer $length Délka náhodného stringu pro vygenerování.
 *  @return string $newPassword Vrací nové, vygenerované heslo. 
 */
function generateRandomString($length) {

    $chars = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijk"
            . "lmnopqrstuvwxyz";
    $charArray = array();
    for ($i = 0; $i < strlen($chars); $i++) {
        array_push($charArray, substr($chars, $i, 1));
    }

    shuffle($charArray);
    $randomString = "";
    for ($i = 0; $i < $length; $i++) {
        $randomNumber = random_int(0, count($charArray) - 1);
        $randomString .= $charArray[$randomNumber];
    }
    return $randomString;
}

/**
 * Aktivuje uživateli nové heslo a to na základě odpovědi na zaslaný email. 
 *  
 *  @param string $email Email klienta.
 *  @param string $hash Hash hodnota nového hesla, získaná z odkazu a ověřována
 * proti hodnotě z databáze.
 *   $return bool
 */
function activateNewPassword($accessToken) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }
    $query = $db->prepare("SELECT * from change_password where accessToken=:accessToken LIMIT 1");
    $query->execute([':accessToken' => $accessToken]);
    $result = $query->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) == 1) {
        $requestStart = $result[0]['requestStart'];
        $id = $result[0]['id'];
        $newPassword = $result[0]['newPassword'];

        if ((time() - $requestStart) > 60 * 60 * 24) {
            $_SESSION['error'] = array(true, "Platnost odkazu vypršela");
            return false;
        } else {
            $db->beginTransaction();

            $query = $db->prepare("DELETE FROM change_password where id=:id");
            $query->execute([':id' => $id]);
            $query = $db->prepare("UPDATE client SET password=:PASSWORD where id=:id");
            $query->execute([':PASSWORD' => $newPassword, ':id' => $id]);
            if (!$db->commit()) {
                $db->rollBack();
                $_SESSION['error'] = array(true, gettext('SQL_ERROR'));
                return false;
            } else {
                return true;
            }
        }
    } else {
        $_SESSION['error'] = array(true, "Platnost odkazuuu vypršela");
        return false;
    }
}

/* ########################################################################## */

/* * ************************* AUTORIZACE PŘES FACEBOOK ******************** */

/**
 *  Vrací objekt obsahující informace o aplikaci. Tento objekt je vyžadován
 * při odesílání požadavku na získání přihlašovacího tokenu. 
 *  @return object $fb 
 */
function getFbAppObject() {
    $fb = new Facebook\Facebook([
        'app_id' => '361160927732671',
        'app_secret' => '1dc7618b0af9d90d84b7da67a93db8de',
        'status' => true,
        'default_graph_version' => 'v2.10',
    ]);
    return $fb;
}

/**
 *  Vrací url adresu, která slouží k získání aktivačního tokenu uživatele. 
 *   $return string $loginUrl Může také vrace chybové hlášení, které je zpraco-
 * váno a zobrazeno uživateli. 
 */
function getFbUserAuth() {

    $fb = getFbAppObject();

    $helper = $fb->getRedirectLoginHelper();

    $permissions = ['email'];
    @$loginUrl = $helper->getLoginUrl(BASE . 'index.php?rememberMe=false', $permissions);

    if (!$loginUrl) {
        return "Nastala chyba při přihlášení přes Facebook";
    } else {

        return $loginUrl;
    }
}

/**
 *  Ověří zda má uživatel platný přístupový token. Pokud ano použijej jej, pro 
 * získání JSON souboru, který obsahuje základní údaje o uživateli, jako 
 * email, jméno a id. Tyto údaje jsou poté použity buď pro přihlášení uživatele,
 * který se sem již přes Facebook přihlašoval, nebo pro zavedení nového Faceboo-
 * kového uživatele do databáze. Získané údaje jsou poté uloženy do SESSION.
 * 
 * @return bool|string Vrací boolean vyjadřující zda bylo přihlášení úspěšné nebo
 * ne. String je vrace pouze pokud nastala jiná chyba, ta je zpracována příslu-
 * šným skriptem a zobrazena uživateli. 
 */
function loginFbUser() {
    $fb = getFbAppObject();

    $helper = $fb->getRedirectLoginHelper();
    $helper->getPersistentDataHandler()->set('state', $_GET['state']);

    $rememberMe = $_GET["rememberMe"];

    try {
        $accessToken = $helper->getAccessToken(BASE . 'index.php?rememberMe=' . $rememberMe);
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        // When Graph returns an error
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        // When validation fails or other local issues
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }

    if (!isset($accessToken)) {
        if ($helper->getError()) {
            header('HTTP/1.0 401 Unauthorized');
            echo "Error: " . $helper->getError() . "\n";
            echo "Error Code: " . $helper->getErrorCode() . "\n";
            echo "Error Reason: " . $helper->getErrorReason() . "\n";
            echo "Error Description: " . $helper->getErrorDescription() . "\n";
        } else {
            header('HTTP/1.0 400 Bad Request');
            echo 'Bad request';
        }
        exit;
    }

// Logged in
// Uncomment this to see token value
    /* echo '<h3>Access Token</h3>';
      var_dump($accessToken->getValue()); */

// The OAuth 2.0 client handler helps us manage access tokens
    $oAuth2client = $fb->getOAuth2client();

// Get the access token metadata from /debug_token
    $tokenMetadata = $oAuth2client->debugToken($accessToken);
//Uncomment this if you want to see token Metadata
    /* echo '<h3>Metadata</h3>';
      var_dump($tokenMetadata); */

// Validation (these will throw FacebookSDKException's when they fail)
    $tokenMetadata->validateAppId('361160927732671');
// If you know the user id this access token belongs to, you can validate it here
//$tokenMetadata->validateUserId('123');
    $tokenMetadata->validateExpiration();

    if (!$accessToken->isLongLived()) {
        // Exchanges a short-lived access token for a long-lived one
        try {
            $accessToken = $oAuth2client->getLongLivedAccessToken($accessToken);
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
            exit;
        }

        echo '<h3>Long-lived</h3>';
        var_dump($accessToken->getValue());
    }


    $fb->setDefaultAccessToken($accessToken);
    $response = $fb->get('/me?fields=name,email,id');
    $userNode = $response->getGraphUser();
    $facebookId = $userNode->getField('id');
    $email = (string) $userNode->getField('email');
    $name = (string) $userNode->getField('name');

    return loginWithExternalService($facebookId, $email, $name, 'facebook', $rememberMe);
}

/* * ************************* PŘIHLÁŠENÍ PŘES EXTERNÍ SLUŽBU ******************** */

/**
 * Přihlašuje uživatele, který se přihlašuje přes externí sluzbu
 * @param type $serviceId Identifikační číslo uživatele sluzby
 * @param type $email Email uživatele 
 * @param type $name Jméno uživatele
 * @param type $serviceName Název služby 
 * @param type $rememberMe Zda si máme uživatele pamatovat
 * @return boolean Při úspěchu vrací true
 */
function loginWithExternalService($serviceId, $email, $name, $serviceName, $rememberMe) {
    $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }
    if (!checkServiceName){
        return false;
    }
    $stmt = $db->prepare("SELECT * FROM client WHERE email =:EMAIL LIMIT 1");
    $stmt->execute([':EMAIL' => $email]);
    $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($user) === 1) {

        $serviceIdD = $user[0][$serviceName . '_id'];

        if ($serviceIdD == null) {
            $stmt = $db->prepare("UPDATE client SET " . $serviceName . "_id=:SERVICE_id "
                    . "where email=:EMAIL");
            $stmt->execute([':EMAIL' => $email, ":SERVICE_id" => $serviceId]);
        } else if ($serviceIdD == !$serviceId) {
            $_SESSION['error'] = array(true, 'Neplatné identifikační číslo!');
            return false;
        } else {

            $_SESSION['id'] = $user[0]['id'];
            $_SESSION['email'] = $user[0]['email'];
            $_SESSION['clientName'] = $user[0]['name'];
            $_SESSION['role'] = $user[0]['role'];
            $_SESSION['lastAccess'] = time();
            session_regenerate_id();
            if ($rememberMe === "true") {
                setAccessToken($db, $user[0]['id']);
            }
            return true;
        }
    } else {
        
        $stmt = $db->prepare("INSERT INTO client(name,email,"
                . "active,".$serviceName."_id) VALUES(:name,:email,1,:service_id)");
        $stmt->execute([':email' => $email, ':name' => $name, ':service_id' => $serviceId]);
        $stmt = $db->prepare("SELECT * FROM client WHERE email =:EMAIL LIMIT 1");
        $stmt->execute([':EMAIL' => $email]);
        $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $_SESSION['id'] = $user[0]['id'];
        $_SESSION['email'] = $user[0]['email'];
        $_SESSION['clientName'] = $user[0]['name'];
        $_SESSION['role'] = $user[0]['role'];
        $_SESSION['lastAccess'] = time();
        session_regenerate_id();
        if ($rememberMe === "true") {
            setAccessToken($db, $user[0]['id']);
        }
        return true;
    }
}
/**
 * Ověří zda je název služby mezi povolenými hodnotami.
 * @param type $name
 * @return boolean
 */
function checkServiceName($name){
    switch ($name){
        case 'facebook':return true;
        case 'google':return true;
        case 'twitter':return true;
        case 'github':return true;
        default :return false;
                
    }
}

/*##########################################################################*/

function changePassword($newPassword,$email){
    
     $db = connectToDatabase();

    if ($db == null) {
        $_SESSION['error'] = array(true, gettext('DB_NO_CONNECTION'));

        return false;
    }  
        $options = [
        'cost' => 12,
    ];
    $hashedclientPassword = password_hash($newPassword, PASSWORD_BCRYPT, $options);
        $stmt = $db->prepare("UPDATE client set password=:password where "
                . "email=:email");
 return  $stmt->execute([':password' => $hashedclientPassword,':email'=>$email]);
 
    
}