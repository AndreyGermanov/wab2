var RepFilterConfig = Class.create(Mailbox, {

    repofilter_check_onClick: function(event) {
        var chk = eventTarget(event);
        var div = $I(this.node.id+"_whitelist");
        if (chk.checked) {
            if (this.repFilterLoaded!=true) {
                var tbl = $O("RepFilterTable_"+this.module_id+"_repofilter");
                tbl.build();
                this.repFilterLoaded = true;
            }
            div.style.display = "";
        } else
            div.style.display = "none";       
    },

    OK_onClick: function(event) {
        if (blur_error!="")
            return 0;

        var rulesetTable = $O("RepFilterTable_"+this.module_id+"_repofilter",'');
        for (var r=0;r<rulesetTable.rows.length;r++) {
            var cells = rulesetTable.rows[r]["cells"];
            for (var c1=0;c1<cells.length;c1++) {
                var ctrl = rulesetTable.getItem(r,c1);
                if (ctrl.type=="plaintext")
                    continue;
                var err = ctrl.checkValue(ctrl.getValue(),false);
                if (err!="") {
                    this.reportMessage("Ошибка в строке "+(r+1)+", в столбце "+(c1+1)+": "+err,"error",true);
                    ctrl.setFocus();
                    return 0;
                }
                if (c1==0) {
                    if (check_ip(ctrl.getValue())==false) {
                        this.reportMessage('IP-адрес в строке '+(r+1)+" указан неверно !","error",true);
                        ctrl.setFocus();
                        return 0;
                    }
                }
            }
        }

        var data = this.getValues();
        var args = data.toObject();
        var cnt = 0;
        var rules_array = new Object;
        for (var c=0;c<rulesetTable.rows.length;c++) {
            var cell = rulesetTable.rows[c]["cells"];
            if (cell[0]["control"]=="plaintext")
                continue;
            rules_array[cnt] = cell[0]["value"]+" "+cell[1]["value"];
            cnt = cnt+1;
        }
        args["rules_array"] = rules_array;

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
            parameters: {ajax: true, object_id: obj.object_id, hook: '3', arguments: Object.toJSON(args)},
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

    sendReportBtn_onClick: function(event) {
    	var obj=this;
        new Ajax.Request("repolog.php",
        {
            method: "post",
            parameters: {ajax: true, object_id: "new_object",
                         init_string: "initstring"},
            onSuccess: function(transport) {
                var response = trim(transport.responseText).replace("\n","");
                if (response.length>1)
                {
                    response = response.evalJSON();
                    if (response["error"]!=null)
                        obj.reportMessage(response["error"],"error",true);
                    return 0;
                }
            }
        });
    }
});