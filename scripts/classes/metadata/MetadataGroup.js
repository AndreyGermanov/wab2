var MetadataGroup = Class.create(Entity, {
	
    OK_onClick: function(event) {
        // Работаем только если что-то действительно изменилось
        var is_changed = this.win.node.getAttribute('changed');
        if (is_changed!="true")
            return 0;

        // Проверяем корректность ввода данных
        var data = this.getValues();

        // Формируем параметры, передаваемые серверу для записи
        var args = data.toObject();
        args["fields"] = this.fieldsTable.getSingleValue();
        args["groups"] = this.groupsTable.getSingleValue();
        args["group"] = this.group;
        // Выводим индикатор прогресса
        var obj = this;
        var wm = getWindowManager();
        var loading_img = document.createElement("img");                
        loading_img.src = this.skinPath+"images/Tree/loading2.gif";
        loading_img.style.zIndex = "100";
        loading_img.style.position = "absolute";
        loading_img.style.top=(window.innerHeight/2-33);
        loading_img.style.left=(window.innerWidth/2-33);        	        
        this.node.appendChild(loading_img);
        
        new Ajax.Request("index.php", {
            method:"post",
            parameters: {ajax: true, object_id: this.object_id, hook: '3', arguments: Object.toJSON(args)},
            onSuccess: function(transport)
            {
                var response = trim(transport.responseText.replace("\n",""));
                obj.node.removeChild(loading_img);
                if (response!="")
                {
                    response = response.evalJSON(true);
                    if (response["error"]!=null)
                    {
                        obj.reportMessage(response["error"],"error",true);
                        return 0;
                    }
                    else
                        obj.reportMessage(response,"error",true);
                }
                else {
                	if (obj.old_name!="" && (obj.old_name!=data["name"] || obj.old_title != data["title"])) {
	                	var tree = $O(obj.opener_item.getAttribute("object"),"");
	                	var elems = tree.node.getElementsByTagName("*");
	                	var i=0;
	                	var elem = 0;
	                	var arr = new Array;
	                	for (i=0;i<elems.length;i++) {
	                		elem = elems[i];
	                		if (elem.id!=null) {
	                			var elem_end = elem.id.split("_").pop();
	                			if (elem_end==obj.old_name) {
	                				var old_id = elem.getAttribute("target_object");
	                				arr = old_id.split("_");
	                				arr.pop();
	                				arr[arr.length] = data['name'];
	                				var new_id = arr.join("_");
	                                var new_target_id = new_id;
	                                var parent = elem.parentNode;
	                                while (parent.getAttribute("target_object")==old_id || parent.getAttribute("target_object")=="" || parent.getAttribute("target_object")==null) {
	                                	parent = parent.parentNode;
	                                }
	                                var old_owner_id = parent.getAttribute("target_object");
	                                var new_owner_id = old_owner_id;
	                                var params = new Array;
	                                if (obj.opener_item.parentNode !=null)
	                                    params["object_id"] = old_id;
	                                params["new_object_id"] = new_id;
	                                params["target_id"] = new_target_id;
	                                params["text"] = data["name"];
	                                params["old_text"] =obj.old_name;
	                                params["title"] = data["title"];
	                                params["old_title"] = obj.old_title;
	                                params["parent"] = new_owner_id;
	                                params["sorting"] = "none";
                            		var icon = obj.skinPath+"images/Tree/metagroup.png";	                                
	                                params["image"] = icon;
	                                if (data["old_name"]=="") {
	                                    params["action"] = "add";
	                                    obj.raiseEvent("NODE_CHANGED",params,true);
	                                }
	                                else {
	                                    params["action"] = "change";
	                                    obj.raiseEvent("NODE_CHANGED",params,true);
	                                }
	                			}                			
	                		}
	                	}
                	}
                	if (obj.old_name!="") {
                		var old_fields = objToArr(obj.old_fields);
                		var fields = objToArr(args["fields"]);
                		var old_groups = objToArr(obj.old_groups);
                		var groups = objToArr(args["groups"]);
                		var diffFields = old_fields.diff(fields);
                		var diffGroups = old_groups.diff(groups);
                		var diffFields1 = fields.diff(old_fields);
                		var diffGroups1 = groups.diff(old_groups);
                		if (diffFields.length>0 || diffGroups.length>0 || diffFields1.length>0 || diffGroups1.length>0) {
                            var parent = obj.opener_item.parentNode;
                            while (parent.getAttribute("target_object")==obj.opener_item.getAttribute("target_object") || parent.getAttribute("target_object")=="" || parent.getAttribute("target_object")==null) {
                            	parent = parent.parentNode;
                            }
                            obj.fullGroup = parent.getAttribute("target_object");
                            obj.opener_item.parentNode.parentNode.removeChild(obj.opener_item.parentNode);
                            obj.old_name="";
                		}
                	}
                	if (obj.old_name=="") {
                        var old_id = obj.className+"_"+obj.module_id+"_"+obj.rnd+"_"+data["name"];
                        var new_id = old_id;
                        if (obj.fullGroup == null || obj.fullGroup=="") {
                            obj.fullGroup = obj.opener_item.parentNode.id;
                        }
                        var old_owner_id = obj.fullGroup;
                        var new_owner_id = old_owner_id;
                        var new_target_id = new_id;
                        var params = new Array;
                        params["object_id"] = old_id;
                        params["new_object_id"] = new_id;
                        params["target_id"] = new_target_id;
                        params["text"] = data["name"];
                        params["old_text"] =data["name"];
                        params["title"] = data["title"];
                        params["old_title"] = data["title"];
                        params["parent"] = new_owner_id;
                        params["sorting"] = "keyAlpha";
                        params["loaded"] = false;
                        var icon = obj.skinPath+"images/Tree/metagroup.png";
                        params["image"] = icon;
                        params["action"] = "add";
                        obj.raiseEvent("NODE_CHANGED",params,true);                		
                	}                	
                    obj.win.node.setAttribute('changed',"false");
                    $I(obj.win.node.id+"_headertext").innerHTML = $I(obj.win.node.id+"_headertext").innerHTML.replace("*","");
                    wm.remove_window(obj.win.object_id);
                }
            }
        });
    }		
});