<?
class SpidermanTree extends Tree {

    public $userConfig;

    function  construct($params) {
        parent::construct($params);
        $this->module_id = @$params[0]."_".@$params[1];
        $this->object_id = @$params[2];
        $this->name = @$params[2];
        $this->handler = "scripts/handlers/cms/SpidermanTree.js";
        $this->clientClass = "SpidermanTree";
        $this->parentClientClasses = "Tree~Entity";        
    }

    function setTreeItems() {
        global $Objects;
        $lapp = $Objects->get($this->module_id);
        $show_sites = $lapp->showWebSites;
        $show_templates = $lapp->showTemplates;
        $add_web_sites = $lapp->addWebSites;
        $this->show_system_settings = $lapp->showSystemSettings;
        $result = array();
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        
        $result = array();
        if ($this->show_system_settings) {
            $result["01_01_SystemSettings"]["id"] = "SystemSettings_".$this->module_id;
            $result["01_01_SystemSettings"]["title"] = "Сервер";
            $result["01_01_SystemSettings"]["icon"] = $app->skinPath."images/Window/system-settings.gif";
            $result["01_01_SystemSettings"]["parent"] = "";
            $result["01_01_SystemSettings"]["loaded"] = "true";

            $result["02_01_SystemSettingsNetwork"]["id"] = "SystemSettingsNetwork_".$this->module_id;
            $result["02_01_SystemSettingsNetwork"]["title"] = "Сеть";
            $result["02_01_SystemSettingsNetwork"]["icon"] = $app->skinPath."images/Window/network-settings.gif";
            $result["02_01_SystemSettingsNetwork"]["parent"] = "SystemSettings_".$this->module_id;
            $result["02_01_SystemSettingsNetwork"]["loaded"] = "true";

            $result["03_01_SystemSettingsNetwork"]["id"] = "SystemSettingsUsers_".$this->module_id;
            $result["03_01_SystemSettingsNetwork"]["title"] = "Пользователи";
            $result["03_01_SystemSettingsNetwork"]["icon"] = $app->skinPath."images/Tree/user.gif";
            $result["03_01_SystemSettingsNetwork"]["parent"] = "SystemSettings_".$this->module_id;
            $result["03_01_SystemSettingsNetwork"]["loaded"] = "false";
        }
        
        if ($show_sites!=null) {
            $result["01_04_SystemSettingsNetwork"]["id"] = "WebSitesTree_".$this->module_id."_Sites";
            $result["01_04_SystemSettingsNetwork"]["title"] = "Сайты";
            $result["01_04_SystemSettingsNetwork"]["icon"] = $app->skinPath."images/Tree/sites.gif";
            $result["01_04_SystemSettingsNetwork"]["parent"] = "";
            $result["01_04_SystemSettingsNetwork"]["loaded"] = "className=WebSite#treeClassName=WebSiteTree#loaded=false#editorType=WABWindow#hierarchy=false";
            if (!$add_web_sites)
                $result["01_04_SystemSettingsNetwork"]["loaded"] .= "#hide_root_context_menu=true";
            $result["01_04_SystemSettingsNetwork"]["subtree"] = "true";
        }

        if ($show_templates!=null) {
            $result["01_05_SystemSettingsNetwork"]["id"] = "WebTemplateTree_".$this->module_id."_Templates";
            $result["01_05_SystemSettingsNetwork"]["title"] = "Шаблоны";
            $result["01_05_SystemSettingsNetwork"]["icon"] = $app->skinPath."images/Tree/templates.gif";
            $result["01_05_SystemSettingsNetwork"]["parent"] = "";
            $result["01_05_SystemSettingsNetwork"]["loaded"] = "className=WebTemplate#loaded=false#editorType=WABWindow#hierarchy=true";
            $result["01_05_SystemSettingsNetwork"]["subtree"] = "true";
        }
        
        if ($lapp->showUsers) {
        	$result["01_06_SystemSettingsNetwork"]["id"] = "ReferenceUsers_".$lapp->docflowClass."_List";
        	$result["01_06_SystemSettingsNetwork"]["title"] = "Пользователи";
        	$result["01_06_SystemSettingsNetwork"]["icon"] = $app->skinPath."images/Tree/user.gif";
        	$result["01_06_SystemSettingsNetwork"]["parent"] = "";
        	$result["01_06_SystemSettingsNetwork"]["loaded"] = "true";
        	$result["01_06_SystemSettingsNetwork"]["subtree"] = "false";

        	$result["01_07_SystemSettingsNetwork"]["id"] = "DeleteMarkedObjectsWindow_".$lapp->docflowClass."_List";
        	$result["01_07_SystemSettingsNetwork"]["title"] = l10n("Удаление объектов");
        	$result["01_07_SystemSettingsNetwork"]["icon"] = $this->skinPath."images/Tree/delmail.png";
        	$result["01_07_SystemSettingsNetwork"]["parent"] = "";
        	$result["01_07_SystemSettingsNetwork"]["loaded"] = "true";
        	$result["01_07_SystemSettingsNetwork"]["subtree"] = "false";
        	 
        }

        ksort($result);
        $res = array();
        foreach($result as $value)
        {
            $res[count($res)] = implode("~",$value);
        }
        $this->items_string = implode("|",$res);
    }

