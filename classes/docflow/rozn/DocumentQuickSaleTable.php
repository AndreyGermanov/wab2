<?php
/*
 * Класс, реализующий таблицу в документе "Розничная продажа"
 * 
 */

class DocumentQuickSaleTable extends DataTable {
    
    function construct($params) {
        parent::construct($params);
        global $Objects;
        $this->documentQuickSale = "";
        $this->handler = "scripts/handlers/docflow/crm/DocumentInvoiceTable.js";
        $this->res = "";
        $this->clientClass = "DocumentQuickSaleTable";
        $this->parentClientClasses = "DataTable~Entity";        
    }

    function getArgs() {
        $result = parent::getArgs();
        global $Objects;
        if ($this->documentQuickSale!="")
        	$this->documentQuickSale = $Objects->get($this->documentQuickSale);
        if (is_object($this->documentQuickSale) and method_exists($this->documentQuickSale,"getId")) {
        	if (!$this->documentQuickSale->loaded)
        		$this->documentQuickSale->load();
        } else
        	return 0;        
        $tbl = explode("|",$this->documentQuickSale->documentTable);
        $str = "";
        $id = $this->getId();
//        $str .= "tbl.properties = 'width=100%';\n";
        $str .= $id."tbl.columns = new Array;\n";
        
        $str .= $id."tbl.columns[0] = new Array;\n";
        $str .= $id."tbl.columns[0]['name'] = 'product';\n";
        $str .= $id."tbl.columns[0]['title'] = 'Наименование';\n";
        $str .= $id."tbl.columns[0]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[0]['properties'] = 'width=50%';\n";
        $str .= $id."tbl.columns[0]['control'] = 'text';\n";
        $str .= $id."tbl.columns[0]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[0]['must_set'] = false;\n";
        $str .= $id."tbl.columns[0]['unique'] = false;\n";
        $str .= $id."tbl.columns[0]['readonly'] = false;\n";
        
        $str .= $id."tbl.columns[1] = new Array;\n";
        $str .= $id."tbl.columns[1]['name'] = 'cost';\n";
        $str .= $id."tbl.columns[1]['title'] = 'Цена';\n";
        $str .= $id."tbl.columns[1]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[1]['properties'] = 'width=10%';\n";
        $str .= $id."tbl.columns[1]['control'] = 'decimal';\n";
        $str .= $id."tbl.columns[1]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[1]['must_set'] = false;\n";
        $str .= $id."tbl.columns[1]['unique'] = false;\n";
        $str .= $id."tbl.columns[1]['readonly'] = false;\n";

        $str .= $id."tbl.columns[2] = new Array;\n";
        $str .= $id."tbl.columns[2]['name'] = 'count';\n";
        $str .= $id."tbl.columns[2]['title'] = 'Количество';\n";
        $str .= $id."tbl.columns[2]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[2]['properties'] = 'width=10%';\n";
        $str .= $id."tbl.columns[2]['control'] = 'decimal';\n";
        $str .= $id."tbl.columns[2]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[2]['must_set'] = true;\n";
        $str .= $id."tbl.columns[2]['unique'] = false;\n";
        $str .= $id."tbl.columns[2]['readonly'] = false;\n";

        $str .= $id."tbl.columns[3] = new Array;\n";
        $str .= $id."tbl.columns[3]['name'] = 'summa';\n";
        $str .= $id."tbl.columns[3]['title'] = 'Сумма';\n";
        $str .= $id."tbl.columns[3]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[3]['properties'] = 'width=10%';\n";
        $str .= $id."tbl.columns[3]['control'] = 'decimal';\n";
        $str .= $id."tbl.columns[3]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[3]['must_set'] = true;\n";
        $str .= $id."tbl.columns[3]['unique'] = false;\n";
        $str .= $id."tbl.columns[3]['readonly'] = false;\n";
        
        
        $str .= $id."tbl.rows = new Array;\n";
        $str .= $id."tbl.rows[0] = new Array;\n";
        $str .= $id."tbl.rows[0]['class'] = '';\n";
        $str .= $id."tbl.rows[0]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['class'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['value'] = 'Наименование';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control_properties'] = 'input_class=input1,deactivated=false';\n";
        
        $str .= $id."tbl.rows[0]['cells'][1] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][1]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['value'] = 'Цена';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['control_properties'] = 'input_class=input1,deactivated=false';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['class'] = 'header';\n";
        
        $str .= $id."tbl.rows[0]['cells'][2] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][2]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['value'] = 'Кол.';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['control_properties'] = 'input_class=input1,deactivated=false';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['class'] = 'header';\n";
        
        $str .= $id."tbl.rows[0]['cells'][3] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][3]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][3]['value'] = 'Сумма';\n";
        $str .= $id."tbl.rows[0]['cells'][3]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][3]['control_properties'] = 'input_class=input1,deactivated=false';\n";
        $str .= $id."tbl.rows[0]['cells'][3]['class'] = 'header';\n";
               
        $str .= $id."tbl.emptyrow = new Array;\n";
        $str .= $id."tbl.emptyrow['class'] = '';\n";
        $str .= $id."tbl.emptyrow['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['control'] = 'text';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['value'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][1] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][1]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][1]['control'] = 'decimal';\n";
        $str .= $id."tbl.emptyrow['cells'][1]['value'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][2] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][2]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][2]['control'] = 'decimal';\n";
        $str .= $id."tbl.emptyrow['cells'][2]['value'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][3] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][3]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][3]['control'] = 'decimal';\n";
        $str .= $id."tbl.emptyrow['cells'][3]['value'] = '';\n";
        $c = 1;
        foreach ($tbl as $row) {  
        	if (trim($row)=="")
        		continue; 
        	$cells = explode("~",$row);
	        $str .= $id."tbl.rows[$c] = new Array;\n";
	        $str .= $id."tbl.rows[$c]['cells'] = new Array;\n";
	        $str .= $id."tbl.rows[$c]['cells'][0] = new Array;\n";
	        $str .= $id."tbl.rows[$c]['cells'][0]['value'] = '".$cells[0]."';\n";
	        $str .= $id."tbl.rows[$c]['cells'][0]['control_properties'] = 'deactivated=true,input_class=input1';\n";
	        	        $str .= $id."tbl.rows[$c]['cells'][1] = new Array;\n";
	        $str .= $id."tbl.rows[$c]['cells'][1]['value'] = '".$cells[1]."';\n";
	        $str .= $id."tbl.rows[$c]['cells'][1]['control_properties'] = 'deactivated=true,input_class=input1';\n";
	       	$str .= $id."tbl.rows[$c]['cells'][2] = new Array;\n";
	        $str .= $id."tbl.rows[$c]['cells'][2]['value'] = '".$cells[2]."';\n";
	        $str .= $id."tbl.rows[$c]['cells'][2]['control_properties'] = 'deactivated=true,input_class=input1';\n";	         
	        $str .= $id."tbl.rows[$c]['cells'][3] = new Array;\n";
	        $str .= $id."tbl.rows[$c]['cells'][3]['value'] = '".$cells[3]."';\n";
	        $str .= $id."tbl.rows[$c]['cells'][3]['control_properties'] = 'deactivated=true,input_class=input1';\n";	         
	        $c++;
        }
        $result["{data}"] = $str;
        return $result;
    }     
}
?>