var MailApplication = Class.create(Mailbox, {
    connectionTest_onClick: function(event) {
        var is_changed = this.win.node.getAttribute('changed');
        
        if (is_changed =="true")
            if (!confirm("Для того чтобы проверить подключение, необходимо сохранить и применить сделанные изменения. Вы согласны ?"))
                return 0;
        this.networkSettingsOK_onClick(event,'no');
        var data = this.getValues();
        var obj = this;
        var params = data.toObject();
        new Ajax.Request("index.php",
        {
            method: "post",
            parameters: {ajax: true, object_id: obj.object_id,
                         hook: '3', arguments: Object.toJSON(params)},
            onSuccess: function(transport) {
                var result = transport.responseText;                
                if (result==2) {
                    obj.reportMessage('Локальная сеть недоступна !',"error",true);
                    return 0;
                }
                params = new Object;
                params["ipaddr"] = "ya.ru";
                new Ajax.Request("index.php",
                {
                    method: "post",
                    parameters: {ajax: true, object_id: obj.object_id,
                                 hook:'3',arguments: Object.toJSON(params)},
                    onSuccess: function(transport) {
                        var result = transport.responseText;                        
                        if (result==2) {
                            obj.reportMessage('Нет доступа к Интернет !',"error",true);
                            return 0;
                        }
                        obj.remoteMessage('Тест прошел успешно !',"info",true);
                    }
                });
            }
        });
    }
});