var ListBoxContextMenu = Class.create(ContextMenu,{
    onClick: function(event) {
        blur_error = "";
        this.opener_item.focus();
        var elem = eventTarget(event);
        var text = get_elem_id(elem).replace("_text","").replace("_td","");
        var obj = $O(this.opener_item.id,'');
        var old_val = obj.getValue();
        $I(this.opener_item.id+"_value").value = text;
        var val = obj.getValue();
        obj.raiseEvent("CONTROL_VALUE_CHANGED",$Arr("object_id="+this.opener_item.id+",value="+val+",old_value="+old_val));
        event = new Array;
        event["target"] = this.opener_item;
        $O(this.opener_item.getAttribute("object"),'').onChange(event);
        globalTopWindow.removeContextMenu();
        event = event || window.event;
        event.cancelBubble = true;
    }
});