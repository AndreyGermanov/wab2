var HTMLBook = Class.create(Mailbox, {

    go_to: function(id,change_select,event,dont_write_history) {
        if (event != null) {
            event = event || window.event;
            event.cancelBubble = true;
        }
        if (obj.toc_by_id[id]!=null) {
            $I(this.node.id+"_contentframe").src = obj.doc_path+"/"+obj.toc_by_id[id]["link"];
            this.prev_id = this.current_id;
            this.current_id = id;
        
            if (change_select) {
                for (var c=0;c<$I(this.node.id+"_current_id").options.length;c++) {
                    if ($I(this.node.id+"_current_id").options[c].value == id) {
                        $I(this.node.id+"_current_id").selectedIndex = c;
                    }
                }
            }
            if (dont_write_history != true) {
                if (typeof(this.history) != "object")
                    this.history = new Array;
                this.history[this.history.length] = id;
            }
        }
        if (event!=null) {
             if (event.preventDefault)
                event.preventDefault();
             else
                event.returnValue= false;
        }
        return false;
        
    },

    current_id_onChange: function(event) {
        this.go_to(eventTarget(event).value,false,event);
    },

    onChange: function(event) {
        return 0;
    },

    home_onClick: function(event) {
        this.go_to("1",true,event);
    },

    up_onClick: function(event) {
        var current = this.toc_by_id[this.current_id].link.split("#").shift();
        this.go_to(this.toc_by_link[current].id,true,event);
    },

    back_onClick: function(event) {
    	var id="";
        if (this.history.length>0)
            id = this.history.pop();
        if (id==this.current_id) {
            if (this.history.length>0)
                id = this.history.pop();
        }
        if (id!=this.current_id)
            this.go_to(id,true,event,true);
    },

    next_onClick: function(event) {
        var current = this.toc_by_id[this.current_id].link.split("#").shift();
        var found = false;
        var c=0;
        for (c in this.toc_by_id) {
            if (this.toc_by_id[c].link.split("#").shift() == current) {
                found = true;
            }
            if (found && this.toc_by_id[c].link.split("#").shift() != current) {
                this.go_to(this.toc_by_id[c].id,true,event);
                break;
            }
        }
    },

    prev_onClick: function(event) {
        var current = this.toc_by_id[this.current_id].link.split("#").shift();
        var c=0;
        for (c in this.toc_by_id) {
            if (this.toc_by_id[c].link.split("#").shift() != current && this.toc_by_id[c].link.split("#").shift() == this.toc_by_id[c].link) {
                prev = this.toc_by_id[c].id;
            }
            if (this.toc_by_id[c].link.split("#").shift() == current) {
                this.go_to(prev,true,event);
                break;
            }
        }
    }
});