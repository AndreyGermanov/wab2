var Menu = Class.create(Entity, {
    
    initialize: function($super,object_id,instance_id) {       
        $super(object_id,instance_id);
        this.items = new Array;
        this.subMenus = new Array;
    },
    
    fillItems: function() {
        if (this.data!=null && this.data!="") {
            var arr = this.data.split("|");
            var c=0;
            for (c=0;c<arr.length;c++) {
                var parts = arr[c].split("~");
                c1 = this.items.length;
                this.items[c1] = new Array;
                this.items[c1]["id"] = parts[0];
                this.items[c1]["text"] = parts[1];
                this.items[c1]["image"] = parts[2];
                if (parts[3]!=null)
                    this.items[c1]["properties"] = parts[3];
                if (parts[4]!=null)
                    this.items[c1]["image_properties"]= parts[4];
                
            }            
        }
    },
    
    build: function() {
        if (this.table==null)
            return 0;
        if (this.items.length==0)
            this.fillItems();
        if (this.items.length==0)
            return 0;
        if (this.width!="" && this.width!=null) {            
            this.node.setAttribute("width",this.width);
        }
        var c=0;
        var tr=null;
        var td=null;
        var img=null;
        var arr = new Array();
        if (this.properties!=null && this.properties!="") {
            arr = this.properties.split("^");
            for (c=0;c<arr.length;c++) {
                parts = arr[c].split("=");
                if (parts[0]=="class") {
                	if (navigator.appName=="Microsoft Internet Explorer") {                		
                		this.node.className = parts[1];
                	}
                	else
                		this.node.setAttribute(parts[0],parts[1]);
                } else {
            		this.node.setAttribute(parts[0],parts[1]);                	
                }
            }
        }
        if (this.table_properties!="" && this.table_properties!=null) {
            arr = this.table_properties.split("^");
            for (c=0;c<arr.length;c++) {
                parts = arr[c].split("=");
            	if (navigator.appName=="Microsoft Internet Explorer") {
            		if (parts[0]=="class") {
    	                this.tbody.className = parts[1];                		
                	} else {                        		
                		this.tbody.setAttribute(parts[0],parts[1]);
                	}
            	} else {
            		if (parts[0]=="class") {
    	                this.table.className = parts[1];                		
                	} else {                 	
                		this.table.setAttribute(parts[0],parts[1]);
                	}            		
            	}
            }
        }
        if (this.horizontal) {
            tr = document.createElement("TR");
            tr.setAttribute("valign","top");
            this.tbody.appendChild(tr);
        }
        var c1=0;
        for (c=0;c<this.items.length;c++) {
            var item = this.items[c];
            if (!this.horizontal) {
                tr = document.createElement("TR");
                tr.setAttribute("valign","top");
                this.tbody.appendChild(tr);
            }
            td = document.createElement("td");
            td.setAttribute("class",this.object_id+"_menu_item");
            td.id = this.node.id+"_"+item["id"];
            td.setAttribute("item_id", td.id);
            td.setAttribute("object",this.object_id);
            td.setAttribute("nowrap","");
            if (item["properties"]!=null && item["properties"]!="") {
                arr = item['properties'].split("^");
                for (c1=0;c1<arr.length;c1++) {
                    parts = arr[c1].split("=");
                    if (parts[0]=="class") {
                    	if (navigator.appName=="Microsoft Internet Explorer") {
        	                td.className = parts[1];                    		
                    	}
                    }
                    td.setAttribute(parts[0],parts[1]);
                }
            }
            if (item["image"]!=null && item["image"]!="") {
                img = document.createElement("IMG");
                img.id = item["id"]+"_image";
                img.setAttribute("item_id",td.id);
                img.setAttribute("align","left");
                img.setAttribute("object",this.object_id);
                img.src = item["image"];
                if (item["image_properties"]!=null && item["image_properties"]!="") {
                    arr = item['image_properties'].split("^");
                    for (c1=0;c1<arr.length;c1++) {
                        var parts = arr[c1].split("=");
                        if (parts[0]=="class") {
                        	if (navigator.appName=="Microsoft Internet Explorer") {
                        		img.className = parts[1];
                        	}
                        }
                        img.setAttribute(parts[0],parts[1]);
                    }                
                }
                td.appendChild(img);
            }
            var span = document.createElement("SPAN");
            span.setAttribute("item_id",td.id);
            span.innerHTML = item.text;

            td.appendChild(span);
            this.items[c]["node"] = td;
            tr.appendChild(td);
            if (!this.horizontal) {
                this.tbody.appendChild(tr);
            }
            var a=null;
            for (a in this)
            {
                for (var counter1=0;counter1<event_types.length;counter1++)
                {
                    var e = event_types[counter1];
                    if (a=="menu_"+e) {
                        if (td.getAttribute(e.toLowerCase().replace("on","")) == null) {
                            td.setAttribute("object",this.object_id);
                            td.setAttribute(e.toLowerCase().replace("on",""),a);
                            td.observe(e.toLowerCase().replace("on",""),this.addHandler);
                        }
                        if (img!=null && img.getAttribute(e.toLowerCase().replace("on",""))==null) {
                        	img.setAttribute("object",this.object_id);
                            img.setAttribute(e.toLowerCase().replace("on",""),a);
                            img.observe(e.toLowerCase().replace("on",""),this.addHandler);
                        }                        
                        if (span!=null && span.getAttribute(e.toLowerCase().replace("on",""))==null) {
                        	span.setAttribute("object",this.object_id);
                            span.setAttribute(e.toLowerCase().replace("on",""),a);
                            span.observe(e.toLowerCase().replace("on",""),this.addHandler);
                        }                        
                    } else {
                        if (a==item["id"]+"_"+e)
                        {
                            if (td.getAttribute(e.toLowerCase().replace("on","")) == null)
                            {
                            	td.setAttribute("object",this.object_id);
                                td.setAttribute(e.toLowerCase().replace("on",""),a);
                                td.observe(e.toLowerCase().replace("on",""),this.addHandler);
                            }
                            if (img!=null && img.getAttribute(e.toLowerCase().replace("on",""))==null) {
                            	img.setAttribute("object",this.object_id);
                                img.setAttribute(e.toLowerCase().replace("on",""),a);
                                img.observe(e.toLowerCase().replace("on",""),this.addHandler);
                            }
                            if (span!=null && span.getAttribute(e.toLowerCase().replace("on",""))==null) {
                            	span.setAttribute("object",this.object_id);
                                span.setAttribute(e.toLowerCase().replace("on",""),a);
                                span.observe(e.toLowerCase().replace("on",""),this.addHandler);
                            }
                        }
                    }
                }
            }
        }
        if (this.horizontal) {
            td = document.createElement("td");
            td,innerHTML = "&nbsp;";
            td.setAttribute("width","100%");
            tr.appendChild(td);
        }
    },
    
    showSubMenu: function(x,y,subMenuId,event) {
        var subMenu = $O(subMenuId,'');
        if (subMenu!=null) {
            if (subMenu.items.length==0) {
                subMenu.build();
            }
            this.subMenus[subMenuId] = subMenu;
            var c=null;
            for (c in this.subMenus) {  
                if (this.subMenus[c].node!=null && c!=subMenuId) {
                    this.subMenus[c].node.style.display = 'none';
                    this.raiseEvent("HIDE_MENU",$Arr("object_id="+c));
                }
            }           
            this.currentSubMenu = subMenuId;
            this.subMenus[subMenuId].node.style.display = '';
            this.subMenus[subMenuId].node.style.opacity = 0;            
    		new Effect.Opacity(this.subMenus[subMenuId].node.id, { from: 0.0, to: 1.0, duration: 0.5 });
            this.subMenus[subMenuId].node.style.left = x;
            this.subMenus[subMenuId].node.style.top = y;        
            this.subMenus[subMenuId].parent_object_id = this.object_id;
            event.cancelBubble = true;
            return subMenu;
        }
        return 0;
    },

    showEntitySubMenu: function(x,y,subMenuId) {
        var subMenu = $O(subMenuId,'');
        if (subMenu==0 || subMenu==null) {
            var obj = this;
            var entityName = subMenuId.replace("_Menu","");
            var adapter = this.adapter;
            var condition = "#EntityField@parent.name='"+entityName+"' AND #BooleanField@isPublic=1";
            var sort = this.sort;
            var className = this.className;
            var args = new Object;
            args["adapter"] = adapter;
            args["condition"] = condition;
            args["sort"] = sort;
            args["textField"] = this.textField;
            args["imageField"] = this.imageField;
            args["className"] = className;
            args["itemProperties"] = this.itemProperties;
            args["imageProperties"] = this.imageProperties;            
            new Ajax.Request("index.php",
            {
                method: "post",
                parameters: {ajax: true, object_id: subMenuId,
                             hook: 'show', arguments: Object.toJSON(args)},
                onSuccess: function(transport,superfunc) {
                        var response = transport.responseText.toString();                        
                        if (response!="") {
                            var response_object = response.evalJSON();
                            var div = document.createElement('div'); 
                            div.innerHTML = response_object["css"].concat(response_object["html"]);
                            if (ignoreChanging) {
                                div.setAttribute("ignoreChanging","true");
                            }
                            document.body.appendChild(div);
                            eval(response_object["javascript"].replace("<script>","").replace("<\/script>",""));                        
                            obj.showSubMenu(x,y,subMenuId);
                    }
                }
           });            
        } else
            this.showSubMenu(x,y,subMenuId);        
    },

    HIDE_MENU_processEvent: function(params) {
        if (params["object_id"]==this.parent_object_id) {
            if (this.node.style.display=="") {
                this.node.style.display = 'none';
                this.raiseEvent("HIDE_MENU",$Arr("object_id="+this.object_id));
            }
        }        
    },
    
    ON_CLICK_processEvent: function(params) {    	
    	this.raiseEvent("HIDE_MENU",$Arr("object_id="+this.object_id));
    }    
}); 