<?php
/*
 * Tato knihovna obsahuje veškeré funkce zajištující validaci proměnných.
 * 
 * @author Oldřich Hradil 
 */

function checkName($name){
 return !preg_match('/[^a-žA-Ž0-9]/',$name)&&!
         preg_match('/\s+/',$name)?true:false;   
}

function checkPassword($password){
    
 return strlen($password)>5&&
         !preg_match('/\s+/',$password)?true:false;      
}

function checkPasswordsMatch($password,$passwordConfirm){
    return $password!==$passwordConfirm?false:true;
}




