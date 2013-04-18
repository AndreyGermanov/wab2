var PrintWindow = Class.create(Entity, {
    button_onMouseDown: function(event) {
        var src = eventTarget(event).src;
        var arr = src.split("/");
        src = arr.pop();
        src = src.split(".");
        var ext = src[1];
        src = src[0];
        src = src.split("_");
        if (src.length>1) {
            src = src.shift();
        }
        else
            src = src.join("_");
        src = arr.join("/")+"/"+src+"_clicked."+ext;
        eventTarget(event).src = src;
    },

    button_onMouseUp: function(event) {
        var src = eventTarget(event).src;
        var arr = src.split("/");
        src = arr.pop();
        src = src.split(".");
        var ext = src[1];
        src = src[0];
        src = src.split("_");
        if (src.length>1) {
            src = src.shift();
        } else
            src = src.join("_");
        src = arr.join("/")+"/"+src+"."+ext;
        eventTarget(event).src = src;
    },

    button_onMouseOver: function(event) {
        var src = eventTarget(event).src;
        var arr = src.split("/");
        src = arr.pop();
        src = src.split(".");
        var ext = src[1];
        src = src[0];
        src = src.split("_");
        if (src.length>1) {
            src = src.shift();
        }
        else
            src = src.join("_");
        src = arr.join("/")+"/"+src+"_hover."+ext;
        eventTarget(event).src = src;
    },

    button_onMouseOut: function(event) {
        var src = eventTarget(event).src;
        var arr = src.split("/");
        src = arr.pop();
        src = src.split(".");
        var ext = src[1];
        src = src[0];
        src = src.split("_");
        if (src.length>1) {
            src = src.shift();
        } else
            src = src.join("_");
        src = arr.join("/")+"/"+src+"."+ext;
        eventTarget(event).src = src;
    },
    
    printButton_onClick: function(event) {
		$I(this.node.id+"_innerFrame").contentWindow.print();
	},
	
	refreshButton_onClick: function(event) {
		$I(this.node.id+"_innerFrame").src = $I(this.node.id+"_innerFrame").src;
	}
});