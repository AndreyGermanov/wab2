<?
class DirectoryTreeContextMenu extends ContextMenu {
    function construct($params) {
        parent::construct($params);
        $this->addItem("add", "Новая папка");
        $this->addItem("addfile", "Новый файл");
        $this->addItem("change", "Изменить");
        $this->addItem("remove","Удалить");
        $this->clientClass = "DirectoryTreeContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";        
    }
}
?>