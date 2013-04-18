<?php
	class ControllerApplicationModuleConfig extends ModuleModelConfig {
		function construct($params) {
			parent::construct($params);
			$this->width = "700";
			$this->height = "550";
			$this->overrided = "width,height";				
		}
		
		function getControlType($field_name,$properties) {
			global $modules;
			if ($field_name=="mailIntegration" or $field_name=="docFlowIntegration") {
				$names = array();
				$titles = array();
				foreach ($modules as $key=>$value) {
					$names[] = $key;
					$titles[] = $value["title"];
				}
				return "list,".implode("~",$names)."|".implode("~",$titles);
			}
			return @$properties["{".$field_name."_type}"];
		}				
	}
?>