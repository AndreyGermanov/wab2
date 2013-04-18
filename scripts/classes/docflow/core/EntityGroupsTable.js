var EntityGroupsTable = Class.create(Entity, {	
	TAB_CHANGED_processEvent: function($super,params) {
		if (params["tabset_id"]==this.tabsetName) {
			if (typeof(this.tabsLoaded)!="undefined" && (typeof(this.tabsLoaded[params["tab"]])=="undefined" || this.tabsLoaded[params["tab"]]==false)) {
				var tbl = $O(this.tabs[params["tab"]]["tabTableId"],'');
				tbl.rebuild();
				this.tabsLoaded[params["tab"]] = true;
			}
		}
	},	
	CONTROL_HAS_FOCUSED_processEvent: function(params) {
		var elem = $I(params["object_id"]);
		var par = $O(params["object_id"],'').parent_object_id;
		var tbl = $O(par,'');		
		var parent = $O(par,"").parent_object_id;
		if (parent==this.object_id) {
			if ($I(params["object_id"],"").getAttribute("isGroup")!=null && $I(params["object_id"],"").getAttribute("isGroup")!="true" && $O(this.node.id+"_showLinksWindow","").getValue()=="1") {
				if (this.linksWindow!="") {
					var elem_id = "";					
					var params = new Object;
					params["hook"] = 3;
					params["topLinkObject"] = tbl.getItem(elem.getAttribute("row"),0).getValue();
					params["ownerObject"] = tbl.getItem(elem.getAttribute("row"),0).getValue();
					params["tabTitles"] = this.tabTitles;
					params["object_text"] = "Связи объекта";
					if (this.linksWindow=="new") {
						elem_id = "EntityGroupsTable_"+this.module_id+"_"+params["topLinkObject"].split("_").pop();
					} else
						elem_id = this.linksWindow;
					var wm = getWindowManager();					
					var window_elem_id = "Window_"+elem_id.replace(/_/g,"");
					var win_elem_id = window_elem_id.split("_");
					win_elem_id.shift();
					win_elem_id = win_elem_id.join("_");
					if (this.linksWindow!="new") {
						if ($O(win_elem_id,"")!=null) {
							if ($O($O(win_elem_id,"").php_object_id).topLinkObject!=params["topLinkObject"])
								wm.remove_window(win_elem_id);
						}
					}					
					if ($O(elem_id,"")==null) {
						wm.show_window(window_elem_id,elem_id,params,this.object_id,this.node.id,null,true);
					}
					else
						wm.activate_window(window_elem_id);
				}
			}				
		}		
	}
});