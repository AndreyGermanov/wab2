<?php
class CollectorMXTree extends Tree {

    function  construct($params) {
        parent::construct($params);
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;
        $this->module_id = @$params[0]."_".@$params[1];
        $this->object_id = @$params[2];
        $this->name = @$params[2];
        $this->clientClass = "CollectorMXTree";
        $this->parentClientClasses = "Tree~Entity";        
    }

    function setTreeItems()
    {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();

        $result = array();
        $result["01_01_SystemSettings"]["id"] = "SystemSettings_".$this->module_id;
        $result["01_01_SystemSettings"]["title"] = "Сервер";
        $result["01_01_SystemSettings"]["icon"] = $app->skinPath."images/Window/system-settings.gif";
        $result["01_01_SystemSettings"]["parent"] = "";
        $result["01_01_SystemSettings"]["loaded"] = "true";
 
        $result["02_01_SystemSettingsNetwork"]["id"] = "MailSettings_".$this->module_id."_Settings";
        $result["02_01_SystemSettingsNetwork"]["title"] = "Основные параметры";
        $result["02_01_SystemSettingsNetwork"]["icon"] = $app->skinPath."images/Tree/base_options.png";
        $result["02_01_SystemSettingsNetwork"]["parent"] = "SystemSettings_".$this->module_id;
        $result["02_01_SystemSettingsNetwork"]["loaded"] = "true";

        $result["03_01_SystemSettingsMQ"]["id"] = "MailQueue_".$this->module_id."_Mail";
        $result["03_01_SystemSettingsMQ"]["title"] = "Почтовая очередь";
        $result["03_01_SystemSettingsMQ"]["icon"] = $app->skinPath."images/Tree/mailqueue.png";
        $result["03_01_SystemSettingsMQ"]["parent"] = "SystemSettings_".$this->module_id;
        $result["03_01_SystemSettingsMQ"]["loaded"] = "true";

        $result["04_01_SystemSettingsSpamFilters"]["id"] = "SpamFilters_".$this->module_id;
        $result["04_01_SystemSettingsSpamFilters"]["title"] = "Спам-фильтры";
        $result["04_01_SystemSettingsSpamFilters"]["icon"] = $app->skinPath."images/Tree/spam_filters.png";
        $result["04_01_SystemSettingsSpamFilters"]["parent"] = "SystemSettings_".$this->module_id;
        $result["04_01_SystemSettingsSpamFilters"]["loaded"] = "true";

//        $result["04_02_SystemSettingsRepFilter"]["id"] = "RepFilterConfig_".$this->module_id."_RepConfig";
//        $result["04_02_SystemSettingsRepFilter"]["title"] = "Репутационный фильтр";
//        $result["04_02_SystemSettingsRepFilter"]["icon"] = $app->skinPath."images/Tree/sites.gif";
//        $result["04_02_SystemSettingsRepFilter"]["parent"] = "SpamFilters_".$this->module_id;
//        $result["04_02_SystemSettingsRepFilter"]["loaded"] = "true";

        $result["04_03_SystemSettingsContentFilter"]["id"] = "MailScannerConfig_".$this->module_id."_Config";
        $result["04_03_SystemSettingsContentFilter"]["title"] = "Фильтр содержимого";
        $result["04_03_SystemSettingsContentFilter"]["icon"] = $app->skinPath."images/Tree/content_filter.png";
        $result["04_03_SystemSettingsContentFilter"]["parent"] = "SpamFilters_".$this->module_id;
        $result["04_03_SystemSettingsContentFilter"]["loaded"] = "true";

        $result["05_01_ControlPanel"]["id"] = "ControlPanel_".$this->module_id;
        $result["05_01_ControlPanel"]["title"] = "Панель управления";
        $result["05_01_ControlPanel"]["icon"] = $app->skinPath."images/Tree/control_panel.png";
        $result["05_01_ControlPanel"]["parent"] = "SystemSettings_".$this->module_id;
        $result["05_01_ControlPanel"]["loaded"] = "false";

        $result["05_02_EventViewer"]["id"] = "Logs_".$this->module_id;
        $result["05_02_EventViewer"]["title"] = l10n("Журналы событий");
        $result["05_02_EventViewer"]["icon"] = $app->skinPath."images/Tree/eventviewer.png";
        $result["05_02_EventViewer"]["parent"] = "ControlPanel_".$this->module_id;
        $result["05_02_EventViewer"]["loaded"] = "true";
                
        $result["05_03_EventViewer"]["id"] = "EventLog_".$this->module_id."_Events";
        $result["05_03_EventViewer"]["title"] = l10n("Панель управления");
        $result["05_03_EventViewer"]["icon"] = $app->skinPath."images/Tree/eventviewer.png";
        $result["05_03_EventViewer"]["parent"] = "Logs_".$this->module_id;
        $result["05_03_EventViewer"]["loaded"] = "true";
        
        $result["05_04_SystemSettingsUsers"]["id"] = "SystemSettingsUsers_".$this->module_id;
        $result["05_04_SystemSettingsUsers"]["title"] = "Пользователи";
        $result["05_04_SystemSettingsUsers"]["icon"] = $app->skinPath."images/Tree/user.gif";
        $result["05_04_SystemSettingsUsers"]["parent"] = "ControlPanel_".$this->module_id;
        $result["05_04_SystemSettingsUsers"]["loaded"] = "false";

        $result["01_02_Mailboxes"]["id"] = "Mailboxes_".$this->module_id;
        $result["01_02_Mailboxes"]["title"] = "Почтовые ящики";
        $result["01_02_Mailboxes"]["icon"] = $app->skinPath."images/Window/mail-folder.gif";
        $result["01_02_Mailboxes"]["parent"] = "";
        $result["01_02_Mailboxes"]["loaded"] = "false";

        $result["01_03_Addresses"]["id"] = "AddressBooks_".$this->module_id;
        $result["01_03_Addresses"]["title"] = "Адресные книги";
        $result["01_03_Addresses"]["icon"] = $app->skinPath."images/Tree/addrbook.gif";
        $result["01_03_Addresses"]["parent"] = "";
        $result["01_03_Addresses"]["loaded"] = "true";

        $result["02_03_Addresses"]["id"] = "AddressBookDefaultFields_".$this->module_id."_15_first";
        $result["02_03_Addresses"]["title"] = "Общие поля";
        $result["02_03_Addresses"]["icon"] = $app->skinPath."images/Tree/addrbook.gif";
        $result["02_03_Addresses"]["parent"] = "AddressBooks_".$this->module_id;
        $result["02_03_Addresses"]["loaded"] = "true";
        
        $result["03_03_Addresses"]["id"] = "AddressBook_".$this->module_id;
        $result["03_03_Addresses"]["title"] = "Системная адресная книга";
        $result["03_03_Addresses"]["icon"] = $app->skinPath."images/Tree/addrbook.gif";
        $result["03_03_Addresses"]["parent"] = "AddressBooks_".$this->module_id;
        $result["03_03_Addresses"]["loaded"] = "false";
        
        $result["04_03_ControlPanelMetadata"]["id"] = "MetadataTree_".$this->module_id."_LDAPAddressBooks";
        $result["04_03_ControlPanelMetadata"]["title"] = l10n("Адресные книги LDAP");
        $result["04_03_ControlPanelMetadata"]["icon"] = $this->skinPath."images/Tree/addrbook.gif";
        $result["04_03_ControlPanelMetadata"]["parent"] = "AddressBooks_".$this->module_id;
        $result["04_03_ControlPanelMetadata"]["loaded"] = "metadataArray=addressbooks#show_groups=0";
        $result["04_03_ControlPanelMetadata"]["subtree"] = "true";
                
        $result["01_05_Docs"]["id"] = "HTMLBook_".$this->module_id."_collector_1";
        $result["01_05_Docs"]["title"] = "Документация";
        $result["01_05_Docs"]["icon"] = $app->skinPath."images/Tree/docs.png";
        $result["01_05_Docs"]["parent"] = "";
        $result["01_05_Docs"]["loaded"] = "true";
        
        @ksort($result);
        $res = array();
        if (is_array($result)) {
            foreach($result as $value)
            {
                $res[count($res)] = implode("~",$value);
            }
            $this->items_string = implode("|",$res);
        }
    }

