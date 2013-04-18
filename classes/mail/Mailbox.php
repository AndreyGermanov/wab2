<?php
/**
 * Description of Mailbox
 *
 * @author Андрей Германов
 * @version 1.0
 *
 * Содержит свойства и методы для почтового ящика Postifx и Dovecot. Почтовые ящики для SMTP
 * описаны в файле Application->postfixMailboxTable в следующем формате:
 *
 * ящик путь-на-диске
 *
 * Также, адрес почтового ящика одновременно является именем пользователя для сервера Dovecot.
 * Информация о пользователях Dovectot хранится в файле Application->dovecotUsersTable в следующем формате:
 *
 * ящик:пароль:UID:GID::путь-к-ящику:/bin/false
 *
 * В качестве UID и GID берется одно и то же значение из переменных Application->mailUID и Application->mailGID
 * Это реквизиты пользователя Unix, имеющего доступ к каталогу с почтой (один пользователь Unix, от имени
 * которого запускается и Dovecot и Postfix.
 *
 *
 * Этот класс позволяет добавлять ящики в эти файлы и получать ящики из них
 *
 * Для этого класс оперирует следующими полями:
 *
 * name - имя ящика
 * domain - домен, в котором находится ящик
 * password - пароль для доступа к ящику
 *
 * Также у класса есть дополнительные поля
 *
 * returnAddress - обратный адрес. Если пользователь отправляет почту из этого ящика в Интернет, то в
 * качестве обратного адреса подставляется именно этот адрес. Эти адреса хранятся в файле
 * Application->postfixGenericTable в следующем формате:
 *
 * ящик обратный-адрес
 *
 * Этот класс работает и с этим файлом тоже.
 *
 * Основные его процедуры это load() и save(), которые читают информацию о ящики из этих файлов и сохраняют
 * в них.
 *
 * Поле loaded указывает, была ли инофрмация о ящике уже загружена
 *
 * Класс содержит ряд вспомогательных функций.
 *
 * getArgs() - возвращает массив аргументов, который используется для подстановки в шаблонах. Ключи массива это
 * фраза в шаблоне, например {name}, а значения массива это то, на что будут заменяться фразы в шаблоне.
 */

class Mailbox extends WABEntity {

    /**
     * Конструктор почтового ящика. Напрямую не вызывается. Вызывается только с помощью Objects->getObject()
     * 
     * @param <массив> $params - массив параметров для создания ящика, params[0] - имя, params[1] - домен
     */
    function  construct($params) {

        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();

        $this->module_id = array_shift($params);
        $this->module_id.= "_".array_shift($params);
        $app = $Objects->get("MailApplication_".$this->module_id);
        $appl = $Objects->get("Application");
        if (!$appl->initiated)
                $appl->initModules();
        $dom = array_pop($params);
        $nam = implode("_",$params);
        if ($app->hasMailDomain($dom))
        {           
            $this->fields["name"] = $nam;
            $this->fields["domain"] = $dom;
            $this->password = "";
            $this->returnAddress = "";
            $this->template="templates/mail/Mailbox.html";
            $this->handler = "scripts/handlers/mail/Mailbox.js";
            $this->css = $appl->skinPath."styles/Mailbox.css";
            $this->icon = $appl->skinPath."images/Window/mail.gif";
            $this->skinPath = $appl->skinPath;
            $this->width = "680";
            $this->height = "440";
            $this->overrided = "width,height";
            $this->tabset_id = "WebItemTabset_".$this->module_id."_".$this->name.$this->domain."Mailbox";
            $this->overrided = "width,height";

            $this->tabs_string = "options|Параметры|".$this->skinPath."images/spacer.gif;";
            $this->tabs_string.= "autoreply|Автоответчик|".$this->skinPath."images/spacer.gif";
            $this->autoreply_text = "";
            $this->autoreply_enabled=false;            
            $this->active_tab = "options";
            
        }
        else
        {
            unset($this);
            return false;
        }
        $this->clientClass = "Mailbox";
		$this->parentClientClasses = "Entity";
	    $this->classTitle = "Ящик электронной почты";
	    $this->classListTitle = "Ящики электронной почты";
    }

