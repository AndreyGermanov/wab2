var FTPActiveUsersTable = Class.create(DataTable, {
    
    dispatchEvent: function($super,event,params) {
        if (event=="CONTROL_DOUBLE_CLICKED") {        
            return 0;
        }
        if (event=="CONTROL_VALUE_CHANGED") {
			if (params["object_id"] == this.node.id+"_currentUser") {
				this.currentUser = params["value"];
				this.rebuild();
				$super(event,params);
			}
		}
		$super(event,params);
    },
    
    refreshButton_onClick: function(event) {
        this.rebuild();
    },
    
    deleteButton_onClick: function(event) {
		var args = new Object;
		if (this.currentUser=="")
			return 0;
		args["user"] = this.currentUser;
		var obj=this;
		new Ajax.Request("index.php",
		{
			method: "post",
			parameters: {ajax: true, object_id: this.object_id,
						hook: '4', arguments: Object.toJSON(args)},
			onSuccess: function(transport) {
				obj.currentUser = '';
				obj.rebuild();
			}
		});
	},
    
	rebuild: function() {
		var args = new Object;
		args["currentUser"] = this.currentUser;
		args["ftpHostName"] = this.ftpHostName; 

		var loading_img = document.createElement("img");
		loading_img.src = this.skinPath+"images/Tree/loading2.gif";
		loading_img.style.zIndex = "100";
		loading_img.style.position = "absolute";
		loading_img.style.top=(window.innerHeight/2-33);
		loading_img.style.left=(window.innerWidth/2-33);
		this.node.appendChild(loading_img);
		var tbl=this;
		var obj=this;
		new Ajax.Request("index.php",
		{
			method: "post",
			parameters: {ajax: true, object_id: this.object_id,
						hook: '3', arguments: Object.toJSON(args)},
			onSuccess: function(transport) {
				var response = transport.responseText;
				obj.node.removeChild(loading_img);
				tbl.deleteRows();
				eval(response);
				tbl.build();
				$I(obj.node.id+"_currentUser").setAttribute("type","list,"+tbl.userList);
				$O(obj.node.id+"_currentUser").build();
			}
		});
	}
});