    function getMailboxesTree()
    {
        global $Objects;
        //echo "RemoteMailboxes_".$this->module_id;
        $mailboxes = $Objects->get("Mailboxes_".$this->module_id);
        $mail_aliases = $Objects->get("MailAliases_".$this->module_id);
        $remoteMailboxes = $Objects->get("RemoteMailboxes_".$this->module_id);
        $app = $Objects->get("MailApplication_".$this->module_id);
        if (!$app->loaded)
            $app->load();

        global $Objects;
        $appl = $Objects->get("Application");
        if (!$appl->initiated)
            $appl->initModules();

        $result = array();
        if ($app->mail_domain != "")
        {
            $domains = explode(",",$app->mail_domain);
            foreach ($domains as $domain)
            {
                $result["01_".$domain]["id"] = $this->module_id."_".$domain."_domain";
                $result["01_".$domain]["title"] = $domain;
                $result["01_".$domain]["icon"] = $appl->skinPath."images/Window/mail-domain.gif";
                $result["01_".$domain]["parent"] = "Mailboxes_".$this->module_id;
                $result["01_".$domain]["loaded"] = "true";
            }
        }
        $mailboxes->load();
        foreach ($mailboxes->mailboxes as $mbox)
        {
            $title = $mbox->getPresentation();
            $result["02_".$title]["id"] = $mbox->getId();
            $result["02_".$title]["title"] = $title;
            $result["02_".$title]["icon"] = $appl->skinPath."images/Window/mail.gif";
            $result["02_".$title]["parent"] = $this->module_id."_".$mbox->domain."_domain";
            $result["02_".$title]["loaded"] = "true";
        }
        $mail_aliases->load();
        foreach ($mail_aliases->mail_aliases as $mbox)
        {
            $title = $mbox->getPresentation();
            $result["02_".$title]["id"] = $mbox->getId();
            $result["02_".$title]["title"] = $title;
            $result["02_".$title]["icon"] = $appl->skinPath."images/Tree/maillist.gif";
            $result["02_".$title]["parent"] = $this->module_id."_".$mbox->domain."_domain";
            $result["02_".$title]["loaded"] = "true";

            $title = "Адресаты";
            $result["03_".$title."_".$mbox->getId()."_Addresses"]["id"] = $mbox->getId()."_Addresses";
            $result["03_".$title."_".$mbox->getId()."_Addresses"]["title"] = $title;
            $result["03_".$title."_".$mbox->getId()."_Addresses"]["icon"] = $appl->skinPath."images/Tree/mail_addresses.gif";
            $result["03_".$title."_".$mbox->getId()."_Addresses"]["parent"] = $mbox->getId();
            $result["03_".$title."_".$mbox->getId()."_Addresses"]["loaded"] = "true";

            $title = "Ящики  в Интернет";
            $result["03_".$title."_".$mbox->getId()."_RemoteMailboxes"]["id"] = $mbox->getId()."_RemoteMailboxes";
            $result["03_".$title."_".$mbox->getId()."_RemoteMailboxes"]["title"] = $title;
            $result["03_".$title."_".$mbox->getId()."_RemoteMailboxes"]["icon"] = $appl->skinPath."images/Tree/remote_mailboxes.gif";
            $result["03_".$title."_".$mbox->getId()."_RemoteMailboxes"]["parent"] = $mbox->getId();
            $result["03_".$title."_".$mbox->getId()."_RemoteMailboxes"]["loaded"] = "true";

            foreach($mbox->addresses as $value) {
                $title = $value;
                $result["04_".$title."_".$mbox->getId()."_MailboxAlias"]["id"] = $mbox->getId()."_Addresses_".$value;
                $result["04_".$title."_".$mbox->getId()."_MailboxAlias"]["title"] = $title;
                $result["04_".$title."_".$mbox->getId()."_MailboxAlias"]["icon"] = $appl->skinPath."images/Tree/mailbox_alias.gif";
                $result["04_".$title."_".$mbox->getId()."_MailboxAlias"]["parent"] = $mbox->getId()."_Addresses";
                $result["04_".$title."_".$mbox->getId()."_MailboxAlias"]["loaded"] = "true";
            }
        }

        $remoteMailboxes->load();
        foreach ($remoteMailboxes->mailboxes as $mbox)
        {
            $title = $mbox->getPresentation();
            $owner_parts = explode("@",$mbox->owner);
            if ($mailboxes->contains($owner_parts[0],$owner_parts[1])) {
                $result["03_".$title]["id"] = $mbox->getId();
                $result["03_".$title]["title"] = $title;
                $result["03_".$title]["icon"] = $appl->skinPath."images/Tree/RemoteMailbox.gif";
                $result["03_".$title]["parent"] = "Mailbox_".$this->module_id."_".str_replace("@","_",$mbox->owner);
                $result["03_".$title]["loaded"] = "true";
            }
            else
            {
                $result["04_".$title]["id"] = $mbox->getId();
                $result["04_".$title]["title"] = $title;
                $result["04_".$title]["icon"] = $appl->skinPath."images/Tree/RemoteMailbox.gif";
                $result["04_".$title]["parent"] = "MailAlias_".$this->module_id."_".$owner_parts[0]."_".$owner_parts[1]."_RemoteMailboxes";
                $result["04_".$title]["loaded"] = "true";
            }
        }
        @ksort($result);
        $res = array();        
        if (is_array($result)) {
            foreach($result as $value)
            {
                $res[count($res)] = implode("~",$value);
            }
            echo implode("|",$res);
        }
    }

