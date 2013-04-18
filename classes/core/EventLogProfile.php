<?php
class EventLogProfile extends EntityProfile {
	
	public $ruleNames = array();
	public $eventNames = array();
	
	function construct($params) {
		parent::construct($params);
		$this->tabs_string = "main|Основное|".$this->skinPath."images/spacer.gif;";
		$this->tabs_string.= "events|События|".$this->skinPath."images/spacer.gif";
		$this->active_tab = "main";			
		$this->ruleNames = array ("canRead","canSetSettings","canEditProfile");
		$this->eventNames = array ("*","ENTITY_OPENED","ENTITY_CLOSED");
		$this->width = "600";
		$this->height = "450";
		$this->overrided = "width,height"; 				
	}
	
	function getId() {
		return get_class($this)."_".$this->module_id."_".$this->role."_".$this->profile;
	}
	
	function renderForm_2() {
		return "";		
	}
	
	function saveProfile_2($arguments) {
	}	
}