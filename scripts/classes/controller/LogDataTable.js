var LogDataTable = Class.create(EntityDataTable, {
    
    dispatchEvent: function($super,event,params) {
        if (event=="CONTROL_DOUBLE_CLICKED") {        
            return 0;
        }
        $super(event,params);
    },
    
    filterButton_onClick: function(event) {   
        if (current_context_menu!=null)
            removeContextMenu(event,null,true);
        var params = new Object;
        var obj = $O(this.parent_object_id,'');
        params["hook"] = "2";
        params["eventType"] = obj.eventType;
        params["eventDateStart"] = obj.eventDateStart;
        params["eventDateEnd"] = obj.eventDateEnd;
        params["eventIP"] = obj.eventIP;
        params["eventFilePath"] = obj.eventFilePath;
        params["eventFileNewPath"] = obj.eventFileNewPath;
        params["parent_object_id"] = this.object_id;
        getWindowManager().show_window("Window_AuditConditionsFloatDiv","AuditConditionsFloatDiv_"+this.module_id+"_"+this.object_id.replace(/_/g,''),params,this.object_id,this.node.id,null,true);
    },
    
    refreshButton_onClick: function(event) {
        this.rebuild();
    }    
});