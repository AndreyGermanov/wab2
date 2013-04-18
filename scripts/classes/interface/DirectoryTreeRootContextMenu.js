var DirectoryTreeRootContextMenu = Class.create(ContextMenu,{
    onClick: function(event) {
        var dirtree = this;
        var elem = eventTarget(event);
        var elem_id = this.opener_item.id;
        var elemid = getClientId(elem_id);
        var elem_arr = elemid.split("_");
        elem_arr.pop();
        var current_path = dirtree.opener_object.rootPath;
        var module_id = this.opener_object.module_id;
        switch(get_elem_id(elem).replace("_text","")) {
            case "add":
                var new_folder_name = prompt("Введите имя папки");
                if (new_folder_name!=null) {
					var args = new Object;
					args["folder"] = current_path+"/"+new_folder_name;
                    new Ajax.Request("index.php", {
                        method: "post",
                        parameters: {ajax: true, object_id: "DirectoryTree_"+module_id+"_Dirs",
                                     hook: '4', arguments: Object.toJSON(args)},
                        onSuccess: function(transport) {
                            var response = trim(transport.responseText).replace("\n","");
                            if (response.length>1) {
                                var response_object = response.evalJSON();
                                if (response_object["error"]!=null)
                                    dirtree.reportMessage(response_object["error"],"error",true);
                                else
                                    dirtree(response,"error",true);
                            }
                            else {
                                dirtree.opener_object.addTreeNode(dirtree.opener_object.root_dir+"/"+new_folder_name,
                                                           new_folder_name,
                                                           dirtree.opener_object.skinPath+"images/Tree/folder.png",
                                                           '',true);
                            }
                        }
                    });
            }
            break;
            case "addfile":
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
                                dirtree.opener_object.addTreeNode(dirtree.opener_object.root_dir+"/"+new_folder_name,
                                                           new_folder_name,
                                                           dirtree.opener_object.skinPath+"images/Tree/file.png",
                                                           '',true);
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