<?
class AddressBookTreeContextMenu extends ContextMenu {
    function construct($params) {
        parent::construct($params);
        $this->addItem("add", "Новый адрес");
        $this->clientClass = "AddressBookTreeContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";        
    }
}
?>