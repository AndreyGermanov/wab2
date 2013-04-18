var WebEntity = Class.create(Entity, {
    
    dispatchEvent: function($super,event,params) {
        
        $super(event,params);
        
        if (event == "DATATABLE_VALUE_CHANGED") {
            if (params["parent"]==this.object_id) {
                var arr = params["object_id"].split("_");
                if (params["object_id"] == "PersistedFieldsTable_"+this.module_id+"_"+this.object_id.replace(this.module_id+"_","").replace(/_/g,"")+"Childs") {
                    $I(this.node.id+"_childPersistedFields").value = params["value"].replace(/xox/g,"=");
                }
                if (params["object_id"] == "CacheDepsTable_"+this.module_id+"_"+this.object_id.replace(this.module_id+"_","").replace(/_/g,"")) {
                    $I(this.node.id+"_cacheDeps").value = params["value"].replace(/xox/g,"=");
                }
                if (params["object_id"] == "CacheDepsTable_"+this.module_id+"_"+this.object_id.replace(this.module_id+"_","").replace(/_/g,"")+"Childs") {
                    $I(this.node.id+"_childCacheDeps").value = params["value"].replace(/xox/g,"=");
                }
                if (params["object_id"] == "RightsTable_"+this.module_id+"_"+this.name+"users") {
                    $I(this.node.id+"_usersRightsTable").value = params["value"].replace(/xox/g,"=");
                }
                if (params["object_id"] == "RightsTable_"+this.module_id+"_"+this.name+"roles") {
                    $I(this.node.id+"_rolesRightsTable").value = params["value"].replace(/xox/g,"=");
                }
            }
        }
        if (event == "TAB_CHANGED") {
            if (params["tabset_id"]==this.fieldsTabsetName) {
                if (params["tab"]=="fieldsChilds" && this.childFieldsTableBuild!=true) {
                    if (this.module_id=="")
                        var tbl = $O("PersistedFieldsTable_"+this.object_id.replace(/_/g,"")+"Childs");
                    else
                        var tbl = $O("PersistedFieldsTable_"+this.module_id+"_"+this.object_id.replace(this.module_id+"_","").replace(/_/g,"")+"Childs");
                    tbl.build(true);         
                    tbl.sort();
                    $I(tbl.node.id+"_innerFrame").src = tbl.frameSrc;
                    this.childFieldsTableBuild = true;
                }
            }
            if (params["tabset_id"]==this.cachedepsTabsetName) {
                if (params["tab"]=="cacheDepsChilds" && this.childCacheDepsTableBuild!=true) {
                    if (this.module_id=="")
                        var tbl = $O("CacheDepsTable_"+this.object_id.replace(/_/g,"")+"Childs");
                    else
                        var tbl = $O("CacheDepsTable_"+this.module_id+"_"+this.object_id.replace(this.module_id+"_","").replace(/_/g,"")+"Childs");
                    tbl.build(true);                    
                    tbl.sort();
                    $I(tbl.node.id+"_innerFrame").src = tbl.frameSrc;
                    this.childCacheDepsTableBuild = true;
                }
            }
            if (params["tabset_id"]==this.tabsetName) {
                if (params["tab"]=="cacheDepsTable" && this.cacheDepsTableBuild!=true) {
                    if (this.module_id=="")
                        var tbl = $O("CacheDepsTable_"+this.object_id.replace(/_/g,""));
                    else
                        var tbl = $O("CacheDepsTable_"+this.module_id+"_"+this.object_id.replace(this.module_id+"_","").replace(/_/g,""));
                    tbl.build(true);
                    tbl.sort();
                    $I(tbl.node.id+"_innerFrame").src = tbl.frameSrc;
                    this.cacheDepsTableBuild = true;
                }
            }
        }        
    },
    
    afterSave: function($super) {
        $super();
        if (this.isStatic) {
            if (result!=0 && result!=null && !parseInt(result)) {
                var res_arr = result.split("\n");
                if (res_arr[0]!=0) {
                    res_arr = res_arr[0].split(";");
                    var i=0;
                    for (i=0;i<res_arr.length;i++) {
                        if (res_arr[i]!="mainpage") {
                            topWindow.cacheUpdateLinks[topWindow.cacheUpdateLinks.length] = "http://"+this.siteName+"/?"+res_arr[i];
                        }
                        else {
                            topWindow.cacheUpdateLinks[topWindow.cacheUpdateLinks.length] = "http://"+this.siteName;
                        }
                    }
                }
            }            
        }        
    }        
});