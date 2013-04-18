var LVMSnapshotsDataTable = Class.create(EntityDataTable, {
        
    refreshButton_onClick: function(event) {
        this.rebuild();
    },

    resizeButton_onClick: function(event) {
        if (this.currentControl==null)
            return 0;
        var row_num = this.currentControl.node.parentNode.getAttribute("row");
        this.raiseEvent("CONTROL_DOUBLE_CLICKED",$Arr("object_id="+this.getItem(row_num,0).object_id+",parent_object_id="+this.object_id));
    },
    
    afterRebuild: function () {        
      var old_tr = 0;
      var o=0;
      for (o=0;o<this.table.childNodes.length;o++) {
        old_tr = this.table.childNodes[o];
        if (old_tr!=null)
            if (old_tr.getAttribute!=null)
                if (old_tr.getAttribute("footer")=="true")
                    this.table.removeChild(old_tr);
      };
      var tr = document.createElement("tr");
      tr.setAttribute("footer","true");
      var td = document.createElement("td");      
      td.setAttribute("colspan","5");
      td.setAttribute("class","cell");
      td.innerHTML  = "<b>Доступно для снимков: <font color='#990000'>"+this.snapshotsSize+" Гб.</font><br/>";
      td.innerHTML += "<b>Занято снимками: <font color='#990000'>"+this.usedSize+" Гб.</font><br/>";
      td.innerHTML += "<b>Свободно для снимков: <font color='#990000'>"+this.freeSize+" Гб.</font></b>";
      tr.appendChild(td);
      this.table.appendChild(tr);
    },
    
    addButton_onClick: function(event) {
        var size = prompt("Укажите размер снимка в гигабайтах:");
        if (size) {
            if (size!=parseFloat(size) || size==0) {
                this.reportMessage("Размер указан не верно!","error",true);
                return 0;
            }
        } else
            return 0;
        var args = new Object;
        args["size"] = size;
        var obj = this;
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
            parameters: {ajax: true, object_id: this.object_id,
                         hook: '5', arguments: Object.toJSON(args)},
            onSuccess: function(transport) {
                var response = transport.responseText;
                if (response!="") {
                    response = response.evalJSON(true);
                    if (response["error"]!=null)
                    {
                        obj.reportMessage(response["error"],"error",true);
                        return 0;
                    }
                    else
                        obj.reportMessage(response,"error",true);
                } else {
                  obj.rebuild();  
                }
                obj.node.removeChild(loading_img);
            }
        });
    },
    
    findButton_onClick: function(event) {
    	var ent=null;
        if (this.currentControl!=null & this.currentControl!=0)
            ent = this.getItem(this.currentControl.node.parentNode.getAttribute("row"),0);
        var item=null;
        if (ent!=null && ent!=0)
                item = this.getItem(this.currentControl.node.parentNode.getAttribute("row"),0).getValue();
        if (item!=null) {
        	agrs = new Object;
        	args["item"] = item;
            var obj = this;
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
                parameters: {ajax: true, object_id: this.object_id,
                             hook: '6', arguments: Object.toJSON(args)},
                onSuccess: function(transport) {
                    var response = transport.responseText;
                    if (response!="") {
                        response = response.evalJSON(true);
                        if (response["error"]!=null)
                        {
                            obj.reportMessage(response["error"],"error",true);
                            return 0;
                        }
                        else
                            obj.reportMessage(response,"error",true);
                    } else {
                        window.open('tmp/snapshot.html');
                    }
                    obj.node.removeChild(loading_img);
                }
            });
        }
    },    

    downloadButton_onClick: function(event) {
        var downloadFolder = $I(this.node.id+"_downloadFolder");
        downloadFolder.setAttribute("value","");
        var leftPosition = (screen.availWidth-250)/2;
        var topPosition = (screen.availHeight-300)/2;
        var params = new Object;
        params["target_item"] = downloadFolder.id;
        params["absolute_path"] = "true";
        params["icon"] = this.skinPath+"images/Tree/folder.gif";
        params["title"] = "/";
        var args = new Array;
        this.selectParentWindow = window.showModalDialog("index.php?object_id=DirectoryTree_"+this.module_id+"_Tree1&hook=show&arguments="+Object.toJSON(params),args,"dialogWidth:250px; dialogHeight:300px; dialogLeft:"+leftPosition+"px; dialogTop:"+topPosition+"px;");
        var folder = downloadFolder.getAttribute("value");
        if (folder=="")
            return 0;
        var ent = null;
        if (this.currentControl!=null & this.currentControl!=0)
            ent = this.getItem(this.currentControl.node.parentNode.getAttribute("row"),0);
        var item = null;
        if (ent!=null && ent!=0)
                item = this.getItem(this.currentControl.node.parentNode.getAttribute("row"),0).getValue();
        if (item!=null) {
        	var args = new Object;
        	args["item"] = item;
        	args["folder"] = folder;
            var obj = this;
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
                parameters: {ajax: true, object_id: this.object_id,
                             hook: '7', arguments: Object.toJSON(args)},
                onSuccess: function(transport) {
                    var response = transport.responseText;
                    if (response!="") {
                        response = response.evalJSON(true);
                        if (response["error"]!=null)
                        {
                            obj.reportMessage(response["error"],"error",true);
                            return 0;
                        }
                        else
                            obj.reportMessage(response,"error",true);
                    } else {
                        obj.reportMessage("Снимок скопирован. Он находится в каталоге "+folder+"/@GMT-"+item,"info",true);
                    }
                    obj.node.removeChild(loading_img);
                }
            });
        }
    },    

    deleteButton_onClick: function(event) {
        if (!confirm("Вы действительно хотите удалить этот снимок?"))
            return 0;
        var ent=null;
        if (this.currentControl!=null & this.currentControl!=0)
            ent = this.getItem(this.currentControl.node.parentNode.getAttribute("row"),0);
        var del_elem=null;
        if (ent!=null && ent!=0)
                del_elem = this.getItem(this.currentControl.node.parentNode.getAttribute("row"),0).getValue();
        if (del_elem!=null)  {
        	var args = new Object;
        	args["item"] = del_elem;
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
            parameters: {ajax: true, object_id: obj.object_id,hook:'8',arguments: Object.toJSON(args)},
            onSuccess: function(transport)
            {                            
                var response = trim(transport.responseText);
                if (response!="") {
                    response = response.evalJSON(true);
                    if (response["error"]!=null)
                    {
                        obj.reportMessage(response["error"],"error",true);
                        return 0;
                    }
                    else
                        obj.reportMessage(response,"error",true);
                } else {
                  var old_tr = 0;
                  var o=0;
                  for (o=0;o<obj.table.childNodes.length;o++) {
                    old_tr = obj.table.childNodes[o];
                    if (old_tr!=null)
                        if (old_tr.getAttribute!=null)
                            if (old_tr.getAttribute("footer")=="true")
                                obj.table.removeChild(old_tr);
                  };                    
                  obj.rebuild();  
                }
                obj.node.removeChild(loading_img);                
            }
        });
        }        
    },    
    
    sort: function() {
        return 0;
    },
    
    dispatchEvent: function($super,event,params) {
        if (event=="CONTROL_DOUBLE_CLICKED") {
            if (params["parent_object_id"]==this.object_id) {
                var name = this.getItem($O(params["object_id"],'').node.parentNode.getAttribute("row"),0).getValue();
                var value = this.getItem($O(params["object_id"],'').node.parentNode.getAttribute("row"),3).getValue();
                var size = prompt("Укажите размер снимка в гигабайтах:",value);
                if (size) {
                    if (size!=parseFloat(size) || size==0) {
                        this.reportMessage("Размер указан не верно!","error",true);
                        return 0;
                    }
                } else
                    return 0;
                var args = new Object;
                args["name"] = name;
                args["size"] = size;                
                var obj = this;
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
                    parameters: {ajax: true, object_id: this.object_id,
                                 hook: '9', arguments: Object.toJSON(args)},
                    onSuccess: function(transport) {
                        var response = transport.responseText;
                        if (response!="") {
                            response = response.evalJSON(true);
                            if (response["error"]!=null)
                            {
                                obj.reportMessage(response["error"],"error",true);
                                return 0;
                            }
                            else
                                obj.reportMessage(response,"error",true);
                        } else {
                          obj.rebuild();  
                        }
                        obj.node.removeChild(loading_img);
                    }
                });                
            }
            return 0;
        }
        $super(event,params);
    }
});