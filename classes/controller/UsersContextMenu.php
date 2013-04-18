<?
class UsersContextMenu extends ContextMenu {
    function construct($params)
    {
        parent::construct($params);
        $this->addItem("add", "Новый пользователь");
        
        $this->clientClass = "UsersContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";        
    }
}
?>