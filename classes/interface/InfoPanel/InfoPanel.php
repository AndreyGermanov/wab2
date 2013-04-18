<?php	
class InfoPanel extends WABEntity {
		
	function construct($params) {
		parent::construct($params);
		$this->name = "InfoPanel";		
		$this->template = "RenderForm";
		$this->handler = "scripts/handlers/interface/InfoPanel/InfoPanel.js";
		global $Objects,$models;
		$app = $Objects->get("Application");
		if (!$app->initiated)
			$app->initModules();
		if (isset($models["InfoPanel"]))
			$this->models[] = "InfoPanel";
		$this->skinPath = $app->skinPath;
		$this->icon = $app->skinPath."images/Tree/info.png";
		$this->clientClass = "InfoPanel";
		$this->height = "300";
		$this->top = "600";
		$this->overrided = "height,top";
		$this->parentClientClasses = "Entity";		
		$this->tabs_string= "messages|Сообщения|".$this->skinPath."images/Tree/info.png;";
		$this->tabs_string.= "events|События|".$this->skinPath."images/Tree/RemoteMailbox.gif";
		$this->eventsStr = "";
		$this->tabset_id = "WebItemTabset_InfoPanelTabset";
		$this->active_tab = "messages";
        $this->classTitle = "Информационная панель";
        $this->classListTitle = "Информационная панель";
	}
	
	function getId() {
		return "InfoPanel";
	}
	
	function load() {
		$this->loaded = true;
	}
	
	function getPresentation() {
		return "Информация";
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/interface/InfoPanel/InfoPanel.html"));
		$out  = $blocks["header"];
		$out .= $blocks["tabset"];
		$out .= $blocks["messages"];
		$out .= $blocks["events"];
		$out .= $blocks["footer"];
		return $out;
	}
	
	function getArgs() {
		global $events,$Objects;
		if (count($this->role)==0)
			$this->setRole();
		$roleEventTypes = $this->getRoleValue($this->role["eventTypes"]);
		$roleEvents = array();
		$eventTitles = array();
		foreach ($roleEventTypes as $value) {
			if (isset($events[$value])) {
				$roleEvents[$value] = $events[$value];
				$eventTitles[] = $events[$value]["title"];
			}
		}
		$this->eventNames = implode("~",array_keys($roleEvents));
		$this->eventTitles = implode("~",$eventTitles);
		$this->eventsStr = json_encode($roleEvents);
		
		$users = $Objects->get("ApacheUsers_users");
		if (!$users->loaded)
			$users->load();
		$usersList = array();
		foreach ($users->apacheUsers as $value) {
			$usersList[$value->name] = $value->name;
		}
		$this->userNames = implode("~",$usersList);
		
		return parent::getArgs();		
	}
}
?>