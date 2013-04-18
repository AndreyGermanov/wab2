var MailQueueTable = Class.create(Mailbox, {

    build: function(data) {
        var tbl = $I(this.node.id+"_table");
        tbl.setAttribute("width","98%");
        tbl.setAttribute("height","100%");
        tbl.setAttribute("bgcolor","#AAAAAA");
        tbl.setAttribute("cellspacing","1");
        tbl.setAttribute("cellpadding","2");
        var elems = $I(this.node.id+"_table").getElementsByTagName("TR");
        var exists = new Array;
        for (var c=0;c<elems.length;c++) {
            exists[elems[c].id] = "OK";
        }
        tbl.innerHTML = '';
        var tr = document.createElement("tr");
        tr.setAttribute("class","header");
        tr.setAttribute("valign","top");
        tr.style.height="0";
        var td = document.createElement("td");
        td.setAttribute("object",this.object_id);
        checkbox = document.createElement("input");
        checkbox.setAttribute("type","checkbox");
        checkbox.id=this.node.id+"_checkAllQueue";
        checkbox.setAttribute("onclick","$O('"+this.object_id+"','').checkAllQueue(event)");
        td.appendChild(checkbox);
        tr.appendChild(td);
        td = document.createElement("td");
        td.setAttribute("object",this.object_id);
        td.innerHTML = "ID";
        tr.appendChild(td);
        td = document.createElement("td");
        td.setAttribute("object",this.object_id);
        td.innerHTML = "Время";
        tr.appendChild(td);
        td = document.createElement("td");
        td.setAttribute("object",this.object_id);
        td.innerHTML = "Отправитель";
        tr.appendChild(td);
        td = document.createElement("td");
        td.setAttribute("object",this.object_id);
        td.innerHTML = "Получатель";
        tr.appendChild(td);
        td = document.createElement("td");
        td.setAttribute("object",this.object_id);
        td.setAttribute("object",this.object_id);
        td.innerHTML = "Тема";
        tr.appendChild(td);
        td = document.createElement("td");
        td.setAttribute("object",this.object_id);
        td.innerHTML = "&nbsp;";
        tr.appendChild(td);
        tbl.appendChild(tr);
        data = data.split("|");
        for (var c=0;c<data.length;c++) {
            var cls = null;
            arr = data[c].split("~");
            if (arr.length<2)
                continue;
            var id   = arr[1];
            var time = arr[2];
            var from = arr[3];
            var to   = arr[4];
            var subject   = arr[5];
            var message   = arr[7];
            var q    = arr[6];
            if (q=="active")
                cls = "cell";
            if (q=="incoming")
                cls = "expandable_cell1";
            if (q=="hold")
                cls = "expandable_cell";
            if (q=="deferred")
                cls = "red_cell";
            tr = document.createElement("tr");
            tr.setAttribute("valign","top");
            tr.id = this.node.id+"_"+id+"_header";
            if (exists[tr.id]==null)
                cls = "yellow_cell";
            if (cls!=null)
                tr.setAttribute("class",cls);
            td = document.createElement("td");
            td.setAttribute("object",this.object_id);
            var checkbox = document.createElement("input");
            checkbox.setAttribute("type","checkbox");
            checkbox.id=this.node.id+"_"+id+"_checkbox";
            td.appendChild(checkbox);
            tr.appendChild(td);
            td = document.createElement("td");
            td.setAttribute("object",this.object_id);
            td.innerHTML = id;
            tr.appendChild(td);
            td = document.createElement("td");
            td.setAttribute("object",this.object_id);
            td.innerHTML = time;
            tr.appendChild(td);
            td = document.createElement("td");
            td.setAttribute("object",this.object_id);
            td.innerHTML = from;
            tr.appendChild(td);
            td = document.createElement("td");
            td.setAttribute("object",this.object_id);
            td.innerHTML = to;
            tr.appendChild(td);
            td = document.createElement("td");
            td.setAttribute("object",this.object_id);
            td.innerHTML = subject;
            tr.appendChild(td);
            td = document.createElement("td");
            td.setAttribute("object",this.object_id);
            var img = document.createElement("img");
            img.src = this.skinPath+"images/Tree/mail.gif";
            img.setAttribute("border","0");
            img.setAttribute("title","Показать письмо");
            img.setAttribute("onclick","$O('"+this.object_id+"','').showMessage('"+id+"')");
            img.setAttribute("alt","Показать письмо");
            td.appendChild(img);
            
            var img1 = document.createElement("img");
            img1.src = this.skinPath+"images/Tree/sendmail.png";
            img1.setAttribute("border","0");
            img1.setAttribute("title","Отправить еще раз");
            img1.setAttribute("onclick","$O('"+this.object_id+"','').sendMessage('"+id+"','true')");
            img1.setAttribute("alt","Отправить еще раз");
            img1.setAttribute("width","16");
            img1.setAttribute("height","16");
            td.appendChild(img1);
            tr.appendChild(td);

            var img2 = document.createElement("img");
            img2.src = this.skinPath+"images/Tree/delmail.png";
            img2.setAttribute("border","0");
            img2.setAttribute("title","Удалить письмо");
            img2.setAttribute("onclick","$O('"+this.object_id+"','').delMessage('"+id+"','true')");
            img2.setAttribute("alt","Удалить письмо");
            td.appendChild(img2);

            if (q=="deferred") {
                var img3 = document.createElement("img");
                img3.src = this.skinPath+"images/Tree/info.png";
                img3.setAttribute("border","0");
                img3.setAttribute("title","Информация об ошибке");
                img3.setAttribute("onclick","$O('"+this.object_id+"','').getDeferReason('"+id+"')");
                img3.setAttribute("alt","Информация об ошибке");
                td.appendChild(img3);
            }
            tr.appendChild(td);
            tbl.appendChild(tr);
            tr = document.createElement("tr");            
            tr.setAttribute("class","black_cell");
            tr.id = this.node.id+"_"+id+"_message";
            tr.style.display = 'none';
            td = document.createElement("td");
            td.setAttribute("object",this.object_id);
            td.innerHTML = message;
            td.setAttribute('colspan',7);
            tr.appendChild(td);
            tbl.appendChild(tr);
        }
        tr = document.createElement("tr");
        tr.style.height="100%";
        td = document.createElement("td");
        td.setAttribute("object",this.object_id);
        td.setAttribute("colspan",7);
        td.setAttribute("style","background-color:#FFFFFF;height:100%");
        td.style.height = "100%";
        td.innerHTML = '&nbsp;';
        tr.appendChild(td);
        tbl.appendChild(tr);
    },

    showMessage: function(id) {
        var msg_row = $I(this.node.id+"_"+id+"_message");
        if (msg_row.style.display == 'none')
            msg_row.style.display = '';
        else
            msg_row.style.display = 'none';
    },

    checkAllQueue: function(event) {
        var cb = eventTarget(event);
        var checked = cb.checked;
        var elems = $I(this.node.id+"_table").getElementsByTagName("INPUT");
        for (var c=0;c<elems.length;c++)
            elems[c].checked = checked;
    },

    sendMessage: function(id,question) {
    	var params = new Object;
    	params["id"] = id;
    	var obj=this;
        new Ajax.Request("index.php", {
            method:"post",
            parameters: {ajax: true, object_id: "MailQueueTable_"+qt.module_id+"_Mail",hook: '3', arguments: Object.toJSON(params)},
            onSuccess: function(transport)
            {
                var response = trim(transport.responseText.replace("\n",""));
                if (response!="") {
                    if (question=="true")
                        obj.reportMessage(response,"error",true);
                }
                else {
                    if (question=="true")
                        obj.reportMessage("Команда передана на сервер!","info",true);
                }
            }
        });
    },

    sendSelected: function() {
        var elems = $I(this.node.id+"_table").getElementsByTagName("INPUT");
        for (var c=0;c<elems.length;c++) {
            if (elems[c].checked==false)
                continue;
            var arr = elems[c].id.split("_");
            var ending = arr.pop();
            if (ending=="checkAllQueue")
                continue;
            var id = arr.join("_");
            id = id.replace(this.node.id+"_",'');
            this.sendMessage(id,'false');
        }
        this.reportMessage('Команда отправлена на сервер',"info",true);
    },

    delMessage: function(id,question) {
        if (question=='true')
            if (!confirm("Вы действительно хотите удалить это письмо ?"))
                return 0;
        var params = new Object;
        params["id"] = id;
        var obj=this;
        new Ajax.Request("index.php", {
            method:"post",
            parameters: {ajax: true, object_id: "MailQueueTable_"+qt.module_id+"_Mail",hook: '4', arguments: Object.toJSON(params)},
            onSuccess: function(transport)
            {
                var response = trim(transport.responseText.replace("\n",""));
                if (response!="") {
                    if (question=='true') 
                        obj.reportMessage(response,"error",true);
                }
                else {
                    if (question=='true')
                        obj.reportMessage('Команда передана на сервер',"info",true);
                }
            }
        });
    },

    delSelected: function() {
        var elems = $I(this.node.id+"_table").getElementsByTagName("INPUT");
        for (var c=0;c<elems.length;c++) {
            if (elems[c].checked==false)
                continue;
            var arr = elems[c].id.split("_");
            var ending = arr.pop();
            if (ending=="checkAllQueue")
                continue;
            var id = arr.join("_");
            id = id.replace(this.node.id+"_",'');
            this.delMessage(id,'false');
        }
        this.reportMessage('Команда отправлена на сервер',"info",true);
    },

    getDeferReason: function(id) {
    	var params = new Object;
    	params["id"] = id;
    	var obj=this;
        new Ajax.Request("index.php", {
            method:"post",
            parameters: {ajax: true, object_id: "MailQueueTable_"+qt.module_id+"_Mail",hook: '5', arguments: Object.toJSON(params)},
            onSuccess: function(transport)
            {
                var response = trim(transport.responseText.replace("\n",""));
                obj.reportMessage(response,"info",true);
            }
        });
    }
});