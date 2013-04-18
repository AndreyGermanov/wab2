<?
class ApacheUsersContextMenu extends ContextMenu {
    function construct($params)
    {
        parent::construct($params);
        global $appconfig;
        if (@$appconfig["apacheAdminsBase"]!="ldap")
        	$this->addItem("add", "Новый пользователь");
        $this->clientClass = "ApacheUsersContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";        
    }
}
?>