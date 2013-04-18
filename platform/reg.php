<?php session_start();?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>ЛВА Управление:: Регистрация</title>
        <link rel="stylesheet" type="text/css" href="/platform/Table.css"/>
        <link rel="stylesheet" type="text/css" href="/platform/Mailbox.css"/>
        <link rel="stylesheet" type="text/css" href="/platform/window.css"/>
        </head>
    <body class="cyan" style="margin-top:0px;margin-left:0px;margin-right:0px;margin-bottom:0px">
    <p align="justify" style="font-family:Arial;font-size:15px;color:#FFFFAA;font-weight:bold;margin-top:20px;margin-left:20px;margin-right:20px;">
    Компания ЛВА приступает к развертыванию облачной платформы "ЛВА Управление". Предлагаем Вам попробовать предварительную версию одной из программ этой системы. 
    Она предназначена для оперативного управления личными делами и делами организации. Нам очень интересно Ваше мнение о ней, чего в ней не хватает, как ее улучшить или расширить, 
    чтобы она была полезной именно для Вас.
    </p>
    
    <p align="justify" style="font-family:Arial;font-size:15px;color:#FFFFAA;font-weight:bold;margin-top:20px;margin-left:20px;margin-right:20px;">
        Для того чтобы приступить к использованию программы, заполните пожалуйста небольшую анкету и по указанному E-Mail мы пришлем Вам реквизиты доступа к программе. Для подключения 
        к ней будет использоваться указанный Вами логин и пароль.</p>		    
    	<?php 
    		$users = array("alex");
    		$error_text = "";
    		if (@$_POST["sendBtn"]=="Отправить") {
    			if (trim(@$_POST["surname"])=="") {
    				$error_text .= "Укажите фамилию</br>";
    			}
    			if (trim(@$_POST["name"])=="") {
    				$error_text .= "Укажите имя</br>";
    			}
    			if (trim(@$_POST["fathername"])=="") {
    				$error_text .= "Укажите отчество</br>";
    			}
    			if (trim(@$_POST["email"])=="") {
    				$error_text .= "Укажите E-Mail</br>";
    			}
    			if (trim(@$_POST["email"])!=trim(@$_POST["email2"])) {
    				$error_text .= "Введенные адреса E-Mail не совпадают</br>";
    			}
    			if (trim(@$_POST["password"])=="") {
    				$error_text .= "Укажите пароль</br>";
    			}
    			if (trim(@$_POST["password"])!=trim(@$_POST["password2"])) {
    				$error_text .= "Введенные пароли не совпадают</br>";
    			}
    			if (trim(@$_POST["captcha"])!=$_SESSION['captcha_keystring']) {
    				$error_text .= "Контрольная фраза введена не верно</br>";
    			}
    			if (array_search(trim(@$_POST["login"]), $users)!==FALSE)
    				$error_text .= "Пользователь с указанным логином уже зарегистрирован</br>";
    		}
    	$POST = array();
    	foreach ($_POST as $key=>$value) {
    		$POST[$key] = strip_tags(trim($value));
    	}
    	if (@$POST["sendBtn"]!="Отправить" or $error_text!="") {
    	?>
    	<div align="center">
    	<form action="reg.php" method="post">    		
    		<table cellpadding="3" cellspacing="1" bgcolor="#FFFFFF" width="50%" style="border-width:2px;border-color:#222222;border-style:solid">
    			<tr valign="top" class="window_header">
    				<td colspan="2">
    					<span id="headerText" style="align:center">АНКЕТА</span>
    				</td>
    			</tr>
    			<?php 
    		if ($error_text!="") { ?>
    			<tr valign="top">
    				<td nowrap="true" class="inner">
    					<p align="justify" style="font-family:Arial;font-size:15px;color:#660000;font-weight:bold">ОШИБКИ:</p>
    				</td>
    				<td width="100%" class="inner">
    					<p align="justify" style="font-family:Arial;font-size:15px;color:#FF0000;font-weight:bold">    
					    <?php 
					    	echo $error_text;
					    ?>
			    	</td>
			    </tr>    
			    <?php 
    		}
			    ?>
    			<tr valign="top">
    				<td nowrap="true" class="inner">
    					Фамилия
    				</td>
    				<td width="100%" class="cell">
    					<input type="text" value="<?=@$POST["surname"]?>" name="surname" class="wide"/>
    				</td>
    			</tr>
    			<tr valign="top" nowrap="true">
    				<td class="inner">
    					Имя
    				</td>
    				<td class="cell">
    					<input type="text" value="<?=@$POST["name"]?>" name="name" class="wide"/>
    				</td>    			
    			</tr>
    			<tr valign="top">
    				<td class="inner" nowrap="true">
    					Отчество
    				</td>
    				<td class="cell">
    					<input type="text" value="<?=@$POST["fathername"]?>" name="fathername" class="wide"/>
    				</td>    			
    			</tr>
    			<tr valign="top">
    				<td class="inner" nowrap="true">
    					E-Mail
    				</td>
    				<td class="cell">
    					<input type="text" value="<?=@$POST["email"]?>" name="email" class="wide"/>
    				</td>    			
    			</tr>
    			<tr valign="top">
    				<td class="inner" nowrap="true">
    					E-Mail еще раз
    				</td>
    				<td class="cell">
    					<input type="text" value="<?=@$POST["email2"]?>" name="email2" class="wide"/>
    				</td>    			
    			</tr>
    			<tr valign="top">
    				<td class="inner" nowrap="true">
    					Логин
    				</td>
    				<td class="cell">
    					<input type="text" value="<?=@$POST["login"]?>" name="login" class="wide"/>
    				</td>    			
    			</tr>
    			<tr valign="top">
    				<td class="inner" nowrap="true">
    					Пароль
    				</td>
    				<td class="cell">
    					<input type="password" value="<?=@$POST["password"]?>" name="password" class="wide"/>
    				</td>    			
    			</tr>
    			<tr valign="top">
    				<td class="inner" nowrap="true">
    					Пароль еще раз
    				</td>
    				<td class="cell">
    					<input type="password" name="password2" value="<?=@$POST["password2"]?>" class="wide"/>
    				</td>    			
    			</tr>
    			<tr valign="top">
    				<td class="inner" colspan="2">
    					Контрольная фраза
    				</td>
    			</tr>
    			<tr valign="top">
    				<td class="inner" nowrap="true">
    					<img src="kcaptcha/index.php?<?php echo session_name()?>=<?php echo session_id()?>">
    				</td>
    				<td class="cell">
    					<input type="captcha" name="captcha" value="<?=@$POST["captcha"]?>" class="wide"/>
    				</td>    			
    			</tr>
    			<tr valign="top">
    				<td colspan="2" class="cell">
    					<div align="right"><input type="submit" name="sendBtn" value="Отправить"/></div>    					
    				</td>
    			</tr>
    		</table>
    	</form>
    	</div>
<?php
    	} else {
    		$to = "andrey@it-port.ru";
    		$headers = "From: Register\n";    			
    		$headers.= "MIME-Version: 1.0\n";
    		$headers.= "Content-type: text/html; charset=utf-8\n";
    		$subject = "Запрос на регистрацию в системе";
    		$message  = "Фамилия: ".@$POST["surname"]."<br/>";
    		$message .= "Имя: ".@$POST["name"]."<br/>";
    		$message .= "Отчество: ".@$POST["fathername"]."<br/>";
    		$message .= "E-Mail: ".@$POST["email"]."<br/>";
    		$message .= "Логин: ".@$POST["login"]."<br/>";
    		$message .= "Пароль: ".@$POST["password"];    		
    		mail($to,$subject,$message,$headers);    		
?>			<div align="center">
    		<table cellpadding="3" cellspacing="1" bgcolor="#FFFFFF" width="50%" style="border-width:2px;border-color:#222222;border-style:solid">
    			<tr valign="top" class="window_header">
    				<td>
    					<span id="headerText" align="center">РЕГИСТРАЦИЯ</span>
    				</td>
    			</tr>
    			<tr valign="top">
    				<td class="inner">
    					<div align="center">
    						БОЛЬШОЕ СПАСИБО ! Информация доставлена. Ожидайте ответа по E-Mail. Он поступит в течение дня.
    					</div>
    				</td>
    			</tr>
    		</table>
    		</div>
<?php     		
    	}
?>
	<br/>
    <p align="center" style="font-family:Arial;font-size:13px;color:#FFFFFF;font-weight:bold;margin-top:20px;margin-left:20px;margin-right:20px;">
    	&copy; 2003-2013 ООО "ЛВА". Все права защищены. <br/><a href="http://www.lvacompany.ru"><font color="#FFFFFF">www.lvacompany.ru</font></a>
	</body>
	</html>
