<?php
	class MetadataObjectModel extends WABEntity {
		
		public $groups = array();
		public $modelFields = array();
		
		function construct($params) {
			global $Objects;
			$this->module_id = array_shift($params)."_".array_shift($params);
			$this->rnd = array_shift($params);
			$this->name = implode("_",$params);
			$this->old_name = $this->name;
			$this->title = "";
			$this->old_title = "";
			$this->file = "/opt/WAB2/metadata/fields/models.php";
			$this->old_file = "";
			$this->group = "";
			$this->fullGroup = "";			
			$this->template = "renderForm";
			$this->app = $Objects->get("Application");
			if (!$this->app->initiated)
				$this->app->initModules();
			$this->skinPath = $this->app->skinPath;
			$this->icon = $this->skinPath."images/Tree/model.png";
			$this->css = $this->skinPath."styles/Mailbox.css";
			$this->handler = "scripts/handlers/metadata/MetadataObjectModel.js";
			$this->width = "450";
			$this->height = "550";
			$this->overrided = "width,height";
			$this->fieldsTable = "MetadataArrayTable_".$this->module_id."_".$this->name."FieldTable";
			$this->groupsTable = "MetadataArrayTable_".$this->module_id."_".$this->name."GroupTable";
			$this->tabset_id = "WebItemTabset_".$this->module_id."_".$this->name."ModelPanel";
			$this->tabs_string = "fields|Поля|".$this->app->skinPath."images/spacer.gif;";
			$this->tabs_string .= "groups|Группы|".$this->app->skinPath."images/spacer.gif";
			$this->active_tab = "fields";				
        	$this->clientClass = "MetadataObjectModel";
			$this->loaded = false;
		}
		
		function renderForm() {
			$blocks = getPrintBlocks(file_get_contents("templates/metadata/MetadataObjectModel.html"));
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
			global $models;
			$name = $this->name;
			if (isset($models[$name])) {
				$this->file = @$models[$name]["file"];
				$this->old_file = $this->file;
				$this->title = @$models[$name]["metaTitle"];
				$this->old_title = $this->title;
				$this->groups = @$models[$name]["groups"];
				$this->modelFields = array();
				foreach ($models[$name] as $key=>$value) {
					if ($key!="file" && $key!="groups" && $key!="metaTitle") {
						$this->modelFields[$value] = $value;
					}
				}
				$this->loaded = true;
			}			
		}
		
		function checkData() {
			
			global $models;				
			if ($this->name=="") {
				$this->reportError("Укажите 'системное имя'",'save');
				return 0;
			}
			if ($this->name!=$this->old_name) {
				if (isset($models[$this->name])) {
					
					$this->reportError("Поле с таким именем уже существует",'save');
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
			$this->modelFields = @$arguments["modelFields"];
			$this->modelFields = (array)$this->modelFields;
			$this->groups = @$arguments["groups"];
			$this->groups = (array)$this->groups;
				
			if ($this->checkData()) {
				if ($this->modelFields!="" and is_array($this->modelFields)) {
					$this->modelFields = array_unique($this->modelFields);
				}
				if ($this->groups!="" and is_array($this->groups)) {
					$this->groups = array_unique($this->groups);
				}
				$this->groups = array_values($this->groups);
				if ($this->name!=$this->old_name and $this->old_name!="") {
					unset($GLOBALS["models"][$this->old_name]);
					foreach($GLOBALS["modelGroups"] as $key=>$value) {
						$item = array_search($this->old_name,@$value["fields"]);
						if ($item!==FALSE) {
							$GLOBALS["modelGroups"][$key]["fields"][$item] = $this->name;
							$str = "<?php\n".getMetadataString(getMetadataInFile($GLOBALS["modelGroups"][$key]["file"]))."\n?>";
							file_put_contents($GLOBALS["modelGroups"][$key]["file"],$str);						
						}
					}
				}
				if ($this->old_name=="" and $this->group!="") {
					$GLOBALS["modelGroups"][$this->group]["fields"][] = $this->name;
					$str = "<?php\n".getMetadataString(getMetadataInFile($GLOBALS["modelGroups"][$this->group]["file"]))."\n?>";
					file_put_contents($GLOBALS["modelGroups"][$this->group]["file"],$str);
						
				}
				$GLOBALS["models"][$this->name] = array();
				$GLOBALS["models"][$this->name]["file"] = $this->file;
				$GLOBALS["models"][$this->name]["groups"] = $this->groups;
				foreach ($this->modelFields as $key=>$value) {
					if ($key!="file" && $key!="groups" && $key!="metaTitle") {
						$GLOBALS["models"][$this->name][$value] = $value;
					}
				}
				$GLOBALS["models"][$this->name]["metaTitle"] = $this->title;
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
			if (isset($GLOBALS["models"][$this->name])) {
				foreach($GLOBALS["modelGroups"] as $key=>$value) {
					$item = @array_search($this->name,@$value["fields"]);
					if ($item!==FALSE) {
						unset($GLOBALS["modelGroups"][$key]["fields"][$item]);
						$str = "<?php\n".getMetadataString(getMetadataInFile($GLOBALS["modelGroups"][$key]["file"]))."\n?>";
						file_put_contents($GLOBALS["modelGroups"][$key]["file"],$str);						
					}
				}
				$file = $GLOBALS["models"][$this->name]["file"];
				unset($GLOBALS["models"][$this->name]);
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