<?php
	class MailApplicationModuleConfig extends ModuleModelConfig {
		function construct($params) {
			parent::construct($params);
			$this->width = "630";
			$this->height = "550";
			$this->overrided = "width,height";				
		}
		
	}
?>