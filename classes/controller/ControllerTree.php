<?
class ControllerTree extends Tree {

    public $userConfig;

    function  construct($params) {
        parent::construct($params);
        $this->module_id = @$params[0]."_".@$params[1];
        $this->object_id = @$params[2];
        $this->name = @$params[2];
        $this->handler = "scripts/handlers/controller/ControllerTree.js";
        $this->online_network_monitor = "";
        $this->online_network_monitor_changed = false;
        $this->loaded = false;
        $this->clientClass = "ControllerTree";
        $this->parentClientClasses = "Tree~Entity";        
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;
        $this->app = $app;
        $this->capp = $Objects->get($this->module_id);
    }

    function setTreeItems() {
    	global $Objects;
        $app = $this->app;
        $this->userConfig = $Objects->get("AdminConfig_".@$_SERVER["PHP_AUTH_USER"])->modules[$app->getModuleNameByClass($this->module_id)];

        $result["01_01_FileServer"]["id"] = "FileServer_".$this->module_id."_Files";
        $result["01_01_FileServer"]["title"] = l10n("Файловый сервер");
        $result["01_01_FileServer"]["icon"] = $app->skinPath."images/Tree/fileserver.png";
        $result["01_01_FileServer"]["parent"] = "";
        $result["01_01_FileServer"]["loaded"] = "true";

        $result["02_01_FileShares"]["id"] = "FileShares_".$this->module_id;
        $result["02_01_FileShares"]["title"] = l10n("Файловый менеджер");
        $result["02_01_FileShares"]["icon"] = $app->skinPath."images/Tree/fileman.png";
        $result["02_01_FileShares"]["parent"] = "FileServer_".$this->module_id."_Files";
        $result["02_01_FileShares"]["loaded"] = "true";

        $result["03_01_Users"]["id"] = "Users_".$this->module_id;
        $result["03_01_Users"]["title"] = l10n("Пользователи");
        $result["03_01_Users"]["icon"] = $app->skinPath."images/Tree/user.png";
        $result["03_01_Users"]["parent"] = "FileServer_".$this->module_id."_Files";
        $result["03_01_Users"]["loaded"] = "false";

        $result["04_01_Groups"]["id"] = "Groups_".$this->module_id;
        $result["04_01_Groups"]["title"] = l10n("Группы пользователей");
        $result["04_01_Groups"]["icon"] = $app->skinPath."images/Tree/group.png";
        $result["04_01_Groups"]["parent"] = "FileServer_".$this->module_id."_Files";
        $result["04_01_Groups"]["loaded"] = "false";

        $result["05_01_FileShares"]["id"] = "ObjectGroupProperties_".$this->module_id;
        $result["05_01_FileShares"]["title"] = l10n("Группы объектов");
        $result["05_01_FileShares"]["icon"] = $app->skinPath."images/Tree/objects_groups.png";
        $result["05_01_FileShares"]["parent"] = "FileServer_".$this->module_id."_Files";
        $result["05_01_FileShares"]["loaded"] = "false";

        $result["06_01_InetAccess"]["id"] = "InternetAccess_".$this->module_id;
        $result["06_01_InetAccess"]["title"] = l10n("Доступ из Интернет");
        $result["06_01_InetAccess"]["icon"] = $app->skinPath."images/Tree/sites.gif";
        $result["06_01_InetAccess"]["parent"] = "FileServer_".$this->module_id."_Files";
        $result["06_01_InetAccess"]["loaded"] = "true";

        $result["07_01_FTPServer"]["id"] = "FTPHost_".$this->module_id."_Default";
        $result["07_01_FTPServer"]["title"] = l10n("FTP-сервер");
        $result["07_01_FTPServer"]["icon"] = $app->skinPath."images/Tree/ftp.png";
        $result["07_01_FTPServer"]["parent"] = "InternetAccess_".$this->module_id;
        $result["07_01_FTPServer"]["loaded"] = "true";

        $result["08_01_EventViewer"]["id"] = "Logs_".$this->module_id;
        $result["08_01_EventViewer"]["title"] = l10n("Журналы событий");
        $result["08_01_EventViewer"]["icon"] = $app->skinPath."images/Tree/eventviewer.png";
        $result["08_01_EventViewer"]["parent"] = "FileServer_".$this->module_id."_Files";
        $result["08_01_EventViewer"]["loaded"] = "true";
        
        $result["08_02_EventViewer"]["id"] = "FullAuditReport_".$this->module_id."_report";
        $result["08_02_EventViewer"]["title"] = l10n("Файловая система");
        $result["08_02_EventViewer"]["icon"] = $app->skinPath."images/Tree/eventviewer.png";
        $result["08_02_EventViewer"]["parent"] = "Logs_".$this->module_id;
        $result["08_02_EventViewer"]["loaded"] = "true";

        $result["08_03_EventViewer"]["id"] = "EventLog_".$this->module_id."_Events";
        $result["08_03_EventViewer"]["title"] = l10n("Панель управления");
        $result["08_03_EventViewer"]["icon"] = $app->skinPath."images/Tree/eventviewer.png";
        $result["08_03_EventViewer"]["parent"] = "Logs_".$this->module_id;
        $result["08_03_EventViewer"]["loaded"] = "true";
        
        $result["09_01_Backup"]["id"] = "Backups_".$this->module_id."_Backup";
        $result["09_01_Backup"]["title"] = l10n("Резервное копирование");
        $result["09_01_Backup"]["icon"] = $app->skinPath."images/Tree/backup.png";
        $result["09_01_Backup"]["parent"] = "FileServer_".$this->module_id."_Files";
        $result["09_01_Backup"]["loaded"] = "true";

        $result["10_01_ShadowCopy"]["id"] = "ShadowCopyManager_".$this->module_id."_Manager";
        $result["10_01_ShadowCopy"]["title"] = l10n("Теневые копии");
        $result["10_01_ShadowCopy"]["icon"] = $app->skinPath."images/Tree/shadowcopy.png";
        $result["10_01_ShadowCopy"]["parent"] = "Backups_".$this->module_id."_Backup";
        $result["10_01_ShadowCopy"]["loaded"] = "true";
        
        
        $result["11_01_Networks"]["id"] = "DhcpServer_".$this->module_id."_Networks";
        $result["11_01_Networks"]["title"] = l10n("Сетевой центр");
        $result["11_01_Networks"]["icon"] = $app->skinPath."images/Tree/networks.gif";
        $result["11_01_Networks"]["parent"] = "";
        $result["11_01_Networks"]["loaded"] = "false";

        $result["12_01_SystemSettings"]["id"] = "SysSettings_".$this->module_id;
        $result["12_01_SystemSettings"]["title"] = l10n("Система");
        $result["12_01_SystemSettings"]["icon"] = $app->skinPath."images/Tree/system-settings.png";
        $result["12_01_SystemSettings"]["parent"] = "";
        $result["12_01_SystemSettings"]["loaded"] = "true";

        $result["13_02_BaseSettings"]["id"] = "SystemSettings_".$this->module_id."_Settings";
        $result["13_02_BaseSettings"]["title"] = l10n("Основные параметры");
        $result["13_02_BaseSettings"]["icon"] = $app->skinPath."images/Tree/base_options.png";
        $result["13_02_BaseSettings"]["parent"] = "SysSettings_".$this->module_id;
        $result["13_02_BaseSettings"]["loaded"] = "true";
        
        $result["14_03_Integration"]["id"] = "Integration_".$this->module_id;
        $result["14_03_Integration"]["title"] = l10n("Интеграция");
        $result["14_03_Integration"]["icon"] = $app->skinPath."images/Tree/integration.png";
        $result["14_03_Integration"]["parent"] = "SystemSettings_".$this->module_id."_Settings";
        $result["14_03_Integration"]["loaded"] = "true";

        $result["15_04_MailIntegrator"]["id"] = "MailIntegrator_".$this->module_id."_Mail";
        $result["15_04_MailIntegrator"]["title"] = l10n("Почтовый сервер");
        $result["15_04_MailIntegrator"]["icon"] = $app->skinPath."images/Tree/mail.gif";
        $result["15_04_MailIntegrator"]["parent"] = "Integration_".$this->module_id;
        $result["15_04_MailIntegrator"]["loaded"] = "true";

        $result["16_05_GatewayIntegrator"]["id"] = "GatewayIntegrator_".$this->module_id."_Bastion";
        $result["16_05_GatewayIntegrator"]["title"] = l10n("Интернет-шлюз");
        $result["16_05_GatewayIntegrator"]["icon"] = $app->skinPath."images/Tree/firewall.png";
        $result["16_05_GatewayIntegrator"]["parent"] = "Integration_".$this->module_id;
        $result["16_05_GatewayIntegrator"]["loaded"] = "true";

        $result["16_06_DocflowIntegrator"]["id"] = "DocFlowIntegrator_".$this->module_id."_Docs";
        $result["16_06_DocflowIntegrator"]["title"] = l10n("Бизнес-сервер");
        $result["16_06_DocflowIntegrator"]["icon"] = $app->skinPath."images/docflow/contragent.png";
        $result["16_06_DocflowIntegrator"]["parent"] = "Integration_".$this->module_id;
       	$result["16_06_DocflowIntegrator"]["loaded"] = "false";
        
        $result["17_06_ControlPanel"]["id"] = "ControlPanel_".$this->module_id;
        $result["17_06_ControlPanel"]["title"] = l10n("Панель управления");
        $result["17_06_ControlPanel"]["icon"] = $app->skinPath."images/Tree/control_panel.png";
        $result["17_06_ControlPanel"]["parent"] = "SysSettings_".$this->module_id;
        $result["17_06_ControlPanel"]["loaded"] = "true";
        
        $result["18_07_ControlPanelAdmins"]["id"] = "ModelConfig_".$this->module_id."_appconfig";
        $result["18_07_ControlPanelAdmins"]["title"] = l10n("Глобальные параметры");
        $result["18_07_ControlPanelAdmins"]["icon"] = $app->skinPath."images/Tree/systemsettings.png";
        $result["18_07_ControlPanelAdmins"]["parent"] = "ControlPanel_".$this->module_id;
        $result["18_07_ControlPanelAdmins"]["loaded"] = "true";
                
        $result["19_08_ControlPanelAdmins"]["id"] = "SystemSettingsUsers_".$this->module_id;
        $result["19_08_ControlPanelAdmins"]["title"] = l10n("Пользователи");
        $result["19_08_ControlPanelAdmins"]["icon"] = $app->skinPath."images/Tree/user.gif";
        $result["19_08_ControlPanelAdmins"]["parent"] = "ControlPanel_".$this->module_id;
        $result["19_08_ControlPanelAdmins"]["loaded"] = "false";

        $result["20_08_ControlPanelMetadata"]["id"] = "Metadata_".$this->module_id;
        $result["20_08_ControlPanelMetadata"]["title"] = l10n("Метаданные");
        $result["20_08_ControlPanelMetadata"]["icon"] = $app->skinPath."images/Tree/metadata.png";
        $result["20_08_ControlPanelMetadata"]["parent"] = "ControlPanel_".$this->module_id;
        $result["20_08_ControlPanelMetadata"]["loaded"] = "false";
        
        $result["21_01_ControlPanelAdmins"]["id"] = "HTMLBook_".$this->module_id."_controller_1";
        $result["21_01_ControlPanelAdmins"]["title"] = l10n("Документация");
        $result["21_01_ControlPanelAdmins"]["icon"] = $app->skinPath."images/Tree/docs.png";
        $result["21_01_ControlPanelAdmins"]["parent"] = "";
        $result["21_01_ControlPanelAdmins"]["loaded"] = "true";

        ksort($result);
        $res = array();
        foreach($result as $value)
        {
            $res[count($res)] = implode("~",$value);
        }
        $this->items_string = implode("|",$res);
    }

