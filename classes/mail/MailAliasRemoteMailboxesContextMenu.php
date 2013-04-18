<?
class MailAliasRemoteMailboxesContextMenu extends ContextMenu {
    function construct($params) {
        parent::construct($params);        
        $this->addItem("add_remote_mailbox","Новый почтовый ящик<br>Интернет");
        $this->clientClass = "MailAliasRemoteMailboxesContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";        
    }
}
?>