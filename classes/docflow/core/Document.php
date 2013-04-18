<?php
/**
 * Класс, реализующий документ
 *
 * @author andrey
 */
class Document extends WABEntity {
    
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
        $this->app = $app;     
        $gapp = $Objects->get("Application");
        if (!$gapp->initiated)
        	$gapp->initModules();
        $this->appUser = $gapp->User;
        $this->user = $this->appUser;
        if (method_exists($app,"getAdapter")) {
        	$app->getAdapter($this);
        	$this->adapter = @$app->getAdapter($this);
        }
        $this->className = get_class($this);
        $this->defaultClassName = get_class($this);
        $this->childClass = $this->className;
        $this->additionalFields = "name~registered~deleted";
        $this->parentEntity = get_class($this)."_".$this->module_id."_";
        $this->objectid = str_replace("_","",$this->getId());
        $this->itemsPerPage = "";
        $this->currentPage = "";
        $this->condition = "";
        $this->hierarchy = "false";
        $this->fieldList = "number Номер~docDate Дата";
        $this->sortOrder = "docDate ASC";
        $this->sortField = $this->sortOrder;
        $this->registered = 0;
        $this->deleted = 0;
        $this->registerConfirmation = "false";
		$this->models[] = "entityBase";
		$this->models[] = "Document";
        $params = $old_params;               
        parent::construct($params);
        
        $this->classTitle = "Документ";
        $this->classListTitle = "Документы";
        $this->classType = "Документ";
        
