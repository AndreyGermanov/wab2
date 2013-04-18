var ModelConfig = Class.create(Entity, {	
	    OK_onClick: function(event) {
	        // Работаем только если что-то действительно изменилось
	        var is_changed = this.win.node.getAttribute('changed');
	        if (is_changed!="true")
	            return 0;

	        // Проверяем корректность ввода данных
	        var data = this.getValues();

	        // Формируем параметры, передаваемые серверу для записи
	        var args = data.toObject();
	        
	        // Выводим индикатор прогресса
	        var mbox = this;
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
	                mbox.node.removeChild(loading_img);
	                if (response!="")
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
	                    var old_id = mbox.object_id;
	                    var new_id = mbox.object_id;
	                    var new_target_id = new_id;
	                    var old_owner_id = getClientId("Modules_"+mbox.module_id);
	                    var new_owner_id = old_owner_id;
	                    var params = new Array;
	                    if (mbox.opener_item.parentNode !=null)
	                        params["object_id"] = old_id;
	                    params["new_object_id"] = new_id;
	                    params["target_id"] = new_target_id;
	                    params["text"] = data["title"];
	                    params["old_text"] = data["old_title"];
	                    params["title"] = data["title"];
	                    params["old_title"] = data["old_title"];
	                    params["parent"] = new_owner_id;
                        var icon = mbox.skinPath+"images/Tree/module.png";
	                    params["image"] = icon;
	                    if (data["old_title"]=="")
	                    {
	                        params["action"] = "add";
	                        mbox.raiseEvent("NODE_CHANGED",params,true);
	                    }
	                    else
	                    {
	                        params["action"] = "change";
	                        mbox.raiseEvent("NODE_CHANGED",params,true);
	                    }
	                	
	                    mbox.win.node.setAttribute('changed',"false");
	                    $I(mbox.win.node.id+"_headertext").innerHTML = $I(mbox.win.node.id+"_headertext").innerHTML.replace("*","");                    
	                }
	            }
	        });
	    },

	    help_onClick: function(event) {
	        var params = new Array;
	        getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"guide10","HTMLBook_"+this.module_id+"_controller_10",params,this.opener_item.getAttribute("object"),this.opener_item.id);
	    }
	}
);