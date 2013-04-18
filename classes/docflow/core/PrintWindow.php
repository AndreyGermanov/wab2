<?
class PrintWindow extends WABEntity {

	function construct($params) {
		parent::construct($params);
		$arr = explode("_",$this->name);
		$this->printForm = array_pop($arr);
		$this->printObject = implode("_",$arr);
		$this->frameId = $this->object_id."_frame";
		$this->template="templates/docflow/core/PrintWindow.html";
		$this->initstring = "";
		global $Objects;
		$app = $Objects->get("Application");
		$this->skinPath = $app->skinPath;
		$this->icon = $this->skinPath."images/Tree/printer.png";
		$this->clientClass = "PrintWindow";
		$this->parentClientClasses = "Entity";		
	}
	
	function getArgs() {		
		$this->initstring = str_replace("xoxoxo","'",str_replace("yoyoyo",'"',$this->initstring));
		return parent::getArgs();
	}
	
	function getPresentation() {
		global $Objects;
		$obj = $Objects->get($this->printObject);
		return $obj->getPresentation();
	}
}
?>