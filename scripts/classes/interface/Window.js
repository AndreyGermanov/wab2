var Window = Class.create(Entity, { 
    postInit: function() {
        this.id = this.full_object_id;       
        this.object_node = globalTopWindow.getElementById(this.node,getClientId(this.id+"_inner_frame")).contentDocument;
        this.frame = getElementById(this.node,getClientId(this.full_object_id+"_inner_frame"));
        this.left = 200;
        this.topy = 200;
        this.width = 350;
        this.height = 220;
        this.has_close = true;
        this.has_minimize = true;
        this.has_maximize = true;
        this.moveable = true;
        this.resizeable = true;
        this.min_left = 1;
        this.min_top = 1;
        this.max_right = globalTopWindow.innerWidth;
        this.max_bottom = globalTopWindow.innerHeight;
        this.node.style.position = "absolute";
    },

    getClassName: function() {
        return "Window";
    },

    add: function() {
        var topWindow = globalTopWindow;
        var wm = getWindowManager();
        this.node.setAttribute('is_window','true');
        
        var args = new Object;
        args[0] = this.objectArguments;        
		if (this.objectArguments!="")
			this.frame.setAttribute("src",'index.php?window_id='+this.object_id+'&object_id='+this.php_object_id+'&hook=1&arguments='+escape(Object.toJSON(this.objectArguments)));
		else
			this.frame.setAttribute("src",'index.php?window_id='+this.object_id+'&object_id='+this.php_object_id+'&hook=1');
		
		if (this.dock_left=="1") {
			if ($O("ApplicationNew","")!=null)
				this.left = parseInt($O("ApplicationNew","").node.style.width);
			else
				this.left = 1;
		}
		if (this.dock_right=="1") {			
			if ($O("ApplicationNew","")!=null)
				this.width = '99%';
			else
				this.width = '99%';
		}
		if (this.dock_top=="1") {
			if ($O(wm.mainMenuName,"")!=null)
                this.topy = parseInt(getElementPosition($O(wm.mainMenuName,"").node.id).height); 
			else
				this.topy = 1;
		}
		if (this.dock_bottom=="1") {
				this.topy = parseInt(topWindow.innerHeight)-60-parseInt(this.height);				
		}
			
        if ($O("ApplicationNew","")!=null) {
            if (parseInt(this.width) > parseInt(topWindow.innerWidth)-parseInt($O("ApplicationNew","").node.style.width)) {
                this.left = parseInt($O("ApplicationNew","").node.style.width);
                this.width = parseInt(topWindow.innerWidth)-parseInt($O("ApplicationNew","").node.style.width);
            }
            if (parseInt(this.height) > parseInt(topWindow.innerHeight)-70) {
                this.topy = parseInt($O("ApplicationNew","").node.style.top);
                this.height = parseInt(topWindow.innerHeight)-70;
            }
        }
        this.topy = parseInt(this.topy);
        this.height = parseInt(this.height);
        if ($O(wm.mainMenuName,"")!=null) {
            if (parseInt(this.topy)<parseInt(getElementPosition($O(wm.mainMenuName,"").node.id).height))
                this.topy = parseInt(getElementPosition($O(wm.mainMenuName,"").node.id).height);            
            if (parseInt(this.topy)+parseInt(this.height) > parseInt(topWindow.innerHeight)-60)
                this.height = parseInt(topWindow.innerHeight)-61-parseInt(this.topy);           
        }
        if (parseInt(this.topy)+parseInt(this.height) > parseInt(topWindow.innerHeight)-60)
            this.height = parseInt(topWindow.innerHeight)-70-parseInt(this.topy);
        if (parseInt(this.topy)<1)
        	this.topy = 1;
        this.node.style.left = this.left;
        this.node.style.top = this.topy;
        this.node.style.width = this.width;
        this.node.style.height = this.height;
		
        if (this.has_close==1)
            topWindow.getElementById(this.node,this.node.id+"_has_close").style.display = "";
        else
            topWindow.getElementById(this.node,this.node.id+"_has_close").style.display = "none";

        if (this.has_minimize==1)
            topWindow.getElementById(this.node,this.node.id+"_has_minimize").style.display = "";
        else
            topWindow.getElementById(this.node,this.node.id+"_has_minimize").style.display = "none";
        if (this.has_maximize==1) {
            topWindow.getElementById(this.node,this.node.id+"_has_maximize").style.display = "";
            this.node.setAttribute('can_maximize','true');
        }
        else {  
            topWindow.getElementById(this.node,this.node.id+"_has_maximize").style.display = "none";
            this.node.setAttribute('can_maximize','false');
        }
        
        if (this.resizeable==1) {
            topWindow.getElementById(this.node,this.node.id+"_toprow").style.cursor = 's-resize';
            topWindow.getElementById(this.node,this.node.id+"_firstcol").style.cursor = 'e-resize';
            topWindow.getElementById(this.node,this.node.id+"_lastcol").style.cursor = 'e-resize';
            topWindow.getElementById(this.node,this.node.id+"_lastrow").style.cursor = 's-resize';
        }
        else {
            topWindow.getElementById(this.node,this.node.id+"_toprow").style.cursor = 'auto';
            topWindow.getElementById(this.node,this.node.id+"_firstcol").style.cursor = 'auto';
            topWindow.getElementById(this.node,this.node.id+"_lastcol").style.cursor = 'auto';
            topWindow.getElementById(this.node,this.node.id+"_lastrow").style.cursor = 'auto';
        }
        if (this.resizeable_left==1) {
            topWindow.getElementById(this.node,this.node.id+"_firstcol").style.cursor = 'e-resize';
        }
        if (this.resizeable_right==1) {
            topWindow.getElementById(this.node,this.node.id+"_lastcol").style.cursor = 'e-resize';
        }
        if (this.resizeable_top==1) {
            topWindow.getElementById(this.node,this.node.id+"_toprow").style.cursor = 's-resize';
        }
        if (this.resizeable_bottom==1) {
            topWindow.getElementById(this.node,this.node.id+"_lastrow").style.cursor = 's-resize';
        }

            if(this.min_left<1)
                this.min_left = 1;

            if(this.min_top<1)
                this.min_top = 1;
        if (this.has_border==0) { 
            $I(this.node.id+"_toprow").style.display = "none";
            $I(this.node.id+"_lastrow").style.display = "none";
            $I(this.node.id+"_header").style.display = "none";
            $I(this.node.id+"_firstcol").style.display = "none";
            $I(this.node.id+"_lasstcol").style.display = "none";
            this.node.setAttribute('can_maximize','false');
        }
        wm.addWindow(this);
    },

    mouseDown: function(event) {        
        var wm = getWindowManager();
        wm.mouse_press = true;
        this.node.setAttribute("curx",event.screenX);
        this.node.setAttribute("cury",event.screenY);
        wm.moving_elem = this;
        wm.moving_elem.addx = event.screenX-parseInt(getElementPosition(wm.moving_elem.node.id).left);
        wm.moving_elem.addy = event.screenY-parseInt(getElementPosition(wm.moving_elem.node.id).top);        
        var active_window = "";
        if (wm.active_elem!=null) {
        	active_window = wm.active_elem.getAttribute("object");
        }
        if (this.object_id!="Window_ApplicationNew" && active_window!=this.id)
            wm.activate_window(this.id);
    },

    headertext_onMouseDown: function(event) {
        if (this.node.getAttribute('maximized')=="true")
            return 0;
        this.mouseDown(event);
        getWindowManager().drag_style = "move";
       return 0;
    },

    lastcol_onMouseDown: function(event) {
        if (this.node.getAttribute('maximized')=="true")
            return 0;
        this.mouseDown(event);
        getWindowManager().drag_style = "right";
       return 0;
    },

    firstcol_onMouseDown: function(event) {
        if (this.node.getAttribute('maximized')=="true")
            return 0;
        this.mouseDown(event);
        getWindowManager().drag_style = "left";
       return 0;
    },

    lastrow_onMouseDown: function(event) {
        if (this.node.getAttribute('maximized')=="true")
            return 0;
        this.mouseDown(event);
        getWindowManager().drag_style = "bottom";
       return 0;
    },

    toprow_onMouseDown: function(event) {
        if (this.node.getAttribute('maximized')=="true")
            return 0;
        this.mouseDown(event);
        getWindowManager().drag_style = "top";
       return 0;
    },

    min_onMouseOver: function(event) {
    	var elem = eventTarget(event);
        elem.src = this.skinPath+"images/Window/min_hover.jpg";
    },

    max_onMouseOver: function(event) {
    	var elem = eventTarget(event);
        elem.src = this.skinPath+"images/Window/restore_hover.jpg";
    },

    close_onMouseOver: function(event) {
    	var elem = eventTarget(event);
        elem.src = this.skinPath+"images/Window/close_hover.jpg";
    },

    min_onMouseOut: function(event) {
    	var elem = eventTarget(event);
    	elem.src = this.skinPath+"images/Window/min.jpg";
    },

    max_onMouseOut: function(event) {
    	var elem = eventTarget(event);
        elem.src = this.skinPath+"images/Window/restore.jpg";
    },

    close_onMouseOut: function(event) {
    	var elem = eventTarget(event);
        elem.src = this.skinPath+"images/Window/close.jpg";
    },

    headertext_onDblClick: function(event) {
            var wm = getWindowManager();
            wm.activate_window(this.id);
            wm.maximize_windows();
    },

    min_onClick: function(event) {
        this.node.style.display = 'none';
    },

    max_onClick: function(event) {
           var wm = getWindowManager();
           wm.activate_window(this.id);           
           wm.maximize_windows();
    },

    close_onClick: function(event) {             
            var wm = getWindowManager();
            wm.remove_window(this.id);
    },
    
    PUSH_WINDOW_processEvent: function(params) {
    	if (params["object_id"] == this.object_id) {
    		if (this.taskbarButton!=null) {
    			$I(this.taskbarButton.id+"_taskbar_button_text").style.fontWeight = "bold";
    			$I(this.taskbarButton.id+"_taskbar_button_text").style.color = "#FF0000";
    		}
    	}
    },
    
    headertext_onContextMenu: function(event) {
    	var elem = eventTarget(event);
        var elem_id = elem.id;
        elem_id_start = elem_id.split("_").shift();
        elem_id_end = elem_id.split("_").pop();
        event = event || window.event;
        event.cancelBubble = true;
		var objectid = elem.getAttribute("object");
		var instanceid = elem.getAttribute("instance");
		$O(objectid,instanceid).show_context_menu("WindowContextMenu_"+this.module_id+"_"+elem_id_end,cursorPos(event).x-10,cursorPos(event).y-10,this.object_id);
        if (event.preventDefault)
            event.preventDefault();
         else
            event.returnValue= false;
        return false;		
    }    
});