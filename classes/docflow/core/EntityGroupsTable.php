<?php
// Таблица групп сущностей, разложенных по вкладкам
class EntityGroupsTable extends WABEntity {
	
	public $tabs = array();
	
	function construct($params) {		
		parent::construct($params);
		global $Objects;
		$this->clientClass = "EntityGroupsTable";
		$this->hierarchy = true;
		$this->parentClientClasses = "Entity";
		$this->template = "renderForm";
        $this->width = "600";
        $this->height = "350";        
        $this->overrided = "width,height,dock_left,dock_right,dock_top,dock_bottom,has_maximize,headless,resizeable_left,resizeable_right,resizeable_top,resizeable_bottom,moveable,has_close";
        $this->handler = "scripts/handlers/docflow/core/EntityGroupsTable.js";
        $this->type = "1";
        $this->itemsPerPage = "10";     
		$this->icon = $this->skinPath."images/tree/folder.png";		
        $this->app = $Objects->get("Application");
        $this->skinPath = $this->app->skinPath;
        $this->unchangeable = "true";
        $this->icon = $this->skinPath."images/docflow/briefcase.png";
        $this->topLinkObject = "";
        $this->linksWindow = "";
        $this->ownerObject = "";
        $this->dock_left = "1";
        $this->has_maximize = "0";
        $this->dock_right = "1";
        $this->dock_bottom = "1";
        $this->dock_top = "1";
        $this->resizeable = "0";
        $this->resizeable_left = "0";
        $this->resizeable_right = "0";
        $this->resizeable_top = "0";
        $this->resizeable_bottom = "0";
        $this->moveable = "0";
        $this->headless = "0";
        $this->has_close = "0";
        if ($this->name=="List") {
        	$this->tabTitles = "ReferenceContragents,ReferenceContacts,ReferenceProducts,DocumentContragentRequest,DocumentOrder,DocumentContract,DocumentInvoice,DocumentQuickSale,ReferenceProjects,DocumentTask,DocumentWorkReport,ReferenceEmailAddresses,ReferenceNotes,ReferenceFiles";
        	$this->linksWindow = "EntityGroupsTable_".$this->module_id."_LinksWindow";
        	$this->object_text = "Рабочее место пользователя";
        }       
	}
	
	function renderForm() {
		if (!$this->loaded)
			$this->load();
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/core/EntityGroupsTable.html"));
		$out = $blocks["header"];
		global $Objects;
		$display = "";
		foreach ($this->tabs as $tab) {
			$obj = $Objects->get($tab["name"]."_".$this->module_id."_".$tab["name"]);			
			$arr = array();
			$arr["{tabTableId}"] = $tab["tabTableId"];
			$arr["{tab}"] = $tab["name"];
			$arr["{tabCode}"] = $tab["tableCode"];
			$arr["{display}"] = $display; 
			$out .= strtr($blocks["tab"],$arr);
			$display = "none";
		}		
		$out .= $blocks["footer"];
		return $out;
	}
	
	function getPresentation() {
		if ($this->noPresent)
			return "";
		$this->loaded = false;
		$this->load();
		if ($this->classTitle != "")
			return $this->classTitle.":".$this->title;
		else
			return $this->title;
	}	
	
