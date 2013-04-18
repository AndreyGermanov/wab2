<?php
/**
 * Класс управляет списком рассылки, или, как его технически называют - почтовым псевдонимом.
 *
 * Почтовый псевдоним это адрес, который обязательно должен входить в один из существующих доменов.
 * Однако этому адресу не соответствует ни один почтовый ящик. Вся почта, поступающая на этот
 * адрес просто пересылается на другие адреса, которые указаны в списке, который привязан к этому
 * адресу.
 *
 * Списки рассылки хранятся в файле Application->postfixAliasesTable, который по умолчанию находится
 * в /etc/postfix/virtual_aliases. Формат одного списка рассылки такой:
 *
 * адрес-списка-рассылки адрес,адрес,адрес
 *
 * Данный класс позволяет загружать списки рассылки из этого файла, сохранять их в этот файл. Список
 * хранится в массиве addresses. Класс позволяет добавлять адреса в этот список, удалять их из него
 * и изменять их, а также проверять, существуют ли они. Для выполнения этих операций в классе содержатся
 * методы:
 *
 * load() - загружает список рассылки из файла
 * save() - сохраняет список рассылки в файл
 * add() - добавляет новый адрес в список
 * change() - заменяет существующий адрес на другой
 * remove() - удаляет адрес из списка
 * contains() - проверяет, существует ли указанный адрес в списке
 * getId() - получает идентификатор списка рассылки
 * getPresentation() - получает строковое представление списка рассылки
 */
class MailAlias extends WABEntity{

    public $addresses = array();

    function construct($params) {
         global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
       $dom = array_pop($params);
        $this->module_id = array_shift($params);
        $this->module_id.= "_".array_shift($params);
        $this->fields["name"] = implode("_",$params);
        $this->fields["domain"] = $dom;
        $this->template = "templates/mail/MailAlias.html";
        $this->css = $app->skinPath."styles/Mailbox.css";
        $this->handler = "scripts/handlers/mail/Mailbox.js";
        $this->icon = $app->skinPath."images/Tree/maillist.gif";
        $this->skinPath = $app->skinPath;
        $this->loaded = false;
        $this->clientClass = "MailAlias";
        $this->parentClientClasses = "Entity";        
	    $this->classTitle = "Список рассылки";
	    $this->classListTitle = "Списки рассылки";
    }

