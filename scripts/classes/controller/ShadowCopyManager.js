var ShadowCopyManager = Class.create(Entity, {
    
    switchShadowCopy: function(check) {
        if (check) {
            $I(this.node.id+"_tabset").style.display = '';
            $I(this.node.id+"_"+this.tabset.active_tab).style.display = '';
            $I(this.node.id+"_"+this.tabset.active_tab).style.height = '100%';
            $I(this.node.id+"_switch").style.height = '0';            
        } else {
            $I(this.node.id+"_tabset").style.display = 'none';
            $I(this.node.id+"_snapshots").style.display = 'none';
            $I(this.node.id+"_settings").style.display = 'none';                        
            $I(this.node.id+"_rotation").style.display = 'none';
            $I(this.node.id+"_switch").style.height = '100%';            
            $I(this.node.id+"_"+this.tabset.active_tab).style.height = '0';
        }
    },
    
    switchShadowCopyRotation: function(check) {
        if (check)
            $I(this.node.id+"_snapshotsRotation").style.display = "";
        else
            $I(this.node.id+"_snapshotsRotation").style.display = "none";
    },
    
    CONTROL_VALUE_CHANGED_processEvent: function(params) {
        if (params["object_id"]==this.node.id+"_enableShadowCopy") {
            this.switchShadowCopy(parseInt(params["value"]));
        }
        if (params["object_id"]==this.node.id+"_enableAutoSnapshotsRotation") {
            this.switchShadowCopyRotation(parseInt(params["value"]));
        }
    },
    
    OK_onClick: function(event) {
        // Работаем только если что-то действительно изменилось
        var is_changed = this.win.node.getAttribute('changed');
        if (is_changed!="true") {
            return 0;
        }

        // Формируем параметры, передаваемые серверу для записи
        var data = this.getValues();
        var args = data.toObject();

        var mbox = this;
        var loading_img = document.createElement("img");                
        loading_img.src = this.skinPath+"images/Tree/loading2.gif";
        loading_img.style.zIndex = "100";
        loading_img.style.position = "absolute";
        loading_img.style.top=(window.innerHeight/2-33);
        loading_img.style.left=(window.innerWidth/2-33);        
        this.node.appendChild(loading_img);
        new Ajax.Request("index.php", {
            method:"post",
            parameters: {ajax: true, object_id: "ShadowCopyManager_"+mbox.module_id+"_Manager",hook: '3', arguments: Object.toJSON(args)},
            onSuccess: function(transport) {
                var response = trim(transport.responseText.replace("\n",""));
                mbox.node.removeChild(loading_img);
                if (response!="" && response!=parseInt(response)) {
                    response = response.evalJSON(true);
                    if (response["error"]!=null) {
                        mbox.reportMessage(response["error"],"error",true);
                        return 0;
                    }
                    else
                        mbox.reportMessage(response,"error",true);
                }
                else {
                    mbox.win.node.setAttribute("changed",false);
                    $I(mbox.win.node.id+"_headertext").innerHTML = $I(mbox.win.node.id+"_headertext").innerHTML.replace("*","");                    
                }
            }
        });
    },
    
    help_onClick: function(event) {
        var params = new Array;
        getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"guide8.6","HTMLBook_"+this.module_id+"_controller_8.6",params,this.opener_item.getAttribute("object"),this.opener_item.id);
    }        
});