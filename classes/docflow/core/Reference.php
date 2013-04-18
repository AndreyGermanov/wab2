<?php
/*
 * Класс реализует справочник элементов, который может быть либо одноуровневым,
 * либо иерархическим и содержать произвольное количество показателей
 * 
 * @author andrey
 */
class Reference extends WABEntity {
    public $persistedFields = array();
    function construct($params) {
        $old_params = $params;
        global $Objects;

        if (count($params)>2)
            $this->module_id = array_shift($params)."_".array_shift($params);
        else
            $this->module_id = "";
        if (count($params)>0)
            $this->name_set = true;
        $this->name = implode("_",$params);
        $this->old_name = $this->name;
        $app = $Objects->get($this->module_id);       
        if (!$app->loaded)
            $app->load();        
        if (method_exists($app,"getAdapter")) {
        	$this->adapter = $app->getAdapter($this);
        }
        $this->className = get_class($this);
        $this->defaultClassName = get_class($this);
        $this->childClass = $this->className;
        $this->additionalFields = "name~deleted";
        $this->parentEntity = get_class($this)."_".$this->module_id."_";
        $this->objectid = str_replace("_","",$this->getId());
        $this->itemsPerPage = "10";
        $this->condition = "";
        $this->hierarchy = "false";
        $this->fieldList = "";
        $this->classTitle = "Справочник";
        $this->classListTitle = "Справочники";
        $params = $old_params;
        parent::construct($params);
        $this->models[] = "entityBase"; 
        $this->models[] = "referenceBase";              
        $this->handler = "scripts/handlers/docflow/core/Reference.js";       
        $app = $Objects->get("Application");
        $this->icon = $app->skinPath."images/Tree/addrbook.png";       
        if (!$app->loaded)
            $app->load();
        $this->app = $app;
        $this->appUser = $app->User;       
        $this->entityDataTableClass = "DocFlowReferenceTable"; 
        $this->tabs_string  = "main|Основное|".$this->skinPath."images/spacer.gif";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_".get_class($this).$this->name;
        $this->tabsetName = $this->tabset_id;        
		$this->active_tab = "main";
        $this->clientClass = "Reference";
        $this->parentClientClasses = "Entity";        
        $this->classTitle = "Справочник";
        $this->classListTitle = "Справочники";
		$this->classType = "Справочник";
		$this->profileClass = "ReferenceProfile";
    }
    
	function getPresentation($full=true) {
		if ($this->noPresent)
			return "";
		if (!$this->loaded)
			$this->load();
		if (!$full)
			return $this->title;
		if ($this->classTitle != "")
			return $this->classTitle.":".$this->title;
		else
			return $this->title;
	}	
        
    function getId() {
        if ($this->name=="")
            $this->name = $this->entityId;
        if ($this->module_id != "")
            return get_class($this)."_".$this->module_id."_".$this->name;
        else
            return get_class($this)."_".$this->name;
    }

    function load() {
    	parent::load();
    	$this->hiddenFields = "";
    	if ($this->loaded and !$this->isGroup) {
    		if ($this->getRoleValue(@$this->role["showTagsTab"])!="false")    		
    			$this->tabs_string = $this->tabs_string.";tags|Поля|".$this->skinPath."images/spacer.gif";
    		if ($this->getRoleValue(@$this->role["showFilesTab"])!="false")    		
    			$this->tabs_string = $this->tabs_string.";files|Файлы|".$this->skinPath."images/spacer.gif";    		
    		if ($this->getRoleValue(@$this->role["showNotesTab"])!="false")    		
    			$this->tabs_string = $this->tabs_string.";notes|Заметки|".$this->skinPath."images/spacer.gif";    		
    		if ($this->getRoleValue(@$this->role["showLinksTab"])!="false")    		
    			$this->tabs_string = $this->tabs_string.";links2|Связи|".$this->skinPath."images/spacer.gif";    		
    		if ($this->getRoleValue(@$this->role["showProfileTab"])!="false")    		
    			$this->tabs_string = $this->tabs_string.";profile|Профиль|".$this->skinPath."images/spacer.gif";    		
    	}    	 
    }
        
