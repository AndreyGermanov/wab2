<?
class ItemTemplateContextMenu extends ContextMenu {
    function construct($params)
    {
        parent::construct($params);
        $this->addItem("add","Новый шаблон");
        $this->addItem("change","Изменить");
        $this->addItem("remove","Удалить");
    }
}
?>
