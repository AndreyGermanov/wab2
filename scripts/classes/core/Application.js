var Application = Class.create(Entity, {

    initModules: function() {
        var modules = this.modules_string.split(";");
        var modules_row = $I(this.node.id+"_module_block").parentNode;
        var counter1=0;
        for (counter1=0;counter1<modules.length;counter1++) {
            var module_node = $I(this.node.id+"_module_block").cloneNode(true);
            var mod_str = modules[counter1].split("|");
            module_node.id = mod_str[0]+"_module_block";
            var iframes = module_node.getElementsByTagName("IFRAME");
            var args = new Object;
            args["window_id"] = this.win.id;
            iframes[0].src = "?object_id="+mod_str[0]+"&hook=1&arguments="+Object.toJSON(args);
            if (this.active_tab == mod_str[1])
                module_node.style.display = '';            
            modules_row.appendChild(module_node);
        }
    },

    getModuleByTab: function(tab) {
        var modules = this.modules_string.split(";");
        var counter2=0;
        for (counter2=0;counter2<modules.length;counter2++) {
            var mod_str = modules[counter2].split("|");
            if (mod_str[1]==tab)
                return mod_str[0];
        }
        return 0;
    },

    dispatchEvent: function($super,event,params) {
        $super(event,params);
        if (event=="USER_ONLINE") {
            alert(params["name"]+" вошел в систему "+params["time"]);
            window.defaultStatus = params["name"]+" вошел в систему "+params["time"];
        }
        if (event=="USER_OFFLINE") {
            alert(params["name"]+" отключился "+params["time"]);
            window.defaultStatus = params["name"]+" отключился "+params["time"];
        }
    }
});