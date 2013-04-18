<?php
	class MetadataObjectCode extends WABEntity {
		
		public $params = array();
		
		function construct($params) {
			global $Objects;
			$this->module_id = array_shift($params)."_".array_shift($params);
			$this->rnd = array_shift($params);
			$this->name = implode("_",$params);
			$this->old_name = $this->name;
			$this->title = "";
			$this->old_title = "";
			$this->file = "/opt/WAB2/metadata/codes/models/base.php";
			$this->old_file = "";
			$this->group = "";
			$this->fullGroup = "";			
			$this->template = "renderForm";
			$this->code = "";
			$this->comment = "";
			$this->app = $Objects->get("Application");
			if (!$this->app->initiated)
				$this->app->initModules();
			$this->skinPath = $this->app->skinPath;
			$this->icon = $this->skinPath."images/Tree/algo2.png";
			$this->css = $this->skinPath."styles/Mailbox.css";
			$this->handler = "scripts/handlers/metadata/MetadataObjectCode.js";
			$this->width = "650";
			$this->height = "550";
			$this->overrided = "width,height";
			$this->paramsTable = "MetadataCodeParamsTable_".$this->module_id."_".$this->name."ParamsTable";
			$this->testTable = "MetadataCodeParamsTable_".$this->module_id."_".$this->name."TestTable";
			$this->tabset_id = "WebItemTabset_".$this->module_id."_".$this->name."CodesPanel";
			$this->tabs_string = "codeTr|Алгоритм|".$this->app->skinPath."images/spacer.gif;";
			$this->tabs_string .= "test|Проверка|".$this->app->skinPath."images/spacer.gif;";
			$this->tabs_string .= "commentTr|Описание|".$this->app->skinPath."images/spacer.gif;";
			$this->tabs_string .= "params|Входные параметры|".$this->app->skinPath."images/spacer.gif";
			$this->active_tab = "codeTr";				
        	$this->clientClass = "MetadataObjectCode";
			$this->loaded = false;
			$this->itemsPerPage = 10;
			$this->sortOrder = "name";
			$this->fieldList = "name Имя~title Описание";
			$this->collectionLoadMethod = "load";
			$this->collectionGetMethod = "getCodes";
			$this->tableClass = "MetadataObjectCode";
			$this->classTitle = "Алгоритм";
			$this->classListTitle = "Алгоритмы";				
		}
		
		function renderForm() {
			$blocks = getPrintBlocks(file_get_contents("templates/metadata/MetadataObjectCode.html"));
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
			global $codes;
			$name = $this->name;
			if (isset($codes[$name])) {
				$this->file = @$codes[$name]["file"];
				$this->old_file = $this->file;
				$this->title = @$codes[$name]["metaTitle"];
				$this->old_title = $this->title;
				$this->comment = @$codes[$name]["comment"];
				$this->old_comment = $this->comment;
				$this->params = @$codes[$name]["params"];
				if (file_exists("metadata/codes/files/".$this->name.".php"))
					$this->code = file_get_contents("metadata/codes/files/".$this->name.".php");
				$this->loaded = true;
			}			
		}
		
		function checkData() {
			
			global $codes;				
			if ($this->name=="") {
				$this->reportError("Укажите 'системное имя'",'save');
				return 0;
			}
			if ($this->name!=$this->old_name) {
				if (isset($codes[$this->name])) {
					
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
			if ($this->checkData()) {
				$params = @$arguments["params"];
				$params = (array)$params;
				if ($this->name!=$this->old_name and $this->old_name!="") {
					unset($GLOBALS["codes"][$this->old_name]);
					foreach($GLOBALS["codeGroups"] as $key=>$value) {
						if (!isset($value["fields"]))
							continue;
						$item = array_search($this->old_name,@$value["fields"]);
						if ($item!==FALSE) {
							$GLOBALS["codeGroups"][$key]["fields"][$item] = $this->name;
							$str = "<?php\n".getMetadataString(getMetadataInFile($GLOBALS["codeGroups"][$key]["file"]))."\n?>";
							file_put_contents($GLOBALS["codeGroups"][$key]["file"],$str);						
						}
					}
				}
				unlink("metadata/codes/files/".$this->old_name.".php");
				if ($this->old_name=="" and $this->group!="") {
					$GLOBALS["codeGroups"][$this->group]["fields"][] = $this->name;
					$str = "<?php\n".getMetadataString(getMetadataInFile($GLOBALS["codeGroups"][$this->group]["file"]))."\n?>";
					file_put_contents($GLOBALS["codeGroups"][$this->group]["file"],$str);
						
				}
				$GLOBALS["codes"][$this->name] = array();
				$GLOBALS["codes"][$this->name]["file"] = $this->file;
				$GLOBALS["codes"][$this->name]["comment"] = $this->comment;
				$GLOBALS["codes"][$this->name]["metaTitle"] = $this->title;
				file_put_contents("metadata/codes/files/".$this->name.".php",$this->code);
				if (is_array($params)) {
					foreach ($params as $key=>$value) {
						$GLOBALS["codes"][$this->name]["params"][$key] = $value;
					}
				}
				unset($GLOBALS["codes"][$this->name]["code"]);
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
			if (isset($GLOBALS["codes"][$this->name])) {
				foreach($GLOBALS["codeGroups"] as $key=>$value) {
					$item = @array_search($this->name,@$value["fields"]);
					if ($item!==FALSE) {
						unset($GLOBALS["codeGroups"][$key]["fields"][$item]);
						$str = "<?php\n".getMetadataString(getMetadataInFile($GLOBALS["codeGroups"][$key]["file"]))."\n?>";
						file_put_contents($GLOBALS["codeGroups"][$key]["file"],$str);						
					}
				}
				$file = $GLOBALS["codes"][$this->name]["file"];
				unset($GLOBALS["codes"][$this->name]);
				@unlink("metatada/codes/files/".$this->name.".php");
				$str = "<?php\n".getMetadataString(getMetadataInFile($file))."\n?>";
				file_put_contents($file,$str);				
			}
		}
		
		function exec($input="",$code="") {
			global $Objects;
			if (!$this->loaded)
				$this->load();
			if ($input=="")
				$input = $this->params;
			$input = (array)$input;
			if (!isset($input["module_id"]))
				$input["module_id"] = $this->module_id;
			if (!isset($input["object_id"]))
				$input["object_id"] = $this->object_id;
			if ($code=="")
				$code = @$GLOBALS["codes"][$this->name]["code"];			
			if ($code=="") {
				$code = file_get_contents("metadata/codes/files/".$this->name.".php");
				$GLOBALS["codes"][$this->name]["code"] = $code;
			}
			$result = eval($code);
			return $result;
		}
		
		function test($arguments) {
			if (!$this->loaded)
				$this->load();			
			$result = $this->exec(@$arguments["params"],@$arguments["code"]);
			if (is_array($result)) {
				echo str_replace("array","массив",getArrayCode($result));				
			} else {
				echo $result;
			}
		}

		function showResult($arguments) {
			if (!$this->loaded)
				$this->load();
			$result = $this->exec(@$arguments["params"],@$arguments["code"]);
			if (is_array($result))
				echo json_encode($result);
			else
				echo $result;			
		}		
		
		function getHookProc($number) {
			switch($number) {
				case '3': return "save";
				case '4': return "remove";
				case '5': return "test";
				case '6': return "showResult";
				case '7': return "showList";
			}
			return parent::getHookProc($number);
		}

		function showList($arguments) {
			$object = $this;
			$object->setArguments($arguments);
			$object->overrided='width,height';
			$object->width=650;$object->height=450;
			$object->loaded=true;
			$object->template="templates/metadata/MetadataRolesTable.html";
			$object->title=$this->classListTitle;
		}
		
		function getCodes() {
			global $Objects;
			$result = array();
			$i=1;
			foreach ($GLOBALS["codes"] as $key=>$value) {
				$code = $Objects->get("MetadataObjectCode_".$this->module_id."_".$i."_".$key);
				$code->load();
				$result[$code->getId()] = $code;
				$i++;
			}
			return $result;
		}
		
	}
?>