var Entity = Class.create({

    getClassName: function() {
        return "Entity";
    },
    
    initialize: function(object_id,instance_id) {       
        this.object_id = object_id;
        this.js_object_id = getClientId(object_id);
        this.instance_id = instance_id;
        this.full_object_id = object_id;
        this.barCodeClasses = new Array;
        if (this.instance_id!="")
        this.full_object_id = this.full_object_id+"_"+this.instance_id;
        this.preInit();
        this.onLoadTemplate();
        this.postInit();
    },

    preInit: function() {

    },

    postInit: function() {
        this.changed_items = new Array;
        this.win = "";
    },

    getValues: function(opCode) {
        var ids = new Array;
        var ids_obj = "";
	    var value="";
        if (opCode!=null) {
        	ids_obj = getObjectData(this);        	
        }
        if (opCode=='loaded')
        	return ids_obj;
               
        for (o in ids_obj) {
        	if (typeof ids_obj[o] != "function")
        		ids[o] = ids_obj[o];
        }
        
        var elems = this.node.getElementsByTagName('*');
        var counter=0;
        for (counter=0;counter<elems.length;counter++) {
           if (elems[counter].value == null)
               continue;
           var attr=null;
           if (elems[counter].getAttribute("control")=="yes") {
                attr = elems[counter].getAttribute("object");
           } else
                attr = elems[counter].id;
           if (elems[counter].tagName!="TEXTAREA") {
                if (elems[counter].getAttribute("control")=="yes") {
                    value = $O(elems[counter].getAttribute("object"),'').getValue();
                }
                else {
                    value = elems[counter].value;
                }
           }
           else {
                if (elems[counter].getAttribute("control")=="yes") {
                    value = $O(elems[counter].getAttribute("object"),'').getValue();
                }
                else
                    value = elems[counter].value;
           }
           var type = elems[counter].getAttribute("type");
           if (value!=null && attr!=null) {
               attr = attr.replace(this.node.id+"_","");
               if (type=="checkbox" && elems[counter].checked==false) {
                   ids[attr] = "";
               }
               else {
                   ids[attr] = value;
               }
           }
        }
        return ids;
    },

    onMouseDown: function(event) {
        var elem = eventTarget(event);
        var objectid = elem.getAttribute("object");
        var instanceid = elem.getAttribute("instance");
        if (instanceid==null)
            instanceid="";
        var mbox = $O(objectid,instanceid);
        if ($O(objectid,instanceid)==null)
            return 0;
        var wm = getWindowManager();
        if (mbox.win!="" && mbox.win!=null && wm!=null)
        {
            if (mbox.win.object_id != "ApplicationNew")
            {
            	var active_window = "";
            	if (wm.active_elem!=null)
            		active_window = wm.active_elem.getAttribute("object");
            	if (active_window!=mbox.win.id)
            		wm.activate_window(mbox.win.id);
                wm.moving_elem = mbox.win;                
            }
        }
    },

    onChange: function(event) {
        var elem = eventTarget(event);
        if ($I(elem.id)==0)
			return 0;
        if ($O(elem.getAttribute("object"),elem.getAttribute("instance"))==null)
            return 0;
        var obj = $O(elem.getAttribute("object"),elem.getAttribute("instance"));
        if ($O(elem.getAttribute("object"),elem.getAttribute("instance")).parent_object_id.split("_").shift()=="EntityDataTable")
            return 0;
        var objectid = elem.getAttribute("object");
        var instanceid = elem.getAttribute("instance");
        if ($O(objectid,instanceid)==null)
            return 0;
        var instarr = elem.id.split("_");
        var changed_item = instarr.pop();
        if ($O(objectid,instanceid).changed_items!=null)
            $O(objectid,instanceid).changed_items[changed_item] = true;

        if (obj.win!="" && obj.win != null && obj.win.node != null)
        {           
            var node1 = obj.win.node;            
            if (node1.getAttribute("ignoreChanging")=="true") {
                return 0;
            }
            if (obj.win.ignoreChanging)
                return 0;
            if (node1!=null) {
                var attr = node1.getAttribute('changed');
                if (attr == null || attr != "true")
                {
                    node1.setAttribute("changed","true");
                    node1 = getElementById(node1,node1.id+"_headertext");
                    if (node1.innerHTML != null)
                        node1.innerHTML = node1.innerHTML.concat('*');
                }
            }
        }
        if (obj.node!=null) {
            obj.node.setAttribute("changed","true");
        }
    },

    cancel_onClick: function(event) {
        getWindowManager().remove_window(this.win.id,this.instance_id);
    },

    fillSelects: function() {        
        this.document = document;
        if (this.instance_id!="") objectid = getClientId(this.object_id.concat("_").concat(this.instance_id));
        else objectid = getClientId(this.object_id);
        
        var elements = new Array;
        if ($(objectid)!=null)
            elements = $(objectid).getElementsByTagName("select");
        else if ($I(objectid)!=0)
            elements = $I(objectid).getElementsByTagName("select");
        else
            return 0;
        var i=0;
        for (i=0;i<elements.length;i++)
        {
            var values = elements[i].getAttribute("collection");
            if (values!=null && values!="|")
            {
                values = values.split("|");
                if (values[1]!=null)
                {
                    var value_texts = values[1].split(",");
                    values = values[0].split(",");
                    var value = elements[i].getAttribute("value");
                    var i1=0;
                    for (i1=0;i1<values.length;i1++)
                    {
                        if (values[i1] == value)
                            selected = true;
                        else
                            selected = false;
                        elements[i].options[elements[i].length] = new Option(value_texts[i1],values[i1],selected,selected);
                    }
                }
            }
        }
    },

    changeIds: function() {
        if ($I(getClientId(this.full_object_id))==0)
            this.node = document.getElementById(getClientId(this.full_object_id));
        else
            this.node = $I(getClientId(this.full_object_id));
        if (this.instance_id!="") objectid = this.object_id.concat("_").concat(this.instance_id);
        else objectid = this.object_id;
        if ($(getClientId(objectid))!=null) object=$(getClientId(objectid)); else object=$I(getClientId(objectid));
        if (object==0)
            return 0;
        var elements = object.getElementsByTagName("*");
        var counter=0;
        for (counter=0;counter<elements.length;counter++)
        {
            var el = elements[counter].parentNode;
            var fnd = false;
            while (true) {
                if ((el.tagName=="CONTROL" && el.getAttribute("type")=="text" && el.getAttribute("control_type")!=null) || (el.tagName=="TEXTAREA" && el.getAttribute("control_type")!=null)) {                    	
                    fnd = true;
                    break;
                }
                if (el.parentNode==null)
                    break;
                el = el.parentNode;                
            }            
            if (fnd)
                continue;
            var objid = elements[counter].getAttribute("object");
            if (objid==null)
            {
                var full_id = this.object_id;
                elements[counter].setAttribute("object", this.object_id);
                if (this.instance_id!="")
                {
                    elements[counter].setAttribute("instance", this.instance_id);
                    full_id = full_id+"_"+this.instance_id;
                }
                var attr = elements[counter].getAttribute("id");
                var yes_focus = false;
                if (attr!=null && attr!=0) {
                    full_id = full_id +"_"+ attr;
                    elements[counter].setAttribute("id",getClientId(full_id));
                    var a=0;
                    for (a in this)
                    {
                    	var counter1=0;
                        for (counter1=0;counter1<event_types.length;counter1++)
                        {
                            var e = event_types[counter1];
                            if (a==attr+"_"+e)
                            {
                                if (elements[counter].getAttribute(e.toLowerCase()) == null)
                                {
                                    if (e.toLowerCase()=="onfocus")
                                        yes_focus = true; 
                                    	var event = new Array;
                                    	event["target"] = elements[counter];
                                    	event["func"] = a;
//                                    	if (elements[counter]["addEventListener"]!=null)
  //                                  		elements[counter].addEventListener(e.toLowerCase().replace(/on/,""),this[a]);
    //                                	else
      //                              		elements[counter].attachEvent(e.toLowerCase(),this[a]);
                                    	elements[counter].observe(e.toLowerCase().replace("on",""),this.fireEvent);
                                    	//elements[counter].setAttribute(e.toLowerCase(),"$O('"+this.object_id+"','"+this.instance_id+"')."+a+"(event)");
                                }
                            }
                        }
                    }
                }
                attr = elements[counter].getAttribute("name");
                if (attr!=null && elements[counter].tagName!="IFRAME")
                {
                    full_id = this.object_id +"_"+ attr;                    
                    var arr = full_id.split("_");
                    if (arr.length>3) {
                        this.module_id = arr[1]+"_"+arr[2];
                        full_id = full_id.replace(this.module_id+"_","");
                    };
                    elements[counter].setAttribute("name",getClientId(full_id));
                }
                if (!yes_focus)
                	elements[counter].observe("focus",this.fireEvent);
                    //elements[counter].setAttribute("onfocus","$O('"+this.object_id+"','"+this.instance_id+"').on_focus(event)");
            }
            else {
                if (elements[counter].getAttribute("id")!=null)
                    elements[counter].setAttribute("id",getClientId(elements[counter].getAttribute("id")));
            }
        }        
    },
    
    fireEvent: function(event) {
    	var element = eventTarget(event);
    	var object_id = element.getAttribute("object");
    	var instance_id = element.getAttribute("instance");
    	if (instance_id==null)
    		instance_id="";
    	if ($O(object_id,instance_id)!=null) {
    		if ($O(object_id,instance_id)[element.id.replace(object_id+"_","")+"_"+event_hashes["on"+event.type]]!=null)
    			$O(object_id,instance_id)[element.id.replace(object_id+"_","")+"_"+event_hashes["on"+event.type]](event);
    	}
    },
    
    addHandler: function(event) {
    	var element = eventTarget(event);
    	var object_id = element.getAttribute("object");      	
    	var instance_id = element.getAttribute("instance");    	
    	if (instance_id==null)
    		instance_id="";
    	if ($O(object_id,instance_id)!=null) {
    		if ($O(object_id,instance_id)[element.getAttribute(event.type)]!=null)
    			$O(object_id,instance_id)[element.getAttribute(event.type)](event);
    		else if ($O(element.id,instance_id)!=null)
    			$O(element.id,instance_id)[element.getAttribute(event.type)](event);
    	}
    },    
    
    keyUp: function(event) {
        var code = event.charCode || event.keyCode;
        if (code==27 && !event.altKey && !event.ctrlKey)
        	this.cancel_onClick(event);
        
        if (code==13 && event.ctrlKey)
        	if (window.document.forms[0]!=undefined)
        		window.document.forms[0].submit();
    },
    
    buildControls: function() {
        if (this.instance_id!="") objectid = this.object_id.concat("_").concat(this.instance_id);
        else objectid = this.object_id;
        if ($(getClientId(objectid))!=null) object=$(getClientId(objectid)); else object=$I(getClientId(objectid));
        var elems = object.getElementsByTagName('CONTROL');
        var tw = globalTopWindow;
        var c1=0;
        var focused = false;
        for (c1=0;c1<elems.length;c1++) {
            var objectid = elems[c1].id;
            if (elems[c1].id!=null) {
                var field_id = elems[c1].id.split("_").pop();
                var accessLevel='write';
                var fieldAccess = "";
                if (this.fieldAccess!=null && this.fieldAccess != "") {
                	fieldAccess = this.fieldAccess;
                } else {
                	if (this.role!=null && this.role["fieldAccess"]!=null)
                		fieldAccess = this.role["fieldAccess"];
                }
                	
                if (fieldAccess!=null && fieldAccess!="") {
	                if (fieldAccess[field_id]!=null) {
	                	accessLevel = fieldAccess[field_id];
	                }
	                else {
	                	if (fieldAccess["*"]!=null)
	                		accessLevel = fieldAccess["*"];
	                }
                }
                if (this.readOnly=="true")
                	accessLevel = 'read';
                if (accessLevel=='read') {
                	elems[c1].setAttribute("readonly","true");
                }
                if (accessLevel=="none") {
                	elems[c1].setAttribute("readonly","true");
                	elems[c1].setAttribute("invisible","true");
                }
            }
            tw.objects.add(new InputControl(objectid,''));
            var ctl = $O(objectid,'');
            ctl.win = this.win;
            ctl.module_id = this.module_id;
            ctl.parent_object_id = this.object_id;
            ctl.skinPath = this.skinPath;
            ctl.build();
            if (!focused && !ctl.readonly) {
            	ctl.setFocus();
            	focused = true;
            }
        }
    },
    
    onLoadTemplate: function(){
        this.changeIds();
        this.fillSelects();
    },

    getFullId: function(){
        if (this.instance_id!="")
            return this.object_id+"_"+this.instance_id;
        else
            return this.object_id;
    },

    show_context_menu: function(id,left,top,item_id,params) {
        var objectid = id;
        var obid=id;
        var entity = this;
        if (entity.node.getAttribute("disable")=="true")
            return 0;        
        if (params==null)
            params = '';
        if (current_context_menu!=null && current_context_menu.node!=null) {
            current_context_menu.node.style.display = 'none';
            delete(current_context_menu.node);
            delete(objects.objects[current_context_menu.object_id]);
        }
        var args = new Object;
        args["left"] = left;
        args["top"] = top;
        args["opener_item"] = item_id;
        args["opener_object"] = this.object_id;
        if (params!=null)
			args["arguments"] = params;
        if (params["opener_object"]!=null)
        	args["opener_object"] = params["opener_object"];
        entity.node.setAttribute("disable","true");
        new Ajax.Request("index.php",
            {
                method: "post",
                parameters: {ajax: true, object_id: objectid,
                             hook: '2', arguments: Object.toJSON(args)},
                onSuccess: function(transport) {
                    var response = transport.responseText;
                    var response_object = response.evalJSON();
                    var div = document.createElement('div');
                    div.innerHTML = response_object["css"].concat(response_object["html"]);
                    entity.node.appendChild(div);
                    div.style.display='';
                    eval(response_object["javascript"].replace("<script>","").replace("<\/script>",""));
                    entity.context_menu=$O(obid,'');
                    current_context_menu = entity.context_menu;
                    entity.node.setAttribute("disable","false");                            
                }
            });
    },

    raiseEvent: function (event,params,remote_event,receivers) {
        if (remote_event==true) {
            var params_arr = new Array;
            for (ar in params) {
                if (typeof(params[ar])!="function")
                if (ar!="")
                	params_arr[params_arr.length] = ar+"="+params[ar];
            }
            var params_string = params_arr.join("xoxox");
			var args = new Object;
			args["params"] = params_string;
			args["event"] = event;
			var wm = getWindowManager();
			if (wm!=null && wm.appUser!=null && !isNaN(wm.appUser) && typeof wm.appUser!='undefined') {
				args["sender"] = wm.appUser;
			}

			if (receivers!=null)
				args["receivers"] = receivers;
			var obj = this;
            if (remote_event==true) {
                new Ajax.Request("index.php", {
                    method:"post",
                    parameters: {ajax: true, object_id: "Application",hook: '4', arguments: Object.toJSON(args)},
                    onSuccess: function(transport)
                    {
                        var response = trim(transport.responseText.replace("\n",""));
                        if (response!="")
                            obj.reportMessage(response,"error",true);
                    }
                });
            }
        }
        if (receivers!=null)
        	return 0;
        var o=0;
        for (o in objects.objects) {
          if (typeof(objects.objects[o])=="object") {
              if (objects.objects[o]["dispatchEvent"]!=null)
                objects.objects[o].dispatchEvent(event,params);
          }
        }
    },

    getChildObjects: function() {
        var result = new Array;
        if (typeof objects != "undefined") {
        	var o=0;
            for (o in objects.objects) {
                if (typeof(objects.objects[o])=="object") {
                    if (objects.objects[o].parent_object_id == this.object_id) {
                        result[result.length] = objects.objects[o];
                    }
                }
            }
        }
        return result;
    },

    dispatchEvent: function(event,params) {
        if (event==null)
            return 0;
        if (event == "REMOVE_WINDOW") {
            getWindowManager().remove_window(params["object_id"],'',true);        	
        }
        if (event == "DESTROY") {
            if (params["object_id"]!=null) {   
                if (this.parent_object_id==null)
                    return 0;
                var wind=null;
                if (this.win!=null)
                 if (params["object_id"] == this.win.id)
					wind = true;
				else
					wind = false;
                if (params["object_id"]==this.parent_object_id || wind) {  
                    this.raiseEvent("DESTROY",$Arr("object_id="+this.object_id));
                    if (typeof objects != "undefined") {
                        if (typeof objects.objects[this.object_id] == "object" && this.object_id!="") {     
                            if (objects.objects[this.object_id].getClassName()=="Window") {
                                getWindowManager().remove_window(this.object_id,'',true);
                            }
                            else {
                                delete objects.objects[this.object_id];
                            }
                        }
                    }
                    return 0;
                }
            }
            return 0;
        }
        if (event == "DATATABLE_VALUE_CHANGED") {
            if (params["parent"]==this.object_id) {
                var arr = params["object_id"].split("_");
                if (params["object_id"] == "PersistedFieldsTable_"+this.module_id+"_"+this.object_id.replace(this.module_id+"_","").replace(/_/g,"")) {                    
                    $I(this.node.id+"_persistedFields").value = params["value"].replace(/xox/g,"=");
                }
                if (arr[0] != "EntityDataTable") {                    
                    var ev = new Array;
                    ev.target = this.node;
                    $O(params["parent"],"").onChange(ev);
                }
            }
            if (params["parent"]==this.object_id && params["object_id"]=="TagsTable_"+this.module_id+"_"+this.name) {
                if (params["object_id"] == "TagsTable_"+this.module_id+"_"+this.name) {
                	if ($I(this.node.id+"_tagsTable")!=0)
                		$I(this.node.id+"_tagsTable").value = params["value"].replace(/xox/g,"=");
                }
            }            
        }
        if (event == "CONTROL_VALUE_CHANGED") {
            if ($O(params["object_id"],'')!=0 && $O(params["object_id"],'')!=null) {
                if ($O(params["object_id"],'').parent_object_id==this.object_id) {
                    var className = $O(params["object_id"],'').parent_object_id.split("_").shift();
                    if (className=="EntityDataTable") {
                        if (params["object_id"].split("_").pop()=="isPublic") {
                            var row = $O(params["object_id"],'').node.parentNode.getAttribute("row");
                            var entity = $O($O(params["object_id"],'').parent_object_id).getItem(row,0).getValue();
							var args = new Object;
							args["value"] = params["value"];							 
                            new Ajax.Request("index.php",
                            {
                                method: "post",
                                parameters: {ajax: true, object_id: entity, hook: 'vchanged', arguments: Object.toJSON(args)},
                                onSuccess: function(transport) {
                                    entity.raiseEvent("ENTITY_CHANGED",$Arr("object_id="+params["object_id"]),true);
                                }
                            });                            
                        }
                    }
                    if (this.win!=null && this.win!="" && className!="EntityDataTable" && $O(this.win.php_object_id,'')!=null && !this.win.ignoreChanging) {
                        var ev = new Array;
                        ev.target = this.node;
                        $O(this.win.php_object_id,'').onChange(ev);
                    }
                }
            }
        }
        if (event == "TAB_CHANGED") {
        	var tbl = null;
            if (params["tabset_id"]==this.tabsetName) {
                if (params["tab"]=="persistedFieldsTable" && this.persistedFieldsTableBuild!=true) {
                    if (this.module_id=="")
                        tbl = $O("PersistedFieldsTable_"+this.object_id.replace(/_/g,""));
                    else
                        tbl = $O("PersistedFieldsTable_"+this.module_id+"_"+this.object_id.replace(this.module_id+"_","").replace(/_/g,""));
                    tbl.build(true);
                    tbl.sort();
                    $I(tbl.node.id+"_innerFrame").src = tbl.frameSrc;
                    this.persistedFieldsTableBuild = true;
                }
                if (params["tab"]=="childrenTable" && this.childrenTableBuild!=true) {
                    if (this.module_id=="")
                        tbl = $O("EntityDataTable_"+this.object_id.replace(/_/g,""));
                    else
                        tbl = $O("EntityDataTable_"+this.module_id+"_"+this.object_id.replace(this.module_id+"_","").replace(/_/g,""));
                    tbl.build();
                    tbl.selectSortColumn();
                    $I(tbl.node.id+"_innerFrame").src = tbl.frameSrc;
                    this.childrenTableBuild = true;
                }
            }
        }
        if (this[event+"_processEvent"]!=null)
            this[event+"_processEvent"](params);        
    },

    onRemoveWindow: function (topWindow) {
    },

    on_focus: function(event) {
        if (blur_object!=null) {
            if (blur_error!="") {
                event.cancelBubble = true;
                var be = blur_error;
                blur_object.setFocus();
                if (event.preventDefault) event.preventDefault();
                if (event.returnValue) event.returnValue = false;
                this.reportMessage(be,"error",true);
                return false;
            }
        }
    },

    setReadOnly: function() {
        if (this.readOnly=="true") {
            var elems = this.node.getElementsByTagName("*");
            var ec=0;
            for (ec=0;ec<elems.length;ec++) {
            	if (elems[ec].getAttribute("control")=="yes") {
            		continue;
            	} else {
            	if (elems[ec].getAttribute("unchangeable")!="true")
            		elems[ec].disabled = true;
            	}
            }
        }
    },

    cancel_onClick: function(event) {
        if (window.dialogArguments == "modal") {
            window.close();
        } else if (this.win.id!=null) {
            getWindowManager().remove_window(this.win.id,this.instance_id);
        }
    },
    
    getPersistedArray: function(persistedString) {
        var persistedArray = persistedString.split("#");
        var c = 0;
        var result = new Array;
        var res = new Array;
        for (c=0;c<persistedArray.length;c++) {
            res = persistedArray[c].split('|');            
            result[res.shift()] = res.join("|");
        }
        return result;
    },

    afterSaveRaiseEvents: function(values) {
        delete values["Text"];
        delete values["comment"];
        if (values["old_name"]==values["name"]) {
            this.raiseEvent("ENTITY_CHANGED",values,true);
        } else {
            this.raiseEvent("ENTITY_ADDED",values,true);                                
        }        
    },
    
    afterSave: function() {
    	var result=0;
        if ($I(this.node.id+"_innerFrame").contentDocument.body!=null)
            result = $I(this.node.id+"_innerFrame").contentDocument.body.innerHTML.replace(/\<script\>(.*)\<\/script\>/g,"").replace(/\n/g,"");
        var obj=this;
        var mbox=this;
        var go = false;
        var is_new = false;
        var is_new_class=false;
        if (result!=parseInt(result)) {            
            var response_object = result.evalJSON();
            if (response_object!=null && response_object["error"]!=null)
                obj.reportMessage(response_object["error"],"error",true);
            else {
                if (!this.isStatic && result != $I(this.node.id+"_innerFrame").contentDocument.body.innerHTML) {
                    this.reportMessage(result,"error",true);
                }
                else
                	go = true;
            }
        } else
            go = true;
        if (go) {
            var obj_id = this.object_id;
            if (this.win!=0 && this.win!=null && this.win!="") {
                this.win.node.setAttribute("changed",false);
                var data = this.getValues();
                if (data["title"]!=null)
                    $I(this.win.node.id+"_headertext").innerHTML = $I(this.win.node.id+"_headertext").innerHTML.replace(data["old_title"],data["title"]).replace("*","");                                    
                $I(this.win.node.id+"_headertext").innerHTML = $I(this.win.node.id+"_headertext").innerHTML.replace("*","");               
                if (data["old_name"]=="") {
                    obj_id = this.object_id+result;
                    is_new = true;
                }
                else
                    is_new = false;
                if (data["class"] != null && data["class"]!="" && data["old_class"] != data["class"]) 
                    is_new_class = true;
                else
                    is_new_class = false;
                
                var fields = new Array;
                var field_definitions = this.getPersistedArray(data["persistedFields"]);
                var o=null;
                for (o in data) {                    
                    if (typeof data[o] != 'function') {
                        if (field_definitions[o]!=null || o.substr(0,4)=="new_" || o.substr(0,4)=="old_") {
                            fields[o] = data[o];
                        }
                    }
                }
                if (result!=0 && result!=null && this.isStatic) {
                    var resarr = result.split("\n");
                    fields["name"] = resarr[resarr.length-1];
                    if (fields["name"]=="")
                        fields["name"] = result;
                } else
                    fields["name"] = result;
                fields["name"] = fields["name"].replace("\n","");
                fields["object_id"] = obj_id;
                fields["image"] = this.icon;            
                var parent33 = fields["parent"];
                var title = fields['title'];
                var obj = this;
                var ending = this.node.id.split("_");
                ending.shift();
                ending.pop();
                ending = ending.join("_")+"_"+fields["name"];
                var classname="";
                if (!is_new_class) {
                    classname = this.node.id.split("_").shift();
                }
                else {
                    classname = data["class"];
                    fields["new_object_id"] = classname+"_"+ending;
                    fields["target_id"] = classname+"_"+ending;
                    fields["new_class"] = "true";
                }
                var wm = getWindowManager();                
                if (fields["parent"]!=null && fields["parent"] != fields["old_parent"] && fields["parent"]!=-1) {
                    new Ajax.Request("index.php",
                    {
                        method: "post",
                        parameters: {ajax: true, object_id: fields["parent"], hook: 'get_id'},
                        onSuccess: function(transport) {
                            var response = transport.responseText;
                            if (response!="") {
                                fields["new_parent"] = response;
                            }
                            obj.afterSaveRaiseEvents(fields);
                            if (is_new || is_new_class) {
                                var new_id = classname+"_"+ending;
                                var new_win_id = "Window_Window"+getClientId(new_id);
                                var pars = new Object;
								pars['hook'] = "admTpl";
                                wm.show_window(new_win_id,new_id,pars,obj.module_id,new_id);                                
                                wm.remove_window(mbox.win.object_id);                                                                
                            }
                        }
                    });
                } else {
                    this.afterSaveRaiseEvents(fields);
                    if ($I(this.node.id+"_old_parent")!=0)
                        $I(this.node.id+"_old_parent").value = parent33;
                    $I(this.node.id+"_old_title").value = title;
                    $I(this.node.id+"_old_name").value = fields["name"];
                    if (is_new || is_new_class) {
                        var new_id = classname+"_"+ending;
                        var new_win_id = "Window_Window"+new_id.replace(/_/g,"");
                        var pars = new Object;
                        pars['hook'] = "admTpl";
                        pars["top"] = this.win.node.style.top;
                        pars["left"] = this.win.node.style.left;
                        wm.show_window(new_win_id,new_id,pars,obj.module_id,new_id);                                
                        wm.remove_window(mbox.win.object_id);                                                                
                    }
                }
            }
            return 1;
        } else
        	return 0;
        
    },
    
    setValuesFromArray: function(arr) {
    	var o=null;
    	for (o in arr) {
    		if (typeof arr[o] != "function")
    			if ($O(this.node.id+"_"+o,"")!=null)
    				$O(this.node.id+"_"+o,"").setValue(arr[o]);
    	}
    },
    
    onLoad: function() {
	},

	uploadDialogStartHandler: function() {
		
	},

	uploadDialogCompleteHandler: function(selNum, queuedNum, allNum) {
		this.startUpload();
	},

	uploadStartHandler: function(file) {

	},

	uploadSuccessHandler: function(file, data, response) {

	},

	uploadProgressHandler: function(file, completedBytes, allBytes) {
		
	},

	uploadCompleteHandler: function(file) {
		
	},

	uploadErrorHandler: function(file, error, message) {

	},
	
	linksTableButton_onClick: function(event) {		
		var params = new Object;
		params["hook"] = 3;
		params["topLinkObject"] = this.object_id;
		params["ownerObject"] = this.object_id;
		params["tabTitles"] = "";
		params["object_text"] = "Связи объекта "+this.presentation;
		var elem_id = "EntityGroupsTable_"+this.module_id+"_"+params["topLinkObject"].split("_").pop();
		var wm = getWindowManager();					
		var window_elem_id = "Window_"+elem_id.replace(/_/g,"");
		var win_elem_id = window_elem_id.split("_");
		win_elem_id.shift();
		win_elem_id = win_elem_id.join("_");
		wm.show_window(window_elem_id,elem_id,params,this.object_id,this.node.id,null,true);
	},
	
	profileEditBtn_onClick: function(event) {
		var wm = getWindowManager();
		var role = $O(this.object_id+"_currentRole","").getValue();
		var elem_id = this.profileClass+"_"+this.module_id+"_"+role+"_"+this.object_id.replace(this.module_id+"_","");
		var window_elem_id = "Window_"+elem_id.replace(/_/g,"");
		var params = new Array;
		wm.show_window(window_elem_id,elem_id,params,this.object_id,this.node.id,null,false);		
	},
	
	reportMessage: function(message,type,activate) {
		var wm = getWindowManager();
		var obj = this;
		if (wm!=null && wm.showInfoPanel) {
			if (activate)
				obj.raiseEvent("SEND_MESSAGE",$Arr("text="+message.replace(/\\n/g,"<br/>")+",object_id=InfoPanelMessages,type="+type+",activate=true"));
			else
				obj.raiseEvent("SEND_MESSAGE",$Arr("text="+message.replace(/\\n/g,"<br/>")+",object_id=InfoPanelMessages,type="+type));
		} else
			alert(message);
	},
	
	insertEntity: function(objName,divName,params) {
		var ob = this;
		new Ajax.Request("index.php", {
			method:"post",
			parameters: {ajax: true, object_id: objName,hook: 'show', arguments: Object.toJSON(params)},
			onSuccess: function(transport)
			{
				var response = transport.responseText;
				var div = $I(divName);
				if (div.innerHTML!="") {
					elems = div.getElementsByTagName("DIV");
					if (elems[0]!=null) {
						var oldName = elems[0].getAttribute("object");
						if (oldName!=null)
							obj.raiseEvent("DESTROY",$Arr("object_id="+oldName));
						
					}
				}				
                var response_object = response.evalJSON();                
                div.innerHTML = response_object["css"].concat(response_object["html"]);
                eval(response_object["javascript"].replace("<script>","").replace("<\/script>",""));
                var newObj = $O(objName,"");
                newObj.opener_object = ob;
                newObj.opener_item = ob.node;
                newObj.win = ob.win; 
                newObj.parent_object_id = ob.object_id;
                newObj.afterInsert();
			}
		});				
	},
	
    help_onClick: function(event) {
        var params = new Array;
        getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"guide"+this.helpGuideId.replace(/_/g,""),"HTMLBook_"+this.module_id+"_"+this.helpGuideId,params,this.opener_item.getAttribute("object"),this.opener_item.id);
    },	
	
	afterInsert: function() {
		
	}
});