var ScanNetworkTable = Class.create(Mailbox, {

    build: function() {
        var table = $I(this.node.id+"_table");
        table.setAttribute("cellspacing","1");
        table.style.height="100%";
        table.setAttribute("bgcolor","#000000");
        var tr = document.createElement("TR");
        tr.setAttribute("object",this.object_id);
        var td = document.createElement("TD");
        td.setAttribute("object",this.object_id);
        td.setAttribute("class","header");
        td.innerHTML = "MAC-адрес";
        td.style.width="10%";
        tr.appendChild(td);
        td = document.createElement("TD");
        td.setAttribute("class","header");
        td.setAttribute("object",this.object_id);
        td.style.width="10%";
        td.innerHTML = "IP-адрес";
        tr.appendChild(td);
        td = document.createElement("TD");
        td.setAttribute("object",this.object_id);
        td.setAttribute("class","header");
        td.style.width="15%";
        td.setAttribute("nowrap","true");
        td.innerHTML = "Имя хоста";
        tr.appendChild(td);
        td = document.createElement("TD");
        td.setAttribute("object",this.object_id);
        td.setAttribute("class","header");
        td.style.width="65%";
        td.innerHTML = "Комментарий";
        tr.appendChild(td);
        table.appendChild(tr);
        var network_table = this.network_table.split("|");
        var c=0;
        for (c=0;c<network_table.length;c++) {
            tr = document.createElement("TR");
            tr.setAttribute("object",this.object_id);
            var network_components = network_table[c].split(",");
            var scan_mac = network_components[0];
            var scan_ip = network_components[1];
            var ip_addr_in_base = network_components[2];
            var host_name = network_components[3];
            var host_in_base = network_components[4];
            var comment = "";
            var host_type = "";
            if (scan_mac=="") {
                var cls= "expandable_cell1";
                comment = "Это сам сканирующий сервер. Имя хоста: "+host_name+" ";
                host_type = "this_host";
            } else {
                if (host_in_base!="") {
                    host_name = host_in_base;
                    comment += "Уже есть в базе. Имя хоста: "+host_in_base;
                    if (scan_ip!=ip_addr_in_base || host_name.toUpperCase() != host_in_base.toUpperCase()) {
                        cls = "expandable_cell1";
                        host_type="exists";
                        if (scan_ip!=ip_addr_in_base) {
                            cls = "red_cell";
                            comment += "<br> ОШИБКА: В базе IP-адрес: "+ip_addr_in_base+", а на хосте прописан "+scan_ip;
                        }
                        if (host_name.toUpperCase()!=host_in_base.toUpperCase() && host_name!="") {
                            cls = "red_cell";
                            comment += "<br> ОШИБКА: В базе имя хоста: "+host_in_base+", а на хосте прописан "+host_name;
                            tr.style.cursor = 'pointer';
                            tr.setAttribute("host_name",host_in_base);
                            host_name = host_in_base;
                        }
                    }
                    else {
                        cls= "expandable_cell1";
                        tr.style.cursor = 'pointer';
                        comment = "Уже есть в базе. Имя хоста: "+host_in_base;
                        host_type="exists";
                        tr.setAttribute("host_name",host_in_base);
                        host_name = host_in_base;
                    }
                } else {
                    cls="cell";
                    comment = "Новый узел";
                    host_type = "new";
                    tr.style.cursor = 'pointer';
                    tr.setAttribute("host_name",host_name);
                }
            }
            td = document.createElement("TD");
            td.setAttribute("object",this.object_id);
            td.setAttribute("class",cls);
            td.innerHTML = scan_mac;
            td.setAttribute("onClick","$O('"+this.object_id+"','"+this.instance_id+"').cellClick(event)");            
            tr.appendChild(td);
            td = document.createElement("TD");
            td.setAttribute("object",this.object_id);
            td.setAttribute("class",cls);
            td.innerHTML = scan_ip;
            td.setAttribute("onClick","$O('"+this.object_id+"','"+this.instance_id+"').cellClick(event)");
            tr.appendChild(td);
            td = document.createElement("TD");
            td.setAttribute("object",this.object_id);
            td.setAttribute("class",cls);
            td.innerHTML = host_name;
            td.setAttribute("onClick","$O('"+this.object_id+"','"+this.instance_id+"').cellClick(event)");
            tr.appendChild(td);
            td = document.createElement("TD");
            td.setAttribute("object",this.object_id);
            td.setAttribute("class",cls);
            td.innerHTML = comment;
            td.setAttribute("onClick","$O('"+this.object_id+"','"+this.instance_id+"').cellClick(event)");
            tr.appendChild(td);
            tr.setAttribute("host_type",host_type);
            tr.setAttribute("mac_address",scan_mac);
            tr.setAttribute("ip_address",scan_ip);            
            table.appendChild(tr);
        }        
        tr = document.createElement("tr");            
        tr.setAttribute("object",this.object_id);
        tr.style.height="100%";
        tr.setAttribute("bgcolor","#FFFFFF");
        td = document.createElement("td");
        td.setAttribute("object",this.object_id);
        td.style.height="100%";
        td.innerHTML = "&nbsp;";
        td.setAttribute("colspan",4);
        tr.appendChild(td);
        table.appendChild(tr);
    },
    
    cellClick: function(event) {
        var tr = eventTarget(event).parentNode;
        var host_type = tr.getAttribute("host_type");
        var host_name = tr.getAttribute("host_name");
        var mac_address = tr.getAttribute("mac_address");
        var ip_address = tr.getAttribute("ip_address");
        var params = new Object;
        if (host_type=="this_host")
            return 0;
        if (host_type!="new") {
            if (host_name!="") {
                var elem_id = "DhcpHost_"+this.module_id+"_"+this.subnet+"_"+host_name;
                var elemid = "Window_"+elem_id.replace(/_/g,"");
                topWindow.getWindowManager().show_window(elemid,elem_id,params,this.opener_object,this.opener_item.id);
            }
        } else {
        	params["hook"] = "setParams";
        	params["nametitle"] = host_name;
        	params["fixed_address"] = ip_address;
        	params["hw_address"] = mac_address;
            var elem_id = "DhcpHost_"+this.module_id+"_"+this.subnet+"_";
            var elemid = "Window_"+elem_id.replace(/_/g,"");
            topWindow.getWindowManager().show_window(elemid,elem_id,params,this.opener_object,this.opener_item.id);
        }
    }
});