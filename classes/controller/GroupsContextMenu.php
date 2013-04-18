<?
class GroupsContextMenu extends ContextMenu {
    function construct($params) {
        parent::construct($params);
        $this->addItem("add", "Новая группа");
        
        $this->clientClass = "GroupsContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";        
    }
}
?>