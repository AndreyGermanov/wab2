var Tree = Class.create(Mailbox,{

    initTree: function() {
        var root_id = this.node.id;
        var root_template = $I(root_id.concat('_root_div'));
        var root = root_template.cloneNode(true);
        root.id = root_id.concat("_tree");
        if (!this.loaded && this.entityId=="") {
            root.setAttribute("loaded",false);
        }
        
        var elems = root.getElementsByTagName('*');
        for (var counter=0;counter<elems.length;counter++)
        {
           var elem_id = elems[counter].getAttribute('id');
            if (elem_id!="")
            {
                if (elem_id==root_id.concat("_image")) {
                    elems[counter].id = root.id.concat("_image");
                    elems[counter].setAttribute("object",this.object_id);
                    elems[counter].setAttribute('click',"onObjectClick");
                    elems[counter].setAttribute('mouseover',"onMouseOver");
                    elems[counter].setAttribute('mouseout',"onMouseOut");
                    elems[counter].setAttribute('contextmenu',"rootContextMenu");
                    elems[counter].observe('click',this.addHandler);
                    elems[counter].observe('mouseover',this.addHandler);
                    elems[counter].observe('mouseout',this.addHandler);
                    elems[counter].observe('contextmenu',this.addHandler);                    
                }
                if (elem_id==root_id.concat("_expand")) {
                    elems[counter].id = root.id.concat("_expand");
                    if (!this.loaded) {
                        if (this.entityId=="" || this.entityId==null || typeof this.entityId=="undefined")
                            elems[counter].src = this.skinPath+"images/Tree/plus_expand.gif";
                        elems[counter].setAttribute('click',"rootExpandClick");
                        elems[counter].observe('click',this.addHandler);
                    }
                }
                if (elem_id==root_id.concat("_text")) {
                    elems[counter].id = root.id.concat("_text");
                    elems[counter].setAttribute("object",this.object_id);
                    elems[counter].setAttribute('click',"onObjectClick");
                    elems[counter].setAttribute('mouseover',"onMouseOver");
                    elems[counter].setAttribute('mouseout',"onMouseOut");
                    elems[counter].setAttribute('contextmenu',"rootContextMenu");
                    elems[counter].observe('click',this.addHandler);
                    elems[counter].observe('mouseover',this.addHandler);
                    elems[counter].observe('mouseout',this.addHandler);
                    elems[counter].observe('contextmenu',this.addHandler);
                }
                if (elem_id==root_id.concat("_content"))
                    elems[counter].id = root.id.concat("_content");
                
            }
        }
        root_template.parentNode.appendChild(root);
        root_template.style.display = 'none';
        this.root_node = root;
        return root;
    },

    createTreeNode: function(id,title,image,parent,loaded,subtree) {
        if (parent==null) parent='';
        if (loaded == null)
            loaded = '';
        if (loaded == loaded.toString())
            loaded = loaded.toString().replace("\n","");
        var root_id = this.root_node.id;
        var root = this.root_node;
        var new_node = null;
        var root_template = getElementById(this.node,root.id.replace("_tree","").concat('_root_div'));
        if ($I(getClientId(root.id.concat("_").concat(id)))!=0)
        	return 0;
        if (subtree!="true") {
            new_node = root_template.cloneNode(true);
            new_node.id = getClientId(root.id.concat("_").concat(id));
            
            new_node.setAttribute("target_object",id);
            new_node.removeAttribute("tree_items");
            new_node.setAttribute('loaded',loaded);
            new_node.setAttribute("icon",image);
            new_node.style.backgroundImage="url('"+this.skinPath+"images/Tree/i.gif')";
            new_node.style.backgroundRepeat="repeat-y";
            var elems = new_node.getElementsByTagName('*');
            root_id = root.id.replace("_tree","");
            if (title==null)
                title = "";
            var title_arr = title.split("#");
            var hint = 0;
            if (title_arr.length == 2) {
                title = title_arr[0];
                hint = title_arr[1];
            }
            for (var counter=0;counter<elems.length;counter++) {
            	var elem_id = elems[counter].getAttribute('id');
                if (elem_id!="") {
                        if (elem_id==root_id.concat("_image")) {
                            elems[counter].id = getClientId(new_node.id.concat("_image"));
                            elems[counter].src = image;
                            elems[counter].setAttribute("object",this.object_id);
                            elems[counter].setAttribute('click',"onObjectClick");
                            elems[counter].setAttribute('contextmenu',"onContextMenu");
                            elems[counter].setAttribute('mouseover',"onMouseOver");
                            elems[counter].setAttribute('mouseout',"onMouseOut");
                            elems[counter].observe('click',this.addHandler);
                            elems[counter].observe('contextmenu',this.addHandler);
                            elems[counter].observe('mouseover',this.addHandler);
                            elems[counter].observe('mouseout',this.addHandler);                            
                            elems[counter].setAttribute('target_object',id);
                            elems[counter].setAttribute("icon",image);
                            if (hint!=0)
                                elems[counter].setAttribute('title',hint.replace(/xyxxyx/g,'#'));
                        }
                        if (elem_id==root_id.concat("_expand"))
                        {
                            elems[counter].id = getClientId(new_node.id.concat("_expand"));
                            if (loaded!='false' && loaded!=false) {
                                elems[counter].src = this.skinPath+"images/Tree/leaf_expand.gif";
                            }
                            else {
                                elems[counter].src = this.skinPath+"images/Tree/plus_expand.gif";
                            }
                            elems[counter].setAttribute('click',"onExpandClick");                            
                            elems[counter].observe('click',this.addHandler);
                            elems[counter].setAttribute('target_object',id);
                        }
                        if (elem_id==root_id.concat("_text"))
                        {
                            elems[counter].id = getClientId(new_node.id.concat("_text"));
                            elems[counter].setAttribute("object",this.object_id);
                            elems[counter].innerHTML = title.replace(/xyxxyx/g,'#');
                            elems[counter].setAttribute('click',"onObjectClick");
                            elems[counter].setAttribute('contextmenu',"onContextMenu");
                            elems[counter].setAttribute('mouseover',"onMouseOver");
                            elems[counter].setAttribute('mouseout',"onMouseOut");
                            elems[counter].observe('click',this.addHandler);
                            elems[counter].observe('contextmenu',this.addHandler);
                            elems[counter].observe('mouseover',this.addHandler);
                            elems[counter].observe('mouseout',this.addHandler);                            
                            elems[counter].setAttribute('target_object',id);
                            elems[counter].setAttribute("icon",image);
                            if (hint!=0)
                                elems[counter].setAttribute('title',hint.replace(/xyxxyx/g,'#'));
                        }
                        if (elem_id==root_id.concat("_content"))
                        {
                            elems[counter].id = getClientId(new_node.id.concat("_content"));
                            elems[counter].setAttribute("icon",image);
                        }
                }
            }
            if (parent=='')
            {
                content = getElementById(root,root.id.concat("_content"));
                content.appendChild(new_node);
                content.style.display = '';
            }
            else
            {
                parent = getElementById(root,root.id.concat("_").concat(getClientId(parent)));
                if (parent!=0) {
                    var content = getElementById(parent,parent.id.concat("_content"));
                    var expand = getElementById(parent,parent.id.concat("_expand"));
                    content.appendChild(new_node);
                    var filename = expand.src.split('/').pop();
                    if (filename == "leaf_expand.gif")
                        expand.src = this.skinPath+"images/Tree/plus_expand.gif";
                }

            }
        } else {
            var obj = this;
            var args = new Object;
            args["title"] = title;
            args["parent_object_id"] = this.object_id;            
            args["icon"] = image;
            var params = loaded.split("#");
            if (params.length>0) { 
                for (var ce=0;ce<params.length;ce++) {
                    var parts = params[ce].split('=');
                    if (parts.length==2) {
                    	args[parts[0]] = parts[1];
                    }
                }
            }     
            new_node = null;
            new Ajax.Request("index.php",
            {
                method: "post",
                parameters: {ajax: true, object_id: id,
                             hook: "show", arguments: Object.toJSON(args)},
                onSuccess: function(transport) {
                    var response = transport.responseText;
                    if (response != "")
                    {
                        new_node = document.createElement("div");
                        var response_object = response.evalJSON();
                        new_node.innerHTML = response_object["css"].concat(response_object["html"]);
                        if (parent=='')
                        {
                            var content = getElementById(root,root.id.concat("_content"));
                            content.appendChild(new_node);
                            content.style.display = '';
                        }
                        else
                        {
                            parent = getElementById(root,root.id.concat("_").concat(getClientId(parent)));
                            var content = getElementById(parent,parent.id.concat("_content"));
                            var expand = getElementById(parent,parent.id.concat("_expand"));
                            content.appendChild(new_node);
                            var filename = expand.src.split('/').pop();

                            if (filename == "leaf_expand.gif")
                                expand.src = obj.skinPath+"images/Tree/plus_expand.gif";
                        }
                        eval(response_object["javascript"].replace("<script>","").replace("<\/script>",""));
                        var arr = response_object["args"].toString().split('\n');
                        var args = new Array;
                        for (var counter=0;counter<arr.length;counter++)
                        {
                            arg_parts = arr[counter].split('=');
                            args[arg_parts[0]]=arg_parts[1];
                        }
                    }
                }
            });
            
        }
        if (new_node!=null)
        	new_node.style.display = '';
        return new_node;
    },

    toggleTreeNode: function(id) {
        var root = this.node;
        var img = "";
        if (id==this.root_node.id)
            img = $I(id.concat("_expand"));
        else
            img = $I(root.id+"_tree_"+id.concat("_expand"));
        var img_src = img.src.split("/").pop();
        
        if (img_src == "leaf_expand.gif")
            return 0;
        var doc_content = ""; var doc_expand = "";
        if (id==this.root_node.id) {
            doc_content = $I(id.concat("_content"));
            doc_expand = $I(id.concat("_expand"));
        } else {
            doc_content = getElementById(root,root.id+"_tree_"+id.concat("_content"));
            doc_expand = getElementById(root,root.id+"_tree_"+id.concat("_expand"));
        }
        if (img_src == "plus_expand.gif")
        {
            doc_content.style.display = '';
            doc_expand.src=this.skinPath+"images/Tree/minus_expand.gif";
        }
        else
        {
            doc_content.style.display = 'none';
            doc_expand.src=this.skinPath+"images/Tree/plus_expand.gif";
        }
        return 0;
    },

    addTreeNode: function(id,title,image,parent,loaded,subtree) {
        return this.createTreeNode(id,title,image,parent,loaded,subtree);
    },

    moveTreeNode: function(item,parent,before) {
        var root = this.root_node;     
        item = getElementById(root,getClientId(root.id.concat("_").concat(item)));
        if (item==0)
            return 0;
        if (parent!='' && parent!=-1) {
            parent = getElementById(root,getClientId(root.id.concat("_").concat(parent)));
            if (parent==0)
                return 0;
        }        
        else
            parent = root;
        if (before!="" && before !="alpha" && before != "keyAlpha") {
            before = getElementById(root,getClientId(root.id.concat("_").concat(before)));
            if (before == 0)
                return 0;
        }
        if (item.parentNode!=null)
            old_parent = item.parentNode.parentNode;
        var doc_content = getElementById(parent,parent.id.concat("_content"));
        var doc_expand = getElementById(parent,parent.id.concat("_expand"));
        if (before=="" || before == "alpha" || before=="keyAlpha" || before == null) {
            doc_content.appendChild(item);
        }
        else
            doc_content.insertBefore(item,before);
        if (doc_content.style.display=='none')
            doc_expand.src = this.skinPath+"images/Tree/plus_expand.gif";
        else
            doc_expand.src = this.skinPath+"images/Tree/minus_expand.gif";
        if (old_parent!=null) {
            var content = old_parent.getElementsByTagName("div");
            if (content[0].hasChildNodes()==false) {
                var img=getElementById(old_parent,old_parent.id.concat("_expand"));
                img.src=this.skinPath+"images/Tree/leaf_expand.gif";
            }
        }
        if (before == "alpha" || before == "keyAlpha") {
            var item_text = getElementById(item,item.id.concat("_text"));
            if (before=="alpha")
            	item_text = item_text.innerHTML.toUpperCase();
            if (before=="keyAlpha")
            	item_text = item.id.toUpperCase();
            var moving_node = item;
            var inserted = false;
            while (moving_node != null) {
                if (moving_node.tagName=="DIV") {
                	var subling_text = "";
                	if (before=="alpha") {
                		subling_text = getElementById(moving_node,moving_node.id.concat("_text"));
                		subling_text = subling_text.innerHTML.toUpperCase();
                	} 
                	if (before=="keyAlpha") {
                		subling_text = getElementById(moving_node,moving_node.id.concat("_text"));
                		if (moving_node.id!=null)
                			subling_text = moving_node.id.toUpperCase();
                	} 
                    if (item_text<subling_text) {
                        doc_content.insertBefore(item,moving_node);
                        inserted = true;
                    }
                }
                moving_node = moving_node.previousSibling;
            }
            if (!inserted) {
                doc_content.insertBefore(item,moving_node);
            }
        }
    },

    deleteTreeNode: function(id) {
        id = getClientId(id);
        var old_parent = $I(this.root_node.id+"_"+id).parentNode;
        var child = $I(this.root_node.id+"_"+id);
        old_parent.removeChild(child);        
        if (old_parent.innerHTML=="" && old_parent.parentNode.id!=this.node.id+"_root_div")
        {            
            var parentNode = old_parent.parentNode;
            var img = parentNode.getElementsByTagName("img");
            img[0].src = this.skinPath+"images/Tree/leaf_expand.gif";
        }
    },

    deleteAllNodes: function(id) {
    	var node = null;
        if (id!=null)
        {
            node = $(this.root_node.id+"_"+id+"_content");
            var img = $(this.roont_node.id+"_"+id).getElementsByTagName("img");
            img[0].src=this.skinPath+"images/Tree/leaf_expand.gif";
        }
        else
            node = $I(this.node.id+"_tree_content");      
        node.innerHTML="";
    },

    fillTree: function(item_string)
    {
        if (item_string==null)
            item_string = this.root_node.getAttribute("tree_items");
        if (item_string=="")
            return 0;        
        var items = item_string.split('|');
        for (var counter1=0;counter1<items.length;counter1++)
        {
        	var itm = items[counter1];
            var item = itm.toString().split('~');
            this.addTreeNode(item[0],item[1],item[2],item[3],item[4],item[5]);
        }
    },

    dispatchEvent: function($super,event,params) {
        $super(event,params);
        var cls = "";
        if (event == "ACTIVATE_WINDOW" || event == "DEACTIVATE_WINDOW") {
            if (params["object_id"]!=null) {
                var new_id = params["object_id"];
                var elem1 = getElementById(this.node,getClientId(this.node.id+"_tree_"+new_id)); 
                if (elem1==0) {
                    var arr = params["opener_item"].split("_");
                    arr.pop();
                    var opener_item = arr.join("_");
                    elem1 = getElementById(this.node,getClientId(opener_item)); 
                    new_id = opener_item.replace(this.node.id+"_tree_","");
                }
                if (elem1!=0) {
                    if (event=="ACTIVATE_WINDOW") {
                        cls = "tree_item_selected";
                        this.prevSelectedNode = elem1;
                    }
                    if (event=="DEACTIVATE_WINDOW") {
                        cls = "tree_item";
                    }
                    var txt = getElementById(this.node,getClientId(this.node.id+"_tree_"+new_id+"_text"));
                    var im = getElementById(this.node,getClientId(this.node.id+"_tree_"+new_id+"_image"));
                    if (txt != 0) {
                        txt.setAttribute("class",cls);
                    }
                    if (im != 0) {
                        im.setAttribute("class",cls);
                    }
                }
            }
        }
        if (event == "NODE_CLICKED") {
            if (params["object_id"] == this.object_id) {
                var new_id = params["node_id"];
                var elem1 = getElementById(this.node,this.node.id+"_tree_"+getClientId(new_id));
                if (elem1 != this.prevSelectedNode) {
                    if (elem1!=0) {
                        cls = "tree_item_selected";
                        var txt = getElementById(this.node,elem1.id+"_text");
                        var im = getElementById(this.node,elem1.id+"_image");
                        if (txt != 0) {
                            txt.setAttribute("class",cls);
                        }
                        if (im != 0) {
                            im.setAttribute("class",cls);
                        }
                    }
                    if (this.prevSelectedNode!=null) {
                        cls = "tree_item";
                        var txt = getElementById(this.node,this.prevSelectedNode.id+"_text");
                        var im = getElementById(this.node,this.prevSelectedNode.id+"_image");
                        if (txt != 0) {
                            txt.setAttribute("class",cls);
                        }
                        if (im != 0) {
                            im.setAttribute("class",cls);
                        }
                    }
                    this.prevSelectedNode = elem1;
                }
            }
        }
        if (event == "NODE_CHANGED") {
            var action = params["action"];
            var object_id = getClientId(params["object_id"]);
            var new_object_id = getClientId(params["new_object_id"]);
            var target_id = params["target_id"];
            var text = params["text"];
            var old_text = params["old_text"];
            var title = params["title"];
            var image = params["image"];
            var parent_node = params["parent"];
            var sorting = params["sorting"];
            var sible_id = params["sible_id"];
            var subtree = params["subtree"];
            var loaded = params["loaded"];
            if (loaded == null)
                loaded = true;
            if (sorting==null)
                sorting='alpha';
            var new_text = "";
            var obj = null;
            if (title!=null && subtree!="true")
                new_text = text+"#"+title;
            else
                new_text = text;
            if (action=="add") {
            	if (parent_node=="")
            		obj = $I(this.node.id+"_tree");
            	else
            		obj = getElementById(this.node,this.node.id+"_tree_"+getClientId(parent_node));
                if (parent_node == this.root_node.id)
                    parent_node = "";
                if (parent_node!="")
                    if (obj==0)
                        return 0;
                this.addTreeNode(target_id,
                                 new_text,
                                 image,
                                 parent_node,loaded,subtree);
                if (sorting=='none') {
                    this.moveTreeNode(target_id,parent_node,'');
                } else if (sorting=="keyAlpha"){
                    this.moveTreeNode(target_id,parent_node,'keyAlpha');                    
                } else {
                    this.moveTreeNode(target_id,parent_node,'alpha');                                    	
                }
            }
            if (action=="change") {
                var image_changed = false;
                var obj = getElementById(this.node,this.node.id+"_tree_"+object_id);
                if (obj==0) {
                    if (this.target_object==object_id) {
                        obj = this.node;
                        this.node.setAttribute("target_object",object_id);
                    }
                }
                if (obj!=0) {
                    var old_target_id = obj.getAttribute("target_object");
                    var parent1 = obj;
                    parent1.id = parent1.id.replace(object_id,new_object_id);
                    parent1.setAttribute("target_object",parent1.getAttribute("target_object").replace(old_target_id,target_id));
                    var elems = parent1.getElementsByTagName('*');                    
                    for (var counter=0;counter<elems.length;counter++) {
                        if (elems[counter].id!=null)
                        {
                            var elem_end = elems[counter].id.split("_").pop();
                            if (elem_end == "text") {
                                elems[counter].innerHTML = elems[counter].innerHTML.replace(old_text,text);
                            }
                            
                            if (title!=null && title!="")
                                elems[counter].setAttribute("title",title);
                                
                            if (elem_end == "image" && image!="" && image_changed==false) {
                                elems[counter].src = image;
                                image_changed = true;
                            }
                            elems[counter].id = elems[counter].id.replace(object_id,new_object_id);
                            if (elems[counter].getAttribute("target_object")!=null)
                                elems[counter].setAttribute("target_object",elems[counter].getAttribute("target_object").replace(old_target_id,target_id));
                        }
                    }
                    if (sorting=='alpha')
                        this.moveTreeNode(target_id,parent_node,"alpha");
                    else if (sorting=="keyAlpha")
                        this.moveTreeNode(target_id,parent_node,"keyAlpha");
                    else if (sorting!='none')
                        this.moveTreeNode(target_id,parent_node,'');
                }
            }
            if (action=="delete") {
                var obj = 0;
                if (object_id==this.object_id) {
                    this.node.parentNode.removeChild(this.node);
                    this.raiseEvent("DESTROY",$Arr("object_id="+this.object_id));
                    delete objects.objects[this.object_id];
                } else 
                    obj = getElementById(this.node,this.node.id+"_tree_"+object_id);
                if (obj!=0) {
                    var wm = getWindowManager();
                    if (wm.windows[object_id.replace(/_/g,"")]!=null)
                        wm.remove_window(object_id.replace(/_/g,""));
                    var elems = obj.getElementsByTagName("*");
                    for (var counter=0;counter<elems.length;counter++)
                    {
                        var target_object = elems[counter].getAttribute("target_object");
                        var a=null;
                        for (a in wm.windows) {
                            if (a=="each") break;
                            if (typeof(wm.windows[a])!="undefined")
                                if (wm.windows[a].php_object_id==target_object)
                                    wm.remove_window(a,'',true);
                        }
                    }
                    this.deleteTreeNode(object_id);
                }
            }
            if (action=="move") {
                this.moveTreeNode(target_id,
                                  parent_node,sible_id);
            }
            if (action=="remove") {
                this.deleteTreeNode(object_id);            	
            }
        }
    },

    rootExpandClick: function(event) {
        var elem = eventTarget(event);
        if (elem.getAttribute("disable")=="true")
            return 0;
        var elem_arr = elem.id.split('_');
        elem_arr.pop();
        var root_elem = elem_arr.join('_');                    
        this.toggleTreeNode(root_elem);
    }
});