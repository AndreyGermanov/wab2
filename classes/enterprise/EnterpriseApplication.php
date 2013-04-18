<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Application
 *
 * @author andrey
 */
class EnterpriseApplication extends WABEntity {
    public $app;
    function  construct($params="")
    {
        global $Objects,$config_user;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->app = $app;
        if (isset($params[1])) {
            $this->module_id = @$params[0]."_".@$params[1];
            $this->object_id = @$params[1];
        }
        else
            $this->object_id = @$params[0];
        if (!isset($config_user))
            $config = $Objects->get("AdminConfig_".$_SERVER["PHP_AUTH_USER"]);
        else
            $config = $Objects->get("AdminConfig_".$config_user);
        $sysconfig = $config;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
                        
        $this->networkSettingsStyle = $sysconfig->networkSettingsStyle;
        $this->redHatNetworkSettingsFile = $sysconfig->redHatNetworkSettingsFile;
        $this->debianNetworkSettingsFile = $sysconfig->debianNetworkSettingsFile;
        $this->debianNetworkSettingsTemplateFile = $sysconfig->debianNetworkSettingsTemplateFile;
        $this->debianNetworkRestartCommand = $sysconfig->debianNetworkRestartCommand;
        $this->redHatNetworkRestartCommand = $sysconfig->redHatNetworkRestartCommand;

        $this->network_style = $this->networkSettingsStyle;
        if ($this->network_style=="RedHat") {
            $this->network_config_file = $this->redHatNetworkSettingsFile;
            $this->restart_command = $this->redHatNetworkRestartCommand;
        }
        else {
            $this->network_config_file = $this->debianNetworkSettingsFile;
            $this->network_config_template_file = $this->debianNetworkSettingsTemplateFile;
            $this->restart_command = $this->debianNetworkRestartCommand;
        }

        $this->rootPasswordFile = $app->rootPasswordFile;

        $this->adminsFile = $app->adminsFile;
        $this->apacheUsersTable = $this->adminsFile;

        $this->tree_init_string = '$object->icon = "'.$app->skinPath.'images/Window/collectormx.jpg";$object->title="LVA Enterprise Server";$object->setTreeItems();';

        $this->template = "templates/enterprise/EnterpriseApplication.html";
        $this->handler = "scripts/handlers/enterprise/EnterpriseApplication.js";
        $this->css=$app->skinPath."styles/MailApplication.css";
        $this->icon=$app->skinPath."images/Window/header-lva.gif";
        $shell = $Objects->get("Shell_shell");        
        $this->clientClass = "EnterpriseApplication";
        $this->parentClientClasses = "Entity";        
        $this->classTitle = "Гипервизор";
        $this->classListTitle = "Гипервизор";
    }

    function load()
    {        
        $this->loaded = true;
    }



    function getArgs()
    {
        if ($this->loaded=="") $this->load();
        $result = parent::getArgs();
        $result["{domains}"] = $this->mail_domain."|".$this->mail_domain;       
        if (trim($this->bootproto) == "dhcp")
                $result["{dhcp_checked}"] = "checked";
        else
            $result["{dhcp_checked}"] = "";
        return $result;
    }

    function getPresentation()
    {
        return "Панель управления";
    }

    function getId() {
        return "EnterpriseApplication_".$this->object_id;
    }
}
?>