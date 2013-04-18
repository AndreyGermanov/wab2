var EntityTree = Class.create(Tree, {
    
    fillTree: function($super,items) {
        $super(items);
        if ($I(this.node.id+"_tree_"+getClientId(this.entityId)+"_image")!=0) {
            $I(this.node.id+"_tree_"+getClientId(this.entityId)+"_image").setAttribute("class","tree_item_selected");
            $I(this.node.id+"_tree_"+getClientId(this.entityId)+"_text").setAttribute("class","tree_item_selected");
        }                                
    },

    onExpandClick: function(event) {
        var elem = eventTarget(event);
        if (elem.getAttribute("disable")=="true")
            return 0;
        var elem_arr = elem.id.split('_');
        elem_arr.pop();
        var root_elem = elem_arr.join('_');
        var elem_id = root_elem.replace(this.node.id+'_tree_','');
        elem_arr = elem_id.split("_");
        elem_start = elem_arr.shift();
        var tree = this;
        var args = new Object;
        args["className"] = tree.className;
        tree.condition = tree.condition.replace(/AND  AND/g,"AND");
        tree.condition = tree.condition.replace(/AND AND/g,"AND");
        args["condition"] = tree.condition;
        args["condition"] = args.condition.replace("AND @parent IS NOT EXISTS","");
        args["condition"] = args.condition.replace("AND @parent IS NOT EXISTS","");
        args["sortOrder"] = tree.sortOrder;
        args["childClassName"] = tree.childClassName;
        if (this.adapterId!="")
        	args["adapterId"] = tree.adapterId;
        args["childCondition"] = tree.childCondition;
        args["entityImage"] = tree.entityImage;
        args["selectGroup"] = tree.selectGroup;
        args["groupEntityImage"] = tree.groupEntityImage;
        args["titleField"] = tree.titleField;
        args["additionalFields"] = tree.additionalFields;
        args["contextMenuId"] = tree.contextMenuId;
        args["editorType"] = tree.editorType;
        args["windowWidth"] = tree.windowWidth;
        args["windowHeight"] = tree.windowHeight;
        args["windowTitle"] = tree.windowTitle;
        args["divName"] = tree.divName;
        args["destroyDiv"] = tree.destroyDiv;
        args["tableId"] = tree.tableId;
        args["elem_id"] = elem_id;

        if ($I(root_elem).getAttribute("loaded")=="false")
        {
            elem.setAttribute("disable","true");
            elem_arr = elem.id.split('_');
            elem_arr.pop();
            root_elem = elem_arr.join('_');
            $I(root_elem.concat("_image")).setAttribute("old_src",$I(root_elem.concat("_image")).src);
            $I(root_elem.concat("_image")).src = this.skinPath+'/images/Tree/loading.gif';
            $I(root_elem.concat("_content")).innerHTML = '';
            new Ajax.Request("index.php",
                {
                    method: "post",
                    parameters: {ajax: true, object_id: tree.object_id, hook: '3', arguments: Object.toJSON(args)},
                    onSuccess: function(transport) {
                        var response = transport.responseText;
                        console.log(response);
                        tree.fillTree(response);
                        $(root_elem).setAttribute("loaded","true");
                        elem.setAttribute("disable","false");
                        $I(root_elem.concat("_image")).src = $I(root_elem.concat("_image")).getAttribute("old_src");
                    }
                });
        }
        this.toggleTreeNode(elem_id);
    },
    
    rootExpandClick: function(event) {
        var elem = eventTarget(event);
        if (elem.getAttribute("disable")=="true")
            return 0;
        var elem_arr = elem.id.split('_');
        elem_arr.pop();
        var root_elem = elem_arr.join('_');
        var tree = this;
        var args = new Object;
        args["className"] = tree.className;
        args["condition"] = tree.condition;
        args["hierarchy"] = tree.hierarchy;
        args["sortOrder"] = tree.sortOrder;
        args["childClassName"] = tree.childClassName;
        args["childCondition"] = tree.childCondition;
        args["entityImage"] = tree.entityImage;
        args["groupEntityImage"] = tree.groupEntityImage;
        args["titleField"] = tree.titleField;
        args["additionalFields"] = tree.additionalFields;
        args["contextMenuId"] = tree.contextMenuId;
        args["editorType"] = tree.editorType;
        args["selectGroup"] = tree.selectGroup;
        args["forEntitySelect"] = tree.forEntitySelect;
        args["windowWidth"] = tree.windowWidth;
        args["windowHeight"] = tree.windowHeight;
        args["windowTitle"] = tree.windowTitle;
        args["divName"] = tree.divName;
        args["destroyDiv"] = tree.destroyDiv;
        args["tableId"] = tree.tableId;
        if (tree.adapterId!=null)
        	args["adapterId"] = tree.adapterId;
        
        if ($I(root_elem).getAttribute("loaded")=="false")
        {
            elem.setAttribute("disable","true");
            elem_arr = elem.id.split('_');
            elem_arr.pop();
            root_elem = elem_arr.join('_');
            $I(root_elem.concat("_image")).setAttribute("old_src",$I(root_elem.concat("_image")).src);
            $I(root_elem.concat("_image")).src = this.skinPath+'/images/Tree/loading.gif';
            $I(root_elem.concat("_content")).innerHTML = '';
            new Ajax.Request("index.php",
                {
                    method: "post",
                    parameters: {ajax: true, object_id: tree.object_id,hook:'3',arguments: Object.toJSON(args)},
                    onSuccess: function(transport) {
                        var response = transport.responseText;
                        if (response!="")
                            tree.fillTree(response);
                        $(root_elem).setAttribute("loaded","true");
                        elem.setAttribute("disable","false");
                        $I(root_elem.concat("_image")).src = $I(root_elem.concat("_image")).getAttribute("old_src");
                    }
                });
        }
            
        this.toggleTreeNode(root_elem);
    },    

    onMouseOver: function(event) {
        var elem = eventTarget(event);
        var par = elem.parentNode;
        par_id = par.id.split("_");
        par_id = par_id.join("_");

        if ($I(par_id+"_text").getAttribute("class")=="tree_item_selected") {
            return 0;
        }
        var elem_id = elem.getAttribute("target_object");
        if (elem_id==null)
            elem_id = "";
        var elem_end = elem_id.split("_").pop();
        if (elem_end == "HIDED")
            return 0;
        if (this.editorType == "entityDataTable") {
        	var node_image="";
        	var group_image="";
        	var hover=false;
                if (elem_id!="")
                    node_image = $I(this.node.id+"_tree_"+getClientId(elem_id)+"_image").src.split("/").pop();
                else
                    node_image = $I(this.node.id+"_tree_"+getClientId(elem_id)+"image").src.split("/").pop();
                if (this.groupEntityImage!=null)
                	group_image = this.groupEntityImage.split("/").pop();
            if (typeof(group_image)!="undefined" && node_image == group_image)
                hover = true;
            else
                hover = false;
            if (this.forEntitySelect)
                hover = true;
        } else
            hover = true;
        if (hover) {
            var elems=elem.parentNode.getElementsByTagName("*");
            var el=0;
            for (el=0;el<elems.length;el++) {
                if (elems[el].parentNode != elem.parentNode)
                    continue;
                if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
                    elems[el].setAttribute("class","tree_item_hover");
            }
        }
    },

    onMouseOut: function(event) {
        var elem = eventTarget(event);
        var par = elem.parentNode;
        var par_id = par.id.split("_");
        par_id = par_id.join("_");

        if ($I(par_id+"_text").getAttribute("class")=="tree_item_selected") {
            return 0;
        }
        var elem_id = elem.getAttribute("target_object");
        if (elem_id==null)
            elem_id = "";
        var elem_end = elem_id.split("_").pop();
        if (elem_end == "HIDED")
            return 0;
        var elems=elem.parentNode.getElementsByTagName("*");       
        for (var el=0;el<elems.length;el++) {
            if (elems[el].parentNode !== elem.parentNode)
                continue;
            if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
                elems[el].setAttribute("class","tree_item");
        }
    },

    onObjectClick: function(event) {
        var elem = eventTarget(event);
        var elem_id = elem.getAttribute("target_object");
        var elem_class = elem.getAttribute("object").split("_").pop();
        var elemIcon = elem.getAttribute("icon");
        var elem_end = "";
        if (elem_id==null) {
        	elem_id = "";
        } else {
        	var elem_start = elem_id.split('_').shift();
        	elem_end = elem_id.split("_").pop();
        }
        if (elem_end == "HIDED")
            return 0;
        if (this.forEntitySelect && this.editorType!="entityDataTable") {
        	if (this.selectGroup!="1" && elemIcon==this.groupEntityImage)
        		return 0;
            this.raiseEvent("NODE_CLICKED",$Arr("object_id="+this.object_id+",node_id="+elem_id+",parent_object_id="+this.parent_object_id+",result_object_id="+this.result_object_id));            
            return 0;
        }            
        if (this.editorType == "entityDataTable") {
            if (elem_id==null || elem_id=="") {
            	elem_id = elem.getAttribute("object");
            	elem_start = elem_id.split("_").pop();
            	elem_end = "";
            } else {
            	elem_start = elem_id.split('_').shift();
            	elem_end = elem_id.split("_").pop();
            }
            var dt = $O(this.tableId);
            if (this.className==elem_start)
            	dt.className = elem_start;
            dt.currentPage = 1;
            var item = $O($O($O($O(this.result_object_id).parent_object_id).parent_object_id).parent_object_id,'');
            if (item==null || item.condition==null) {
            	item = new Object;
            	item.condition = "@parent IS NOT EXISTS";
            }    
            if (elem_end!="") {
            	item.condition = item.condition.replace("@parent IS NOT EXISTS","");
            	if (item.condition!="")
            		dt.condition = item.condition+" AND @parent.@name="+elem_end;
            	else
            		dt.condition = "@parent.@name="+elem_end;
                dt.parentEntity = elem_id;
            }
            else {
            	if (trim(item.condition)!="")
            		dt.condition = item.condition+" AND @parent IS NOT EXISTS";
            	else
            		dt.condition = "@parent IS NOT EXISTS"; 
            }
            if (dt.condition=="@parent IS NOT EXISTS AND @parent IS NOT EXISTS")
            	dt.condition = "@parent IS NOT EXISTS";
            dt.condition = dt.condition.replace(/AND  AND/g,"AND");
            dt.condition = dt.condition.replace(/AND AND/g,"AND");
            dt.rebuild();
            dt.selectCurrentEntity();
            $I(this.tableId+"_innerFrame").src = $I(this.tableId+"_innerFrame").src;
            this.raiseEvent("NODE_CLICKED",$Arr("object_id="+this.object_id+",node_id="+elem_id+",parent_object_id="+this.parent_object_id+",result_object_id="+this.result_object_id));            
            removeContextMenu();
        } else if (this.editorType == "window") {
          if (elem_id=="")
              return 0;
          var str = "";
          str += "index.php?object_id="+elem_id+"&hook=show";
          var args = "modal";
          var leftPosition = (screen.availWidth-this.windowWidth)/2;
          var topPosition = (screen.availHeight-this.windowHeight)/2;
          var options = "dialogWidth:"+this.windowWidth+"px; dialogHeight:"+this.windowHeight+"px; dialogLeft:"+leftPosition+"px; dialogTop:"+topPosition+"px;";
          this.raiseEvent("NODE_CLICKED",$Arr("object_id="+this.object_id+",node_id="+elem_id));
          this.selectValueWindow = window.showModalDialog(str,args,options);
        } else if (this.editorType == "div") {
            if (elem_id=="")
              return 0;            
            var div = $I(this.divName);
            new Ajax.Request("index.php",
            {
                method: "post",
                parameters: {ajax: true, object_id: elem_id,hook: 'show'},
                onSuccess: function(transport) {
                    var response = transport.responseText;
                    if (response != "")
                    {
                        var response_object = response.evalJSON();
                        var divs = div.getElementsByTagName("DIV");
                        if (divs[0]!=null) {
                            var object = $O(divs[0].getAttribute("object"),'');
                            object.raiseEvent("DESTROY",$Arr("object_id="+object.object_id));
                            delete objects.objects[object.object_id];                            
                        }
                        div.innerHTML = response_object["css"].concat(response_object["html"]);
                        eval(response_object["javascript"].replace("<script>","").replace("<\/script>",""));
                        var arr = response_object["args"].toString().split('\n');
                        var args = new Array;
                        for (var counter=0;counter<arr.length;counter++)
                        {
                            var arg_parts = arr[counter].split('=');
                            args[arg_parts[0]]=arg_parts[1];
                        }
              //          this.raiseEvent("NODE_CLICKED",$Arr("object_id="+this.object_id+",node_id="+elem_id));
                    }
                }
            });
        } else if (this.editorType=="WABWindow") {
            var elem = eventTarget(event);
            elem_id = elem.getAttribute("target_object");            
            if (elem_id=="" || elem_id==null)
                return 0;
            var elem_end = elem_id.split("_").pop();
            var icon = $I(this.node.id+"_tree_"+getClientId(elem_id)+"_image").src;
            if (icon!=null) {
                var icon_arr = icon.split('/');
                icon_arr.shift();
                icon_arr.shift();
                icon_arr.shift();
                icon = icon_arr.join('/');
            }
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,"");
            if (elem_end == "HIDED")
                return 0;
            var params = new Object;
            params["hook"] = "admTpl";  
			getWindowManager().show_window(window_elem_id,elem_id,params,this.module_id,elem.id);
        } else if (this.editorType=="none" || this.editorType==null || this.editorType=="") {
            this.raiseEvent("NODE_CLICKED",$Arr("object_id="+this.object_id+",node_class="+elem_class+",node_id="+elem_id+",parent_object_id="+this.parent_object_id+",result_object_id="+this.result_object_id));            
        }
    },
    
    onContextMenu: function(event) {
        var elem = eventTarget(event);
        event = event || window.event;
        event.cancelBubble = true;

        var objectid = elem.getAttribute("object");
        var instanceid = elem.getAttribute("instance");
        $O(objectid,instanceid).show_context_menu(this.contextMenuClass+"_elem",cursorPos(event).x-10,cursorPos(event).y-10,elem.id);

         if (event.preventDefault)
            event.preventDefault();
         else
            event.returnValue= false;
        return false;
    },   

    rootContextMenu: function(event) {
        var elem = eventTarget(event);
        event = event || window.event;
        event.cancelBubble = true;

        var objectid = elem.getAttribute("object");
        var instanceid = elem.getAttribute("instance");        
        if (!this.hide_root_context_menu)
            $O(objectid,instanceid).show_context_menu(this.rootContextMenuClass+"_elem",cursorPos(event).x-10,cursorPos(event).y-10,elem.id);

         if (event.preventDefault)
            event.preventDefault();
         else
            event.returnValue= false;
        return false;
    },   

    selectCurrentEntity: function() {
        var parentArr = this.entityParentStr.split(",");
        var root_event = new Array;
        root_event.target = $I(this.node.id+"_tree_expand");
        for (var ce=0;ce<parentArr.length;ce++) {
            var event = new Array;
            event["target"] = $I(this.node.id+"_tree_"+getClientId(parentArr[ce])+"_expand");
            if (event["target"]!=0)
                this.onExpandClick(event);
        }
        if ($I(this.node.id+"_tree_"+getClientId(this.entityId)+"_image")!=0) {
            $I(this.node.id+"_tree_"+getClientId(this.entityId)+"_image").setAttribute("class","tree_item_selected");
            $I(this.node.id+"_tree_"+getClientId(this.entityId)+"_text").setAttribute("class","tree_item_selected");
            this.prevSelectedNode = $I(this.node.id+"_tree_"+getClientId(parentArr[parentArr.length-1]));
            $I(this.node.id+"_tree_"+getClientId(this.entityId)+"_image").focus();
            $I(this.node.id+"_tree_"+getClientId(this.entityId)+"_image").scrollIntoView(true);
            var el = $I(this.node.id+"_tree_"+getClientId(this.entityId)+"_image");
            window.scrollTo(el.offsetLeft,el.offsetTop);
        }
    },
    
    dispatchEvent: function($super,event,params) {
        $super(event,params);
        if (event=="ENTITY_CHANGED" || event=="ENTITY_ADDED") {
            var classname = params["object_id"].split("_").shift();
            var reg=null;
            if (this.classNameForReg!=null)
                reg = new RegExp(this.classNameForReg.replace(/\*/g,".*"));
            if (reg!=null && (classname.search(reg)!=-1 || params["object_id"] == this.target_object || classname == this.treeClassName )) {
                if (params["title"] != params["old_title"] || params["action"] == "move" || params["new_class"]!=null || params["parent"]!=params["new_parent"] || params["old_parent"]!=params["parent"]) {
                    params["text"] = params["title"];
                    params["old_text"] = params["old_title"];
                    if (params["new_class"]==null)
                        params["new_object_id"] = params["object_id"];
                    params['title'] = '';
                    params['old_title'] = '';
                    if (params["new_class"]==null)
                        params["target_id"] = params["object_id"];
                    if (event=="ENTITY_CHANGED" && params["action"]==null)
                        params["action"] = "change";
                    else if (params["action"]==null)
                        params["action"] = "add";
                    if (params['old_parent']==null)
                        params['old_parent'] = "";
                    if (params['old_parent']=="-1")
                        params['old_parent'] = "";
                    
                    if (params['parent']=="-1")
                        params["parent"] = '';
                    if (params['parent']==null)
                        params['parent'] = "";

                    if (params['old_parent']!=params['parent'])                        
                        params['sorting'] = '';
                    else
                        params['sorting'] = 'none';
                    if (params["action"]=="add") {
                        if (params["parent"]=="") {
                            params["parent"] = this.root_node.id;
                        }                               
                    }
                    this.raiseEvent("NODE_CHANGED",params,true);
                    
                    if (params["old_name"] == "" || (params["parent"]!=params["old_parent"] && params["parent"]!="")) {
                        $I(this.node.id+"_tree_"+getClientId(params["parent"])+"_image").src = this.groupEntityImage;
                    }
                    if (params["old_parent"]!="-1" && params["old_parent"]!="") {
                        if ($I(this.node.id+"_tree_"+getClientId(params["old_parent"])+"_content").innerHTML=="")
                            $I(this.node.id+"_tree_"+getClientId(params["old_parent"])+"_image").src = this.entityImage;                        
                    }
                }
            }
        }
        if (event=="ENTITY_DELETED") {
            var obj_ids = params["object_id"];
            var object_ids = params["object_id"].split("~");
            var obj_id = "";
            var o=null;
            for (o in object_ids) {
                if (typeof object_ids[o] != "string")
                    return 0;
                obj_id = object_ids[o];
                var classname = obj_id.split("_").shift();
                var reg=null;
                if (this.classNameForReg!=null)
                    reg = new RegExp(this.classNameForReg.replace(/\*/g,'.*'));                
                if ((reg!=null && classname.search(reg)!=-1) || obj_id == this.object_id) {
                    params["object_id"] = obj_id;
                    this.raiseEvent("NODE_CHANGED",params,true);
                    params["object_id"] = obj_ids;
                }
            }
        }
    }
});