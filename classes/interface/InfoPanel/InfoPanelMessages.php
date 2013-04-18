<?php
class InfoPanelMessages extends WABEntity {
	
	function construct($params) {
		$this->name = "InfoPanel";
		$this->template = "templates/interface/Table.html";
		$this->handler = "scripts/handlers/interface/InfoPanel/InfoPanelMessages.js";
		$this->clientClass = "InfoPanelMessages";
		$this->parentClientClasses = "Entity";
		global $models,$Objects;		
		if (isset($models["InfoPanelMessages"]))
			$this->models[] = "InfoPanelMessages";
		$app = $Objects->get("Application");
		if ($app->initiated)
			$app->initModules();
		$this->skinPath = $app->skinPath;
		$this->appUser = $app->User;
		$this->css = $this->skinPath."styles/interface/InfoPanel/InfoPanel.css";		
	}
	
	function getId() {
		return "InfoPanelMessages";
	}
	
	function getPresentation() {
		return "Сообщения";
	}
}
?>