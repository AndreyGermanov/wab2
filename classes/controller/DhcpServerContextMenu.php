<?
class DhcpServerContextMenu extends ContextMenu {
    function construct($params)
    {
        parent::construct($params);
        $this->addItem("add", "Новая сеть");
        $this->addItem("report", "Отчет");
        $this->addItem("restart", "Перезапустить");
        
        $this->clientClass = "DhcpServerContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";        
    }
}
?>