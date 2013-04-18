var DocumentInvoiceTable = Class.create(DataTable, {
    getSingleValue: function() {
    	var arr = new Array;
    	var rows = this.rows;
    	var c=0;
    	for (c in rows) {
    		if (c==0)
    			continue;
    		if (typeof(rows[c])=="function")
    			continue;
    		var str = new Array;
    		str[0] = rows[c]["cells"][0]["value"];
    		str[8] = rows[c]["cells"][1]["value"];
    		str[1] = rows[c]["cells"][2]["value"];
    		str[2] = rows[c]["cells"][3]["value"];
    		str[3] = rows[c]["cells"][4]["value"];
    		str[4] = rows[c]["cells"][5]["value"];
    		str[5] = rows[c]["cells"][6]["value"];
    		str[6] = rows[c]["cells"][7]["value"];
    		str[7] = rows[c]["cells"][8]["value"];
    		arr[arr.length] = str.join("~");
    	}    		    
    	return arr.join("|");
    },
    
    calcTable: function() {
    	var total = this.summColumn('total');
    	var summaNDS = this.summColumn('summaNDS');
    	$I(this.parent_object_id+"_invoiceNDS").value = summaNDS.toFixed(2);
    	$I(this.parent_object_id+"_invoiceSumma").value = total.toFixed(2);    	
    	$O(this.parent_object_id+"_invoiceNDSStr").setValue(summaNDS.toFixed(2),true);
    	$O(this.parent_object_id+"_invoiceSummaStr").setValue(total.toFixed(2),true);    	
    },
    
    CONTROL_VALUE_CHANGED_processEvent: function(params) {
        var obje = $O(params["object_id"],"");
        if (params["value"]==params["old_value"])
        	return 0;
        if (obje!=null && obje.parent_object_id == this.object_id) {
        	if (isNaN(params["value"]))
        		return 0;
            var id_arr = obje.object_id.split("_");
            id_arr.pop();
            var row_num = id_arr.pop();
            var objid = id_arr.join("_");
        	var count = parseFloat($O(objid+"_"+row_num+"_count").getValue());
        	var cost = parseFloat($O(objid+"_"+row_num+"_cost").getValue());
        	var stNDS = parseFloat($O(objid+"_"+row_num+"_stNDS").getValue());
        	var summa = parseFloat($O(objid+"_"+row_num+"_summa").getValue());
        	var summaNDS = parseFloat($O(objid+"_"+row_num+"_summaNDS").getValue());
        	var total = parseFloat($O(objid+"_"+row_num+"_total").getValue());
            if (obje.object_id.split("_").pop()=="cost") {
            	cost = parseFloat(params["value"]);
            	summa = cost*count;
            	summaNDS = "";
            	total = "";
            	if ($O(this.parent_object_id+"_includeNDS").getValue()=="1") {
            		summaNDS = summa/(100+stNDS)*stNDS;
            		total = summa;
            	} else {
            		summaNDS = summa/100*stNDS;    
            		total = summa+summaNDS;
            	}
            }
            if (obje.object_id.split("_").pop()=="count") {
            	count = parseFloat(params["value"]);
            	summa = cost*count;
            	summaNDS = "";
            	total = "";
            	if ($O(this.parent_object_id+"_includeNDS").getValue()=="1") {
            		summaNDS = summa/(100+stNDS)*stNDS;
            		total = summa;
            	} else {
            		summaNDS = summa/100*stNDS;    
            		total = summa+summaNDS;
            	}
            }
            if (obje.object_id.split("_").pop()=="summa") {
            	summa = parseFloat(params["value"]);
            	cost = 0;
            	if (count!=0)
            		cost = summa/count;            		
            	summaNDS = "";
            	total = "";
            	if ($O(this.parent_object_id+"_includeNDS").getValue()=="1") {
            		summaNDS = summa/(100+stNDS)*stNDS;
            		total = summa;
            	} else {
            		summaNDS = summa/100*stNDS;    
            		total = summa+summaNDS;
            	}
            }
            if (obje.object_id.split("_").pop()=="stNDS") {
            	stNDS = parseFloat(params["value"]);            	
            	if ($O(this.parent_object_id+"_includeNDS").getValue()=="1") {            		
            		summaNDS = summa/(100+stNDS)*stNDS;
            		total = summa;
            	} else {
            		summaNDS = summa/100*stNDS;    
            		total = summa+summaNDS;
            	}
            }
            if (obje.object_id.split("_").pop()=="summaNDS") {
            	summaNDS = parseFloat(params["value"]);
            	if ($O(this.parent_object_id+"_includeNDS").getValue()=="1") {            		
            		total = summa;
            	} else {            		    
            		total = summa+summaNDS;
            	}
            }
        	if (isNaN(total))
        		total = 0;            
        	$O(objid+"_"+row_num+"_cost","").setValue(cost.toFixed(2),true);
        	$O(objid+"_"+row_num+"_summa","").setValue(summa.toFixed(2),true);
        	$O(objid+"_"+row_num+"_summaNDS","").setValue(summaNDS.toFixed(2),true);
        	$O(objid+"_"+row_num+"_total","").setValue(total.toFixed(2),true);
        	var table_row_num = obje.node.parentNode.getAttribute("row");        	
        	this.rows[table_row_num]["cells"][this.getColumnNumber("cost")]["value"] = cost.toFixed(2);
        	this.rows[table_row_num]["cells"][this.getColumnNumber("summa")]["value"] = summa.toFixed(2);
        	this.rows[table_row_num]["cells"][this.getColumnNumber("summaNDS")]["value"] = summaNDS.toFixed(2);
        	this.rows[table_row_num]["cells"][this.getColumnNumber("total")]["value"] = total.toFixed(2);
        	this.calcTable();
        }
    },
    
    sort: function($super) {
        $super();
        $O(this.parent_object_id,'').checkTable();
    },

    deleteRow: function($super,position) {
    	$super(position);
    	this.calcTable();
    },
    
    addButton_onClick: function($super,event) {
    	var maxRow = this.getMaxRow();
    	this.emptyrow['cells'][0]['control_properties'] = "deactivated=true,input_class=input1,selectClass=SelectEntityFloatMenu,selectOptions=entityClasszozoReferenceProducts~displayFieldzozotitle~searchFieldzozotitle~resultFieldszozotitle-code-cost-dimension.title-NDS|product-code-cost-ed-stNDS~resultObjectzozo"+this.object_id+"_"+maxRow+"~showAdvancedzozotrue,showOnKeyPress=true,hideSelectButton=true";
    	this.emptyrow['cells'][1]['control_properties'] = "deactivated=true,input_class=input1,selectClass=SelectEntityFloatMenu,selectOptions=entityClasszozoReferenceProducts~displayFieldzozocode~searchFieldzozocode~resultFieldszozotitle-code-cost-dimension.title-NDS|product-code-cost-ed-stNDS~resultObjectzozo"+this.object_id+"_"+maxRow+"~showAdvancedzozotrue,showOnKeyPress=true,hideSelectButton=true";
    	$super(event);
    }    
});