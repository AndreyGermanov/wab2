var AddressBookTable = Class.create(Table, {

    getClassName: function() {
        return "AddressBookTable";
    },

    onContextMenu: function(event) {
        event = event || window.event;
        event.cancelBubble = true;
        elem = eventTarget(event);
        var objectid = elem.getAttribute("object");
        var instanceid = elem.getAttribute("instance");        
        $O(objectid,instanceid).show_context_menu("AddressBookContextMenu_Addr",cursorPos(event).x-10,cursorPos(event).y-10,elem.id);
         if (event.preventDefault)
            event.preventDefault();
         else
            event.returnValue= false;
        return false;
    }

});