    function getSubnetsTree() {
        global $Objects;
        $app = $Objects->get("Application");
        $result = array();
        if (!$app->initiated)
            $app->initModules();
        $subnets = $Objects->get("DhcpSubnets_".$this->module_id."_Subnets");
        if (!$subnets->loaded)
            $subnets->load();

        foreach ($subnets->subnets as $subnet)
        {
            $result["01_".$subnet->title]["id"] = "DhcpSubnet_".$this->module_id."_".$subnet->name;
            $result["01_".$subnet->title]["title"] = $subnet->title."#".$subnet->name;
            $result["01_".$subnet->title]["icon"] = $app->skinPath."images/Tree/network.gif";
            $result["01_".$subnet->title]["parent"] = "DhcpServer_".$this->module_id."_Networks";
            $result["01_".$subnet->title]["loaded"] = "false";
        }
        @ksort($result);
        $res = array();
        foreach($result as $value)
        {
            $res[count($res)] = implode("~",$value);
        }
        echo implode("|",$res);
    }

    function getHostsTree($subnet_name) {
		if (is_array($subnet_name))
			$subnet_name = $subnet_name["subnet_name"];
        global $Objects;
        $app = $Objects->get("Application");
        $result = array();
        if (!$app->initiated)
            $app->initModules();
        $subnet = $Objects->get("DhcpSubnet_".$this->module_id."_".$subnet_name);
        if (!$subnet->hosts_loaded)
            $subnet->loadHosts();
        foreach ($subnet->hosts as $host)
        {
            $result["01_".strtoupper($host->name)]["id"] = "DhcpHost_".$this->module_id."_".$subnet_name."_".$host->name;
            $result["01_".strtoupper($host->name)]["title"] = $host->name."#".$host->title;
            if (substr($host->icon, 0,1)=="/" or substr($host->icon,0,5)=="skins")
                $result["01_".strtoupper($host->name)]["icon"] = $host->icon;
            else
                $result["01_".strtoupper($host->name)]["icon"] = $app->skinPath.$host->icon;
            $result["01_".strtoupper($host->name)]["parent"] = "DhcpSubnet_".$this->module_id."_".$subnet_name;
            $result["01_".strtoupper($host->name)]["loaded"] = "true";
        }
        @ksort($result);
        $res = array();
        foreach($result as $value)
        {
            $res[count($res)] = implode("~",$value);
        }
        echo implode("|",$res);
    }

