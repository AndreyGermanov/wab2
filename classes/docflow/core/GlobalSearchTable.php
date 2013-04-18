<?php
/**
 * Данный класс описывает таблицу результатов глобального поиска по
 * сущностям.
 * 
 * В нее передаются параметры:
 * 
 * searchText - текст для поиска
 * classesList - список наименований классов сущностей, по которым производится поиск
 * fieldsList - список полей, по которым производится поиск
 * 
 * Поиск производится по полю value2 таблицы fields, используя механизм полнотекстового
 * поиска. Фактически выполняется следующий запрос
 * 
 * SELECT entityId, classname, name, value FROM fields WHERE MATCH('searchText' IN BOOLEAN MODE) AGAINST (value2) 
 *                                                           AND classname IN (classesList)
 *                                                           AND name IN (fieldsList)
 * 
 * Строки результата сортируются по релевантности и выводятся в виде таблицы сущностей с одной колонкой, в которой
 * отображается представление сущности.
 * 
 * При двойном щелчке мышью по строке открывается сущность.
 * 
 * Если classesList и fieldsList не указаны, они берутся из настроек (settings) данного класса или объекта или
 * из профиля для данной роли из параметров classesList и fieldsList
 * 
 * Также пользователь может сам сохранять персональные настройки этих списков в массивы classesList и fieldsList.
 *
 * @author andrey
 */
class GlobalSearchTable extends DataTable {

	public $classesList = array();
	public $fieldsList = array();
	
    function construct($params) {
        parent::construct($params);        
        $this->persistedFields = "";
        $this->className = "";
       
        $this->condition = "";
        $this->hierarchy = false;

        $this->itemsPerPage = 10;
        $this->currentPage = 1;
        $this->entityId = "";
		$this->autoload = "false";
		$this->entityImagesStr = "";
		$this->additionalLinksStr = "";
		$this->topLinkObject = "";
		$this->searchText = "";
		$this->autoload = "true";
		$this->width = "600";
		$this->height = "350";
		$this->overrided = "width,height";
		$this->setRoleArgs();
		
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;

        $this->css = $this->skinPath."styles/DataTable.css";
        $this->entityImage = $this->skinPath."images/Buttons/EntityImage.png";
        $this->groupImage = $this->skinPath."images/Buttons/entityGroupImage.png";
		$this->icon = $this->skinPath."images/Buttons/gfind.png";
		
        $this->defaultClassName = "";
        $this->entityImageField = "";

        $this->parentField = "parent";
        $this->sortField = "sortOrder";

        $this->editorType = "WABWindow";

        $this->contextMenuId = "";

        $this->parent_object_id = "";

        $this->windowWidth = 400;
        $this->windowHeight = 500;
        $this->template = "templates/docflow/core/GlobalSearchTable.html";

        $this->windowTitle = "";
        $this->additionalFields = "";
        $this->divName = "";
        $this->destroyDiv = false;
        $this->showHierarchy = false;

        $this->tableId = "";
        $this->forEntitySelect = false;     
           
        $this->handler = "scripts/handlers/docflow/core/GlobalSearchTable.js";
        $this->classTitle = "Глобальный поиск";
        $this->classListTitle = "Глобальный поиск";
        $this->profileClass = "GlobalSearchProfile";        
        $this->clientClass = "GlobalSearchTable";
        $this->parentClientClasses = "DataTable~Entity";        
    }    

    function getPresentation() {
    	return "Глобальный поиск";
    }
    
