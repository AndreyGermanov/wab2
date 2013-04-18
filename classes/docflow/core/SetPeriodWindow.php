<?php

/**
 * Класс реализует форму выбора периода для документов и отчетов
 *
 * @author andrey
 */
class SetPeriodWindow extends WABEntity {
    
    function construct($params) {
		parent::construct($params);
		$this->template = "templates/docflow/core/SetPeriodWindow.html";
		$this->periodStart = "";
		$this->periodEnd = time()."000";
		$this->width="350";
		$this->height="160";
		$this->overrided = "width,height";
		global $Objects;
		$app = $Objects->get("Application");
		if (!$app->initiated)
			$app->initModules();
		$this->skinPath = $app->skinPath;
		$this->icon = $this->skinPath."images/Tree/address.gif";
		$this->clientClass = "SetPeriodWindow";
		$this->parentClientClasses = "Entity";		
	}
	
	function getPresentation() {
		return "Установка периода";
	}
}
?>