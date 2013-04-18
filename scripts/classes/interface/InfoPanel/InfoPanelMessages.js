var InfoPanelMessages = Class.create(Entity, {
	
	build: function() {
		this.tbl.setAttribute("width",'100%');
		this.tbl.setAttribute("cellpadding","2");
		var params = new Array;
		params["object_id"] = this.object_id;
		params["text"] = "Добро пожаловать <font color='#FF0000'>"+this.appUser+"</font> !";
		params["notscroll"] = "1";
		this.raiseEvent("SEND_MESSAGE",params);
	},

	selectRow: function(event) {
		eventTarget(event).style.fontWeight = 'normal';
		eventTarget(event).style.color = '';
		wm = getWindowManager();
		wm.activate_window("InfoPanelNew");
		var tabset = $O("InfoPanelTabset");
		var tabnode = $I(tabset.node.id+"_text_messages");
		tabnode.style.color = '';
		tabnode.style.fontWeight = '';
	},
	
	filterMessages: function(type) {
		var elems = this.tbl.getElementsByTagName("tr");
		var c=null;
		for (c in elems) {
			if (typeof elems[c] != "function" && typeof(elems[c])=="object") {
				if (type=="all")
					elems[c].style.display = '';
				else {
					if (elems[c].getAttribute("type")==type)
						elems[c].style.display = '';
					else
						elems[c].style.display = 'none';
				}
			}
		}		
	},
	
	SEND_MESSAGE_processEvent: function(params) {
		if (params["object_id"] == this.object_id) {
			
			var row = document.createElement("tr");
			row.setAttribute("object",this.object_id);
			var type = "info";
			if (params["type"]!=null)
				type = params["type"];
			row.setAttribute("type",type);
			row.setAttribute("valign","top");			
			var textcolumn = document.createElement("td");
			textcolumn.setAttribute("object",this.object_id);
			textcolumn.setAttribute("type",type);
			textcolumn.setAttribute("width","100%");
			textcolumn.setAttribute("class","infoPanel");
			textcolumn.setAttribute("click","selectRow");
			textcolumn.observe("click",this.addHandler);
			if (params["color"]!=null)
				textcolumn.style.color = params["color"];
			var text = params["text"].replace(/xoxox/g,',');
			var d = new Date();
			var day = d.getDate();
			if (day<10)
				day = "0"+day;
			var month = d.getMonth()+1;
			if (month<10)
				month = "0"+month;
			var hours = d.getHours();
			if (hours<10)
				hours = "0"+hours;
			var minutes = d.getMinutes();
			if (minutes<10)
				minutes = "0"+minutes;
			var seconds = d.getSeconds();
			if (seconds<10)
				seconds = "0"+seconds;
			
			var dateStr = day+"."+month+"."+d.getFullYear()+" "+hours+":"+minutes+":"+seconds; 
			textcolumn.innerHTML = dateStr+" - "+text;
			textcolumn.style.fontWeight = 'bold';			
			row.appendChild(textcolumn);
			var elems = this.tbl.getElementsByTagName("TR");
			if (elems.length==0)
				this.tbl.appendChild(row);
			else {
				this.tbl.insertBefore(row,elems[0]);
			}
			this.raiseEvent("PUSH_WINDOW",$Arr("object_id=InfoPanelNew"));
			this.raiseEvent("PUSH_TAB",$Arr("object_id=InfoPanelTabset,tab=messages"));
			if (params["notscroll"]==null)
				textcolumn.scrollIntoView(true);				
			if (params["activate"]!=null) {
				var wm = getWindowManager();
				wm.activate_window("InfoPanelNew");
				var tabset = $O("InfoPanelTabset","");
				if (tabset!=null)
					tabset.activateTab("messages");
				if (params["color"]==null)
					textcolumn.style.color = "#FF0000"; 				
			}
			this.filterMessages($O("InfoPanel_showMessagesList",'').getValue());
		}
	}
});