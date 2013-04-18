<?php
	class MetadataObjectField extends WABEntity {
		
		public $params = array();
		
		function construct($params) {
			global $Objects;
			$this->module_id = array_shift($params)."_".array_shift($params);
			$this->rnd = array_shift($params);
			$this->name = implode("_",$params);
			$this->old_name = $this->name;
			$this->title = "";
			$this->old_title = "";
			$this->file = "/opt/WAB2/metadata/fields/fields.php";
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
			$this->icon = $this->skinPath."images/Tree/field.png";
			$this->css = $this->skinPath."styles/Mailbox.css";
			$this->handler = "scripts/handlers/metadata/MetadataObjectField.js";
			$this->width = "450";
			$this->height = "350";
			$this->overrided = "width,height";
			$this->fieldParamsTable = "MetadataFieldParamsTable_".$this->module_id."_".$this->name."Table";
        	$this->clientClass = "MetadataObjectField";
			$this->loaded = false;
		}
		
		function renderForm() {
			$blocks = getPrintBlocks(file_get_contents("templates/metadata/MetadataObjectField.html"));
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
			global $fields;
			$name = $this->name;
			if (isset($fields[$name])) {
				$params = $fields[$name]["params"];
				$this->file = @$fields[$name]["file"];
				$this->type = @$fields[$name]["type"];
				$this->old_file = $this->file;
				$this->title = @$params["title"];
				$this->old_title = $this->title;
				$this->base = @$fields[$name]["base"];
				$this->params = $params;				
				$this->loaded = true;
			}			
		}
		
		function checkData() {
			
			global $fields;				
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
				
			if ($this->title=="") {
				$this->reportError("Укажите 'описание'","save");
				return 0;
			}
				
			if ($this->type=="" and $this->base=="") {
				$this->reportError("Укажите 'тип'","save");
				return 0;
			}
				
			if ($this->base!="" and !isset($fields[$this->base])) {
				$this->reportError("Указанное базовое поле не существует");
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
			$this->params = @$arguments["params"];
			$this->params = (array)$this->params;
			if ($this->checkData()) {
				if ($this->params!="" and is_array($this->params)) {
					$this->params = array_unique($this->params);
				}
				if ($this->name!=$this->old_name and $this->old_name!="") {
					unset($GLOBALS["fields"][$this->old_name]);
					foreach($GLOBALS["groups"] as $key=>$value) {
						$item = array_search($this->old_name,@$value["fields"]);
						if ($item!==FALSE) {
							$GLOBALS["groups"][$key]["fields"][$item] = $this->name;
							$str = "<?php\n".getMetadataString(getMetadataInFile($GLOBALS["groups"][$key]["file"]))."\n?>";
							file_put_contents($GLOBALS["groups"][$key]["file"],$str);						
						}
					}
					foreach($GLOBALS["models"] as $key=>$value) {
						if (isset($value[$this->old_name])) {
							unset($GLOBALS["models"][$key][$this->old_name]);
							$GLOBALS["models"][$key][$this->name] = $this->name;
							$str = "<?php\n".getMetadataString(getMetadataInFile($GLOBALS["models"][$key]["file"]))."\n?>";
							file_put_contents($GLOBALS["models"][$key]["file"],$str);						
						}
					}
				}
				if ($this->old_name=="" and $this->group!="") {
					$GLOBALS["groups"][$this->group]["fields"][] = $this->name;
					$str = "<?php\n".getMetadataString(getMetadataInFile($GLOBALS["groups"][$this->group]["file"]))."\n?>";
					file_put_contents($GLOBALS["groups"][$this->group]["file"],$str);
						
				}
				$GLOBALS["fields"][$this->name] = array();
				$GLOBALS["fields"][$this->name]["type"] = $this->type;
				if ($this->base!="")
					$GLOBALS["fields"][$this->name]["base"] = $this->base;
				$GLOBALS["fields"][$this->name]["file"] = $this->file;
				$GLOBALS["fields"][$this->name]["params"] = $this->params;
				$GLOBALS["fields"][$this->name]["params"]["title"] = $this->title;
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
			if (isset($GLOBALS["fields"][$this->name])) {
				foreach($GLOBALS["groups"] as $key=>$value) {
					$item = @array_search($this->name,@$value["fields"]);
					if ($item!==FALSE) {
						unset($GLOBALS["groups"][$key]["fields"][$item]);
						$str = "<?php\n".getMetadataString(getMetadataInFile($GLOBALS["groups"][$key]["file"]))."\n?>";
						file_put_contents($GLOBALS["groups"][$key]["file"],$str);						
					}
				}
				foreach($GLOBALS["models"] as $key=>$value) {
					if (isset($value[$this->name])) {
						unset($GLOBALS["models"][$key][$this->name]);
						$str = "<?php\n".getMetadataString(getMetadataInFile($GLOBALS["models"][$key]["file"]))."\n?>";
						file_put_contents($GLOBALS["models"][$key]["file"],$str);						
					}
				}
				$file = $GLOBALS["fields"][$this->name]["file"];
				unset($GLOBALS["fields"][$this->name]);
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