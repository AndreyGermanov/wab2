<?
class ObjectGroupContextMenu extends ContextMenu {
    function construct($params)
    {
        parent::construct($params);
        $this->addItem("change","Изменить");
        $this->addItem("remove","Удалить");
        
        $this->clientClass = "ObjectGroupContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";        
    }
}
?>