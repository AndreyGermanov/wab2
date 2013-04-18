<?
class ApacheUserContextMenu extends ContextMenu {
    function construct($params)
    {
        parent::construct($params);
        global $appconfig;
        $this->addItem("change", "Изменить");
        if (@$appconfig["apacheAdminsBase"]!="ldap")
			$this->addItem("remove","Удалить");
        $this->clientClass = "ApacheUserContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";        
    }
}
?>