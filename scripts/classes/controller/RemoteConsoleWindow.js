var RemoteConsoleWindow = Class.create(FrameWindow, {    
    onRemoveWindow: function(window) {
        var obj = this;
        var args = new Object;
        args["cmd"] = this.cmd;
        new Ajax.Request("index.php",{
            method:"post",
            parameters: {ajax:true,object_id:obj.object_id,hook: '4',arguments: Object.toJSON(args)},
            onSuccess:function(transport) {
                var response = trim(transport.responseText.replace("\n",""));
            }
        });
    }
});