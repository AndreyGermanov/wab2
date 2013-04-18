var AddressBookDefaultFields = Class.create(Entity, {
	
    OK_onClick: function(event) {
        // Работаем только если что-то действительно изменилось
        var is_changed = this.win.node.getAttribute('changed');
        if (is_changed!="true")
            return 0;

        // Проверяем корректность ввода данных
        var data = this.getValues();

        // Формируем параметры, передаваемые серверу для записи
        var args = data.toObject();
        args["fields"] = this.fieldParamsTable.getSingleValue();
        // Выводим индикатор прогресса
        var obj = this;
        var loading_img = document.createElement("img");                
        loading_img.src = this.skinPath+"images/Tree/loading2.gif";
        loading_img.style.zIndex = "100";
        loading_img.style.position = "absolute";
        loading_img.style.top=(window.innerHeight/2-33);
        loading_img.style.left=(window.innerWidth/2-33);        	        
        this.node.appendChild(loading_img);
        
        new Ajax.Request("index.php", {
            method:"post",
            parameters: {ajax: true, object_id: this.object_id, hook: '3', arguments: Object.toJSON(args)},
            onSuccess: function(transport)
            {
                var response = trim(transport.responseText.replace("\n",""));
                obj.node.removeChild(loading_img);
                if (response!="")
                {
                    response = response.evalJSON(true);
                    if (response["error"]!=null)
                    {
                        obj.reportMessage(response["error"],"error",true);
                        return 0;
                    }
                    else
                        obj.reportMessage(response,"error",true);
                }
                else {             	
                    obj.win.node.setAttribute('changed',"false");
                    $I(obj.win.node.id+"_headertext").innerHTML = $I(obj.win.node.id+"_headertext").innerHTML.replace("*","");
                }
            }
        });
    }		
});