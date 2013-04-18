    var AddressBook = Class.create(Mailbox, {

    OK_onClick: function(event) {
        var obj = this;
        var is_changed = obj.win.node.getAttribute('changed');
        if (is_changed!="true") {
            obj.win.node.setAttribute("changed","false");
            getWindowManager().remove_window(obj.win.id);
        }

        var data = $O("Table_"+this.module_id+"_").getCellsData();
        for (var counter=0;counter<data.length;counter++) {
            var cells = data[counter].split("|");
            for (var counter1=0;counter1<cells.length;counter1++) {
                if (cells[counter1]=="") {
                    this.reportMessage('В таблице есть пустые поля! ',"error",true);
                    return 0;
                }
            }
        }
        var arr = new Array;
        for (var counter=0;counter<data.length;counter++) {
            arr[counter] = data[counter];
        }
        arr.shift();
        var loading_img = document.createElement("img");                
        loading_img.src = this.skinPath+"images/Tree/loading2.gif";
        loading_img.style.zIndex = "100";
        loading_img.style.position = "absolute";
        loading_img.style.top=(window.innerHeight/2-33);
        loading_img.style.left=(window.innerWidth/2-33);   
        var args = new Object;
        args["fields"] = arr.join("|");
        this.node.appendChild(loading_img);
        new Ajax.Request("index.php", {
            method: "post",
            parameters: {ajax: true, object_id: "AddressBook_"+this.module_id, hook: '3', arguments: Object.toJSON(args)},
            onSuccess: function(transport) {
                var response = trim(transport.responseText.replace("\n",""));
                obj.node.removeChild(loading_img);
                if (response.length>1) {
                    var response_object = response.evalJSON();
                    if (response_object["error"]!=null)
                        obj.reportMessage(response_object["error"],"error",true);
                    else
                        obj.reportMessage(response,"error",true);
                } else {
                        obj.win.node.setAttribute("changed","false");
                        getWindowManager().remove_window(obj.win.id);
                }
            }
        });
    },
    
    help_onClick: function(event) {
        var params = new Array;
        getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"collector9","HTMLBook_"+this.module_id+"_collector_9",params,this.opener_item.getAttribute("object"),this.opener_item.id);
    }    
});