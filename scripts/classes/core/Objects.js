var Objects = Class.create( {
        initialize: function() {
            this.objects = new Array();
        },

        add: function(obj) {
            if (obj==null)
                return null;
            if (obj.instance_id!="" && obj.instance_id!=null)
                objid = obj.object_id+"_"+obj.instance_id;
            else
                objid = obj.object_id;
            delete this.objects[objid];
            this.objects[objid] = obj;
            return this.objects[objid];
        }
});

function $O(object_id,instance_id) {
	var objid=null;
    if (instance_id!="" && instance_id!=null)
        objid = object_id+"_"+instance_id;
    else
        objid = object_id;
    var tv = globalTopWindow;
    if (tv!=null && tv!=0 && tv.objects!=null)
        return tv.objects.objects[objid];
    else
        objects.objects[objid];
}

function $I(id) {
    id = getClientId(id);
    var result = 0;
    var topWindow = globalTopWindow;
    if (topWindow!=null && topWindow!=0 && topWindow!="") {
        if (topWindow.objects!=null && topWindow.objects.objects!=null) {
        	var o=null;
            for (o in topWindow.objects.objects)
                if (objects.objects[o].node!=null)
                {            
                    if (topWindow.objects.objects[o].node.id==id)
                    {
                        result = topWindow.objects.objects[o].node;
                        break;
                    };
                    result = topWindow.getElementById(objects.objects[o].node,id);
                    if (result!=0)
                        return result;
                }
        }
    }
    return result;
}

function getElementPosition(elemId) {
	var elem=null
	if (typeof(elemId)=="string")
		elem = $I(elemId);
	else
		elem = elemId;
	var w = elem.offsetWidth;
	var h = elem.offsetHeight;
	
	var l = 0;
	var t = 0;
	
	while (elem)
	{
		l += elem.offsetLeft;
		t += elem.offsetTop;
		elem = elem.offsetParent;
	}
	
	return {"left":l, "top":t, "width": w, "height":h};
}
	

function $Arr(str) {
    var str_arr = str.split(",");
    var result = new Array;
    for (var co=0;co<str_arr.length;co++) {
        parts = str_arr[co].split("=");
        result[parts[0]] = parts[1];
    }
    return result;
}

function removeContextMenu(event,context_menu,force) {   
    var clickEventRaised = false;
    var cmenu_parentnode = null;
    var cmenu_node = null;
    if (context_menu!=null) {
        cmenu_node = context_menu.node;
        if (context_menu.node.parentNode != null)
            cmenu_parentnode = context_menu.node.parentNode;
        context_menu.raiseEvent("DESTROY",$Arr("object_id="+context_menu.object_id));
        if(cmenu_node!=null && cmenu_parentnode!=null) {                            
            cmenu_parentnode.innerHTML = "";
        }        
    } else {
    	var o=null;
        for (o in objects.objects) {
            if (objects.objects[o].context_menu!=null) {
                if (objects.objects[o].node!=null) {                        
                    if (objects.objects[o].context_menu!=null && (objects.objects[o].context_menu.notHide!=true || force==true)) {
                        if (objects.objects[o].context_menu!= null)
                            if (objects.objects[o].context_menu.node !=null) {
                                cmenu_node = objects.objects[o].context_menu.node;                                
                                if (objects.objects[o].context_menu.node.parentNode != null)
                                    cmenu_parentnode = objects.objects[o].context_menu.node.parentNode;
                            }                        
                            if (event!=null) {
                            	var target_object=eventTarget(event).getAttribute("object");
                                var target_object_class = "";
                                if (target_object!=null)
                                	target_object_class = target_object.split("_").shift();                                
                                if (target_object_class == "ObjGroupsTree" || target_object_class == "EntityTree")
                                	continue;
                                if (target_object!=null) {
                                    var target_obj = $O(target_object,'');
                                    if (objects.objects[o].context_menu!=null && target_obj!=null) {
                                        if (objects.objects[o].context_menu.object_id == target_obj.parent_object_id) {
                                            continue;
                                        }
                                    }
                                }
                            }
                            if (objects.objects[o].context_menu!=null) {
                            	var selectId="";var treeId="";
                                if (objects.objects[o].context_menu.module_id=="") {
                                    treeId = "EntityTree_"+objects.objects[o].context_menu.object_id.replace(/_/g,'');
                                    if (objects.objects[o].context_menu.selectClass!=null)
                                    	selectId = objects.objects[o].context_menu.selectClass+"_select"; 
                                } else {
                                    treeId = "EntityTree_"+objects.objects[o].context_menu.module_id+"_"+objects.objects[o].context_menu.object_id.replace(objects.objects[o].context_menu.module_id+"_","").replace(/_/g,'');                                
                                    if (objects.objects[o].context_menu.selectClass!=null)
                                    	selectId = objects.objects[o].context_menu.selectClass+"_"+objects.objects[o].context_menu.module_id+"_select"; 
                                }
                                if ($O(treeId,'')!=0) {
                                    delete $O(treeId,'');
                                    delete objects.objects[treeId];
                                }
                                if ($O(selectId,'')!=0) {
                                    delete $O(selectId,'');
                                    delete objects.objects[selectId];
                                }
                                objects.objects[o].context_menu.raiseEvent("DESTROY",$Arr("object_id="+objects.objects[o].context_menu.object_id));
                            }
                            if(cmenu_node!=null && cmenu_parentnode!=null) {                            
                                cmenu_parentnode.innerHTML = "";
                            }
                        }
                    }
                    if (objects.objects[o].context_menu!=null)
                        delete objects.objects[objects.objects[o].context_menu.object_id];
                    delete objects.objects[o].context_menu;
                }
            	if (!clickEventRaised) {
                	if (objects.objects[o].raiseEvent != null) {
                    	objects.objects[o].raiseEvent("ON_CLICK","");
                    	clickEventRaised = true;
                	}            
            	}                    
            
            }
    }        
}

var tv = globalTopWindow;

if (window===tv)
    var objects = new Objects();
else {
    if (tv!=null) {
        if (tv.objects!=null)
            var objects = tv.objects;
        else
            var objects = new Objects();
    } else
        var objects = new Objects();    	
}
window.onclick = removeContextMenu;