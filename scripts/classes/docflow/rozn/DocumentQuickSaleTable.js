var DocumentQuickSaleTable = Class.create(DataTable, {
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
    		str[1] = rows[c]["cells"][1]["value"];
    		str[2] = rows[c]["cells"][2]["value"];
    		str[3] = rows[c]["cells"][3]["value"];
    		arr[arr.length] = str.join("~");
    	}    		    
    	return arr.join("|");
    },
    
    calcTable: function() {
    	var summa = this.summColumn('summa');
    	var discountSumma = $O(this.parent_object_id+"_allDiscountSumma","").getValue()*1;
    	if (discountSumma>summa)
    		discountSumma = summa;
    	var total = summa-discountSumma;
    	if (total<0)
    		total = 0;
    	$I(this.parent_object_id+"_orderSumma").value = summa.toFixed(2);
    	if (discountSumma>0) {
    		$I(this.parent_object_id+"_discountSumma").value = discountSumma.toFixed(2);
        	$O(this.parent_object_id+"_discountSummaStr").setValue(discountSumma.toFixed(2),true);        
    	} else
        	$O(this.parent_object_id+"_discountSummaStr").setValue(0,true);        
    	var prihodSumma = $O(this.parent_object_id+"_prihodSumma").getValue();
    	var backSumma = prihodSumma-total;
    	if (backSumma<0)
    		backSumma = 0;
    	$O(this.parent_object_id+"_orderSummaStr").setValue(summa.toFixed(2),true);    
    	$O(this.parent_object_id+"_totalSummaStr").setValue(total.toFixed(2),true);
    	$O(this.parent_object_id+"_backSummaStr").setValue(backSumma.toFixed(2),true);
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
        	var summa = parseFloat($O(objid+"_"+row_num+"_summa").getValue());
            if (obje.object_id.split("_").pop()=="cost") {
            	cost = parseFloat(params["value"]);
            	summa = cost*count;
            }
            if (obje.object_id.split("_").pop()=="count") {
            	count = parseFloat(params["value"]);
            	summa = cost*count;
            }
            if (obje.object_id.split("_").pop()=="summa") {
            	summa = parseFloat(params["value"]);
           		cost = summa/count;            		
            }
        	$O(objid+"_"+row_num+"_cost","").setValue(cost.toFixed(2),true);
        	$O(objid+"_"+row_num+"_summa","").setValue(summa.toFixed(2),true);
        	table_row_num = obje.node.parentNode.getAttribute("row");
        	this.rows[table_row_num]["cells"][this.getColumnNumber("cost")]["value"] = cost.toFixed(2);
        	this.rows[table_row_num]["cells"][this.getColumnNumber("summa")]["value"] = summa.toFixed(2);
        	this.calcTable();
        }
    },
    
    deleteRow: function($super,position) {
    	$super(position);
    	this.calcTable();
    },
    
    sort: function($super) {
        $super();
        alert(this.parent_object_id);
        alert($O(this.parent_object_id,""));
        $O(this.parent_object_id,'').checkTable();
    }    
});