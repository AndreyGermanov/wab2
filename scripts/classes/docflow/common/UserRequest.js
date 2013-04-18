var UserRequest = Class.create(Entity, {

    send_onClick: function(event) {

        // Проверяем корректность ввода данных
        var data = this.getValues();
                
        // Формируем параметры, передаваемые серверу для записи
        var args = data.toObject();        
        var mbox = this;
        var wm = getWindowManager();
        var loading_img = document.createElement("img");                
        loading_img.src = this.skinPath+"images/Tree/loading2.gif";
        loading_img.style.zIndex = "100";
        loading_img.style.position = "absolute";
        loading_img.style.top=(window.innerHeight/2-33);
        loading_img.style.left=(window.innerWidth/2-33);        
        this.node.appendChild(loading_img);
        new Ajax.Request("index.php", {
            method:"post",
            parameters: {ajax: true, object_id: mbox.object_id,hook: '3', arguments: Object.toJSON(args)},
            onSuccess: function(transport)
            {
                var response = trim(transport.responseText.replace("\n",""));
                mbox.node.removeChild(loading_img);
                if (response!="" && response!=parseInt(response))
                {
                    response = response.evalJSON(true);
                    if (response["error"]!=null)
                    {
                        mbox.reportMessage(response["error"],"error",true);
                        return 0;
                    }
                    else
                        mbox.reportMessage(response,"error",true);
                }
                else {
                	mbox.reportMessage("Ваше сообщение отправлено. Спасибо!","info");
                    mbox.win.node.setAttribute('changed',"false");
                    wm.remove_window(mbox.win.id);
                }
            }
        });
    }
});