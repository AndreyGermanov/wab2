var FileServerContextMenu = Class.create(ContextMenu,{

    onClick: function(event) {
        var elem = eventTarget(event);
        var module_id = this.opener_object.module_id;
        switch(get_elem_id(elem).replace("_text",""))
        {
            case "report":
                var params = new Array;                
                getWindowManager().show_window("Window_FullAuditReport"+this.opener_object.module_id.replace(/_/g,""),"FullAuditReport_"+this.opener_object.module_id+"_report",params,this.opener_object,this.opener_item.id);
                break;
            case "restart":
                if (confirm("Вы действительно хотите перезапустить файловые службы ?"))
                {
                    var menu = this;
                    new Ajax.Request("index.php",{
                        method:"post",
                        parameters: {ajax:true,object_id:"FileServer_"+module_id+"_Shares",hook: '4'},
                        onSuccess:function(transport) {
                            var response = trim(transport.responseText.replace("\n",""));
                            if (response!="")
                                menu.reportMessage("Файловые службы перезапущены.\nСервер выдал следующее при перезапуске служб:\n\n"+response,"info");                            
                            else
                                menu.reportMessage("Файловые службы перезапущены.","info");                            
                            
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