<?php
/*
 * Класс, реализующий таблицу в документе "Анализ крови"
 * 
 */

class DocumentBloodAnalyzeTable extends DataTable {
    
    function construct($params) {
        parent::construct($params);
        global $Objects;
        $this->definitionsList = "";
        $this->handler = "scripts/handlers/docflow/medic/DocumentBloodAnalyzeTable.js";
        $this->res = "";
        $this->clientClass = "DocumentBloodAnalyzeTable";
        $this->parentClientClasses = "DataTable~Entity";        
    }

    function getArgs() {
        $result = parent::getArgs();
        global $Objects;
        $this->adapter = $Objects->get("DocFlowDataAdapter_".$this->module_id."_".$this->name);
       	$readonly = "";
        $str = "";
        
//        $str .= "tbl.properties = 'width=100%';\n";
		$id = $this->getId();
        $str .= $id."tbl.columns = new Array;\n";
        
        $str .= $id."tbl.columns[0] = new Array;\n";
        $str .= $id."tbl.columns[0]['name'] = 'definition';\n";
        $str .= $id."tbl.columns[0]['title'] = 'Показатель';\n";
        $str .= $id."tbl.columns[0]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[0]['properties'] = 'width=100%';\n";
        $str .= $id."tbl.columns[0]['control'] = 'string';\n";
        $str .= $id."tbl.columns[0]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[0]['must_set'] = true;\n";
        $str .= $id."tbl.columns[0]['unique'] = true;\n";
        $str .= $id."tbl.columns[0]['readonly'] = false;\n";

        $str .= $id."tbl.columns[1] = new Array;\n";
        $str .= $id."tbl.columns[1]['name'] = 'value';\n";
        $str .= $id."tbl.columns[1]['title'] = 'Значение';\n";
        $str .= $id."tbl.columns[1]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[1]['properties'] = 'width=100%';\n";
        $str .= $id."tbl.columns[1]['control'] = 'string';\n";
        $str .= $id."tbl.columns[1]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[1]['must_set'] = true;\n";
        $str .= $id."tbl.columns[1]['unique'] = false;\n";
        $str .= $id."tbl.columns[1]['readonly'] = false;\n";

        $str .= $id."tbl.columns[2] = new Array;\n";
        $str .= $id."tbl.columns[2]['name'] = 'dimension';\n";
        $str .= $id."tbl.columns[2]['title'] = 'Ед.';\n";
        $str .= $id."tbl.columns[2]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[2]['properties'] = 'width=100%';\n";
        $str .= $id."tbl.columns[2]['control'] = 'plaintext';\n";
        $str .= $id."tbl.columns[2]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[2]['must_set'] = false;\n";
        $str .= $id."tbl.columns[2]['unique'] = false;\n";
        $str .= $id."tbl.columns[2]['readonly'] = false;\n";
        
        $str .= $id."tbl.columns[3] = new Array;\n";
        $str .= $id."tbl.columns[3]['name'] = 'minValue';\n";
        $str .= $id."tbl.columns[3]['title'] = 'Мин.';\n";
        $str .= $id."tbl.columns[3]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[3]['properties'] = 'width=100%';\n";
        $str .= $id."tbl.columns[3]['control'] = 'plaintext';\n";
        $str .= $id."tbl.columns[3]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[3]['must_set'] = false;\n";
        $str .= $id."tbl.columns[3]['unique'] = false;\n";
        $str .= $id."tbl.columns[3]['readonly'] = false;\n";

        $str .= $id."tbl.columns[4] = new Array;\n";
        $str .= $id."tbl.columns[4]['name'] = 'maxValue';\n";
        $str .= $id."tbl.columns[4]['title'] = 'Макс.';\n";
        $str .= $id."tbl.columns[4]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[4]['properties'] = 'width=100%';\n";
        $str .= $id."tbl.columns[4]['control'] = 'plaintext';\n";
        $str .= $id."tbl.columns[4]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[4]['must_set'] = false;\n";
        $str .= $id."tbl.columns[4]['unique'] = false;\n";
        $str .= $id."tbl.columns[4]['readonly'] = false;\n";

        $str .= $id."tbl.columns[5] = new Array;\n";
        $str .= $id."tbl.columns[5]['name'] = 'code';\n";
        $str .= $id."tbl.columns[5]['title'] = 'Код';\n";
        $str .= $id."tbl.columns[5]['class'] = 'hidden';\n";
        $str .= $id."tbl.columns[5]['properties'] = 'width=100%';\n";
        $str .= $id."tbl.columns[5]['control'] = 'hidden';\n";
        $str .= $id."tbl.columns[5]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[5]['must_set'] = false;\n";
        $str .= $id."tbl.columns[5]['unique'] = false;\n";
        $str .= $id."tbl.columns[5]['readonly'] = false;\n";        
        
        $str .= $id."tbl.rows = new Array;\n";
        $str .= $id."tbl.rows[0] = new Array;\n";
        $str .= $id."tbl.rows[0]['class'] = '';\n";
        $str .= $id."tbl.rows[0]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['class'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['value'] = 'Показатель';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control_properties'] = 'input_class=input1,deactivated=false';\n";

        $str .= $id."tbl.rows[0]['cells'][1] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][1]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['value'] = 'Значение';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['control_properties'] = 'input_class=input1,deactivated=false';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['class'] = 'header';\n";
        
        $str .= $id."tbl.rows[0]['cells'][2] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][2]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['value'] = 'Ед.';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['control_properties'] = 'input_class=input1,deactivated=false';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['class'] = 'header';\n";
        
        $str .= $id."tbl.rows[0]['cells'][3] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][3]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][3]['value'] = 'Мин.';\n";
        $str .= $id."tbl.rows[0]['cells'][3]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][3]['control_properties'] = 'input_class=input1,deactivated=false';\n";
        $str .= $id."tbl.rows[0]['cells'][3]['class'] = 'header';\n";
        
        $str .= $id."tbl.rows[0]['cells'][4] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][4]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][4]['value'] = 'Макс.';\n";
        $str .= $id."tbl.rows[0]['cells'][4]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][4]['control_properties'] = 'input_class=input1,deactivated=false';\n";
        $str .= $id."tbl.rows[0]['cells'][4]['class'] = 'header';\n";
        
        $str .= $id."tbl.rows[0]['cells'][5] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][5]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][5]['value'] = 'Код';\n";
        $str .= $id."tbl.rows[0]['cells'][5]['control'] = 'hidden';\n";
        $str .= $id."tbl.rows[0]['cells'][5]['control_properties'] = 'input_class=input1,deactivated=false';\n";
        
        $str .= $id."tbl.emptyrow = new Array;\n";
        $str .= $id."tbl.emptyrow['class'] = '';\n";
        $str .= $id."tbl.emptyrow['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['control'] = 'entity';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['control_properties'] = 'show_float_div=true,className=BloodDefinitionsReference,tableClassName=DocFlowReferenceTable,classTitle=Показатели,editorType=WABWindow,deactivated=true,parentEntity=BloodDefinitionsReference_".$this->module_id."_,input_class=input1,width=100%,adapterId=".$this->adapter->getId().",fieldList=title Описание~code Код~dimension.title AS edizm Ед.изм,sortOrder=title,hierarchy=false';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['value'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][1] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][1]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][1]['control'] = 'decimal';\n";
        $str .= $id."tbl.emptyrow['cells'][1]['control_properties'] = 'empty=true';\n";
        $str .= $id."tbl.emptyrow['cells'][1]['value'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][2] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][2]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][2]['control'] = 'plaintext';\n";
        $str .= $id."tbl.emptyrow['cells'][2]['value'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][3] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][3]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][3]['control'] = 'plaintext';\n";
        $str .= $id."tbl.emptyrow['cells'][3]['value'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][4] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][4]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][4]['control'] = 'plaintext';\n";
        $str .= $id."tbl.emptyrow['cells'][4]['value'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][5] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][5]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][5]['control'] = 'hidden';\n";
        $str .= $id."tbl.emptyrow['cells'][5]['value'] = '';\n";

        if ($this->documentTable != "") {
            $bloodDefinitionsArray = explode("|",$this->documentTable);
            $c=1;
            $arr = array();
            $arr2 = array();
            foreach ($bloodDefinitionsArray as $per_item) {   
                $per_item_parts = explode("~",$per_item);
                if (trim($per_item)=="")
                    continue;
                if (!isset($per_item_parts[1])) {
                    $arr2[$per_item_parts[0]] = "";
                } else
                    $arr2[$per_item_parts[0]] = $per_item_parts[1];
                $arr[] = "'".$per_item_parts[0]."'";
            }            
            if ($this->res=="") {
                $this->res = $Objects->query("*BloodDefinitionsReference*_".$this->module_id,"simple|name~code~title~dimension.title AS dimension~mMinV~mMaxV~wmMinV~wmMaxV|"."@name IN (".implode(",",$arr).")",$this->adapter,"","");
            }
            $res = $this->res;
            foreach ($res as $obj) {
                //$obj = $Objects->get("WebEntity_".$this->module_id."_".$this->siteId."_".$per_item);
//                if (isset($obj) and is_object($obj) and !$obj->loaded)
//                    $obj->load();
                if (isset($obj)) {
                    $str .= $id."tbl.rows[$c] = new Array;\n";
                    $str .= $id."tbl.rows[$c]['class'] = '';\n";
                    $str .= $id."tbl.rows[$c]['properties'] = '';\n";
                    $str .= $id."tbl.rows[$c]['cells'] = new Array;\n";
                    $str .= $id."tbl.rows[$c]['cells'][0] = new Array;\n";
                    $str .= $id."tbl.rows[$c]['cells'][0]['properties'] = '';\n";
                    $str .= $id."tbl.rows[$c]['cells'][0]['control'] = 'entity';\n";
                    $str .= $id."tbl.rows[$c]['cells'][0]['control_properties'] = 'show_float_div=true,valueTitle=".str_replace(",","xyzxyz",$obj->title).",className=BloodDefinitionsReference,tableClassName=DocFlowReferenceTable,classTitle=Показатели,parentEntity=BloodDefinitionsReference_".$this->module_id."_,editorType=WABWindow,deactivated=true,input_class=input1,width=100%,adapterId=".$this->adapter->getId().",fieldList=title Описание~code Код~dimension.title AS edizm Ед.изм,sortOrder=title,hierarchy=false';\n";
                    $str .= $id."tbl.rows[$c]['cells'][0]['class'] = 'cell';\n";
                    $str .= $id."tbl.rows[$c]['cells'][0]['value'] = '".$obj->getId()."';\n";

                    $str .= $id."tbl.rows[$c]['cells'][1] = new Array;\n";
                    $str .= $id."tbl.rows[$c]['cells'][1]['properties'] = '';\n";
                    $str .= $id."tbl.rows[$c]['cells'][1]['control'] = 'decimal';\n";
                    $str .= $id."tbl.rows[$c]['cells'][1]['control_properties'] = 'empty=true';\n";
                    $str .= $id."tbl.rows[$c]['cells'][1]['class'] = 'cell';\n";
                    $str .= $id."tbl.rows[$c]['cells'][1]['value'] = '".@$arr2[$obj->name]."';\n";
                    
//                    $cls = array_shift(explode("_",$obj->edizm));
//                    $id = array_pop(explode("_",$obj->edizm));
//                    $obj->dimension = $Objects->get($cls."_".$this->module_id."_".$id);
//                    $obj->dimension->load();
//                    if (!$obj->edizm->loaded)
//                        $obj->edizm->load();
                    $str .= $id."tbl.rows[$c]['cells'][2] = new Array;\n";
                    $str .= $id."tbl.rows[$c]['cells'][2]['properties'] = '';\n";
                    $str .= $id."tbl.rows[$c]['cells'][2]['control'] = 'plaintext';\n";
                    $str .= $id."tbl.rows[$c]['cells'][2]['class'] = 'cell';\n";
                    $str .= $id."tbl.rows[$c]['cells'][2]['value'] = '".$obj->dimension."';\n";
                    
                    $str .= $id."tbl.rows[$c]['cells'][3] = new Array;\n";
                    $str .= $id."tbl.rows[$c]['cells'][3]['properties'] = '';\n";
                    $str .= $id."tbl.rows[$c]['cells'][3]['control'] = 'plaintext';\n";
                    $str .= $id."tbl.rows[$c]['cells'][3]['class'] = 'cell';\n";
                    if ($this->gender==1) 
                        $str .= $id."tbl.rows[$c]['cells'][3]['value'] = '".$obj->mMinV."';\n";
                    else
                        $str .= $id."tbl.rows[$c]['cells'][3]['value'] = '".$obj->wmMinV."';\n";

                    $str .= $id."tbl.rows[$c]['cells'][4] = new Array;\n";
                    $str .= $id."tbl.rows[$c]['cells'][4]['properties'] = '';\n";
                    $str .= $id."tbl.rows[$c]['cells'][4]['control'] = 'plaintext';\n";
                    $str .= $id."tbl.rows[$c]['cells'][4]['class'] = 'cell';\n";
                    if ($this->gender==1) 
                        $str .= $id."tbl.rows[$c]['cells'][4]['value'] = '".$obj->mMaxV."';\n";
                    else
                        $str .= $id."tbl.rows[$c]['cells'][4]['value'] = '".$obj->wmMaxV."';\n";
                    
                    $str .= $id."tbl.rows[$c]['cells'][5] = new Array;\n";
                    $str .= $id."tbl.rows[$c]['cells'][5]['properties'] = '';\n";
                    $str .= $id."tbl.rows[$c]['cells'][5]['control'] = 'hidden';\n";
                    $str .= $id."tbl.rows[$c]['cells'][5]['class'] = 'hidden';\n";
                    $str .= $id."tbl.rows[$c]['cells'][5]['value'] = '".$obj->code."';\n";
                    $c++;
                }
            }
        }
        $result["{data}"] = $str;
        return $result;
    } 
    
    function getHookProc($number) {
		switch($number) {
			case '3': return "analyzeTypeChanged";
			case '4': return "patientChanged";
			case '5': return "definitionChanged";
		}
		return parent::getHookProc($number);
	}
    
   	function analyzeTypeChanged($arguments) {
		$object = $this;
		global $Objects;
		$patient = $arguments["patient"];
		if ($patient!="") {
			$patient = $Objects->get($patient);
			$patient->load();
			$object->gender=$patient->gender;
		} else {
			$object->gender=1;
		}                
		echo $arguments["def"];
		$def=$Objects->get($arguments["def"]);
		$def->load();
		$object->documentTable=str_replace('~','|',$def->defs);
		$res = $object->getArgs();
		echo $def->helpTopic.'|'.$res['{data}'];
	}
	
	function patientChanged($arguments) {
		$object = $this;
		global $Objects;
		if (isset($arguments['patient']) && $arguments['patient']!="") {
			$patient = $Objects->get($arguments["patient"]);
			$patient->load();
			$object->gender=$patient->gender;
		} else {
			$object->gender=1;
		}
		$object->documentTable=$arguments["documentTable"];
		$res = $object->getArgs();
		echo $res['{data}'];
	}
}
?>