    /**
     * Загружает информацию о почтовом ящике из файлов
     *
     */
    function load()
    {
        global $Objects;

        // Обращаемся к объекту приложения для чтения информации о расположении файлов
        $app = $Objects->get("MailApplication_".$this->module_id);
        $app->load();
        $this->spambox = $app->spambox;

        // Проверяем, есть ли информация о ящике в таблице виртуальных пользователей Postfix
        $fp = fopen($app->remotePath.$app->postfixMailboxTable,"r");
        $found = false;
        while ($line=fgets($fp))
        {
            $arr = explode(" ",$line);
            if ($arr[0] == $this->name."@".$this->domain)
            {
                $this->loaded_name = $this->name."@".$this->domain;
                $found = true;
                break;
            }
        }
        fclose($fp);

        // Если есть, пытаемся получить пароль пользователя из таблицы пользователей Dovecot
        if ($found)
        {
            $fp = fopen($app->remotePath.$app->dovecotUsersTable,"r");
            while ($line=fgets($fp))
            {
                $arr = explode(":",$line);
                if ($arr[0]== $this->name."@".$this->domain)
                {
                    $found = true;
                    $this->password = $arr[1];
                    break;
                }
            }
            if ($this->password=="")
                    $found = false;
        }

        // Если пользователь есть в обоих предыдущих таблицах, пробуем получить обратный адрес
        // из таблицы обратных адресов (Generic)
        if ($found)
        {
            $fp = fopen($app->remotePath.$app->postfixGenericTable,"r");
            while ($line=fgets($fp))
            {
                $arr = explode(" ",$line);

                if ($arr[0]==$this->name."@".$this->domain)
                {
                    if (isset($arr[1]))
                        $this->returnAddress = str_replace("\n","",trim($arr[1]));
                }
            }
        }

        // Если пользователь существуем, попробуем получить адрес ящика для спама
        // для этого пользователя
        if ($found) {
            $strings = file($app->remotePath.$app->MailScannerRulesTable);
            for ($counter=0;$counter<count($strings);$counter++) {
                if (trim($strings[$counter])=="")
                    continue;
                $string = explode(" ",$strings[$counter]);
                if (trim($string[0]." ".$string[1])=="To: ".trim($this->name."@".$this->domain)) {
                    if (trim($string[3])!=$this->name."@".$this->domain)
                        $this->spambox = trim($string[3]);
                    else
                        $this->spambox = "";
                    break;
                }
            }
        }
        
        // Подгружаем текст автоответчика, если есть
        if (file_exists($app->remotePath.$app->autoreplyTextsPath."/".$this->name."@".$this->domain))
            $this->autoreply_text = file_get_contents($app->remotePath.$app->autoreplyTextsPath."/".$this->name."@".$this->domain);

        // Проверяем, включен ли автоответчик
        if (file_exists($app->remotePath.$app->autoreplyAliasesFile)) {
            $strings = file($app->remotePath.$app->autoreplyAliasesFile);
            foreach ($strings as $line) {
                $parts = explode(" ",$line);
                if (trim($parts[0])==$this->name."@".$this->domain) {
                    $this->autoreply_enabled = true;
                    break;
                }
            }
        }        
        
        // Устанавливаем признак того, что загрузка была выполнена
        if ($found)
            $this->loaded = true;
    }
    
    function getAutoreplyOptions() {
        global $Objects;        
        $app = $Objects->get($this->module_id);
        
        // Подгружаем текст автоответчика, если есть
        if (file_exists($app->remotePath.$app->autoreplyTextsPath.$this->name."@".$this->domain))
            $this->autoreply_text = file_get_contents($app->remotePath.$app->autoreplyTextsPath.$this->name."@".$this->domain);

        // Проверяем, включен ли автоответчик
        if (file_exists($app->remotePath.$app->autoreplyAliasesFile)) {
            $strings = file($app->remotePath.$app->autoreplyAliasesFile);
            foreach ($strings as $line) {
                $parts = explode(" ",$line);
                if (trim($parts[0])==$this->name."@".$this->domain) {
                    $this->autoreply_enabled = true;
                    break;
                }
            }
        }        
    }

