var AddressBookContextMenu = Class.create(ContextMenu, {
    onClick: function(event) {
        var elem = eventTarget(event);
        switch(get_elem_id(elem).replace("_text",""))
        {
            case "add":
                var evt = document.createEvent("KeyboardEvent");
                evt.initKeyEvent("keyup", true, true, null, false, false, false, false, 13, 0);
                elem = this.opener_item;
                if (elem.tagName=="INPUT")
                    elem = elem.parentNode;
                elem = elem.parentNode;
                while (elem.nextSibling!=null) {
                    elem = elem.nextSibling;
                }
                var elems = elem.previousSibling.getElementsByTagName("td");
                elems[1].dispatchEvent(evt);
                break;
            case "remove":
                var evt = document.createEvent("KeyboardEvent");
                evt.initKeyEvent("keyup", true, true, null, false, true, false, false, 46, 0);
                this.opener_item.dispatchEvent(evt);
                break;
            case "insert":                
                var evt = document.createEvent("KeyboardEvent");
                evt.initKeyEvent("keyup", true, true, null, false, true, false, false, 45, 0);
                this.opener_item.dispatchEvent(evt);
                break;
            case "move_up":
                var evt = document.createEvent("KeyboardEvent");
                evt.initKeyEvent("keyup", true, true, null, false, true, false, false, 38, 0);
                this.opener_item.dispatchEvent(evt);
                break;
            case "move_down":
                var evt = document.createEvent("KeyboardEvent");
                evt.initKeyEvent("keyup", true, true, null, false, true, false, false, 40, 0);
                this.opener_item.dispatchEvent(evt);
                break;
        }
        globalTopWindow.removeContextMenu();
        event = event || window.event;
        event.cancelBubble = true;
    }
});