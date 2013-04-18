<?
class PrintContextMenu extends ContextMenu {
	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "PrintContextMenu";
		$this->parentClientClasses = "ContextMenu~Entity";		
	}
	
	function show() {
		if ($this->opener_object!="") {
			global $Objects;
			$printObject = $Objects->get($this->opener_object);
			$this->items = $printObject->getPrintForms();
			parent::show();
		}
	}
}
?>