    function getSitesTree($arguments=null) {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $sites = $Objects->get($this->module_id);
        $sites->load();
		$lapp = $Objects->get($this->module_id);
        $valid_sites = array();
        $all = false;

		foreach($lapp->webSites as $site) {
	            $valid_sites[$site] = $site;
	            if ($site=="All") {
    	            $all = true;
    	        break;
	        }
        }
        
        foreach ($sites->sites as $site)
        {
            if (!$all and !isset($valid_sites[$site->domain_name]))
                continue;
            $result["01_".$site->name]["id"] = "WebSite_".$this->module_id."_".$site->name;
            $result["01_".$site->name]["title"] = $site->domain_name;
            $result["01_".$site->name]["icon"] = $app->skinPath."images/Tree/sites.gif";
            $result["01_".$site->name]["parent"] = "Sites";
            $result["01_".$site->name]["loaded"] = "false";
        }
        ksort($result);
        $res = array();
        foreach($result as $value)
        {
            $res[count($res)] = implode("~",$value);
        }
        echo implode("|",$res);
    }

    function getTemplatesTree($parent_id="0") {
    	if (is_array($parent_id)) {
    		$parent_id = $parent_id["parent_id"];
    	}
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $templates = $Objects->get("ItemTemplates_".$this->module_id);
        if ($parent_id!="0") {
            $templates->connect();
            $tpl = $Objects->get("ItemTemplate_".$this->module_id."_".$parent_id);
            $tpl->load();
            $parent_parent_id = $tpl->parent_id;
        }
        $templates_array = $templates->load($parent_id);
        foreach ($templates_array as $template) {
            $result["01_".$template["title"]]["id"] = "ItemTemplate_".$this->module_id."_".$template["id"]."_".$template["parent_id"];
            $result["01_".$template["title"]]["title"] = $template["title"];
            $result["01_".$template["title"]]["icon"] = $app->skinPath."images/Tree/templates.gif";
            if ($parent_id=="0")
                $result["01_".$template["title"]]["parent"] = "Templates";
            else
                $result["01_".$template["title"]]["parent"] = "ItemTemplate_".$this->module_id."_".$parent_id."_".$parent_parent_id;
            $arr = $templates->load($template["id"]);
            if ($arr!=0)
                $result["01_".$template["title"]]["loaded"] = "false";
            else
                $result["01_".$template["title"]]["loaded"] = "true";
        }
        ksort($result);
        $res = array();
        foreach($result as $value)
        {
            $res[count($res)] = implode("~",$value);
        }
        echo implode("|",$res);
    }

    function getSiteRootTree($site,$parent=1,$parent_parent=0,$parent_open_as="") {
        global $Objects;
        if (is_array($site))
        	$site = $site["site"];
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $site_root = $Objects->get("WebItem_".$this->module_id."_".$site."_".$parent);
        if (!$site_root->loaded)
            $site_root->load();
        $items = $site_root->getChildren(false,0,0,$site_root->sort_order);
        $website = $Objects->get("WebSite_".$this->module_id."_".$site);
        $website->connect();
        if ($items==FALSE)
            return 0;            

        foreach ($items as $item)
        {   
            $parent = $item->getNode()->getParent()->id;
            $result["01_".$item->name]["id"] = $item->class."_".$this->module_id."_".$site."_".$item->id."_".$parent."_asAdminList";
            $result["01_".$item->name]["title"] = $item->title;
            $result["01_".$item->name]["icon"] = $item->icon;
            if ($parent_parent==0)
                $result["01_".$item->name]["parent"] = "WebSite_".$this->module_id."_".$site;
            else
                $result["01_".$item->name]["parent"] = $item->getNode()->getParent()->class."_".$this->module_id."_".$site."_".$parent."_".$parent_parent."_".$parent_open_as;
            if ($item->getNode()->isLeaf()) {
                $result["01_".$item->name]["loaded"] = "true";                
            }
            else {
                $result["01_".$item->name]["loaded"] = "false";                
            }
        }
        $res = array();
        foreach($result as $value)
        {
            $res[count($res)] = implode("~",$value);
        }
        echo implode("|",$res);
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
    		case '3': return "getSitesTree";
    		case '4': return "getTemplatesTree";
    		case '5': return "getSiteRootTree";
    		case '6': return "getSiteRootTreeHook";
    		case '7': return "getApacheUsersTree";
    	}
    }
    
    function getSiteRootTreeHook($arguments) {
    	$this->getSiteRootTree($arguments["site"],$arguments["item"],$arguments["parent_item"],$arguments["elem_end"]);
    }
}
?>