<?php
/*
 * Класс, реализующий таблицу в документе "Счет на оплату"
 * 
 */

class DocumentInvoiceTable extends DataTable {
    
    function construct($params) {
        parent::construct($params);
        global $Objects;
        $this->documentInvoice = "";
        $this->handler = "scripts/handlers/docflow/crm/DocumentInvoiceTable.js";
        $this->res = "";
        $this->clientClass = "DocumentInvoiceTable";
        $this->parentClientClasses = "DataTable~Entity";        
    }

    function getArgs() {
        $result = parent::getArgs();
        global $Objects;
        if ($this->documentInvoice!="")
        	$this->documentInvoice = $Objects->get($this->documentInvoice);
        if (is_object($this->documentInvoice) and method_exists($this->documentInvoice,"getId")) {
        	if (!$this->documentInvoice->loaded)
        		$this->documentInvoice->load();
        } else
        	return 0;        
        $tbl = explode("|",$this->documentInvoice->documentTable);
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
        $str .= $id."tbl.columns[0]['must_set'] = true;\n";
        $str .= $id."tbl.columns[0]['unique'] = false;\n";
        $str .= $id."tbl.columns[0]['readonly'] = false;\n";

        $str .= $id."tbl.columns[1] = new Array;\n";
        $str .= $id."tbl.columns[1]['name'] = 'code';\n";
        $str .= $id."tbl.columns[1]['title'] = 'Код';\n";
        $str .= $id."tbl.columns[1]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[1]['properties'] = 'width=10%';\n";
        $str .= $id."tbl.columns[1]['control'] = 'string';\n";
        $str .= $id."tbl.columns[1]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[1]['must_set'] = false;\n";
        $str .= $id."tbl.columns[1]['unique'] = false;\n";
        $str .= $id."tbl.columns[1]['readonly'] = false;\n";
        
        $str .= $id."tbl.columns[2] = new Array;\n";
        $str .= $id."tbl.columns[2]['name'] = 'ed';\n";
        $str .= $id."tbl.columns[2]['title'] = 'Ед.';\n";
        $str .= $id."tbl.columns[2]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[2]['properties'] = 'width=10%';\n";
        $str .= $id."tbl.columns[2]['control'] = 'string';\n";
        $str .= $id."tbl.columns[2]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[2]['must_set'] = true;\n";
        $str .= $id."tbl.columns[2]['unique'] = false;\n";
        $str .= $id."tbl.columns[2]['readonly'] = false;\n";
        
        $str .= $id."tbl.columns[3] = new Array;\n";
        $str .= $id."tbl.columns[3]['name'] = 'cost';\n";
        $str .= $id."tbl.columns[3]['title'] = 'Цена';\n";
        $str .= $id."tbl.columns[3]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[3]['properties'] = 'width=10%';\n";
        $str .= $id."tbl.columns[3]['control'] = 'decimal';\n";
        $str .= $id."tbl.columns[3]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[3]['must_set'] = true;\n";
        $str .= $id."tbl.columns[3]['unique'] = false;\n";
        $str .= $id."tbl.columns[3]['readonly'] = false;\n";

        $str .= $id."tbl.columns[4] = new Array;\n";
        $str .= $id."tbl.columns[4]['name'] = 'count';\n";
        $str .= $id."tbl.columns[4]['title'] = 'Количество';\n";
        $str .= $id."tbl.columns[4]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[4]['properties'] = 'width=10%';\n";
        $str .= $id."tbl.columns[4]['control'] = 'decimal';\n";
        $str .= $id."tbl.columns[4]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[4]['must_set'] = true;\n";
        $str .= $id."tbl.columns[4]['unique'] = false;\n";
        $str .= $id."tbl.columns[4]['readonly'] = false;\n";

        $str .= $id."tbl.columns[5] = new Array;\n";
        $str .= $id."tbl.columns[5]['name'] = 'summa';\n";
        $str .= $id."tbl.columns[5]['title'] = 'Сумма';\n";
        $str .= $id."tbl.columns[5]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[5]['properties'] = 'width=10%';\n";
        $str .= $id."tbl.columns[5]['control'] = 'decimal';\n";
        $str .= $id."tbl.columns[5]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[5]['must_set'] = true;\n";
        $str .= $id."tbl.columns[5]['unique'] = false;\n";
        $str .= $id."tbl.columns[5]['readonly'] = false;\n";
        
        $str .= $id."tbl.columns[6] = new Array;\n";
        $str .= $id."tbl.columns[6]['name'] = 'stNDS';\n";
        $str .= $id."tbl.columns[6]['title'] = '% НДС';\n";
        $str .= $id."tbl.columns[6]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[6]['properties'] = '';\n";
        $str .= $id."tbl.columns[6]['control'] = 'integer';\n";
        $str .= $id."tbl.columns[6]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[6]['must_set'] = true;\n";
        $str .= $id."tbl.columns[6]['unique'] = false;\n";
        $str .= $id."tbl.columns[6]['readonly'] = false;\n";
       

        $str .= $id."tbl.columns[7] = new Array;\n";
        $str .= $id."tbl.columns[7]['name'] = 'summaNDS';\n";
        $str .= $id."tbl.columns[7]['title'] = 'Сумма НДС';\n";
        $str .= $id."tbl.columns[7]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[7]['properties'] = 'width=10%';\n";
        $str .= $id."tbl.columns[7]['control'] = 'decimal';\n";
        $str .= $id."tbl.columns[7]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[7]['must_set'] = true;\n";
        $str .= $id."tbl.columns[7]['unique'] = false;\n";
        $str .= $id."tbl.columns[7]['readonly'] = false;\n";

        $str .= $id."tbl.columns[8] = new Array;\n";
        $str .= $id."tbl.columns[8]['name'] = 'total';\n";
        $str .= $id."tbl.columns[8]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[8]['title'] = 'Всего';\n";
        $str .= $id."tbl.columns[8]['control'] = 'plaintext';\n";
        $str .= $id."tbl.columns[8]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[8]['must_set'] = true;\n";
        $str .= $id."tbl.columns[8]['unique'] = false;\n";
        $str .= $id."tbl.columns[8]['readonly'] = false;\n";        
        
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
        $str .= $id."tbl.rows[0]['cells'][1]['value'] = 'Код';\n";
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
        $str .= $id."tbl.rows[0]['cells'][3]['value'] = 'Цена';\n";
        $str .= $id."tbl.rows[0]['cells'][3]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][3]['control_properties'] = 'input_class=input1,deactivated=false';\n";
        $str .= $id."tbl.rows[0]['cells'][3]['class'] = 'header';\n";
        
        $str .= $id."tbl.rows[0]['cells'][4] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][4]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][4]['value'] = 'Кол.';\n";
        $str .= $id."tbl.rows[0]['cells'][4]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][4]['control_properties'] = 'input_class=input1,deactivated=false';\n";
        $str .= $id."tbl.rows[0]['cells'][4]['class'] = 'header';\n";
        
        $str .= $id."tbl.rows[0]['cells'][5] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][5]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][5]['value'] = 'Сумма';\n";
        $str .= $id."tbl.rows[0]['cells'][5]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][5]['control_properties'] = 'input_class=input1,deactivated=false';\n";
        $str .= $id."tbl.rows[0]['cells'][5]['class'] = 'header';\n";
       
        $str .= $id."tbl.rows[0]['cells'][6] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][6]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][6]['value'] = '% НДС';\n";
        $str .= $id."tbl.rows[0]['cells'][6]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][6]['control_properties'] = 'input_class=input1,deactivated=false';\n";
        $str .= $id."tbl.rows[0]['cells'][6]['class'] = 'header';\n";
        
        $str .= $id."tbl.rows[0]['cells'][7] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][7]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][7]['value'] = 'Сумма НДС';\n";
        $str .= $id."tbl.rows[0]['cells'][7]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][7]['control_properties'] = 'input_class=input1,deactivated=false';\n";
        $str .= $id."tbl.rows[0]['cells'][7]['class'] = 'header';\n";
       
        $str .= $id."tbl.rows[0]['cells'][8] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][8]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][8]['value'] = 'Всего';\n";
        $str .= $id."tbl.rows[0]['cells'][8]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][8]['control_properties'] = 'input_class=input1,deactivated=false';\n";
        $str .= $id."tbl.rows[0]['cells'][8]['class'] = 'header';\n";
        

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
        $str .= $id."tbl.emptyrow['cells'][1]['control'] = 'string';\n";
        $str .= $id."tbl.emptyrow['cells'][1]['value'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][2] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][2]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][2]['control'] = 'string';\n";
        $str .= $id."tbl.emptyrow['cells'][2]['value'] = 'шт.';\n";
        $str .= $id."tbl.emptyrow['cells'][3] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][3]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][3]['control'] = 'decimal';\n";
        $str .= $id."tbl.emptyrow['cells'][3]['value'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][4] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][4]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][4]['control'] = 'decimal';\n";
        $str .= $id."tbl.emptyrow['cells'][4]['value'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][5] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][5]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][5]['control'] = 'decimal';\n";
        $str .= $id."tbl.emptyrow['cells'][5]['value'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][6] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][6]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][6]['control'] = 'integer';\n";
        $str .= $id."tbl.emptyrow['cells'][6]['value'] = '18';\n";
        $str .= $id."tbl.emptyrow['cells'][7] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][7]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][7]['control'] = 'decimal';\n";
        $str .= $id."tbl.emptyrow['cells'][7]['value'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][8] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][8]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][8]['control'] = 'plaintext';\n";
        $str .= $id."tbl.emptyrow['cells'][8]['value'] = '';\n";
        $c = 1;
        foreach ($tbl as $row) {  
        	if (trim($row)=="")
        		continue; 
        	$cells = explode("~",$row);
	        $str .= $id."tbl.rows[$c] = new Array;\n";
	        $str .= $id."tbl.rows[$c]['cells'] = new Array;\n";
	        $str .= $id."tbl.rows[$c]['cells'][0] = new Array;\n";
	        $str .= $id."tbl.rows[$c]['cells'][0]['value'] = '".str_replace("\r\n","xoxoxo",$cells[0])."';".$id."tbl.rows[$c]['cells'][0]['value'] = ".$id."tbl.rows[$c]['cells'][0]['value'].replace(/xoxoxo/g,'\\r\\n');\n";
	        $str .= $id."tbl.rows[$c]['cells'][0]['control_properties'] = 'deactivated=true,input_class=input1,selectClass=SelectEntityFloatMenu,selectOptions=entityClasszozoReferenceProductsxyzxyzsearchFieldzozotitlexyzxyzdisplayFieldzozotitlexyzxyzresultFieldszozotitle-code-cost-dimension.title-NDS|product-code-cost-ed-stNDSxyzxyzresultObjectzozo".$this->getId()."_".$c."~showAdvancedzozotrue,showOnKeyPress=true,hideSelectButton=true';\n";
	        $str .= $id."tbl.rows[$c]['cells'][1] = new Array;\n";
	        $str .= $id."tbl.rows[$c]['cells'][1]['value'] = '".str_replace("\r\n","xoxoxo",@$cells[8])."';".$id."tbl.rows[$c]['cells'][1]['value'] = ".$id."tbl.rows[$c]['cells'][1]['value'].replace(/xoxoxo/g,'\\r\\n');\n";
	        $str .= $id."tbl.rows[$c]['cells'][1]['control_properties'] = 'deactivated=true,input_class=input1,selectClass=SelectEntityFloatMenu,selectOptions=entityClasszozoReferenceProductsxyzxyzsearchFieldzozocodexyzxyzdisplayFieldzozocodexyzxyzresultFieldszozotitle-code-cost-dimension.title-NDS|product-code-cost-ed-stNDSxyzxyzresultObjectzozo".$this->getId()."_".$c."~showAdvancedzozotrue,showOnKeyPress=true,hideSelectButton=true';\n";
	        $str .= $id."tbl.rows[$c]['cells'][2] = new Array;\n";
	        $str .= $id."tbl.rows[$c]['cells'][2]['value'] = '".$cells[1]."';\n";
	        $str .= $id."tbl.rows[$c]['cells'][2]['control_properties'] = 'deactivated=true,input_class=input1';\n";
	       	$str .= $id."tbl.rows[$c]['cells'][3] = new Array;\n";
	        $str .= $id."tbl.rows[$c]['cells'][3]['value'] = '".$cells[2]."';\n";
	        $str .= $id."tbl.rows[$c]['cells'][3]['control_properties'] = 'deactivated=true,input_class=input1';\n";	         
	        $str .= $id."tbl.rows[$c]['cells'][4] = new Array;\n";
	        $str .= $id."tbl.rows[$c]['cells'][4]['value'] = '".$cells[3]."';\n";
	        $str .= $id."tbl.rows[$c]['cells'][4]['control_properties'] = 'deactivated=true,input_class=input1';\n";	         
	        $str .= $id."tbl.rows[$c]['cells'][5] = new Array;\n";
	        $str .= $id."tbl.rows[$c]['cells'][5]['value'] = '".$cells[4]."';\n";
	        $str .= $id."tbl.rows[$c]['cells'][5]['control_properties'] = 'deactivated=true,input_class=input1';\n";	         
	        $str .= $id."tbl.rows[$c]['cells'][6] = new Array;\n";
	        $str .= $id."tbl.rows[$c]['cells'][6]['value'] = '".$cells[5]."';\n";
	        $str .= $id."tbl.rows[$c]['cells'][6]['control_properties'] = 'deactivated=true,input_class=input1';\n";	         
	        $str .= $id."tbl.rows[$c]['cells'][7] = new Array;\n";
	        $str .= $id."tbl.rows[$c]['cells'][7]['value'] = '".$cells[6]."';\n";	         
	        $str .= $id."tbl.rows[$c]['cells'][7]['control_properties'] = 'deactivated=true,input_class=input1';\n";	         
	        $str .= $id."tbl.rows[$c]['cells'][8] = new Array;\n";
	        $str .= $id."tbl.rows[$c]['cells'][8]['value'] = '".$cells[7]."';\n";	         
	        $str .= $id."tbl.rows[$c]['cells'][8]['control_properties'] = 'deactivated=true,input_class=input1';\n";	         
	        $c++;
        }
        $result["{data}"] = $str;
        return $result;
    }     
}
?>