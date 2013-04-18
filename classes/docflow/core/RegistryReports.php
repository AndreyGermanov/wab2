<?php
class RegistryReports extends WABEntity {
		
	function construct($params) {
		
		parent::construct($params);
		global $Objects,$fields,$objGroups;
		$this->template = "renderForm";
		$this->app = $Objects->get("Application");
		if (!$this->app->initiated)
			$this->app->initModules();
		$this->skinPath = $this->app->skinPath;
		$this->icon = $this->skinPath."images/Tree/report.png";
		$this->width="700";
		$this->height = "550";
		
		$values = implode("~",$objGroups["registries"]["items"]);
		$titles = array();
		foreach ($objGroups["registries"]["items"] as $value) {
			$obj = $Objects->get($value."_".$this->module_id."_reg");
			$titles[] = $obj->classTitle;
		}
		
		$this->registries = " ~".$values."| ~".implode("~",$titles);
						
				
        $this->tabs_string  = "fields|Поля|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string  .= "sort|Сортировка|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string  .= "groups|Группировка|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string  .= "totals|Итоги|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string  .= "conditions|Отбор|".$this->skinPath."images/spacer.gif";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_".get_class($this).$this->name;
        $this->active_tab = "fields";
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/core/RegistryReports.html"));
		$out = $blocks["header"];
		return $out;
	}
	
	function getArgs() {
		$this->printProfileJSON = json_encode($this->printProfile);
		
		$this->tabsetCode = '$object->module_id="'.$this->module_id.'";
		$object->item="'.$this->getId().'";
		$object->window_id="'.$this->window_id.'";
		$object->tabs_string="'.$this->tabs_string.'";
		$object->active_tab="'.$this->active_tab.'";';

		$this->tabsetCode = cleanText($this->tabsetCode);
		
		return parent::getArgs();
	}
	
	function getPresentation() {
		return "Отчеты по регистрам";
	}
}