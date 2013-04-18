var GlobalSearchSettings = Class.create(Entity, {
    OK_onClick: function(event) {
    	var classesList = new Array;
    	var fieldsList = new Array;
    	var data = this.getValues();
		var i=0;
		var i1=0;
		var c=null;
    	for (c in data) {
    		if (typeof data[c] != "function") {
    			var parts = c.split("_");
    			if (parts[0]=="obj") {
    				if (data[c]=="1") {
    					classesList[i] = parts[1];
    					i++;
    				}
    			}
    			if (parts[0]=="field") {
    				if (data[c]=="1") {
    					fieldsList[i1] = parts[1];
    					i1++;
    				}
    			}
    		}
    	}
    	this.opener_object.classesList = classesList;
    	this.opener_object.fieldsList = fieldsList;
    	this.opener_object.findButton_onClick(event);
    	getWindowManager().remove_window(this.win.id);
    }
});