<?
class DirectoryTreeRootContextMenu extends ContextMenu {
    function construct($params) {
        parent::construct($params);
        $this->addItem("add", "Новая папка");
        $this->addItem("addfile", "Новый файл");
        $this->clientClass = "DirectoryTreeRootContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";        
    }
}
?>