    function getArgs() {
    	
    	$this->setRole();
    	if (isset($this->role["tabs"])) {
	    	$tabs = $this->getRoleValue($this->role["tabs"]);
	    	if (is_array($tabs) and count($tabs)>0) {
	    		
		    	$all_tabs = explode(";",$this->tabs_string);
		    	$result_tabs = array();
		    	foreach ($all_tabs as $key=>$value) {
		    		$tab = explode("|",$value);
		    		if (array_search($tab[0],$tabs)!==FALSE) {
		    			$result_tabs[] = $value;
		    		}
		    	}
		    	$this->tabs_string = implode(";",$result_tabs);
	    	}
    	}
    	
    	$this->tabsetCode = '$object->module_id="'.$this->module_id.'";
    						 $object->item="'.$this->getId().'";
    						 $object->window_id="'.$this->window_id.'";
    						 $object->tabs_string="'.$this->tabs_string.'";
    						 $object->active_tab="'.$this->active_tab.'";';
    	
    	$this->tabsetCode = cleanText($this->tabsetCode);
    	 
    	$this->filesfieldList = "path Файл~title Описание";
    	$this->filesitemsPerPage = 10;
    	$this->filessortOrder = "path ASC";
    	$this->filessortField = $this->filessortOrder;
    	 
    	$this->filesCode = '$object->topLinkObject="'.$this->getId().'";
					    	$object->window_id="'.$this->window_id.'";
					    	$object->parent_object_id="'.$this->getId().'";
					    	$object->className="ReferenceFiles";
					    	$object->defaultClassName="ReferenceFiles";
					    	$object->fieldList="'.$this->filesfieldList.'";
					    	$object->itemsPerPage='.$this->filesitemsPerPage.';
					    	$object->currentPage=1;
					    	$object->hierarchy=0;
					    	$object->additionalFields="name~deleted";
					    	$object->adapterId="'.$this->adapter->getId().'";
					    	$object->autoload="false";
					    	$object->sortOrder="'.$this->filesSortOrder.'";';
    	    	 
		$this->filesCode = cleanText($this->filesCode);
				
		$this->filesTableId = "DocFlowReferenceTable_".$this->module_id."_".$this->name."files";
		
		$this->notesfieldList = "date Дата~title Описание~user Создатель";
		$this->notesitemsPerPage = 10;
		$this->notessortOrder = "date DESC";
		$this->notessortField = $this->filessortOrder;
		
		$this->notesCode = '$object->topLinkObject="'.$this->getId().'";
							$object->window_id="'.$this->window_id.'";
							$object->parent_object_id="'.$this->getId().'";
							$object->className="ReferenceNotes";
							$object->defaultClassName="ReferenceNotes";
							$object->fieldList="'.$this->notesfieldList.'";
							$object->itemsPerPage='.$this->notesitemsPerPage.';
							$object->currentPage=1;
							$object->hierarchy=0;
							$object->additionalFields="name~deleted";
							$object->adapterId="'.$this->adapter->getId().'";
							$object->autoload="false";
							$object->sortOrder="'.$this->notesSortOrder.'";';
		 
		$this->notesCode = cleanText($this->notesCode);
		
		$this->notesTableId = "DocFlowReferenceTable_".$this->module_id."_".$this->name."notes";
		
 		$this->linksTreeId = "LinksTree_".$this->module_id."_".$this->name."links";

 		$this->linksCode = '$object->topObject="'.$this->getId().'";
 							$obj = $Objects->get($object->topObject);
 							if (!$obj->loaded) $obj->load();
 							$object->title = "Связи объекта ".$obj->getPresentation();
 							$object->parent_object_id = "'.$this->getId().'";
 							$object->setTreeItems();
 		';
					
 		$this->linksCode = cleanText($this->linksCode);

 		$this->tagsTableId = "TagsTable_".$this->module_id."_".$this->name;
 		$this->tagsTableCode = '$object->entityObject="'.$this->getId().'";$object->parent_object_id="'.$this->getId().'";$object->window_id="'.$this->window_id.'";'; 			
				
    	return parent::getArgs();
    }
    
    function checkData() {                
    	if ($this->hierarchy and is_object($this->parent)) 			
			if ($this->getId()==$this->parent->getId()) {
				$this->reportError("Нельзя указывать самого себя в качестве родительской группы","save");
    	}
        $this->modifications = date("d.m.Y H:i:s")." - ".$this->app->User."\n".$this->modifications;
        if ($this->old_name=="") {
            $this->dateCreated = time();
            $this->user = $this->app->User;
        }
        return true;        
    }
    
    function afterSave($out=true) {
        global $Objects;
        $to_save = false;
        if ($this->name=="") {
            $this->name = $this->entityId;
            $to_save = true;
        }        
        if ($to_save)
            $this->adapter->save();            
    }
        
	function getEntityImage() {
		return parent::getEntityImage();
	}  

	function getAuthor() {
    	if ($this->name=="" or $this->name=="List")
    		return $this->appUser;
		if ($this->adapter!="") {
			if (!$this->adapter->connected)
				$this->adapter->connect();
			if ($this->adapter->connected)
				$this->user = $this->adapter->getAuthor($this);
		}
		return $this->user;
	}	
	
	function getHookProc($number) {
		switch($number) {
			case '3': return "showListHook";
		}
		return parent::getHookProc($number);
	}
	
	function showListHook($arguments=null) {
		$object = $this;
		$object->setArguments($arguments);
		$object->overrided='width,height';
		$object->width=750;$object->height=350;
		$object->className="*".get_class($this)."*";
		$object->defaultClassName=get_class($this);
		$object->loaded=true;
		$object->template="templates/docflow/core/ReferenceList.html";		
		$object->title=$this->classListTitle;
	}

	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents($this->renderTemplate));
		$common = getPrintBlocks(file_get_contents("templates/docflow/common/CommonFields.html"));
		$out = str_replace("{hiddenFields}",$common["hiddenFields"],$blocks["header"]);
		if ($this->name!="") {
			$out .= $common["tabs"];
			$out .= $common["profileEditTab"];
		}
		$out .= $blocks["footer"];
		return $out;
	}	
	
	function afterCopyFrom() {
		$this->deleted = "0";
		parent::afterCopyFrom();
	}
}
?>