<?
class CreateObjectContextMenu extends ContextMenu {
	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "CreateObjectContextMenu";
		$this->parentClientClasses = "ContextMenu~Entity";		
	}
	
	function show() {
		if ($this->opener_object!="") {
			global $Objects;
			$obj = $Objects->get($this->opener_object);
			$this->items = $obj->getCreateObjectList();
			parent::show();
		}
	}
}
?>