    function getAddressBookTree() {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        global $Objects;
        $addresses = $Objects->get("AddressBook_".$this->module_id);
        $addresses->load();
        foreach ($addresses->addresses as $addr)
        {
            $result[strtoupper("01_".$addr->name)]["id"] = "Address_".$this->module_id."_".$addr->name;
            $result[strtoupper("01_".$addr->name)]["title"] = $addr->name;
            $result[strtoupper("01_".$addr->name)]["icon"] = $app->skinPath."images/Tree/address.gif";
            $result[strtoupper("01_".$addr->name)]["parent"] = "AddressBook_".$this->module_id;
            $result[strtoupper("01_".$addr->name)]["loaded"] = "true";
        }
        @ksort($result);
        $res = array();
        if (is_array($result)) {
            foreach($result as $value)
            {
                $res[count($res)] = implode("~",$value);
            }
            echo implode("|",$res);
        }
    }

    function getApacheUsersTree() {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $users = $Objects->get("ApacheUsers_".$this->module_id);
        $users->load();
        foreach ($users->apacheUsers as $user)
        {
            $result["01_".$user->name]["id"] = "ApacheUser_".$this->module_id."_".$user->name;
            $result["01_".$user->name]["title"] = $user->name;
            $result["01_".$user->name]["icon"] = $app->skinPath."images/Tree/user.gif";
            $result["01_".$user->name]["parent"] = "SystemSettingsUsers_".$this->module_id;
            $result["01_".$user->name]["loaded"] = "true";
        }
        @ksort($result);
        $res = array();
        if (is_array($result)) {
            foreach($result as $value)
            {
                $res[count($res)] = implode("~",$value);
            }
            echo implode("|",$res);
        }
    }
    
    function getHookProc($number) {
		switch ($number) {
			case '3': return "getApacheUsersTree";
			case '4': return "getMailboxesTree";
			case '5': return "getAddressBookTree";
		}
		return parent::getHookProc($number);
	}
}
?>