    function getArgs($arguments) {
    	    	
        global $Objects;
        $arguments = (array)$arguments;
        if (is_array($arguments)) {
        	if ($arguments["classesList"]!="")
        		$this->classesList = explode(",",$arguments["classesList"]);
        	if ($arguments["fieldsList"]!="")
        		$this->fieldsList = explode(",",$arguments["fieldsList"]);
        }
        
        if ($this->searchText!="")
        	$this->searchText = "*".str_replace("*","",$this->searchText)."*";               
        
        $this->setSettings();
        $this->setRole();
        
        $this->classesListStr = "";
        $this->fieldsListStr = "";
        
        if (count($this->classesList)==0) {
        	if (isset($this->role["classesList"]))
        		$this->classesList = $this->role["classesList"];
        	else if (isset($this->settings["classesList"]))
        		$this->classesList = $this->settings["classesList"];
        	else
        		$this->classesList = "";
        	$this->classesListStr = json_encode($this->classesList);
        } else 
        	$this->classesListStr = json_encode($this->classesList);
        	
        if (count($this->fieldsList)==0) {
        	if (isset($this->role["fieldsList"]))
        		$this->fieldsList = $this->role["fieldsList"];
        	else if (isset($this->settings["fieldsList"]))
        		$this->fieldsList = $this->settings["fieldsList"];
        	$this->fieldsListStr = json_encode($this->fieldsList);
        } else 
        	$this->fieldsListStr = json_encode($this->fieldsList);
        	        
        $condition = "";
        $adapter = $Objects->get("DocFlowDataAdapter_".$this->module_id."_Adapter");
        $count = 0;
        $query = "SELECT DISTINCT classname,entityId FROM fields WHERE MATCH(value2) AGAINST('".$this->searchText."' IN BOOLEAN MODE)";
        $countquery = "SELECT DISTINCT count(entityId) AS cnt FROM fields WHERE MATCH(value2) AGAINST('".$this->searchText."' IN BOOLEAN MODE)";        
        if (is_array($this->classesList) and count($this->classesList)>0) {
        	$arr = array();
        	foreach ($this->classesList as $value)
        		$arr[] = "'".$value."'";
        	$condition .= " AND classname IN (".implode(",",$arr).")";
        }
        if (is_array($this->fieldsList) and count($this->fieldsList)>0) {
        	$arr = array();
        	foreach ($this->fieldsList as $value)
        		$arr[] = "'".$value."'";
        	$condition .= " AND name IN (".implode(",",$arr).")";
        }
        $query .= $condition;
        $countquery .= $condition;
        if (!$adapter->connected)
            $adapter->connect();

        if ($adapter->connected) {
            $stmt = $adapter->dbh->prepare($countquery);
	    $stmt->execute();
  		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
  		if (isset($res[0]))
       		$count = $res[0]["cnt"];
   		else
   			$count = 0;  
   	    }
        if ($this->itemsPerPage!=0) {
			$limit = (($this->itemsPerPage)*($this->currentPage-1)).",".$this->itemsPerPage;                
        } else
           $limit = "";
        if ($limit!="")
            $limit = "LIMIT ".$limit;
        
        if ($adapter->connected) {
            $stmt = $adapter->dbh->prepare($query." ".$limit);
		   	$stmt->execute();
    		$entities = $stmt->fetchAll();
        }
        $this->entityCount = $count;
        if ($this->itemsPerPage>0)
            $this->numPages = ceil($this->entityCount/$this->itemsPerPage);
        else
           $this->numPages = 0;

        $this->adapterId = $adapter->getId();
        $result = parent::getArgs();
        $id = $this->getId();
            
        $str = "";
        $str .= $this->getId()."Rows = new Array;\n";
        $str .= $this->getId()."Rows[0] = new Array;\n";
        $str .= $id."tbl.columns = new Array;\n";
        $str .= $this->getId()."Rows[0]['class'] = '';\n";
        $str .= $this->getId()."Rows[0]['properties'] = 'valign=top';\n";
        $str .= $this->getId()."Rows[0]['cells'] = new Array;\n";
          
        $str .= $id."tbl.columns[0] = new Array;\n";
        $str .= $id."tbl.columns[0]['name'] = 'entityName';\n";
        $str .= $id."tbl.columns[0]['title'] = '';\n";
        $str .= $id."tbl.columns[0]['class'] = 'hidden';\n";
        $str .= $id."tbl.columns[0]['properties'] = 'width=1';\n";
        $str .= $id."tbl.columns[0]['control'] = 'plaintext';\n";
        $str .= $id."tbl.columns[0]['control_properties'] = '';\n";
        $str .= $id."tbl.columns[0]['must_set'] = false;\n";
        $str .= $id."tbl.columns[0]['unique'] = false;\n";
        $str .= $id."tbl.columns[0]['readonly'] = false;\n";
        $str .= $id."tbl.columns[1] = new Array;\n";
        $str .= $id."tbl.columns[1]['name'] = 'entityName1';\n";
        $str .= $id."tbl.columns[1]['title'] = '';\n";
        $str .= $id."tbl.columns[1]['class'] = 'hidden';\n";
        $str .= $id."tbl.columns[1]['properties'] = 'width=1';\n";
        $str .= $id."tbl.columns[1]['control'] = 'plaintext';\n";
        $str .= $id."tbl.columns[1]['control_properties'] = '';\n";
        $str .= $id."tbl.columns[1]['must_set'] = false;\n";
        $str .= $id."tbl.columns[1]['unique'] = false;\n";
        $str .= $id."tbl.columns[1]['readonly'] = false;\n";
        

        $str .= $this->getId()."Rows[0]['cells'][0] = new Array;\n";
        $str .= $this->getId()."Rows[0]['cells'][0]['properties'] = 'width=1%';\n";
        $str .= $this->getId()."Rows[0]['cells'][0]['class'] = 'hidden';\n";
        $str .= $this->getId()."Rows[0]['cells'][0]['value'] = 'header';\n";
        $str .= $this->getId()."Rows[0]['cells'][0]['control'] = 'plaintext';\n";
        $str .= $this->getId()."Rows[0]['cells'][0]['control_properties'] = '';\n";
        $str .= $this->getId()."Rows[0]['cells'][1] = new Array;\n";
        $str .= $this->getId()."Rows[0]['cells'][1]['properties'] = 'width=1%';\n";
        $str .= $this->getId()."Rows[0]['cells'][1]['class'] = 'hidden';\n";
        $str .= $this->getId()."Rows[0]['cells'][1]['value'] = 'header';\n";
        $str .= $this->getId()."Rows[0]['cells'][1]['control'] = 'plaintext';\n";
        $str .= $this->getId()."Rows[0]['cells'][1]['control_properties'] = '';\n";
        
        $str .= $id."tbl.columns[2] = new Array;\n";
        $str .= $id."tbl.columns[2]['name'] = 'entityImage';\n";
        $str .= $id."tbl.columns[2]['title'] = '';\n";
        $str .= $id."tbl.columns[2]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[2]['properties'] = 'width=1%';\n";
        $str .= $id."tbl.columns[2]['control'] = 'hidden';\n";
        $str .= $id."tbl.columns[2]['control_properties'] = 'showValue=false';\n";
        $str .= $id."tbl.columns[2]['must_set'] = false;\n";
        $str .= $id."tbl.columns[2]['unique'] = false;\n";
        $str .= $id."tbl.columns[2]['readonly'] = false;\n";
        
        $str .= $this->getId()."Rows[0]['cells'][2] = new Array;\n";
        $str .= $this->getId()."Rows[0]['cells'][2]['properties'] = 'width=1%';\n";
        $str .= $this->getId()."Rows[0]['cells'][2]['class'] = 'header';\n";
        $str .= $this->getId()."Rows[0]['cells'][2]['value'] = '';\n";
        $str .= $this->getId()."Rows[0]['cells'][2]['control'] = 'plaintext';\n";
        $str .= $this->getId()."Rows[0]['cells'][2]['control_properties'] = '';\n";
        
        $str .= $id."tbl.columns[3] = new Array;\n";
        $str .= $id."tbl.columns[3]['name'] = 'presentation';\n";
        $str .= $id."tbl.columns[3]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[3]['properties'] = '';\n";
        $str .= $id."tbl.columns[3]['control'] = 'plaintext';\n";
        $str .= $id."tbl.columns[3]['control_properties'] = '';\n";
        $str .= $id."tbl.columns[3]['must_set'] = false;\n";
        $str .= $id."tbl.columns[3]['unique'] = false;\n";
        $str .= $id."tbl.columns[3]['readonly'] = false;\n";

        $str .= $this->getId()."Rows[0]['cells'][3] = new Array;\n";
        $str .= $this->getId()."Rows[0]['cells'][3]['properties'] = '';\n";
        $str .= $this->getId()."Rows[0]['cells'][3]['class'] = 'header';\n";
        $str .= $this->getId()."Rows[0]['cells'][3]['value'] = 'Объект';\n";
        $str .= $this->getId()."Rows[0]['cells'][3]['control'] = 'header';\n";
        $rw=1;
            
        if (isset($entities) and is_array($entities)) {
	        foreach ($entities as $entity) {
            	$str .= $this->getId()."Rows[$rw] = new Array;\n";
                $str .= $this->getId()."Rows[$rw]['class'] = '';\n";
                $str .= $this->getId()."Rows[$rw]['properties'] = 'valign=top';\n";
                $str .= $this->getId()."Rows[$rw]['cells'] = new Array;\n";
                $str .= $this->getId()."Rows[$rw]['cells'][0] = new Array;\n";
                $str .= $this->getId()."Rows[$rw]['cells'][0]['properties'] = 'width=1%';\n";
                $str .= $this->getId()."Rows[$rw]['cells'][0]['class'] = 'hidden';\n";
                $str .= $this->getId()."Rows[$rw]['cells'][0]['value'] = '".$entity["classname"]."_".$this->module_id."_".$entity["entityId"]."';\n";
                $str .= $this->getId()."Rows[$rw]['cells'][0]['control'] = 'plaintext';\n";
                $str .= $this->getId()."Rows[$rw]['cells'][0]['control_properties'] = '';\n";
                $str .= $this->getId()."Rows[$rw]['cells'][1] = new Array;\n";
                $str .= $this->getId()."Rows[$rw]['cells'][1]['properties'] = 'width=1%';\n";
                $str .= $this->getId()."Rows[$rw]['cells'][1]['class'] = 'hidden';\n";
                $str .= $this->getId()."Rows[$rw]['cells'][1]['value'] = '".$entity["classname"]."_".$this->module_id."_".$entity["entityId"]."';\n";
                $str .= $this->getId()."Rows[$rw]['cells'][1]['control'] = 'plaintext';\n";
                $str .= $this->getId()."Rows[$rw]['cells'][1]['control_properties'] = '';\n";
                
                $entityObject = $Objects->get($entity["classname"]."_".$this->module_id."_".$entity["entityId"]);                
                $entityImage = $entityObject->getEntityImage();
                $str .= $this->getId()."Rows[$rw]['cells'][2] = new Array;\n";
                $str .= $this->getId()."Rows[$rw]['cells'][2]['properties'] = 'width=1%';\n";
                $str .= $this->getId()."Rows[$rw]['cells'][2]['class'] = 'cell';\n";
                $str .= $this->getId()."Rows[$rw]['cells'][2]['value'] = 'false';\n";
                $str .= $this->getId()."Rows[$rw]['cells'][2]['control'] = 'hidden';\n";
                $str .= $this->getId()."Rows[$rw]['cells'][2]['control_properties'] = 'buttonImage=".$entityImage.",actionButton=true';\n";
                
                $str .= $this->getId()."Rows[$rw]['cells'][3] = new Array;\n";
                $str .= $this->getId()."Rows[$rw]['cells'][3]['properties'] = '';\n";
                $str .= $this->getId()."Rows[$rw]['cells'][3]['class'] = 'cell';\n";
                $str .= $this->getId()."Rows[$rw]['cells'][3]['value'] = '".$entityObject->getPresentation()."';\n";                    
                $str .= $this->getId()."Rows[$rw]['cells'][3]['control'] = 'static';\n";                    
                $str .= $this->getId()."Rows[$rw]['cells'][3]['control_properties'] = '';\n";
                $rw++;
            }
        }
            
        $str .= $this->getId()."EntityCount = ".$this->entityCount.";";
        $str .= $this->getId()."NumPages = ".$this->numPages.";";
        $this->data = $str;
        
        $this->loaded = true;
        $result["{data}"] = $this->data;
        $result["{className}"] = $this->className;
        if ($this->destroyDiv)
            $result["{destroyDivStr}"] = "true";
        else
            $result["{destroyDivStr}"] = "false";

        if ($this->forEntitySelect)
            $result["{forEntitySelectStr}"] = "true";
        else
            $result["{forEntitySelectStr}"] = "false";    
        return $result;
    }
    
    function getHookProc($number) {
    	switch($number) {
    		case '5': return "saveHook";
    	}
    	return parent::getHookProc($number);
    }
    
    function saveHook($arguments) {
    	global $Objects;
    	if (is_array($arguments)) {
    		if ($arguments["classesList"]!="")
    			$this->classesList = explode(",",$arguments["classesList"]);
    		if ($arguments["fieldsList"]!="")
    			$this->fieldsList = explode(",",$arguments["fieldsList"]);
    	}
    	$app = $Objects->get("Application");
    	if (!$app->initiated)
    		$app->initModules();
    	 
    	$object = $this;
    	mkdir("/var/WAB2/users/".$app->User."/settings",0777,true);
    	$str  = '$object->classesList = '.getArrayCode($this->classesList).";\n";
    	$str .= '$object->fieldsList = '.getArrayCode($this->fieldsList).";\n";
    	file_put_contents("/var/WAB2/users/".$app->User."/settings/".$this->getId(),$str);    	
    }
    
}
?>