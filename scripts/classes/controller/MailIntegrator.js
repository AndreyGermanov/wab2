var MailIntegrator = Class.create(Mailbox, {    
    OK_onClick: function(event) {        
        if (blur_error!="")
            return 0;
        var is_changed = this.win.node.getAttribute('changed');
        if (is_changed =="true") {
            var data = this.getValues();               
            var params = data.toObject();
            var mbox = this;
            var loading_img = document.createElement("img");                
            loading_img.src = this.skinPath+"images/Tree/loading2.gif";
            loading_img.style.zIndex = "100";
            loading_img.style.position = "absolute";
            loading_img.style.top=(window.innerHeight/2-33);
            loading_img.style.left=(window.innerWidth/2-33);        
            this.node.appendChild(loading_img);
            new Ajax.Request("index.php",
            {
                method: "post",
                parameters: {ajax: true, object_id: mbox.object_id,
                             hook: '3', arguments: Object.toJSON(params)},
                onSuccess: function(transport) {
                    var response = trim(transport.responseText).replace("\n","");
                    mbox.node.removeChild(loading_img);
                    if (response.length>1)
                    {
                        var response_object = response.evalJSON();
                        if (response_object["error"]!=null)
                            mbox.reportMessage(response_object["error"],"error",true);
                        else
                            mbox.reportMessage(response,"error",true);
                    }
                    else
                    {
                        mbox.win.node.setAttribute("changed","false");
                        getWindowManager().remove_window(mbox.win.id);
                    }
                }
            });
        }
        else {
            if (this.win!="")
                getWindowManager().remove_window(this.win.id);
        }
    },
    
    help_onClick: function(event) {
        var params = new Array;
        getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"controller43","HTMLBook_"+this.module_id+"_controller_4.3",params,this.opener_item.getAttribute("object"),this.opener_item.id);
    }           
});