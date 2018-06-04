<?php
require_once 'class.phpmailer.php';
/**
 * Knihovna starající se o veškerou práci týkající se emailů. 
 *
 *  @author Oldřich Hradil 
 * 
 **/

/**
 * Odesílá email potřebný pro dokončení registrace a aktivaci účtu klienta. 
 * @param string $receiver Email klienta.
 * @param string $hash Hash hodnota hesla, která je vyžadována skripty, při 
 * přechodu na odeslaný odkaz. 
 * @param string $password Heslo klienta, které je mu posíláno v potvrzovacím
 * emailu. 
 * @param string $username Uživatelské jméno klienta, které je mu posíláno v
 *  potvrzovacím emailu.
 * $return bool Pokud byl email úspěšně odeslán vrací true. 
 **/
function sendConfirmRegistration($receiver,$password,$username,$accessToken){
    
       $options = [
    'cost' => 12,
];

//// Multiple recipients
//$to = $receiver; // note the comma
//
//// Subject
//$subject = "=?utf-8?B?".base64_encode(gettext('MAIL_CR_SUB'))."?=";
//
//// Message
//$message = '
//<html>
//<head>
//  <title>'.gettext('MAIL_CR_TITLE').'</title>
//  <style>
//  
//
//  </style>
//</head>
//<body style=" background-color: #f2f2f2;font-family:Georgia;   ">
//<h1 class="mailHeading" style=" background-color:#2574A9;color: #f2f2f2;text-alignment:center;padding:2rem;">
//'.gettext('MAIL_CR_H1').'</h1>
//</br>
//</br>
//<h2 class="mailsubHeading" style="padding:2rem;">'.gettext('MAIL_CR_H2').'</h2>
//</br>
//<p style="padding:2rem;">'.gettext('MAIL_CR_USERNAME_LB').' '.$username.'</p>
//<p style="padding:2rem;">'.gettext('MAIL_CR_PASSWORD_LB').' '.$password.'</p>
//    </br>
//    </br>
//    
//<p style="padding:2rem;">'.gettext('MAIL_CR_LINK_LB').'
//<a class="mailActivationLink" href="'.BASE.'verify?'.$accessTokenLabel.'='
//        .$accessToken.'">'.BASE.'</a></p>
// 
//</body>
//</html>
//';
//
//// To send HTML mail, the Content-type header must be set
//$headers[] = 'MIME-Version: 1.0';
//$headers[] = 'Content-type: text/html; charset=uft-8';
//
//// Additional headers
//$headers[] = 'To: <'.$to.'>';
//$headers[] = 'From: <hasatori1@gmail.com>';
//
//
//// Mail it
//return  mail($to, $subject, $message, implode("\r\n", $headers));


$mailer=new PHPMailer();
$mailer->isSendmail();//nastavení, že se mail má odeslat přes sendmail
//přidání adres (obdobně jdou přidat adresy do polí CC a BCC
$mailer->addAddress($receiver);
$mailer->setFrom(ODESILATEL);
//nastavíme předmět
$mailer->CharSet='utf-8';
$mailer->Subject=gettext('MAIL_CR_SUB');
//přidáme HTML obsah (může jim být celý HTML dokument, nebo jen kousek body)
$mailer->msgHTML('<!DOCTYPE html>
<html>
<head>
  <title>'.gettext('MAIL_CR_TITLE').'</title>

</head>
<body style=" background-color: #f2f2f2;font-family:Georgia;" >
<h1 class="mailHeading" style=" background-color:#2574A9;color: #f2f2f2;text-alignment:center;padding:2rem;">
'.gettext('MAIL_CR_H1').'</h1>
</br>
</br>
<h2 class="mailsubHeading" style="padding:2rem;">'.gettext('MAIL_CR_H2').'</h2>
</br>
<p style="padding:2rem;">'.gettext('MAIL_CR_USERNAME_LB').' '.$username.'</p>
<p style="padding:2rem;">'.gettext('MAIL_CR_PASSWORD_LB').' '.$password.'</p>
    </br>
    </br>
    
<p style="padding:2rem;">'.gettext('MAIL_CR_LINK_LB').'
<a class="mailActivationLink" href="'.BASE.'verify?accessToken='
        .$accessToken.'">'.BASE.'</a></p>
 
</body>
</html>
');

return $mailer->send();
}
/**
 * Odesílá email potřebný pro aktivaci nového heslo v případě, že klient staré 
 * zapomněl. Přikládá odkaz, který aktivuje uživatelův účet. Z důvodu zajištění 
 * vyšší bezpečnosti jsou parametry GET požadavku zahešovány.
 *@param string $receiver Email klienta.
 * @param string $newPassword  Nové heslo klienta.
 * @param string $hash Hash hodnota starého hesla klienta. Je využívána při 
 * aktivaci nového hesla pro oveření správnosti. 
 * $return bool Pokud byl email úspěšně odeslán vrací true. 
 **/
function sendForgottenPasswordEmail($receiver,$newPassword,$accessToken){

       $options = [
    'cost' => 12,
];


//// Multiple recipients
//$to = $receiver; // note the comma
//
//// Subject
//$subject = "=?utf-8?B?".base64_encode(gettext('MAIL_FP_SUB'))."?=";
//
//// Message
//$message = '
//<html>
//<head>
//  <title>'.gettext('MAIL_FP_TITLE').'</title>
//  <style>
//  
//
//  </style>
//</head>
//<body style=" background-color: #f2f2f2;font-family:Georgia;   ">
//<h1 class="mailHeading" style=" background-color:#2574A9;color: #f2f2f2;text-alignment:center;padding:2rem;">'.gettext('MAIL_FP_H1').'</h1>
//</br>
//</br>
//
//<p style="padding:2rem;">'.gettext('MAIL_FP_PASSWORD_LB').' '.$newPassword.'</p>
//    </br>
//    </br>
//    
//<p style="padding:2rem;">'.gettext('MAIL_FP_LINK_LB').'
//<a  class="mailActivationLink" href="'.BASE.'newPasswordA?'.$accessTokenLabel.'='.
//$accessToken.'"> '.BASE.'
//  </a></p>
// 
//</body>
//</html>
//';
//
//// To send HTML mail, the Content-type header must be set
//$headers[] = 'MIME-Version: 1.0';
//$headers[] = 'Content-type: text/html; charset=uft-8';
//
//// Additional headers
//$headers[] = 'To: <'.$to.'>';
//$headers[] = 'From: <hasatori1@gmail.com>';
//
//
//// Mail it
//return mail($to, $subject, $message, implode("\r\n", $headers));    

$mailer=new PHPMailer();
$mailer->isSendmail();//nastavení, že se mail má odeslat přes sendmail
//přidání adres (obdobně jdou přidat adresy do polí CC a BCC
$mailer->addAddress($receiver);
$mailer->setFrom(ODESILATEL);
//nastavíme předmět
$mailer->CharSet='utf-8';
$mailer->Subject=gettext('MAIL_FP_SUB');
//přidáme HTML obsah (může jim být celý HTML dokument, nebo jen kousek body)
$mailer->msgHTML('<!DOCTYPE html>
<html>
<head>
  <title>'.gettext('MAIL_FP_TITLE').'</title>
  <style>
  

  </style>
</head>
<body style=" background-color: #f2f2f2;font-family:Georgia;   ">
<h1 class="mailHeading" style=" background-color:#2574A9;color: #f2f2f2;text-alignment:center;padding:2rem;">'.gettext('MAIL_FP_H1').'</h1>
</br>
</br>

<p style="padding:2rem;">'.gettext('MAIL_FP_PASSWORD_LB').' '.$newPassword.'</p>
    </br>
    </br>
    
<p style="padding:2rem;">'.gettext('MAIL_FP_LINK_LB').'
<a  class="mailActivationLink" href="'.BASE.'newPasswordA?accessToken='.
$accessToken.'"> '.BASE.'
  </a></p>
 
</body>
</html>
');
//volitelně lze přidat alternativní obsah (pokud nemá být vytvořen z HTML obsahu)
//$mailer->AltBody='alternativní obsah';
//přidáme přílohu
return $mailer->send();


}
?>
