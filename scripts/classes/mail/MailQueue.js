var MailQueue = Class.create(Mailbox, {

    onRemoveWindow: function (topWindow) {
    	delete topWindow.objects.objects[this.table_id];
    },

    sendSelected_onClick: function(event) {
        $O(this.table_id,'').sendSelected();
    },

    deleteSelected_onClick: function(event) {
        if (confirm("Вы действительно хотите удалить выбранные сообщения ?")) {
            $O(this.table_id,'').delSelected();
            this.reportMessage('Команда отправлена на сервер',"info",true);
        }
    },

    sendAll_onClick: function(event) {
        $O(this.table_id,'').sendMessage('ALL','true');
    },

    deleteAll_onClick: function(event) {
        if (confirm("Вы действительно хотите удалить все сообщения ?")) {
            $O(this.table_id,'').delMessage('ALL','false');
            this.reportMessage('Команда отправлена на сервер',"info",true);
        }
    },
    
    help_onClick: function(event) {
        var params = new Array;
        getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"collector10","HTMLBook_"+this.module_id+"_collector_10",params,this.opener_item.getAttribute("object"),this.opener_item.id);
    }
});