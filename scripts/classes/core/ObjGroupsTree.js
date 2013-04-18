var ObjGroupsTree = Class.create(EntityTree,{
    rootExpandClick: function(event) {
        var elem = eventTarget(event);
        if (elem.getAttribute("disable")=="true")
            return 0;
        var elem_arr = elem.id.split('_');
        elem_arr.pop();
        var root_elem = elem_arr.join('_');
        var tree = this;
        var args = new Object;
        args["objGroup"] = tree.objGroup;   
        args["result_object_id"] = tree.result_object_id;
        args["selectGroup"] = tree.selectGroup;
        args["forEntitySelect"] = tree.forEntitySelect;
        args["condition"] = tree.condition;
        args["editorType"] = tree.editorType;
        args["tableId"] = tree.tableId;
        if ($I(root_elem).getAttribute("loaded")=="false") {
            elem.setAttribute("disable","true");
            elem_arr = elem.id.split('_');
            elem_arr.pop();
            root_elem = elem_arr.join('_');
            $I(root_elem.concat("_image")).setAttribute("old_src",$I(root_elem.concat("_image")).src);
            $I(root_elem.concat("_image")).src = this.skinPath+'/images/Tree/loading.gif';
            $I(root_elem.concat("_content")).innerHTML = '';
            new Ajax.Request("index.php",
                {
                    method: "post",
                    parameters: {ajax: true, object_id: tree.object_id,hook:'3',arguments: Object.toJSON(args)},
                    onSuccess: function(transport) {
                        var response = transport.responseText;
                        if (response!="")
                            tree.fillTree(response);
                        $(root_elem).setAttribute("loaded","true");
                        elem.setAttribute("disable","false");
                        $I(root_elem.concat("_image")).src = $I(root_elem.concat("_image")).getAttribute("old_src");
                    }
                });
        }            
        this.toggleTreeNode(root_elem);
    }    	
});