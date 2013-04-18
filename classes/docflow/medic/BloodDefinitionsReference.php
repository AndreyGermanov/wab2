<?php
/**
 * Класс справочника показателей анализа крови
 *
 * @author andrey
 */
class BloodDefinitionsReference extends Reference {
    
    function construct($params) {        
        parent::construct($params);
        global $Objects;
        
        $this->persistedFields["code"] = array("type" => "string",
        										"params" => array("type" => "string",
        												           "title" => "Код"
        										)
        );
        $this->persistedFields["title"] = array("type" => "string",
        		                                 "params" => array("type" => "string",
        		                                 		            "title" => "Описание"
        		                                 )
        );
        $this->persistedFields["dimension"] = array("type" => "entity",
        		                                     "params" => array("type" => "entity",
        		                                     		            "className" => "ReferenceDimensions",
        		                                     		            "additionalFields" => "name",
        		                                     		            "show_float_div" => "true",
        		                                     		            "classTitle" => "Единицы измерения",
        		                                     		            "editorType" => "WABWindow",
        		                                     		            "title" => "Описание",
        		                                     		            "fieldList" => "title Наименование",
        		                                     		            "sortOrder" => "title ASC",
        		                                     		            "width" => "100%",
        		                                     					"adapterId" => "DocFlowDataAdapter_".$this->module_id."_1",
        		                                     					"parentEntity" => "ReferenceDimensions_".$this->module_id."_"        		                                     					
        		                                    )
        );
        $this->persistedFields["mMinV"] = array("type" => "decimal",
        		                                 "params" => array("type" => "decimal",
        		                                 		            "title" => "Минимальный порог для мужчин"        		                                 					
        		                                )
        );
        $this->persistedFields["wmMinV"] = array("type" => "decimal",
        		                                  "params" => array("type" => "decimal",
        		                                  		             "title" => "Минимальный порог для женщин"
        		                                  )
        );
        $this->persistedFields["mMaxV"] = array("type" => "decimal",
        		"params" => array("type" => "decimal",
                    			   "title" => "Максимальный порог для мужчин"
        		)
        );
        $this->persistedFields["wmMaxV"] = array("type" => "decimal",
        		"params" => array("type" => "decimal",
        				           "title" => "Максимальный порог для женщин"
        		)
        );
        $this->persistedFields["mAvgV"] = array("type" => "decimal",
        		"params" => array("type" => "decimal",
        				           "title" => "Среднее значение для мужчин"
        		)
        );
        $this->persistedFields["wmAvgV"] = array("type" => "decimal",
        		"params" => array("type" => "decimal",
        				           "title" => "Среднее значение для женщин"
        		)
        );
        $this->persistedFields["helpTopic"] = array("type" => "string",
        		"params" => array("type" => "string",
        				           "title" => "Номер раздела в справочной системе"
        		)
        );
        
        $this->fieldList = "title Описание~code Код~dimension.title AS dimension Ед.изм.";
        $this->conditionFields = "title~Описание~string,code~Код~string,dimension~ЕдИзм~entity,dimension.title~Наименование ед.изм.~string";
        $this->printFieldList = $this->fieldList;
        $this->allFieldList = $this->fieldList;
        $this->sortOrder = "title ASC";
        $this->sortField = $this->sortOrder;
        $this->renderTemplate = "templates/docflow/medic/BloodDefinitionsReference.html";
        $this->template = "renderForm";
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules;
        $this->skinPath = $app->skinPath;
        $this->icon = $app->skinPath."images/Tree/addrbook.gif";       
        $this->width="650";
        $this->height="400";
        $this->clientClass = "BloodDefinitionsReference";
        $this->parentClientClasses = "Reference~Entity";        
        $this->classTitle = "Показатель анализа крови";
        $this->classListTitle = "Показатели анализа крови";
    }
    
    function checkData() {
        if ($this->code=="") {
            $this->reportError("Укажите код показателя!");
            return false;
        }
        if ($this->dimension=="") {
            $this->reportError("Укажите единицу измерения!");
            return false;
        }
        
        if ($this->name!="")
            $cond = " AND @name!=".$this->name;
        else
            $cond = "";
        $res = PDODataAdapter::makeQuery("SELECT title FROM fields WHERE @code='".$this->code."'".$cond." AND @classname='".get_class($this)."'",$this->adapter,$this->module_id);
        if (count($res)>0) {
            $this->reportError("Позиция с таким кодом уже есть в справочнике!","save");
            return false;
        }                    
        return parent::checkData();
    }
    
    function getHookProc($number) {
    	switch($number) {
    		case '3': return "definitionChanged";
    		case '4': return "showListHook";
    	}
    	return parent::getHookProc($number);
    }
    
    function definitionChanged($arguments) {
    	$object = $this;
    	global $Objects;
    	if (isset($arguments['patient']) && $arguments['patient']!="") {
    		$patient = $Objects->get($arguments["patient"]);
    		$patient->load();
    		$gender=$patient->gender;
    	} else {
    		$gender=1;
    	}
    	$object->load();
    	$object->dimension->load();
    	if ($gender==1) {
    		$min=$object->mMinV;$max=$object->mMaxV;
    	} else {
    		$min=$object->wmMinV;$max=$object->wmMaxV;
    	};
    	echo $object->dimension->title.'~'.$min.'~'.$max.'~'.$object->code;
    }
        
    function showListHook($arguments=null) {
    	$object = $this;
    	$object->overrided='width,height';
    	$object->width=750;$object->height=450;
    	$object->className="*BloodDefinitionsReference*";
    	$object->defaultClassName="BloodDefinitionsReference";
    	$object->loaded=true;
    	$object->template="templates/docflow/core/ReferenceList.html";
    	$object->title="Показатели анализа крови";    	 
    }
}
?>