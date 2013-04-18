var DhcpServerContextMenu = Class.create(ContextMenu,{

    onClick: function(event) {
        var elem = eventTarget(event);
        switch(get_elem_id(elem).replace("_text",""))
        {
            case "add":
                var params = new Array;                
                getWindowManager().show_window("Window_DhcpSubnet"+this.opener_object.module_id.replace(/_/g,""),"DhcpSubnet_"+this.opener_object.module_id+"_",params,this.opener_object,this.opener_item.id);
                break;
            case "report":
                var params = new Array;
                getWindowManager().show_window("Window_NetCenterReport"+this.opener_object.module_id.replace(/_/g,""),"NetCenterReport_"+this.opener_object.module_id+"_Report",params,this.opener_object,this.opener_item.id);
                break;
            case "restart":
                if (confirm("Вы действительно хотите перезапустить службы сетевого центра?"))
                {
                    var menu = this;
                    new Ajax.Request("index.php",{
                        method:"post",
                        parameters: {ajax:true,object_id:"DhcpServer_"+this.opener_object.module_id+"_Network",hook: '4'},
                        onSuccess:function(transport) {
                            var response = trim(transport.responseText.replace("\n",""));
                            if (response!="")
                                menu.reportMessage("Cлужбы сетевого центра перезапущены.\nСервер выдал следующее при перезапуске служб:\n\n"+response,"info",false);                            
                            else
                                menu.reportMessage("Cлужбы сетевого центра перезапущены.","info",false);                            
                        }
                    });
                }
                break;
        }
        topWindow.removeContextMenu();
        event = event || window.event;
        event.cancelBubble = true;
    }
});