var UserContextMenu = Class.create(ContextMenu,{
    onClick: function(event) {
        var elem = eventTarget(event);
        var module_id = this.opener_object.module_id;
        switch(get_elem_id(elem).replace("_text",""))
        {
            case "change":
                var user = this.opener_item.getAttribute("target_object");
                var wuser = this.opener_item.getAttribute("target_object").replace(/_/g,"");
                var params = new Array;
                getWindowManager().show_window("Window_"+wuser,user,params,this.opener_object,this.opener_item.id);
                break;
            case "remove":
                var user = this.opener_item.getAttribute("target_object").split("_").pop();
                if (user=="guest") {
                    menu.reportMessage("Системного пользователя guest удалять запрещено!","error",true);                    
                    globalTopWindow.removeContextMenu();
                    event = event || window.event;
                    event.cancelBubble = true;
                    return 0;
                }                    
                var mailModule = menu.mailModule;
                var domain = menu.domain;
                var userName = menu.userName;
                var mailIntegration = menu.mailIntegration;
                var authType = menu.authType;
                if (confirm("Вы действительно хотите удалить этого пользователя ?"))
                {
                    var menu = this;
                    new Ajax.Request("index.php",{
                        method:"post",
                        parameters: {ajax:true,object_id:"User_"+module_id+"_"+user,hook: '4'},
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
                                menu.raiseEvent("NODE_CHANGED",$Arr("action=delete,object_id="+getClientId("User_"+module_id+"_"+user)),true);
                                if (mailIntegration)
                                    menu.raiseEvent("NODE_CHANGED",$Arr("action=delete,object_id=Mailbox_"+mailModule+"_"+userName+"_"+getClientId(domain)),true);
                                if (authType)
                                	menu.raiseEvent("NODE_CHANGED",$Arr("action=delete,object_id="+getClientId("ApacheUser_"+module_id+"_"+user)),true);                    	
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