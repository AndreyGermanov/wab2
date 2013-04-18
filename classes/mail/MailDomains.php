<?php
/**
 * Данный классу правляет списком почтовых доменов, которые находятся в файле
 * /etc/postfix/virtual_domains, на который ссылается переменная Application->postfixDomainsTable.
 *
 * С его помощью можно загржать список из файла и сохранять список в файл, добавлять
 * новые домены и удалять их, а также проверять, существует ли указанный домен в файле.
 *
 * Файл имеет формат:
 *
 * домен 1
 *
 * Для оперирования доменами предназначен массив $mail_domains, который на самом
 * деле является виртуальным и получается из глобального кэша объектов $Objects->query("MailDomain");
 *
 * Для выполнения операций предназначены следующие методы:
 *
 * load() - загрузка списка доменов из файла
 * save() - запись списка доменов в файл
 * add() - добавление нового домена в список
 * remove() - удаление домена из списка и из файла
 * contains() - проверка, существует ли указанный домен в списке и в файле
 */

class MailDomains extends WABEntity {

    function construct($params) {
        $this->module_id = $params[0]."_".$params[1];
        $this->loaded = false;
        $this->clientClass = "MailDomains";
        $this->parentClientClasses = "Entity";        
    }

    function load() {
        
        global $Objects;
        $app = $Objects->get("MailApplication_".$this->module_id);
        $strings = @file($app->remotePath.$app->postfixDomainsTable);
        for ($counter=0;$counter<count($strings);$counter++) {
            $line = explode(" ",$strings[$counter]);
            $line = trim($line[0]);
            $domain = $Objects->get("MailDomain_".$this->module_id."_".$line);
            $domain->loaded_name = $line;
            $domain->loaded = true;
        }
        $this->loaded = true;
    }

    function save() {
        global $Objects;
        $app = $Objects->get("MailApplication_".$this->module_id);
        $fp = fopen($app->remotePath.$app->postfixDomainsTable,"w");
        foreach ($this->mail_domains as $value)
        {
            fwrite($fp,$value->name." 1\n");
            $value->loaded = true;
            $value->loaded_name = $value->name;
        }
        $fp = fopen($app->remotePath.$app->postfixTransportTable,"w");
        foreach ($this->mail_domains as $value)
        {
            fwrite($fp,$value->name." virtual\n");
            $value->loaded = true;
            $value->loaded_name = $value->name;
        }
        $this->loaded = true;
        $shell = $Objects->get("Shell_Helix");
        $shell->exec_command($app->remoteSSHCommand." ".$app->postDomainsTableCommand);
        $shell->exec_command($app->remoteSSHCommand." ".$app->postAliasesTableCommand);
        $shell->exec_command($app->remoteSSHCommand." ".$app->postMailboxTableCommand);
        $shell->exec_command($app->remoteSSHCommand." ".$app->postTransportTableCommand);
        $shell->exec_command($app->remoteSSHCommand." ".$app->restartMailScannerCommand);
    }

    function contains($name)
    {
        global $Objects;
        if (!$this->loaded)
            $this->load();
        return $Objects->contains("MailDomain_".$this->module_id."_".trim($name));
    }

    function add($name)
    {
        if ($this->contains($name)) {
            $this->reportError("Домен ".$name." уже существует !","add");
            return 0;
        }
        global $Objects;
        return $Objects->get("MailDomain_".$this->module_id."_".$name);
    }

    function remove($name)
    {
    	if (is_array($name)) {
    		$name = $name["domain"];
    	}
        if (!$this->contains($name))
        {
            $this->reportError("Домен ".$name." не существует !","remove");
            return 0;
        }

        global $Objects;
        
        $app = $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
        $app->raiseRemoteEvent("MAILDOMAIN_DELETED","object_id="."MailDomain_".$this->module_id."_".trim($name));
        
        $app = $Objects->get("MailApplication_".$this->module_id);

        // Теперь нужно удалить все почтовые ящики, относящиеся к этому домену,
        // всю почту в них и все привязанные к ним почтовые ящики Интернет
        $mailboxes = $Objects->get("Mailboxes_".$this->module_id);
        $mailboxes->load();
        $mail_aliases = $Objects->get("MailAliases_".$this->module_id);
        $mail_aliases->load();
        $remote_mailboxes = $Objects->get("RemoteMailboxes_".$this->module_id);
        $remote_mailboxes->load();
        $owned_mailboxes = $Objects->query("Mailbox",array("domain" => $name));
        foreach($owned_mailboxes as $value) {            
            $mailboxes->remove($value->name,$value->domain);
        }

        $owned_mail_aliases = $Objects->query("MailAlias",array("domain" => $name));
        foreach($owned_mail_aliases as $value) {
            $mail_aliases->remove($value->name,$value->domain);
        }

        $strings = file($app->remotePath.$app->postfixDomainsTable);
        $fp = fopen($app->remotePath.$app->postfixDomainsTable,"w");
        for ($counter=0;$counter<count($strings);$counter++)
        {
            $line = explode(" ",$strings[$counter]);
            $line = trim($line[0]);
            if ($line==trim($name))
                continue;
            fwrite($fp,$line." 1\n");
        }

        $strings = file($app->remotePath.$app->postfixTransportTable);
        $fp = fopen($app->remotePath.$app->postfixTransportTable,"w");
        for ($counter=0;$counter<count($strings);$counter++)
        {
            $line = explode(" ",$strings[$counter]);
            $line = trim($line[0]);
            if ($line==trim($name))
                continue;
            fwrite($fp,$line." 1\n");
        }
        
        $Objects->remove("MailDomain_".$this->module_id."_".trim($name));
        $shell = $Objects->get("Shell_Helix");
        $shell->exec_command($app->remoteSSHCommand." postmap ".$app->postfixDomainsTable);
        $shell->exec_command($app->remoteSSHCommand." postmap ".$app->postfixAliasesTable);
        $shell->exec_command($app->remoteSSHCommand." postmap ".$app->postfixMailboxTable);
        $shell->exec_command($app->remoteSSHCommand." postmap ".$app->postfixTransportTable);
        $shell->exec_command($app->remoteSSHCommand." postfix reload");
     }

    function __get($name)
    {
        global $Objects;
        switch ($name) {
            case "mail_domains":
                return $Objects->query("MailDomain");
                break;
            default:
                if (isset($this->fields[$name]))
                    return $this->fields[$name];
                else
                    return "";
        }
    }
    
    function getHookProc($number) {
    	switch ($number) {
    		case '4': return "remove";
    	}
    	return parent::getHookProc($number);
    }
}
?>
