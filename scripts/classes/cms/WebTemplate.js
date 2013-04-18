var WebTemplate = Class.create(Entity, {
    
    putFileContent: function(file,text_area) {
        var args = new Object;
        args['file'] = file;
        var obj = this;
        //var app = objects.objects["Application"];
        //var root_path = app.root_path;
        if (file[0]!="/")
        	var root_path = "/opt/WAB2";
        else
        	var root_path = "";
        var file = root_path+"/"+file;
        new Ajax.Request("index.php",
            {
                method: "post",
                parameters: {ajax: true, object_id: this.object_id, hook: '4', arguments: Object.toJSON(args)},
                onSuccess: function(transport) {
                    var response = transport.responseText;
                    var text_arr = text_area.split("_");
//                    if (text_arr[0]!='template') 
                        editAreaLoader.setValue(obj.node.id+"_"+text_area+"_value",response);
//                    else
//                        tinyMCE.get(obj.node.id+"_"+text_area+"_value").setContent(response);
                }
            })
        
    },
    
    dispatchEvent:function($super,event,params) {
        $super(event,params);
        if (event=="CONTROL_VALUE_CHANGED") {
            if (params["parent_object_id"]==this.object_id) {
                var field_id = params["object_id"].replace(this.node.id+"_","");                
                var field_arr = field_id.split("_");
                var field_end = field_arr.pop();
                if (field_end=="file") {
                    field_end = "text";
                    var text_field = field_arr.join("_")+"_"+field_end;
                    this.putFileContent(params["value"],text_field);
                }
            }
        }
        if (event=="TAB_CHANGED") {
            if (params["tabset_id"]==this.tabsetName) {
                    var text_field = this.node.id+"_"+params["tab"]+"_text_value";
                    if (params["tab"]!="template") {
                        editAreaLoader.toggle(text_field,"off");
                        editAreaLoader.toggle(text_field,"on");
                    }
            }
        }
    }
});