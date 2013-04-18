<?php
class ModelConfig extends WABEntity {
	
	public $object_data = array();
	
	function construct($params) {
		global $Objects,$models,$groups;
		$this->module_id = array_shift($params)."_".array_shift($params);
		$this->metadata_class = array_shift($params);
		if (count($params)==0) {
			$this->name = $this->metadata_class;
			$this->metadata_field = "";
		} else {
			$this->metadata_field = implode("_",$params);
			if (strpos($this->metadata_field,'~')!==FALSE) {
				$arr = explode("~",$this->metadata_field);
				$this->metadata_field = $arr[0];
				$this->metadata_subfield = $arr[1];
				$this->name = $this->metadata_subfield; 				
			} else				
				$this->name = $this->metadata_field; 
		}
		$this->template = "renderForm";
		$app = $Objects->get("Application");
		if (!$app->initiated)
			$app->initModules();
		$this->app=$app;
		$this->skinPath = $app->skinPath;
		$this->css = $this->skinPath."styles/Mailbox.css";
		$this->handler = "scripts/handlers/core/WABEntity.js";
		$this->icon = $this->skinPath."images/Tree/systemsettings.png";
		$this->width = "600";
		$this->height = "550";
		$this->overrided = "width,height";
		$this->showButtons = true; 
        $this->loaded = false;
		
		// Определяем, где находятся метаданные
		if ($this->metadata_field=="") {
			$this->object_data = $GLOBALS[$this->metadata_class];
			$this->models = array($this->metadata_class);
		}
		else {
			if ($this->metadata_subfield=="") {
				$this->object_data = $GLOBALS[$this->metadata_class][$this->metadata_field];
				$this->models = array($this->metadata_field);
			}
			else {
				$this->object_data = $GLOBALS[$this->metadata_class][$this->metadata_field][$this->metadata_subfield];
				$this->models = array($this->metadata_subfield);
			}
		}		
	}
	
	function getControlType($field_name,$properties) {
		global $modules;
		if ($field_name=="defaultModule") {
			$names = array();
			$titles = array();
			foreach ($modules as $key=>$value) {
				$names[] = $key;
				$titles[] = $value["title"];
			}
			return "list,".implode("~",$names)."|".implode("~",$titles);
		}
		if ($field_name=="language")
			return "list,".implode("~",array_keys($this->app->languages))."|".implode("~",array_values($this->app->languages));
		if ($field_name=="skinPath") {
			$strings = file("/etc/WAB2/config/skins");
			$arr = array();
			foreach($strings as $line) {
				$parts = explode("|",$line);
				$arr[$parts[0]] = $parts[1];
			}
			return "list,".implode("~",array_keys($arr))."|".implode("~",array_values($arr));				
		}
		return @$properties["{".$field_name."_type}"];
	}
	
	function renderForm() {
		global $groups,$fields,$models;
		if (!$this->loaded)
			$this->load();		
		$blocks = getPrintBlocks(file_get_contents("templates/metadata/ModelConfig.html"));
		$args = array("{md_name}" => @$this->object_data["name"], "{collection}" => $this->metadata_class);
		$out = strtr($blocks["header"],$args); 
		$args = array("{module_id}" => $this->module_id, "{object_id}" => $this->getId(), "{window_id}" => $this->window_id, "{tabs_string}" => $this->tabs_string, "{active_tab}" => $this->active_tab);
		$out .= strtr($blocks["tabset"],$args);
		$params = $this->getArgs();
		$counter = 0;		
		foreach (@$models[$this->models[0]]["groups"] as $tab) {
			if ($counter==0)
				$displayStyle = "";
			else
				$displayStyle = "none";
			$args = array("{tabId}" => $tab, "{displayStyle}" => $displayStyle);
			$out .= strtr($blocks["tabHeader"],$args);
			foreach($groups[$tab]["fields"] as $field_name) {
				if ($field_name=="")
					continue;
				$title = @$fields[$field_name]["params"]["title"];
				if ($title=="")
					continue;
				$id = $field_name;
				$properties = @$params["{".$field_name."_properties}"];
				$type = $this->getControlType($field_name,$params);
				$value = @$this->object_data[$field_name];			
				if ($type=="array")
					$value = implode("~",$value);
				$args = array("{id}" => $id, "{title}" => $title, "{properties}" => $properties, "{value}" => $value, "{type}" => $type, "{name}" => $field_name);
				$out .= strtr($blocks["tabRow"],$args);
			}			
			$out .= $blocks["emptyRow"].$blocks["tabFooter"];
			$counter++;
		}		
		if ($this->showButtons)
			$out .= str_replace("{title}",$this->title,$blocks["buttons"]);
		$out .= $blocks["footer"];
		return $out;
	}
	
	function load() {
		global $models,$groups;		
		// Строим панель закладок, соответствующих группам метаданных
		$this->tabset_id = "WebItemTabset_".$this->module_id."_".$this->name."Metadata";
		$tabs = array();
		foreach(@$models[$this->models[0]]["groups"] as $v) {
			$value = $groups[$v];
			$tabs[] = $v."|".$value["title"]."|".$this->skinPath."images/spacer.gif";
		}
		$this->tabs = $tabs;
		$this->tabs_string = implode(";",$tabs);
		$this->active_tab = @$models[$this->models[0]]["groups"][0];		
		// Загружаем значения метаданных в поля объектов
		foreach($this->object_data as $key=>$value) {
			$this->fields[$key] = $value;	
		}
		$this->loaded = true;
	}
	
	function checkData() {
		global $models,$fields;
		foreach($models[$this->models[0]] as $key=>$value) {
			if ($key=="groups" or $key=="file")
				continue;
			if (@$fields[$value]["params"]["required"]) {
				if (trim($this->fields[$value]=="")) {
					$this->reportError("Поле '".$fields[$value]["params"]["title"]."' не заполнено !");
					return false;
				}
			}
		}
		return parent::checkData();
	}
	
	function save($arguments=null) {
		global $models,$fields;
		if (isset($arguments)) {
			if (!$this->loaded)
				$this->load();
			$this->setArguments($arguments);
		}
		if ($this->checkData()) {
			foreach($models[$this->models[0]] as $key=>$value) {
				if ($key=="groups" or $key=="file" or $key=="metaTitle")
					continue;
				if (@$fields[$value]["type"]=="array") {
					$this->fields[$value] = explode("~",$this->fields[$value]);					
				}
				$v = str_replace("xyxyxy","\n",$this->fields[$value]);				
				$this->object_data[$value] = $v; 
			}
			if ($this->metadata_field=="") {
				$GLOBALS[$this->metadata_class] = $this->object_data;
			}
			else
				$GLOBALS[$this->metadata_class][$this->metadata_field] = $this->object_data;
		}
		$str = "<?php\n".getMetadataString(getMetadataInFile($this->object_data["file"]))."\n?>";
		file_put_contents($this->object_data["file"],$str);		
	}
	
	function getPresentation() {
		return "Основные параметры панели управления";
	}
	
	function getId() {
		if ($this->metadata_subfield!="")
			return get_class($this)."_".$this->module_id."_".$this->metadata_class."_".$this->metadata_field."~".$this->metadata_subfield;
		else if ($this->metadata_field!="")
			return get_class($this)."_".$this->module_id."_".$this->metadata_class."_".$this->metadata_field;
		else
			return get_class($this)."_".$this->module_id."_".$this->metadata_class;
	}
	
	function getHookProc($number) {
		switch ($number) {
			case '3': return 'save';
		}	
		return parent::getHookProc($number);
	}
}
?>