<?
class MailAliasAddressContextMenu extends ContextMenu {
    function construct($params)
    {
        parent::construct($params);
        $this->addItem("change", "Изменить");
        $this->addItem("remove","Удалить");
        $this->clientClass = "MailAliasAddressContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";        
    }
}
?>