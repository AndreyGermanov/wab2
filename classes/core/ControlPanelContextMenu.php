<?
class ControlPanelContextMenu extends ContextMenu {
    function construct($params) {
        parent::construct($params);
        $this->addItem("options","Параметры");
        $this->addItem("check","Проверить обновления");
        $this->clientClass = "ControlPanelContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";
    }
}
?>