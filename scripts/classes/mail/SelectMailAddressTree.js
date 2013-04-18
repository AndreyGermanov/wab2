var SelectMailAddressTree = Class.create(Tree, {

    onExpandClick: function(event)
    {
        var elem = eventTarget(event);        
        if (elem.getAttribute("disable")=="true")
            return 0;
        var elem_arr = elem.id.split('_');
        elem_arr.pop();
        var root_elem = elem_arr.join('_');
        var elem_id = root_elem.replace(this.node.id+'_tree_','');        
        var tree = this;
        if (elem_id == "Mailboxes_"+this.module_id)
        {
            if ($I(root_elem).getAttribute("loaded")=="false")
            {
                elem.setAttribute("disable","true");
                $I(root_elem.concat("_content")).innerHTML = '';
                new Ajax.Request("index.php",
                    {
                        method: "post",
                        parameters: {ajax: true, object_id: 'SelectMailAddressTree_'+tree.module_id+'_mail', hook: '4'},
                        onSuccess: function(transport) {
                            var response = transport.responseText.toString().replace(" ","").replace("\n","");
                            tree.fillTree(response);
                            $(root_elem).setAttribute("loaded","true");
                            elem.setAttribute("disable","false");
                       }
                    });
            }
        }
        if (elem_id == "RemoteMailboxes_"+this.module_id)
        {
            if ($I(root_elem).getAttribute("loaded")=="false")
            {
                $I(root_elem.concat("_content")).innerHTML = '';
                new Ajax.Request("index.php",
                    {
                        method: "post",
                        parameters: {ajax: true, object_id: 'SelectMailAddressTree_'+tree.module_id+'_mail', hook: '5'},
                        onSuccess: function(transport) {
                            var response = transport.responseText.toString().replace(" ","").replace("\n","");
                            tree.fillTree(response);
                            $(root_elem).setAttribute("loaded","true");
                        }
                    });
            }
        }
        if (elem_id == "AddressBook_"+this.module_id)
        {
            if ($I(root_elem).getAttribute("loaded")=="false")
            {
                $I(root_elem.concat("_content")).innerHTML = '';
                var mbox = this;
                new Ajax.Request("index.php",
                    {
                        method: "post",
                        parameters: {ajax: true, object_id: 'CollectorMXTree_'+mbox.module_id+'_mail', hook: '5'},
                        onSuccess: function(transport) {
                            var response = transport.responseText.toString().replace(" ","").replace("\n","");
                            tree.fillTree(response);
                            $(root_elem).setAttribute("loaded","true");
                        }
                    });
            }
        }
        this.toggleTreeNode(elem_id);
    },

    onObjectClick: function(event) {
        var elem = eventTarget(event);
        var elem_id = elem.getAttribute("target_object");
        var obj_id = this.object_id.split("_");
        obj_id.shift();
        obj_id.pop();
        obj_id = obj_id.join("_");
        elem_id = elem_id.replace(obj_id+"_","");
        elem_id = elem_id.split("_");
        var elem_start = elem_id.shift();
        var elem_end = elem_id.pop();
        if (elem_id.join("_")!="")
            elem_end = elem_id.join("_")+"@"+elem_end;        
        elem_id = elem_end;
//        elem_end = elem_id.split("_").pop();
        if (elem_end == "HIDED")
            return 0;
        if (elem_start == "Mailbox")
        {
            var result = elem_end;
            window.opener.$I(this.target_item).value = result;
            var ev = new Array;
            ev.target = window.opener.$I(this.target_item);
            window.opener.$O(window.opener.$I(this.target_item).getAttribute("object"),'').onChange(ev);
            window.close();
        }
        if (elem_start == "Address")
        {
            var result = elem_end;
            window.opener.$I(this.target_item).value = result;
            var ev = new Array;
            ev.target = window.opener.$I(this.target_item);
            window.opener.$O(window.opener.$I(this.target_item).getAttribute("object"),'').onChange(ev);
            window.close();
        }
        if (elem_start == "RemoteMailbox")
        {
            var result = elem_end;
            window.opener.$I(this.target_item).value = result;
            var ev = new Array;
            ev.target = window.opener.$I(this.target_item);
            window.opener.$O(window.opener.$I(this.target_item).getAttribute("object"),'').onChange(ev);
            window.close();
        }
    },

    onMouseOver: function(event) {
        var elem = eventTarget(event);
        var elem_id = elem.getAttribute("target_object");
        var elems = this.root_node.getElementsByClassName('tree_item_selected');
        for (var counter=0;counter<elems.length;counter++)
            elems[counter].setAttribute('class','tree_item');
        var elem_start = elem_id.split('_').shift();
        var elem_end = elem_id.split("_").pop();
        if (elem_end == "HIDED")
            return 0;
        if (elem_start == "Mailbox")
        {
            elems=elem.parentNode.getElementsByTagName("*");
            for (var el=0;el<elems.length;el++) {
                if (elems[el].parentNode != elem.parentNode)
                    continue;
                if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
                    elems[el].setAttribute("class","tree_item_hover");
            }
        }
        if (elem_start == "Address")
        {
            elems=elem.parentNode.getElementsByTagName("*");
            for (var el=0;el<elems.length;el++) {
                if (elems[el].parentNode !== elem.parentNode)
                    continue;
                if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
                    elems[el].setAttribute("class","tree_item_hover");
            }
        }
        if (elem_start == "RemoteMailbox")
        {
            elems=elem.parentNode.getElementsByTagName("*");
            for (var el=0;el<elems.length;el++) {
                if (elems[el].parentNode !== elem.parentNode)
                    continue;
                if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
                    elems[el].setAttribute("class","tree_item_hover");
            }
        }
    },

    onMouseOut: function(event) {
        var elem = eventTarget(event);
        var elem_id = elem.getAttribute("target_object");
        var elems = this.root_node.getElementsByClassName('tree_item_selected');
        for (var counter=0;counter<elems.length;counter++)
            elems[counter].setAttribute('class','tree_item');
        var elem_start = elem_id.split('_').shift();
        var elem_end = elem_id.split("_").pop();
        if (elem_end == "HIDED")
            return 0;
        if (elem_start == "Mailbox")
        {
            elems=elem.parentNode.getElementsByTagName("*");
            for (var el=0;el<elems.length;el++) {
                if (elems[el].parentNode !== elem.parentNode)
                    continue;
                if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
                    elems[el].setAttribute("class","tree_item");
            }
        }
        if (elem_start == "Address")
        {
            elems=elem.parentNode.getElementsByTagName("*");
            for (var el=0;el<elems.length;el++) {
                if (elems[el].parentNode !== elem.parentNode)
                    continue;
                if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
                    elems[el].setAttribute("class","tree_item");
            }
        }
        if (elem_start == "RemoteMailbox")
        {
            elems=elem.parentNode.getElementsByTagName("*");
            for (var el=0;el<elems.length;el++) {
                if (elems[el].parentNode !== elem.parentNode)
                    continue;
                if (elems[el].id.split("_").pop()!="expand" && elems[el].id.split("_").pop()!="content")
                    elems[el].setAttribute("class","tree_item");
            }
        }
    },

    onContextMenu: function(event) {       
        return false;
    }
});