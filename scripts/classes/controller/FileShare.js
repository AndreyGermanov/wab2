var FileShare = Class.create(Mailbox, {

    OK_onClick: function(event) {

        // Работаем только если что-то действительно изменилось
        var is_changed = this.win.node.getAttribute('changed');
        if (is_changed!="true") {
            getWindowManager().remove_window(this.win.id);
            return 0;
        }

        // Проверяем корректность ввода данных
        var data = this.getValues();

        if (data["name"]=="") {
            this.reportMessage('Укажите имя общей папки',"error",true);
            return 0;
        }

        if (data["path"]=="") {
            this.reportMessage('Укажите путь к общей папке !',"error",true);
            return 0;
        }
        if (data["path"].search(/ /g)!=-1) {
            this.reportMessage("Путь к общей папке не должен содержать пробелов !","error",true);
            return 0;
        }
        var changed_rules = 0;
        var users_rules = 0;
        var groups_rules = 0;
        if ($O(this.hosts_access_rules_table)!=null && $O(this.hosts_access_rules_table)!=0)
            changed_rules = $O(this.hosts_access_rules_table).getChanged();
        if ($O(this.users_access_rules_table)!=null && $O(this.users_access_rules_table)!=null)        
            users_rules = $O(this.users_access_rules_table).getChecked();
        if ($O(this.groups_access_rules_table)!=null)        
            groups_rules = $O(this.groups_access_rules_table).getChecked();
        // Формируем параметры, передаваемые серверу для записи
        var initstring = "$object->load();";
        initstring += "$object->name='"+data["name"]+"';";
        initstring += "$object->path='"+data["path"]+"';";        
        initstring += "$object->recycleBin="+data["recycleBin"]+";";        
        initstring += "$object->recyclePath='"+data["recyclePath"]+"';";        
        initstring += "$object->recyclePeriod="+data["recyclePeriod"]+";";        
        initstring += "$object->fullAudit="+data["fullAudit"]+";";        
        if (changed_rules != 0)
            initstring += "$object->changed_rules='"+changed_rules+"';";
        if (users_rules != 0)
            initstring += "$object->users_rules='"+users_rules+"';";
        if (groups_rules != 0)
            initstring += "$object->groups_rules='"+groups_rules+"';";
        initstring += "$object->save();";
        var mbox = this;
        var wm = getWindowManager();
        var loading_img = document.createElement("img");                
        loading_img.src = this.skinPath+"images/Tree/loading2.gif";
        loading_img.style.zIndex = "100";
        loading_img.style.position = "absolute";
        loading_img.style.top=(window.innerHeight/2-33);
        loading_img.style.left=(window.innerWidth/2-33);        
        this.node.appendChild(loading_img);
        new Ajax.Request("index.php", {
            method:"post",
            parameters: {ajax: true, object_id: "FileShare_"+mbox.module_id+"_"+data["idnumber"],init_string: initstring},
            onSuccess: function(transport)
            {
                var response = trim(transport.responseText.replace("\n",""));
                mbox.node.removeChild(loading_img);
                if (response!="" && parseInt(response) != response)
                {
                    response = response.evalJSON(true);
                    if (response["error"]!=null)
                    {
                        mbox.reportMessage(response["error"],"error",true);
                        return 0;
                    }
                    else
                        mbox.reportMessage(response,"error",true);
                }
                else {
                    var id = response;
                    var target_id = "FileShare_"+mbox.module_id+"_"+id;
                    var owner_id = getClientId("FileShares_"+mbox.module_id);
                    var params = new Array;
                    if (mbox.opener_item.parentNode !=null)
                        params["object_id"] = target_id;
                    params["new_object_id"] = target_id;
                    params["target_id"] = target_id;
                    params["text"] = data["name"];
                    if (data["path"]!="root")
                        params["title"] = mbox.shares_root+"/"+data["path"];
                    else
                        params["title"] = data["path"];
                    params["old_text"] = data["old_name"];
                    params["parent"] = owner_id;
                    var icon = mbox.skinPath+"images/Tree/folder.png";
                    params["image"] = icon;

                    if (data["old_name"]=="")
                        params["action"] = "add";
                    else
                        params["action"] = "change";
                    mbox.raiseEvent("NODE_CHANGED",params,true);
                    mbox.win.node.setAttribute('changed',"false");
                    wm.remove_window(mbox.win.id);
                }
            }
        });
    },

    onRemoveWindow: function (topWindow) {
        delete topWindow.objects.objects[this.tabset_id];
        delete topWindow.objects.objects[this.hosts_access_rules_table];
        delete topWindow.objects.objects[this.users_access_rules_table];
        delete topWindow.objects.objects[this.groups_access_rules_table];
    },

    sharepath_selectButton_onClick: function() {
        var leftPosition = (screen.availWidth-250)/2;
        var topPosition = (screen.availHeight-300)/2;
        var init_string = '$object->icon = "'+this.skinPath+'images/Tree/folder.gif";$object->title="'+this.shares_root+'";$object->rootPath="'+this.shares_root+'";+$object->target_item="'+this.node.id+'_path";$object->absolute_path="false";$object->SetTreeItems();';
        var data = this.getValues();
        var new_id = this.getClassName()+"_"+this.module_id+"_"+this.site+"_"+getClientId(data["id"])+"_"+getClientId(data["old_parent_id"]);
        var args = new_id+"_parent_id;"+new_id+"_parent_title;"+this.object_id+";"+new_id+"_parent_parent_id";
        this.selectParentWindow = window.showModalDialog("index.php?object_id=DirectoryTree_"+this.module_id+"_Tree1&init_string="+init_string+";$object->show();",args,"dialogWidth:250px; dialogHeight:300px; dialogLeft:"+leftPosition+"px; dialogTop:"+topPosition+"px;");
    },

    help_onClick: function(event) {
        var params = new Array;
        getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"guide5.3","HTMLBook_"+this.module_id+"_controller_5.3",params,this.opener_item.getAttribute("object"),this.opener_item.id);
    }
});