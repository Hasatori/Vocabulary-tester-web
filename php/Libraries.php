<?php
/**
 * Tento soubor sdružuje veškeré knihovny aplikace. Je poté zahrnován do každého
 * skriptu, čím umožnuje práci s veškerými knihovnami. 
 *
 *  @author Oldřich Hradil 
 * 
 **/

require_once 'Localization.php';
require_once __DIR__ . '/../vendor/facebook/autoload.php';
require_once 'HtmlBuilder.php';
require_once 'Authorization.php';
require_once 'Validation.php';
require_once 'Mailer.php';
require_once 'Database.php';
require_once 'Constants.php';
require_once 'Dictionary.php';
require_once 'Client.php';
require_once 'Practice.php';



