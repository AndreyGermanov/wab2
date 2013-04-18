<?
class EnterpriseTree extends Tree {

    function  construct($params) {
        parent::construct($params);
        $this->module_id = @$params[0]."_".@$params[1];
        $this->object_id = @$params[2];
        $this->name = @$params[2];
        $this->handler = "scripts/handlers/enterprise/EnterpriseTree.js";
        $this->online_network_monitor = "";
        $this->online_network_monitor_changed = false;
        $this->loaded = true;
        $this->clientClass = "EnterpriseTree";
        $this->parentClientClasses = "Tree~Entity";        
    }

    function setTreeItems() {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->userConfig = $Objects->get("AdminConfig_".@$_SERVER["PHP_AUTH_USER"])->modules[$this->module_id];
        $result = array();

        $result["01_01_ControlPanel"]["id"] = "LocalSettings_".$this->module_id;
        $result["01_01_ControlPanel"]["title"] = "Сервер";
        $result["01_01_ControlPanel"]["icon"] = $app->skinPath."images/Tree/host.gif";
        $result["01_01_ControlPanel"]["parent"] = "";
        $result["01_01_ControlPanel"]["loaded"] = "true";

        $result["02_01_ControlPanel"]["id"] = "VirtualBox_".$this->module_id;
        $result["02_01_ControlPanel"]["title"] = "Виртуальные машины";
        $result["02_01_ControlPanel"]["icon"] = $app->skinPath."images/Tree/virtualbox.png";
        $result["02_01_ControlPanel"]["parent"] = "";
        $result["02_01_ControlPanel"]["loaded"] = "true";
        
        $result["07_06_ControlPanel"]["id"] = "ControlPanel_".$this->module_id;
        $result["07_06_ControlPanel"]["title"] = "Панель управления";
        $result["07_06_ControlPanel"]["icon"] = $app->skinPath."images/Tree/control_panel.png";
        $result["07_06_ControlPanel"]["parent"] = "";
        $result["07_06_ControlPanel"]["loaded"] = "true";

        $result["07_06_ControlPanelAdmins"]["id"] = "SystemSettingsUsers_".$this->module_id;
        $result["07_06_ControlPanelAdmins"]["title"] = "Администраторы";
        $result["07_06_ControlPanelAdmins"]["icon"] = $app->skinPath."images/Tree/user.gif";
        $result["07_06_ControlPanelAdmins"]["parent"] = "ControlPanel_".$this->module_id;
        $result["07_06_ControlPanelAdmins"]["loaded"] = "false";

        $result["08_01_ControlPanelAdmins"]["id"] = "HTMLBook_".$this->module_id."_enterprise_1";
        $result["08_01_ControlPanelAdmins"]["title"] = "Документация";
        $result["08_01_ControlPanelAdmins"]["icon"] = $app->skinPath."images/Tree/docs.png";
        $result["08_01_ControlPanelAdmins"]["parent"] = "";
        $result["08_01_ControlPanelAdmins"]["loaded"] = "true";

        ksort($result);
        $res = array();
        foreach($result as $value)
        {
            $res[count($res)] = implode("~",$value);
        }
        $this->items_string = implode("|",$res);
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

    function getArgs() {
       $result = parent::getArgs();
       return $result;
    }

    function load() {
        $this->loaded = true;
    }        
}
?>