var WebSite = Class.create(Entity, {
    
    dispatchEvent :function($super,event,params) {
        $super(event,params);
        if (event=="CONTROL_VALUE_CHANGED") {
            if (params["parent_object_id"]==this.object_id && params["object_id"] == this.object_id+"_db_type") {
                if (params["value"]=="pdo_sqlite") {
                    $I(this.node.id+"_dbProperties").style.display = "none";
                    $I(this.node.id+"_sqlitePath").style.display = "";
                } else {
                    $I(this.node.id+"_dbProperties").style.display = "";
                    $I(this.node.id+"_sqlitePath").style.display = "none";                    
                }
            }
        }
    },
    
    afterSaveRaiseEvents: function(values) {
        if (values["old_name"]!=values["name"]) {
            var fields = values;
            fields["loaded"] = "target_object="+fields["object_id"]+"#className=WebEntity#loaded=false#editorType=WABWindow#adapterId=SiteDataAdapter_"+this.module_id+"_"+this.name;
            fields["new_object_id"] = "WebSiteTree_"+this.module_id+"_"+this.name+"_"+this.name;
            fields["object_id"] = "WebSiteTree_"+this.module_id+"_"+fields["name"]+"_"+fields["name"];
            fields["target_id"] = "WebSiteTree_"+this.module_id+"_"+fields["name"]+"_"+fields["name"];
            fields["subtree"] = "true";
            this.raiseEvent("ENTITY_ADDED",fields,true);            
        } else {
            this.raiseEvent("ENTITY_CHANGED",values,true);                                
        }        
    }
    
});