var MetadataContextMenu = Class.create(ContextMenu, {
    onClick: function(event) {
    	var elem = eventTarget(event);
        switch(get_elem_id(elem).replace("_text",""))
        {
            case "change":
                var params = new Object;
                var win="Window_"+this.target.replace(/_/g,"");
                getWindowManager().show_window(win,this.target,params,this.opener_object,this.opener_item.id);
            	break;
            case "add":
            	var arr = this.target.split("_");            	
            	var className = "";
            	switch(arr[0]) {
            		case "MetadataGroup":
            			className = "MetadataObjectField";
            			break;
            		case "MetadataModelGroup":
            			className = "MetadataObjectModel";
            			break;
            		case "MetadataCodeGroup":            		
            			className = "MetadataObjectCode";
            			break;            			
            	}
            	var group = arr.pop();
                var params = new Object;
            	params["group"] = group;
            	params["fullGroup"] = this.target;
            	params["hook"] = "setParams";
            	var target = className+"_"+this.opener_object.module_id+"_"+(Math.round(Math.random() * 50000))+"_";
            	var win = "Window_"+target.replace(/_/g,"");
                getWindowManager().show_window(win,target,params,this.opener_object,this.opener_item.id);
            	break;     
            case "addGroup":
            	var arr = this.target.split("_");            	
            	var group = arr.pop();
                var params = new Object;
            	params["group"] = group;
            	params["fullGroup"] = this.target;
            	params["hook"] = "setParams";
            	var target = arr[0]+"_"+this.opener_object.module_id+"_"+(Math.round(Math.random() * 50000))+"_";
            	var win = "Window_"+target.replace(/_/g,"");
                getWindowManager().show_window(win,target,params,this.opener_object,this.opener_item.id);
            	break;     
            case "remove":
                if (confirm("Вы действительно хотите удалить этот объект ?")) {
                	var obj = this;
                	new Ajax.Request("index.php",{
                		method:"post",
                		parameters: {ajax:true,object_id:obj.target,hook: '4'},
                		onSuccess:function(transport) {
                			var response = trim(transport.responseText.replace("\n",""));
                			if (response.length>1) {
                				response = response.evalJSON();
                				if (response["error"]!=null)
                					obj.reportMessage(response["error"],"error",true);
                			} else {
				            	var arr = obj.target.split("_");
				            	var className = arr[0];
				            	var name = arr.pop();
				            	var tree = $O(obj.opener_item.getAttribute("object"),'');
				            	var elems = tree.node.getElementsByTagName("*")
				            	var i=0;
				            	var elem = 0;
				            	for (i=0;i<elems.length;i++) {
				            		elem = elems[i];
				            		var target_id = elem.getAttribute("target_object");
				            		if (target_id!=null) {
				            			var elem_start = target_id.split("_").shift();
				            			var elem_end = target_id.split("_").pop();
				            			if (elem_start==className && elem_end==name) {
				                            obj.raiseEvent("NODE_CHANGED",$Arr("action=delete,object_id="+target_id),true);            				
				            			}
				            		}
				            	}            	
                			}
                		}
                	});
                }
        }
        globalTopWindow.removeContextMenu();
        event = event || window.event;
        event.cancelBubble = true;
    }
});