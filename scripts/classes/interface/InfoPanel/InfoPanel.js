var InfoPanel = Class.create(Entity, {
	
	clearMessages_onClick: function(event) {
		var messagesPanel = $O("InfoPanelMessages","");
		messagesPanel.tbl.innerHTML = "";
	},

	clearEvents_onClick: function(event) {
		var eventsPanel = $O("InfoPanelEvents","");
		eventsPanel.build();
	},
	
	showEventLog_onClick: function(event) {
		var wm = getWindowManager();
		wm.show_window("Window_EventLogLog","EventLog_"+this.module_id+"_Events",null,null,null);
	},
	
	CONTROL_VALUE_CHANGED_processEvent: function(params) {
		if (params["parent_object_id"]==this.object_id) {
			if (params["object_id"]== this.node.id+"_showMessagesList") {
				$O("InfoPanelMessages","").filterMessages(params["value"]);
			}
		}
		if (params["object_id"]== this.node.id+"_usersList") {
			$O("InfoPanelEvents","").filterEvents($O(this.node.id+"_eventsList").getValue(),params["value"]);
		}
		if (params["object_id"]== this.node.id+"_eventsList") {
			$O("InfoPanelEvents","").filterEvents(params["value"],$O(this.node.id+"_usersList").getValue());
		}
	}	
});