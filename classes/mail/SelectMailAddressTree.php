<?php
/* 
 * Класс, отображающий дерево почтовых ящиков в окне выбора почтового ящика
 * для спама или обратного адреса.
 * 
 */

class SelectMailAddressTree extends Tree {

    function construct($params) {
        $this->module_id = $params[0]."_".$params[1];
        $this->name = $params[2];
        $this->title = "Адреса";

        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;
        $this->template = "templates/interface/Tree.html";
        $this->css = $this->skinPath."styles/Tree.css";
        $this->handler = "scripts/handlers/mail/SelectMailAddressTree.js";
        $this->icon = $app->skinPath."images/Window/mail-domain.gif";

        $this->target_item = "";
        $this->show_mailboxes = false;
        $this->show_remote_mailboxes = false;
        $this->show_addressbook = false;
        $this->clientClass = "SelectMailAddressTree";
        $this->parentClientClasses = "Tree~Entity";        
    }

    function setTreeItems()
    {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();

        $result = array();
        if ($this->show_mailboxes) {
            $result["01_01_Mailboxes"]["id"] = "Mailboxes_".$this->module_id;
            $result["01_01_Mailboxes"]["title"] = "Почтовые ящики";
            $result["01_01_Mailboxes"]["icon"] = $app->skinPath."images/Window/mail-folder.gif";
            $result["01_01_Mailboxes"]["parent"] = "";
            $result["01_01_Mailboxes"]["loaded"] = "false";
        }

        if ($this->show_remote_mailboxes) {
            $result["01_02_RemoteMailboxes"]["id"] = "RemoteMailboxes_".$this->module_id;
            $result["01_02_RemoteMailboxes"]["title"] = "Почтовые ящики Интернет";
            $result["01_02_RemoteMailboxes"]["icon"] = $app->skinPath."images/Tree/remote_mailboxes.gif";
            $result["01_02_RemoteMailboxes"]["parent"] = "";
            $result["01_02_RemoteMailboxes"]["loaded"] = "false";
        }

        if ($this->show_addressbook) {
            $result["01_03_Addresses"]["id"] = "AddressBook_".$this->module_id;
            $result["01_03_Addresses"]["title"] = "Адресная книга";
            $result["01_03_Addresses"]["icon"] = $app->skinPath."images/Tree/addrbook.gif";
            $result["01_03_Addresses"]["parent"] = "";
            $result["01_03_Addresses"]["loaded"] = "false";
        }

        ksort($result);
        $res = array();
        foreach($result as $value)
        {
            $res[count($res)] = implode("~",$value);
        }
        $this->items_string = implode("|",$res);
    }

    function getMailboxesTree()
    {
        global $Objects;
        $mailboxes = $Objects->get("Mailboxes_".$this->module_id);
        $app = $Objects->get("MailApplication_".$this->module_id);
        if (!$app->loaded)
            $app->load();

        global $Objects;
        $appl = $Objects->get("Application");
        if (!$appl->initiated)
            $appl->initModules();

        $result = array();
        $mailboxes->load();

        foreach ($mailboxes->mailboxes as $mbox)
        {
            $title = $mbox->getPresentation();
            $result["02_".$title]["id"] = $mbox->getId();
            $result["02_".$title]["title"] = $title;
            $result["02_".$title]["icon"] = $appl->skinPath."images/Window/mail.gif";
            $result["02_".$title]["parent"] = "Mailboxes_".$this->module_id;
            $result["02_".$title]["loaded"] = "true";
        }

        ksort($result);
        $res = array();
        foreach($result as $value)
        {
            $res[count($res)] = implode("~",$value);
        }
        echo implode("|",$res);
    }

    function getRemoteMailboxesTree()
    {
        global $Objects;
        $remoteMailboxes = $Objects->get("RemoteMailboxes_".$this->module_id);
        $app = $Objects->get("MailApplication_".$this->module_id);
        if (!$app->loaded)
            $app->load();

        global $Objects;
        $appl = $Objects->get("Application");
        if (!$appl->initiated)
            $appl->initModules();

        $result = array();
        $remoteMailboxes->load();
        foreach ($remoteMailboxes->mailboxes as $mbox)
        {
            $title = $mbox->getPresentation();
            $result["03_".$title]["id"] = $mbox->getId();
            $result["03_".$title]["title"] = $title;
            $result["03_".$title]["icon"] = $appl->skinPath."images/Tree/RemoteMailbox.gif";
            $result["03_".$title]["parent"] = "RemoteMailboxes_".$this->module_id;
            $result["03_".$title]["loaded"] = "true";
        }

        ksort($result);
        $res = array();
        foreach($result as $value)
        {
            $res[count($res)] = implode("~",$value);
        }
        echo implode("|",$res);
    }

    function getHookProc($number) {
    	switch($number) {
    		case '3': return "showHook";
    		case '4': return "getMailboxesTree";
    		case '5': return "getRemoteMailboxesTree";
    	}
    	return parent::getHookProc($number);
    }
    
    function showHook($arguments) {
    	$this->setArguments($arguments);
    	$this->setTreeItems(@$arguments["site"]);
    	$this->show();    	 
    }  
}
?>