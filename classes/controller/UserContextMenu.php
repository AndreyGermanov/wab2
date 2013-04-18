<?
class UserContextMenu extends ContextMenu {
    function construct($params)
    {
        parent::construct($params);
        $this->addItem("change","Изменить");
        $this->addItem("remove","Удалить");
        
        $this->handler = "scripts/handlers/controller/UserContextMenu.js";
        global $Objects,$appconfig;
        $user = $Objects->get("User_".$this->module_id."_".$this->name);
        $this->mailModule = $user->mailModule;
        $this->domain = $user->domain;
        $this->mailIntegration = $user->mailIntegration;
        $this->authType = @$appconfig["apacheAdminsBase"];
        $this->userName = $this->name;
        
        $this->clientClass = "UserContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";        
    }
}
?>