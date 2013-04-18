<?
class MailAliasAddressesContextMenu extends ContextMenu {
    function construct($params) {
        parent::construct($params);
        $this->addItem("add","Новый адресат");
        $this->clientClass = "MailAliasAddressesContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";        
    }
}
?>