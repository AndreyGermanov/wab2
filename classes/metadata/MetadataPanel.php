<?php
	class MetadataPanel extends WABEntity {
		
		public $modules = array();
		
		function construct($params) {
			global $Objects;
			$this->module_id = array_shift($params)."_".array_shift($params);
			$this->rnd = array_shift($params);
			$this->name = implode("_",$params);
			$this->old_name = $this->name;
			$this->title = "";
			$this->old_title = "";
			$this->file = "/opt/WAB2/metadata/panels/panels.php";
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
			$this->icon = $this->skinPath."images/Tree/panel.png";
			$this->css = $this->skinPath."styles/Mailbox.css";
			$this->handler = "scripts/handlers/metadata/MetadataPanel.js";
			$this->width = "450";
			$this->height = "350";
			$this->overrided = "width,height";
			$this->modulesTable = "MetadataArrayTable_".$this->module_id."_".$this->name."Table";
        	$this->clientClass = "MetadataPanel";
			$this->loaded = false;
		}
		
		function renderForm() {
			$blocks = getPrintBlocks(file_get_contents("templates/metadata/MetadataPanel.html"));
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
			global $panels;
			$name = $this->name;
			if (isset($panels[$name])) {
				$modules = $panels[$name]["modules"];
				$modules = array_keys(array_unique($modules));
				$this->file = @$panels[$name]["file"];
				$this->old_file = $this->file;
				$this->title = @$panels[$name]["metaTitle"];
				$this->old_title = $this->title;
				$this->modules = $modules;				
				$this->defaultModule = $panels[$name]["defaultModule"];
				$this->loaded = true;
			}			
		}
		
		function checkData() {
			
			global $panels;				
			if ($this->name=="") {
				$this->reportError("Укажите 'системное имя'",'save');
				return 0;
			}
			if ($this->name!=$this->old_name) {
				if (isset($panels[$this->name])) {
					
					$this->reportError("Поле с таким именем уже существует",'save');
					return 0;
				}
			}
				
			if ($this->title=="") {
				$this->reportError("Укажите 'описание'","save");
				return 0;
			}
			
			if (count($this->modules)<=0) {
				$this->reportError("Укажите хотя-бы один модуль !",'save');
				return 0;
			};
			
			if (!isset($this->modules[$this->defaultModule])) {
				$this->reportError("Указанного модуль по умолчанию нет в таблицей модулей этой панели управления !",'save');
				return 0;
			};
								
			if (!file_exists($this->file)) {
				$this->reportError("Указанный файл не существует");
				return 0;
			};
			
			return 1;
		}
		
		function save($arguments) {
			global $modules;
			if (!$this->loaded)
				$this->load();
			$this->setArguments($arguments);
			$this->modules = @$arguments["modules"];
			$this->modules = (array)$this->modules;
			if ($this->checkData()) {
				if ($this->modules!="" and is_array($this->modules)) {
					$this->modules = array_unique($this->modules);
				}
				foreach($this->modules as $key=>$value)
					$this->modules[$key] = $modules[$key];
				if ($this->name!=$this->old_name and $this->old_name!="") {
					unset($GLOBALS["panels"][$this->old_name]);
					foreach($GLOBALS["interfaces"] as $key=>$value) {				
						if (isset($value["controlPanel"]) and $value["controlPanel"]==$this->old_name) {							
							$GLOBALS["interfaces"][$key]["controlPanel"] = $this->name;
							$str = "<?php\n".getMetadataString(getMetadataInFile($GLOBALS["interfaces"][$key]["file"]))."\n?>";							
							file_put_contents($GLOBALS["interfaces"][$key]["file"],$str);						
						}
					}
				}
				$GLOBALS["panels"][$this->name] = array();
				$GLOBALS["panels"][$this->name]["name"] = $this->name;
				$GLOBALS["panels"][$this->name]["collection"] = "panels";
				$GLOBALS["panels"][$this->name]["file"] = $this->file;
				$GLOBALS["panels"][$this->name]["modules"] = $this->modules;
				$GLOBALS["panels"][$this->name]["metaTitle"] = $this->title;
				$GLOBALS["panels"][$this->name]["defaultModule"] = $this->defaultModule;				
				if ($this->file!=$this->old_file and file_exists($this->old_file)) {
					$str = "<?php\n".getMetadataString(getMetadataInFile($this->old_file),false)."\n?>";
					file_put_contents($this->old_file,$str);						
				}
				$str = "<?php\n".getMetadataString(getMetadataInFile($this->file),false)."\n?>";
				file_put_contents($this->file,$str);						
			}				
		}
		
		function remove($arguments) {
			$this->setArguments($arguments);
			if (isset($GLOBALS["panels"][$this->name])) {
				foreach($GLOBALS["interfaces"] as $key=>$value) {
					if (isset($GLOBALS["interfaces"][$key]["controlPanel"]) and $GLOBALS["interfaces"][$key]["controlPanel"]==$this->name) {
						$this->reportError("Данная панель используется в интерфейсе '".$GLOBALS["interfaces"][$key]["metaTitle"]."'. Удаление невозможно.");
						return 0;
					}
				}
				$file = $GLOBALS["panels"][$this->name]["file"];
				unset($GLOBALS["panels"][$this->name]);
				$str = "<?php\n".getMetadataString(getMetadataInFile($file),false)."\n?>";
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
		
		function getArgs() {
			$this->modulesList = implode("~",array_keys($GLOBALS["modules"]))."|".implode("~",array_keys($GLOBALS["modules"]));
			return parent::getArgs();
		}
	}
?>