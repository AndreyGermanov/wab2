<?
class ApplicationContextMenu extends ContextMenu {
    function construct($params)
    {
        parent::construct($params);
        $this->addItem("setip","Изменить IP-адрес");
        $this->clientClass = "ApplicationContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";
    }
}
?>