        $this->tabs_string  = "main|Основное|".$this->skinPath."images/spacer.gif";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_".get_class($this).$this->name;
        $this->active_tab = "main";
        $this->tabsetName = $this->tabset_id;
 		$this->icon = $this->skinPath."images/Tree/document.png";       
        $this->handler = "scripts/handlers/docflow/core/Document.js";  
        $this->printForms = $this->getPrintForms(); 
        $this->entityDataTableClass = "DocFlowDocumentTable"; 
        $this->clientClass = "Document";
        $this->parentClientClasses = "Entity";        
        $this->profileClass = "DocumentProfile";        
    }
    
    function checkData() {    	    	
    	if (!$this->isGroup) {
	    	if ($this->number=="") {
	    		$this->reportError("Укажите номер документа !","save");
	    		return 0;
	    	}	    	
	    	if ($this->docDate=="") {
	    		$this->reportError("Укажите дату документа !","save");
	    		return 0;
	    	}	    	
	    	$this->setRole();
        	if (@$this->getRoleValue($this->role["numbering"])=="periodicYear") {
        		$date = getdate(time());
        		$year = $date["year"];
        		$begin = mktime(0,0,0,1,1,$year);
        		$end = mktime(23,59,59,12,31,$year);
				$dateCondition = "@docDate>=".$begin."000 AND @docDate<=".$end."000 AND";         		
        	} else
        		$dateCondition = "";
	    	if ($this->name!="")
	    		$query = "SELECT entities FROM fields WHERE @name!=".$this->name." AND $dateCondition AND @number=".$this->number." AND @classname='".get_class($this)."'";
	    	else
	    		$query = "SELECT entities FROM fields WHERE $dateCondition @number=".$this->number." AND @classname='".get_class($this)."'";
	    	$query = str_replace(" AND AND "," AND ",$query);
	    	$query = str_replace(" WHERE AND "," WHERE ",$query);

	    	$result = PDODataAdapter::makeQuery($query, $this->adapter,$this->module_id);
	    	if (count($result)>0) {
	    		$this->reportError("Документ с таким номером уже существует","save");
	    		return 0;
	    	}    	 
    	}   
   	    //$this->modifications = date("d.m.Y H:i:s")." - ".$this->app->User."\n".$this->modifications;
        if ($this->old_name=="") {
            $this->dateCreated = time();
            $this->user = $this->user;
        }        
        if ($this->docDate=="") {
            $this->docDate = time()."000";
        }
    	return true;        
    }
    
    function getAuthor() {
    	if ($this->name=="" or $this->name=="List") {    		
    		return parent::getAuthor();
    	}
    	if ($this->adapter!="") {
    		if (!$this->adapter->connected)
    			$this->adapter->connect();
    		if ($this->adapter->connected)
    			$this->user = $this->adapter->getAuthor($this);
    	}
   		return $this->user;
    }
    
    function getPresentation($full=true) {
		if ($this->noPresent)
			return "";
		$this->loaded = false;
		$this->load();		
    	if ($this->number=="")
    		return "";
    	if (!$full)
    		return $this->classTitle." № ".$this->number." от ".date("d.m.Y H:i:s",substr($this->docDate,0,-3));
        if ($this->docDate=="")
            $this->docDate= time()."000";
        if ($this->classTitle=="")
        	return $this->title." № ".$this->number." от ".date("d.m.Y H:i:s",substr($this->docDate,0,-3));
        else
        	return $this->classTitle." № ".$this->number." от ".date("d.m.Y H:i:s",substr($this->docDate,0,-3));
    }
    
    function getId() {
        if ($this->name=="")
            $this->name = $this->entityId;
        if ($this->module_id != "")
            return get_class($this)."_".$this->module_id."_".$this->name;
        else
            return get_class($this)."_".$this->name;
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
            
        //if ($this->registered!=$this->old_registered) {
			if ($this->registered)
				$this->register();
			else
				$this->unregister();
		//}
		$app = $Objects->get($this->module_id);
		$this->adapter = $app->getAdapter($this);				
    }    

    function load() {
    	parent::load();
    	$this->hiddenFields = "";
    	$this->old_registered = $this->registered;
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
        if ($this->number=="") {
        	$date = getdate(time());
        	$year = $date["year"];
        	$begin = mktime(0,0,0,1,1,$year);
        	$end = mktime(23,59,59,12,31,$year);
        	$this->setRole();
        	if (@$this->getRoleValue($this->role["numbering"])=="periodicYear")
            	$this->number = $this->adapter->getMaxValue("fields","number","@docDate>=".$begin."000 AND @docDate<=".$end."000 AND @classname='".get_class($this)."'");
        	else
        		$this->number = $this->adapter->getMaxValue("fields","number","@classname='".get_class($this)."'");
        }
        $this->setRole();
        
        if ($this->registered) {
			$this->registerButtonTitle = "Отмена проведения";
			if (@$this->role["canViewMovements"]!="false")
				$this->movementsDisplayStyle = "";
			else
				$this->movementsDisplayStyle = "none";
        }
		else {
			$this->registerButtonTitle = "Провести";
			$this->movementsDisplayStyle = "none";
		}
		if (count($this->printForms)>0 and @$this->getRoleValue($this->role["canPrint"])!="false")
			$this->printDisplayStyle = "";
		else
			$this->printDisplayStyle = "none";
		$this->printFormsCount = count($this->printForms);
		if ($this->printFormsCount>0) {
			$keys = array_keys($this->printForms);
			$this->firstPrintForm = $keys[0];
		}
		
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
		$this->tagsTableCode = '$object->entityObject="'.$this->getId().'";$object->parent_object_id="'.$this->getId().'";';		
		
        return parent::getArgs();
    }
    
    function printDocument($formName="") {    	 
    	if ($this->getRoleValue($this->role["canPrint"])=="false") {
    		$this->reportError("Не достаточно прав доступа");
    		return 0;
    	};
		return '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body bgcolor="#FFFFFF"/>';
	}
	
	function getPrintForms() {
		return array();
	}
    
    function register() {
    	if (count($this->role)==0)
    		$this->setRole();
    	if (!$this->registered and $this->getRoleValue($this->role["canRegister"])=="false") {
    		//$this->reportError("Не достаточно прав доступа","register");
    		//return 0;
    	};
		$this->unregister();
	}
	
	function unregister() {
		global $Objects;
    	if (count($this->role)==0)
    		$this->setRole();
		if ($this->registered and $this->getRoleValue($this->role["canUnregister"])=="false") {
    		//$this->reportError("Не достаточно прав доступа","unregister");
    		//return 0;
    	};
		$reg = $Objects->get("Registry_".$this->module_id."_");
		$reg->registratorDeleted($this);
	}
	
	function getEntityImage() {		
		return parent::getEntityImage();
	}
	
	function getHookProc($number) {
		switch($number) {
			case "prn": return "printDocumentHook";
			case '3': return "showListHook";				
		}
		return parent::getHookProc($number);
	}
	
	function showListHook($arguments=null) {
		$object = $this;
		$object->overrided='width,height';
		$object->width=750;$object->height=450;
		$object->className="*".get_class($this)."*";
		$object->defaultClassName=get_class($this);
		$object->loaded=true;
		$object->template="templates/docflow/core/DocumentList.html";
		$object->title="Список документов ".$this->classTitle;
	}	
	
	function printDocumentHook($arguments) {
		$formName = "";
		if (isset($arguments["formName"]))
			$formName = $arguments["formName"];
		echo $this->printDocument($formName);
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
		$this->registered = "0";
		parent::afterCopyFrom();
	}
	
}
?>