<?
class AddressContextMenu extends ContextMenu {
    function construct($params) {
        parent::construct($params);
        $this->addItem("change", "Изменить");
        $this->addItem("remove","Удалить");
        $this->clientClass = "AddressContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";        
    }
}
?>