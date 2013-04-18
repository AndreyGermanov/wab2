<?php
/**
 * Данный класс предназначен для отображения списка сущностей в виде таблицы.
 * Он выполняет запрос к указанному хранилищу adapter в котором он ищет сущности
 * указанных классов, которые передаются в формате сlassName, применяя при этом
 * условие condition. К этому условию также может добавляться параметры LIMIT,
 * которые вычисляются исходя из переменных currentPage и itemsPerPage. Параметр
 * sortOrder задает порядок сортировки.
 *
 * В колонках таблицы отображаются поля сущности, которые указываются в переменной
 * fieldList. Также добавляются две дополнительные колонки в начало: "GroupCheckbox"
 * с флажком, для того чтобы можно было выделять несколько строк для работы с ними
 * как с группой и entityImage - картинка-кнопка типа hidden, позволяющая разворачивать
 * сущность в случае если у нее есть дочерние элементы. Картинка, отображаемая
 * на кнопке определяется одной из двух иконок: entityIcon - если у сущности нет
 * потомков и groupIcon если у сущности есть потомки. Также вместо entityImage
 * может браться картинка из поля самой сущности. Поле определеяется параметром
 * entityImageField.
 *
 * Наличие потомков у сущности определяется значением поля сущности,
 * на которое указывает свойство parentField. Это должно быть поле типа Entity,
 * которая ссылается на родительскую сущность. Для каждой сущности определяется,
 * есть ли в базе данных сущности, у которых поле parentField указывает на нее
 * и если есть, то считается что у сущности есть потомки и в таблице для нее
 * отображается groupIcon. Будет ли вообще работать режим иерархичности определяется
 * параметром hierarchy. Если он установлен в true, то разворачивание по иерархии
 * будет работать, иначе нет.
 *
 * Также есть еще одна дополнительная колонка entityName, в которой находится имя
 * текущей сущности.
 *
 * Если указан параметр sortField и в нем указано поле, то по умолчанию сортировка
 * производится по этому полю и только в этом случае появляются кнопки "вверх"/
 * "вниз" над таблицей, что позволяет менять порядок сортировки полей вручную
 * на уровне базы данных, меняя значение поля sortField.
 *
 * 
 *
 * @author andrey
 */
class EntityDataTable extends DataTable {

	public $fieldAccess = array();
	public $fieldDefaults = array();
	public $entityImages = array();
	public $additionalLinks = array();
	
    function construct($params) {
        parent::construct($params);
        global $defaultCacheDataAdapter;
        $this->adapter = null;
        $this->persistedFields = "";
        $this->className = "";

        $this->condition = "@parent IS NOT EXISTS";
        $this->tagsCondition = "";
        $this->childCondition = "";
        $this->additionalCondition = "";
        $this->sortOrder = "";
        $this->fieldList = "";
        $this->hierarchy = false;
		$this->selectGroup = "1";
        $this->itemsPerPage = 0;
        $this->currentPage = 1;
        $this->entityId = "";
        $this->topLinkObject = "";
        $this->ownerObject = "";
        $this->table = "";

        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;               

        $this->entityImage = $this->skinPath."images/Buttons/EntityImage.png";
        $this->groupImage = $this->skinPath."images/Buttons/entityGroupImage.png";
        $this->defaultClassName = "";
        $this->entityImageField = "";

        $this->parentField = "parent";
        $this->sortField = "sortOrder";

        $this->editorType = "WABWindow";

        $this->contextMenuId = "";

        $this->parent_object_id = "";

        $this->windowWidth = 400;
        $this->windowHeight = 500;
        $this->currentPage = "1";
        $this->hierarchy = "false";
        $this->itemsPerPage = "10";
		$this->tableClassName = get_class($this);
        $this->windowTitle = "";
        $this->additionalFields = "";
        $this->divName = "";
        $this->destroyDiv = false;
		$this->collection = "";
		$this->className = "";
		$this->defaultClassName = "";
		$this->parentEntity = "";
		$this->collectionLoadMethod = "load";
		$this->collectionGetMethod = "";
        $this->tableId = "";
        $this->forEntitySelect = false;        
        $this->adapterId = "";
        $this->autoload = "true";
        $this->handler = "scripts/handlers/core/EntityDataTable.js";
        $this->clientClass = "EntityDataTable";
        $this->parentClientClasses = "DataTable~Entity";
        $this->valueTitle = "";
        $this->entityParentId = "";
        $this->parentTabset = "";
        $this->showHierarchy = 1;
        $this->defaultListProfile = "Основной";
    }

