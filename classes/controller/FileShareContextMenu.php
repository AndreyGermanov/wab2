<?
class FileShareContextMenu extends ContextMenu {
    function construct($params)
    {
        parent::construct($params);
        $this->addItem("change","Изменить");
        $this->addItem("remove","Удалить");
        
        $this->clientClass = "FileShareContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";        
    }
}
?>