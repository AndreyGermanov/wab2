<?php

error_reporting (E_ALL);

include('kcaptcha.php');
if (session_id()=="")
	session_start();

$captcha = new KCAPTCHA();

//if($_REQUEST[session_name()]){
	$_SESSION['captcha_keystring'] = $captcha->getKeyString();
//}

?>