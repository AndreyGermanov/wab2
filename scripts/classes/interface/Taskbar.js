var Taskbar = Class.create(Entity, {
    postInit: function() {                   
        this.taskbar_button = getElementById(this.node,this.node.id+"_taskbar_button");
        this.taskbar_whitespace = getElementById(this.node,this.node.id+"_taskbar_whitespace");
        this.active_button = null;
    },

    activateButton: function(window_id,image,text) {
      if (this.active_button!=null)
            this.active_button.setAttribute("class",'taskbar');
      var cur_button = getElementById(this.node,this.node.id+"_"+getClientId(window_id));
      if (cur_button!=0) {
        cur_button.setAttribute("class",'taskbar_selected');
        this.active_button = cur_button;
        var button_id = cur_button.id;
        $I(button_id+"_taskbar_button_text").style.fontWeight = "normal";        
        $I(button_id+"_taskbar_button_text").style.color = "";        
      }
      else {
        var new_button = this.taskbar_button.cloneNode(true);
        new_button.setAttribute("window_id",window_id);
        new_whitespace = this.taskbar_whitespace.cloneNode(true);
        new_whitespace.id = this.node.id+"_new_whitespace";
        new_button.id = this.node.id+"_"+getClientId(window_id);
        new_button.setAttribute("class","taskbar_selected");
        new_button.style.display = '';
        new_whitespace.style.display = '';
        getElementById(new_button,this.node.id+"_taskbar_button_image_td").setAttribute("window_id",window_id);
        getElementById(new_button,this.node.id+"_taskbar_button_image_td").setAttribute("object",this.object_id);
        getElementById(new_button,this.node.id+"_taskbar_button_image_td").setAttribute("button_id",new_button.id);
        getElementById(new_button,this.node.id+"_taskbar_button_image_td").setAttribute("click","onClick");
        getElementById(new_button,this.node.id+"_taskbar_button_image_td").observe("click",this.addHandler);        
        getElementById(new_button,this.node.id+"_taskbar_button_image_td").id = new_button.id+"_taskbar_button_td";
        getElementById(new_button,this.node.id+"_taskbar_button_image").setAttribute("window_id",window_id);
        getElementById(new_button,this.node.id+"_taskbar_button_image").setAttribute("object",this.object_id);
        getElementById(new_button,this.node.id+"_taskbar_button_image").setAttribute("button_id",new_button.id);
        getElementById(new_button,this.node.id+"_taskbar_button_image").setAttribute("click","onClick");
        getElementById(new_button,this.node.id+"_taskbar_button_image").observe("click",this.addHandler);        
        getElementById(new_button,this.node.id+"_taskbar_button_image").setAttribute("src",image);
        getElementById(new_button,this.node.id+"_taskbar_button_image").id = new_button.id+"_taskbar_button_image";
        getElementById(new_button,this.node.id+"_taskbar_button_text").setAttribute("window_id",window_id);
        getElementById(new_button,this.node.id+"_taskbar_button_text").setAttribute("object",this.object_id);
        getElementById(new_button,this.node.id+"_taskbar_button_text").setAttribute("button_id",new_button.id);
        getElementById(new_button,this.node.id+"_taskbar_button_text").setAttribute("click","onClick");
        getElementById(new_button,this.node.id+"_taskbar_button_text").observe("click",this.addHandler);
        getElementById(new_button,this.node.id+"_taskbar_button_text").innerHTML = text;
        getElementById(new_button,this.node.id+"_taskbar_button_text").id = new_button.id+"_taskbar_button_text";
        if (getElementById(this.node,this.node.id+"_new_whitespace")!=0)
            this.taskbar_button.parentNode.removeChild(getElementById(this.node,this.node.id+"_new_whitespace"));
        this.taskbar_button.parentNode.appendChild(new_button);
        this.taskbar_button.parentNode.appendChild(new_whitespace);
        $O(window_id,'').taskbarButton = new_button;
        new_button.setAttribute("button_id",new_button.id);
        this.active_button = new_button;
      }      
    },

    removeButton: function(window_id) {
        getElementById(this.node,this.node.id+"_"+getClientId(window_id)).parentNode.removeChild(getElementById(this.node,this.node.id+"_"+getClientId(window_id)));
    },

    onClick: function(event) {
        var elem = eventTarget(event);
        var button_id = eventTarget(event).getAttribute("button_id");
        $I(button_id+"_taskbar_button_text").style.fontWeight = "normal";        
        $I(button_id+"_taskbar_button_text").style.color = "";        
        var active_button_id = this.active_button.getAttribute("button_id");
        var window_id = elem.getAttribute("window_id");
        if (button_id == active_button_id) {
        	var win = $O(window_id,'');
        	if (win.node.style.display=='')
        		win.node.style.display='none';
        	else
        		windowManager.activate_window(window_id);
        } else                
        	windowManager.activate_window(window_id);
    }
});