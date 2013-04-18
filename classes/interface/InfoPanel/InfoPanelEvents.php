<?php
class InfoPanelEvents extends WABEntity {
	
	function construct($params) {
		$this->name = "InfoPanelEvents";
		$this->template = "templates/interface/Table.html";
		$this->handler = "scripts/handlers/interface/InfoPanel/InfoPanelEvents.js";
		$this->clientClass = "InfoPanelEvents";
		$this->parentClientClasses = "Entity";
		global $models,$Objects;		
		if (isset($models["InfoPanelEvents"]))
			$this->models[] = "InfoPanelEvents";
		$app = $Objects->get("Application");
		if ($app->initiated)
			$app->initModules();
		$this->skinPath = $app->skinPath;
		$this->appUser = $app->User;
		$this->css = $this->skinPath."styles/interface/InfoPanel/InfoPanel.css";		
	}
	
	function getId() {
		return "InfoPanelEvents";
	}
	
	function getPresentation() {
		return "События";
	}
}
?>