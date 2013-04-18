//include scripts/handlers/interface/Tree.js
globalTopWindow.online_network_monitor = '{online_network_monitor}';
globalTopWindow.online_network_monitor_update_period = {online_network_monitor_update_period};
globalTopWindow.online_network_monitor_changed = {online_network_monitor_changed};
tree.hosts_info_table = new Array;

function updateHostsInfo() {
    if (globalTopWindow.online_network_monitor_changed==true) {
        if (globalTopWindow.online_network_monitor=="0") {
            for (cc in tree.hosts_info_table) {
                if (typeof(tree.hosts_info_table[cc])=="string") {
                    tree.raiseEvent("CHANGE_HOST_STATUS",$Arr("host="+cc+",status=yes"));
                    tree.hosts_info_table[cc] = "yes";
                }
            }
        }
        globalTopWindow.online_network_monitor_changed = false;
    }
    if (globalTopWindow.online_network_monitor=="1") {
    obj1 = tree;
        new Ajax.Request("index.php", {
            method:"post",
            parameters: {ajax: true, object_id: "ControllerTree_"+tree.module_id+"_Tree",hook: '9'},
            onSuccess: function(transport)
            {
                data1 = trim(transport.responseText.replace("\n",""));
                arr1 = new Array;
                data1 = data1.split("|");
                for (var cc=0;cc<data1.length;cc++) {
                    parts1 = data1[cc].split("~");
                    arr1[parts1[0]] = parts1[1];
                    if (obj1.hosts_info_table[parts1[0]]!=arr1[parts1[0]]) {
                        obj1.raiseEvent("CHANGE_HOST_STATUS",$Arr("host="+parts1[0]+",status="+arr1[parts1[0]]));
                        obj1.hosts_info_table[parts1[0]] = arr1[parts1[0]];
                    };
                };
            };
        });
    };
}
globalTopWindow.monitorTimer = setInterval('updateHostsInfo()',globalTopWindow.online_network_monitor_update_period);