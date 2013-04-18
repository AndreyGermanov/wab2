var InfoPanelEvents = Class.create(Entity, {
	
	build: function() {
		var elems = this.tbl.getElementsByTagName("TBODY");
		if (elems[0]!=null)
			this.tbl.removeChild(elems[0]);
		this.tbl.setAttribute("width",'100%');
		this.tbl.setAttribute("cellpadding","2");
		this.tbl.setAttribute("cellspacing","1");
		this.tbl.setAttribute("bgcolor","#CCCCCC");
		var tbody = document.createElement("tbody");  
		var row = document.createElement("tr");
		row.setAttribute("valign","top");
		row.setAttribute("header","true");
		var timecolumn = document.createElement("td");
		timecolumn.setAttribute("class","infoPanel");
		timecolumn.style.fontWeight = "bold";
		timecolumn.setAttribute("bgcolor","#EEEEEE");
		timecolumn.innerHTML = "Время";
		var usercolumn = document.createElement("td");
		usercolumn.setAttribute("class","infoPanel");
		usercolumn.style.fontWeight = "bold";
		usercolumn.setAttribute("bgcolor","#EEEEEE");
		usercolumn.innerHTML = "Пользователь";
		var eventcolumn = document.createElement("td");
		eventcolumn.setAttribute("class","infoPanel");
		eventcolumn.style.fontWeight = "bold";
		eventcolumn.setAttribute("bgcolor","#EEEEEE");
		eventcolumn.innerHTML = "Событие";
		eventcolumn.setAttribute("width","100%");
		row.appendChild(timecolumn);
		row.appendChild(usercolumn);
		row.appendChild(eventcolumn);		
		tbody.appendChild(row);
		this.tbl.appendChild(tbody);		
	},

	selectRow: function(event) {
		var parentNode = eventTarget(event).parentNode;
		var elems = parentNode.getElementsByTagName("td");
		var c=null;
		for (c in elems) {
			if (typeof elems[c]!="function" && typeof elems[c] == "object" && elems[c].getAttribute("type")!=null) {
				elems[c].style.fontWeight = 'normal';
				elems[c].style.color = '';
			}
		}
		var wm = getWindowManager();
		wm.activate_window("InfoPanelNew");
		var tabset = $O("InfoPanelTabset");
		var tabnode = $I(tabset.node.id+"_text_events");
		tabnode.style.color = '';
		tabnode.style.fontWeight = '';
	},
	
	filterEvents: function(type,user) {
		var elems = this.tbl.getElementsByTagName("tr");
		var c=null;
		for (c in elems) {
			if (typeof elems[c] != "function" && typeof(elems[c])=="object") {
				if ((type=="all" && user=="all") || (elems[c].getAttribute("header")=="true"))
					elems[c].style.display = '';
				else {
					if ((elems[c].getAttribute("type")==type && user=="all") || (type=="all" && elems[c].getAttribute("user")==user) || (elems[c].getAttribute("type")==type && elems[c].getAttribute("user")==user))
						elems[c].style.display = '';
					else
						elems[c].style.display = 'none';
				}
			}
		}		
	},
	
	dispatchEvent: function(event,params) {
		var infoPanel = $O("InfoPanel","");
		var c=null;
		for (c in infoPanel.events) {
			if (event==c) {
				var row = document.createElement("tr");
				row.setAttribute("object",this.object_id);
				row.setAttribute("type",event);
				row.setAttribute("bgcolor","#FFFFFF");
				var type = event;
				row.setAttribute("user",params["fromUser"]);
				if (params["fromUser"]=="undefined")
					continue;
				if (typeof params["fromUser"]=="undefined")
					continue;
				row.setAttribute("valign","top");			
				timecolumn = document.createElement("td");
				timecolumn.setAttribute("object",this.object_id);
				timecolumn.setAttribute("type",type);
				timecolumn.setAttribute("class","infoPanel");
				timecolumn.setAttribute("nowrap","true");
				timecolumn.setAttribute("click","selectRow");
				timecolumn.observe("click",this.addHandler);
				if (params["color"]!=null)
					timecolumn.style.color = params["color"];
				var d = new Date(parseFloat(params["eventTime"]+"000"));
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
				timecolumn.innerHTML = dateStr;
				timecolumn.style.fontWeight = 'bold';			
				
				var usercolumn = document.createElement("td");
				usercolumn.setAttribute("object",this.object_id);
				usercolumn.setAttribute("nowrap","true");
				usercolumn.setAttribute("type",type);
				usercolumn.setAttribute("class","infoPanel");
				usercolumn.setAttribute("click","selectRow");
				usercolumn.observe("click",this.addHandler);
				if (params["color"]!=null)
					usercolumn.style.color = params["color"];
				usercolumn.innerHTML = params["fromUser"];
				usercolumn.style.fontWeight = 'bold';			
				
				var eventcolumn = document.createElement("td");
				eventcolumn.setAttribute("object",this.object_id);
				eventcolumn.setAttribute("type",type);
				eventcolumn.setAttribute("width","100%");
				eventcolumn.setAttribute("class","infoPanel");
				eventcolumn.setAttribute("click","selectRow");
				eventcolumn.observe("click",this.addHandler);
				if (params["color"]!=null)
					eventcolumn.style.color = params["color"];
				if (params["message"]!=null)
					eventcolumn.innerHTML = params["message"];
				else
					eventcolumn.innerHTML = infoPanel.events[c]["comment"];
				eventcolumn.style.fontWeight = 'bold';			
				row.appendChild(timecolumn);
				row.appendChild(usercolumn);
				row.appendChild(eventcolumn);
				var elems = this.tbl.getElementsByTagName("TBODY");
				var tbody = elems[0];				
				elems = this.tbl.getElementsByTagName("TR");
				if (elems.length==1)
					tbody.appendChild(row);
				else {
					tbody.insertBefore(row,elems[1]);
				}
				this.raiseEvent("PUSH_WINDOW",$Arr("object_id=InfoPanelNew"));
				this.raiseEvent("PUSH_TAB",$Arr("object_id=InfoPanelTabset,tab=events"));
				if (params["notscroll"]==null)
					textcolumn.scrollIntoView(true);				
				if (params["activate"]!=null) {
					var wm = getWindowManager();
					wm.activate_window("InfoPanelNew");
					var tabset = $O("InfoPanelTabset","");
					tabset.activateTab("messages");
					if (params["color"]==null) {
						timecolumn.style.color = "#FF0000";
						usercolumn.style.color = "#FF0000";
						eventcolumn.style.color = "#FF0000";
					}
				}
				this.filterEvents($O("InfoPanel_eventsList",'').getValue(),$O("InfoPanel_usersList",'').getValue());				
			}
		}
	}
});