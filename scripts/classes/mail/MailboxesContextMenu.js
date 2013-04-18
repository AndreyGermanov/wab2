var MailboxesContextMenu = Class.create(ContextMenu,{

    onClick: function(event) {
        var elem = eventTarget(event);
        var module_id = this.opener_object.module_id;
        var moduleid = module_id.replace(/_/g,"");
        switch(get_elem_id(elem).replace("_text",""))
        {
            case "add":
                var domain = this.opener_item.getAttribute("target_object");
                domain = domain.split("_");domain.pop();domain = domain.pop();
                var params = new Array;
                getWindowManager().show_window("Window_Mailbox"+module_id.replace(/_/g,"")+domain,"Mailbox_"+module_id+"__"+domain,params,this.opener_object,this.opener_item.id);
                break;
            case "add_list":
                var domain = this.opener_item.getAttribute("target_object");
                domain = domain.split("_");domain.pop();domain = domain.pop();
                var params = new Array;
                getWindowManager().show_window("Window_MailAlias"+moduleid+domain,"MailAlias_"+module_id+"__"+domain,params,this.opener_object,this.opener_item.id);
                break;
            case "change":
                var domain = this.opener_item.getAttribute("target_object");
                domain = domain.split("_");domain.pop();domain = domain.pop();
                var params = new Array;
                getWindowManager().show_window("Window_MailDomain"+moduleid+domain,"MailDomain_"+module_id+"_"+domain,params,this.opener_object,this.opener_item.id);
                break;
            case "remove":

                var domain = this.opener_item.getAttribute("target_object");
                domain = domain.split("_");domain.pop();domain = domain.pop();
                if (confirm("Вы действительно хотите удалить домен "+name+" и все его почтовые ящики вместе с почтой?"))
                {
                	var params = new Object;
                	params["domain"] = domain;
                    var menu = this;
                    new Ajax.Request("index.php",{
                        method:"post",
                        parameters: {ajax:true,object_id:"MailDomains_"+module_id,hook: '4', arguments: Object.toJSON(params)},
                        onSuccess:function(transport) {
                            var response = trim(transport.responseText.replace("\n",""));
                            if (response.length>1)
                            {
                                response = response.evalJSON();
                                if (response["error"]!=null)
                                    menu.reportMessage(response["error"],"error",true);
                            }
                            else
                            {
                                menu.raiseEvent("NODE_CHANGED",$Arr("action=delete,object_id="+getClientId(module_id+"_"+domain+"_domain")),true);
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