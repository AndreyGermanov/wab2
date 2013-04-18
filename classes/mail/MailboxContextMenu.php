<?
class MailboxContextMenu extends ContextMenu {
    function construct($params) {
        parent::construct($params);
        $this->addItem("change", "Изменить");
        $this->addItem("remove","Удалить");
        $this->addItem("add_remote_mailbox","Новый почтовый ящик<br>Интернет");
        $this->clientClass = "MailboxContextMenu";
		$this->parentClientClasses = "ContextMenu~Entity";
    }
}
?>