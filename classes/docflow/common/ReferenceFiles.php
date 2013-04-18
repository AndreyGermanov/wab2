<?php
// Справочник файлов
class ReferenceFiles extends Reference {
	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "ReferenceFiles";
		$this->hierarchy = true;
		$this->parentClientClasses = "Reference~Entity";
		$this->classListTitle = "Файлы";
		$this->classTitle = "Файл";		
		$this->template = "renderForm";
        $this->fieldList = "title Описание~path Имя файла~statusString Состояние";
        $this->additionalFields = "name~isGroup~parent~deleted~status";
        $this->condition = "@parent IS NOT EXISTS";
        $this->printFieldList = $this->fieldList;
        $this->allFieldList = $this->fieldList;
        $this->width = "700";
        $this->height = "600";     
        $this->overrided = "width,height";
        $this->conditionFields = "path~Имя файла~string,title~Наименование~string,description~Описание~string,status~Состояние";        
        $this->sortOrder = "path ASC";
        $this->handler = "scripts/handlers/docflow/common/ReferenceFiles.js";
        $this->tabs_string = "main|Основное|".$this->skinPath."images/spacer.gif";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_ReferenceFiles".$this->name;
        $this->tabsetName = $this->tabset_id;
        $this->active_tab = "main";
        $this->adapterId = $this->adapter->getId();
        $this->icon = $this->skinPath."images/docflow/file.png";
        $this->createObjectList = array("DocumentWorkReport" => "Отчет о работе", "ReferenceProjects" => "Проект", "DocumentTask" => "Задача");
        $this->entityDataTableClass = "ReferenceFilesTable";
        $this->entityTableClass = "ReferenceFilesTable";        
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/common/ReferenceFiles.html"));
		$common = getPrintBlocks(file_get_contents("templates/docflow/common/CommonFields.html"));
		$out = str_replace("{hiddenFields}",$common["hiddenFields"],$blocks["header"]);
		if ($this->name!="") {
			$out .= $common["tabs"];
			$out .= $common["profileEditTab"];
		}
		$out .= $blocks["footer"];		
		return $out;
	}
	
	function getPresentation() {
		if ($this->noPresent)
			return "";
		if (!$this->loaded)
			$this->load();
		if ($this->classTitle != "")
			return $this->classTitle.":".$this->title." (".array_pop(explode("/",$this->path)).")";
		else
			return $this->title."(".array_pop(explode("/",$this->path)).")";
	}	
		
	function checkData() {
		if ($this->title=="") {
			$this->reportError("Укажите наименование","save1");
			return 0;
		}		
		if (!$this->isGroup) {
			if (trim($this->path)=="") {
				$this->reportError("Укажите путь к файлу!","save2");	
				return 0;
			}
			if (!file_exists($this->path))
				$this->status = "1";
			else 
			    $this->status = "0";
			if ($this->title != "") {				
				if ($this->name!="")
					$query = "SELECT entities FROM fields WHERE @path='".$this->path."' AND @name!=".$this->name." AND @classname='".get_class($this)."'";
				else
					$query = "SELECT entities FROM fields WHERE @path='".$this->path."' AND @classname='".get_class($this)."'";
				$result = PDODataAdapter::makeQuery($query, $this->adapter,$this->module_id);
				if ($result!=0 and count($result)>0) {
					$this->reportError("Такой файл уже есть в базе","save4");
					return 0;
				}
			}
		} 		
		$this->inode = trim(shell_exec(str_replace("{path}",$this->path,$this->app->getInodeCommand)));
		$this->md5sum = trim(shell_exec(str_replace("{path}",$this->path,$this->app->mD5SumCommand)));
		$this->md5sumDate = time();
		return parent::checkData();			
	}
	
	function getArgs() {		        
		if ($this->window_id=="")
			$this->window_id = @$_GET["window_id"];
		
		$this->entityImages = json_encode(array());
						
		return parent::getArgs();
	}
	
	function __get($name) {
		if ($name=="statusString") {
			if ($this->fields["status"])
				return "Не найден";
			else
				return "Существует";
		}
		return parent::__get($name);
	}
	
	function updateStatus($arguments) {
		global $Objects;
		$adapter = $Objects->get("DocFlowDataAdapter_".$this->module_id."_Adapter");
		if (!$adapter->connected)
			$adapter->connect();
		$stmt = $adapter->dbh->prepare("SELECT entityId,value FROM fields WHERE classname='ReferenceFiles' AND name='path'");
		$stmt->execute();
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$query = "";
		foreach ($res as $row) {
			if (file_exists($row["value"]))
				$status = "0";
			else 
				$status = "1";
			$query .= "UPDATE fields SET value='".$status."',value2='".$status."' WHERE classname='ReferenceFiles' AND name='status' AND entityId=".$row["entityId"].";";			
		}
		$adapter->dbh->exec($query);
	}
	
	function getHookProc($number) {
		switch ($number) {
			case '4': return "updateStatus";
		}
		return parent::getHookProc($number);
	}
}
?>