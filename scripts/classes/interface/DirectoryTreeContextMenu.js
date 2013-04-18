var DirectoryTreeContextMenu = Class.create(ContextMenu,{
    onClick: function(event) {
        var dirtree = this;
        var elem = eventTarget(event);
        var current_path = this.opener_item.getAttribute("target_object");
        var elem_id = this.opener_item.id;
        var elemid = getClientId(elem_id);
        var elem_arr = elemid.split("_");
        elem_arr.pop();
        var elemid = elem_arr.join("_");        
        var module_id = this.opener_object.module_id;
        switch(get_elem_id(elem).replace("_text",""))
        {
            case "add":
                var new_folder_name = prompt("Введите имя папки");
                if (new_folder_name!=null) {
				var args = new Object;
				args["folder"] = current_path+"/"+new_folder_name;
                new Ajax.Request("index.php",
                    {
                        method: "post",
                        parameters: {ajax: true, object_id: "DirectoryTree_"+module_id+"_Dirs",
                                     hook: '4', arguments: Object.toJSON(args)},
                        onSuccess: function(transport) {
                            var response = trim(transport.responseText).replace("\n","");
                            if (response.length>1)
                            {
                                var response_object = response.evalJSON();
                                if (response_object["error"]!=null)
                                    dirtree.reportMessage(response_object["error"],"error",true);
                                else
                                    dirtree.reportMessage(response,"error",true);
                            }
                            else
                            {
                                dirtree.opener_object.addTreeNode(current_path+"/"+new_folder_name,
                                                               new_folder_name,
                                                               dirtree.opener_object.skinPath+"images/Tree/folder.png",
                                                               current_path,true);
                                dirtree.opener_object.moveTreeNode(current_path+"/"+new_folder_name,
                                                                current_path,'alpha');
                            }
                        }
                    });
                }
                break;
            case "addfile":
                var id_arr = this.opener_item.id.split("_");
                id_arr.pop();
                var id = id_arr.join("_")+"_image";
                var file = $I(id).src.split("/").pop();
                if (file=="file.png")
                    return 0;
                var new_folder_name = prompt("Введите имя файла");
                if (new_folder_name!=null) {
				var args = new Object;
				args["file"] = current_path+"/"+new_folder_name;
                new Ajax.Request("index.php", {
                    method: "post",
                    parameters: {ajax: true, object_id: "DirectoryTree_"+module_id+"_Dirs",
                                 hook: '5', arguments: Object.toJSON(args)},
                    onSuccess: function(transport) {
                        var response = trim(transport.responseText).replace("\n","");
                        if (response.length>1) {
                            var response_object = response.evalJSON();
                            if (response_object["error"]!=null)
                                dirtree.reportMessage(response_object["error"],"error",true);
                            else
                                dirtree.reportMessage(response,"error",true);
                        }
                        else {
                            dirtree.opener_object.addTreeNode(current_path+"/"+new_folder_name,
                                                           new_folder_name,
                                                           dirtree.opener_object.skinPath+"images/Tree/file.png",
                                                           current_path,true);
                            dirtree.opener_object.moveTreeNode(current_path+"/"+new_folder_name,
                                                            current_path,'alpha');
                        }
                    }
                });
                }
                break;
            case "change":              
                var new_folder_name = prompt("Введите новое имя ",current_path.split("/").pop());
                if (new_folder_name!=null) {
                    var current_target = current_path.split("/");
                    //current_target.shift();
                    var current_target_id = getClientId(current_target.join("_"));
                    var new_target_arr = current_path.split("/");
                    new_target_arr.pop();
                    var new_target = new_target_arr.join("/");
                    new_target = new_target+"/"+new_folder_name;
                    new_target_id = getClientId(new_target);
                    new_target_id = new_target_id.split("_");
                    new_target_id = new_target_id.join("_");
                    var args = new Object;
                    args["old_folder"] = current_path;
                    args["new_folder"] = new_target;
                    new Ajax.Request("index.php", {
                        method: "post",
                        parameters: {ajax: true, object_id: "DirectoryTree_"+module_id+"_Dirs",
                                     hook: '6', arguments: Object.toJSON(args)},
                        onSuccess: function(transport) {
                            var response = trim(transport.responseText).replace("\n","");
                            if (response.length>1) {
                                var response_object = response.evalJSON();
                                if (response_object["error"]!=null)
                                    dirtree.reportMessage(response_object["error"],"error",true);
                                else
                                    dirtree.reportMessage(response,"error",true);
                            }
                            else {
                                var el =$I(elemid);
                                $I(elemid+"_text").innerHTML = new_folder_name;
                                $I(elemid).setAttribute("target_object",$I(elemid).getAttribute("target_object").replace(current_path,new_target));
                                $I(elemid).id = $I(elemid).id.replace(current_target_id,new_target_id);
                                var elems = el.getElementsByTagName("*");
                                for (var c=0;c<elems.length;c++) {
                                    if (elems[c].getAttribute("target_object")!=null)
                                        elems[c].setAttribute("target_object",elems[c].getAttribute("target_object").replace(current_path,new_target));
                                    if (elems[c].id!=null) {                                            
                                        elems[c].id = elems[c].id.replace(current_target_id,new_target_id);
                                    }
                                }
                            }
                        }
                    });
                }
                break;
            case "remove":
                if (confirm("Вы действительно хотите удалить эту папку и все ее содержимое ?")) {
					var args = new Object;
					args["folder"] = current_path;
                    new Ajax.Request("index.php", {
                        method: "post",
                        parameters: {ajax: true, object_id: "DirectoryTree_"+module_id+"_Dirs",
                                     hook: '7', arguments: Object.toJSON(args)},
                        onSuccess: function(transport) {
                            var response = trim(transport.responseText).replace("\n","");
                            if (response.length>1) {
                                var response_object = response.evalJSON();
                                if (response_object["error"]!=null)
                                    dirtree.reportMessage(response_object["error"],"error",true);
                                else
                                    dirtree.reportMessage(response,"error",true);
                            }
                            else {
                                dirtree.opener_object.deleteTreeNode(current_path);
                            }
                        }
                    });
                }
                break;
        }
        globalTopWindow.removeContextMenu();
        event = event || window.event;
        event.cancelBubble = true;
    }
});