    function getArgs($arguments=array()) {
        global $Objects;
        
        if ($this->collection!="") {
        	$collection = $Objects->get($this->collection);
        	if ($this->collectionGetMethod=="") { 
        		$collectionLoadMethod = $this->collectionLoadMethod;
        		$collection->$collectionLoadMethod();
        	}        	
        }
        
        $app = $Objects->get("Application");
        $object = $this;
        if (file_exists("/var/WAB2/users/".$app->User."/settings/".$this->getId())) {
			eval(file_get_contents("/var/WAB2/users/".$app->User."/settings/".$this->getId()));
		}
		if ($this->defaultListProfile != "") {
			if (isset($this->listProfiles[$this->defaultListProfile])) {
				foreach ($this->listProfiles[$this->defaultListProfile] as $key=>$value)
					$this->fields[$key] = $value;
			}
		} else 
			$this->defaultListProfile = "Основной";
		$arr = array();
		if (count($this->listProfiles)>0) 
			$arr = array_keys($this->listProfiles);
		$arr = array_flip($arr);
		$arr["Основной"] = "Основной";
		$this->profilesList = implode("~",array_keys($arr))."|".implode("~",array_keys($arr));
		if (isset($arguments["defaultListProfile"]) and isset($arguments["prevListProfile"]) and isset($this->listProfiles[$arguments["prevListProfile"]]) and @$arguments["prevListProfile"]!=@$arguments["defaultListProfile"]) {
			$this->setArguments($arguments);
			if (isset($this->listProfiles[@$arguments["defaultListProfile"]])) {
				$this->fieldList = "";
				$this->printFieldList = "";
				$this->condition = "";
				$this->advancedCondition = "";
				$this->tagsCondition = "";
				$this->periodStart = "";
				$this->periodEnd = "";
				$this->sortField = "";
				$this->itemsPerPage = "";		
				$this->showQRCode = "";		
				$profileData["fieldList"] = "";
				$profileData["printFieldList"] = "";
				$profileData["condition"] = "";
				$profileData["advancedCondition"] = "";
				$profileData["tagsCondition"] = "";
				$profileData["periodStart"] = "";
				$profileData["periodEnd"] = "";
				$profileData["sortField"] = "";
				$profileData["itemsPerPage"] = "";				
				$profileData["showQRCode"] = "";		
				foreach ($this->listProfiles[@$arguments["defaultListProfile"]] as $key=>$value) {
					$this->fields[$key] = $value;
					$profileData[$key] = $value;
				}
			}
			$this->defaultListProfile = @$arguments["defaultListProfile"];				
		} else {
			//$str = print_r($arguments); 
			$this->setArguments($arguments);
		}
		
		$profileData = array();
		
        if ($this->topLinkObject!="" and !is_object($this->topLinkObject))
        	$this->topLinkObject = $Objects->get($this->topLinkObject);
        if (is_object($this->topLinkObject) and $this->topLinkObject->name!="")
        	$this->additionalLinks[$this->topLinkObject->name] = $this->topLinkObject->getId();
        
        $this->fieldAccessStr = json_encode($this->fieldAccess);
        $this->fieldDefaultsStr = json_encode($this->fieldDefaults);
        $this->entityImagesStr = json_encode($this->entityImages);
        $this->additionalLinksStr = json_encode($this->additionalLinks);
        $this->condition = str_replace('"',"'",$this->condition);
        $this->condition = str_replace('yoyo',"@",$this->condition);
        $this->condition = str_replace('zozo',"=",$this->condition);
        if ($this->adapterId!="")
            $this->adapter = $Objects->get($this->adapterId);     
        $this->loaded=false;
        $ob = $Objects->get($this->className."_".$this->module_id."_");
        $this->classTitle = @$ob->classTitle;
        $this->tableClassName = @$ob->entityTableClass;
        $this->treeClassName = "EntityTree";
        
        if ($this->loaded!=true) {
            if ($this->sortOrder=="")
                if (!@$this->adapter->isPDO)
                    $this->sortOrder = $this->sortField." ASC integers";
                else
                 $this->sortOrder = $this->sortField." ASC";
            $this->condition = str_replace("xoxo","#",$this->condition);
            $fldList = $this->fieldList;
            $this->fieldList = str_replace("~",",",$this->fieldList);            
            $this->sortOrder = str_replace("~",",",$this->sortOrder);
            if ($this->itemsPerPage!=0) {
                $limit = (($this->itemsPerPage)*($this->currentPage-1)).",".$this->itemsPerPage;
            } else
                $limit = "";
            if ($this->entityId!="" and $this->entityId!="-1" and method_exists($Objects->get($this->entityId),"getId") AND $this->adapterId!="") {
                $currentEntity = $Objects->get($this->entityId);
                if (!$currentEntity->loaded)
                    $currentEntity->load();

                if (@$currentEntity->parent->name!="") {
                    if (!$this->adapter->isPDO)
                        $this->condition = "#EntityField@parent=".$currentEntity->parent->name;
                    else {
                     $this->condition = "@parent='".get_class($currentEntity->parent)."_".$currentEntity->parent->name."'";
                     $this->entityParentId = $currentEntity->parent->getId();
                    }

                }
                else {
                    if (!$this->adapter->isPDO)
                        $this->condition = "#EntityField@parent=-1";
                    else
                     $this->condition = "@parent IS NOT EXISTS";
                }
            } 
            if ($this->module_id!="")
                $classname = $this->className."_".$this->module_id;
            else
              $classname = $this->className;
            
            $this->condition = str_replace($this->module_id."_","",$this->condition);
        	if (!$this->loaded)
        		$this->load();      
        	$condition = $this->condition;
        	$this->additionalCondition = trim($this->additionalCondition);
        	$arr = array();
        	if ($this->autoload=="true") {
	        	if (count($this->additionalLinks)>0) {
	        		foreach ($this->additionalLinks as $obj) {
	        			if (!is_object($obj))
	        				$obj = $Objects->get($obj);
	        			if (is_object($obj) and method_exists($obj,"getLinks")) {
	        				if (!$obj->loaded)
	        					$obj->load();	        				
	        				$links = $obj->getLinks(array($this->className));
		        			foreach ($links as $link)
	    	    				$arr[@$link->name] = @$link->name;
	        			}
	        		}

		       		$linksCondition = " @entityId IN (".implode(",",$arr).")";
		       		if (trim($condition)=="@parent IS NOT EXISTS")
		       			$condition = "";
	        	} 
	        	        	
	        	if ($this->additionalCondition!="") {        	
		        	if ($condition=="")  	
		            	$condition .= $this->additionalCondition;
		        	else if (trim($condition) != trim($this->additionalCondition))
		        		$condition = trim($condition)." AND ".trim($this->additionalCondition);
	        	}
        		if ($this->advancedCondition!="") {        	
		        	if ($condition=="")  	
		            	$condition .= $this->advancedCondition;
		        	else if (trim($condition) != trim($this->advancedCondition))
		        		$condition = trim($condition)." ".trim($this->advancedCondition);
	        	}
	        	if (isset($linksCondition) and $linksCondition!="") {
	        		if ($condition=="")
	        			$condition .= $linksCondition;
	        		else if (trim($condition) != trim($linksCondition))
	        			$condition = trim($condition)." AND ".trim($linksCondition);	        		 
	        	}
	        	$condition = trimSpaces($condition);
	        	if ($this->tagsCondition!="") {
	        		if ($condition=="")
	        			$condition = trimSpaces($this->tagsCondition);
	        		else
	        			$condition = trimSpaces($this->tagsCondition)." AND ".$condition;	        			 
	        	}

	        	if ($this->additionalFields!="")
	                $this->additionalFields = ",".str_replace("~",",",$this->additionalFields);	        	
	            if ($this->entityId!="" and $this->entityId!="-1" and $this->itemsPerPage!=0 and method_exists($Objects->get($this->entityId),"getId")) {
	                $entityNum = array_pop(explode("_",$this->entityId));
	                $entityNumber = $entityNum;
	                $limit = "";
	                if ($this->adapterId!="") {
		                $Objects->query($classname,"simple|".$this->fieldList.$this->additionalFields."|".$condition,$this->adapter,$this->sortOrder,&$limit,&$entityNum);
		                if ($entityNumber==$entityNum)
		                	$entityNum = 1;
	    	            $this->currentPage = ceil($entityNum/$this->itemsPerPage);
	    	            if ($this->currentPage<1)
	    	            	$this->currentPage = 1;
	        	        $limit = (($this->itemsPerPage)*($this->currentPage-1)).",".$this->itemsPerPage;
	            	    $entities = $Objects->query($classname,"simple|".$this->fieldList.$this->additionalFields.",persistedFields texts|".$condition,$this->adapter,$this->sortOrder,&$limit);
	                } else {
	                	if ($this->collectionGetMethod!="") {
	                		$classname = "procedure|".$this->collection."|".$this->collectionGetMethod;
	                		if ($this->collectionGetMethodParams!="")
	                			$classname .= "|".$this->collectionGetMethodParams;
	                	}
	                	$Objects->query($classname,$condition,"",$this->sortOrder,&$limit,&$entityNum);
		                if ($entityNumber==$entityNum)
		                	$entityNum = 1;
	                	$this->currentPage = ceil($entityNum/$this->itemsPerPage);
	                	
	                	$limit = (($this->itemsPerPage)*($this->currentPage-1)).",".$this->itemsPerPage;
	                	$entities = $Objects->query($classname,$condition,"",$this->sortOrder,&$limit);                	 
	                }
	            } else {
	            	if ($this->adapterId!="") {
	            		$entities = $Objects->query($classname,str_replace(",,",",","simple|".str_replace("presentation","user",$this->fieldList).$this->additionalFields.",persistedFields texts|".$condition),$this->adapter,$this->sortOrder,&$limit);
	            	} else {
	            	    if ($this->collectionGetMethod!="") {
	                		$classname = "procedure|".$this->collection."|".$this->collectionGetMethod;
	                		if ($this->collectionGetMethodParams!="")
	                			$classname .= "|".$this->collectionGetMethodParams;
	                	}                	
	            		$entities = $Objects->query($classname,$condition,"",$this->sortOrder,&$limit);            		
	            	}
	            }
	            $this->entityCount = $limit;
	            if ($this->itemsPerPage>0)
	                $this->numPages = ceil($limit/$this->itemsPerPage);
	            else
	                $this->numPages = 0;   
	            if (is_object($this->adapter))         
	            	$this->adapterId = $this->adapter->getId();
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
	
	            $str .= $this->getId()."Rows[0]['cells'][0] = new Array;\n";
	            $str .= $this->getId()."Rows[0]['cells'][0]['properties'] = 'width=1%';\n";
	            $str .= $this->getId()."Rows[0]['cells'][0]['class'] = 'hidden';\n";
	            $str .= $this->getId()."Rows[0]['cells'][0]['value'] = 'header';\n";
	            $str .= $this->getId()."Rows[0]['cells'][0]['control'] = 'plaintext';\n";
	            $str .= $this->getId()."Rows[0]['cells'][0]['control_properties'] = '';\n";
	
	            $str .= $id."tbl.columns[1] = new Array;\n";
	            $str .= $id."tbl.columns[1]['name'] = 'deleteField';\n";
	            $str .= $id."tbl.columns[1]['title'] = '';\n";
	            $str .= $id."tbl.columns[1]['class'] = 'cell';\n";
	            $str .= $id."tbl.columns[1]['properties'] = '';\n";
	            $str .= $id."tbl.columns[1]['control'] = 'plaintext';\n";
	            $str .= $id."tbl.columns[1]['control_properties'] = '';\n";
	            $str .= $id."tbl.columns[1]['must_set'] = false;\n";
	            $str .= $id."tbl.columns[1]['unique'] = false;\n";
	            $str .= $id."tbl.columns[1]['readonly'] = false;\n";
	
	            $str .= $this->getId()."Rows[0]['cells'][1] = new Array;\n";
	            $str .= $this->getId()."Rows[0]['cells'][1]['properties'] = 'width=1%';\n";
	            $str .= $this->getId()."Rows[0]['cells'][1]['class'] = 'header';\n";
	            $str .= $this->getId()."Rows[0]['cells'][1]['value'] = '';\n";
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
	
	            $c = 3;
	            $fieldList = explode(",",$this->fieldList);
	            foreach($fieldList as $field) {
	                if (stripos($field,"AS")!==FALSE) {
	                    $arr = explode("AS",$field);
	                    $name=$arr[0];
	                    $arr = explode(" ",$arr[1]);
	                    array_shift($arr);    
	                    array_shift($arr);
	                    $title = trim(implode(" ",$arr));
	                } else {
	                    $field_parts = explode(" ",$field);
	                    $name = array_shift($field_parts);
	                    if (count($field_parts)>0)
	                        $title = implode(" ",$field_parts);
	                    else
	                     $title = $name;
	                }
	                $str .= $id."tbl.columns[$c] = new Array;\n";
	                $str .= $id."tbl.columns[$c]['name'] = '".trim($name)."';\n";
	                $str .= $id."tbl.columns[$c]['class'] = 'cell';\n";
	                $str .= $id."tbl.columns[$c]['properties'] = '';\n";
	                $str .= $id."tbl.columns[$c]['control'] = 'plaintext';\n";
	                $str .= $id."tbl.columns[$c]['control_properties'] = '';\n";
	                $str .= $id."tbl.columns[$c]['must_set'] = false;\n";
	                $str .= $id."tbl.columns[$c]['unique'] = false;\n";
	                $str .= $id."tbl.columns[$c]['readonly'] = false;\n";
	
	                $str .= $this->getId()."Rows[0]['cells'][$c] = new Array;\n";
	                $str .= $this->getId()."Rows[0]['cells'][$c]['properties'] = '';\n";
	                $str .= $this->getId()."Rows[0]['cells'][$c]['class'] = 'header';\n";
	                $str .= $this->getId()."Rows[0]['cells'][$c]['value'] = '".str_replace("\n","",html_entity_decode($title,ENT_QUOTES,'UTF-8'))."';\n";
	                $str .= $this->getId()."Rows[0]['cells'][$c]['control'] = 'header';\n";
	                $c++;
	            }
	            $rw=1;
	            foreach ($entities as $entity) {	            	
	                $str .= $this->getId()."Rows[$rw] = new Array;\n";
	                $str .= $this->getId()."Rows[$rw]['class'] = '';\n";
	                $str .= $this->getId()."Rows[$rw]['properties'] = 'valign=top';\n";
	                $str .= $this->getId()."Rows[$rw]['cells'] = new Array;\n";
	                $str .= $this->getId()."Rows[$rw]['cells'][0] = new Array;\n";
	                $str .= $this->getId()."Rows[$rw]['cells'][0]['properties'] = 'width=1%';\n";
	                $str .= $this->getId()."Rows[$rw]['cells'][0]['class'] = 'hidden';\n";
	                $str .= $this->getId()."Rows[$rw]['cells'][0]['value'] = '".$entity->getId()."';\n";
	                $str .= $this->getId()."Rows[$rw]['cells'][0]['control'] = 'plaintext';\n";
	                $str .= $this->getId()."Rows[$rw]['cells'][0]['control_properties'] = '';\n";
	                $str .= $this->getId()."Rows[$rw]['cells'][1] = new Array;\n";
	                $str .= $this->getId()."Rows[$rw]['cells'][1]['properties'] = '';\n";
	                $str .= $this->getId()."Rows[$rw]['cells'][1]['class'] = 'cell';\n";
	                $str .= $this->getId()."Rows[$rw]['cells'][1]['value'] = 'false ';\n";
	                $str .= $this->getId()."Rows[$rw]['cells'][1]['control'] = 'boolean';\n";
	                $str .= $this->getId()."Rows[$rw]['cells'][1]['control_properties'] = 'control_type=checkbox';\n";
	                $isGroup = "isGroup=false";
	                if ($this->hierarchy) {
		                if ($entity->childrenCount()) {
		                	if ($entity->isGroup)
		                		$isGroup='isGroup=true';
		                	else
		                		$isGroup='isGroupItem=true';
						   $entImage = $entity->getEntityGroupImage();
						   if ($entImage=="") {
								$entityImage = $this->groupImage;
						   }
						   else
								$entityImage = $entImage;
	                       $explode = ",explode=true";
	                    } else {
	                       if (isset($this->entityImages[$entity->getId()]))
	                       		$entImage = $this->entityImages[$entity->getId()];
	                       else
						   		$entImage = $entity->getEntityImage();
						   if ($entImage=="")
								$entityImage = $this->entityImage;
							else
								$entityImage = $entImage;
	                        $explode = ",explode=false";
	                    }
	                } else {
						    $entImage = $entity->getEntityImage();
						    if ($entImage=="") 
								$entityImage = $this->entityImage;
							else
								$entityImage = $entImage;
	                        $explode = ",explode=false";
	                }
	                $str .= $this->getId()."Rows[$rw]['cells'][2] = new Array;\n";
	                $str .= $this->getId()."Rows[$rw]['cells'][2]['properties'] = 'width=1%,align=center,valign=middle';\n";
	                $str .= $this->getId()."Rows[$rw]['cells'][2]['class'] = 'cell';\n";
	                $str .= $this->getId()."Rows[$rw]['cells'][2]['value'] = 'false';\n";
	                $str .= $this->getId()."Rows[$rw]['cells'][2]['control'] = 'hidden';\n";
	                $str .= $this->getId()."Rows[$rw]['cells'][2]['control_properties'] = 'fieldList=".str_replace(",","~",$fldList).",buttonImage=".str_replace("=","zozo",$entityImage).",".$isGroup.",actionButton=true$explode';\n";
	                $c=3;
	                $entity->noPresent = false;
	                $ta = $entity->getPersistedArray();
	                foreach ($fieldList as $field) {
	                    $field = str_replace(".","->",$field);
	                    $field_parts = explode(" ",$field);
	                    $title = "";
	                    if ($field_parts[0]!="") {
	                    	if (stripos($field_parts[0],'->')==="FALSE")
	                    		$fp = $field_parts[0];
	                    	else {
	                    		$fp = array_shift(explode("->",$field_parts[0]));
	                    	}	                    			                    		
	                        $strr = explode("|",@$ta[$fp]);
	                        $strr = @$strr[1];
	                        $attrs_array = @$entity->getClientInputControlAttrsArray($strr);
	                        if (@$attrs_array["type"]=="date" and @$entity->fields[@$field_parts[0]]!="") {
	                                eval('$title = html_entity_decode(date("d.m.Y H:i:s",@$entity->'.@$field_parts[0]."/1000),ENT_QUOTES,'UTF-8');");
	                        }
	                        else {
	                            if (stripos($field_parts[0],'->')!==FALSE) {
	                                $arr2 = explode("AS",$field);
	                                $arr2 = explode(" ",$arr2[1]);
	                                $as = $arr2[1];
	                                $arr = explode("->",$field_parts[0]);
	                                $ent = $arr[0];
	                                if (isset($entity->fields[$as])) {
	                                    $title = @$entity->fields[$as];
	                                }
	                            } else if (!is_object($field_parts[0])) {
	                                eval('$title = @html_entity_decode(@$entity->'.@$field_parts[0].",ENT_QUOTES,'UTF-8');");
	                            }                        
	                        }
	                    }
	                    $title = str_replace("'","\'",$title);
	                    $title = str_replace("\r\n","<br/>",$title);
	                    $title = str_replace("\n","<br/>",$title);
	                    
	                    $str .= $this->getId()."Rows[$rw]['cells'][$c] = new Array;\n";
	                    $str .= $this->getId()."Rows[$rw]['cells'][$c]['properties'] = '';\n";
	                    $str .= $this->getId()."Rows[$rw]['cells'][$c]['class'] = 'cell';\n";
	                    $str .= $this->getId()."Rows[$rw]['cells'][$c]['value'] = '".$title."';\n";                    
	                    if ($field_parts[count($field_parts)-1]!="booleans") {
	                        $str .= $this->getId()."Rows[$rw]['cells'][$c]['control'] = 'static';\n";
	                        if (@$attrs_array["type"]=="file")	                                            
	                        	$str .= $this->getId()."Rows[$rw]['cells'][$c]['control_properties'] = 'control_type=file,$isGroup';\n";
	                        else if (@$attrs_array["control_type"]=="email")	                                            
	                        	$str .= $this->getId()."Rows[$rw]['cells'][$c]['control_properties'] = 'control_type=email,$isGroup';\n";
	                        else
	                        	$str .= $this->getId()."Rows[$rw]['cells'][$c]['control_properties'] = '$isGroup';\n";
	                    } else {
	                        $str .= $this->getId()."Rows[$rw]['cells'][$c]['control'] = 'boolean';\n";                    
	                        $str .= $this->getId()."Rows[$rw]['cells'][$c]['control_properties'] = 'control_type=checkbox,$isGroup';\n";                        
	                    }	                    
	                    $c++;
	                }
	                $rw++;
	            }
				if ($this->entityCount=="")
					$this->entityCount=0;
				if ($this->numPages=="")
					$this->numPages = 0;
				if ($this->currentPage>=$this->numPages)
					$this->currentPage = $this->numPages;
	            $str .= $this->getId()."EntityCount = ".$this->entityCount.";";
	            $str .= $this->getId()."NumPages = ".$this->numPages.";";
	            $str .= $this->getId()."tbl.additionalCondition = \"".$this->additionalCondition."\";";
	            $str .= $this->getId()."tbl.condition = \"".$this->condition."\";";
	            $str .= $this->getId()."tbl.advancedCondition = \"".$this->advancedCondition."\";";
	            $str .= $this->getId()."tbl.tagsCondition = \"".$this->tagsCondition."\";";
	            $str .= $this->getId()."tbl.fieldList = \"".$this->fieldList."\";";
	            $str .= $this->getId()."tbl.printFieldList = \"".$this->printFieldList."\";";
	            $str .= $this->getId()."tbl.itemsPerPage = \"".$this->itemsPerPage."\";";
	            $str .= $this->getId()."tbl.sortField = \"".$this->sortField."\";";
	            $str .= $this->getId()."tbl.showQRCode = \"".$this->showQRCode."\";";
	            if (count($profileData)>0) {
	            	$str .= $this->getId()."tbl.profileData = '".json_encode(str_replace("'","xoxoxo",$profileData))."';";
	            	foreach($profileData as $key=>$value)
	            		$str .= $this->getId()."tbl.".$key." = \"".$value."\";";
	            }
	            $this->data = $str;
        	} else { 
        		$this->data = "";
	            $result = parent::getArgs();
        	}
            $this->loaded = true;
        }        
        
        $result["{data}"] = $this->data;
        $result["{className}"] = $this->className;
        if ($this->hierarchy)
        	$result["{showHierarchy}"] = "1";
        else 
        	$result["{showHierarchy}"] = "0";
        if ($this->destroyDiv)
            $result["{destroyDivStr}"] = "true";
        else
            $result["{destroyDivStr}"] = "false";

        if ($this->forEntitySelect)
            $result["{forEntitySelectStr}"] = "true";
        else
            $result["{forEntitySelectStr}"] = "false";
        if (is_object($this->topLinkObject)) {
        	$topLink = $this->topLinkObject;
       		$topLink->setRoleArgs();
       		$this->topLinkRole = str_replace("'","``",$topLink->roleStr);
        } else
        	$this->topLinkRole = "";
        $result["{topLinkRole}"] = $this->topLinkRole;
        return $result;
    }

    function load() {
    	$this->loaded = true;
    }

    function getHookProc($number) {
		switch($number) {
			case '3': return "rebuildHook";
			case '4': return "show";
		}
		return parent::getHookProc($number);
    }

    function rebuildHook($arguments) {
		global $Objects;
		if (isset($arguments["adapterId"]) and $arguments["adapterId"]!="")
			$this->adapter = $Objects->get($arguments["adapterId"]);
		$result = $this->getArgs($arguments);
		echo $result["{data}"];
    }
}
?>