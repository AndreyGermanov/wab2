var EntityProfile = Class.create(Entity, {
		
	CONTROL_VALUE_CHANGED_processEvent: function(params) {
		if (params["parent_object_id"]==this.object_id) {
			var elem = $O(params["object_id"],"");
			if (elem.node.getAttribute("isSimpleProfileParam")!=null) {
				if (params["value"]=="code") {
					$O(params["object_id"]+"Code","").node.style.display = "";
					if ($O(params["object_id"]+"_name","")!=null)
						$O(params["object_id"]+"_name","").node.style.display = "none";
				}
				else {
					$O(params["object_id"]+"Code","").node.style.display = "none";
					if ($O(params["object_id"]+"_name","")!=null)
						$O(params["object_id"]+"_name","").node.style.display = "";
				}
			}	
		}
	},
		
	OK_onClick: function(event) {
        // Работаем только если что-то действительно изменилось
		var is_changed = false;
		if (this.win.node!=null)
			is_changed = this.win.node.getAttribute('changed');
		else
			is_changed = true;
        if (is_changed!="true") {
            return 0;
        }
		
		this.args = this.getValues().toObject();
		var o=null;
		for (o in this) {
			if (typeof this[o] == "function") {
				if (o.search(/getData_/)!=-1) {
					this[o]();
				}
			}
		}
        var obj = this;
        new Ajax.Request("index.php",
        {
            method: "post",
            parameters: {ajax: true, object_id: obj.object_id,hook: '3',arguments: Object.toJSON(obj.args)},
            onSuccess: function(transport) {
                var response = transport.responseText;
                if (response!="") {
                    var error = response.evalJSON();
                    obj.reportMessage(error["error"],"error",true);
                } else {
                    $I(obj.win.node.id+"_headertext").innerHTML = $I(obj.win.node.id+"_headertext").innerHTML.replace("*","");
                    obj.win.node.setAttribute('changed',"false");
                    obj.raiseEvent("ROLE_PROFILE_CHANGED",$Arr("profileId="+obj.profileId+",profileTitle="+obj.profileTitle+",parent_object_id="+obj.opener_object.object_id));
                }
            }
        });		
	}
});