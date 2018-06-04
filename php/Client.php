<?php
/**
 * Tato knihovna poskytuje veškeré informace týkající se přihlášeného klienta.
 *
 *  @author Oldřich Hradil 
 * 
 **/

/**
 * Poskytuje základní informace potřebné pro identifikaci klienta.
 * @return array|null $client Vrací pole obsahující inforamce o klientovi. Pokud není
 * žádný klient přihlášen, vrací null.
 */
function getClient(){
 if(!isset($_SESSION['id']) || !isset($_SESSION['email']) 
|| !isset($_SESSION['clientName'])||!isset($_SESSION['role'])){

     return null;
 }else{

     
                 $clientName=$_SESSION['clientName'];
                 $email= $_SESSION['email'];
                 $id=$_SESSION['id'];
                  $role=$_SESSION['role'];

if(!checkValidUser()){
    return null;
}else{
                 $client=array('id'=>$id,'clientName'=> $clientName,'email'=>$email,'role'=>$role);
   return $client;
 }
 
}

 }