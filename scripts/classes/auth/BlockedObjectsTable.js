var BlockedObjectsTable = Class.create(EntityDataTable, {
	
	deleteButton_onClick: function(event) {
	    if (this.objRole!=null && this.objRole["canRead"]!=null && this.objRole["canDelete"]=="false") {
	    	this.reportMessage("Не достаточно прав доступа !","error",true);
	    	return 0;
	    }
	    var delete_checks = this.getColValues(this.rows,1,true);
	    if (!confirm("Вы действительно хотите удалить выбранные элементы?"))
	        return 0;
	    var ids = this.getColValues(this.rows,0,true);
	    var deleted_entities = new Array;
	    var vl=0;
	    for (vl in delete_checks) {
	        if (delete_checks[vl]==1) {
	            deleted_entities[ids[vl]] = ids[vl];
	        }
	    }
	    if (deleted_entities.length==0) {
	        if (this.currentControl!=null & this.currentControl!=0)
	            var ent = this.getItem(this.currentControl.node.parentNode.getAttribute("row"),0);
	        if (ent!=null && ent!=0) {
	            deleted_entities[this.getItem(this.currentControl.node.parentNode.getAttribute("row"),0).getValue()] = this.getItem(this.currentControl.node.parentNode.getAttribute("row"),0).getValue();
	        }
	    }
	    var c=0;
    	for (c in deleted_entities) {
    		if (typeof deleted_entities[c] != "function")
    			this.raiseEvent("REMOVE_WINDOW",$Arr("object_id=Window"+deleted_entities[c].replace(/_/g,"")),true,this.name);
    	}
	}
});