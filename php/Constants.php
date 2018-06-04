<?php

/**
 * Knihovna obsahující základní konstanty nezbytné pro chod aplikace. 
 *
 *  @author Oldřich Hradil 
 * 
 * */
/**
 * Definice domény aplikace. 
 */
$protocol = "https://";
$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER["CONTEXT_PREFIX"] . '/VocabularyTester/';
define('BASE', $url);

/* Lokace konfiguračního souboru, kde se nachází přihlašovací údaje k databázi. */
define('INI_LOCATION', __DIR__ . '/config.ini');
define('ODESILATEL', 'hrao01@vse.cz');

function myExceptionHandler(Exception $ex) {
    $_SESSION['error'] = array(true, gettext('SQL_ERROR'));
    $url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('location:' . $url);
    exit();
}


set_exception_handler("myExceptionHandler");
