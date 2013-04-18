<?
class ObjectGroupsContextMenu extends ContextMenu {
    function construct($params) {
        parent::construct($params);
        $this->addItem("add", "Новая группа объектов");
        $this->clientClass = "ObjectGroupsContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";        
    }
}
?>