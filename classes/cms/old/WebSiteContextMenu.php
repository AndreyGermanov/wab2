<?
class WebSiteContextMenu extends ContextMenu {
    function construct($params)
    {
        parent::construct($params);
        $this->addItem("change", "Изменить");
        $this->addItem("remove","Удалить");
        $this->addItem("add_chapter","Новый раздел");
    }
}
?>
