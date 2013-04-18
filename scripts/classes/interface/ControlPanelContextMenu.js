var ControlPanelContextMenu = Class.create(ContextMenu,{

    onClick: function(event) {
        var elem = eventTarget(event);
        switch(get_elem_id(elem).replace("_text",""))
        {
            case "check":
                var windowManager = globalTopWindow.getWindowManager();
                windowManager.checkUpdates();
                alert('Будет выполнена проверка наличия обновлений ...');
                if (windowManager.result!=false && windowManager.result!="noconnect") {
                    if (confirm("Появилась новая версия системы. Обновить сейчас ?")) {
                        windowManager.updateSystem();
                    };
                } else if (windowManager.result!="noconnect")
                    this.reportMessage("Система находится в актуальном состоянии.","info",true);
                else
                    this.reportMessage("Нет соединения с сервером обновлений.","error",true);
                break;
            case "options":
                getWindowManager().show_window("Window_ControlPanelPropertiesProps","ControlPanelProperties_Props",null,'EnterpriseApplication_'+this.opener_item.module_id,this.opener_item.id);
                this.opener_item.setAttribute('class',"tree_item_selected");
                break;
        }
        globalTopWindow.removeContextMenu();
        event = event || window.event;
        event.cancelBubble = true;
    }
});