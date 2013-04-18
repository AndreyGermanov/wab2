<?php

/**
 * Класс управляет отдельным почтовым доменом, позволяет сохранять его в файл
 * Application->postfixDomainsTable.
 *
 * Для управления доменом существуют функции:
 *
 * save() - сохраняет домен в файл
 * getId() - получает идентификатор домена
 * getPresentation() - получает представление домена
 */
class MailDomain extends WABEntity {

    function construct($params) {
        $this->module_id = array_shift($params);
        $this->module_id.= "_".array_shift($params);
        $this->name = implode("_",$params);
        $this->loaded = false;
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();

        $this->template ="templates/mail/MailDomain.html";
        $this->css = $app->skinPath."styles/Mailbox.css";
        $this->handler = "scripts/handlers/mail/Mailbox.js";
        $this->icon =$app->skinPath."images/Window/mail-domain.gif";
        $this->height = "185";
        $this->overrided = "height";
        $this->skinPath = $app->skinPath;
    	$this->clientClass = "MailDomain";
		$this->parentClientClasses = "Entity";
	    $this->classTitle = "Почтовый домен";
	    $this->classListTitle = "Почтовые домены";
    }

    function load() {
        
    }

    function save($arguments=null)
    {
        global $Objects;
        if (isset($arguments)) {
        	$this->load();
        	$this->name = $arguments["name"];
        	$this->old_name = $arguments["old_name"];
        }
        $mail_domains = $Objects->get("MailDomains_".$this->module_id);
        $app = $Objects->get("MailApplication_".$this->module_id);

        if ($this->old_name == $this->name)
                return 0;
        
        // Проверяем, существует ли домен, который мы пытаемся сохранить
        if ($mail_domains->contains($this->name)) {
            $this->reportError("Указанный домен уже существует", "save");
            return 0;
        }

        // Если имя домена изменилось и это не вновь создаваемый домен,
        // нужно изменить имя домена во всех почтовых ящиках и во всех
        // почтовых ящиках интернет, которые принадлежат почтовым ящикам из
        // этого домена
        if ($this->old_name!=$this->name && $this->old_name!="")
        {
            $mailboxes = $Objects->get("Mailboxes_".$this->module_id);
            $mailboxes->load();
            $remote_mailboxes = $Objects->get("RemoteMailboxes_".$this->module_id);
            $remote_mailboxes->load();
            $owned_mailboxes = $Objects->query("Mailbox",array("domain" => $this->old_name));
            $mail_aliases = $Objects->get("MailAliases_".$this->module_id);
            $mail_aliases->load();
            $owned_mail_aliases = $Objects->query("MailAlias",array("domain" => $this->old_name));
        }        
		
        $this->loaded_name = $this->old_name;
        
        // Получаем список доменов из файла
        $strings = file($app->remotePath.$app->postfixDomainsTable);
        $fp = fopen($app->remotePath.$app->postfixDomainsTable,"w");

        // Ищем и заменяем строку с указанным доменом в файле
        $found = false;
        for ($counter=0;$counter<count($strings);$counter++) {
            $line = $strings[$counter];
            $line = explode(" ",$line);
            $line = trim($line[0]);
            if ($line==$this->loaded_name)
            {
                $strings[$counter] = $this->name." 1\n";
                $found = true;
            }
        }

        // если не нашли, значит домен новый и строку с ним нужно добавить в файл
        if (!$found)
            $strings[count($strings)] = $this->name." 1\n";

        // записываем обновленный список в файл
        for($counter=0;$counter<count($strings);$counter++) {
            fwrite($fp,$strings[$counter]);
        }
        fclose($fp);
        
        // Получаем список доменов из файла
        $strings = file($app->remotePath.$app->postfixTransportTable);
        $fp = fopen($app->remotePath.$app->postfixTransportTable,"w");

        // Ищем и заменяем строку с указанным доменом в файле
        $found = false;
        for ($counter=0;$counter<count($strings);$counter++) {
            $line = $strings[$counter];
            $line = explode(" ",$line);
            $line = trim($line[0]);
            if ($line==$this->loaded_name)
            {
                $strings[$counter] = $this->name." virtual\n";
                $found = true;
            }
        }

        // если не нашли, значит домен новый и строку с ним нужно добавить в файл
        if (!$found)
            $strings[count($strings)] = $this->name." virtual\n";

        // записываем обновленный список в файл
        for($counter=0;$counter<count($strings);$counter++) {
            fwrite($fp,$strings[$counter]);
        }
        fclose($fp);

        $gapp = $Objects->get("Application");
        if (!$gapp->initiated)
        	$gapp->initModules();
        if ($this->loaded_name=="")
        	$gapp->raiseRemoteEvent("MAILDOMAIN_ADDED","object_id=".$this->getId());
        else
        	$gapp->raiseRemoteEvent("MAILDOMAIN_CHANGED","object_id=".$this->getId());
        
        $mail_domains->load();

        if ($this->loaded_name!=$this->name && $this->loaded_name!="") {
            foreach ($owned_mailboxes as $value) {
//                if (!$value->loaded)
                    $value->load();
                $owned_remote_mailboxes = $remote_mailboxes->getByOwner($value->presentation);

                $value->domain = $this->name;
                $value->save();
                foreach($owned_remote_mailboxes as $value2) {
                    $value2->owner = $value->presentation;
                    $value2->save();
                }
            }
            foreach ($owned_mail_aliases as $value) {
                $owned_remote_mailboxes = $remote_mailboxes->getByOwner($value->presentation);

                $value->domain = $this->name;
                $value->save();
                foreach($owned_remote_mailboxes as $value2) {
                    $value2->owner = $value->presentation;
                    $value2->save();
                }
            }
        }

        // устанавливаем признак соответствия загруженного имени текущему
        $this->loaded = true;
        $shell = $Objects->get("Shell_shell");
        $shell->exec_command($app->remoteSSHCommand." ".$app->postDomainsTableCommand);
        $shell->exec_command($app->remoteSSHCommand." ".$app->postAliasesTableCommand);
        $shell->exec_command($app->remoteSSHCommand." ".$app->postMailboxTableCommand);        
        $shell->exec_command($app->remoteSSHCommand." ".$app->postTransportTableCommand);        
        $shell->exec_command($app->remoteSSHCommand." ".$app->restartMailScannerCommand);
        $this->loaded_name = $this->name;
    }

    function getId()
    {
        return "MailDomain_".$this->module_id."_".$this->name;
    }

    function getPresentation()
    {
        return $this->name;
    }
    
    function getHookProc($number) {
    	switch ($number) {
    		case '3': return "save";
    	}
    	return parent::getHookProc($number);
    }
}
?>