    function getSharesTree() {
        global $Objects;
        $app = $Objects->get("Application");
        $result = array();
        if (!$app->initiated)
            $app->initModules();
        $server = $Objects->get("FileServer_".$this->module_id."_Shares");
        if (!$server->sharesLoaded)
            $server->loadShares();

        foreach ($server->shares as $share)
        {            
            if ($share->name!="root")
                $sharepath = $server->shares_root."/".$share->path;
            else
                $sharepath = $share->path;
            $result["01_".strtoupper($share->name)]["id"] = "FileShare_".$this->module_id."_".$share->idnumber;
            $result["01_".strtoupper($share->name)]["title"] = $share->name."#".$sharepath;
            $result["01_".strtoupper($share->name)]["icon"] = $share->icon;
            $result["01_".strtoupper($share->name)]["parent"] = "FileShares_".$this->module_id;
            $result["01_".strtoupper($share->name)]["loaded"] = "true";
        }
        @ksort($result);
        //uksort($result,"cmp");
        $res = array();
        foreach($result as $value)
        {
            $res[count($res)] = implode("~",$value);
        }
        echo implode("|",$res);
    }

    function getUsersTree() {
        global $Objects;
        $result = array();
        $server = $Objects->get("FileServer_".$this->module_id."_Shares");
        if (!$server->users_loaded) {
            $server->loadUsers(false);
        }

        foreach ($server->users as $user)
        {
            if ($user->name=="root" or $user->name=="")
                continue;
            $result["01_".$user->name]["id"] = "User_".$this->module_id."_".$user->name;
            $result["01_".$user->name]["title"] = $user->name;
            $result["01_".$user->name]["icon"] = $user->icon;
            $result["01_".$user->name]["parent"] = "Users_".$this->module_id;
            $result["01_".$user->name]["loaded"] = "true";
        }
        @ksort($result);
        $res = array();
        foreach($result as $value)
        {
            $res[count($res)] = implode("~",$value);
        }
        echo implode("|",$res);
    }

