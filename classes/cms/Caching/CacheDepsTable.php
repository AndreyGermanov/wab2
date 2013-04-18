<?php
class CacheDepsTable extends DataTable {
    
    function construct($params) {
        parent::construct($params);
        global $Objects;
        $this->cacheDepsList = "";
        $this->handler = "scripts/handlers/cms/Caching/CacheDepsTable.js";
        $this->clientClass = "CacheDepsTable";
        $this->parentClientClasses = "DataTable~Entity";        
    }

    function getArgs() {
        $result = parent::getArgs();
        global $Objects;
        $this->adapter = $Objects->get("SiteDataAdapter_".$this->module_id."_".$this->siteId."_Adapter");
        $id = $this->getId();
        $str = "";
        $str .= $id."tbl.columns = new Array;\n";
        
        $str .= $id."tbl.columns[0] = new Array;\n";
        $str .= $id."tbl.columns[0]['name'] = 'fieldName';\n";
        $str .= $id."tbl.columns[0]['title'] = 'Имя раздела';\n";
        $str .= $id."tbl.columns[0]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[0]['properties'] = 'width=100%';\n";
        $str .= $id."tbl.columns[0]['control'] = 'string';\n";
        $str .= $id."tbl.columns[0]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[0]['must_set'] = true;\n";
        $str .= $id."tbl.columns[0]['unique'] = true;\n";
        $str .= $id."tbl.columns[0]['readonly'] = false;\n";

        $str .= $id."tbl.rows = new Array;\n";
        $str .= $id."tbl.rows[0] = new Array;\n";
        $str .= $id."tbl.rows[0]['class'] = 'header';\n";
        $str .= $id."tbl.rows[0]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0]['properties'] = 'class=header';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['value'] = 'Имя раздела';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control_properties'] = 'input_class=input1,deactivated=false';\n";

        $str .= $id."tbl.emptyrow = new Array;\n";
        $str .= $id."tbl.emptyrow['class'] = '';\n";
        $str .= $id."tbl.emptyrow['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['control'] = 'entity';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['control_properties'] = 'show_float_div=true,className=*WebEntity*,classTitle=Разделы,editorType=WABWindow,deactivated=true,input_class=input1,additionalFields=siteId,width=100%,adapterId=".$this->adapter->getId()."';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['value'] = '';\n";

        if ($this->cacheDepsList != "") {
            $cacheDepsArray = explode("~",$this->cacheDepsList);
            $c=1;
            foreach ($cacheDepsArray as $per_item) {                  
                $res = $Objects->query("*WebEntity*_".$this->module_id."_".$this->siteId,"simple|name~title~siteId~persistedFields|"."@name=".$per_item,$this->adapter,"title","");
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
                    $str .= $id."tbl.rows[$c]['cells'][0]['control_properties'] = 'show_float_div=true,valueTitle=".str_replace(",","~",$obj->title).",className=*WebEntity*,classTitle=Разделы,additionalFields=siteId,editorType=WABWindow,deactivated=true,input_class=input1,width=100%,adapterId=".$this->adapter->getId()."';\n";
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