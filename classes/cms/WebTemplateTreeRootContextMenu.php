<?
class WebTemplateTreeRootContextMenu extends WABEntityRootContextMenu {
    function construct($params)
    {
        parent::construct($params);
        $this->addItem("add", "Новый");
        
        $this->clientClass = "WebTemplateTreeRootContextMenu";
        $this->parentClientClasses = "WABEntityRootContextMenu~ContextMenu~Entity";        
    }
}
?>