    function getGroupsTree() {
        global $Objects;
        $result = array();
        $server = $Objects->get("FileServer_".$this->module_id."_Shares");
        if (!$server->groups_loaded)
            $server->loadGroups(false);

        foreach ($server->groups as $group)
        {
            $result["01_".$group->name]["id"] = "Group_".$this->module_id."_".$group->name;
            $result["01_".$group->name]["title"] = $group->name;
            $result["01_".$group->name]["icon"] = $group->icon;
            $result["01_".$group->name]["parent"] = "Groups_".$this->module_id;
            $result["01_".$group->name]["loaded"] = "true";
        }
        @ksort($result);
        $res = array();
        foreach($result as $value)
        {
            $res[count($res)] = implode("~",$value);
        }
        echo implode("|",$res);
    }

    function getObjectGroupsTree() {
        global $Objects;
        $result = array();
        $server = $Objects->get("FileServer_".$this->module_id."_Shares");
        if (!$server->objectGroupsLoaded)
            $server->loadObjectGroups();

        foreach ($server->objectGroups as $share)
        {
            if ($share->idnumber==0)
                continue;
            $result["01_".$share->idnumber]["id"] = "ObjectGroup_".$this->module_id."_".$share->idnumber;
            $result["01_".$share->idnumber]["title"] = $share->name;
            $result["01_".$share->idnumber]["icon"] = $share->icon;
            $result["01_".$share->idnumber]["parent"] = "ObjectGroupProperties_".$this->module_id;
            $result["01_".$share->idnumber]["loaded"] = "true";
        }
        ksort($result);
        $res = array();
        foreach($result as $value)
        {
            $res[count($res)] = implode("~",$value);
        }
        echo implode("|",$res);
    }

