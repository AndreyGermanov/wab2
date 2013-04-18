<?php
	class MetadataGroup extends WABEntity {
		
		public $groups = array();
		public $groupFields = array();
		
		function construct($params) {
			global $Objects;
			$this->module_id = array_shift($params)."_".array_shift($params);
			$this->rnd = array_shift($params);
			$this->name = implode("_",$params);

			$this->app = $Objects->get("Application");
			if (!$this->app->initiated)
				$this->app->initModules();
			$this->skinPath = $this->app->skinPath;
			switch(get_class($this)) {
				case "MetadataGroup":
					$this->metadataArray = "groups";
					break;
				case "MetadataModelGroup":
					$this->metadataArray = "modelGroups";
					break;
				case "MetadataCodeGroup":
					$this->metadataArray = "codeGroups";
					break;													
			}
			$this->old_name = $this->name;
			$this->title = "";
			$this->old_title = "";
			$this->file = "/opt/WAB2/metadata/fields/fields.php";
			$this->old_file = "";
			$this->group = "";
			$this->fullGroup = "";			
			$this->template = "renderForm";
			$this->icon = $this->skinPath."images/Tree/metagroup.png";
			$this->css = $this->skinPath."styles/Mailbox.css";
			$this->handler = "scripts/handlers/metadata/MetadataGroup.js";
			$this->width = "450";
			$this->height = "550";
			$this->overrided = "width,height";
			$this->fieldsTable = "MetadataArrayTable_".$this->module_id."_".$this->name."FieldTable";
			$this->groupsTable = "MetadataArrayTable_".$this->module_id."_".$this->name."GroupTable";
			$this->tabset_id = "WebItemTabset_".$this->module_id."_".$this->name."ModelPanel";
			$this->tabs_string = "fields|Поля|".$this->app->skinPath."images/spacer.gif;";
			$this->tabs_string .= "groups|Группы|".$this->app->skinPath."images/spacer.gif";
			$this->active_tab = "fields";				
        	$this->clientClass = get_class($this);
			$this->loaded = false;
		}
		
		function renderForm() {
			$blocks = getPrintBlocks(file_get_contents("templates/metadata/MetadataGroup.html"));
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
			$name = $this->name;
			if (isset($GLOBALS[$this->metadataArray][$name])) {
				$this->file = @$GLOBALS[$this->metadataArray][$name]["file"];
				$this->old_file = $this->file;
				$this->title = @$GLOBALS[$this->metadataArray][$name]["title"];
				$this->old_title = $this->title;
				$this->groups = @$GLOBALS[$this->metadataArray][$name]["groups"];
				$this->groupFields = @$GLOBALS[$this->metadataArray][$name]["fields"];
				$this->loaded = true;
			}			
		}
		
		function checkData() {			
			if ($this->name=="") {
				$this->reportError("Укажите 'системное имя'",'save');
				return 0;
			}
			if ($this->name!=$this->old_name) {
				if (isset($GLOBALS[$this->metadataArray][$this->name])) {					
					$this->reportError("Группа с таким именем уже существует",'save');
					return 0;
				}
			}
				
			if ($this->title=="") {
				$this->reportError("Укажите 'описание'","save");
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
			$this->groupFields = @$arguments["fields"];
			$this->groupFields = (array)$this->groupFields;
			$this->groupFields = array_values($this->groupFields);
			$this->groups = @$arguments["groups"];
			$this->groups = (array)$this->groups;
			$this->groups = array_values($this->groups);
				
			if ($this->checkData()) {
				if ($this->groupFields!="" and is_array($this->groupFields)) {
					$this->groupFields = array_unique($this->groupFields);
				}
				if ($this->groups!="" and is_array($this->groups)) {
					$this->groups = array_unique($this->groups);
				}
				if ($this->name!=$this->old_name and $this->old_name!="") {
					unset($GLOBALS[$this->metadataArray][$this->old_name]);
					foreach($GLOBALS[$this->metadataArray] as $key=>$value) {
						if (!isset($value["groups"]))
							continue;
						$item = array_search($this->old_name,@$value["groups"]);
						if ($item!==FALSE) {
							$GLOBALS[$this->metadataArray][$key]["groups"][$item] = $this->name;
							$str = "<?php\n".getMetadataString(getMetadataInFile($GLOBALS[$this->metadataArray][$key]["file"]))."\n?>";
							file_put_contents($GLOBALS[$this->metadataArray][$key]["file"],$str);						
						}
					}
				}
				if ($this->old_name=="" and $this->group!="") {
					$GLOBALS[$this->metadataArray][$this->group]["groups"][] = $this->name;
					$str = "<?php\n".getMetadataString(getMetadataInFile($GLOBALS[$this->metadataArray][$this->group]["file"]))."\n?>";
					file_put_contents($GLOBALS[$this->metadataArray][$this->group]["file"],$str);
						
				}
				$GLOBALS[$this->metadataArray][$this->name] = array();
				$GLOBALS[$this->metadataArray][$this->name]["file"] = $this->file;
				$GLOBALS[$this->metadataArray][$this->name]["groups"] = $this->groups;
				$GLOBALS[$this->metadataArray][$this->name]["fields"] = $this->groupFields;				
				$GLOBALS[$this->metadataArray][$this->name]["title"] = $this->title;
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
			if (isset($GLOBALS[$this->metadataArray][$this->name])) {
				foreach($GLOBALS[$this->metadataArray] as $key=>$value) {
					$item = @array_search($this->name,@$value["groups"]);
					if ($item!==FALSE) {
						unset($GLOBALS[$this->metadataArray][$key]["groups"][$item]);
						$str = "<?php\n".getMetadataString(getMetadataInFile($GLOBALS[$this->metadataArray][$key]["file"]))."\n?>";
						file_put_contents($GLOBALS[$this->metadataArray][$key]["file"],$str);						
					}
				}
				$file = $GLOBALS[$this->metadataArray][$this->name]["file"];
				unset($GLOBALS[$this->metadataArray][$this->name]);
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