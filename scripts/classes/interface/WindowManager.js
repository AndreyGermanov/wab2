var WindowManager = Class.create(Entity,{

    postInit: function() {
        this.windows = new Array;
        this.active_elem = null;
        this.blockedWindows = new Array;
        this.topWindow = globalTopWindow;
        this.registeredAttempts = new Array;
    },

    getTaskbar: function() {        
        this.topWindow = globalTopWindow;                            
        var result = { taskbar_frame: this.topWindow.document.getElementById('WindowManager_window_manager_iframe'),
                       taskbar:  this.topWindow.document.getElementById('WindowManager_window_manager_iframe').contentDocument.all['WindowManager_taskbar'],
                       taskbar_whitespace: this.topWindow.document.getElementById('WindowManager_window_manager_iframe').contentDocument.all['WindowManager_taskbar_whitespace'],
                       taskbar_button: this.topWindow.document.getElementById('WindowManager_window_manager_iframe').contentDocument.all['WindowManager_taskbar_button']
                  };
        return result;
    },
        
    load: function() {
        var params = new Object;
        params["hook"] = "setParams";
        params["has_border"] = "0";
        params["module_id"] = this.module_id;
        params["has_maximize"] = "0";
        params["has_minimize"] = "0";
        params["has_close"] = "0";
        params["resizeable"] = "0";
        params["left"] = "1";
        params["height"] = "30";
        this.show_window("Window_TaskbarMain","Taskbar_Main",params);        
    },
    
    show_window: function(window_id,object_id,parms,opener_object,opener_item,opener_instance,ignoreChanging) {
        if (this.windows[window_id]!=null)
            return 0;        
        var wid = window_id.split("_");
        wid.shift();
        var old_src = "";old_src = old_src+"";
        var opener_item_end = ""; opener_item_end = opener_item_end+"";
        var op_item = ""; op_item = op_item+"";
        var wind_id = wid.join("_");        
        if (this.windows[wind_id]!=null) {
            var loadimg = $I("loadImg");
            if (loadimg!=0 && loadimg!=null)
            	loadimg.parentNode.removeChild(loadimg);
            this.activate_window(wind_id);
            return 0;
        }
        if (this.blockedWindows[wind_id]!=null)
        	return 0;
        else
        	this.blockedWindows[wind_id] = wind_id;
        if (opener_item!=null) {
            opener_item_end = opener_item.split("_").pop();
            op_item = $I(opener_item);
            if (opener_item_end == "image" || opener_item_end == "text") {
                op_item.parentNode.setAttribute("target_object",op_item.parentNode.getAttribute("target_object")+"_HIDED");
                op_item.setAttribute("target_object",op_item.getAttribute("target_object")+"_HIDED");
                if (opener_item_end == "image") {
                	if ($I(opener_item.replace("_image","_text"))!=0) {
                		$I(opener_item.replace("_image","_text")).setAttribute("target_object",$I(opener_item.replace("_image","_text")).getAttribute("target_object")+"_HIDED");
                		old_src = $I(opener_item).src;
                		op_item.src = $O(op_item.getAttribute("object")).skinPath+"/images/Tree/loading.gif";
                	}
                }
                if (opener_item_end == "text") {
                    $I(opener_item.replace("_text","_image")).setAttribute("target_object",$I(opener_item.replace("_text","_image")).getAttribute("target_object")+"_HIDED");
                    old_src = $I(opener_item.replace("_text","_image")).src;
                    $I(opener_item.replace("_text","_image")).src = $O($I(opener_item.replace("_text","_image")).getAttribute("object")).skinPath+"/images/Tree/loading.gif";           
                }
            }
        }
        var args = new Object;
        args["php_object_id"] = object_id;
        if (typeof opener_object=="object" && opener_object!=null)
                args["opener_object"] = opener_object.object_id;
            else
                args["opener_object"] = opener_object;
        args["opener_item"] = opener_item;
        args["opener_instance"] = opener_instance;
        if (parms!=null) {
			if (parms["hook"]!=null)
				args["params"] = Object.toJSON(parms);
			else
				args["params"] = parms;
		}
        if (ignoreChanging==true)
			args["ignoreChanging"] = true;
		else
			args["ignoreChanging"] = false;
        var ignChanging = ignoreChanging;
        var wm = this;    
        new Ajax.Request("index.php", {
            method: "post",
            parameters: {ajax: true, object_id: window_id, hook: '2', arguments: Object.toJSON(args)},
            onSuccess: function(transport) {
                var response = transport.responseText;
                if (response != "") {
                    wid = window_id.split("_");
                    wid.shift();
                    window_id = wid.join("_");
                    var js_id = getClientId(window_id);
                    var response_object = response.evalJSON();                    
                    var div = document.createElement('div'); 
                    div.innerHTML = response_object["css"].concat(response_object["html"]);
                    document.body.appendChild(div);                    
                    if (opener_object!=null)
                        $(js_id).setAttribute("opener_object",opener_object);
                    if (opener_item!=null)
                        $(js_id).setAttribute("opener_item",opener_item);
                    if (opener_instance!=null)
                        $(js_id).setAttribute("opener_instance",opener_instance);
                    eval(response_object["javascript"].replace("<script>","").replace("<\/script>",""));
                    if (ignChanging) {
                        div.setAttribute("ignoreChanging","true");
                        $(js_id).setAttribute("ignoreChanging","true");
                        $O(window_id,'').ignoreChanging = ignChanging;
                    }
                    var arr = response_object["args"].toString().split('\n');
                    var args = new Array;
                    for (var counter=0;counter<arr.length;counter++) {
                        var arg_parts = arr[counter].split('=');
                        args[arg_parts[0]]=arg_parts[1];
                    }
                    
                    $(js_id).setAttribute('is_window','true');
                    $(js_id).style.opacity = 0;
                    js_id = getClientId(window_id);
                    wm.activate_window(window_id,true);
                    var elems = globalTopWindow.document.body.getElementsByTagName('DIV');
                    if ($(js_id).getAttribute('can_maximize')=='true') {
                        for (var counter=0;counter<elems.length;counter++) {
                            var is_window = elems[counter].getAttribute('is_window');
                            var is_maximized = elems[counter].getAttribute('maximized');                            
                            if (is_window=='true' & is_maximized=='true') { 
                                wm.maximize_window(window_id);
                                break;
                            }
                        }
                    }                    
                }
                if (opener_item!=null) {
                    if (opener_item_end == "image" || opener_item_end == "text") {
                        op_item.parentNode.style.display = '';
                        op_item.style.display = '';
                        if (opener_item_end == "image") {
                            $I(opener_item.replace("_image","_text")).style.display = '';
                            op_item.src = old_src;
                        }
                        if (opener_item_end == "text") {
                            $I(opener_item.replace("_text","_image")).style.display = '';
                            $I(opener_item.replace("_text","_image")).src = old_src;
                        }
                    }
                }
                var loadimg = $I("loadImg");
                if (loadimg!=0 && loadimg!=null)
                	loadimg.parentNode.removeChild(loadimg);
                wm.blockedWindows[window_id] = null;                    
            }
        });
        return 0;
    },

    activate_window: function(window_id,isnew,isdel) {   
        var js_id = getClientId(window_id);
        var win = $O(window_id,'');
        if (win==null)
        	return 0;
        var node = globalTopWindow.document.getElementById(js_id);
        if (this.active_elem!=null && this.active_elem!=node) {
            if ($O(this.active_elem.getAttribute("object"),'')!=null) {
                this.raiseEvent("DEACTIVATE_WINDOW",$Arr("object_id="+$O(this.active_elem.getAttribute("object"),'').php_object_id+",opener_item="+$O(this.active_elem.getAttribute("object"),'').opener_item));
            }
            if ($(this.active_elem.id.concat("_window_table"))!=null)
            	$(this.active_elem.id.concat("_window_table")).style.borderWidth='0px';
		}
        this.prev_active_elem = this.active_elem;        
        var prev_el = "";
        if (this.prev_active_elem!=null)
        	prev_el = this.prev_active_elem.id;
        this.active_elem = node;
        if (this.active_elem.style.display=="none" || this.active_elem.id != prev_el) {
        	this.active_elem.style.display = '';
        	this.active_elem.style.opacity = 0;
        	new Effect.Opacity(this.active_elem.id, { from: 0.3, to: 1.0, duration: 0.5 });
            this.raiseEvent("ACTIVATE_WINDOW",$Arr("object_id="+$O(this.active_elem.getAttribute("object"),'').php_object_id+",opener_item="+$O(this.active_elem.getAttribute("object"),'').opener_item));        	
        }
        if (!isdel && !isnew) { 
        	this.active_elem.style.display = '';
        }
        this.topWindow.document.getElementById(js_id.concat('_window_table')).style.borderWidth='2px';
		this.topWindow.document.getElementById(js_id.concat('_window_table')).style.borderColor='#222222';
		this.topWindow.document.getElementById(js_id.concat('_window_table')).style.borderStyle='solid';
		if (isnew) {
			this.active_elem.style.zIndex = parseInt(this.getMaxZIndex())+1;
		} else if (isdel) {
			this.active_elem.style.zIndex = parseInt(this.getMaxZIndex());
		} else if (this.active_elem != this.prev_active_elem) {
			this.active_elem.style.zIndex = parseInt(this.getMaxZIndex());
			this.prev_active_elem.style.zIndex = parseInt(this.active_elem.style.zIndex)-1;
			var o=null;
			for (o in this.windows) {
				if (typeof this.windows[o] != "function") {
					if (parseInt(this.windows[o].node.style.zIndex)<=this.prev_active_elem.style.zIndex) {
						if (this.windows[o].node != this.active_elem && this.windows[o].node != this.prev_active_elem) {
							this.windows[o].node.style.zIndex = parseInt(this.windows[o].node.style.zIndex)-1;
							if (this.windows[o].node.style.zIndex<0)
								this.windows[o].node.style.zIndex=0;
						}
					}
				}
			}
		}
		$O("Taskbar_Main","").activateButton(window_id,$O(window_id,'').icon,$O(window_id,'').object_text);
    },

    remove_window: function(window_id,instance_id,force_remove) {
        var js_id = getClientId(window_id);
        if (instance_id!=undefined && instance_id!="")
            instance_id="_"+instance_id;
        else
            instance_id="";
        var remove_node = globalTopWindow.document.getElementById(js_id);        
        var attr = remove_node.getAttribute('changed');
        if (attr!=null && attr == "true" && remove_node.getAttribute("ignoreChanging") != "true" && force_remove!=true)
        {
            if (confirm("Данные были изменены. Закрыть без сохранения ?"))
            {
                if (objects.objects[$O(window_id).php_object_id+instance_id]!=null) {
                    objects.objects[$O(window_id).php_object_id+instance_id].onRemoveWindow(window);
                }
                var win = $O(window_id,'');
                this.raiseEvent("DEACTIVATE_WINDOW",$Arr("object_id="+win.php_object_id+",opener_item="+win.opener_item));
                var args = new Object;
                args["php_object_id"] = win.php_object_id;
                this.raiseEvent("DESTROY",$Arr("object_id="+win.object_id));
                new Ajax.Request("index.php",
                {
                    method: "post",
                    parameters: {ajax: true, object_id: "Application", hook: '7', arguments: Object.toJSON(args)},
                    onSuccess: function(transport) {
                    }
                });
                var o = 0;
                for (o in this.windows) {
                	if (typeof this.windows[o] != "function") {
                		if (this.windows[o]!=this.windows[window_id] && parseInt(this.windows[o].node.style.zIndex)>parseInt(this.windows[window_id].node.style.zIndex)) {
                			this.windows[o].node.style.zIndex = parseInt(this.windows[o].node.style.zIndex)-1;
                			if (this.windows[o].node.style.zIndex<0)
                				this.windows[o].node.style.zIndex = 0;
                		}
                	}
                }
                remove_node.parentNode.removeChild(remove_node);
                remove_node.innerHTML="";
                remove_node = null;
                delete this.windows[window_id];
                delete objects.objects[$O(window_id).php_object_id+instance_id];
                delete objects.objects[window_id];
                $O("Taskbar_Main").removeButton(js_id);
                var zIndex = this.getMaxZIndex();
                if (zIndex<0)
                	zIndex = 0;
                var prev_win = this.getWindowByZIndex(zIndex);
                if (prev_win!=null) {
                	this.activate_window(prev_win,false,true);
                }
            }
        } else {
                if (objects.objects[$O(window_id).php_object_id+instance_id]!=null) {
                    objects.objects[$O(window_id).php_object_id+instance_id].onRemoveWindow(window);
                }
                var win = $O(window_id,'');
                this.raiseEvent("DEACTIVATE_WINDOW",$Arr("object_id="+win.php_object_id+",opener_item="+win.opener_item));
                var args = new Object;
                args["php_object_id"] = win.php_object_id;
                this.raiseEvent("DESTROY",$Arr("object_id="+window_id));
                new Ajax.Request("index.php",
                {
                    method: "post",
                    parameters: {ajax: true, object_id: "Application", hook: '7', arguments: Object.toJSON(args)},
                    onSuccess: function(transport) {
                    }
                });
                var o = 0;
                for (o in this.windows) {
                	if (typeof this.windows[o] != "function") {
                		if (this.windows[o]!=this.windows[window_id] && parseInt(this.windows[o].node.style.zIndex)>parseInt(this.windows[window_id].node.style.zIndex)) {
                			this.windows[o].node.style.zIndex = parseInt(this.windows[o].node.style.zIndex)-1;
                			if (this.windows[o].node.style.zIndex<0)
                				this.windows[o].node.style.zIndex = 0;
                		}
                	}
                }
                remove_node.parentNode.removeChild(remove_node);
                remove_node.innerHTML="";
                remove_node = null;
                delete this.windows[window_id];
                delete objects.objects[$O(window_id).php_object_id+instance_id];
                delete objects.objects[window_id];
                $O("Taskbar_Main").removeButton(js_id);
                var zIndex = this.getMaxZIndex();
                if (zIndex<0)
                	zIndex = 0;
                var prev_win = this.getWindowByZIndex(zIndex);
                if (prev_win!=null) {
                	this.activate_window(prev_win,false,true);
                }
        }
    },

    maximize_window: function(window_id) {
       var js_id = getClientId(window_id);
       var obj = $O(window_id);
       if (obj.dock_left=="1" || obj.dock_right=="1" || obj.dock_top=="1" || obj.dock_bottom=="1")
    	   return 0;
       var app = globalTopWindow.document.getElementById("ApplicationNew");
       var wm = getWindowManager();
       if (app!=null)
        var app_width = app.style.width;
       else
        app_width = 1;
       var node1 = globalTopWindow.document.getElementById(js_id);
       var attr = node1.getAttribute("maximized");
       if (attr==null || attr!="true") {    
    	    node1.setAttribute("maximized","true");
            node1.setAttribute("normal_left",node1.style.left);
            node1.setAttribute("normal_top",node1.style.top);
            node1.setAttribute("normal_width",node1.style.width);
            node1.setAttribute("normal_height",node1.style.height);
            node1.style.left = parseInt(app_width);
            node1.style.width = window.innerWidth-parseInt(app_width);           
            if ($O(wm.mainMenuName)!=null) {
                node1.style.top = getElementPosition(wm.mainMenuName).height;
                node1.style.height = window.innerHeight-61-getElementPosition(wm.mainMenuName).height;
            }
            else {
                node1.style.top = "1px";
                node1.style.height = window.innerHeight-61;
            }
        }
        else {
            node1.style.left = node1.getAttribute("normal_left");
            node1.style.top = node1.getAttribute("normal_top");
            node1.style.width = node1.getAttribute("normal_width");
            node1.style.height = node1.getAttribute("normal_height");
            node1.setAttribute("maximized","false");
        }       
		wm.raiseEvent("ON_WINDOW_RESIZE",$Arr("object_id="+obj.php_object_id));
    },

    maximize_windows: function() {
        var elems = globalTopWindow.document.getElementsByTagName('div');
        var counter=0;
        for (counter=0;counter<elems.length;counter++)
        {            
            var is_window = elems[counter].getAttribute('is_window');
            var can_maximize = elems[counter].getAttribute('can_maximize');
            if (is_window=='true' & can_maximize=='true')
            {   
				if (window == globalTopWindow)
					this.maximize_window(elems[counter].id);
            }
        }
    },

    resize_window: function() {   
        var app = globalTopWindow.document.getElementById("ApplicationNew");
        var taskbar = $O("TaskbarMain");
        if (typeof(taskbar)!="undefined" && taskbar!=0)
        {
        	if (window.innerHeight!=null)
        		taskbar.node.style.top = window.innerHeight-60;
        	else
        		taskbar.node.style.top = getElementPosition(document.body).height-60;
            taskbar.node.style.width = '99%';
            taskbar.node.style.height = 50;
            $I(taskbar.node.id+"_inner_frame").style.height = 50;
        }
        
        if (app!=null) {
        	if (window.innerHeight!=null)
        		app.style.height=window.innerHeight-61;
        	else
        		app.style.height=document.body.clientHeight-61;
        }
        var customObject = globalTopWindow.document.getElementById("customObjectDiv");
    	var windowManager = $O("WindowManager","");
        if (customObject!=null && customObject!=0) {
        	if (windowManager.showMainMenu) {
        		if (window.innerWidth!=null) {
            		customObject.style.height = window.innerHeight-61-getElementPosition(windowManager.mainMenuName).height;
            		customObject.style.width = "99%";
        			customObject.style.top = getElementPosition(windowManager.mainMenuName).height;
	        	} else {
	        		frame.setAttribute("width",document.body.clientWidth);
	        		frame.setAttribute("height",document.body.clientHeight-61);                                	
	        	}
        	} else {
        		if (window.innerWidth!=null) {
            		customObject.style.height = window.innerHeight-61;
            		customObject.style.width = "99%";
        			customObject.style.top = 1;
	        	} else {
	        		frame.setAttribute("width",document.body.clientWidth);
	        		frame.setAttribute("height",document.body.clientHeight-61);                                	
	        	}        		
        	}
        }
        var elems = document.getElementsByTagName('div');
        var wm = getWindowManager();
        if ($O(wm.mainMenuName,'')!=null && $O(wm.mainMenuName,'')!=0) {
        	$O(wm.mainMenuName,'').node.setAttribute("width",window.innerWidth);        	
        }
        var topWindow = globalTopWindow;
        for (var counter=0;counter<elems.length;counter++) {
            var is_window = elems[counter].getAttribute('is_window');
            var is_maximized = elems[counter].getAttribute('maximized');            
            if (is_window) {
            	var docked = false;
	        	var win = $O(elems[counter].id,'');
	        	if (win!=null) {
		    		if (win.dock_left=="1") {
		    			if ($O("ApplicationNew","")!=null && $O("ApplicationNew","")!=0)
		    				win.left = parseInt($O("ApplicationNew","").node.style.width);
		    			else
		    				win.left = 1;
		    			docked = true;
		    		}
		    		if (win.dock_right=="1") {			
		    			if ($O("ApplicationNew","")!=null && $O("ApplicationNew","")!=0)
		    				win.width = '99%';
		    			else
		    				win.width = '99%';
		    			docked = true;
		    		}
		    		if (win.dock_bottom=="1") {
		    			win.topy = parseInt(topWindow.innerHeight)-60-win.height;
		    			docked = true;
		    		}
		    		if (win.dock_top=="1") {
		    			if ($O(wm.mainMenuName,"")!=null && $O(wm.mainMenuName,"")!=0)
		    				win.topy = getElementPosition($O(wm.mainMenuName,"").node.id).height; 
		    			else
		    				win.topy = 1;
		    			docked = true;
		    		}
	        	}
	    		if (docked) {
		            if ($O("ApplicationNew","")!=null && $O("ApplicationNew","")!=0) {
		                if (win.width > parseInt(topWindow.innerWidth)-parseInt($O("ApplicationNew","").node.style.width)) {
		                	win.left = parseInt($O("ApplicationNew","").node.style.width);
		                	win.width = parseInt(topWindow.innerWidth)-parseInt($O("ApplicationNew","").node.style.width);
		                }
		                if (win.height > parseInt(topWindow.innerHeight)-60) {
		                	win.topy = parseInt($O("ApplicationNew","").node.style.top);
		                	win.height = parseInt(topWindow.innerHeight)-60;
		                }
		            }
		            if ($O(wm.mainMenuName,"")!=null && $O(wm.mainMenuName,"")!=0) {
		                if (win.topy<getElementPosition($O(wm.mainMenuName,"").node.id).height)
		                	win.topy = getElementPosition($O(wm.mainMenuName,"").node.id).height;
		                if (win.topy+win.height > parseInt(topWindow.innerHeight)-60)
		                	win.height = parseInt(topWindow.innerHeight)-60-win.topy;
		                if (win.dock_bottom=="1")
		                	win.height = parseInt(topWindow.innerHeight)-60-win.topy;
		            } else {
		                if (win.topy+win.height > parseInt(topWindow.innerHeight)-60)
		                	win.height = parseInt(topWindow.innerHeight)-60-win.topy;
		                if (win.dock_bottom=="1")
		                	win.height = parseInt(topWindow.innerHeight)-60-win.topy;		            	
		            }
		            win.node.style.left = win.left;
		            win.node.style.top = win.topy;
		            win.node.style.width = win.width;
		            win.node.style.height = win.height;
	    		}
	            if (is_maximized=='true')
	            {
	                wm.maximize_window(elems[counter].id);
	                wm.maximize_window(elems[counter].id);
	            }
            }
        }
    },

    addWindow: function(obj) {
        if (obj!=null)
            this.windows[obj.id] = obj;
        this.resize_window();
        var opener_item = obj.opener_item;

        if (opener_item!=null) {
            var opener_item_end = opener_item.split("_").pop();
            var op_item = $I(opener_item);
            if (opener_item_end == "image" || opener_item_end == "text") {
                op_item.parentNode.setAttribute("target_object",op_item.parentNode.getAttribute("target_object").replace("_HIDED",""));
                op_item.setAttribute("target_object",op_item.getAttribute("target_object").replace("_HIDED",""));
                if (opener_item_end == "image")
                    $I(opener_item.replace("_image","_text")).setAttribute("target_object",$I(opener_item.replace("_image","_text")).getAttribute("target_object").replace("_HIDED",""));
                if (opener_item_end == "text")
                    $I(opener_item.replace("_text","_image")).setAttribute("target_object",$I(opener_item.replace("_text","_image")).getAttribute("target_object").replace("_HIDED",""));
            }
        }
    },    

    onMouseMove: function(event) {
            var wm = getWindowManager();
            var topWindow = globalTopWindow;
            var counter=0;            
            if (wm.moving_elem!=null & wm.mouse_press==true) {  
                event = event || window.event;
                event.cancelBubble = true;
                if (wm.frames_hide!=true) {
                    var elems = globalTopWindow.document.getElementsByTagName('iframe');
                    for (counter=0;counter<elems.length;counter++) {
                    	if (elems[counter].parentNode!=null) {
                    		if (elems[counter].parentNode.id=="customObjectDiv")
                    			continue;
                    	}
                    	elems[counter].style.display='none';
                    }
                    wm.frames_hide = true;
               }
               var appwidth=1;
               if ($O("ApplicationNew","")!=null)
                appwidth = parseInt($O("ApplicationNew","").node.style.width);
               if (wm.drag_style=="move") {
                    if (wm.moving_elem.moveable==0)
                                   return 0;
                        var addx = wm.moving_elem.addx;
                        var addy = wm.moving_elem.addy;
                        var newx = String(event.screenX-addx);
                        var newy = String(event.screenY-addy);
                        if (event.which<=0) {
                            wm.moving_elem = null;
                            wm.mouse_press = false;
                            return 0;
                        }
                        if (newx<wm.moving_elem.min_left) newx=wm.moving_elem.min_left;
                        if (newx<appwidth) newx=appwidth;
                        if (newy<wm.moving_elem.min_top) newy=wm.moving_elem.min_top;

                        if (wm.moving_elem.max_right!=0)
                            if (parseInt(newx)+parseInt(wm.moving_elem.node.style.width)>this.max_right)
                                newx = wm.moving_elem.max_right-parseInt(wm.moving_elem.node.style.width);

                        if (wm.moving_elem.max_bottom!=0)
                            if (parseInt(newy)+parseInt(wm.moving_elem.node.style.height)>wm.moving_elem.max_bottom)
                                newy = wm.moving_elem.max_bottom-parseInt(wm.moving_elem.node.style.height);

                        if (parseInt(newx)+parseInt(wm.moving_elem.node.style.width)>topWindow.innerWidth)
                            newx = topWindow.innerWidth-parseInt(wm.moving_elem.node.style.width);

                        if (parseInt(newy)+parseInt(wm.moving_elem.node.style.height)>topWindow.innerHeight-60)
                            newy = topWindow.innerHeight-60-parseInt(wm.moving_elem.node.style.height);
                        
                        if ($O(wm.mainMenuName,'')!=null) {
                            if (parseInt(newy)<getElementPosition(wm.mainMenuName).height)
                                newy = getElementPosition(wm.mainMenuName).height;
                        }

                        wm.moving_elem.node.style["left"] = newx;
                        wm.moving_elem.node.style["top"] = newy;
                        wm.moving_elem.node.setAttribute("curx",event.screenX);
                        wm.moving_elem.node.setAttribute("cury",event.screenY);
               }
               
               if (wm.drag_style=="right") { 
                   if (wm.moving_elem.resizeable==0 && wm.moving_elem.resizeable_right==0)
                       return 0;
                   
                        var addwidth = event.screenX-parseInt(wm.moving_elem.node.getAttribute("curx"));
                        var newwidth = parseInt(wm.moving_elem.node.style.width)+addwidth;                        
                        if (wm.moving_elem.max_right!=0)
                            if (parseInt(wm.moving_elem.node.style.left)+newwidth>wm.moving_elem.max_right)
                                newwidth = wm.moving_elem.max_right-parseInt(wm.moving_elem.node.style.left);
                        
                        if (parseInt(wm.moving_elem.node.style.left)+newwidth>topWindow.innerWidth)
                            newwidth = topWindow.innerWidth-parseInt(wm.moving_elem.node.style.left);
                        wm.moving_elem.node.style.width = newwidth;
                        wm.moving_elem.node.setAttribute("curx",event.screenX);
               }
               if (wm.drag_style=="left") {
                   if (wm.moving_elem.resizeable==0 && wm_moving_elem.resizeable_left==0)
                       return 0;
                        var addleft = event.screenX-parseInt(wm.moving_elem.node.getAttribute("curx"));
                        var newleft = parseInt(wm.moving_elem.node.style.left)+addleft;
                        if (newleft<appwidth) {newleft= appwidth;addleft=0;};
                        wm.moving_elem.node.style.left = newleft;
                        if (newleft<wm.moving_elem.min_left)
                        {
                            newleft = wm.moving_elem.min_left;
                        }
                        addwidth = event.screenX-parseInt(wm.moving_elem.node.getAttribute("curx"));
                        var newwidth = parseInt(wm.moving_elem.node.style.width)-addleft;
                        wm.moving_elem.node.style.width = newwidth;
                        wm.moving_elem.node.setAttribute("curx",event.screenX);
               }
               if (wm.drag_style=="bottom") { 
                   if (wm.moving_elem.resizeable==0 && wm.moving_elem.resizeable_bottom==0)
                       return 0;
                        var addheight = event.screenY-parseInt(wm.moving_elem.node.getAttribute("cury"));
                        var newheight = parseInt(wm.moving_elem.node.style.height)+addheight;
                        if (wm.moving_elem.max_bottom!=0)
                            if (parseInt(wm.moving_elem.node.style.top)+newheight>wm.moving_elem.max_bottom)
                                newheight = wm.moving_elem.max_bottom-parseInt(wm.moving_elem.node.style.top);
                        if (parseInt(wm.moving_elem.node.style.top)+newheight>topWindow.innerHeight-60)
                            newheight = topWindow.innerHeight-60-parseInt(wm.moving_elem.node.style.top);
                        
                        wm.moving_elem.node.style.height = newheight;
                        wm.moving_elem.node.setAttribute("cury",event.screenY);
                        
               }
               if (wm.drag_style=="top") {
                   if (wm.moving_elem.resizeable==0 && wm.moving_elem.resizeable_top==0)
                       return 0;
                        var addtop = event.screenY-parseInt(wm.moving_elem.node.getAttribute("cury"));
                        var newtop = parseInt(wm.moving_elem.node.style.top)+addtop;
                        if (newtop<wm.moving_elem.min_top)
                            {newtop = wm.moving_elem.min_top;addtop=0;};
                        if ($O(wm.mainMenuName,'')!=null) {
                            if (newtop<getElementPosition(wm.mainMenuName).height)
                                {newtop = getElementPosition(wm.mainMenuName).height;addtop=0;};                            
                        }
                        wm.moving_elem.node.style.top = newtop;
                        addheight = event.screenY-parseInt(wm.moving_elem.node.getAttribute("cury"));
                        var newheight = parseInt(wm.moving_elem.node.style.height)-addtop;
                        wm.moving_elem.node.style.height = newheight;
                        wm.moving_elem.node.setAttribute("cury",event.screenY);
               }
               if (window.getSelection()!=null)
            	   window.getSelection().removeAllRanges();
         if (event.preventDefault)
            event.preventDefault();
         else
            event.returnValue= false;
            }
            return false;
    },

    onMouseUp: function(event) {
        var wm = getWindowManager();
        wm.mouse_press = false;
        wm.mouse_press = false;
        this.drag_style = "";
        
        if (wm.frames_hide == true) {
            var elemz = globalTopWindow.document.getElementsByTagName('iframe');
            for (var conter=0;conter<elemz.length;conter++) {
                if (elemz[conter]!=null)
                    if (elemz[conter].style!=null) {
                        if (elemz[conter].getAttribute("object")!=null)
                            elemz[conter].style.display='';
                    }
            }
            wm.frames_hide = false;
        }
        if (wm.moving_elem!=null) {
			if (wm.drag_style!="move")
				wm.raiseEvent("ON_WINDOW_RESIZE",$Arr("object_id="+wm.moving_elem.php_object_id));
			wm.moving_elem = null;
		}
        if (window.getSelection()!=null)
        	window.getSelection().removeAllRanges();
    },

    checkUpdates: function() {
      var obj = this;
      new Ajax.Request("index.php", {
          method:"post",
          parameters: {ajax: true, object_id: "Application",hook: '8'},
          onSuccess: function(transport)
          {
            var response = trim(transport.responseText.replace("\n",""));
            if (response=="yes")
                obj.result = true;
            else if (response=="no")
                obj.result = false;
            else
                obj.result = "noconnect";
          }
      });
    },

    updateSystem: function() {
      var obj = this;
      new Ajax.Request("index.php", {
          method:"post",
          parameters: {ajax: true, object_id: "Application",hook: '9'},
          onSuccess: function(transport)
          {
            var response = trim(transport.responseText.replace("\n",""));
            if (response!="")
                alert(response);
            else {
                obj.reportMessage("Система успешно обновлена.","info","true");
                location.reload();
            }
          }
      });
    },
    
    getMaxZIndex: function() {
        var maxIndex = 0;
        var c = 0;
        for (c in this.windows) {            
            if (this.windows[c].node!=null && parseInt(this.windows[c].node.style.zIndex)>maxIndex)
                maxIndex = this.windows[c].node.style.zIndex;
        }
        return maxIndex;
    },
    
    getWindowByZIndex: function(zIndex) {
    	var c = 0;
    	for (c in this.windows) {
    		if (typeof this.windows[c] != "function") {
    			if (this.windows[c].node.style.zIndex==zIndex)
    				return c;
    		}
    	}
    	return null;
    },
    
	SCAN_CODE_processEvent: function(params) {
		var found = false;var o = "";		
		for (o in objects.objects) {						
			if (objects.objects[o].receiveBarCodes=="1" && objects.objects[o].barCodeClasses!=null && objects.objects[o].barCodeClasses[params["object_class"]]!=null) {
				found = true;
				break;					
			}
		}
		if (!found) {
			var elemid = params["object_class"]+"_"+this.module_id+"_"+params["i"];
			var windowid = "Window_"+elemid.replace(/_/g,"");
			this.show_window(windowid,elemid,null,this.object_id,this.node_id);
		}
	},	        
    
    USER_BAN_processEvent: function(params) {
    	location.reload();
    }
});