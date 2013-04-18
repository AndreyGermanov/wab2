<?
class FileSharesContextMenu extends ContextMenu {
    function construct($params)
    {
        parent::construct($params);
        $this->addItem("add", "Новая общая папка");
        
        $this->clientClass = "FileSharesContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";        
    }
}
?>