    function getApacheUsersTree() {
        global $Objects;
        $result = array();
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $users = $Objects->get("ApacheUsers_".$this->module_id);
        $users->load();
        foreach ($users->apacheUsers as $user)
        {
            if (strlen($user->name)<1)
                continue;
            $result["01_".$user->name]["id"] = "ApacheUser_".$this->module_id."_".$user->name;
            $result["01_".$user->name]["title"] = $user->name;
            $result["01_".$user->name]["icon"] = $app->skinPath."images/Tree/user.gif";
            $result["01_".$user->name]["parent"] = "SystemSettingsUsers_".$this->module_id;
            $result["01_".$user->name]["loaded"] = "true";
        }
        ksort($result);
        $res = array();
        foreach($result as $value)
        {
            $res[count($res)] = implode("~",$value);
        }
        echo implode("|",$res);
    }

    function getModulesTree() {
    	global $Objects,$modules;
    	$result = array();
    	$app = $Objects->get("Application");
    	if (!$app->initiated)
    		$app->initModules();
    	foreach ($modules as $key=>$value)
    	{
    		$class = array_shift(explode("_",$value["class"]));
    		$result["01_".$key]["id"] = $class."ModuleConfig_".$this->module_id."_modules_".$key;
    		$result["01_".$key]["title"] = $value["title"];
    		$result["01_".$key]["icon"] = $app->skinPath."images/Tree/module.png";
    		$result["01_".$key]["parent"] = "Modules_".$this->module_id;
    		$result["01_".$key]["loaded"] = "true";
    	}
    	ksort($result);
    	$res = array();
    	foreach($result as $value)
    	{
    		$res[count($res)] = implode("~",$value);
    	}
    	echo implode("|",$res);
    }
    
