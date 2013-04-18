var WebSiteContextMenu = Class.create(ContextMenu,{

    onClick: function(event) {
        var elem = eventTarget(event);
        switch(get_elem_id(elem).replace("_text",""))
        {
            case "change":
                var site = this.opener_item.getAttribute("target_object");
                site = site.replace("WebSite_","");
                var params = new Array;
                getWindowManager().show_window("Window_WebSite"+site,"WebSite_"+site,params,this.opener_object,this.opener_item.id);
                break;
            case "remove":
                this.module_id = this.opener_object.module_id;
                var site = this.opener_item.getAttribute("target_object");
                site = site.replace("WebSite_","");
                site = site.replace(this.module_id+"_","");
                if (confirm("Вы действительно хотите удалить сайт "+site+" и все его содержимое?"))
                {
                    var initstring="$object->remove('"+site+"');";
                    var menu = this;
                    new Ajax.Request("index.php",{
                        method:"post",
                        parameters: {ajax:true,object_id:menu.module_id,init_string:initstring},
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
                                menu.raiseEvent("NODE_CHANGED",$Arr("action=delete,object_id="+getClientId("WebSite_"+menu.module_id+"_"+site)),true);                                
                            }
                        }

                    });
                }
                break;
            case "add_chapter":
                var site = this.opener_item.getAttribute("target_object");
                site = site.replace("WebSite_","");
                var params = new Array;
                params[0] = "$object->init_string='$object->openAs=\"asAdminChapter\";$object->setTemplate()';";
                getWindowManager().show_window("Window_WebItem"+site,"WebItem_"+site+"__1",params,this.opener_object,this.opener_item.id);
                break;
        }
        topWindow.removeContextMenu();
        event = event || window.event;
        event.cancelBubble = true;
    }
});