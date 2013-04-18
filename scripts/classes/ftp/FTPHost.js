var FTPHost = Class.create(Entity,{
	dispatchEvent: function($super,event,params) {
		$super(event,params);
		if (event == "DATATABLE_VALUE_CHANGED") {
			if (params["parent"]==this.object_id) {
				if (params["object_id"] == this.tbl.id) {
					$I(this.node.id+"_usersTable").value = params["value"].replace(/xox/g,"=");
				}
			}
		}
	},

    OK_onClick: function(event) {
        var data = this.getValues();
        var args = new Object;
        var o=null;
		for (o in data)
			if (typeof data[o] != "function")
				args[o] = data[o];
		args["usersTable"] = this.tbl.getSingleValue();

		var loading_img = document.createElement("img");
        loading_img.src = this.skinPath+"images/Tree/loading2.gif";
        loading_img.style.zIndex = "100";
        loading_img.style.position = "absolute";
        loading_img.style.top=(window.innerHeight/2-33);
        loading_img.style.left=(window.innerWidth/2-33);
        this.node.appendChild(loading_img);
        var obj=this;
        new Ajax.Request("index.php",
        {
            method: "post",
            parameters: {ajax: true, object_id: this.object_id,
                         hook: '3', arguments: Object.toJSON(args)},
            onSuccess: function(transport) {
                var response = transport.responseText;
                obj.node.removeChild(loading_img);
                if (response!="" && response!=parseInt(response)) {
                    response = response.evalJSON(response);
                    obj.reportMessage(response["error"],"error",true);
                } else {
                    obj.win.node.setAttribute("changed",false);
                    $I(obj.win.node.id+"_headertext").innerHTML = $I(obj.win.node.id+"_headertext").innerHTML.replace("*","");
                    obj.opener_object.buildTable();
                }
            }
        });
    },	
    
    help_onClick: function(event) {
		getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"guide11.3","HTMLBook_"+this.module_id+"_controller_11.3",null,this.opener_item.getAttribute("object"),this.opener_item.id);
	}    
});