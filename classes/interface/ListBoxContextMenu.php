<?
class ListBoxContextMenu extends ContextMenu {
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "ListBoxContextMenu";
		$this->parentClientClasses = "ContextMenu~Entity";		
	}
		
    function fillMenu() {
        $arr = explode("|",$this->item_string);
        $values = explode("~",$arr[0]);
        $titles = explode("~",$arr[1]);
        for ($counter=0;$counter<count($values);$counter++) {
            $this->addItem($titles[$counter],$titles[$counter]);
        }
    }
    
    function getHookProc($number) {
    	switch($number) {
    		case '3': return "fillMenuHook";
    	}
    	return parent::getHookProc($number);
    }
    
    function fillMenuHook($arguments) {
    	$this->setArguments($arguments);
    	$this->fillMenu();
    }
}
?>