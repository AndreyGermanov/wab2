<?
class AddressBookContextMenu extends ContextMenu {
    function construct($params) {
        parent::construct($params);
        $this->addItem("add","Добавить");
        $this->addItem("insert","Вставить");
        $this->addItem("remove","Удалить");
        $this->addItem("move_up","Сдвинуть вверх");
        $this->addItem("move_down","Сдвинуть вниз");
        $this->clientClass = "AddressBookContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";        
    }
}
?>
