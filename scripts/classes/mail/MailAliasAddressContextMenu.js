var MailAliasAddressContextMenu = Class.create(ContextMenu,{
    onClick: function(event) {
        var elem = eventTarget(event);
        var mboxid = this.opener_item.getAttribute("target_object");
        switch(get_elem_id(elem).replace("_text","")) {
            case "change":
                var arr = mboxid.split("_");
                var elem_id_arr = new Array;
                for (var counter=0;counter<arr.length;counter++) {
                    if (arr[counter]=="Addresses") {
                        var params = new Object;
                        var elem_end = arr.slice(counter+1,arr.length).join("_");
                        params["address"] = elem_end;
                        params["instance"] = "Address"+getClientId(elem_end).replace(/_/g,'');
                        params["template"] = "templates/MailAliasAddress.html";
                        params["object_text"] = elem_end;
                        params["icon"] = "images/Tree/mailbox_alias.gif";
                        params["hook"] = "show";
                        var elem_id = elem_id_arr.join("_");
                        var window_elem_id = "Window_"+this.opener_object.module_id.replace(/_/g,"")+elem_id.replace(/_/g,"")+"Address"+getClientId(elem_end).replace(/_/g,"");
                        getWindowManager().show_window(window_elem_id,elem_id,params,'MailApplication_'+this.opener_object.module_id,this.opener_item.id,"Address");
                        break;
                    }
                    elem_id_arr[elem_id_arr.length] = arr[counter];
                }
                break;
            case "remove":
                var arr = mboxid.split("_");
                var elem_id_arr = new Array;
                for (counter=0;counter<arr.length;counter++) {
                    if (arr[counter]=="Addresses") {
                        address = arr.slice(counter+1,arr.length).join("_");
                        break;
                    }
                    elem_id_arr[elem_id_arr.length] = arr[counter];
                }                                                
                if (confirm("Вы действительно хотите удалить адрес "+address+" ?")) {
                	var params = new Object;
                    var menu = this;
                    params["address"] = address;
                    var elem_id = elem_id_arr.join("_");                    
                    new Ajax.Request("index.php", {
                        method: "post",
                        parameters: {ajax: true,object_id: elem_id, hook: '5', arguments: Object.toJSON(params)},
                        onSuccess: function(transport) {
                            var response = trim(transport.responseText.replace("\n",""));
                            if (response.length>1) {
                                var response_object = response.evalJSON();
                                if (response_object["error"]!=null)
                                    menu.reportMessage(response_object["error"],"error",true);
                            }
                            else {
                                var elem_id_arr = elem_id.split("_");
                                var elem_id = elem_id_arr.join("_");
                                menu.raiseEvent("NODE_CHANGED",$Arr("action=delete,object_id="+getClientId(elem_id+"_Addresses_"+address)),true);
                            }
                        }
                    });
                }
                break;
        }
        globalTopWindow.removeContextMenu();
        event = event || window.event;
        event.cancelBubble = true;
    }
});