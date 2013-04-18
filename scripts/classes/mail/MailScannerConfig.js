var MailScannerConfig = Class.create(Mailbox, {

    OK_onClick: function(event) {
        if (blur_error!="")
            return 0;
        var is_changed = this.win.node.getAttribute('changed');
        if (is_changed!="true") {
            if (this.win!="")
                getWindowManager().remove_window(this.win.id);
        }
        var data = this.getValues();
        
        var whitelist_table = $O("SpamWhitelistTable_"+this.module_id+"_whitelist").rows;
        var white_list_rules_array = new Object;
        for(var i=0;i<whitelist_table.length;i++) {
            var cells = whitelist_table[i]["cells"];
            if (cells[0]["control"]=="plaintext")
                continue;
            if (cells[1]["value"]==null)
                continue;
            white_list_rules_array[i] = cells[0]["value"]+" "+cells[1]["value"]+" yes";
        }
        var blacklist_table = $O("SpamBlacklistTable_"+this.module_id+"_blacklist").rows;
        var black_list_rules_array = new Object;
        for(var i=0;i<blacklist_table.length;i++) {
            var cells = blacklist_table[i]["cells"];
            if (cells[0]["control"]=="plaintext")
                continue;
            if (cells[1]["value"]==null)
                continue;
            black_list_rules_array[i] = cells[0]["value"]+" "+cells[1]["value"]+" yes";
        }
        data["blacklist_rules_array"] = black_list_rules_array;
        data["whitelist_rules_array"] = white_list_rules_array;
        var args = data.toObject();
        var obj = this;
        var loading_img = document.createElement("img");                
        loading_img.src = this.skinPath+"images/Tree/loading2.gif";
        loading_img.style.zIndex = "100";
        loading_img.style.position = "absolute";
        loading_img.style.top=(window.innerHeight/2-33);
        loading_img.style.left=(window.innerWidth/2-33);        
        this.node.appendChild(loading_img);
        new Ajax.Request("index.php",
        {
            method: "post",
            parameters: {ajax: true, object_id: obj.object_id,
                         hook: '3', arguments: Object.toJSON(args)},
            onSuccess: function(transport) {
                var response = trim(transport.responseText).replace("\n","");
                obj.node.removeChild(loading_img);
                if (response.length>1)
                {
                    response = response.evalJSON();
                    if (response["error"]!=null)
                        obj.reportMessage(response["error"],"error",true);
                    return 0;
                }
                if (obj.win!="")
                {
                    obj.win.node.setAttribute("changed","false");
                    obj.win.opener_item = obj.opener_item.id;
                    getWindowManager().remove_window(obj.win.id);
                }
            }
        });
    },

    categorySelect_onChange: function(event) {
        if (this.prev_category!=null)
            $I(this.node.id+"_"+this.prev_category).style.display = 'none';
            this.prev_category = eventTarget(event).options[eventTarget(event).selectedIndex].value;
            $I(this.node.id+"_"+this.prev_category).style.display = '';
    },
    
    dispatchEvent: function($super,event,params) {
        $super(event,params);
        if (event=="TAB_CHANGED") {
            if (params["tabset_id"]==this.tabset_id) {
                if (params["tab"]=="whitelist" && this.whitelistTableBuild!=true) {
                    var tbl = $O("SpamWhitelistTable_"+this.module_id+"_whitelist");
                    tbl.build(true);
                    $I(tbl.node.id+"_innerFrame").src = tbl.frameSrc;
                    this.whitelistTableBuild = true;
                }           
                if (params["tab"]=="blacklist" && this.blacklistTableBuild!=true) {
                    var tbl = $O("SpamBlacklistTable_"+this.module_id+"_blacklist");
                    tbl.build(true);
                    $I(tbl.node.id+"_innerFrame").src = tbl.frameSrc;
                    this.blacklistTableBuild = true;
                }           
            }
        }
    },
    
    help_onClick: function(event) {
        var params = new Array;
        getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"collector11","HTMLBook_"+this.module_id+"_collector_11",params,this.opener_item.getAttribute("object"),this.opener_item.id);
    }
});