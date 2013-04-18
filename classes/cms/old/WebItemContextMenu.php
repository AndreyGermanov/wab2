<?
class WebItemContextMenu extends ContextMenu {
    function construct($params)
    {
        parent::construct($params);
        $this->addItem("add", "Новый раздел");
        $this->addItem("add_by_template", "Скопировать из");
        $this->addItem("add_item", "Новый элемент");
        $this->addItem("change", "Изменить");
        $this->addItem("up", "Вверх");
        $this->addItem("down", "Вниз");
        $this->addItem("remove","Удалить");
    }
}
?>