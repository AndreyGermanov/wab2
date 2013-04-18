<?
class DomainsContextMenu extends ContextMenu {
    function construct($params) {
        parent::construct($params);
        $this->addItem("add", "Новый почтовый домен");
        $this->clientClass = "DomainsContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";        
    }
}
?>