    /**
     * Сохраняет информацию о почтовых ящиках в файлы postfixMailboxTable, dovecotUsersTable и
     * postfixGenericTable.
     *
     * @global <массив> $Objects - коллекция загруженных объектов
     * @return ничего
     */
    function save($arguments=null) {
        // Получаем данные из объекта приложения
        global $Objects;
        if (isset($arguments)) {
        	$this->load();
        	$this->setArguments($arguments);   
        	if (trim($arguments["password"])!="")     	
        		$this->password = crypt(trim($arguments["password"]));
        	if (trim($arguments["password1"])!="")     	
        		$this->password1 = crypt(trim($arguments["password1"]));        	 
        }
        $app = $Objects->get("MailApplication_".$this->module_id);
        if (!$app->loaded)
            $app->load();
        $mailboxes = $Objects->get("Mailboxes_".$this->module_id);
        // Если имя домена изменено на такое, которого не существует, то
        // сообщаем об ошибке и выходим
        if (!$app->hasMailDomain($this->domain))
        {
            echo json_encode(array("error" => "Указанный домен не обслуживается !"));
            return 0;
        }

        // Если такой почтовый ящик уже существует, выдаем ошибку
        if ($this->loaded_name != trim($this->name."@".$this->domain))
        { 
            if ($mailboxes->contains($this->name,$this->domain))
            {   
                if ($Objects->get("Mailbox_".$this->module_id."_".$this->name."_".$this->domain)->loaded)
                { 
                    echo json_encode(array("error" => $this->name."@".$this->domain.": почтовый ящик с таким именем уже существует !"));
                    return 0;
                }
            }
        }        
        
        // Получаем имя, которое будем искать в файлах. Это либо текущее имя,
        // либо имя, под которым этот ящик был загружен из файла
        $loaded_name = $this->loaded_name;
        if ($this->loaded_name=="")
                $this->loaded_name = $this->name."@".$this->domain;

        // Проверяем существование указанного ящика для спама
        $spam_parts = explode("@",$this->spambox);
        if ($this->spambox!="" and !$mailboxes->contains($spam_parts[0],@$spam_parts[1])) {
            $this->reportError("Почтовый ящик для спама ".$this->spambox." не существует !","save");
        }

        // Записываем ящик в таблицу ящиков Postfix;
        $strings = file($app->remotePath.$app->postfixMailboxTable);
        $found = false;
        for ($counter=0;$counter<count($strings);$counter++)
        {
            $arr = explode(" ",$strings[$counter]);
            if ($arr[0]==$this->loaded_name)
            {
                $strings[$counter] = $this->name."@".$this->domain." ".$this->domain."/".$this->name."@".$this->domain."/";
                $found = true;
                break;
            }
            else
                $strings[$counter] = str_replace("\n","",$strings[$counter]);
        }
        if (!$found)
            $strings[count($strings)] = $this->name."@".$this->domain." ".$this->domain."/".$this->name."@".$this->domain."/";

        $fp = fopen($app->remotePath.$app->postfixMailboxTable,"w");
        for ($counter=0;$counter<count($strings);$counter++)
        {
            if (trim($strings[$counter]!="") and trim($strings[$counter]!="\n"))
                fwrite($fp,$strings[$counter]."\n");
        }
        fclose($fp);

        // Записываем ящик в таблицу ящиков Dovecot;
        $strings = array();
        $strings = file($app->remotePath.$app->dovecotUsersTable);
        $found = false;
        for ($counter=0;$counter<count($strings);$counter++)
        {
            $arr = explode(":",$strings[$counter]);
            if ($arr[0]==$this->loaded_name)
            {
            	if ($this->password!="")
                	$strings[$counter] = $this->name."@".$this->domain.":".$this->password.":".$app->mailUID.":".$app->mailGID."::".$app->mailBasePath.$this->domain."/".$this->name."@".$this->domain."/:/bin/false";
            	else
            		$strings[$counter] = $this->name."@".$this->domain.":".$arr[1].":".$app->mailUID.":".$app->mailGID."::".$app->mailBasePath.$this->domain."/".$this->name."@".$this->domain."/:/bin/false";
                $found = true;
                break;
            }
            else
                $strings[$counter] = str_replace("\n","",$strings[$counter]);
        }
        if (!$found)
            $strings[count($strings)] = $this->name."@".$this->domain.":".$this->password.":".$app->mailUID.":".$app->mailGID."::".$app->mailBasePath.$this->domain."/".$this->name."@".$this->domain."/:/bin/false";

        $fp = fopen($app->remotePath.$app->dovecotUsersTable,"w");
        for ($counter=0;$counter<count($strings);$counter++)
            if (trim($strings[$counter]!="") and trim($strings[$counter]!="\n"))
                fwrite($fp,$strings[$counter]."\n");
        fclose($fp);

        // Записываем данные о нем в таблицу обратных адресов Postfix
        $strings = array();
        $strings = file($app->remotePath.$app->postfixGenericTable);
        $found = false;
        for ($counter=0;$counter<count($strings);$counter++)
        {
            $arr = explode(" ",$strings[$counter]);
            if ($arr[0]==$this->loaded_name)
            {
                $strings[$counter] = $this->name."@".$this->domain." ".$this->returnAddress;
                $found = true;
                break;
            }
            else
                $strings[$counter] = str_replace("\n","",$strings[$counter]);
        }
        if (!$found)
            $strings[count($strings)] = $this->name."@".$this->domain." ".$this->returnAddress;

        $fp = fopen($app->remotePath.$app->postfixGenericTable,"w");
        for ($counter=0;$counter<count($strings);$counter++)
            if (trim($strings[$counter]!="") and trim($strings[$counter]!="\n"))
                fwrite($fp,$strings[$counter]."\n");
        fclose($fp);

        // Записываем информацию о почтовом ящике для спама
        $strings = array();
        $found_spambox = false;
        $strings = file($app->remotePath.$app->MailScannerRulesTable);
        $fp = fopen($app->remotePath.$app->MailScannerRulesTable,"w");
        if ($this->spambox == "")
            $spam_box = $this->name."@".$this->domain;
        else
            $spam_box = $this->spambox;
        for ($counter=0;$counter<count($strings);$counter++) {
            if (trim($strings[$counter])=="")
                continue;
            $string = explode(" ",trim($strings[$counter]));
            if (trim($string[1])==trim($this->loaded_name)) {
                $found_spambox = true;
                if ($this->spambox!=$app->spambox) {
                    fwrite($fp,'To: '.$this->name.'@'.$this->domain.' forward '.$spam_box.' header "X-Spam-Status: yes"'."\n");
                }
            }
            else
                fwrite($fp,str_replace("\n","",$strings[$counter]."\n")."\n");
        }
        if (!$found_spambox)
            if ($this->spambox!=$app->spambox) 
                fwrite($fp,'To: '.$this->name.'@'.$this->domain.' forward '.$spam_box.' header "X-Spam-Status: yes"'."\n");

        fclose($fp);
        
        // Записываем объект в глобальный кэш объектов. Если мы изменили имя объекта или домен,
        // то предварительно удаляем из кэша запись с прошлым именем или доменом.        
        if ($this->loaded_name!=$this->name."@".$this->domain)
        {
            $arr = explode("@",$this->loaded_name);
            $Objects->remove("Mailbox_".$arr[0]."_".$arr[1]);
            // Перемещаем почту пользователя в новый каталог, соответствующий его новому имени
            shell_exec($app->remoteSSHCommand.' "mkdir -p '.$app->mailBasePath.$this->domain.'"');
            shell_exec($app->remoteSSHCommand.' "chown -R postfix:postfix '.$app->mailBasePath.$this->domain.'"');
            shell_exec($app->remoteSSHCommand.' mv '.$app->mailBasePath.$arr[1]."/".$this->loaded_name." ".$app->mailBasePath.$this->domain."/".$this->name."@".$this->domain);
            // Если после этого каталог домена оказался пустым, удаляем его
            shell_exec($app->remoteSSHCommand.' rmdir '.$app->mailBasePath.$arr[1]);
            // Записываем объект в кэш
            $Objects->set("Mailbox_".$this->name."_".$this->domain,$this);
        }

        shell_exec($app->remoteSSHCommand.' "mv '.$app->autoreplyTextsPath."/".$this->loaded_name.' '.$app->autoreplyTextsPath."/".$this->name."\@".$this->domain.'"');
        //echo $app->remoteSSHCommand.' "mv '.$app->autoreplyTextsPath.$this->loaded_name.' '.$app->autoreplyTextsPath.$this->name."\@".$this->domain.'"';
        if (file_exists($app->remotePath.$app->autoreplyAliasesFile)) {
            $strings = array();
            $strings = file($app->remotePath.$app->autoreplyAliasesFile);
            $found = false;
            for ($counter=0;$counter<count($strings);$counter++)
            {
                $arr = explode(" ",$strings[$counter]);
                if ($arr[0]==$this->loaded_name)
                {
                    if ($this->autoreply_enabled)
                        $strings[$counter] = $this->name."@".$this->domain." ".$this->name."@".$this->domain.",".$this->name."@".$this->domain."@".$app->autoreplyDomain;
                    else
                        $strings[$counter] = "";
                    $found = true;
                    break;
                }
                else
                    $strings[$counter] = str_replace("\n","",$strings[$counter]);
            }
            if (!$found and $this->autoreply_enabled)
                $strings[count($strings)] = $this->name."@".$this->domain." ".$this->name."@".$this->domain.",".$this->name."@".$this->domain."@".$app->autoreplyDomain;

            $fp = fopen($app->remotePath.$app->autoreplyAliasesFile,"w");
            for ($counter=0;$counter<count($strings);$counter++)
                if (trim($strings[$counter]!="") and trim($strings[$counter]!="\n"))
                    fwrite($fp,$strings[$counter]."\n");
            fclose($fp);            
        }
        
        if ($this->autoreply_text!="")
            file_put_contents($app->remotePath.$app->autoreplyTextsPath."/".$this->name."@".$this->domain,str_replace("#|#X#-","'",$this->autoreply_text));
        else
            shell_exec($app->remoteSSHCommand.' "rm '.$app->autoreplyTextsPath."/".$this->name."\@".$this->domain.'"');
            
        // Если нет каталога, в котором хранится почта пользователя, устанавливаем его
        if (!file_exists($app->remotePath.$app->mailBasePath.$this->domain."/".$this->name."@".$this->domain)) {
                shell_exec($app->remoteSSHCommand.' "mkdir -p '.$app->mailBasePath."/".$this->domain."/".$this->name."@".$this->domain.'"');
                shell_exec($app->remoteSSHCommand.' "chown -R postfix:postfix '.$app->mailBasePath."/".$this->domain."/".$this->name."@".$this->domain.'"');
        }

        // Если у этого ящика есть подчиненные почтовые ящики Интернет, то обновляем в них информацию о владельце,
        // если у него изменилось имя или домен
        if ($this->loaded_name != trim($this->name."@".$this->domain))
        {
            $remote_mailboxes = $Objects->get("RemoteMailboxes_".$this->module_id);
            $owned_mailboxes=$remote_mailboxes->getByOwner($this->loaded_name);
            foreach($owned_mailboxes as $value)
            {
                $value->load();
                $value->owner = $this->name."@".$this->domain;
                $value->save();
            }
        }

        // Устанавливаем признак того, что объект был загружен, т.е. признак того что объект соответствует
        // тому, что записано о нем в файлах. Также устанавливаем что текущее имя соответствует загруженному,
        // так как только что оно было записано
        $this->loaded = true;
        $shell = $Objects->get("Shell_shell");
        $shell->exec_command($app->remoteSSHCommand." ".$app->postMailboxTableCommand);
        $shell->exec_command($app->remoteSSHCommand." ".$app->postGenericTableCommand);
        $shell->exec_command($app->remoteSSHCommand." ".$app->restartMailScannerCommand);
        $shell->exec_command($app->remoteSSHCommand." ".$app->postmapCommand." ".$app->autoreplyAliasesFile);
        $app = $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
        if ($loaded_name=="")
        	$app->raiseRemoteEvent("MAILBOX_ADDED","object_id=".$this->getId());
        else
        	$app->raiseRemoteEvent("MAILBOX_CHANGED","object_id=".$this->getId());
        $this->loaded_name = $this->name."@".$this->domain;
    }

