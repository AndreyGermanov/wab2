var MailScannerRulesetEditor = Class.create(Mailbox, {

    OK_onClick: function(event) {
        if (blur_error!="")
            return 0;

        var defValue_ctl = $O(this.node.id+"_defaultValue");
        var defValue = defValue_ctl.getValue();
        var err = defValue_ctl.checkValue(defValue,false);
        if (err!="") {
            alert("Значение по умолчанию: "+err);
            defValue_ctr.setFocus();
            return 0;
        }
        var tbl_id = this.object_id.split("_");
        tbl_id[0] = "SpamRulesTable";
        tbl_id = tbl_id.join("_");
        var rulesetTable = $O(tbl_id,'');
        for (var r=0;r<rulesetTable.rows.length;r++) {
            var cells = rulesetTable.rows[r]["cells"];
            for (var c1=0;c1<cells.length;c1++) {
                var ctrl = rulesetTable.getItem(r,c1);
                if (ctrl.type=="plaintext")
                    continue;
                err = ctrl.checkValue(ctrl.getValue(),false);
                if (err!="") {
                    alert("Ошибка в строке "+(r+1)+", в столбце "+(c1+1)+": "+err);
                    ctrl.setFocus();
                    return 0;
                }
            }
        }

        var data = this.getValues();

        var cnt = 0;
        var rules_array = new Object;
        for (var c=0;c<rulesetTable.rows.length;c++) {
            var cell = rulesetTable.rows[c]["cells"];
            if (cell[0]["control"]=="plaintext")
                continue;
            rules_array[cnt] = cell[0]["value"]+" "+cell[1]["value"]+" "+cell[2]["value"];
            cnt = cnt+1;
        }
        data["rules_array"] = rules_array;
        var args = data.toObject();
        var obj = this;
        new Ajax.Request("index.php",
        {
            method: "post",
            parameters: {ajax: true, object_id: obj.object_id,hook: '3', arguments: Object.toJSON(args)},
            onSuccess: function(transport) {
                var response = trim(transport.responseText).replace("\n","");
                if (response.length>1) {
                    response = response.evalJSON();
                    if (response["error"]!=null)
                        obj.reportMessage(response["error"],"error",true);
                    return 0;
                }
                if (obj.win!="") {
                    obj.win.node.setAttribute("changed","false");
                    obj.win.opener_item = obj.opener_item.id;
                    getWindowManager().remove_window(obj.win.id);
                }
            }
        });
    }
});