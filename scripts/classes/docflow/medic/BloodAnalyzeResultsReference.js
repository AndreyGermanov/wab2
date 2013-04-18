var BloodAnalyzeResultsReference = Class.create(Reference, {    
    dispatchEvent: function($super,event,params) {        
        $super(event,params);        
        if (event == "DATATABLE_VALUE_CHANGED") {
            if (params["parent"]==this.object_id) {
                if (params["object_id"] == "BloodAnalyzeTypesTable_"+this.module_id+"_"+this.object_id.replace(/_/g,"")) {
                    $I(this.node.id+"_defs").value = params["value"].replace(/xox/g,"=");
                }
            }
        }
    }
});