var Tabset = Class.create(Mailbox, {

    initTabset: function() {
        this.tabs = new Array;
        if (this.type==null)
            this.type = "up";
        if (this.type=="up" && this.node!=null)
            if ($I(this.node.id+"_tabset_bottom_table_down")!=0 && $I(this.node.id+"_tabset_bottom_table_down").parentNode!=null) {
                $I(this.node.id+"_tabset_bottom_table_down").parentNode.removeChild($I(this.node.id+"_tabset_bottom_table_down"));
            }
        if (this.type=="down" && this.node!=null)
            if ($I(this.node.id+"_tabset_top_table_up")!=0)
            $I(this.node.id+"_tabset_top_table_up").parentNode.removeChild($I(this.node.id+"_tabset_top_table_up"));
        var tabss = this.tabs_string.split(";");       
        if (tabss.length<2 && this.node.parentNode != null) {        	
            this.node.parentNode.removeChild(this.node);
        }
        else {
            for (var counter1=0;counter1<tabss.length;counter1++) {
                tab = tabss[counter1].split("|");
                this.addTab(tab[0],tab[1],tab[2]);
            }
        }
        if (this.active_tab!=null && this.active_tab!="")
        	this.activateTab(this.active_tab);
    },

    addTab: function(name,title,image) {       
    	if (this.node==null)
    		return 0;
        var top_row = $I(this.node.id+"_top_row");
        var top_row_spacer = $I(this.node.id+"_top_row_spacer");
        var left_top_corner = $I(this.node.id+"_left_top_corner").cloneNode(true);
        left_top_corner.id = this.node.id+"_left_top_corner_"+name;
        left_top_corner.setAttribute("class","tab");
        left_top_corner.style.display = "";
        top_row.insertBefore(left_top_corner,top_row_spacer);
        var top_line = $I(this.node.id+"_top_line").cloneNode(true);
        top_line.id = this.node.id+"_top_line_"+name;
        top_line.setAttribute("class","tab");
        top_line.style.display = "";
        top_row.insertBefore(top_line,top_row_spacer);
        var right_top_corner = $I(this.node.id+"_right_top_corner").cloneNode(true);
        right_top_corner.id = this.node.id+"_right_top_corner_"+name;
        right_top_corner.setAttribute("class","tab");
        right_top_corner.style.display = "";
        top_row.insertBefore(right_top_corner,top_row_spacer);
        
        var content_row = $I(this.node.id+"_content_row");
        var content_spacer = $I(this.node.id+"_content_spacer");
        var left_line = $I(this.node.id+"_left_line").cloneNode(true);
        left_line.id = this.node.id+"_left_line_"+name;
        left_line.setAttribute("class","tab");
        left_line.style.display = "";
        content_row.insertBefore(left_line,content_spacer);
        var content = $I(this.node.id+"_content").cloneNode(true);
        content.id = this.node.id+"_content_"+name;
        content.setAttribute("class","tab");
        content.style.display = "";        
        content.setAttribute("onclick","$O('"+this.object_id+"','').onClick(event,'"+name+"')");
        content.setAttribute("onmouseover","$O('"+this.object_id+"','').onMouseOver(event,'"+name+"')");
        content.setAttribute("onmouseout","$O('"+this.object_id+"','').onMouseOut(event,'"+name+"')");
        content.setAttribute("oncontextmenu","$O('"+this.object_id+"','').onContextMenu(event,'"+name+"')");
        var content_image = getElementById(content,this.node.id+"_image");
        content_image.id = this.node.id+"_image_"+name;
        content_image.src = image;
        content_image.setAttribute("class","tab");
        content_image.setAttribute("onclick","$O('"+this.object_id+"','').onClick(event,'"+name+"')");
        content_image.setAttribute("onmouseover","$O('"+this.object_id+"','').onMouseOver(event,'"+name+"')");
        content_image.setAttribute("onmouseout","$O('"+this.object_id+"','').onMouseOut(event,'"+name+"')");
        content_image.setAttribute("oncontextmenu","$O('"+this.object_id+"','').onContextMenu(event,'"+name+"')");
        var content_text = getElementById(content,this.node.id+"_text");
        content_text.id = this.node.id+"_text_"+name;
        content_text.setAttribute("onclick","$O('"+this.object_id+"','').onClick(event,'"+name+"')");
        content_text.innerHTML = title;
        content_text.setAttribute("nowrap","");
        content_text.setAttribute("class","tab");
        content_text.setAttribute("onmouseover","$O('"+this.object_id+"','').onMouseOver(event,'"+name+"')");
        content_text.setAttribute("onmouseout","$O('"+this.object_id+"','').onMouseOut(event,'"+name+"')");
        content_text.setAttribute("oncontextmenu","$O('"+this.object_id+"','').onContextMenu(event,'"+name+"')");
        content_row.insertBefore(content,content_spacer);

        var right_line = $I(this.node.id+"_right_line").cloneNode(true);
        right_line.id = this.node.id+"_right_line_"+name;
        right_line.setAttribute("class","tab");
        right_line.style.display = "";
        content_row.insertBefore(right_line,content_spacer);

        var bottom_row = $I(this.node.id+"_bottom_row");
        var bottom_row_spacer = $I(this.node.id+"_bottom_row_spacer");
        var bottom_line = $I(this.node.id+"_bottom_line").cloneNode(true);
        bottom_line.id = this.node.id+"_bottom_line_"+name;
        bottom_line.setAttribute("class","tab");
        bottom_line.style.display = "";
        bottom_row.insertBefore(bottom_line,bottom_row_spacer);
        this.tabs[name] = name;
    },

    activateTab: function(tab) {
    	if (this.node==null)
    		return 0;
        if (this.active_tab!=null) {
            var left_top_corner = $I(this.node.id+"_left_top_corner_"+this.active_tab);
            if (left_top_corner==0)
            	return 0;
            left_top_corner.setAttribute("class","tab");
            var top_line = $I(this.node.id+"_top_line_"+this.active_tab);
            top_line.setAttribute("class","tab");
            var right_top_corner = $I(this.node.id+"_right_top_corner_"+this.active_tab);
            right_top_corner.setAttribute("class","tab");
            var left_line =$I(this.node.id+"_left_line_"+this.active_tab);
            left_line.setAttribute("class","tab");
            var content = $I(this.node.id+"_content_"+this.active_tab);
            content.setAttribute("class","tab");
            var content_image = getElementById(content,this.node.id+"_image_"+this.active_tab);
            content_image.setAttribute("class","tab");
            var content_text = getElementById(content,this.node.id+"_text_"+this.active_tab);
            content_text.setAttribute("class","tab");
            var right_line = $I(this.node.id+"_right_line_"+this.active_tab);
            right_line.setAttribute("class","tab");
            var bottom_line = $I(this.node.id+"_bottom_line_"+this.active_tab);
            bottom_line.setAttribute("class","active_tab");
            var bottom_line_img = bottom_line.getElementsByTagName("IMG");
            bottom_line_img[0].src = this.skinPath+"images/Tabs/bl.gif";
        }
        var left_top_corner = $I(this.node.id+"_left_top_corner_"+tab);
        left_top_corner.setAttribute("class","active_tab");
        var top_line = $I(this.node.id+"_top_line_"+tab);
        top_line.setAttribute("class","active_tab");
        var right_top_corner = $I(this.node.id+"_right_top_corner_"+tab);
        right_top_corner.setAttribute("class","active_tab");
        var left_line =$I(this.node.id+"_left_line_"+tab);
        left_line.setAttribute("class","active_tab");
        var content = $I(this.node.id+"_content_"+tab);
        content.setAttribute("class","active_tab");
        var content_image = getElementById(content,this.node.id+"_image_"+tab);
        content_image.setAttribute("class","active_tab");
        var content_text = getElementById(content,this.node.id+"_text_"+tab);
        content_text.setAttribute("class","active_tab");
        content_text.style.fontWeight = 'normal';
        content_text.style.color = '';        
        var right_line = $I(this.node.id+"_right_line_"+tab);
        right_line.setAttribute("class","active_tab");
        var bottom_line = $I(this.node.id+"_bottom_line_"+tab);
        bottom_line.setAttribute("class","active_tab");
        var bottom_line_img = bottom_line.getElementsByTagName("IMG");        
        bottom_line_img[0].src = this.skinPath+"images/spacer.gif";
        this.active_tab = tab;        
    },

    onMouseOver: function(event,tab) {	
        var left_top_corner = $I(this.node.id+"_left_top_corner_"+tab);
        left_top_corner.setAttribute("class","tab_hover");
        var top_line = $I(this.node.id+"_top_line_"+tab);
        top_line.setAttribute("class","tab_hover");
        var right_top_corner = $I(this.node.id+"_right_top_corner_"+tab);
        right_top_corner.setAttribute("class","tab_hover");
        var left_line =$I(this.node.id+"_left_line_"+tab);
        left_line.setAttribute("class","tab_hover");
        var content = $I(this.node.id+"_content_"+tab);
        content.setAttribute("class","tab_hover");
        var content_image = getElementById(content,this.node.id+"_image_"+tab);
        content_image.setAttribute("class","tab_hover");
        var content_text = getElementById(content,this.node.id+"_text_"+tab);
        content_text.setAttribute("class","tab_hover");
        var right_line = $I(this.node.id+"_right_line_"+tab);
        right_line.setAttribute("class","tab_hover");
        var bottom_line = $I(this.node.id+"_bottom_line_"+tab);
        bottom_line.setAttribute("class","tab_hover");
        var bottom_line_img = bottom_line.getElementsByTagName("IMG");
        bottom_line_img[0].src = this.skinPath+"images/spacer.gif";
    },

    onMouseOut: function(event,tab) {
    	var tab_style = "tab";
        if (tab==this.active_tab)
            tab_style = "active_tab";
        var left_top_corner = $I(this.node.id+"_left_top_corner_"+tab);
        left_top_corner.setAttribute("class",tab_style);
        var top_line = $I(this.node.id+"_top_line_"+tab);
        top_line.setAttribute("class",tab_style);
        var right_top_corner = $I(this.node.id+"_right_top_corner_"+tab);
        right_top_corner.setAttribute("class",tab_style);
        var left_line =$I(this.node.id+"_left_line_"+tab);
        left_line.setAttribute("class",tab_style);
        var content = $I(this.node.id+"_content_"+tab);
        content.setAttribute("class",tab_style);
        var content_image = getElementById(content,this.node.id+"_image_"+tab);
        content_image.setAttribute("class",tab_style);
        var content_text = getElementById(content,this.node.id+"_text_"+tab);
        content_text.setAttribute("class",tab_style);
        var right_line = $I(this.node.id+"_right_line_"+tab);
        right_line.setAttribute("class",tab_style);
        var bottom_line = $I(this.node.id+"_bottom_line_"+tab);
        bottom_line.setAttribute("class",tab_style);
        var bottom_line_img = bottom_line.getElementsByTagName("IMG");
        bottom_line_img[0].src = this.skinPath+"images/spacer.gif";
    },


    onClick: function(event,tab) {
        this.activateTab(tab);
    },
    
    PUSH_TAB_processEvent: function(params) {
    	if (params["object_id"]==this.object_id) {    		
            var content_text = $I(this.node.id+"_text_"+params["tab"]);
            if (content_text!=0) {
        		new Effect.Morph(this.node.id+"_text_"+params["tab"], { style: "color:#FF0000;fontWeight:bold", opacity:2 });
            	//content_text.style.color = "#FF0000";
            	//content_text.style.fontWeight = "bold";
            }
    	}
    } 
});