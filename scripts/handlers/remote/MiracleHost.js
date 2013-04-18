mhost = $O(object_id,instance_id);
mhost.skinPath = '{skinPath}';
mhost.status = "none";
mhost.parent_object_id = '{parent_object_id}';
{virtual_servers}
mhost.showHosts();
    function applyStatus() {
        new Ajax.Request("index.php", {
            method:"post",
            parameters: {ajax: true, object_id: "MiracleHost_MiracleHost_BootUp",hook: '6'},
            onSuccess: function(transport)
            {
                var response = trim(transport.responseText.replace("\n",""));
                var resp_array = response.split("|");
                mhost.status = resp_array.shift();
                var server_power_button = $I(mhost.node.id+"_serverPowerButton");
                var server_power_label = $I(mhost.node.id+"_serverPowerLabel");
                if (mhost.status=="on") {
                    server_power_button.src = mhost.skinPath+"images/MiracleHost/poweron.png";
                    server_power_button.title = "Выключить";
                    server_power_button.setAttribute("onclick","$O('"+mhost.object_id+"','').virtualServerPowerOnButton_click('rootHost','off')");
                    server_power_label.innerHTML = "Питание сервера (включено)";
                    server_power_label.setAttribute("class","ServerOn");
                } else {
                    server_power_button.src = mhost.skinPath+"images/MiracleHost/poweroff.png";
                    server_power_button.title = "Включить";
                    server_power_button.setAttribute("onclick","$O('"+mhost.object_id+"','').virtualServerPowerOnButton_click('rootHost','on')");
                    server_power_label.innerHTML = "Питание сервера (выключено)";
                    server_power_label.setAttribute("class","ServerOff");
                }
                mhost.alloff = true;
                for (var i=0;i<resp_array.length;i++) {
                    var parts = resp_array[i].split('~');
                    var name = parts[0];
                    var status = parts[1];
                    var server_label =  $I(mhost.node.id+"_"+name+"Label");
                    var server_power_button = $I(mhost.node.id+"_"+name+"PowerOnButton");
                    var server_display_button = $I(mhost.node.id+"_"+name+"RemoteDesktopButton");
                    var server_cp_button = $I(mhost.node.id+"_"+name+"ControlPanelButton");
                    if (status=="off") {
                        server_power_button.src = mhost.skinPath+"images/MiracleHost/poweroff.png";
                        server_power_button.setAttribute("onclick","$O('"+mhost.object_id+"','').virtualServerPowerOnButton_click('"+name+"','on')");
                        server_display_button.src = mhost.skinPath+"images/spacer.gif";
                        server_cp_button.src = mhost.skinPath+"images/spacer.gif";
                        server_label.innerHTML = mhost.virtual_servers[name]["title"]+" (не загружен)";
                        server_label.setAttribute("class","serverOff");
                    }
                    if (status=="loading") {
                        server_power_button.src = mhost.skinPath+"images/MiracleHost/poweron.png";
                        server_power_button.setAttribute("onclick","$O('"+mhost.object_id+"','').virtualServerPowerOnButton_click('"+name+"','off')");
                        server_display_button.src = mhost.skinPath+"images/MiracleHost/display.png";
                        server_cp_button.src = mhost.skinPath+"images/spacer.gif";
                        server_label.innerHTML = mhost.virtual_servers[name]["title"]+" (загружается)";
                        server_label.setAttribute("class","serverLoading");
                        mhost.alloff = false;
                    }
                    if (status=="on") {
                        server_power_button.src = mhost.skinPath+"images/MiracleHost/poweron.png";
                        server_power_button.setAttribute("onclick","$O('"+mhost.object_id+"','').virtualServerPowerOnButton_click('"+name+"','off')");
                        server_display_button.src = mhost.skinPath+"images/MiracleHost/display.png";
                        server_cp_button.src = mhost.skinPath+"images/MiracleHost/cp.png";
                        server_label.innerHTML = mhost.virtual_servers[name]["title"]+" (загружен)";
                        server_label.setAttribute("class","serverOn");
                        mhost.alloff = false;
                    }
                }
                if (mhost.shutting_down && mhost.alloff == true) {
                    var initstring = "$object->load();";
                    initstring = "echo $object->pressPowerButton('rootHost','off','false');";
                    new Ajax.Request("index.php", {
                        method:"post",
                        parameters: {ajax: true, object_id: "MiracleHost_MiracleHost_BootUp",init_string: initstring},
                        onSuccess: function(transport)
                        {
                            response = trim(transport.responseText.replace("\n",""));
                            mhost.shutting_down = false;
                        }
                    });
                }                    
            }
        });
    }
setInterval('applyStatus()',2000);