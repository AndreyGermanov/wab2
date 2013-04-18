var WindowContextMenu = Class.create(ContextMenu,{

    onClick: function(event) {
        var elem = eventTarget(event);
        switch(get_elem_id(elem).replace("_text","")) {
            case "saveSettings":
                var pos = getElementPosition(this.opener_item.id);
        		var obj = this;            
        		var args = new Object;
        		args["left"] = pos.left;
        		args["top"] = pos.top;
        		args["width"] = pos.width;
        		args["height"] = pos.height;
        		new Ajax.Request("index.php", {
        			method:"post",
        			parameters: {ajax: true, object_id: "Window_"+obj.opener_object.object_id,hook: '3', arguments: Object.toJSON(args)},
        			onSuccess: function(transport)
        			{                            
        			}
        		});                
                break;
            case "removeSettings":
        		var args = new Object;
        		var obj = this;            
        		new Ajax.Request("index.php", {
        			method:"post",
        			parameters: {ajax: true, object_id: "Window_"+obj.opener_object.object_id,hook: '4', arguments: Object.toJSON(args)},
        			onSuccess: function(transport)
        			{                            
        			}
        		});                
                break;
            case "addAutorun":
        		var args = new Object;
        		args["php_object_id"] = this.opener_object.php_object_id;
        		args["object_text"] = this.opener_object.object_text;
        		var obj = this;            
        		new Ajax.Request("index.php", {
        			method:"post",
        			parameters: {ajax: true, object_id: "Window_"+obj.opener_object.object_id,hook: '5', arguments: Object.toJSON(args)},
        			onSuccess: function(transport)
        			{                            
        			}
        		});                
                break;
            case "removeAutorun":
        		var args = new Object;
        		args["php_object_id"] = this.opener_object.php_object_id;
        		var obj = this;            
        		new Ajax.Request("index.php", {
        			method:"post",
        			parameters: {ajax: true, object_id: "Window_"+obj.opener_object.object_id,hook: '6', arguments: Object.toJSON(args)},
        			onSuccess: function(transport)
        			{                            
        			}
        		});                
                break;
        }
        globalTopWindow.removeContextMenu();
        event = event || window.event;
        event.cancelBubble = true;
    }
});