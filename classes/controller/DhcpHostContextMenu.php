<?
class DhcpHostContextMenu extends ContextMenu {
    
    function construct($params) {
        parent::construct($params);       
        $this->addItem("change","Изменить");
        $this->addItem("control","Управлять");
        $this->addItem("remove","Удалить");
        
        $this->clientClass = "DhcpHostContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";        
    }
}
?>