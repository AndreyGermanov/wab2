var EntityMenu = Class.create(Menu, {
    fillItems: function($super) {
        if (this.data==null || this.data=="") {
            var obj = this;            
            new Ajax.Request("index.php", {
                    method: "post",
                    parameters: {ajax: true, object_id: obj.object_id,hook: '3'},
                    onSuccess: function(transport) {
                        var response = transport.responseText.toString();                        
                        if (response!="") {
                            obj.data = response;
                            $super();
                        }
                    }
               });
        } else
            $super();
    }    
});