    function load() {

        global $Objects;
        $app = $Objects->get("MailApplication_".$this->module_id);

        if (!$app->loaded)
            $app->load();

        $this->spambox = $app->spambox;

        $strings = file($app->remotePath.$app->postfixAliasesTable);
        for ($counter=0;$counter<count($strings);$counter++) {
            $line = trim($strings[$counter]);
            $line = explode(" ",$line);
            if ($line[0]==$this->name."@".$this->domain) {
                $this->loaded_name = $line[0];
                if (!isset($line[1]))
                    $this->addresses = array();
                else
                    $this->addresses = explode(",",$line[1]);
                $this->loaded = true;
                break;
            }
        }
        
        $strings = file($app->remotePath.$app->MailScannerRulesTable);
        for ($counter=0;$counter<count($strings);$counter++) {
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

    function save($arguments=null) {    	
    	if (isset($arguments)) {
    		$this->load();
    		$this->setArguments($arguments);
    	}
        if ($this->name == "")
        {
            $this->reportError("Укажите имя списка рассылки !", "save");
            return 0;
        }
        if ($this->domain == "")
        {
            $this->reportError("Укажите домен для списка рассылки !", "save");
            return 0;
        }

        global $Objects;
        $app = $Objects->get("MailApplication_".$this->module_id);

        if (!$app->loaded)
            $app->load();
        
        $mail_domains = $Objects->get("MailDomains_".$this->module_id);
        $mailboxes = $Objects->get("Mailboxes_".$this->module_id);

        if (!$mail_domains->contains($this->domain))
        {
            $this->reportError("Указанный домен не обслуживается !", "save");
            return 0;
        }
        if ($this->name."@".$this->domain != $this->loaded_name) {
            $mail_aliases = $Objects->get("MailAliases_".$this->module_id);

            if ($mailboxes->contains($this->name,$this->domain))
            {
                $this->reportError("Почтовый ящик с адресом ".$this->presentation." уже существует", "save");
                return 0;
            }
            if ($mail_aliases->contains($this->name,$this->domain))
            {
                $this->reportError("Список рассылки с адресом ".$this->presentation." уже существует", "save");
                return 0;
            }
            
            $remote_mailboxes = $Objects->get("RemoteMailboxes_".$this->module_id);
            $remote_mailboxes->load();
            $owned_mailboxes = $remote_mailboxes->getByOwner($this->loaded_name);
        }
        
        // Проверяем существование указанного ящика для спама
        $spam_parts = explode("@",$this->spambox);
        if ($this->spambox!="" and !$mailboxes->contains($spam_parts[0],@$spam_parts[1])) {
            $this->reportError("Почтовый ящик для спама ".$this->spambox." не существует !","save");
        }

        $target_line = $this->presentation." ".implode(",",$this->addresses);

        $found = false;
        
        $strings = file($app->remotePath.$app->postfixAliasesTable);
        $fp = fopen($app->remotePath.$app->postfixAliasesTable,"w");
        for ($counter=0;$counter<count($strings);$counter++) {
            $line = trim($strings[$counter]);
            $line = explode(" ",$line);
            if ($line[0] == $this->loaded_name) {
                $strings[$counter] = $target_line;
                $found = true;
            }
            else
              $strings[$counter] = str_replace("\n","",$strings[$counter]);
            fwrite($fp,$strings[$counter]."\n");
        }
        if (!$found)
            fwrite($fp,$target_line."\n");
        
        if ($this->name."@".$this->domain != $this->loaded_name) {
            foreach($owned_mailboxes as $value)
            {
                $value->owner = $this->presentation;
                $value->save();
            }
        }

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
        
        $app = $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
        if ($this->loaded_name=="")
        	$app->raiseRemoteEvent("MAILALIAS_ADDED","object_id=".$this->getId());
        else
        	$app->raiseRemoteEvent("MAILALIAS_CHANGED","object_id=".$this->getId());
        	
        $this->loaded_name = $this->presentation;
        $this->loaded = true;
        
        $shell = $Objects->get("Shell_Helix");
        $shell->exec_command($app->remoteSSHCommand." ".$app->postAliasesTableCommand);
        $shell->exec_command($app->remoteSSHCommand." ".$app->restartMailScannerCommand);
}

    function add($address) {
        if ($this->contains($address)) {
            $this->reportError("Указанный адрес уже есть в списке", "add");
            return 0;
        }
        $this->addresses[count($this->addresses)] = $address;
    }

    function remove($address) {    	
    	if (is_array($address)) {
    		$this->load();
    		$address = $address["address"];
    	}
        $result = array_search($address,$this->addresses);
        if (isset($this->addresses[$result]))
        {   
            unset($this->addresses[array_search($address,$this->addresses)]);
            global $Objects;
            $app = $Objects->get("Application");
            if (!$app->initiated)
            	$app->initModules();
            $app->raiseRemoteEvent("MAILALIAS_ADDRESS_DELETED","message=Пользователь удалил адресата `".$address."` из списка рассылки `".$this->presentation."`");
            $this->save();
        }
        else
          $this->reportError("Указанный адрес: ".$address." не существует !".implode("_",$this->addresses),"remove");
    }

    function change($old_address,$new_address) {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
    	if ($this->contains($new_address)) {
            $this->reportError("Указанный адрес уже есть в списке - ".$new_address, "change");
            return 0;
        }
        if (!$this->contains($old_address)) {
            $this->add($new_address);
            if ($old_address=="")
        		$app->raiseRemoteEvent("MAILALIAS_ADDRESS_ADDED","message=Пользователь добавил адресата `".$new_address."` в список рассылки `".$this->presentation."`");
            else
				$app->raiseRemoteEvent("MAILALIAS_ADDRESS_CHANGED","message=Пользователь изменил параметры адресата `".$new_address."` списка рассылки `".$this->presentation."`");				
            return 0;
        }   
        $this->addresses[array_search($old_address,$this->addresses)] = $new_address;
		$app->raiseRemoteEvent("MAILALIAS_ADDRESS_CHANGED","message=Пользователь изменил параметры адресата `".$new_address."` списка рассылки `".$this->presentation."`");				
    }

    function contains($address) {
        if (!$this->loaded)
                $this->load();
        $result = array_search($address,$this->addresses);
        if ($result == 0)
            return false;
        else
            return true;
    }

    function getId() {
        return "MailAlias_".$this->module_id."_".$this->name."_".$this->domain;
    }

    function getPresentation() {
        return $this->name."@".$this->domain;
    }

    function getArgs()
    {
        global $Objects;
        $app = $Objects->get("MailApplication_".$this->module_id);
        $result = parent::getArgs();
        $result['{name}'] = $this->name;
        $result['{domain}'] = $this->domain;

        $aliases = $Objects->get("MailAliases_".$this->module_id);
        $aliases->load();
        $arr = array();
        foreach ($aliases->mail_aliases as $value)
            $arr[count($arr)] = $value->name."@".$value->domain;
        $result["{aliases_list}"] = implode(",",$arr)."|".implode(",",$arr);
        return array_merge($app->getArgs(),$result);
    }
    
    function getHookProc($number) {
    	switch($number) {
    		case '3': return "save";
    		case '4': return "changeHook";
    		case '5': return "remove";
    	}
    	return parent::getHookProc($number);
    }
    
    function changeHook($arguments) {
    	$this->load();
    	$this->change($arguments["old_address"],$arguments["address"]);
    	$this->save();
    }
}
?>