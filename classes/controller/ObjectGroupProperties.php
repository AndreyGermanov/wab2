<?php
/**
 * Модуль окна свойств групп объектов
 *
 * @author andrey
 */
class ObjectGroupProperties extends WABEntity {
    
    function construct($params) {
        $this->module_id = array_shift($params)."_".array_shift($params);
        $this->name = implode("_",$params);
        $this->inheritObjectGroupRights = 0;
        $this->template = "templates/controller/ObjectGroupProperties.html";
        $this->handler = "scripts/handlers/mail/Mailbox.js";
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->intitiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;
        $this->icon = $app->skinPath."images/Tree/objectgroup.png";
        $this->css = $app->skinPath."styles/Mailbox.css";
        $this->width = 600;
        $this->overrided = "width";
        
        $this->clientClass = "ObjectGroupProperties";
        $this->parentClientClasses = "Entity";        
    }
    
    function getArgs() {
        if ($this->inheritObjectGroupRights)
            $this->inheritObjectGroupRightsStr = "1";
        else
            $this->inheritObjectGroupRightsStr = "0";
        return parent::getArgs();
    }
    
    function load() {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        if ($app->config->config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("InheritObjectGroupRights")->item(0)!=null)
            $inheritRights = $app->config->config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("InheritObjectGroupRights")->item(0)->getAttribute("value");
        if (!isset($inheritRights)) {
            $this->inheritObjectGroupRights = 0;
        } else {
            $this->inheritObjectGroupRights = $inheritRights;
        }
        $this->loaded = true;
    }
    
    function save() {
        global $Objects;
        $app = $Objects->get("Application");
        $shell = $Objects->get("Shell_shell");
        if (!$this->loaded)
            $this->load();
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $config = new DOMDocument();
        $config->load($app->config->user->config_file);
        if ($this->inheritObjectGroupRights)
            $value = "1";
        else
            $value = "0";
        if ($config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("InheritObjectGroupRights")->item(0)) {
            $el = $config->getElementsByTagName("FileServer")->item(0)->getElementsByTagName("InheritObjectGroupRights")->item(0);
            $el->setAttribute("value",$value);
        }
        if (!isset($el)) {
            $el = $config->createElement("InheritObjectGroupRights");
            $el->setAttribute("value",$value);
            $config->getElementsByTagName("FileServer")->item(0)->appendChild($el);
        }            
        $config->save($app->config->user->config_file);
        $this->loaded = true;
    }
    
    function getId() {
        return "ObjectGroupProperties_".$this->module_id."_".$this->name;
    }
    
    function getPresentation() {
        return "Настройка групп объектов";
    }   
}
?>