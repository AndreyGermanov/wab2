<?
class MailboxesContextMenu extends ContextMenu {
    function construct($params) {
        parent::construct($params);
        $this->addItem("change","Изменить");
        $this->addItem("remove","Удалить");
        $this->addItem("add", "Новый почтовый ящик");
        $this->addItem("add_list", "Новый список<br>рассылки");
    	$this->clientClass = "MailboxesContextMenu";
		$this->parentClientClasses = "ContextMenu~Entity";
    }
}
?>