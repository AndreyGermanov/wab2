<?
class MailAliasContextMenu extends ContextMenu {
    function construct($params) {
        parent::construct($params);
        $this->addItem("change", "Изменить");
        $this->addItem("remove","Удалить");
        $this->clientClass = "MailAliasContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";        
    }
}
?>