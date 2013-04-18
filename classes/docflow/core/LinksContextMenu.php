<?
class LinksContextMenu extends ContextMenu {
    function construct($params) {
        parent::construct($params);
        $this->clientClass = "LinksContextMenu";
        $this->parentClientClasses = "ContextMenu~Entity";        
    }
    
    function getArgs() {
    	global $Objects;
    	$obj = $Objects->get($this->link_object);
    	$obj->setRole();
    	if ($obj->getRoleValue(@$obj->role["canEditLinks"])!="false") {
    		$this->addItem("add", "Новая связь");
    		$this->addItem("remove","Удалить связь");    	 
    	}
    	return parent::getArgs();
    }
}
?>