<?
class WebTemplateTreeContextMenu extends ContextMenu {
    function construct($params)
    {
        parent::construct($params);
        $this->addItem("add", "Новый");
        $this->addItem("add_by_template", "Скопировать из");
        $this->addItem("change", "Изменить");
        $this->addItem("remove","Удалить");
        
        $this->clientClass = "WebTemplateTreeContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";        
    }
}
?>