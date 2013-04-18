<?php
	class MetadataObjectTag extends WABEntity {
		
		public $params = array();
		
		function construct($params) {
			global $Objects;
			$this->module_id = array_shift($params)."_".array_shift($params);
			$this->rnd = array_shift($params);
			$this->name = implode("_",$params);
			$this->old_name = $this->name;
			$this->title = "";
			$this->old_title = "";
			$this->file = "/opt/WAB2/metadata/tags/tags.php";
			$this->old_file = "";
			$this->base = "";
			$this->type = "";
			$this->group = "";
			$this->fullGroup = "";			
			$this->template = "renderForm";
			$this->app = $Objects->get("Application");
			if (!$this->app->initiated)
				$this->app->initModules();
			$this->skinPath = $this->app->skinPath;
			$this->icon = $this->skinPath."images/Tree/tag.png";
			$this->css = $this->skinPath."styles/Mailbox.css";
			$this->handler = "scripts/handlers/metadata/MetadataObjectTag.js";
			$this->width = "450";
			$this->height = "350";
			$this->overrided = "width,height";
        	$this->clientClass = "MetadataObjectTag";
			$this->loaded = false;
			$this->field = "";
			$this->value = "";
		}
		
		function renderForm() {
			$blocks = getPrintBlocks(file_get_contents("templates/metadata/MetadataObjectTag.html"));
			$out = $blocks["header"];
			return $out;
		}
		
		function getId() {
			if ($this->module_id!="")
				return get_class($this)."_".$this->module_id."_".$this->rnd."_".$this->name;
			else
				return get_class($this)."_".$this->rnd."_".$this->name;
		}
		
		function getPresentation() {
			return $this->name;
		}
		
		function load() {
			global $tags;
			$name = $this->name;
			if (isset($tags[$name])) {
				$this->file = @$tags[$name]["file"];
				$this->field = @$tags[$name]["field"];
				$this->value = @$tags[$name]["value"];
				$this->old_file = $this->file;
				$this->loaded = true;
			}			
		}
		
		function checkData() {
			
			global $tags;				
			if ($this->name=="") {
				$this->reportError("Укажите 'системное имя'",'save');
				return 0;
			}
			if ($this->name!=$this->old_name) {
				if (isset($fields[$this->name])) {
					
					$this->reportError("Поле с таким именем уже существует",'save');
					return 0;
				}
			}
				
			if ($this->field=="") {
				$this->reportError("Укажите базовое поле","save");
				return 0;
			}
								
			if (!file_exists($this->file)) {
				$this->reportError("Указанный файл не существует");
				return 0;
			}				
			return 1;
		}
		
		function save($arguments) {
			if (!$this->loaded)
				$this->load();
			$this->setArguments($arguments);
			if ($this->checkData()) {
				if ($this->name!=$this->old_name and $this->old_name!="") {
					unset($GLOBALS["tags"][$this->old_name]);
					foreach($GLOBALS["tagGroups"] as $key=>$value) {
						if (isset($value[$this->old_name])) {
							unset($GLOBALS["tagGroups"][$key][$this->old_name]);
							$GLOBALS["tagGroups"][$key][$this->name] = $this->name;
							$str = "<?php\n".getMetadataString(getMetadataInFile($GLOBALS["tagGroups"][$key]["file"]))."\n?>";
							file_put_contents($GLOBALS["tagGroups"][$key]["file"],$str);						
						}
					}
				}
				$GLOBALS["tags"][$this->name] = array();
				$GLOBALS["tags"][$this->name]["field"] = $this->field;
				$GLOBALS["tags"][$this->name]["file"] = $this->file;
				$GLOBALS["tags"][$this->name]["value"] = $this->value;
				if ($this->file!=$this->old_file and file_exists($this->old_file)) {
					$str = "<?php\n".getMetadataString(getMetadataInFile($this->old_file))."\n?>";
					file_put_contents($this->old_file,$str);						
				}
				$str = "<?php\n".getMetadataString(getMetadataInFile($this->file))."\n?>";
				file_put_contents($this->file,$str);						
			}				
		}
		
		function remove($arguments) {
			$this->setArguments($arguments);
			if (isset($GLOBALS["tags"][$this->name])) {
				foreach($GLOBALS["tagGroups"] as $key=>$value) {
					if (isset($value[$this->name])) {
						unset($GLOBALS["tagGroups"][$key][$this->name]);
						$str = "<?php\n".getMetadataString(getMetadataInFile($GLOBALS["tagGroups"][$key]["file"]))."\n?>";
						file_put_contents($GLOBALS["tagGroups"][$key]["file"],$str);						
					}
				}
				$file = $GLOBALS["tags"][$this->name]["file"];
				unset($GLOBALS["tags"][$this->name]);
				$str = "<?php\n".getMetadataString(getMetadataInFile($file))."\n?>";
				file_put_contents($file,$str);				
			}
		}
		
		function getHookProc($number) {
			switch($number) {
				case '3': return "save";
				case '4': return "remove";
			}
			return parent::getHookProc($number);
		}
	}
?>