var elems=globalTopWindow.document.getElementsByTagName('body');
if (globalTopWindow==window) {   
    if (typeof(windowManager) == "undefined") {
        windowManager = $O("WindowManager","");
        windowManager.appUser = '{appUser}';
        windowManager.autorunStr = '{autorunStr}';
        windowManager.path = "{path}";
        windowManager.fileId = "{fileId}";
        windowManager.module_id = '{defaultModuleName}';
        if (windowManager.autorunStr[0]=="{") {
        	windowManager.autorun = windowManager.autorunStr.evalJSON(); 
        } else
        	windowManager.autorun = new Array;
        if (windowManager!=null) {
            windowManager.showControlPanel = +'{showControlPanel}';
            windowManager.showMainMenu = +'{showMainMenu}';
            windowManager.showInfoPanel = +'{showInfoPanel}';
            windowManager.mainMenuName = '{mainMenuName}';
            windowManager.defaultModuleName = '{defaultModuleName}';
            windowManager.customObjectName = '{customObjectName}';            
            if (windowManager.mainMenuName!=null && windowManager.mainMenuName!='' && windowManager.defaultModuleName!='') {
                var arr = windowManager.mainMenuName.split('_');
                var menuClass = arr.shift();
                windowManager.mainMenuName = menuClass+"_"+windowManager.defaultModuleName+"_"+arr.join("_");
            }            
            windowManager.topObjects = new Array;
            windowManager.load();
            windowManager.parent_object_id = '{parent_object_id}';
            windowManager.height = '{height}';
            if (windowManager.showControlPanel && windowManager.path=="") {
                var params = new Object;
                params["moveable"] = "0";
                params["resizeable"] = "0";
                params["height"] = window.innerHeight-windowManager.height-1;                
                params["has_close"] = "0";
                params["has_minimize"] = "0";
                params["has_maximize"] = "0";
                params["left"] = "1";
                params["top"] = "1";
                params["width"] = "250";
                params["resizeable_right"] = "1";
                params["hook"] = "6";                
                windowManager.show_window('Window_ApplicationNew','Application',params);
                windowManager.topObjects['Window_ApplicationNew'] = $O('Window_ApplicationNew','');
            }
            if (windowManager.showMainMenu && windowManager.path=="") {
            	var params = new Object;
            	params["width"] = window.innerWidth;
                new Ajax.Request("index.php", {
                    method: "post",
                    parameters: {ajax: true, object_id: windowManager.mainMenuName,
                                 hook: "show", arguments: Object.toJSON(params)},
                    onSuccess: function(transport) {
                        response = transport.responseText;
                        if (response != "") {                           
                            response_object = response.evalJSON();                                
                            div = document.createElement('div'); 
                            div.innerHTML = response_object["css"].concat(response_object["html"]); 
                            document.body.appendChild(div);
                            if (window.innerWidth!=null)
                            	div.setAttribute("width",window.innerWidth);
                            else
                            	div.setAttribute("width",document.body.clientWidth);
                            eval(response_object["javascript"].replace("<script>","").replace("<\/script>",""));
                            $O(windowManager.mainMenuName,"").build();
                        }
                    }
                });
            }
            if (windowManager.customObjectName!="" && windowManager.path=="") {
            	var params = new Object;
            	var div2 = document.createElement("div");
            	var frame = document.createElement("iframe");
            	frame.src = 'index.php?object_id='+windowManager.customObjectName;
                frame.style.width = "100%";
                frame.setAttribute("height","100%");
            	div2.style.position = "absolute";
            	div2.setAttribute('frameborder','0');
            	div2.setAttribute('marginwidth','0');
            	div2.setAttribute('marginheight','0');
            	div2.appendChild(frame);
            	document.body.appendChild(div2);
                if (windowManager.showMainMenu) {
                	if (window.innerWidth!=null) {
                		div2.style.top = getElementPosition(windowManager.mainMenuName).height;
                		div2.style.height = window.innerHeight-61-getElementPosition(windowManager.mainMenuName).height;                                	
                	} else {
                		frame.setAttribute("width",document.body.clientWidth);
                		frame.setAttribute("height",document.body.clientHeight-61);                                	
                	}
                }
            	div2.style.width = "99%";
            	div2.id = "customObjectDiv";
            }
            windowManager.topObjects['Window_TaskbarMain'] = $O('Window_TaskbarMain','');            
            window.onresize = windowManager.resize_window;
            windowManager.resize_window();  
            window.onmousemove = windowManager.onMouseMove;
            window.onmouseup = windowManager.onMouseUp;        
        }
        if (windowManager.path=="") {
	        for (c in windowManager.autorun) {
	        	if (typeof windowManager.autorun[c] != "function") {
	        		var args = new Object;
	        		args["object_text"] = windowManager.autorun[c];
	      			windowManager.show_window("Window_Window"+c.replace(/_/g,""),c,args,null,null,null,true);
	        	}
	        }
        }
        if (windowManager.path!="") {
    		var args = new Object;		
    		args["object_text"] = windowManager.path.split("/").pop();
    		args["path"]=windowManager.path;
    		args["hook"] = "setParams";
    		var elemid = "";
    		if (windowManager.fileId!="")
    			elemid = windowManager.fileId;
    		else
    			elemid = "ReferenceFiles_DocFlowApplication_Docs_";
    		var windowid = "Window_"+elemid.replace(/_/g,'');    		
    		windowManager.show_window(windowid,elemid,args,null,null,null); 
    		elemid = "ReferenceFiles_DocFlowApplication_Docs_List";
    		var windowid = "Window_"+elemid.replace(/_/g,'');    		
    		args["object_text"] = "Файлы";
    		args["hook"] = "3";
    		windowManager.show_window(windowid,elemid,args,null,null,null,true); 
        }
    }
}
else {
	windowManager = globalTopWindow.windowManager;
}
windowManager = $O("WindowManager","");
window.onmousemove = windowManager.onMouseMove;
window.onmouseup = windowManager.onMouseUp;        
webitem_classes = "{webitem_classes}";

var app_not_processed = false;

function processUser() {
    if (app_not_processed==true)
        return 0;
    var locked_objects = new Array;
    for (i in windowManager.windows) {
    	if (typeof windowManager.windows[i] != "function") {
    		var obj = $O(windowManager.windows[i].php_object_id,"");
    		if (obj!=null && !windowManager.windows[i].ignoreChanging && obj.readOnly!="true") {
    			locked_objects[locked_objects.length] = windowManager.windows[i].php_object_id;
    		}
    	}    	
    }
    var args = new Object;
    args["locked_objects"] = locked_objects.toObject();
    new Ajax.Request("index.php", {
        method:"post",
        parameters: {ajax: true, object_id: "Application",hook: '5',arguments: Object.toJSON(args)},
        onSuccess: function(transport) {
            app_not_processed = false;
            var data1 = trim(transport.responseText.replace("\n",""));
            if (data1!="") {
                var events = data1.split("|");
                var evc=0;
                for (evc=0;evc<events.length;evc++) {                	
                    var even = events[evc].split("~");
                    windowManager.raiseEvent(even[0],$Arr(even[1]));
                }
            }
        }
    });
}
var procUser = setInterval('processUser()',25000);