<?
class WABEntityRootContextMenu extends ContextMenu {
    function construct($params)
    {
        parent::construct($params);
        $this->addItem("add", "Новый");
        
        $this->clientClass = "WABEntityRootContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";        
    }
}
?>