    function getMetadataTree() {
    	$result = array();
    	
    	$result["20_09_ControlPanelMetadata"]["id"] = "MetadataTree_".$this->module_id."_MetadataFields";
    	$result["20_09_ControlPanelMetadata"]["title"] = l10n("Поля");
    	$result["20_09_ControlPanelMetadata"]["icon"] = $this->skinPath."images/Tree/fields.png";
    	$result["20_09_ControlPanelMetadata"]["parent"] = "Metadata_".$this->module_id;
    	$result["20_09_ControlPanelMetadata"]["loaded"] = "metadataArray=fields";
    	$result["20_09_ControlPanelMetadata"]["subtree"] = "true";
    	
    	$result["20_10_ControlPanelMetadata"]["id"] = "MetadataTree_".$this->module_id."_MetadataModels";
    	$result["20_10_ControlPanelMetadata"]["title"] = l10n("Модели");
    	$result["20_10_ControlPanelMetadata"]["icon"] = $this->skinPath."images/Tree/models.png";
    	$result["20_10_ControlPanelMetadata"]["parent"] = "Metadata_".$this->module_id;
    	$result["20_10_ControlPanelMetadata"]["loaded"] = "metadataArray=models";
    	$result["20_10_ControlPanelMetadata"]["subtree"] = "true";
    	
    	$result["20_11_ControlPanelMetadata"]["id"] = "MetadataTree_".$this->module_id."_MetadataCodes";
    	$result["20_11_ControlPanelMetadata"]["title"] = l10n("Алгоритмы");
    	$result["20_11_ControlPanelMetadata"]["icon"] = $this->skinPath."images/Tree/algo4.png";
    	$result["20_11_ControlPanelMetadata"]["parent"] = "Metadata_".$this->module_id;
    	$result["20_11_ControlPanelMetadata"]["loaded"] = "metadataArray=codes";
    	$result["20_11_ControlPanelMetadata"]["subtree"] = "true";

    	$result["20_12_ControlPanelAdmins"]["id"] = "Modules_".$this->module_id;
    	$result["20_12_ControlPanelAdmins"]["title"] = l10n("Модули");
    	$result["20_12_ControlPanelAdmins"]["icon"] = $this->skinPath."images/Tree/modules.png";
    	$result["20_12_ControlPanelAdmins"]["parent"] = "Metadata_".$this->module_id;
    	$result["20_12_ControlPanelAdmins"]["loaded"] = "false";
    	 
    	$result["20_13_ControlPanelMetadata"]["id"] = "MetadataTree_".$this->module_id."_MetadataPanels";
    	$result["20_13_ControlPanelMetadata"]["title"] = l10n("Панели");
    	$result["20_13_ControlPanelMetadata"]["icon"] = $this->skinPath."images/Tree/fields.png";
    	$result["20_13_ControlPanelMetadata"]["parent"] = "Metadata_".$this->module_id;
    	$result["20_13_ControlPanelMetadata"]["loaded"] = "metadataArray=panels#show_groups=0";
    	$result["20_13_ControlPanelMetadata"]["subtree"] = "true";
    	
    	$result["20_14_ControlPanelMetadata"]["id"] = "MetadataTree_".$this->module_id."_MetadataInterfaces";
    	$result["20_14_ControlPanelMetadata"]["title"] = l10n("Интерфейсы");
    	$result["20_14_ControlPanelMetadata"]["icon"] = $this->skinPath."images/Tree/interfaces.png";
    	$result["20_14_ControlPanelMetadata"]["parent"] = "Metadata_".$this->module_id;
    	$result["20_14_ControlPanelMetadata"]["loaded"] = "metadataArray=interfaces#show_groups=0";
    	$result["20_14_ControlPanelMetadata"]["subtree"] = "true";    	 
    	
    	$result["20_15_ControlPanelMetadata"]["id"] = "MetadataTree_".$this->module_id."_MetadataRoles";
    	$result["20_15_ControlPanelMetadata"]["title"] = l10n("Роли");
    	$result["20_15_ControlPanelMetadata"]["icon"] = $this->skinPath."images/Tree/role.gif";
    	$result["20_15_ControlPanelMetadata"]["parent"] = "Metadata_".$this->module_id;
    	$result["20_15_ControlPanelMetadata"]["loaded"] = "metadataArray=roles#show_groups=0";
    	$result["20_15_ControlPanelMetadata"]["subtree"] = "true";
    	 
    	$res = array();
    	foreach($result as $value)
    	{
    		$res[count($res)] = implode("~",$value);
    	}
    	echo trim(str_replace("\t","",implode("|",$res)));
    }

