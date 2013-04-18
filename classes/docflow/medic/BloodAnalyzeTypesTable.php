<?php
/*
 * Класс, реализующий таблицу в справочнике "Типы анализа крови"
 * 
 */
class BloodAnalyzeTypesTable extends DataTable {
    
    function construct($params) {
        parent::construct($params);
        global $Objects;
        $this->definitionsList = "";
        $this->handler = "scripts/handlers/interface/DataTable.js";
        $this->clientClass = "BloodAnalyzeTypesTable";
        $this->parentClientClasses = "DataTable~Entity";        
    }

    function getArgs() {
        $result = parent::getArgs();
        global $Objects;
        $this->adapter = $Objects->get("DocFlowDataAdapter_".$this->module_id."_".$this->name);
        $id = $this->getId();
        $str = "";
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


        $str .= $id."tbl.rows = new Array;\n";
        $str .= $id."tbl.rows[0] = new Array;\n";
        $str .= $id."tbl.rows[0]['class'] = '';\n";
        $str .= $id."tbl.rows[0]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0]['properties'] = '';\n";
       // $str .= "tbl.rows[0]['cells'][0]['class'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['value'] = 'Показатель';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['class'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control_properties'] = 'input_class=input1,deactivated=false';\n";


        $str .= $id."tbl.emptyrow = new Array;\n";
        $str .= $id."tbl.emptyrow['class'] = '';\n";
        $str .= $id."tbl.emptyrow['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['control'] = 'entity';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['control_properties'] = 'show_float_div=true,tableClassName=DocFlowReferenceTable,className=BloodDefinitionsReference,classTitle=Показатели,editorType=WABWindow,deactivated=true,parentEntity=BloodDefinitionsReference_".$this->module_id."_,input_class=input1,width=100%,adapterId=".$this->adapter->getId().",fieldList=title Описание~code Код~dimension.title AS edizm Ед.изм,sortOrder=title';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['value'] = '';\n";

        if ($this->definitionsList != "") {
            $bloodDefinitionsArray = explode("~",$this->definitionsList);
            $c=1;
            foreach ($bloodDefinitionsArray as $per_item) {   
                if (trim($per_item)=="")
                    continue;
                $res = $Objects->query("*BloodDefinitionsReference*_".$this->module_id,"simple|name~title|"."@name=".$per_item,$this->adapter,"title","");
                if (count($res)>0)
                    $obj = $res[0];
                if (isset($obj) and is_object($obj) and !$obj->loaded)
                    $obj->load();
                if (isset($obj) and $obj->loaded) {
                    $str .= $id."tbl.rows[$c] = new Array;\n";
                    $str .= $id."tbl.rows[$c]['class'] = '';\n";
                    $str .= $id."tbl.rows[$c]['properties'] = '';\n";
                    $str .= $id."tbl.rows[$c]['cells'] = new Array;\n";
                    $str .= $id."tbl.rows[$c]['cells'][0] = new Array;\n";
                    $str .= $id."tbl.rows[$c]['cells'][0]['properties'] = '';\n";
                    $str .= $id."tbl.rows[$c]['cells'][0]['control'] = 'entity';\n";
                    $str .= $id."tbl.rows[$c]['cells'][0]['control_properties'] = 'show_float_div=true,tableClassName=DocFlowReferenceTable,valueTitle=".str_replace(",","xyzxyz",$obj->title).",className=BloodDefinitionsReference,classTitle=Показатели,parentEntity=BloodDefinitionsReference_".$this->module_id."_,editorType=WABWindow,deactivated=true,input_class=input1,width=100%,adapterId=".$this->adapter->getId().",fieldList=title Описание~code Код~dimension.title AS edizm Ед.изм,sortOrder=title';\n";
                    $str .= $id."tbl.rows[$c]['cells'][0]['class'] = 'cell';\n";
                    $str .= $id."tbl.rows[$c]['cells'][0]['value'] = '".$obj->getId()."';\n";
                    $c++;
                }
            }
        }
        $result["{data}"] = $str;
        return $result;
    }    
}
?>