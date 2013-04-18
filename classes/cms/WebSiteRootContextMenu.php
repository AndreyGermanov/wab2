<?
class WebSiteRootContextMenu extends WABEntityRootContextMenu {
    function construct($params)
    {
        parent::construct($params);
        $this->addItem("change", "Изменить");
        $this->addItem("remove", "Удалить");
        $this->addItem("add", "Новый раздел");
        $this->clientClass = "WebSiteRootContextMenu";
        $this->parentClientClasses = "WABEntityRootContextMenu~ContextMenu~Entity";        
    }
}
?>