    /**
     * Функция возвращает массив подстановок для генератора шаблонов.
     * @global  $Objects - массив загруженных объектов
     * @return <массив>
     */
    function getArgs()
    {
        global $Objects;
        $app = $Objects->get("MailApplication_".$this->module_id);
        $result = parent::getArgs();
        $result['{name}'] = $this->name;
        $result['{domain}'] = $this->domain;
        return array_merge($app->getArgs(),$result);
    }

    /**
     *
     * Функция-обработчик изменения значений свойств объекта.
     *
     * @global  $Objects массив загруженных объектов
     * @param <строка> $name имя свойства
     * @param <любой> $value значение свойства
     */
    function __set($name,$value)
    {
        global $Objects;
        // Заносим значение свойства в массив fields.
        $this->fields[$name] = trim($value);
    }

    /**
     *
     * Возвращает глобальный уникальный идентификатор объекта
     * 
     * @return <строка>
     */
    function getId()
    {
        return "Mailbox_".$this->module_id."_".$this->name."_".$this->domain;
    }

    /**
     *
     * Возвращает текстовое представление объекта
     *
     * @return <строка>
     */
    function getPresentation()
    {
        return $this->name."@".$this->domain;
    }
    
    function getHookProc($number) {
    	switch ($number) {
    		case '3': return "save";
    	}
    	return parent::getHookProc($number);
    }
}
?>