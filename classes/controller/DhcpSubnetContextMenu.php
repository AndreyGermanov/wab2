<?
class DhcpSubnetContextMenu extends ContextMenu {
    function construct($params)
    {
        parent::construct($params);
        $this->addItem("change","Изменить");
        $this->addItem("remove","Удалить");
        $this->addItem("add", "Новый хост");
        
        $this->clientClass = "DhcpSubnetContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";        
    }
}
?>