	function load() {
		global $Objects;
		$this->tabset_id = "WebItemTabset_".$this->module_id."_EntityGroupsTable".$this->name;
		$this->tabsetName = $this->tabset_id;
		$this->tabs = array();
		if ($this->topLinkObject=="") {
			$this->setRole();
			if (isset($this->role["tabTitles"]))
				$tabTitles = $this->role["tabTitles"];
			else
				if ($this->tabTitles!="") {
					$tabTitles = explode(",",$this->tabTitles);
				}
		} else {
			if ($this->tabTitles!="") {
				$tabTitles = explode(",",$this->tabTitles);			
			}
		}
		if ($this->topLinkObject!="") {
			$obj = $Objects->get($this->topLinkObject);
			$titles = $obj->getLinkClasses();
			foreach ($titles as $title) {
				if (!in_array($title, $tabTitles))
					$tabTitles[] = $title;
			}
		}		
		$tabs_strings = array();
		$this->active_tab = "";
		if ($this->topLinkObject!="") {
			$obj = $Objects->get($this->topLinkObject);
			$this->topLinkName = $obj->name;				
		}
		foreach ($tabTitles as $tab) {
			$this->tabs[$tab]["name"] = $tab;
			$obj = $Objects->get($tab."_".$this->module_id."_".$tab);
			if (!$obj->loaded)
				$obj->load();
			$this->tabs[$tab]["title"] = $obj->classListTitle;
			$this->tabs[$tab]["tableCode"] = "";
			if ($this->topLinkObject!="")
				$this->tabs[$tab]["tableCode"] .= '$object->topLinkObject="'.$this->topLinkObject.'";';
			if ($this->ownerObject!="")
				$this->tabs[$tab]["tableCode"] .= '$object->ownerObject="'.$this->ownerObject.'";';				
			$this->tabs[$tab]["tableCode"] .=  '$object->window_id="'.$this->window_id.'";
												$object->parent_object_id="'.$this->getId().'";
												$object->className="'.$tab.'";
												$object->defaultClassName="'.$tab.'";
												$object->fieldList="'.$obj->fieldList.'";
												$object->itemsPerPage='.$this->itemsPerPage.';
												$object->currentPage=1;
												$object->hierarchy = 1;
												$object->parentTabset = "'.$this->getId().'";
												$object->additionalFields="'.$obj->additionalFields.'";
												$object->condition="'.$obj->condition.'";
												$object->tagsCondition="'.$obj->tagsCondition.'";
												$object->adapterId="'.$obj->adapter->getId().'";
												$object->autoload="false";
												$object->sortOrder="'.$obj->sortOrder.'";';
			$this->tabs[$tab]["tableCode"] = cleanText($this->tabs[$tab]["tableCode"]);
			$this->tabs[$tab]["tabTableId"] = $obj->entityDataTableClass."_".$this->module_id."_".$obj->name.$this->topLinkName;
			$tabs_strings[] = $tab."|".$obj->classListTitle."|".$obj->icon;
			if ($this->active_tab=="")
				$this->active_tab = $tab;
		}		
		$this->tabs_string = implode(";",$tabs_strings);
		if ($this->topLinkObject!="") {
			$obj = $Objects->get($this->topLinkObject);
			$this->object_text = "Связи ".$obj->getPresentation();
		}
		$this->loaded = true;				
	}
			
	function getArgs() {		
		global $Objects;   
		if (!$this->loaded)
			$this->load();
		if ($this->window_id=="")
			$this->window_id = @$_GET["window_id"];
		$tabs = array();
		foreach ($this->tabs as $tab) {
			$tabs[$tab["name"]]["name"] = $tab["name"];			
			$tabs[$tab["name"]]["title"] = $tab["title"];
			$tabs[$tab["name"]]["tabTableId"] = $tab["tabTableId"];				
		}		
		$this->tabsCode = json_encode($tabs);
		$this->tabsetCode = '$object->module_id="'.$this->module_id.'";
							 $object->item="'.$this->getId().'";
							 $object->window_id="'.$this->window_id.'";
							 $object->tabs_string="'.$this->tabs_string.'";
							 $object->active_tab="'.$this->active_tab.'";';	
				
		$this->tabsetCode = cleanText($this->tabsetCode);
						
		return parent::getArgs();
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
		$object->load();
		$object->overrided='width,height';
		$object->width=750;$object->height=350;
		$object->className="*".get_class($this)."*";
		$object->defaultClassName=get_class($this);
		$object->loaded=true;
		$object->title=$this->classListTitle;
	}	
}
?>