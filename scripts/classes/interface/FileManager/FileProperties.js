var FileProperties = Class.create(Entity, {
    
    checkAllChecked: function(checkboxName) {
        var tbl = $O(this.node.id+"_"+checkboxName,"").node.parentNode.parentNode.parentNode.parentNode;
        var tbl_arr = tbl.id.split("_");
        tbl_arr.pop();
        var tbl_pre_end = tbl_arr.pop();
        var object_type = "allGroups";
        if (tbl_pre_end=="users")
            object_type = "allUsers";
        var arr = checkboxName.split("_");
        var end = arr.pop();
        object_type = arr.pop();
        var elems = tbl.getElementsByTagName("CONTROL");
        var allChecked = "1";
        var o=null;
        for (o in elems) {
            if (elems[o]==null || typeof elems[o] == "function" || typeof elems[o] != "object")
                continue;
            var obj = $O(elems[o].getAttribute("id"),"");
            var obj_arr = obj.object_id.split("_");
            var obj_end = obj_arr.pop();
            var obj_pre_end = obj_arr.pop();
            if (obj_end == end && obj_pre_end!="allUsers" && obj_pre_end!="allGroups") {
                if (obj.getValue()=="0") {
                    allChecked = "0";
                    break;
                }
            }
        }
        $O(this.node.id+"_"+object_type+"_"+end,"").setValue(allChecked,true);
        
    },
    
    CONTROL_VALUE_CHANGED_processEvent: function(params) {
        var arr = params["object_id"].split("_");
        var el = $O(params["object_id"],"");
        el.node.setAttribute("value",params["value"]);
        var tbl = $O(params["object_id"]).node.parentNode.parentNode.parentNode;
        var end = arr.pop();
        var pre_end = arr.pop();
        var o=null;
        if (end == "shareCheckBox" && el.parent_object_id==this.object_id) {
            if (params["value"]=="1")
                $I(this.node.id+"_shareDisplay").style.display="";
            else
                $I(this.node.id+"_shareDisplay").style.display="none";
        }
        if (pre_end=="allUsers" || pre_end=="allGroups") {
            var elems = tbl.getElementsByTagName("CONTROL");
            for (o in elems) {
                if (elems[o]==null || typeof elems[o] == "function" || typeof elems[o] != "object")
                    continue;
                var obj = $O(elems[o].getAttribute("id"),"");
                var obj_arr = obj.object_id.split("_");
                var obj_end = obj_arr.pop();
                var obj_pre_end = obj_arr.pop();
                if (obj_end == end && obj_pre_end!=pre_end) {
                    obj.setValue(params["value"],true);
                }
            }            
        } else {            
            var tbl = $O(params["object_id"],"").node.parentNode.parentNode.parentNode.parentNode;
            var tbl_arr = tbl.id.split("_");
            tbl_arr.pop();
            var tbl_pre_end = tbl_arr.pop();
            var object_type = "allUsers";
            if (tbl_pre_end=="users")
                object_type = "allUsers";
            var arr = params["object_id"].split("_");
            var end = arr.pop();
            arr.pop();
            var elems = tbl.getElementsByTagName("CONTROL");
            var allChecked = "1";            
            for (o in elems) {
                if (elems[o]==null || typeof elems[o] == "function" || typeof elems[o] != "object")
                    continue;
                var obj = $O(elems[o].getAttribute("id"),"");
                var obj_arr = obj.object_id.split("_");
                var obj_end = obj_arr.pop();
                var obj_pre_end = obj_arr.pop();
                if (obj_end == end && obj_pre_end!="allUsers" && obj_pre_end!="allGroups" && obj.object_id!=params["object_id"]) {
                    if (obj.getValue()=="0") {
                        allChecked = "0";
                        break;
                    }
                }
            }
            if (params["value"]=="0")
                allChecked = "0";
            if ($O(this.node.id+"_"+object_type+"_"+end,"")!=null)
                $O(this.node.id+"_"+object_type+"_"+end,"").setValue(allChecked,true);
        } 
    },
    
    OK_onClick: function(event) {
        var data = this.getValues();
        var args = new Object;
        args["rights"] = new Array;
        var userRights = new Array;
        var defaultUserRights = new Array;
        var groupRights = new Array;
        var defaultGroupRights = new Array;
        var arr = new Array;
        var end = "";
        var o = null;
        for (o in data) {
            pre_end = "";
            prepreend = "";
            end = "";
            if (typeof data[o] == "function")
                continue;
            arr = o.split("_");
            end = arr.pop();
            found = false;
            if (arr.length>0)
                pre_end = arr.pop();
            if (arr.length>0)
                prepre_end = arr.pop();
            if (pre_end=="allUsers" || pre_end=="allGroups")
                continue;
            if (end=="ReadCheck") {
                found = true;
                if (prepre_end=="user") {
                    if (userRights[pre_end]==null)
                        userRights[pre_end] = new Array;
                    if (data[o]=="true" || data[o] == "1")
                        userRights[pre_end]["read"] = "r";
                    else
                        userRights[pre_end]["read"] = "-";
                } else {
                    if (groupRights[pre_end]==null)
                        groupRights[pre_end] = new Array;
                    if (data[o]=="true" || data[o] == "1")
                        groupRights[pre_end]["read"] = "r";
                    else
                        groupRights[pre_end]["read"] = "-";                    
                }
            }
            if (end=="WriteCheck") {
                found = true;
                if (prepre_end=="user") {
                    if (userRights[pre_end]==null)
                        userRights[pre_end] = new Array;
                    if (data[o]=="true" || data[o] == "1")
                        userRights[pre_end]["write"] = "w";
                    else
                        userRights[pre_end]["write"] = "-";
                } else {
                    if (groupRights[pre_end]==null)
                        groupRights[pre_end] = new Array;
                    if (data[o]=="true" || data[o] == "1")
                        groupRights[pre_end]["write"] = "w";
                    else
                        groupRights[pre_end]["write"] = "-";                    
                }
            }
            if (end=="ExecuteCheck") {
                found = true;
                if (prepre_end=="user") {
                    if (userRights[pre_end]==null)
                        userRights[pre_end] = new Array;
                    if (data[o]=="true" || data[o] == "1")
                        userRights[pre_end]["execute"] = "x";
                    else
                        userRights[pre_end]["execute"] = "-";
                } else {
                    if (groupRights[pre_end]==null)
                        groupRights[pre_end] = new Array;
                    if (data[o]=="true" || data[o] == "1")
                        groupRights[pre_end]["execute"] = "x";
                    else
                        groupRights[pre_end]["execute"] = "-";                    
                }
            }
            if (end=="DefaultReadCheck") {
                found = true;
                if (prepre_end=="user") {
                    if (defaultUserRights[pre_end]==null)
                        defaultUserRights[pre_end] = new Array;
                    if (data[o]=="true" || data[o] == "1")
                        defaultUserRights[pre_end]["read"] = "r";
                    else
                        defaultUserRights[pre_end]["read"] = "-";
                } else {
                    if (defaultGroupRights[pre_end]==null)
                        defaultGroupRights[pre_end] = new Array;
                    if (data[o]=="true" || data[o] == "1")
                        defaultGroupRights[pre_end]["read"] = "r";
                    else
                        defaultGroupRights[pre_end]["read"] = "-";                    
                }
            }
            if (end=="DefaultWriteCheck") {
                found = true;
                if (prepre_end=="user") {
                    if (defaultUserRights[pre_end]==null)
                        defaultUserRights[pre_end] = new Array;
                    if (data[o]=="true" || data[o] == "1")
                        defaultUserRights[pre_end]["write"] = "w";
                    else
                        defaultUserRights[pre_end]["write"] = "-";
                } else {
                    if (defaultGroupRights[pre_end]==null)
                        defaultGroupRights[pre_end] = new Array;
                    if (data[o]=="true" || data[o] == "1")
                        defaultGroupRights[pre_end]["write"] = "w";
                    else
                        defaultGroupRights[pre_end]["write"] = "-";                    
                }
            }
            if (end=="DefaultExecuteCheck") {
                found = true;
                if (prepre_end=="user") {
                    if (defaultUserRights[pre_end]==null)
                        defaultUserRights[pre_end] = new Array;
                    if (data[o]=="true" || data[o] == "1")
                        defaultUserRights[pre_end]["execute"] = "x";
                    else
                        defaultUserRights[pre_end]["execute"] = "-";
                } else {
                    if (defaultGroupRights[pre_end]==null)
                        defaultGroupRights[pre_end] = new Array;
                    if (data[o]=="true" || data[o] == "1")
                        defaultGroupRights[pre_end]["execute"] = "x";
                    else
                        defaultGroupRights[pre_end]["execute"] = "-";                    
                }
            }
        }
        var rights = "";
       
        for (o in userRights) {
            if (typeof userRights[o] == "function")
                continue;
            rights = userRights[o]["read"]+userRights[o]["write"]+userRights[o]["execute"];
            if (rights=="-w-")
                rights = "rw-";
            if (rights=="--x")
                rights = "r-x";
            if (rights=="-wx")
                rights = "rwx";
            if (o=="userOwner")
                args["rights"][args["rights"].length] = "user::"+rights;
            else if (o=="userOther")
                args["rights"][args["rights"].length] = "other::"+rights;
            else if (rights!="---")
                args["rights"][args["rights"].length] = "user:"+o+":"+rights;
        }
        for (o in groupRights) {
            if (typeof groupRights[o] == "function")
                continue;
            rights = groupRights[o]["read"]+groupRights[o]["write"]+groupRights[o]["execute"];
            if (rights=="-w-")
                rights = "rw-";
            if (rights=="--x")
                rights = "r-x";
            if (rights=="-wx")
                rights = "rwx";
            if (o=="groupOwner")
                args["rights"][args["rights"].length] = "group::"+rights;
            else if (o=="groupOther")
                args["rights"][args["rights"].length] = "other::"+rights;
            else if (rights!="---")
                args["rights"][args["rights"].length] = "group:"+o+":"+rights;
        }
        for (o in defaultUserRights) {
            if (typeof defaultUserRights[o] == "function")
                continue;
            rights = defaultUserRights[o]["read"]+defaultUserRights[o]["write"]+defaultUserRights[o]["execute"];
            if (rights=="-w-")
                rights = "rw-";
            if (rights=="--x")
                rights = "r-x";
            if (rights=="-wx")
                rights = "rwx";
            if (o=="userOwner")
                args["rights"][args["rights"].length] = "default:user::"+rights;
            else if (o=="userOther")
                args["rights"][args["rights"].length] = "default:other::"+rights;
            else if (rights!="---")
                args["rights"][args["rights"].length] = "default:user:"+o+":"+rights;
        }
        for (o in defaultGroupRights) {
            if (typeof defaultGroupRights[o] == "function")
                continue;
            rights = defaultGroupRights[o]["read"]+defaultGroupRights[o]["write"]+defaultGroupRights[o]["execute"];
            if (rights=="-w-")
                rights = "rw-";
            if (rights=="--x")
                rights = "r-x";
            if (rights=="-wx")
                rights = "rwx";
            if (o=="groupOwner")
                args["rights"][args["rights"].length] = "default:group::"+rights;
            else if (o=="groupOther")
                args["rights"][args["rights"].length] = "default:other::"+rights;
            else if (rights!="---")
                args["rights"][args["rights"].length] = "default:group:"+o+":"+rights;
        }
        args["rights"] = args["rights"].join("\n");
        if (data["users_owner_name"]!=data["old_users_owner_name"]) {
            args["userOwner"] = data["users_owner_name"];
        }
        if (data["groups_owner_name"]!=data["old_groups_owner_name"]) {
            args["groupOwner"] = data["groups_owner_name"];
        }
        args["reverseRules"] = data["reverseRules"];
        args["paths"] = data["paths"];
        args["shareCheckBox"] = data["shareCheckBox"];
        args["share"] = data["share"];
        args["old_share"] = data["old_share"];
        args["recyclePath"] = data["recyclePath"];
        args["recycleBin"] = data["recycleBin"];
        args["recyclePeriod"] = data["recyclePeriod"];
        args["fullAudit"] = data["fullAudit"];
        args["ftpFolder"] = data["ftpFolder"]; 
        args["registerFileInfo"] = data["registerFileInfo"];
        if (this.hosts_access_rules_table!=null)
            args["changed_rules"] = this.hosts_access_rules_table.getChanged();

        if (data["shareCheckBox"]==true || data["shareCheckBox"]=="1" || data["shareCheckBox"]==1) {
            if (data["share"]=="") {
                alert("Укажите имя общего ресурса!");
                return 0;
            }
        }
        var loading_img = document.createElement("img");                
        loading_img.src = this.skinPath+"images/Tree/loading2.gif";
        loading_img.style.zIndex = "100";
        loading_img.style.position = "absolute";
        loading_img.style.top=(window.innerHeight/2-33);
        loading_img.style.left=(window.innerWidth/2-33);        
        this.node.appendChild(loading_img);
        obj=this;
        new Ajax.Request("index.php",
        {
            method: "post",
            parameters: {ajax: true, object_id: this.object_id,
                         hook: '3', arguments: Object.toJSON(args)},
            onSuccess: function(transport) {
                var response = transport.responseText;
                obj.node.removeChild(loading_img);
                if (response!="" && response!=parseInt(response)) {
                    response = response.evalJSON(response);
                    obj.reportMessage(response["error"],"error",true);
                } else {
                    obj.win.node.setAttribute("changed",false);                    
                    $I(obj.win.node.id+"_headertext").innerHTML = $I(obj.win.node.id+"_headertext").innerHTML.replace("*","");
                    obj.hosts_access_rules_table.syncCheckboxes();
                    obj.opener_object.buildTable();
                }
            }
        });                
    },
    
    onRemoveWindow: function (topWindow) {
        delete topWindow.objects.objects[this.hosts_access_rules_table];
    },
    
    help_onClick: function(event) {
		getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"guide5.3.4","HTMLBook_"+this.module_id+"_controller_5.3.4",null,this.opener_item.getAttribute("object"),this.opener_item.id);
	},
	
	referenceFileButton_onClick: function(event) {
		var but = eventTarget(event);
		var elemid = but.getAttribute("fileid");
		var windowid = "Window_"+elemid.replace(/_/g,'');
		var args = new Object;		
		var data = this.getValues();
		args["path"]=data["paths"];
		args["hook"] = "setParams";
		wm.show_window(windowid,elemid,args,this.object_id,but.id,null);
	} 
});