    function getReferencesTree() {
    	$result = array();
		if (is_object($this->capp->docFlowApp)) {    	 
	    	$result["20_09_DocFlowIntegrator"]["id"] = "ReferenceFiles_".$this->capp->docFlowApp->getId()."_List";
	    	$result["20_09_DocFlowIntegrator"]["title"] = l10n("Файлы");
	    	$result["20_09_DocFlowIntegrator"]["icon"] = $this->skinPath."images/docflow/file.png";
	    	$result["20_09_DocFlowIntegrator"]["parent"] = "DocFlowIntegrator_".$this->module_id."_Docs";
	    	$result["20_09_DocFlowIntegrator"]["loaded"] = "true";
	    	$result["20_09_DocFlowIntegrator"]["subtree"] = "false";
	    	
	    	$result["20_10_DocFlowIntegrator"]["id"] = "ReferenceUserInfoCard_".$this->capp->docFlowApp->getId()."_List";
	    	$result["20_10_DocFlowIntegrator"]["title"] = l10n("Пользователи");
	    	$result["20_10_DocFlowIntegrator"]["icon"] = $this->skinPath."images/Tree/user.png";
	    	$result["20_10_DocFlowIntegrator"]["parent"] = "DocFlowIntegrator_".$this->module_id."_Docs";
	    	$result["20_10_DocFlowIntegrator"]["loaded"] = "true";
	    	$result["20_10_DocFlowIntegrator"]["subtree"] = "false";
	
	    	$result["20_11_DocFlowIntegrator"]["id"] = "ReferenceGroupInfoCard_".$this->capp->docFlowApp->getId()."_List";
	    	$result["20_11_DocFlowIntegrator"]["title"] = l10n("Группы");
	    	$result["20_11_DocFlowIntegrator"]["icon"] = $this->skinPath."images/Tree/group.png";
	    	$result["20_11_DocFlowIntegrator"]["parent"] = "DocFlowIntegrator_".$this->module_id."_Docs";
	    	$result["20_11_DocFlowIntegrator"]["loaded"] = "true";
	    	$result["20_11_DocFlowIntegrator"]["subtree"] = "false";
	
	    	$result["20_12_DocFlowIntegrator"]["id"] = "ReferenceObjectGroupInfoCard_".$this->capp->docFlowApp->getId()."_List";
	    	$result["20_12_DocFlowIntegrator"]["title"] = l10n("Группы объектов");
	    	$result["20_12_DocFlowIntegrator"]["icon"] = $this->skinPath."images/Tree/objectgroup.png";
	    	$result["20_12_DocFlowIntegrator"]["parent"] = "DocFlowIntegrator_".$this->module_id."_Docs";
	    	$result["20_12_DocFlowIntegrator"]["loaded"] = "true";
	    	$result["20_12_DocFlowIntegrator"]["subtree"] = "false";
	    	
	    	$result["20_13_DocFlowIntegrator"]["id"] = "ReferenceDhcpHostInfoCard_".$this->capp->docFlowApp->getId()."_List";
	    	$result["20_13_DocFlowIntegrator"]["title"] = l10n("Хосты");
	    	$result["20_13_DocFlowIntegrator"]["icon"] = $this->skinPath."images/Window/system-settings.gif";
	    	$result["20_13_DocFlowIntegrator"]["parent"] = "DocFlowIntegrator_".$this->module_id."_Docs";
	    	$result["20_13_DocFlowIntegrator"]["loaded"] = "true";
	    	$result["20_13_DocFlowIntegrator"]["subtree"] = "false";
	    	 
	    	$result["20_14_DocFlowIntegrator"]["id"] = "ReferenceDhcpSubnetInfoCard_".$this->capp->docFlowApp->getId()."_List";
	    	$result["20_14_DocFlowIntegrator"]["title"] = l10n("Сети");
	    	$result["20_14_DocFlowIntegrator"]["icon"] = $this->skinPath."images/Tree/network.gif";
	    	$result["20_14_DocFlowIntegrator"]["parent"] = "DocFlowIntegrator_".$this->module_id."_Docs";
	    	$result["20_14_DocFlowIntegrator"]["loaded"] = "true";
	    	$result["20_14_DocFlowIntegrator"]["subtree"] = "false";
	
	    	$result["20_15_DocFlowIntegrator"]["id"] = "DeleteMarkedObjectsWindow_".$this->capp->docFlowApp->getId()."_List";
	    	$result["20_15_DocFlowIntegrator"]["title"] = l10n("Удаление");
	    	$result["20_15_DocFlowIntegrator"]["icon"] = $this->skinPath."images/Tree/delmail.png";
	    	$result["20_15_DocFlowIntegrator"]["parent"] = "DocFlowIntegrator_".$this->module_id."_Docs";
	    	$result["20_15_DocFlowIntegrator"]["loaded"] = "true";
	    	$result["20_15_DocFlowIntegrator"]["subtree"] = "false";
		}
    	 
    	$res = array();
    	foreach($result as $value)
    	{
    		$res[count($res)] = implode("~",$value);
    	}
    	echo trim(str_replace("\t","",implode("|",$res)));
    }
    
