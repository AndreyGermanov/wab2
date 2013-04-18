<?
class WebSitesContextMenu extends ContextMenu {
    function construct($params)
    {
        parent::construct($params);
        $this->addItem("add","Новый Web-сайт");
    }
}
?>
