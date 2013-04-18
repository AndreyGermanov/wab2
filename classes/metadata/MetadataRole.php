<?php
	class MetadataRole extends WABEntity {
		function construct($params) {
			global $Objects;
			$this->module_id = array_shift($params)."_".array_shift($params);
			$nm = array_shift($params);
			$this->name = "";			
			if (@is_numeric($nm)) {
				$this->rnd = $nm;				
			} else {
				if (count($params)>0)
					$this->name = $nm."_";
				else
					$this->name = $nm;
			}
			$this->name .= implode("_",$params);				
			$this->old_name = $this->name;
			$this->title = "";
			$this->old_title = "";
			$this->file = "/opt/WAB2/metadata/roles/roles.php";
			$this->old_file = "";
			$this->template = "renderForm";
			$this->app = $Objects->get("Application");
			if (!$this->app->initiated)
				$this->app->initModules();
			$this->skinPath = $this->app->skinPath;
			$this->icon = $this->skinPath."images/Tree/role.gif";
			$this->css = $this->skinPath."styles/Mailbox.css";
			$this->handler = "scripts/handlers/metadata/MetadataRole.js";
			$this->width = "600";
			$this->height = "450";
			$this->overrided = "width,height";
        	$this->clientClass = "MetadataRole";
			$this->loaded = false;
			$this->group = "";
			$this->fullGroup = "";				
	        $this->itemsPerPage = 10;
	        $this->sortOrder = "name";
	        $this->fieldList = "name Имя~title Описание";
	        $this->collectionLoadMethod = "load";
	        $this->collectionGetMethod = "getRoles";
	        $this->tableClass = "MetadataRole";
			if ($this->module_id!="") {
				$this->tabset_id = "WebItemTabset_".$this->module_id."_".str_replace("_","",$this->getId())."Tabset";
				$this->tabsetName = $this->tabset_id;
			}
			else {
				$this->tabset_id = "WebItemTabset_".str_replace("_","",$this->getId())."Tabset";
				$this->tabsetName = str_replace("_","",$this->getId())."Tabset";
			}
			$this->tabs_string = "main|Основное|".$this->skinPath."images/spacer.gif;";
			$this->tabs_string.= "profiles|Профили|".$this->skinPath."images/spacer.gif";
			$this->active_tab = "main";
			$profilesValues = array();
			$profilesKeys = array();
			if (isset($GLOBALS["roles"][$this->name])) {
				foreach ($GLOBALS["roles"][$this->name] as $key=>$value) {
					if (is_array($value)) {
						$arr = explode("_",$key);
						$class = array_shift($arr);
						$entityId = implode("_",$arr);
						$obj = $Objects->get($class."_".$this->module_id."_".implode("_",$arr));
						$profilesKeys[] = $obj->profileClass."_".$this->module_id."_".$this->name."_".$key;
						if ($entityId!="") {
							$profilesValues[] = @$obj->getPresentation();						
						} else {
							$profilesValues[] = @$obj->classType." ".$obj->classListTitle;						
						}
					}					
				}
			}
			$this->profilesList = implode("~",$profilesKeys)."|".implode("~",$profilesValues);
		}
		
		function renderForm() {
			$blocks = getPrintBlocks(file_get_contents("templates/metadata/MetadataRole.html"));
			$out = $blocks["header"];
			return $out;
		}
		
		function getId() {
			if ($this->rnd!="")
				$name = $this->rnd."_".$this->name;
			else
				$name = $this->name;
			if ($this->module_id!="")
				return get_class($this)."_".$this->module_id."_".$name;
			else
				return get_class($this)."_".$name;
		}
		
		function getPresentation() {			
			return $this->name;
		}
		
		function load() {
			global $roles;
			$name = $this->name;
			if (isset($roles[$name])) {
				$this->file = @$roles[$name]["file"];
				$this->old_file = $this->file;
				$this->title = @$roles[$name]["title"];
				$this->old_title = $this->title;
				$this->loaded = true;
			}			
		}
		
		function checkData() {
			
			global $roles;				
			if ($this->name=="") {
				$this->reportError("Укажите 'системное имя'",'save');
				return 0;
			}
			if ($this->name!=$this->old_name) {
				if (isset($roles[$this->name])) {
					
					$this->reportError("Роль с таким именем уже существует",'save');
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
			global $Objects;
			$app = $Objects->get("Application");
			if (!$app->initiated)
				$app->initModules();
			if (!$this->loaded)
				$this->load();
			$this->setArguments($arguments);
			if ($this->checkData()) {
				if ($this->name!=$this->old_name and $this->old_name!="") {
					$app->raiseRemoteEvent("ROLE_CHANGED","object_id=".$this->getId());
					$GLOBALS["roles"][$this->name] = $GLOBALS["roles"][$this->old_name];
					unset($GLOBALS["roles"][$this->old_name]);
				} else if ($this->old_name=="")				
					$app->raiseRemoteEvent("ROLE_ADDED","object_id=".$this->getId());
				if (!isset($GLOBALS["roles"][$this->name]))
					$GLOBALS["roles"][$this->name] = array();				
				$GLOBALS["roles"][$this->name]["name"] = $this->name;
				$GLOBALS["roles"][$this->name]["collection"] = "roles";
				$GLOBALS["roles"][$this->name]["file"] = $this->file;
				$GLOBALS["roles"][$this->name]["title"] = $this->title;
				if ($this->file!=$this->old_file and file_exists($this->old_file)) {
					$str = "<?php\n".getMetadataString(getMetadataInFile($this->old_file))."\n?>";
					file_put_contents($this->old_file,$str);						
				}
				$str = "<?php\n".getMetadataString(getMetadataInFile($this->file))."\n?>";
				file_put_contents($this->file,$str);						
			}				
		}
		
		function remove($arguments) {
			global $Objects;
			$app = $Objects->get("Application");
			if (!$app->initiated)
				$app->initModules();
			$this->setArguments($arguments);
			if (isset($GLOBALS["roles"][$this->name])) {
				$app->raiseRemoteEvent("ROLE_DELETED","object_id=".$this->getId());
				$file = $GLOBALS["roles"][$this->name]["file"];
				unset($GLOBALS["roles"][$this->name]);
				$str = "<?php\n".getMetadataString(getMetadataInFile($file))."\n?>";
				file_put_contents($file,$str);				
			}
		}
		
		function getHookProc($number) {
			switch($number) {
				case '3': return "save";
				case '4': return "remove";
				case '5': return "showList";
				case '6': return "removeProfile";
			}
			return parent::getHookProc($number);
		}
		
		function showList($arguments) {
			$object = $this;
			$object->setArguments($arguments);
			$object->overrided='width,height';
			$object->width=450;$object->height=350;
			$object->loaded=true;
			$object->template="templates/metadata/MetadataRolesTable.html";
			$object->title=$this->classListTitle;				
		}
		
		function getRoles() {
			global $Objects;
			$result = array();
			foreach ($GLOBALS["roles"] as $key=>$value) {
				$role = $Objects->get("MetadataRole_".$this->module_id."_".$key);
				$role->load();
				$result[$role->getId()] = $role;
			}
			return $result;
		}
		
		function getArgs() {
			$this->tabsetCode = '$object->module_id="'.$this->module_id.'";
			$object->item="'.$this->getId().'";
			$object->window_id="'.$this->window_id.'";
			$object->tabs_string="'.$this->tabs_string.'";
			$object->active_tab="'.$this->active_tab.'";';
				
			$this->tabsetCode = cleanText($this->tabsetCode);
			
			$this->treeId = "EntityTree_".$this->module_id."_".$this->name;
			$this->treeCode = '$object->module_id="'.$this->module_id.'";
			$object->window_id="'.$this->window_id.'";
			$object->parent_object_id="'.$this->getId().'";
			$object->result_object_id="'.$this->getId().'";
			$object->className="";$object->setTreeItems();';				
			$this->treeCode = cleanText($this->treeCode);
			return parent::getArgs();
		}

		function removeProfile($arguments) {
			if (!$this->loaded)
				$this->load();
			global $Objects;
			$app = $Objects->get("Application");
			if (!$app->initiated)
				$app->initModules();
			$app->raiseRemoteEvent("PROFILE_DELETED","object_id=EntityProfile_".$this->module_id."_".$this->name."_".$arguments["profile"]);
			unset($GLOBALS["roles"][$this->name][$arguments["profile"]]);
			$str = "<?php\n".getMetadataString(getMetadataInFile($this->file))."\n?>";
			file_put_contents($this->file,$str);
		}		
	}
?>