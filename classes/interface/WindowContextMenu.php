<?
class WindowContextMenu extends ContextMenu {   
    function construct($params) {
        parent::construct($params);
		global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
        $this->addItem("saveSettings","Прикрепить окно");
       	$this->addItem("removeSettings","Открепить окно");        
        $this->addItem("addAutorun","Добавить в автозагрузку");
       	$this->addItem("removeAutorun","Удалить из автозагрузки");        
       	$this->clientClass = "WindowContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";        
    }
}
?>