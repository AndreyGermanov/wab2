var RightsTable = Class.create(DataTable, {
    getSingleValue: function() {
    	var arr = new Array;
    	var rows = this.rows;
    	var c = null;
    	for (c in rows) {
    		if (c==0)
    			continue;
    		if (typeof(rows[c])=="function")
    			continue;
    		var str = new Array;
    		for (c1 in rows[c]["cells"]) {
    			if (typeof(rows[c1])=="function")
    				continue;
    			str[str.length] = rows[c]["cells"][c1]["value"].replace(/\r\n/g,"").replace(/\n/g,"");    			
    		}
    		arr[arr.length] = str.join("~");
    	}    		    
    	return arr.join("|");
    }      
});