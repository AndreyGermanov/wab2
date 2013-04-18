var EntitySelectWindow = Class.create(Entity, {

    onChange: function(event) {
        return 0;
    },
    
    dispatchEvent: function($super,event,params) {
        $super(event,params);
        if (event=="ENTITY_SELECTED_FROM_DATATABLE") {
            if (params["parent_object_id"]==this.object_id) {
                if (this.win!="" && this.win!=null && this.win!=0) {
                    this.raiseEvent("CONTROL_VALUE_CHANGED",$Arr("object_id="+this.parent_object_id+",value="+params["entity_id"]+",valueTitle="+params["entity_title"]+",target_object="+this.tableObject));
                    getWindowManager().remove_window(this.win.id);
                } else {
                    if (this.editorType=="window") {
                        window.opener.$O(this.parent_object_id,'').raiseEvent("CONTROL_VALUE_CHANGED",$Arr("object_id="+this.parent_object_id+",value="+params["entity_id"]+",old_value="+$O(this.parent_object_id,'').getValue()+",valueTitle="+params["entity_title"]+",target_object="+this.tableObject));
                        window.close();
                    } else {
                        this.raiseEvent("CONTROL_VALUE_CHANGED",$Arr("object_id="+this.parent_object_id+",value="+params["entity_id"]+",valueTitle="+params["entity_title"]+",old_value="+$O(this.parent_object_id,'').getValue()));                                            
                    }
                }
            }            
        }
    },
    
    selectButton_onClick: function(event) {
        var tbl = this.tbl;
        if (tbl.currentControl!=null && tbl.currentControl!=0 && tbl.currentControl!="") {
            var row = tbl.currentControl.node.parentNode.getAttribute("row");
            var it = tbl.getItem(row,0).getValue();
            var tit = tbl.getItem(row,'title').getValue();
            if (it!=0) {
                tbl.raiseEvent("ENTITY_SELECTED_FROM_DATATABLE",$Arr("object_id="+tbl.object_id+",parent_object_id="+tbl.parent_object_id+",entity_id="+it+",entity_title="+tit));
                if (this.win!="" && this.win!=null && this.win!=0) {               
                    getWindowManager().remove_window(this.win.id);
                } else if (this.editorType=="window")
                    window.close();
            }
        }
    }
});