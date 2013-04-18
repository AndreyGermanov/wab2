<?
class RemoteMailboxContextMenu extends ContextMenu {
    function construct($params) {
        parent::construct($params);
        $this->addItem("change", "Изменить");
        $this->addItem("remove","Удалить");
        $this->clientClass = "RemoteMailboxContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";        
    }
}
?>