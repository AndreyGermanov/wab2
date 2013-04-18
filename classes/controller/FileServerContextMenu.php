<?
class FileServerContextMenu extends ContextMenu {
    function construct($params)
    {
        parent::construct($params);
//        $this->addItem("report","Журнал событий");
        $this->addItem("restart","Перезапустить");
        
        $this->clientClass = "FileServerContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";        
    }
}
?>