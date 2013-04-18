<?php
	class MetadataInterface extends WABEntity {
		
		public $modules = array();
		
		function construct($params) {
			global $Objects;
			$this->module_id = array_shift($params)."_".array_shift($params);
			$this->rnd = array_shift($params);
			$this->name = implode("_",$params);
			$this->old_name = $this->name;
			$this->title = "";
			$this->old_title = "";
			$this->file = "/opt/WAB2/metadata/interfaces/interfaces.php";
			$this->old_file = "";
			$this->controlPanel = "";
			$this->showControlPanel = 0;
			$this->showInfoPanel = 0;
			$this->mainMenuName = "";
			$this->showMainMenu = 0;
			$this->customObjectName = "";
			$this->group = "";
			$this->fullGroup = "";			
			$this->template = "renderForm";
			$this->app = $Objects->get("Application");
			if (!$this->app->initiated)
				$this->app->initModules();
			$this->skinPath = $this->app->skinPath;
			$this->icon = $this->skinPath."images/Tree/interface.png";
			$this->css = $this->skinPath."styles/Mailbox.css";
			$this->handler = "scripts/handlers/metadata/MetadataInterface.js";
			$this->width = "450";
			$this->height = "350";
			$this->overrided = "width,height";
        	$this->clientClass = "MetadataInterface";
			$this->loaded = false;
		}
		
		function renderForm() {
			$blocks = getPrintBlocks(file_get_contents("templates/metadata/MetadataInterface.html"));
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
			global $interfaces;
			$name = $this->name;
			if (isset($interfaces[$name])) {
				$this->file = @$interfaces[$name]["file"];
				$this->old_file = $this->file;
				$this->title = @$interfaces[$name]["metaTitle"];
				$this->old_title = $this->title;
				$this->controlPanel = @$interfaces[$name]["controlPanel"];
				$this->mainMenuName = @$interfaces[$name]["mainMenuName"];
				$this->showControlPanel = @$interfaces[$name]["showControlPanel"];
				$this->showInfoPanel = @$interfaces[$name]["showInfoPanel"];
				$this->showMainMenu = @$interfaces[$name]["showMainMenu"];
				$this->customObjectName = @$interfaces[$name]["customObjectName"];
				$this->loaded = true;
			}			
		}
		
		function checkData() {			
			global $interfaces;				
			if ($this->name=="") {
				$this->reportError("Укажите 'системное имя'",'save');
				return 0;
			}
			if ($this->name!=$this->old_name) {
				if (isset($interfaces[$this->name])) {
					
					$this->reportError("Поле с таким именем уже существует",'save');
					return 0;
				}
			}
				
			if ($this->title=="") {
				$this->reportError("Укажите 'описание'","save");
				return 0;
			}
			
			if ($this->showMainMenu and $this->mainMenuName=="") {
				$this->reportError("Укажите 'Системное имя главного меню'","save");
				return 0;
			}
				
			if (!file_exists($this->file)) {
				$this->reportError("Указанный файл не существует");
				return 0;
			};			
			return 1;
		}
		
		function save($arguments) {
			if (!$this->loaded)
				$this->load();
			$this->setArguments($arguments);
			if ($this->checkData()) {
				if ($this->name!=$this->old_name and $this->old_name!="") {
					unset($GLOBALS["interfaces"][$this->old_name]);
				}
				$GLOBALS["interfaces"][$this->name] = array();
				$GLOBALS["interfaces"][$this->name]["name"] = $this->name;
				$GLOBALS["interfaces"][$this->name]["collection"] = "interfaces";
				$GLOBALS["interfaces"][$this->name]["file"] = $this->file;
				$GLOBALS["interfaces"][$this->name]["showControlPanel"] = $this->showControlPanel;
				$GLOBALS["interfaces"][$this->name]["controlPanel"] = $this->controlPanel;
				$GLOBALS["interfaces"][$this->name]["metaTitle"] = $this->title;
				$GLOBALS["interfaces"][$this->name]["showMainMenu"] = $this->showMainMenu;				
				$GLOBALS["interfaces"][$this->name]["showInfoPanel"] = $this->showInfoPanel;				
				$GLOBALS["interfaces"][$this->name]["mainMenuName"] = $this->mainMenuName;				
				$GLOBALS["interfaces"][$this->name]["customObjectName"] = $this->customObjectName;
				
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
			if (isset($GLOBALS["interfaces"][$this->name])) {
				$file = $GLOBALS["interfaces"][$this->name]["file"];
				unset($GLOBALS["interfaces"][$this->name]);
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
			$arr = array();
			foreach ($GLOBALS["panels"] as $value)
				$arr[] = $value["metaTitle"];
			$this->panelsList = implode("~",array_keys($GLOBALS["panels"]))."|".implode("~",$arr);
			return parent::getArgs();
		}
	}
?>