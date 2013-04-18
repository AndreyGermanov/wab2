<?php
/*
 * Класс, реализующий таблицу коэффициентов пересчета в форме элемента справочника "Номенклатура"
 * 
 */

class ProductDimensionsTable extends DataTable {
    
    function construct($params) {
        parent::construct($params);
        global $Objects;
        $this->product = "";
        $this->handler = "scripts/handlers/docflow/common/ProductDimensionsTable.js";
        $this->res = "";
        $this->clientClass = "ProductDimensionsTable";
        $this->parentClientClasses = "DataTable~Entity";        
    }

    function getArgs() {
        $result = parent::getArgs();
        global $Objects;
        if ($this->product!="")
        	$this->product = $Objects->get($this->product);
        if (is_object($this->product) and method_exists($this->product,"getId")) {
        	if (!$this->product->loaded)
        		$this->product->load();
        } else
        	return 0;        
        
        $tbl = $this->product->table;
        $str = "";
        $id = $this->getId();
        $str .= "tbl.properties = 'width=100%';\n";
        $str .= $id."tbl.columns = new Array;\n";
        
        $str .= $id."tbl.columns[0] = new Array;\n";
        $str .= $id."tbl.columns[0]['name'] = 'product';\n";
        $str .= $id."tbl.columns[0]['title'] = 'Ед. изм';\n";
        $str .= $id."tbl.columns[0]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[0]['properties'] = 'width=50%';\n";
        $str .= $id."tbl.columns[0]['control'] = 'entity';\n";
        $str .= $id."tbl.columns[0]['control_properties'] = 'show_float_div=true,className=ReferenceDimensions,tableClassName=DocFlowReferenceTable,classTitle=Единицы измерения,editorType=WABWindow,deactivated=true,parentEntity=ReferenceDimensions_".$this->module_id."_,input_class=input1,width=100%,adapterId=".$this->adapter->getId().",fieldList=title Название,sortOrder=title,hierarchy=false';\n";
        $str .= $id."tbl.columns[0]['must_set'] = false;\n";
        $str .= $id."tbl.columns[0]['unique'] = false;\n";
        $str .= $id."tbl.columns[0]['readonly'] = false;\n";
        
        $str .= $id."tbl.columns[1] = new Array;\n";
        $str .= $id."tbl.columns[1]['name'] = 'koeff';\n";
        $str .= $id."tbl.columns[1]['title'] = 'Коэффициент';\n";
        $str .= $id."tbl.columns[1]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[1]['properties'] = 'width=10%';\n";
        $str .= $id."tbl.columns[1]['control'] = 'decimal';\n";
        $str .= $id."tbl.columns[1]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[1]['must_set'] = false;\n";
        $str .= $id."tbl.columns[1]['unique'] = false;\n";
        $str .= $id."tbl.columns[1]['readonly'] = false;\n";
        
        
        $str .= $id."tbl.rows = new Array;\n";
        $str .= $id."tbl.rows[0] = new Array;\n";
        $str .= $id."tbl.rows[0]['class'] = '';\n";
        $str .= $id."tbl.rows[0]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['class'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['value'] = 'Ед. изм.';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control_properties'] = 'input_class=input1,deactivated=false';\n";
        
        $str .= $id."tbl.rows[0]['cells'][1] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][1]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['value'] = 'Коэффициент';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['control_properties'] = 'input_class=input1,deactivated=false';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['class'] = 'header';\n";
                       
        $str .= $id."tbl.emptyrow = new Array;\n";
        $str .= $id."tbl.emptyrow['class'] = '';\n";
        $str .= $id."tbl.emptyrow['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0]['value'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][1] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][1]['value'] = '';\n";
        $c = 1;
        foreach ($tbl as $row) {  
        	if (trim($row)=="")
        		continue; 
        	$cells = $row;
	        $str .= $id."tbl.rows[$c] = new Array;\n";
	        $str .= $id."tbl.rows[$c]['cells'] = new Array;\n";
	        $str .= $id."tbl.rows[$c]['cells'][0] = new Array;\n";
	        $str .= $id."tbl.rows[$c]['cells'][0]['value'] = '".$row["dimension"]->getId()."';\n";
   	        $str .= $id."tbl.rows[$c]['cells'][1] = new Array;\n";
	        $str .= $id."tbl.rows[$c]['cells'][1]['value'] = '".$row["koeff"]."';\n";
	        $c++;
        }
        $result["{data}"] = $str;
        return $result;
    }     
}
?>