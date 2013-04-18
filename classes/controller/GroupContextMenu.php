<?
class GroupContextMenu extends ContextMenu {
    function construct($params)
    {
        parent::construct($params);
        $this->addItem("change","Изменить");
        $this->addItem("remove","Удалить");

        $this->clientClass = "GroupContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";        
    }
}
?>