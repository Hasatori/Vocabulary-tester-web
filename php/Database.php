<?php
/**
 * Knihovna obsahující veškeré funkce pro manupulaci s databází. 
 *
 *  @author Oldřich Hradil 
 * 
 **/
/**
 * Poskytuje připoneí k databázi, na základě přihlašovacích údajů obsažených 
 * v konfiguračním souboru. 
 * @param mysqli|null $conn Vrací object mysqli, popřípade null pokud nastala 
 * chyba. 
 */
function connectToDatabase(){
 $config = parse_ini_file(INI_LOCATION);   
//pripojeni do db na serveru eso.vse.cz
 try{
$db = new PDO('mysql:host=127.0.0.1;dbname='.$config['dbname'].';charset=utf8',$config['username'] , $config['password']);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
 } catch (PDOException $ex){
     
       return null;
 }

}