    function getArgs() {
        $result = parent::getArgs();
        if ($this->online_network_monitor_changed==true)
            $result["{online_network_monitor_changed}"] = "true";
        else
           $result["{online_network_monitor_changed}"] = "false";
        return $result;
    }

    function load() {
        global $Objects;
        $dhcpServer = $Objects->get("DhcpServer_".$this->module_id."_Network");
        if (!$dhcpServer->loaded)
            $dhcpServer->load();
        $this->online_network_monitor = $dhcpServer->online_monitor;
		$this->online_network_monitor_update_period = $dhcpServer->online_monitor_update_period;
        $this->loaded = true;
    }

    function getHostsActivity() {
        global $Objects;
        $app = $Objects->get("Application");
        $pingTest = $app->pingIpTestCommand;
        $shell = $Objects->get("Shell_shell");
        $arr = array();
        $dhcpSubnets = $Objects->get("DhcpSubnets_".$this->module_id."_Subs");
        if (!$dhcpSubnets->loaded)
            $dhcpSubnets->load();
        foreach ($dhcpSubnets->subnets as $subnet) {
            if (!$subnet->hostsLoaded)
                $subnet->loadHosts();
            foreach($subnet->hosts as $host) {
                if (!$host->loaded)
                    $host->load();
                $ip = $host->fixed_address;
                $test = $shell->exec_command(str_replace("{address}",$ip,$pingTest));
                if ($test==0)
                    $arr[$host->getId()] = "yes";
                else
                    $arr[$host->getId()] = "no";
            }
        }
        $result = array();
        foreach ($arr as $key=>$value) {
            $result[count($result)] = $key."~".$value;
        }
        return implode("|",$result);
    }

	function getHookProc($number) {
		switch ($number) {
			case '3': return "getSubnetsTree";
			case '4': return "getHostsTree";
			case '5': return "getSharesTree";
			case '6': return "getObjectGroupsTree";
			case '7': return "getUsersTree";
			case '8': return "getGroupsTree";
			case '9': return "getHostsActivityHook";
			case '10': return "getModulesTree";
			case '11': return "getMetadataTree";
			case '12': return "getReferencesTree";
		}
		return parent::getHookProc($number);
	}
	
	function getHostsActivityHook() {
		echo $this->getHostsActivity();
	}
}
?>