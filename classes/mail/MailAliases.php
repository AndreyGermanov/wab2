<?php
/**
 * Данный класс управляет списком почтовых псевдонимов класса MailAlias. Он позволяет
 * загружать их из файла Application->postfixAliasesTable и сохранять в этот файл,
 * добавлять новые псевдонимы в список и удалять псевдонимы из списка и из файла.
 * Также класс позволяет узнать, существует ли указанный псевдоним в списке.
 *
 * Список псевдонимов хранится в массиве $mail_aliases. Массив является динамическим,
 * он формируется из списка объектов $Objects->query("MailAlias").
 *
 * Для выполнения своих задач, класс использует следующие методы:
 *
 * load() - загрузка списка псевдонимов из файла
 * save() - запись списка псевдонимов в файл
 * contains() - проверяет, содержится ли указанный псевдоним в списке
 * add() - добавляет указанный псевдоним в список
 * remove() - удаляет указанный псевдоним из списка и из файла
 * 
 */

class MailAliases extends WABEntity {
    function construct($params) {
        $this->module_id = $params[0]."_".$params[1];
        $this->loaded = false;
        $this->clientClass = "MailAliases";
        $this->parentClientClasses = "Entity";        
    }

    function load() {
        global $Objects;
        $app = $Objects->get("MailApplication_".$this->module_id);
        if (!$app->loaded)
                $app->load();
        
        $strings = file($app->remotePath.$app->postfixAliasesTable);
        for ($counter=0;$counter<count($strings);$counter++) {
            $line = trim($strings[$counter]);            
            $line = explode(" ",$line);
            $line_parts = explode("@",$line[0]);
            $load = true;
            if ($Objects->contains("MailAlias_".$this->module_id."_".$line_parts[0]."_".$line_parts[1])) {
                $alias = $Objects->get("MailAlias_".$this->module_id."_".$line_parts[0]."_".$line_parts[1]);
                if ($alias->loaded)
                        $load = false;
            }
            if ($load)
            {
                $alias = $Objects->get("MailAlias_".$this->module_id."_".$line_parts[0]."_".$line_parts[1]);
                if (!isset($line[1]))
                    $alias->addresses = array();
                else
                    $alias->addresses = explode(",",$line[1]);
                $alias->spambox = $app->spambox;
                $alias->loaded = true;
                $alias->loaded_name = $alias->presentation;
            }
        }

        $strings = file($app->remotePath.$app->MailScannerRulesTable);
        for ($counter=0;$counter<count($strings);$counter++) {
            if (trim($strings[$counter])=="")
                continue;
            $string = explode(" ",$strings[$counter]);
            $arr = explode("@",trim($string[1]));
            if ($Objects->contains("MailAlias_".$this->module_id."_".$arr[0]."_".@$arr[1])) {
                $mail_alias = $Objects->get("MailAlias_".$this->module_id."_".$arr[0]."_".@$arr[1]);
                if (!$mail_alias->loaded)
                    if (trim($strings[3])!=$mail_alias->name."@".$mail_alias->domain)
                        $mail_alias->spambox = trim($string[3]);
                    else
                        $mail_alias->spambox = "";
            }
        }
        $this->loaded = true;
    }

    function save() {
        global $Objects;
        $app = $Objects->get("MailApplication_".$this->module_id);
        $fp = fopen($app->remotePath.$app->postfixAliasesTable,"w");
        foreach ($this->mail_aliases as $value) {
            fwrite($fp,$value->presentation." ".implode(",",$value->addresses."\n"));
            $value->loaded = true;
            $value->loaded_name = $value->presentation;
        }
        $this->loaded = true;
        $shell = $Objects->get("Shell_Helix");
        $shell->exec_command($app->remoteSSHCommand." postmap ".$app->postfixAliasesTable);
        $shell->exec_command($app->remoteSSHCommand." postfix reload");
    }

    function contains($name,$domain) {
        global $Objects;
        if (!$this->loaded)
            $this->load();
        return $Objects->contains("MailAlias_".$this->module_id."_".$name."_".$domain);
    }

    function add($name,$domain) {
        global $Objects;
        if ($this->contains($name,$domain)) {
            $this->reportError("Адрес ".$name."@".$domain." уже существует !","add");
            return 0;
        }

        $mailboxes = $Objects->get("Mailboxes_".$this->module_id);
        if ($mailboxes->contains($name,$domain)) {
            $this->reportError("Адрес ".$name."@".$domain." уже существует !","add");
            return 0;
        }
        return $Objects->get("MailAlias_".$this->module_id."_".$name."_".$domain);
    }

    function remove($name,$domain) {

        // Если такого псевдонима нет, просто выходим
        if (!$this->contains($name,$domain)) {
            $this->reportError("Адрес ".$name."@".$domain." не существует !");
            return 0;
        }

        global $Objects;
		
        $app = $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
        $app->raiseRemoteEvent("MAILALIAS_DELETED","object_id="."MailAlias_".$this->module_id."_".$name."_".$domain);
        // Удаляем все подчиненные этому псевдониму почтовые ящики Интернет
        $remote_mailboxes = $Objects->get("RemoteMailboxes_".$this->module_id);
        $remote_mailboxes->load();
        $owned_mailboxes = $remote_mailboxes->getByOwner($name."@".$domain);
        foreach($owned_mailboxes as $value) {
            $remote_mailboxes->remove($value->name);
        }

        // Удаляем псевдоним из файла (перезаписываем содержимое файла, пропустив
        // строку с этим псевдонимом
        $app = $Objects->get("MailApplication_".$this->module_id);
        $strings = file($app->remotePath.$app->postfixAliasesTable);
        $fp = fopen($app->remotePath.$app->postfixAliasesTable,"w");
        for ($counter=0;$counter<count($strings);$counter++) {
            $line = trim($strings[$counter]);
            $line = explode(" ",$line);
            if ($line[0]==$name."@".$domain)
                continue;
            fwrite($fp,trim(str_replace("\n","",$strings[$counter]))."\n");
        }

        $strings = file($app->remotePath.$app->MailScannerRulesTable);
        $fp = fopen($app->remotePath.$app->MailScannerRulesTable,"w");
        foreach($strings as $line) {
            $arr = explode(" ",$line);
            if (trim($arr[1])!=$name."@".$domain)
                fwrite($fp,trim(str_replace("\n","",$line))."\n");
        }
        fclose($fp);

        // Удаляем псевдоним из глобального кэша объектов
        $Objects->remove("MailAlias_".$this->module_id."_".$name."_".$domain);
        $shell = $Objects->get("Shell_Helix");
        $shell->exec_command($app->remoteSSHCommand." postmap ".$app->postfixAliasesTable);
        $shell->exec_command($app->remoteSSHCommand." postfix reload");
}    

    function __get($name) {
        global $Objects;
        switch ($name) {
            case "mail_aliases":
                return $Objects->query("MailAlias");
                break;
            default:
                if (isset($this->fields[$name]))
                        return $this->fields[$name];
                else
                    return "";
        }
    }
    
    function getHookProc($number) {
    	switch($number) {
    		case '3': return "removeHook";
    	}
    	return parent::getHookProc($number);
    }
    
    function removeHook($arguments) {
    	$this->load();
    	$this->remove($arguments["name"],$arguments["domain"]);
    }
}
?>