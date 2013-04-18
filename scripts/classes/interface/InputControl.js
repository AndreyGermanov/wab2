var InputControl = Class.create(Entity, {
        
    openCKHandler: function(event) {
    	var element = eventTarget(event);
    	var object_id = element.getAttribute("object");
    	var instance_id = element.getAttribute("instance");
    	if (instance_id==null)
    		instance_id="";
    	if ($O(object_id,instance_id)!=null) {
            openKCFinder_singleFile($O(object_id,instance_id).node.id+"_value",'images');    
    		$O(object_id,instance_id).on_change(event);
    	}    	
    },
	
    makeArrayControl: function(rebuild) {
        if (this.node.getAttribute("show_delete_button")!=null) {
            this.show_delete_button = this.node.getAttribute("show_delete_button");
        }
        this.itemPrototype = this.node.getAttribute("itemPrototype");
        var tbl = $I(this.node.id+"_tbl");
        if (tbl==0)
            tbl = document.createElement("table");
        tbl.setAttribute("object",this.object_id);
        tbl.setAttribute("id",this.node.id+"_tbl");
        tbl.setAttribute("cellspacing",0);
        tbl.setAttribute("cellpadding",0);
        tbl.setAttribute("border",0);
        if (this.node.getAttribute("width")!=null)
            tbl.setAttribute("width",this.node.getAttribute("width"));
        var tr = $I(this.node.id+"_row");
        if (tr==0)
            tr = document.createElement("tr");
        tr.setAttribute("object",this.object_id);
        tr.setAttribute("id",this.node.id+"_row");
        tr.setAttribute("valign","top");
        var input_td = $I(this.node.id+"_inputcol");
        if (input_td==0)
            input_td = document.createElement("td");
        input_td.setAttribute("object",this.object_id);
        input_td.setAttribute("id",this.node.id+"_inputcol");     
        if (this.node.getAttribute("width")!=null)
            input_td.setAttribute("width",this.node.getAttribute("width"));
        var value_field = $I(this.node.id+"_value");
        if (value_field == 0)
            value_field = document.createElement("input");
        value_field.setAttribute("object",this.object_id);
        value_field.setAttribute("id",this.node.id+"_value");
        value_field.setAttribute("name",this.shortName);
        value_field.setAttribute('type',"hidden");
        value_field.setAttribute("control","yes");
        value_field.value = this.value;
        var title_field = $I(this.node.id+"_valueTitle");
        if (title_field==0)
            title_field = document.createElement("input");
        var input = title_field;
        title_field.setAttribute("object",this.object_id);
        title_field.setAttribute('readonly',"true");
        title_field.style.backgroundColor = '#FFFFFF';
        title_field.setAttribute("id",this.node.id+"_valueTitle");
        title_field.type = "text";
        if (this.invisible=="true")
        	title_field.style.display = 'none';
        if (this.value!="")
			this.valueTitle = "список...";
		title_field.setAttribute("value",this.valueTitle);
        if (this.node.getAttribute("width")!=null)
            title_field.style.width = this.node.getAttribute("width");
        title_field.setAttribute("keyup","keyPress");
        title_field.setAttribute("blur","on_blur");
        title_field.setAttribute("focus","on_focus");
        title_field.observe("keyup",this.addHandler);
        title_field.observe("blur",this.addHandler);
        title_field.observe("focus",this.addHandler);
        title_field.setAttribute("name",this.node.id+"_valueTitle");
        if (this.readonly=="true")
            title_field.setAttribute("readonly",this.readonly);
        if (this.deactivated=="true") {
            title_field.setAttribute("class",this.deactivate_class);
        }
        if (value_field.parentNode==null)
            input_td.appendChild(value_field);
        if (title_field.parentNode==null)
            input_td.appendChild(title_field);
        if (input_td.parentNode==null)
            tr.appendChild(input_td);
        
        if (this.readonly!="true") {
	        var select_button_td = $I(this.node.id+"_selectbuttoncol");
	        if (select_button_td==0)
	            select_button_td = document.createElement("td");
	        select_button_td.setAttribute("object",this.object_id);
	        select_button_td.setAttribute("id",this.node.id+"_selectbuttoncol");
	        select_button_td.setAttribute("bgcolor","#00AA00");
	        
	        select_button = $I(this.node.id+"_selectBtn");
	        if (select_button==0)
	            select_button = document.createElement("img");
	        select_button.setAttribute("object",this.object_id);
	        select_button.setAttribute("id",this.node.id+"_selectBtn");
	        select_button.setAttribute("type","image");
	        if (this.node.getAttribute("buttonImage")!=null)
	            select_button.src = this.node.getAttribute("buttonImage");
	        else
	            select_button.src = this.skinPath+"images/Buttons/selectButton.png";
	        if (this.node.getAttribute("actionButton")!="true") {	        	
	            select_button.setAttribute("mouseover","img_mouseOver");
	            select_button.setAttribute("mouseout","img_mouseOut");
	            select_button.observe("mouseover",this.addHandler);
	            select_button.observe("mouseout",this.addHandler);
	        }
	        select_button.setAttribute("dblclick","dblClick");
	        select_button.setAttribute("mousedown","img_mouseDown");
	        select_button.setAttribute("mouseup","img_mouseUp");
	        select_button.setAttribute("click","selectInArray");
	        select_button.setAttribute("focus","on_focus");
	        select_button.observe("dblclick",this.addHandler);
	        select_button.observe("mousedown",this.addHandler);
	        select_button.observe("mouseup",this.addHandler);
	        select_button.observe("click",this.addHandler);
	        select_button.observe("focus",this.addHandler);
	        //select_button.setAttribute("onsubmit","$O('"+this.node.id+"','').on_submit(event)");
	        if (this.readonly=="true")
	            select_button.style.display = 'none';
	        if (select_button.parentNode==null)
	            select_button_td.appendChild(select_button);
	        if (select_button_td.parentNode==null)
	            tr.appendChild(select_button_td);
        }
        
        if (this.show_delete_button=="true" && this.readonly!="true") {
            var delete_button_td = $I(this.node.id+"_deleteBtn");
            if (delete_button_td==0)
                delete_button_td = document.createElement("td");
            delete_button_td.setAttribute("object",this.object_id);
            delete_button_td.setAttribute("id",this.node.id+"_deletecol");
            delete_button_td.setAttribute("bgcolor","#00AA00");

            var delete_button = $I(this.node.id+"_deleteBtn");
            if (delete_button==0)
                delete_button = document.createElement("input");
            delete_button.setAttribute("object",this.object_id);
            delete_button.setAttribute("id",this.node.id+"_deleteBtn");
            delete_button.setAttribute("type","image");
            delete_button.src = this.skinPath+"images/Buttons/deleteButton.png";
            if (this.node.getAttribute("actionButton")!="true") {
                delete_button.setAttribute("mouseover","img_mouseOver");
                delete_button.setAttribute("mouseout","img_mouseOut");
                delete_button.observe("mouseover",this.addHandler);
                delete_button.observe("mouseout",this.addHandler);
            }
            delete_button.setAttribute("dblclick","dblClick");
            delete_button.setAttribute("mousedown","img_mouseDown");
            delete_button.setAttribute("mouseup","img_mouseUp");
            delete_button.setAttribute("click","deleteEntity");
            delete_button.setAttribute("focus","on_focus");
            delete_button.setAttribute("submit","on_submit");
            delete_button.observe("dblclick",this.addHandler);
            delete_button.observe("mousedown",this.addHandler);
            delete_button.observe("mouseup",this.addHandler);
            delete_button.observe("click",this.addHandler);
            delete_button.observe("focus",this.addHandler);
            delete_button.observe("submit",this.addHandler);
            delete_button.id = this.node.id+"_deleteBtn";
            if (this.readonly=="true")
                delete_button.style.display = 'none';
            if (delete_button.parentNode==null)
                delete_button_td.appendChild(delete_button);
            if (delete_button_td.parentNode==null)
                tr.appendChild(delete_button_td);
        }
        if (this.calcAlgo!=null && this.readonly!="true") {
            var td = document.createElement("td");
            var img = document.createElement("img");                
            img.setAttribute("object",this.object_id);
            img.setAttribute("title","Вычислить");
            img.setAttribute("mouseover","img_mouseOver");
            img.setAttribute("mouseout","img_mouseOut");
            img.setAttribute("mousedown","img_mouseDown");
            img.setAttribute("mouseup","img_mouseUp");
            img.observe("mouseover",this.addHandler);
            img.observe("mouseout",this.addHandler);
            img.observe("mousedown",this.addHandler);
            img.observe("mouseup",this.addHandler);
            
        	img.src = this.skinPath+"images/Buttons/calcButton.png";
        	img.setAttribute("onclick","$O('"+this.node.id+"','').calc()");
        	img.id = this.node.id+"_calcBtn";
            img.style.height=input.style.height;
            if (this.readonly=="true")
                img.style.display = 'none';
            td.appendChild(img);
            tr.appendChild(td);                	
        };
        if (this.node.getAttribute("selectClass")!=null && this.readonly!="true" && this.node.getAttribute("hideSelectButton")!="true") {
            var td = document.createElement("td");
            var img = document.createElement("img");                
            img.setAttribute("object",this.object_id);
            img.setAttribute("title","Выбрать");
            img.setAttribute("mouseover","img_mouseOver");
            img.setAttribute("mouseout","img_mouseOut");
            img.setAttribute("mousedown","img_mouseDown");
            img.setAttribute("mouseup","img_mouseUp");
            img.observe("mouseover",this.addHandler);
            img.observe("mouseout",this.addHandler);
            img.observe("mousedown",this.addHandler);
            img.observe("mouseup",this.addHandler);
        	img.src = this.skinPath+"images/Buttons/selectButton.png";
        	img.setAttribute("click","getSelectedValue");
        	img.observe("click",this.addHandler);
        	img.id = this.node.id+"_selectBtn";                	
            img.style.height=input.style.height;
            if (this.readonly=="true")
                img.style.display = 'none';
            td.appendChild(img);
            tr.appendChild(td);                	                	
        };                
        
        if (tr.parentNode==null)
            tbl.appendChild(tr);
        if (tbl.parentNode==null)
            this.node.appendChild(tbl);
	},
	
	selectInArray: function() {
		if (this.readonly=="true")
			return 0;
		var params = new Object;
		params["hook"] = 'setParams';
		params["adapterId"] = this.adapterId;
		params["itemPrototype"] = this.itemPrototype;
		params["values"] = this.value;
		getWindowManager().show_window("Window_ArrayListTable"+this.module_id.replace(/_/g,"")+this.node.id.split("_").pop().replace(this.module_id.replace(/_/g,""),""),"ArrayListTable_"+this.module_id+"_"+this.node.id.split("_").pop().replace(this.module_id.replace(/_/g,""),""),params,this.object_id,this.node.id,null,true);
	},
    
    makeEntityControl: function() {
        if (this.node.getAttribute("show_delete_button")!=null) {
            this.show_delete_button = this.node.getAttribute("show_delete_button");
        }
        var parent_object = $O(this.parent_object_id);
        if (parent_object.entityLists==null)
        	parent_object.entityLists = new Array;
        var is_new_control = false;
        var tbl = $I(this.node.id+"_tbl");
        if (tbl==0) {
            tbl = document.createElement("table");
            is_new_control = true;
        }
        if (this.node.getAttribute("list")=="true") {
        	var select = document.createElement("select");
        	select.id = this.node.id+"_value";
        	select.setAttribute("object",this.object_id);
            select.setAttribute("blur","on_blur");
            select.setAttribute("focus","on_focus");
            select.setAttribute("change","onListChange");
            select.setAttribute("name",this.shortName);
            this.node.appendChild(select);
            select.observe("blur",this.addHandler);
            select.observe("focus",this.addHandler);
            select.observe("change",this.addHandler);
            if (this.node.getAttribute("width")!=null)
            	select.style.width = this.node.getAttribute("width");
            if (this.node.getAttribute("class")!=null)
            	select.setAttribute("class",this.node.getAttribute("class"));
            var selectValues = "";
            if (parent_object.entityLists[this.node.getAttribute("className")]!=null) {
            	selectValues = this.entityLists[this.className];
                var items_parts = selectValues.split("|");
                var values = items_parts[0].split("~");
                var titles = items_parts[1].split("~");
                for (var c=0;c<values.length;c++) {
                    if (values[c]==this.value)
                        selected = true;
                    else
                        selected = false;
                    select.options[select.length] = new Option(titles[c],values[c],selected,selected);
                }

            }
            else {
	            var obj = this;
	            new Ajax.Request("index.php",
	            {
	                method: "post",
	                parameters: {ajax: true, object_id: obj.node.getAttribute("className")+"_"+obj.module_id+"_",
	                             hook: "getListString"},
	                onSuccess: function(transport) {
	                    var response = transport.responseText;
	                    selectValues = response;
	                    parent_object.entityLists[obj.node.getAttribute("className")] = response;
	                    var items_parts = selectValues.split("|");
	                    var values = items_parts[0].split("~");
	                    var titles = items_parts[1].split("~");
	                    for (var c=0;c<values.length;c++) {
	                        if (values[c]==obj.value)
	                            selected = true;
	                        else
	                            selected = false;
	                        select.options[select.length] = new Option(titles[c],values[c],selected,selected);
	                    }	                    
	                }
	            });  
	            this.node.appendChild(select);
            }
        } else {
	        tbl.setAttribute("object",this.object_id);
	        tbl.setAttribute("id",this.node.id+"_tbl");
	        tbl.setAttribute("cellspacing",0);
	        tbl.setAttribute("cellpadding",0);
	        tbl.setAttribute("border",0);
	        if (this.node.getAttribute("width")!=null)
	            tbl.setAttribute("width",this.node.getAttribute("width"));
	        var tr = $I(this.node.id+"_row");
	        if (tr==0)
	            tr = document.createElement("tr");
	        tr.setAttribute("object",this.object_id);
	        tr.setAttribute("id",this.node.id+"_row");
	        tr.setAttribute("valign","top");
	        var input_td = $I(this.node.id+"_inputcol");
	        if (input_td==0)
	            input_td = document.createElement("td");
	        input_td.setAttribute("object",this.object_id);
	        input_td.setAttribute("id",this.node.id+"_inputcol");     
	        if (this.node.getAttribute("width")!=null)
	            input_td.setAttribute("width",this.node.getAttribute("width"));
	        var value_field = $I(this.node.id+"_value");
	        if (value_field == 0)
	            value_field = document.createElement("input");
	        value_field.setAttribute("object",this.object_id);
	        value_field.setAttribute("id",this.node.id+"_value");
	        value_field.setAttribute("name",this.shortName);
	        value_field.setAttribute('type',"hidden");
	        value_field.setAttribute("control","yes");
	        if (this.value!=null)
	        	value_field.value = this.value;
	        
	        var title_field = $I(this.node.id+"_valueTitle");
	        if (title_field==0)
	            title_field = document.createElement("input");
	        
	        var input = title_field;
	        
	        title_field.setAttribute("object",this.object_id);
	        title_field.setAttribute('readonly',"true");
	        title_field.style.backgroundColor = '#FFFFFF';
	        title_field.setAttribute("id",this.node.id+"_valueTitle");
	        title_field.type = "text";
	        if (this.node.getAttribute("valueTitle")!=null)
	        	this.valueTitle = this.node.getAttribute("valueTitle");
	        if (this.node.getAttribute("className")!=null)
	            this.className = this.node.getAttribute("className"); 
	        if (this.invisible!="true") {
		        if (this.value!="" && this.value!="-1" && (this.valueTitle=="" || this.valueTitle==null)) {
		        	var args = new Object;
		        	args["adapterId"] = this.adapterId;
		        	var objid="";
		            if (this.module_id!=null)
		                objid = this.value.split("_").shift()+"_"+this.module_id+"_"+this.value.split("_").pop();
		            else
		                objid = this.value;
		            var obj = this;
		            new Ajax.Request("index.php",
		            {
		                method: "post",
		                parameters: {ajax: true, object_id: objid,
		                             hook: "getPresentation", arguments: Object.toJSON(args)},
		                onSuccess: function(transport) {
		                    var response = transport.responseText;
		                    obj.valueTitle = response;
		                    title_field.setAttribute("value",obj.valueTitle);
		                }
		            });
		        } else
		            title_field.setAttribute("value",this.valueTitle);
	        } else
	        	title_field.style.display='none';
	        if (this.node.getAttribute("width")!=null)
	            title_field.style.width = this.node.getAttribute("width");
	        title_field.setAttribute("keyup","keyPress");
	        title_field.setAttribute("blur","on_blur");
	        title_field.setAttribute("focus","on_focus");
	        title_field.setAttribute("keyup","keyPress");
	        title_field.observe("blur",this.addHandler);
	        title_field.observe("focus",this.addHandler);
	        title_field.observe("name",this.node.id+"_valueTitle");
	        title_field.observe("keyup",this.addHandler);
	        if (this.readonly=="true")
	            title_field.setAttribute("readonly",this.readonly);
	        if (this.deactivated=="true") {
	            title_field.setAttribute("class",this.deactivate_class);
	        }
	        if (value_field.parentNode==null)
	            input_td.appendChild(value_field);
	        if (title_field.parentNode==null)
	            input_td.appendChild(title_field);
	        if (input_td.parentNode==null)
	            tr.appendChild(input_td);
	        
	        var select_button_td = $I(this.node.id+"_selectbuttoncol");
	        if (select_button_td==0)
	            select_button_td = document.createElement("td");
	        select_button_td.setAttribute("object",this.object_id);
	        select_button_td.setAttribute("id",this.node.id+"_selectbuttoncol");
	        select_button_td.setAttribute("bgcolor","#00AA00");
	        
	        var select_button = $I(this.node.id+"_selectBtn");
	        if (select_button==0)
	            select_button = document.createElement("img");
	        select_button.setAttribute("object",this.object_id);
	        select_button.setAttribute("id",this.node.id+"_selectBtn");
	        select_button.setAttribute("type","image");
	        if (this.node.getAttribute("buttonImage")!=null)
	            select_button.src = this.node.getAttribute("buttonImage");
	        else {
	        	
	        	if (this.node.getAttribute("show_float_div")!="true")        	
	        		select_button.src = this.skinPath+"images/Buttons/selectButton.png";
	        	else
	        		select_button.src = this.skinPath+"images/Buttons/moveDownButton.png";
	        }
	        if (this.node.getAttribute("actionButton")!="true") {
	            select_button.setAttribute("mouseover","img_mouseOver");
	            select_button.setAttribute("mouseout","img_mouseOut");
	            select_button.observe("mouseover",this.addHandler);
	            select_button.observe("mouseout",this.addHandler);
	        }
	        select_button.setAttribute("dblclick","dblClick");
	        select_button.setAttribute("mousedown","img_mouseDown");
	        select_button.setAttribute("mouseup","img_mouseUp");
	        select_button.setAttribute("click","selectEntity");
	        select_button.setAttribute("focus","on_focus");
	        select_button.observe("dblclick",this.addHandler);
	        select_button.observe("mousedown",this.addHandler);
	        select_button.observe("mouseup",this.addHandler);
	        select_button.observe("click",this.addHandler);
	        select_button.observe("focus",this.addHandler);
	        
	        if (this.readonly=="true")
	            select_button.style.display = 'none';
	        if (select_button.parentNode==null)
	            select_button_td.appendChild(select_button);
	        if (select_button_td.parentNode==null)
	            tr.appendChild(select_button_td);
	        
	        if (this.show_delete_button=="true" && this.readonly!="true") {
	            var delete_button_td = $I(this.node.id+"_deleteBtn");
	            if (delete_button_td==0) 
	                delete_button_td = document.createElement("td");
	            delete_button_td.setAttribute("object",this.object_id);
	            delete_button_td.setAttribute("id",this.node.id+"_deletecol");
	            delete_button_td.setAttribute("bgcolor","#00AA00");
	
	            var delete_button = $I(this.node.id+"_deleteBtn");
	            if (delete_button==0)
	                delete_button = document.createElement("input");
	            delete_button.setAttribute("object",this.object_id);
	            delete_button.setAttribute("id",this.node.id+"_deleteBtn");
	            delete_button.setAttribute("type","image");
	            delete_button.src = this.skinPath+"images/Buttons/deleteButton.png";
	            if (this.node.getAttribute("actionButton")!="true") {
	                delete_button.setAttribute("mouseover","img_mouseOver");
	                delete_button.setAttribute("mouseout","img_mouseOut");
	                delete_button.observe("mouseover",this.addHandler);
	                delete_button.observe("mouseout",this.addHandler);
	            }
	            delete_button.setAttribute("dblclick","dblClick");
	            delete_button.setAttribute("mousedown","img_mouseDown");
	            delete_button.setAttribute("mouseup","img_mouseUp");
	            delete_button.setAttribute("click","deleteEntity");
	            delete_button.setAttribute("focus","on_focus");
	            delete_button.setAttribute("submit","on_submit");
	            delete_button.observe("dblclick",this.addHandler);
	            delete_button.observe("mousedown",this.addHandler);
	            delete_button.observe("mouseup",this.addHandler);
	            delete_button.observe("click",this.addHandler);
	            delete_button.observe("focus",this.addHandler);
	            delete_button.observe("submit",this.addHandler);
	            delete_button.id = this.node.id+"_deleteBtn";
	            if (this.readonly=="true")
	                delete_button.style.display = 'none';
	            if (delete_button.parentNode==null)
	                delete_button_td.appendChild(delete_button);
	            if (delete_button_td.parentNode==null)
	                tr.appendChild(delete_button_td);
	        }
	        if (this.calcAlgo!=null && this.readonly!="true" && is_new_control) {
	            var td = document.createElement("td");
	            var img = document.createElement("img");                
	            img.setAttribute("object",this.object_id);
	            img.setAttribute("title","Вычислить");
	            img.setAttribute("mouseover","img_mouseOver");
	            img.setAttribute("mouseout","img_mouseOut");
	            img.setAttribute("mousedown","img_mouseDown");
	            img.setAttribute("mouseup","img_mouseUp");
	            img.observe("mouseover",this.addHandler);
	            img.observe("mouseout",this.addHandler);
	            img.observe("mousedown",this.addHandler);
	            img.observe("mouseup",this.addHandler);
	        	img.src = this.skinPath+"images/Buttons/calcButton.png";
	        	img.setAttribute("onclick","$O('"+this.node.id+"','').calc()");
	        	img.id = this.node.id+"_calcBtn";
	            img.style.height=input.style.height;
	            if (this.readonly=="true")
	                img.style.display = 'none';
	            td.appendChild(img);
	            tr.appendChild(td);                	
	        };
	        if (this.node.getAttribute("selectClass")!=null && this.readonly!="true" && is_new_control  && this.node.getAttribute("hideSelectButton")!="true") {
	            var td = document.createElement("td");
	            var img = document.createElement("img");                
	            img.setAttribute("object",this.object_id);
	            img.setAttribute("title","Выбрать");
	            img.setAttribute("mouseover","img_mouseOver");
	            img.setAttribute("mouseout","img_mouseOut");
	            img.setAttribute("mousedown","img_mouseDown");
	            img.setAttribute("mouseup","img_mouseUp");
	            img.observe("mouseover",this.addHandler);
	            img.observe("mouseout",this.addHandler);
	            img.observe("mousedown",this.addHandler);
	            img.observe("mouseup",this.addHandler);
	            
	        	img.src = this.skinPath+"images/Buttons/selectButton.png";
	        	img.setAttribute("onclick","$O('"+this.node.id+"','').getSelectedValue(event)");
	        	img.id = this.node.id+"_selectBtn";                	
	            img.style.height=input.style.height;
	            if (this.readonly=="true")
	                img.style.display = 'none';
	            td.appendChild(img);
	            tr.appendChild(td);                	                	
	        };                        
	        if (tr.parentNode==null)
	            tbl.appendChild(tr);
	        if (tbl.parentNode==null)
	            this.node.appendChild(tbl);
        }
    },
    
    selectEntity: function(event) {
    	if (this.readonly=="true")
    		return 0;
        var pos = getElementPosition(this.node.id+"_valueTitle");        
        var pos1 = getElementPosition(this.node.id+"_selectBtn");
        this.className = '';
        this.classTitle = '';
        this.condition = '';
        this.childCondition = '';
        this.windowHeight = '';
        this.editorType = '';
        this.divName = '';
        this.windowWidth = '';
        this.windowTitle = '';
        this.destroyDiv = '';
        this.tableId = '';
        this.editorType = '';

        if (this.node.getAttribute("className")!=null)
            this.className = this.node.getAttribute("className");
        if (this.node.getAttribute("defaultClassName")!=null)
            this.defaultClassName = this.node.getAttribute("defaultClassName");
        if (this.node.getAttribute("treeClassName")!=null)
            this.treeClassName = this.node.getAttribute("treeClassName");
        else
            this.treeClassName = "";
        if (this.node.getAttribute("tableClassName")!=null)
            this.tableClassName = this.node.getAttribute("tableClassName");
        else
            this.tableClassName = "EntityDataTable";
        if (this.node.getAttribute("parentEntity")!=null)
            this.parentEntity = this.node.getAttribute("parentEntity");
        else
            this.parentEntity = "";
       
        if (this.node.getAttribute("sortOrder")!=null)
            this.sortOrder = this.node.getAttribute("sortOrder");
        else
            this.sortOrder = "";
        if (this.node.getAttribute("fieldList")!=null)
            this.fieldList = this.node.getAttribute("fieldList");
        else
            this.fieldList = "";
        if (this.node.getAttribute("adapterId")!=null)
            this.adapterId = this.node.getAttribute("adapterId");
        else
            this.adapterId = "";
        if (this.node.getAttribute("classTitle")!=null)
            this.classTitle = this.node.getAttribute("classTitle");

        if (this.node.getAttribute("hierarchy")!=null)
            this.hierarchy = this.node.getAttribute("hierarchy");
        else
            this.hierarchy="true";

        if (this.node.getAttribute("condition")!=null)        
            this.condition = this.node.getAttribute("condition");
        else
            this.condition = "@parent IS NOT EXISTS";
        if (this.node.getAttribute("childcondition")!=null)        
            this.childCondition = this.node.getAttribute("childcondition");
        
        if (this.node.getAttribute("windowHeight")!=null)        
            this.windowHeight = this.node.getAttribute("windowHeight");
        if (this.node.getAttribute("editorType")!=null)        
            this.editorType = this.node.getAttribute("editorType");
        else
            this.editorType = 'none';
        if (this.node.getAttribute("divName")!=null)        
            this.divName = this.node.getAttribute("divName");
        if (this.node.getAttribute("windowWidth")!=null)        
            this.windowWidth = this.node.getAttribute("windowWidth");
        if (this.node.getAttribute("windowHeight")!=null)        
            this.windowHeight = this.node.getAttribute("windowWidth");
        if (this.node.getAttribute("windowTitle")!=null)        
            this.windowTitle = this.node.getAttribute("windowTitle");
        if (this.node.getAttribute("destroyDiv")!=null)        
            this.destroyDiv = this.node.getAttribute("destroyDiv");
        if (this.node.getAttribute("tableId")!=null)        
            this.tableId = this.node.getAttribute("tableId");
        if (this.node.getAttribute("selectGroup")!=null)        
            this.selectGroup = this.node.getAttribute("selectGroup");
        else
        	this.selectGroup = 1;
        if (this.node.getAttribute("value")!=null)        
            this.value = this.node.getAttribute("value");
        if (this.node.getAttribute("valueTitle")!=null)        
            this.valueTitle = this.node.getAttribute("valueTitle");
        else
            this.valueTitle = '';
        if (this.node.getAttribute("additionalFields")!=null)
            this.additionalFields = this.node.getAttribute("additionalFields");
        else
            this.parentEntity = "";
		if (this.tableClassName==null)
			this.tableClassName = 'EntityDataTable';
        if (this.node.getAttribute("show_float_div")=="true") {
            var x = pos.left-5;
            var y = pos.top + pos.height-5;
            var w = pos.width+pos1.width;
            if (x+w>window.innerWidth) {
                w = w - (window.innerWidth-x-10);
            }
            var args = new Object;
            args["width"] = w;
            args["height"] = this.windowHeight;
            args["className"] = this.className;
            args["adapterId"] = this.adapterId;
            args["hierarchy"] = this.hierarchy;
            args["defaultClassName"] = this.defaultClassName;
            args["tableClassName"] = this.tableClassName;
            args["treeClassName"] = this.treeClassName;
            args["condition"] = this.condition.replace(/'/g,"\'").replace(/\=/g,'zozo');            
            args["childCondition"] = this.childCondition.replace(/'/g,"\'");     
            args["editorType"] = this.editorType;
            args["divName"] = this.divName;
            args["sortOrder"] = this.sortOrder;            
            if (this.fieldList!="")
            	args["fieldList"] = this.fieldList.replace(/,/g,'~');
            if (this.parentEntity!="")
            	args["parentEntity"] = this.parentEntity;
            args["windowWidth"] = this.windowWidth;
            if (this.additionalFields!=null)
            	args["additionalFields"] = this.additionalFields;
            args["windowHeight"] = this.windowHeight;
            args["windowTitle"] = this.windowTitle;
            args["destroyDiv"] = this.destroyDiv;
            args["entityId"] = this.value;
            args["tableId"] = this.tableId;
            args["selectGroup"] = this.selectGroup;
            args["classTitle"] = this.node.getAttribute('classTitle');
            args["parent_object_id"] = this.object_id;
            args["fieldAccess"] = this.fieldAccess;
            args["fieldDefaults"] = this.fieldDefaults;
            args["hook"] = "setParams";
            removeContextMenu();
            if ($O(this.parent_object_id,'').module_id!="")
                this.show_context_menu("SelectEntityFloatDiv_"+$O(this.parent_object_id,'').module_id+"_"+this.object_id.replace($O(this.parent_object_id,'').module_id+"_","").replace(/_/g,''),x,y,this.node.id,args);
            else
                this.show_context_menu("SelectEntityFloatDiv_"+this.object_id.replace(/_/g,''),x,y,this.node.id,args);
            event = event || window.event;
            event.cancelBubble = true;
            if (event.preventDefault)
               event.preventDefault();
            else
               event.returnValue= false;
            return false;
        } else {
            if (this.node.getAttribute("editorType")=="WABWindow") {
            	var elem_id = "";
                if ($O(this.parent_object_id,'').module_id=="")
                    elem_id = "EntitySelectWindow_"+this.node.id.replace(/_/g,'');
                else
                    elem_id = "EntitySelectWindow_"+$O(this.parent_object_id,'').module_id+"_"+this.node.id.replace($O(this.parent_object_id,'').module_id+"_","").replace(/_/g,'');
                var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
                var params = new Object;
                params["hook"] = "setParams";
                params["className"] = this.className;
                params["defaultClassName"] = this.className;
                params["tableClassName"] = this.tableClassName;
                params["hierarchy"] = this.hierarchy;
                params["treeClassName"] = this.treeClassName;
                if (this.additionalFields!=null)
                	params["additionalFields"] = this.additionalFields;
                params["condition"] = this.condition.replace(/\#/g,"xoxo").replace(/\@/g,"yoyo").replace(/\=/g,"zozo");
                params["childCondition"] = this.childCondition.replace(/\#/g,"xoxo");
                params["sortOrder"] = this.sortOrder;
                params["adapterId"] = this.adapterId;
                params["tableId"] = this.tableId;
                if (this.fieldList!="")
                	params["fieldList"] = this.fieldList.replace(/,/g,'~');
                if (this.parentEntity!="")
                	params['entityParentId'] = this.parentEntity;
                params['present'] = this.title.replace(/#/g,'xyxyx'+":Выбор");
                params["classTitle"] = this.classTitle;
                params["editorType"] = this.editorType;
                params["selectGroup"] = this.selectGroup;
                params["entityId"] = this.value;
                params["parent_object_id"] = this.object_id;
                params["fieldAccess"] = Object.toJSON(this.fieldAccess);
                params["fieldDefaults"] = Object.toJSON(this.fieldDefaults);
                var obj = this;
                if (this.value!="" && this.value!="-1") {
                    var objid = this.value;                    
                	var arr = this.value.split("_");
                	if (arr.length==2)
                		objid = arr[0]+"_"+this.module_id+"_"+arr[1];
                    new Ajax.Request("index.php",
                    {
                        method: "post",
                        parameters: {ajax: true, object_id: objid,
                                     hook: 'getParent'},
                        onSuccess: function(transport) {
                            var response = transport.responseText;
                            var parent_name = response.split("_").pop();
                            if (parent_name!="-1" && parent_name!="") {
                                if (obj.condition!="" && obj.condition!="@parent IS NOT EXISTS" && parent_name!="")
                                	params['condition'] += " AND @parent.@name="+parent_name;
                                else
                                	params['condition'] = "@parent.@name="+parent_name;
                            } else {
                            	if (obj.condition!="@parent IS NOT EXISTS") {
	                            	if (obj.condition!="")
	                            		params["condition"] += " AND @parent IS NOT EXISTS";
	                            	else
	                            		params["condition"] = "@parent IS NOT EXISTS";
                            	}
                            }
                            if (response!="")
                            	params["entityParentId"] = response;
                            getWindowManager().show_window(window_elem_id,elem_id,params,obj.module_id,$I(obj.node.id+"_value").id,null,true);                                            
                        }
                    });
                } else {                   
                    getWindowManager().show_window(window_elem_id,elem_id,params,$O(this.module_id,''),$I(this.node.id+"_value").id,null,true);                
                }
            } else if (this.node.getAttribute("editorType")=="window") {
            	var elem_id =  "";
                if ($O(this.parent_object_id,'').module_id=="")
                    elem_id = "EntitySelectWindow_"+this.node.id.replace(/_/g,'');
                else
                    elem_id = "EntitySelectWindow_"+this.module_id+"_"+this.node.id.replace(/_/g,'');
                var params = new Object;
                params["className"] = this.className;
                params["defaultClassName"] = this.defaultClassName;
                params["treeClassName"] = this.treeClassName;
                params["condition"] = this.condition.replace(/\#/g,"xoxo");
                params["childCondition"] = this.childCondition.replace(/\#/g,"xoxo");
                params["present"] = this.title.replace(/#/g,'xyxyx');
                params["classTitle"] = this.classTitle;
                params["hierarchy"]= this.hierarchy;
                params["sortOrder"] = this.sortOrder;
                params["adapterId"] = this.adapterId;
                if (this.fieldList!="")
                	params["fieldList"] = this.fieldList;
                if (this.additionalFields!=null)
                	params["additionalFields"] = this.additionalFields;
                if (this.parentEntity!="")
                	params["entityParentId"] = this.parentEntity;
                params["editorType"] = this.editorType;
                params["selectGroup"] = this.selectGroup;
                params["parent_object_id"] = this.object_id;
                params["entityId"] = this.value;
                params["fieldAccess"] = this.fieldAccess;
                params["fieldDefaults"] = this.fieldDefaults;
                
               var args = "modal";
               var leftPosition = (screen.availWidth-this.windowWidth)/2;
               var topPosition = (screen.availHeight-this.windowHeight)/2;
               var options = "dialogWidth:"+this.windowWidth+"px; dialogHeight:"+this.windowHeight+"px; dialogLeft:"+leftPosition+"px; dialogTop:"+topPosition+"px;";
                var obj = this;
                if (this.value!="" && this.value!="-1") {
                    var objid = this.value;
                    new Ajax.Request("index.php",
                    {
                        method: "post",
                        parameters: {ajax: true, object_id: objid,
                                     hook: "getParent"},
                        onSuccess: function(transport) {
                            var response = transport.responseText;
                            var parent_name = null;
                            if (response!="")
                            	parent_name = response.split("_").pop();
                            if (parent_name!=null && parent_name!="-1" && parent_name!="") {
                                if (obj.condition!="")
                                	params["condition"] += " AND @parent=xyxyxy"+parent_name+"xyxyxy";
                                else
                                	params["condition"] = "simple|"+this.fieldList+"|@parent=xyxyxy"+parent_name+"xyxyxy";
                            }
                            obj.selectValueWindow = window.showModalDialog("?object_id="+elem_id+"&hook=show&arguments"+Object.toJSON(params),args,options);
                        }
                    });
                } else {                   
                    obj.selectValueWindow = window.showModalDialog("?object_id="+elem_id+"&hook=show&arguments"+Object.toJSON(params),args,options);
                }                
            }
            if (event!=null) {
                event = event || window.event;
                event.cancelBubble = true;
                if (event.preventDefault)
                   event.preventDefault();
                else
                   event.returnValue= false;
            }
            return false;
        }
    },
    
    deleteEntity: function(event) {
    	if (this.readonly=="true")
    		return 0;
        this.raiseEvent("CONTROL_VALUE_CHANGED",$Arr("object_id="+this.object_id+",value=,valueTitle="));        
        if (event!=null) {
            event = event || window.event;
            event.cancelBubble = true;
            if (event.preventDefault)
               event.preventDefault();
            else
               event.returnValue= false;
        }
    },
    
    makeBooleanControl: function() {        
        var value_field = $I(this.node.id+"_value");
        if (value_field==0) {
            value_field = document.createElement("input");
            is_new_control = true;
        }
        value_field.setAttribute("object",this.object_id);
        value_field.setAttribute("id",this.node.id+"_value");
        value_field.setAttribute("name",this.shortName);
        value_field.setAttribute("type","hidden");
        value_field.setAttribute("control","yes");

        if (this.value==false || this.value=="false")
            this.value = 0;
        value_field.setAttribute("value",this.value);
        if (value_field.parentNode==null)
            this.node.appendChild(value_field);
        if (this.node.getAttribute("control_type")==null)
        	this.node.setAttribute("control_type","checkbox");
        if (this.node.getAttribute("control_type")=="checkbox") {
            var checkbox_field = $I(this.node.id+"_checkbox");
            if (checkbox_field==0)
                checkbox_field = document.createElement("input");
            checkbox_field.setAttribute("object",this.object_id);
            checkbox_field.setAttribute("id",this.node.id+"_checkbox");
            checkbox_field.setAttribute("type","checkbox");
            if (this.invisible=="true")
            	checkbox_field.style.display = 'none';
            if (checkbox_field.checked==true) {
                this.value = 1;
                value_field.setAttribute("value",this.value);
            }
            if (this.value=="true" || this.value=="1" || this.value==1)
                checkbox_field.setAttribute("checked","true");
            else
                if (checkbox_field.getAttribute("checked")!=null)
                    checkbox_field.removeAttribute("checked");
            if (this.readonly!="true") {
            	checkbox_field.setAttribute("click","checkboxClick");
            	checkbox_field.observe("click",this.addHandler);
            }
            else
            	checkbox_field.setAttribute("readonly","true");
            if (checkbox_field.parentNode==null)
                this.node.appendChild(checkbox_field);
            if (this.node.getAttribute("show_description")=="true") {
                var descr = this.node.getAttribute("description");
                if (descr!=null) {
                    var span = $I(this.node.id+"_description");
                    if (span==0)
                        span = document.createElement("span");
                    if (this.invisible=="true")
                    	span.style.display = 'none';                    
                    span.innerHTML = descr;
                    if (span.parentNode==null)
                        this.node.appendChild(span);
                }
            }
        } else if (this.node.getAttribute("control_type")=="listbox") {
            var listbox_field = $I(this.node.id+"_listbox");
            if (listbox_field==0)
                listbox_field = document.createElement("SELECT");
            listbox_field.setAttribute("object",this.object_id);
            listbox_field.setAttribute("id",this.node.id+"_listbox");
            if (this.node.getAttribute("width")!=null)
                listbox_field.style.width = this.node.getAttribute("width");
            listbox_field.setAttribute("value",this.value);
            while (listbox_field.options.length>0)
                input.options[0] = null;
            if (this.value==1) {
                listbox_field.options[listbox_field.length] = new Option("Да",1,true,true);
                listbox_field.options[listbox_field.length] = new Option("Нет",0,false,false);                
            } else {
                listbox_field.options[listbox_field.length] = new Option("Да",1,false,false);
                listbox_field.options[listbox_field.length] = new Option("Нет",0,true,true);                
            }
            if (this.readonly!="true") {
            	listbox_field.setAttribute("change","booleanListboxChange");
            	listbox_field.observe("change",this.addHandler);
            } else
            	listbox_field.setAttribute("readonly","true");
            if (this.invisible=="true")
            	listbox_field.style.display = 'none';
            if (listbox_field.parentNode==null)
                this.node.appendChild(listbox_field);
        }
    },
    
    checkboxClick: function(event) {
    	if (this.readonly=="true")
    		return 0;
        if (eventTarget(event).checked) {
            this.raiseEvent("CONTROL_VALUE_CHANGED",$Arr("object_id="+this.object_id+",value=1"));    
        }
        else {
            this.raiseEvent("CONTROL_VALUE_CHANGED",$Arr("object_id="+this.object_id+",value=0"));
        }
    },    

    booleanListboxChange: function(event) {
    	if (this.readonly=="true")
    		return 0;
        this.raiseEvent("CONTROL_VALUE_CHANGED",$Arr("object_id="+this.object_id+",value="+eventTarget(event).value.replace(/,/g,'xoxoxo')));            
    },
    
    makeDateControl: function() {
        var tbl = $I(this.node.id+"_tbl");
        if (tbl==0) {
            tbl = document.createElement("table");
        }
        tbl.setAttribute("object",this.object_id);
        tbl.setAttribute("id",this.node.id+"_tbl");
        tbl.setAttribute("cellspacing",0);
        tbl.setAttribute("cellpadding",0);
        tbl.setAttribute("border",0);
        var tr = $I(this.node.id+"_row");
        if (tr==0)
            tr = document.createElement("tr");
        tr.setAttribute("object",this.object_id);
        tr.setAttribute("id",this.node.id+"_row");
        tr.setAttribute("valign","top");
        var date_td = $I(this.node.id+"_inputcol");
        if (date_td==0)
            date_td = document.createElement("td");
        date_td.setAttribute("object",this.object_id);
        date_td.setAttribute("id",this.node.id+"_datecol");     
    	
        var value_field = $I(this.node.id+"_value");
        if (value_field==0) {        	
            value_field = document.createElement("input");
        }
        value_field.setAttribute("object",this.object_id);
        value_field.setAttribute("id",this.node.id+"_value");
        value_field.setAttribute("name",this.shortName);
        value_field.setAttribute("type","hidden");
        value_field.setAttribute("value",this.value);
        value_field.setAttribute("control","yes");
        if (value_field.parentNode==null)
            date_td.appendChild(value_field);
        
        var date_field = $I(this.node.id+"_date");
        if (date_field==0)
            date_field = document.createElement("input");
        date_field.setAttribute("object",this.object_id);
        date_field.setAttribute("id",this.node.id+"_date");
        if (this.value!="") {
            var d = new Date(parseFloat(this.value));            
            var day = d.getDate();
            if (day<10) day = "0"+day;
            var month = d.getMonth()+1;
            if (month<10) month = "0"+month;
            var year = d.getFullYear();
            var hour = d.getHours();
            if (hour<10)
            	hour = "0"+hour;        
            var minute = d.getMinutes();
            if (minute<10)
            	minute = "0"+minute;
            var second = d.getSeconds();
            if (second<10)
            	second = "0"+second;
            date_field.setAttribute("value",day+"."+month+"."+year+" "+hour+":"+minute+":"+second);
        }
        if (this.readonly!="true") { 
        	date_field.setAttribute("focus","lcs");
        	date_field.setAttribute("click","lcs");
        	date_field.setAttribute("keyup","keyPress");
        	date_field.setAttribute("change","changeDateField");
        	date_field.observe("focus",this.addHandler);
        	date_field.observe("click",this.addHandler);
        	date_field.observe("change",this.addHandler);
        	date_field.observe("keyup",this.addHandler);
        } else
        	date_field.setAttribute("readonly","true");
        if (this.invisible=="true")
        	date_field.style.display='none';
        if (date_field.parentNode==null)
            date_td.appendChild(date_field);
        tr.appendChild(date_td);        
       	date_td.setAttribute("width","100%");
        tbl.appendChild(tr);
        this.node.appendChild(tbl);
    },

    changeDateField: function(event) {
    	if (this.readonly=="true")
    		return 0;
    	if ($I(this.node.id+"_date").value=="") {
			this.raiseEvent("CONTROL_VALUE_CHANGED",$Arr("object_id="+this.object_id+",value="));
			return 0;
    	}
    	var arr = $I(this.node.id+"_date").value.split(" ");
        var val = arr[0].split('.');
        var hour = 0;
        var minute = 0;
        var second = 0;
        val = val.reverse();
        val = val.join('-');
        var dt = Date.parse(val);
        dt = new Date(dt);
        var timeval = arr[1].split(":");
        if (timeval[0]!=null && !isNaN(parseInt(timeval[0])))
        	hour = parseInt(timeval[0]);
        if (timeval[1]!=null && !isNaN(parseInt(timeval[1])))
        	minute = parseInt(timeval[1]);
        if (timeval[2]!=null && !isNaN(parseInt(timeval[2])))
        	second = parseInt(timeval[2]);        
        dt.setHours(hour,minute,second);
        this.raiseEvent("CONTROL_VALUE_CHANGED",$Arr("object_id="+this.object_id+",value="+dt.getTime()));
    },
    
    makeTextControl: function() {
        var textarea = $I(this.node.id+"_value");
        if (textarea==0)
            textarea = document.createElement("textarea");
        textarea.setAttribute("object",this.object_id);
        textarea.setAttribute("id",this.node.id+"_value");
        textarea.setAttribute("name",this.shortName);
        textarea.setAttribute("blur","on_blur");
        textarea.setAttribute("focus","on_focus");
        textarea.setAttribute("keyup","keyPress");
        textarea.observe("blur",this.addHandler);
        textarea.observe("focus",this.addHandler);
        textarea.observe("keyup",this.addHandler);
        textarea.setAttribute("control","yes");
        if (this.readonly=="true") {
            textarea.setAttribute("readonly","true");            
        }
        if (this.deactivated=="true") {
        	if (navigator.appName=="Microsoft Internet Explorer")
        		textarea.className = this.deactivate_class;
        	else
        		textarea.setAttribute("class",this.deactivate_class);
        }
        if (this.value==null || this.value=="")
        	if (this.node.value!=null && typeof(this.node.value)!="undefined")
        		this.value = this.node.value;
        if (this.node.innerHTML.replace(/\ /g,"").replace(/\\n/g,"")=="") {
        	if (this.value!="")
        		this.node.innerHTML = this.value.replace(/xoxoxo/g,"'").replace(/yoyoyo/g,'"').replace(/\</g,'&lt;').replace(/\>/g,'&gt;');
        }
        if (textarea.parentNode==null) {        	
            textarea.innerHTML = this.node.innerHTML;
        }    
        if (this.node.innerHTML!="")
        	this.node.innerHTML = "";
        if (this.node.getAttribute("width")!=null)
            textarea.style.width = this.node.getAttribute("width");
        if (this.node.getAttribute("height")!=null) {
            textarea.style.height = this.node.getAttribute("height");
            this.node.style.height = this.node.getAttribute("height");
            textarea.setAttribute("height",this.node.getAttribute("height"));
        }
        if (textarea.parentNode==null) {
            this.node.appendChild(textarea);
        }
        if (this.invisible)
        	textarea.style.display = 'none';
        if (this.node.getAttribute("autoresize")=="true") {
	        if (this.node.getAttribute("control_type")!="editArea" && this.node.getAttribute("control_type")!="tinyMCE") {
	        	var div = document.createElement("div");
	        	div.setAttribute("class","hiddendiv");
	        	div.id = this.node.id+"_hiddendiv";
	        	this.node.appendChild(div);
	        	div.innerHTML = this.value.replace(/\n/g,"<br/>");
	        	textarea.setAttribute("keyup","textAreaKeyUp");
	        	textarea.observe("keyup",this.addHandler);
	        	textarea.style.resize = "none";
	        	textarea.style.overflow = "hidden";
	        	this.textAreaKeyUp();
	        }
        }
        if (this.node.getAttribute("control_type")=="editArea") {
            if (this.node.getAttribute("syntax")!=null)
                this.syntax = this.node.getAttribute("syntax");
            else
                this.syntax = "js";
            var syntax = this.syntax;
            editAreaLoader.init({
                    id : textarea.id
                    ,syntax: syntax
                    ,start_highlight: true
                    ,language: "ru"
                    ,allow_resize: "no"
                    ,toolbar: "undo,redo,search,go_to_line,|,word_wrap,highlight,reset_highlight,|,syntax_selection,select_font,|,fullscreen"
                    ,cursor_position: "auto"
                    //,display: "later"
                    ,change_callback : "onTinyMCETextChanged"
            });
        }
        if (this.node.getAttribute("control_type")=="tinyMCE") {
            if (this.node.getAttribute("css")!=null && this.node.getAttribute("css")!="")
                this.css = "styles/blank.css,"+this.node.getAttribute("css");
            else
                this.css = "styles/blank.css";
            if (this.node.getAttribute("width")!=null)
                this.width = this.node.getAttribute("width");
            else
                this.width = "100%";
            if (this.node.getAttribute("height")!=null)
                this.height = this.node.getAttribute("height");
            else
                this.height = "100%";
            tinyMCE.init({
                language: "ru",
                theme : "advanced",
                mode : "exact",
                width: this.width,
                height: this.height,
                apply_source_formatting: true,
                elements: textarea.id,
                file_browser_callback : "openKCFinder",
                entity_encoding : "raw",
                plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
                paintweb_config : {
                    configFile: "config-example.json",
                    // TinyMCE plugin options.
                    tinymce: {
                      // Tell where PaintWeb is located.
                      paintwebFolder: "../paintweb/build/",
                      imageDataURLfilter: "utils/paintweb.php",
                      imageSaveDataURL: true,
                      overlayButton: true,
                      contextMenuItem: true,
                      dblclickHandler: true,
                      pluginBar: true,
                      syncViewportSize: true
                    }
                },
                theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,forecolor,backcolor,|,styleselect,formatselect,fontselect,fontsizeselect",
                theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,anchor,image,images,paintwebEdit,cleanup,code,|,insertdate,inserttime,preview",
                theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
                theme_advanced_toolbar_location : "top",
                theme_advanced_toolbar_align : "left",
                theme_advanced_statusbar_location : "bottom",
                theme_advanced_resizing : false,
                content_css: this.css+"?" + new Date().getTime(),
                verify_html: false,
                onchange_callback : "onTinyMCETextChanged"
            });            
        }
    },

    build: function(rebuild) {
    	var isJSON = false;
    	var properties_array = "";
        this.properties = this.node.getAttribute("properties");
        if (this.properties!=null) {
        	if (this.properties[0]=='{') {
        		properties_array = this.properties.evalJSON(true);
        		isJSON = true;
        	}
        	else
        		properties_array = this.properties.split('~');
            for (pc in properties_array) {
            	if (typeof properties_array[pc] == "function")
            		continue;
            	if (!isJSON) {
            		var properties_parts = properties_array[pc].split("=");
            		if (properties_parts.length==2)
            			if (this.node.getAttribute(properties_parts[0])==null)
            				this.node.setAttribute(properties_parts[0],properties_parts[1]);
            	} else {
            		if (this.node.getAttribute(pc)==null) {
            			if (typeof properties_array[pc] == "object")
            				this.node.setAttribute(pc,Object.toJSON(properties_array[pc]));
            			else {
            				this.node.setAttribute(pc,properties_array[pc]);
            			}
            		}
            	}
            }
        }
        this.calcAlgo = this.node.getAttribute("calcAlgo");        
        this.calcProperties = this.node.getAttribute("calcProperties");
        var calcProps = new Object;
        if (this.calcProperties!=null) {
        	properties_array = "";
        	if (this.calcProperties[0]=='{')
        		properties_array = this.calcProperties.evalJSON(true);
        	if (typeof(properties_array)!="object" && typeof(properties_array)!="array")
        		properties_array = this.calcProperties.split('~');
        	else
        		isJSON = true;
            for (pc in properties_array) {
            	if (typeof properties_array[pc] == "function")
            		continue;
            	if (!isJSON) {
            		properties_parts = properties_array[pc].split("=");
            		if (properties_parts.length==2) {
	    				calcProps[properties_parts[0]] = properties_parts[1];
            		}
            	} else {
            		calcProps[pc] = properties_array[pc];
            	}
            }
            this.calcProperties = calcProps;
        } else 
        	this.calcProperties = new Object;
        this.autoCalc = this.node.getAttribute("autoCalc");
        this.fieldAccess = this.node.getAttribute("fieldAccess");
        this.fieldDefaults = this.node.getAttribute("fieldDefaults");
        if (this.fieldAccess!=null && this.fieldAccess[0]=="{") {
        	this.fieldAccess = this.fieldAccess.evalJSON();
        }
        if (this.fieldDefaults!=null && this.fieldDefaults[0]=="{")
        	this.fieldDefaults = this.fieldDefaults.evalJSON();        
        this.type = this.node.getAttribute("type");
        this.control_type = this.node.getAttribute("control_type");
        if (this.type==null)
            return 0;
        this.full_type = this.type.replace(",ruleset","");
        this.width = this.node.getAttribute("width");
        this.additionalFields = this.node.getAttribute("additionalFields");
        this.height = this.node.getAttribute("height");
        this.is_header = this.node.getAttribute("is_header");
        if (this.parent_object_id==null)
            this.parent_object_id = this.node.getAttribute("object");
        this.must_set = this.node.getAttribute("must_set");
        this.unique = this.node.getAttribute("unique");
        this.invisible = this.node.getAttribute("invisible");
        this.unchangeable = this.node.getAttribute("unchangeable");
        if (this.readonly==null) {
	        if (this.invisible=="true")
	        	this.readonly = "true";
	        else {
	        	if (this.unchangeable=="true")
	        		this.readonly = "false";
	        	else
	        		this.readonly = this.node.getAttribute("readonly");
	        }
        }
        this.input_class = this.node.getAttribute("input_class");
        this.adapterId = this.node.getAttribute("adapterId");
        if (this.adapterId==null)
            this.adapterId = '';
        this.deactivate_class = this.node.getAttribute("deactivate_class");
        if (this.input_class==null)
            this.input_class = "wide";
        if (this.deactivate_class==null)
            this.deactivate_class = "deactivated";
        if (this.deactivated==null)
            this.deactivated = this.node.getAttribute("deactivated");

        var regs = this.node.getAttribute("regs");
        if (regs!=null) {
            this.regs = new Array;
            regs = regs.split("~");
            for (var c=0;c<regs.length;c++) {
                this.regs[this.regs.length] = new RegExp(regs[c]);
            }
        }
        if (this.must_set == "true")
            this.must_set = true;
        else
            this.must_set = false;
        this.value = '';
        if (this.node.getAttribute('value')!=null)
            this.value = this.node.getAttribute('value');
        this.title = '';
        if (this.node.getAttribute('title')!=null)
            this.title = this.node.getAttribute('title');        
        this.valueTitle = '';
        if (this.node.getAttribute('valueTitle')!=null)
            this.title = this.node.getAttribute('valueTitle');        
        if (this.regs==null)
            this.regs = new Array;
        if (this.module_id!="")
            this.shortName = this.node.id.replace(this.module_id+"_","");
        else
            this.shortName = this.node.id;
        var type_arr = this.type.split(",");
        if (type_arr[type_arr.length-1]=="ruleset")
            this.is_ruleset = true;
        if (type_arr[0]=="array") {
            this.makeArrayControl(rebuild);
        }
        if (type_arr[0]=="boolean") {
            this.makeBooleanControl(rebuild);
        }
        if (type_arr[0]=="date") {
            this.makeDateControl(rebuild);
        }
        if (type_arr[0]=="text") {
            this.makeTextControl(rebuild);
        }
        if (type_arr[0] == "plaintext") {        	
        		this.node.innerHTML = this.value;
        }
        var input = "";
        var is_new_input = false;
        if (type_arr[0] == "static") {
            input = $I(this.node.id+"_value");
            if (input==0 || rebuild==true) {
                input = document.createElement("A");
                input.id = this.node.id+"_value";
                input.setAttribute("object",this.object_id);
                input.setAttribute("name",this.shortName);
                if (this.control_type=="email")
                	input.setAttribute("href","mailto:"+this.value);
                else
                	input.setAttribute("href","#");

                if (this.node.getAttribute("class")==null)
                	input.setAttribute("class",this.deactivated_class);
                else
                	input.setAttribute("class",this.node.getAttribute("class"));
                input.style.width = '100%';
                input.setAttribute("keyup","keyPress");
                input.setAttribute("blur","on_blur");
                input.setAttribute("focus","on_focus");
                input.setAttribute("click","plainTextClick");
                input.setAttribute("dblclick","dblClick");
                input.observe("keyup",this.addHandler);
                input.observe("blur",this.addHandler);
                input.observe("focus",this.addHandler);
                input.observe("click",this.addHandler);
                input.observe("dblclick",this.addHandler);
                
                input.setAttribute("control","yes");
                is_new_input = true;
            } else {
                is_new_input = false;                
            }
            input.innerHTML = "";
            if (this.control_type=="file") {
            	input.innerHTML = this.value.split("/").pop();
            	input.setAttribute("title",this.value);
            }
            else
            	input.innerHTML = this.value;
            input.style.textDecoration = "none";
            if (this.readonly=="true")
                input.setAttribute("readonly",this.readonly);
            if (is_new_input)
                this.node.appendChild(input);
            if (this.invisible=="true")
            	input.style.display = 'none';
        }
        var is_new_table = false;
        var tabl = null;
        if (type_arr[0] == "header") {
            this.node.setAttribute("onclick","$O('"+this.object_id+"','').cellClick(event)");
            this.node.setAttribute("object",this.object_id);
            var elems = this.node.getElementsByTagName("table");
            if (elems[0]==null || rebuild==true) {
                tabl = document.createElement("table");
                tabl.setAttribute("object",this.object_id);
                tabl.setAttribute("cellpadding","0");
                tabl.setAttribute("cellspacing","0");
                if (this.width!=null) {
                    tabl.setAttribute("width","100%");
                }
                is_new_table = true;
            }
            else {
                is_new_table = false;
                tabl = elems[0];
            }
            var elems = tabl.getElementsByTagName("tr");
            var is_new_row = false;
            if (elems[0]==null || rebuild==true) {
                var tr = document.createElement("tr");
                tr.setAttribute("object",this.object_id);
                is_new_row = true;
            } else {
                tr = elems[0];
                is_new_row = false;
            }
            var elems = tr.getElementsByTagName("td");
            var is_new_column1 = false;
            if (elems[0]==null || rebuild==true) {
                var td = document.createElement("td");
                td.setAttribute("object",this.object_id);
                td.setAttribute("width","100%");
                td.setAttribute("valign","top");
                td.setAttribute("nowrap","nowrap");
                td.setAttribute("align","center");
                td.setAttribute("class","header_text");
                is_new_column1 = true;
            }
            else {
                td = elems[0];
                is_new_column1 = false;
            }
            td.innerHTML = this.value;
            if (is_new_column1==true)
                tr.appendChild(td);
            var td = null;
            if (elems[1]==null || rebuild==true) {
                td = document.createElement("td");
                td.setAttribute("class","header_text");
                is_new_column2=true;
            } else {
                td = elems[1];
                is_new_column2 = false;
            }
            var elems = td.getElementsByTagName("img");
            var is_new_image = "";
            if (elems[0]==null || rebuild==true) {
                var img = document.createElement("img");
                img.setAttribute("object",this.object_id);
                img.id = this.node.id+"_image";
                is_new_image = true;
            } else {
                img = elems[0];
                is_new_image = false;
            }
            img.src = this.skinPath+"images/spacer.gif";
            if (is_new_image)
                td.appendChild(img);
            if (is_new_column2)
                tr.appendChild(td);
            if (this.invisible=="true")
            	tr.style.display = "none";
            if (is_new_row)
                tabl.appendChild(tr);
            if (is_new_table)
                this.node.appendChild(tabl);
        }
        if (type_arr[0] == "integer") {
            this.type = type_arr[0];
            if (this.regs.length==0) {
                this.regs[0] = /^[1-9\-][0-9]*$/;
            }
            this.regs[this.regs.length] = /^$/;
            if (this.is_ruleset)
                this.regs[this.regs.length] = /^\/.*/;
            input = $I(this.node.id+"_value");
            if (input == 0 || rebuild==true) {
                input = document.createElement("INPUT");
                input.id = this.node.id+"_value";
                input.setAttribute("object",this.object_id);
                input.setAttribute("name",this.shortName);
                input.setAttribute("class",this.input_class);
                if (this.node.getAttribute("onkeypress")==null) {
                    input.setAttribute("keyup","keyPress");
                    input.observe("keyup",this.addHandler);                    
                }
                else {
                    input.setAttribute("keyup",this.node.getAttribute("onkeypress"));
                    input.observe("keyup",this.addHandler);
                }
                input.setAttribute("blur","on_blur");
                input.setAttribute("focus","on_focus");
                input.observe("blur",this.addHandler);
                input.observe("focus",this.addHandler);
                if (this.readonly=="true")
                    input.setAttribute("readonly",this.readonly);
                if (this.deactivated=="true") {
                    input.setAttribute("class",this.deactivate_class);
                }
                is_new_input = true;
            } else
                is_new_input = false;
            input.setAttribute("value",this.value);
            input.setAttribute("control","yes");
            if (this.invisible=="true")
            	input.style.display = "none";
            if (this.readonly!="true" && (this.is_ruleset || this.node.getAttribute("selectClass")!=null || this.calcAlgo!=null) && is_new_input) {
                input.style.height = 23;
                tabl = document.createElement("table");
                tabl.setAttribute("cellpadding","0");
                tabl.setAttribute("cellspacing","0");
                var tr = document.createElement("tr");
                tr.setAttribute("valign","top");
                var td = document.createElement("td");
                td.appendChild(input);
                tr.appendChild(td);
                if (this.width!=null) {
                    tabl.setAttribute("width",this.width);
                    td.setAttribute("width",this.width);
                    input.style.width = this.width;
                }
                if (this.calcAlgo!=null) {
                    var td = document.createElement("td");
                    var img = document.createElement("img");                
                    img.setAttribute("object",this.object_id);
                    img.setAttribute("title","Вычислить");
                    img.setAttribute("mouseover","img_mouseOver");
                    img.setAttribute("mouseout","img_mouseOut");
                    img.setAttribute("mousedown","img_mouseDown");
                    img.setAttribute("mouseup","img_mouseUp");
                    img.observe("mouseover",this.addHandler);
                    img.observe("mouseout",this.addHandler);
                    img.observe("mousedown",this.addHandler);
                    img.observe("mouseup",this.addHandler);                    
                	img.src = this.skinPath+"images/Buttons/calcButton.png";
                	img.setAttribute("onclick","$O('"+this.node.id+"','').calc()");
                	img.id = this.node.id+"_calcBtn";
                    img.style.height=input.style.height;
                    if (this.readonly=="true")
                        img.style.display = 'none';
                    td.appendChild(img);
                    tr.appendChild(td);                	
                };
                if (this.node.getAttribute("selectClass")!=null && this.node.getAttribute("hideSelectButton")!="true") {
                    var td = document.createElement("td");
                    var img = document.createElement("img");                
                    img.setAttribute("object",this.object_id);
                    img.setAttribute("title","Выбрать");
                    img.setAttribute("mouseover","img_mouseOver");
                    img.setAttribute("mouseout","img_mouseOut");
                    img.setAttribute("mousedown","img_mouseDown");
                    img.setAttribute("mouseup","img_mouseUp");
                    img.observe("mouseover",this.addHandler);
                    img.observe("mouseout",this.addHandler);
                    img.observe("mousedown",this.addHandler);
                    img.observe("mouseup",this.addHandler);
                    
                	img.src = this.skinPath+"images/Buttons/selectButton.png";
                	img.setAttribute("onclick","$O('"+this.node.id+"','').getSelectedValue(event)");
                	img.id = this.node.id+"_selectBtn";                	
                    img.style.height=input.style.height;
                    if (this.readonly=="true")
                        img.style.display = 'none';
                    td.appendChild(img);
                    tr.appendChild(td);                	                	
                };
                if (this.is_ruleset) {
                    var td = document.createElement("td");
                    var img = document.createElement("img");                
                    img.setAttribute("object",this.object_id);
                    img.setAttribute("title","Выбрать");
                    img.setAttribute("mouseover","img_mouseOver");
                    img.setAttribute("mouseout","img_mouseOut");
                    img.setAttribute("mousedown","img_mouseDown");
                    img.setAttribute("mouseup","img_mouseUp");
                    img.observe("mouseover",this.addHandler);
                    img.observe("mouseout",this.addHandler);
                    img.observe("mousedown",this.addHandler);
                    img.observe("mouseup",this.addHandler);
                    
                	img.src = this.skinPath+"images/Buttons/edit_ruleset.png";
                	img.setAttribute("onclick","$O('"+this.node.id+"','').openRulesetWindow(event)");
                	img.id = this.node.id+"_editrulesetbtn";
                    img.style.height=input.style.height;
                    if (this.readonly=="true")
                        img.style.display = 'none';
                    td.appendChild(img);
                    tr.appendChild(td);                	                	
                }
                tabl.appendChild(tr);
                this.node.appendChild(tabl);
            } else {
                if (is_new_input)
                    this.node.appendChild(input);
            }
        }
        if (type_arr[0] == "decimal") {
            this.type = type_arr[0];
            if (this.regs.length==0) {
                this.regs[0] = /[0-9\.]*$/;
            }
            this.regs[this.regs.length] = /^$/;
            if (this.is_ruleset)
                this.regs[this.regs.length] = /^\/.*/;
            input = $I(this.node.id+"_value");
            if (input == 0 || rebuild==true) {
                input = document.createElement("INPUT");
                input.id = this.node.id+"_value";
                input.setAttribute("object",this.object_id);
                input.setAttribute("name",this.shortName);
                input.setAttribute("class",this.input_class);
                if (this.node.getAttribute("onkeypress")==null) {
                    input.setAttribute("keyup","keyPress");
                    input.observe("keyup",this.addHandler);                    
                }
                else {
                    input.setAttribute("keyup",this.node.getAttribute("onkeypress"));
                    input.observe("keyup",this.addHandler);
                }
                input.setAttribute("blur","on_blur");
                input.setAttribute("focus","on_focus");
                input.observe("blur",this.addHandler);
                input.observe("focus",this.addHandler);
                if (this.readonly=="true")
                    input.setAttribute("readonly",this.readonly);
                if (this.deactivated=="true") {
                    input.setAttribute("class",this.deactivate_class);
                }
                is_new_input = true;
            } else
                is_new_input = false;
            input.setAttribute("value",this.value);
            input.setAttribute("control","yes");
            if (this.invisible=="true")
            	input.style.display = "none";
            if (this.readonly!="true" && (this.is_ruleset || this.node.getAttribute("selectClass")!=null || this.calcAlgo!=null) && is_new_input) {
                input.style.height = 23;
                tabl = document.createElement("table");
                tabl.setAttribute("cellpadding","0");
                tabl.setAttribute("cellspacing","0");
                var tr = document.createElement("tr");
                tr.setAttribute("valign","top");
                var td = document.createElement("td");
                td.appendChild(input);
                tr.appendChild(td);
                if (this.width!=null) {
                    tabl.setAttribute("width",this.width);
                    td.setAttribute("width",this.width);
                    input.style.width = this.width;
                }
                if (this.calcAlgo!=null) {
                    var td = document.createElement("td");
                    var img = document.createElement("img");                
                    img.setAttribute("object",this.object_id);
                    img.setAttribute("title","Вычислить");
                    img.setAttribute("mouseover","img_mouseOver");
                    img.setAttribute("mouseout","img_mouseOut");
                    img.setAttribute("mousedown","img_mouseDown");
                    img.setAttribute("mouseup","img_mouseUp");
                    img.observe("mouseover",this.addHandler);
                    img.observe("mouseout",this.addHandler);
                    img.observe("mousedown",this.addHandler);
                    img.observe("mouseup",this.addHandler);
                	img.src = this.skinPath+"images/Buttons/calcButton.png";
                	img.setAttribute("onclick","$O('"+this.node.id+"','').calc()");
                	img.id = this.node.id+"_calcBtn";
                    img.style.height=input.style.height;
                    if (this.readonly=="true")
                        img.style.display = 'none';
                    td.appendChild(img);
                    tr.appendChild(td);                	
                };
                if (this.node.getAttribute("selectClass")!=null && this.node.getAttribute("hideSelectButton")!="true") {
                    var td = document.createElement("td");
                    var img = document.createElement("img");                
                    img.setAttribute("object",this.object_id);
                    img.setAttribute("title","Выбрать");
                    img.setAttribute("mouseover","img_mouseOver");
                    img.setAttribute("mouseout","img_mouseOut");
                    img.setAttribute("mousedown","img_mouseDown");
                    img.setAttribute("mouseup","img_mouseUp");
                    img.observe("mouseover",this.addHandler);
                    img.observe("mouseout",this.addHandler);
                    img.observe("mousedown",this.addHandler);
                    img.observe("mouseup",this.addHandler);
                	img.src = this.skinPath+"images/Buttons/selectButton.png";
                	img.setAttribute("click","getSelectedValue");
                	img.observe("click",this.addHandler);
                	img.id = this.node.id+"_selectBtn";                	
                    img.style.height=input.style.height;
                    if (this.readonly=="true")
                        img.style.display = 'none';
                    td.appendChild(img);
                    tr.appendChild(td);                	                	
                };
                if (this.is_ruleset) {
                    var td = document.createElement("td");
                    var img = document.createElement("img");                
                    img.setAttribute("object",this.object_id);
                    img.setAttribute("title","Выбрать");
                    img.setAttribute("mouseover","img_mouseOver");
                    img.setAttribute("mouseout","img_mouseOut");
                    img.setAttribute("mousedown","img_mouseDown");
                    img.setAttribute("mouseup","img_mouseUp");
                	img.setAttribute("click","openRulesetWindow");
                    img.observe("mouseover",this.addHandler);
                    img.observe("mouseout",this.addHandler);
                    img.observe("mousedown",this.addHandler);
                    img.observe("mouseup",this.addHandler);
                	img.observe("click",this.addHandler);
                	img.src = this.skinPath+"images/Buttons/edit_ruleset.png";
                	img.id = this.node.id+"_editrulesetbtn";
                    img.style.height=input.style.height;
                    if (this.readonly=="true")
                        img.style.display = 'none';
                    td.appendChild(img);
                    tr.appendChild(td);                	                	
                }
                tabl.appendChild(tr);
                this.node.appendChild(tabl);
            } else {
                if (is_new_input)
                    this.node.appendChild(input);
            }
        }
        if (type_arr[0] == "string") {
            this.type = type_arr[0];
            input = $I(this.node.id+"_value");
            if (input==0 || rebuild==true) {
                input = document.createElement("INPUT");
                input.setAttribute("object",this.object_id);
                input.id = this.node.id+"_value";
                if (this.node.getAttribute("password")=="true") {
                    input.setAttribute("type","password");                        
                } 
                input.setAttribute("name",this.shortName);
                input.setAttribute("class",this.input_class);
                input.setAttribute("control","yes");
                if (this.node.getAttribute("onkeypress")==null) {
                    input.setAttribute("keyup","keyPress");
                    input.observe("keyup",this.addHandler);                    
                }
                else {
                    input.setAttribute("keyup",this.node.getAttribute("onkeypress"));
                    input.observe("keyup",this.addHandler);
                }
                input.setAttribute("focus","on_focus");
                input.setAttribute("blur","on_blur");
                input.observe("focus",this.addHandler);
                input.observe("blur",this.addHandler);
                if (this.readonly=="true")
                    input.setAttribute("readonly",this.readonly);
                if (this.deactivated=="true") {
                    input.setAttribute("class",this.deactivate_class);
                }
                is_new_input = true;
            }
            else
                is_new_input = false;
            input.setAttribute("value",this.value);
            if (this.invisible=="true")
            	input.style.display = "none";
            if (this.readonly!="true" && (this.is_ruleset || this.node.getAttribute("selectClass")!=null || this.calcAlgo!=null) && is_new_input) {
                input.style.height = 23;
                tabl = document.createElement("table");
                tabl.setAttribute("cellpadding","0");
                tabl.setAttribute("cellspacing","0");
                tabl.setAttribute("width","100%");
                var tr = document.createElement("tr");
                tr.setAttribute("valign","top");
                var td = document.createElement("td");
                td.appendChild(input);
                tr.appendChild(td);
                if (this.width!=null) {
                    tabl.setAttribute("width",this.width);
                    td.setAttribute("width",this.width);
                    input.style.width = this.width;
                } else
                	input.style.width = "100%";
                if (this.calcAlgo!=null) {
                    var td = document.createElement("td");
                    var img = document.createElement("img");                
                    img.setAttribute("object",this.object_id);
                    img.setAttribute("title","Вычислить");
                    img.setAttribute("mouseover","img_mouseOver");
                    img.setAttribute("mouseout","img_mouseOut");
                    img.setAttribute("mousedown","img_mouseDown");
                    img.setAttribute("mouseup","img_mouseUp");
                	img.setAttribute("click","calc");
                    img.observe("mouseover",this.addHandler);
                    img.observe("mouseout",this.addHandler);
                    img.observe("mousedown",this.addHandler);
                    img.observe("mouseup",this.addHandler);
                	img.observe("click",this.addHandler);
                	
                	img.src = this.skinPath+"images/Buttons/calcButton.png";
                	img.id = this.node.id+"_calcBtn";
                    img.style.height=input.style.height;
                    if (this.readonly=="true")
                        img.style.display = 'none';
                    td.appendChild(img);
                    tr.appendChild(td);                	
                };
                if (this.node.getAttribute("selectClass")!=null && this.node.getAttribute("hideSelectButton")!="true") {
                    var td = document.createElement("td");
                    var img = document.createElement("img");                
                    img.setAttribute("object",this.object_id);
                    img.setAttribute("title","Выбрать");
                    img.setAttribute("mouseover","img_mouseOver");
                    img.setAttribute("mouseout","img_mouseOut");
                    img.setAttribute("mousedown","img_mouseDown");
                    img.setAttribute("mouseup","img_mouseUp");
                	img.setAttribute("click","getSelectedValue");
                    img.observe("mouseover",this.addHandler);
                    img.observe("mouseout",this.addHandler);
                    img.observe("mousedown",this.addHandler);
                    img.observe("mouseup",this.addHandler);
                	img.observe("click",this.addHandler);
                	
                	img.src = this.skinPath+"images/Buttons/selectButton.png";
                	img.id = this.node.id+"_selectBtn";                	
                    img.style.height=input.style.height;
                    if (this.readonly=="true")
                        img.style.display = 'none';
                    td.appendChild(img);
                    tr.appendChild(td);                	                	
                };
                if (this.is_ruleset) {
                    var td = document.createElement("td");
                    var img = document.createElement("img");                
                    img.setAttribute("object",this.object_id);
                    img.setAttribute("title","Выбрать");
                    img.setAttribute("mouseover","img_mouseOver");
                    img.setAttribute("mouseout","img_mouseOut");
                    img.setAttribute("mousedown","img_mouseDown");
                    img.setAttribute("mouseup","img_mouseUp");
                	img.setAttribute("click","openRulesetWindow");
                    img.observe("mouseover",this.addHandler);
                    img.observe("mouseout",this.addHandler);
                    img.observe("mousedown",this.addHandler);
                    img.observe("mouseup",this.addHandler);
                	img.observe("click",this.addHandler);
                	
                	img.src = this.skinPath+"images/Buttons/edit_ruleset.png";
                	img.id = this.node.id+"_editrulesetbtn";
                    img.style.height=input.style.height;
                    if (this.readonly=="true")
                        img.style.display = 'none';
                    td.appendChild(img);
                    tr.appendChild(td);                	                	
                }
                tabl.appendChild(tr);
                this.node.appendChild(tabl);
            } else {
                if (is_new_input)
                    this.node.appendChild(input);
            }
        }
        if (type_arr[0] == "file") {
            this.type = type_arr[0];
            if (this.regs.length==0) {                
                if (this.node.getAttribute("absolutePath")=="true")
                    this.regs[0] = /^\/.*$/;   
                else
                    this.regs[0] = /.*/;
            }
            input = $I(this.node.id+"_value");
            if (input==0 || rebuild==true) {
                input = document.createElement("INPUT");
                input.setAttribute("object",this.object_id);
                input.id = this.node.id+"_value";
                input.setAttribute("name",this.shortName);
                input.setAttribute("class",this.input_class);
                if (this.readonly=="true")
                    input.setAttribute("readonly",this.readonly);
                input.setAttribute("value",this.value);
                input.setAttribute("control","yes");
                if (this.node.getAttribute("onkeypress")==null) {
                    input.setAttribute("keyup","keyPress");
                    input.observe("keyup",this.addHandler);                    
                }
                else {
                    input.setAttribute("keyup",this.node.getAttribute("onkeypress"));
                    input.observe("keyup",this.addHandler);                    
                }
                input.setAttribute("blur","on_blur");
                input.setAttribute("focus","on_focus");
                input.observe("blur",this.addHandler);
                input.observe("focus",this.addHandler);
                if (this.deactivated=="true") {
                    input.setAttribute("class",this.deactivate_class);
                }
                input.style.height = 23;
                if (this.invisible=="true")
                	input.style.display = "none";
                tabl = document.createElement("table");
                tabl.setAttribute("cellpadding","0");
                tabl.setAttribute("cellspacing","0");
                var tr = document.createElement("tr");
                tr.setAttribute("valign","top");
                var td = document.createElement("td");
                if (this.width!=null) {
                    td.setAttribute('width',this.width);
                    input.style.width = this.width;
                }
                if (this.width!=null) {
                    tabl.setAttribute("width",this.width);
                    td.setAttribute('width',this.width);
                    input.style.width = this.width;
                }
                if (this.node.getAttribute("control_type")!=null)
                    this.control_type = this.node.getAttribute("control_type");
                else
                    this.control_type = "fileTree";                
                td.appendChild(input);
                tr.appendChild(td);
                if (this.readonly!="true") {
	                var td = document.createElement("td");
	                td.setAttribute("object",this.object_id);
	                td.setAttribute("id",this.node.id+"_selectCol");                
	                var img = document.createElement("img");
	                img.src = this.skinPath+"images/Buttons/select_folder.png";
	                img.setAttribute("object",this.object_id);
	                img.setAttribute("mouseover","img_mouseOver");
	                img.setAttribute("mouseout","img_mouseOut");
	                img.setAttribute("mousedown","img_mouseDown");
	                img.setAttribute("mouseup","img_mouseUp");
	                img.observe("mouseover",this.addHandler);
	                img.observe("mouseout",this.addHandler);
	                img.observe("mousedown",this.addHandler);
	                img.observe("mouseup",this.addHandler);
	                
	                if (this.node.getAttribute("control_type")=="image")
	                    img.observe("click",this.openCKHandler);
	                else {
	                    img.setAttribute("click","openSelectFileWindow");
	                    img.observe("click",this.addHandler);
	                }
	                img.style.height=input.style.height;
	                img.id = this.node.id+"_selectfilebtn";
	                if (this.readonly=="true")
	                    img.style.display = 'none';
	                td.appendChild(img);
	                tr.appendChild(td);
	                if (this.node.getAttribute("control_type") == "image" || this.node.getAttribute("control_type") =="fileManagerImage" ) {
	                    var td = document.createElement("td");
	                    td.setAttribute("object",this.object_id);
	                    td.setAttribute("id",this.node.id+"_previewCol");                
	                    var img = document.createElement("img");
	                    img.src = this.skinPath+"images/Buttons/imagePreviewButton.png";
	                    img.setAttribute("object",this.object_id);
	                    img.setAttribute("mouseover","img_mouseOver");
	                    img.setAttribute("mouseout","img_mouseOut");
	                    img.setAttribute("mousedown","img_mouseDown");
	                    img.setAttribute("mouseup","img_mouseUp");
	                    img.setAttribute("click","openImagePreviewWindow");
	                    img.observe("mouseover",this.addHandler);
	                    img.observe("mouseout",this.addHandler);
	                    img.observe("mousedown",this.addHandler);
	                    img.observe("mouseup",this.addHandler);
	                    img.observe("click",this.addHandler);
	                    img.style.height=input.style.height;
	                    img.id = this.node.id+"_imagePreviewBtn";
	                    if (this.readonly=="true")
	                        img.style.display = 'none';
	                    td.appendChild(img);
	                    tr.appendChild(td);
	                }
	                if (this.calcAlgo!=null) {
	                    var td = document.createElement("td");
	                    var img = document.createElement("img");                
	                    img.setAttribute("object",this.object_id);
	                    img.setAttribute("title","Вычислить");
	                    img.setAttribute("mouseover","img_mouseOver");
	                    img.setAttribute("mouseout","img_mouseOut");
	                    img.setAttribute("mousedown","img_mouseDown");
	                    img.setAttribute("mouseup","img_mouseUp");
	                	img.setAttribute("click","calc");
	                    img.observe("mouseover",this.addHandler);
	                    img.observe("mouseout",this.addHandler);
	                    img.observe("mousedown",this.addHandler);
	                    img.observe("mouseup",this.addHandler);
	                	img.observe("click",this.addHandler);
	                	img.src = this.skinPath+"images/Buttons/calcButton.png";
	                	img.id = this.node.id+"_calcBtn";
	                    img.style.height=input.style.height;
	                    if (this.readonly=="true")
	                        img.style.display = 'none';
	                    td.appendChild(img);
	                    tr.appendChild(td);                	
	                };
	                if (this.node.getAttribute("selectClass")!=null && this.node.getAttribute("hideSelectButton")!="true") {
	                    var td = document.createElement("td");
	                    var img = document.createElement("img");                
	                    img.setAttribute("object",this.object_id);
	                    img.setAttribute("title","Выбрать");
	                    img.setAttribute("mouseover","img_mouseOver");
	                    img.setAttribute("mouseout","img_mouseOut");
	                    img.setAttribute("mousedown","img_mouseDown");
	                    img.setAttribute("mouseup","img_mouseUp");
	                	img.setAttribute("click","getSelectedValue");
	                    img.observe("mouseover",this.addHandler);
	                    img.observe("mouseout",this.addHandler);
	                    img.observe("mousedown",this.addHandler);
	                    img.observe("mouseup",this.addHandler);
	                	img.observe("click",this.addHandler);
	                	
	                	img.src = this.skinPath+"images/Buttons/selectButton.png";
	                	img.id = this.node.id+"_selectBtn";                	
	                    img.style.height=input.style.height;
	                    if (this.readonly=="true")
	                        img.style.display = 'none';
	                    td.appendChild(img);
	                    tr.appendChild(td);                	                	
	                };                
	                if (this.is_ruleset) {
	                    var td = document.createElement("td");
	                    var img = document.createElement("img");
	                    img.src = this.skinPath+"images/Buttons/edit_ruleset.png";
	                    img.setAttribute("object",this.object_id);
	                    img.setAttribute("mouseover","img_mouseOver");
	                    img.setAttribute("mouseout","img_mouseOut");
	                    img.setAttribute("mousedown","img_mouseDown");
	                    img.setAttribute("mouseup","img_mouseUp");
	                    img.setAttribute("click","openRulesetWindow");
	                    img.observe("mouseover",this.addHandler);
	                    img.observe("mouseout",this.addHandler);
	                    img.observe("mousedown",this.addHandler);
	                    img.observe("mouseup",this.addHandler);
	                    img.observe("click",this.addHandler);
	                    img.style.height=input.style.height;
	                    img.id = this.node.id+"_editrulesetbtn";
	                    if (this.readonly=="true")
	                        img.style.display = 'none';
	                    td.appendChild(img);
	                    tr.appendChild(td);
	                }
                }
                if (this.node.getAttribute("hide_field")==null)
                	tabl.appendChild(tr);
                if (this.node.getAttribute("show_preview")=="true") {
                	var tr = document.createElement("tr");
                	tr.setAttribute("valign","top");
                	var td = document.createElement("td");
                	td.setAttribute("colspan","2");
                	td.setAttribute("class","inner");
                	var img = document.createElement("img");
                	img.id = this.node.id+"_preview";
                	if (this.value!="")
                		img.src = this.value;
                	else
                		img.src = "images/spacer.gif";
                	img.setAttribute("width","100");
                	img.setAttribute("object",this.object_id);
                	img.setAttribute("height","100");
                	img.setAttribute("click","img_Click");
                	img.observe("click",this.addHandler);
                	td.appendChild(img);
                	tr.appendChild(td);
                	tabl.appendChild(tr);
                };
                this.node.appendChild(tabl);
            } else {
                input.setAttribute("value",this.value);
            }
        }
        if (type_arr[0] == "path") {
            this.type = type_arr[0];
            if (this.regs.length==0)
                this.regs[0] = /^\/.*$/;
            input = $I(this.node.id+"_value");
            if (input == 0 || rebuild==true) {
                input = document.createElement("INPUT");
                input.setAttribute("object",this.object_id);
                input.id = this.node.id+"_value";
                input.setAttribute("value",this.value);
                input.setAttribute("name",this.shortName);
                input.setAttribute("class",this.input_class);
                if (this.readonly=="true")
                    input.setAttribute("readonly",this.readonly);
                input.setAttribute("control","yes");
                if (this.node.getAttribute("onkeypress")==null) {
                    input.setAttribute("keyup","keyPress");
                    input.observe("keyup",this.addHandler);
                }
                else {
                    input.setAttribute("keyup",this.node.getAttribute("onkeypress"));
                    input.observe("keyup",this.addHandler);                    
                }
                input.setAttribute("blur","on_blur");
                input.setAttribute("focus","on_focus");
                input.observe("blur",this.addHandler);
                input.observe("focus",this.addHandler);
                if (this.deactivated=="true") {
                    input.setAttribute("class",this.deactivate_class);
                }
                input.style.height = 23;
                if (this.invisible=="true")
                	input.style.display = "none";
                tabl = document.createElement("table");
                tabl.setAttribute("cellpadding","0");
                tabl.setAttribute("cellspacing","0");
                var tr = document.createElement("tr");
                tr.setAttribute("valign","top");
                var td = document.createElement("td");
                if (this.width!=null) {
                    tabl.setAttribute("width",this.width);
                    td.setAttribute("width",this.width);
                    input.style.width = this.width;
                }
                if (this.node.getAttribute("control_type")!=null)
                    this.control_type = this.node.getAttribute("control_type");
                else
                    this.control_type = "fileTree";
                td.appendChild(input);
                tr.appendChild(td);
                if (this.readonly!="true") {
	                var td = document.createElement("td");
	                var img = document.createElement("img");
	                img.src = this.skinPath+"images/Buttons/select_folder.png";
	                img.setAttribute("object",this.object_id);
	                img.setAttribute("mouseover","img_mouseOver");
	                img.setAttribute("mouseout","img_mouseOut");
	                img.setAttribute("mousedown","img_mouseDown");
	                img.setAttribute("mouseup","img_mouseUp");
	                img.setAttribute("click","openSelectPathWindow");
	                img.observe("mouseover",this.addHandler);
	                img.observe("mouseout",this.addHandler);
	                img.observe("mousedown",this.addHandler);
	                img.observe("mouseup",this.addHandler);
	                img.observe("click",this.addHandler);
	                
	                img.style.height=input.style.height;
	                img.id = this.node.id+"_selectpathbtn";
	                if (this.readonly=="true")
	                    img.style.display = 'none';
	                td.appendChild(img);
	                tr.appendChild(td);
	                if (this.calcAlgo!=null) {
	                    var td = document.createElement("td");
	                    var img = document.createElement("img");                
	                    img.setAttribute("object",this.object_id);
	                    img.setAttribute("title","Вычислить");
	                    img.setAttribute("mouseover","img_mouseOver");
	                    img.setAttribute("mouseout","img_mouseOut");
	                    img.setAttribute("mousedown","img_mouseDown");
	                    img.setAttribute("mouseup","img_mouseUp");
	                	img.setAttribute("click","calc");
	                    img.observe("mouseover",this.addHandler);
	                    img.observe("mouseout",this.addHandler);
	                    img.observe("mousedown",this.addHandler);
	                    img.observe("mouseup",this.addHandler);
	                	img.observe("click",this.addHandler);
	                	
	                	img.src = this.skinPath+"images/Buttons/calcButton.png";
	                	img.id = this.node.id+"_calcBtn";
	                    img.style.height=input.style.height;
	                    if (this.readonly=="true")
	                        img.style.display = 'none';
	                    td.appendChild(img);
	                    tr.appendChild(td);                	
	                };
	                if (this.node.getAttribute("selectClass")!=null && this.node.getAttribute("hideSelectButton")!="true") {
	                    var td = document.createElement("td");
	                    var img = document.createElement("img");                
	                    img.setAttribute("object",this.object_id);
	                    img.setAttribute("title","Выбрать");
	                    img.setAttribute("mouseover","img_mouseOver");
	                    img.setAttribute("mouseout","img_mouseOut");
	                    img.setAttribute("mousedown","img_mouseDown");
	                    img.setAttribute("mouseup","img_mouseUp");
	                	img.setAttribute("click","getSelectedValue");
	                    img.observe("mouseover",this.addHandler);
	                    img.observe("mouseout",this.addHandler);
	                    img.observe("mousedown",this.addHandler);
	                    img.observe("mouseup",this.addHandler);
	                	img.observe("click",this.addHandler);
	                	img.src = this.skinPath+"images/Buttons/selectButton.png";
	                	img.id = this.node.id+"_selectBtn";                	
	                    img.style.height=input.style.height;
	                    if (this.readonly=="true")
	                        img.style.display = 'none';
	                    td.appendChild(img);
	                    tr.appendChild(td);                	                	
	                };                
	                if (this.is_ruleset) {
	                    var td = document.createElement("td");
	                    var img = document.createElement("img");
	                    img.src = this.skinPath+"images/Buttons/edit_ruleset.png";
	                    img.setAttribute("object",this.object_id);
	                    img.setAttribute("mouseover","img_mouseOver");
	                    img.setAttribute("mouseout","img_mouseOut");
	                    img.setAttribute("mousedown","img_mouseDown");
	                    img.setAttribute("mouseup","img_mouseUp");
	                    img.setAttribute("click","openRulesetWindow");
	                    img.observe("mouseover",this.addHandler);
	                    img.observe("mouseout",this.addHandler);
	                    img.observe("mousedown",this.addHandler);
	                    img.observe("mouseup",this.addHandler);
	                    img.observe("click",this.addHandler);
	                    img.style.height=input.style.height;
	                    img.id = this.node.id+"_editrulesetbtn";
	                    if (this.readonly=="true")
	                        img.style.display = 'none';
	                    td.appendChild(img);
	                    tr.appendChild(td);
	                }
                };
                tabl.appendChild(tr);
                this.node.appendChild(tabl);
            } else {
                input.setAttribute("value",this.value);
            }
        }
        if (type_arr[0] == "list") {
            this.type = type_arr[0];
            var items_parts = type_arr[1].split("|");
            var values = items_parts[0].split("~");
            var titles = items_parts[1].split("~");
            input = $I(this.node.id+"_value");
            if (input == 0 || rebuild==true) {
                input = document.createElement("SELECT");
                input.setAttribute("object",this.object_id);
                input.id = this.node.id+"_value";
                input.setAttribute("name",this.shortName);
                input.setAttribute("class",this.input_class);
                input.setAttribute("value",this.value);
                input.setAttribute("size",this.node.getAttribute("size"));
                if (this.readonly=="true")
                    input.setAttribute("readonly",this.readonly);
                input.setAttribute("control","yes");
                if (this.node.getAttribute("onkeypress")==null) {
                    input.setAttribute("keyup","keyPress");
                    input.observe("keyup",this.addHandler);
                }
                else {
                    input.setAttribute("keyup",this.node.getAttribute("onkeypress"));
                    input.observe("keyup",this.addHandler);
                }
                input.setAttribute("blur","on_blur");
                input.setAttribute("focus","on_focus");
                input.setAttribute("change","onListChange");
                input.observe("blur",this.addHandler);
                input.observe("focus",this.addHandler);
                input.observe("change",this.addHandler);
                if (this.invisible=="true")
                	input.style.display = "none";
                for (var c=0;c<values.length;c++) {
                    if (values[c]==this.value)
                        selected = true;
                    else
                        selected = false;
                    input.options[input.length] = new Option(titles[c],values[c],selected,selected);
                }
                if (this.deactivated=="true") {
                    input.setAttribute("class",this.deactivate_class);
                }
                if (this.is_ruleset && this.readonly!="true") {
                    input.style.height = 23;
                    tabl = document.createElement("table");
                    tabl.setAttribute("cellpadding","0");
                    tabl.setAttribute("cellspacing","0");
                    if (this.width!=null) {
                        tabl.setAttribute("width",this.width);
                    }
                    var tr = document.createElement("tr");
                    tr.setAttribute("valign","top");
                    var td = document.createElement("td");
                    td.appendChild(input);
                    tr.appendChild(td);
                    td = document.createElement("td");
                    var img = document.createElement("img");
                    img.src = this.skinPath+"images/Buttons/edit_ruleset.png";
                    img.setAttribute("object",this.object_id);
                    img.setAttribute("mouseover","img_mouseOver");
                    img.setAttribute("mouseout","img_mouseOut");
                    img.setAttribute("mousedown","img_mouseDown");
                    img.setAttribute("mouseup","img_mouseUp");
                    img.setAttribute("click","openRulesetWindow");
                    img.observe("mouseover",this.addHandler);
                    img.observe("mouseout",this.addHandler);
                    img.observe("mousedown",this.addHandler);
                    img.observe("mouseup",this.addHandler);
                    img.observe("click",this.addHandler);
                    img.style.height=input.style.height;
                    img.id = this.node.id+"_editrulesetbtn";
                    if (this.readonly=="true")
                        img.style.display = 'none';
                    td.appendChild(img);
                    tr.appendChild(td);
                    tabl.appendChild(tr);
                    this.node.appendChild(tabl);
                } else {
                    this.node.appendChild(input);
                }
            } else {
                input.setAttribute("value",this.value);
                while (input.options.length>0)
                    input.options[0] = null;
                for (var c=0;c<values.length;c++) {
                	var selected = false;
                    if (values[c]==this.value)
                        selected = true;
                    else
                        selected = false;
                    input.options[input.length] = new Option(titles[c],values[c],selected,selected);
                }
            }
        }
        if (type_arr[0] == "listedit") {
            this.type = type_arr[0];
            this.item_string = type_arr[1];
            items_parts = type_arr[1].split("|");
            this.edittype = type_arr[2];            
            this.values = items_parts[0].split("~");
            this.titles = items_parts[1].split("~");
            this.bytitle = new Array;
            this.byvalue = new Array;
            for (var c=0;c<this.values.length;c++) {
                this.bytitle[this.titles[c].toUpperCase()] = this.values[c];
                this.byvalue[this.values[c].toUpperCase()] = this.titles[c];
            }
            if (this.regs.length==0) {
                if (this.edittype=="string") {
                    this.regs[0] = /^.*$/;
                }
                if (this.edittype=="integer") {
                    this.regs[0] = /^[1-9\-][0-9]*$/;
                }
                if (this.edittype=="file" || this.edittype=="path") {
                    this.regs[0] = /^\/.*$/;
                }
            }
            this.regs[this.regs.length] = /^$/;
            if (this.is_ruleset)
                this.regs[this.regs.length] = /^\/.*/;
            input = $I(this.node.id+"_value");
            if (input == 0 || rebuild==true) {
                tabl = document.createElement("table");
                tabl.setAttribute("cellpadding",0);
                tabl.setAttribute("cellspacing",0);
                if (this.width!=null) {
                    tabl.setAttribute("width",this.width);
                }
                var tr = document.createElement("tr");
                tr.setAttribute("valign","top");
                var td = document.createElement("td");
                td.setAttribute("width","100%");
                input = document.createElement("INPUT");
                input.setAttribute("object",this.object_id);
                if (this.readonly=="true")
                    input.setAttribute("readonly",this.readonly);
                input.id = this.node.id+"_value";
                input.style.width = "100%";
                input.setAttribute("name",this.node.id);
                input.setAttribute("class",this.input_class);
                if (this.byvalue[this.value.toUpperCase()]!=null)
                    input.setAttribute("value",this.byvalue[this.value.toUpperCase()]);
                else
                    input.setAttribute("value",this.value);

                input.setAttribute("control","yes");
                if (this.node.getAttribute("onkeypress")==null) {
                    input.setAttribute("keyup","keyPress");
                    input.observe("keyup",this.addHandler);
                }
                else {
                    input.setAttribute("keyup",this.getAttribute("onkeypress"));
                    input.observe("keyup",this.addHandler);
                }
                input.setAttribute("blur","on_blur");
                input.setAttribute("focus","on_focus");
                input.observe("blur",this.addHandler);
                input.observe("focus",this.addHandler);
                input.style.height='23';
                if (this.invisible=="true")
                	input.style.display = "none";
                td.appendChild(input);
                tr.appendChild(td);
                if (this.readonly!="true") {
	                var td = document.createElement("td");
	                var img = document.createElement("img");
	                img.src = this.skinPath+"images/Buttons/openlist.png";
	                img.setAttribute("object",this.object_id);
	                img.setAttribute("mouseover","img_mouseOver");
	                img.setAttribute("mouseout","img_mouseOut");
	                img.setAttribute("mousedown","img_mouseDown");
	                img.setAttribute("mouseup","img_mouseUp");
	                img.setAttribute("click","openListBox");
	                img.observe("mouseover",this.addHandler);
	                img.observe("mouseout",this.addHandler);
	                img.observe("mousedown",this.addHandler);
	                img.observe("mouseup",this.addHandler);
	                img.observe("click",this.addHandler);
	                img.style.height=input.style.height;
	                img.id = this.node.id+"_openlistbtn";
	                if (this.readonly=="true")
	                    img.style.display = 'none';
	                td.appendChild(img);
	                tr.appendChild(td);
	                if (this.calcAlgo!=null) {
	                    var td = document.createElement("td");
	                    var img = document.createElement("img");                
	                    img.setAttribute("object",this.object_id);
	                    img.setAttribute("title","Вычислить");
	                    img.setAttribute("mouseover","img_mouseOver");
	                    img.setAttribute("mouseout","img_mouseOut");
	                    img.setAttribute("mousedown","img_mouseDown");
	                    img.setAttribute("mouseup","img_mouseUp");
	                	img.setAttribute("click","calc");
	                    img.observe("mouseover",this.addHandler);
	                    img.observe("mouseout",this.addHandler);
	                    img.observe("mousedown",this.addHandler);
	                    img.observe("mouseup",this.addHandler);
	                	img.observe("click",this.addHandler);
	                	img.src = this.skinPath+"images/Buttons/calcButton.png";
	                	img.id = this.node.id+"_calcBtn";
	                    img.style.height=input.style.height;
	                    if (this.readonly=="true")
	                        img.style.display = 'none';
	                    td.appendChild(img);
	                    tr.appendChild(td);                	
	                };
	                if (this.node.getAttribute("selectClass")!=null && this.node.getAttribute("hideSelectButton")!="true") {
	                    var td = document.createElement("td");
	                    var img = document.createElement("img");                
	                    img.setAttribute("object",this.object_id);
	                    img.setAttribute("title","Выбрать");
	                    img.setAttribute("mouseover","img_mouseOver");
	                    img.setAttribute("mouseout","img_mouseOut");
	                    img.setAttribute("mousedown","img_mouseDown");
	                    img.setAttribute("mouseup","img_mouseUp");
	                	img.setAttribute("click","getSelectedValue");
	                    img.observe("mouseover",this.addHandler);
	                    img.observe("mouseout",this.addHandler);
	                    img.observe("mousedown",this.addHandler);
	                    img.observe("mouseup",this.addHandler);
	                	img.observe("click",this.addHandler);
	                	img.src = this.skinPath+"images/Buttons/selectButton.png";
	                	img.id = this.node.id+"_selectBtn";                	
	                    img.style.height=input.style.height;
	                    if (this.readonly=="true")
	                        img.style.display = 'none';
	                    td.appendChild(img);
	                    tr.appendChild(td);                	                	
	                };                                
	                if (this.is_ruleset) {
	                    var td = document.createElement("td");
	                    var img = document.createElement("img");
	                    img.src = this.skinPath+"images/Buttons/edit_ruleset.png";
	                    img.setAttribute("object",this.object_id);
	                    img.setAttribute("mouseover","img_mouseOver");
	                    img.setAttribute("mouseout","img_mouseOut");
	                    img.setAttribute("mousedown","img_mouseDown");
	                    img.setAttribute("mouseup","img_mouseUp");
	                    img.setAttribute("click","openRulesetWindow");
	                    img.observe("mouseover",this.addHandler);
	                    img.observe("mouseout",this.addHandler);
	                    img.observe("mousedown",this.addHandler);
	                    img.observe("mouseup",this.addHandler);
	                    img.observe("click",this.addHandler);
	                    img.style.height=input.style.height;
	                    img.id = this.node.id+"_editrulesetbtn";
	                    if (this.readonly=="true")
	                        img.style.display = 'none';
	                    td.appendChild(img);
	                    tr.appendChild(td);
	                }
                }
                tabl.appendChild(tr);
                if (this.deactivated=="true") {
                    input.setAttribute("class",this.deactivate_class);
                }
                this.node.appendChild(tabl);
            } else {
                if (this.byvalue[this.value.toUpperCase()]!=null)
                    input.setAttribute("value",this.byvalue[this.value.toUpperCase()]);
                else
                    input.setAttribute("value",this.value);
            }
        }
        if (type_arr[0] == "hidden") {
            this.type = type_arr[0];
            input = $I(this.node.id+"_value");

            if (input == 0 || rebuild==true) {
                input = document.createElement("INPUT");
            }

            input.setAttribute("object",this.object_id);
            input.setAttribute("type","hidden");
            input.id = this.node.id+"_value";
            input.setAttribute("name",this.shortName);
            input.setAttribute("class",this.input_class);
            input.setAttribute("value",this.value);
            this.showValue = this.node.getAttribute("showValue");

            if (this.readonly=="true")
                input.setAttribute("readonly",this.readonly);
            input.setAttribute("control","yes");
            if (this.deactivated=="true") {
                input.setAttribute("class",this.deactivate_class);
            }
            if (input.parentNode!=this.node)
                this.node.appendChild(input);
            var img = $I(this.node.id+"_selectBtn");
            if (img==0 || rebuild==true)
                img = document.createElement("input");
            img.setAttribute("type","image");
            if (this.node.getAttribute("buttonImage")!=null)
                img.src = this.node.getAttribute("buttonImage");
            else
                img.src = this.skinPath+"images/Buttons/selectButton.png";
            img.setAttribute("object",this.object_id);
            if (this.node.getAttribute("actionButton")!="true") {
                img.setAttribute("mouseover","img_mouseOver");
                img.setAttribute("mouseout","img_mouseOut");
                img.observe("mouseover",this.addHandler);
                img.observe("mouseout",this.addHandler);
            }
            img.setAttribute("dblclick","dblClick");
            img.setAttribute("mousedown","img_mouseDown");
            img.setAttribute("mouseup","img_mouseUp");
            img.setAttribute("click","selectValue");
            img.setAttribute("focus","on_focus");
            img.setAttribute("keyup","keyPress");
            img.setAttribute("submit","on_submit");
            img.observe("dblclick",this.addHandler);
            img.observe("mousedown",this.addHandler);
            img.observe("mouseup",this.addHandler);
            img.observe("click",this.addHandler);
            img.observe("focus",this.addHandler);
            img.observe("submit",this.addHandler);
            img.observe("keyup",this.addHandler);
            img.style.height=input.style.height;
            img.id = this.node.id+"_selectBtn";
            if (this.readonly=="true")
                img.style.display = 'none';
            if (this.showValue && this.invisible!="true") {
                var fld = $I(this.node.id+"_title");
                if (fld == 0 || rebuild==true) {
                    fld = document.createElement("INPUT");
                    is_new_fld = true;
                } else
                    is_new_fld = false;
                fld.style.height = 23;
                fld.setAttribute("object",this.object_id);
                fld.id = this.node.id+"_title";
                fld.setAttribute("onfocus","$O('"+this.node.id+"','').on_focus(event)");
                if (this.deactivated=="true") {
                    fld.setAttribute("class",this.deactivate_class);
                }
                if (this.node.getAttribute("fieldPresentation")!=null)
                    fld.value = this.node.getAttribute("fieldPresentation");
                else
                    fld.value = this.value;
                fld.setAttribute("readonly",true);
                if (is_new_fld) {
                    tabl = document.createElement("table");
                    tabl.setAttribute("cellpadding","0");
                    tabl.setAttribute("cellspacing","0");
                    if (this.width!=null) {
                        tabl.setAttribute("width",this.width);
                        fld.style.width = this.width;
                    }
                    var tr = document.createElement("tr");
                    tr.setAttribute("valign","top");
                    var td = document.createElement("td");
                    td.appendChild(fld);
                    tr.appendChild(td);
                    td = document.createElement("td");
                    td.appendChild(img);
                    tr.appendChild(td);
                    tabl.appendChild(tr);
                    this.node.appendChild(tabl);
                }
            } else {
                if (img.parentNode!=this.node)
                    this.node.appendChild(img);
            }
        }
        if (type_arr[0]=="entity")
            this.makeEntityControl(rebuild);
        this.node.removeAttribute("class");
    },
    
    img_Click:function(event) {
    	var elem = eventTarget(event);
    	if (elem.getAttribute("width")=="100" && elem.getAttribute("height")=="100") {
    		elem.setAttribute("width","");
    		elem.setAttribute("height","");
    	} else {
    		elem.setAttribute("width","100");
    		elem.setAttribute("height","100");    		
    	}
    },

    changeId: function(newid) {
        var item = $I(this.node.id+"_value");
        item.id = newid+"_value";
        var img = $I(this.node.id+"_openlistbtn");
        if (img!=0)
            img.id = newid+"_openlistbtn";
        delete objects.objects[this.id];
        objects.objects[newid] = this;
        this.id = newid;
        this.node.id = newid;        
    },

    processVariables: function(str) {
      if (this.variables == null)
          return str;
      var c1=null;
      var str = "";
      for (c1 in this.variables) {
          if (typeof(this.variables[c1])=="string") {
              str = str.replace(c1,this.variables[c1]);
          }
      }
      return str;
    },

    keyPress: function(event) {   	
    	if (this.readonly=="true")
    		return 0;    	
        this.variables = $O(this.parent_object_id,'').variables;
        var text = eventTarget(event).value;
        var code = event.charCode || event.keyCode;
        this.raiseEvent("CONTROL_KEYPRESS",$Arr("object_id="+this.object_id+",value="+text+String.fromCharCode(code)+",keycode="+code+",alt="+event.altKey+",ctrl="+event.ctrlKey));                                
        if (this.type == "integer" || this.type == "string" || this.type == "file" || this.type == "path" || this.type == "listedit" || this.type == 'static' || this.type == 'text' || this.type=="hidden") {
            var goout = false;
            //if (event.charCode == 0) goout = true;
            if (event.ctrlKey || event.altKey) goout = true;
            if (code < 32) goout = true;
            if (code==38 || code==40 || code==13 || code==45) goout = true;
            if (goout) {
                if (this.type == "listedit") {
                    var pos = getElementPosition(this.node.id+"_value");
                    var pos1 = getElementPosition(this.node.id+"_openlistbtn");
                    var x = pos.left-5;
                    var y = pos.top + pos.height-5; 
                    var w = pos.width+pos1.width;
                    var item_string = this.getListByKeypress(this.processVariables(text+String.fromCharCode(event.which)));
                    if (item_string != "") {
                        removeContextMenu();
                        var args = new Object;
                        args["hook"] = "3";
                         args["item_string"] = item_string;
                        args["width"] = w;
                        this.show_context_menu("ListBoxContextMenu_menu",x,y,this.node.id,args);
                    }
                }
                if (code==38 || code==40 || code==13) {
                  event = event || window.event;
                  event.cancelBubble = true;
                  if (event.preventDefault)
                     event.preventDefault();
                  else
                     event.returnValue = false;
                }
                var parent = $O(this.parent_object_id,"");
                if (parent!=null &parent["keyUp"]!=undefined)
                	parent.keyUp(event);
                return goout;
            }
            var found = true;
            if (this.type == "listedit") {
                if (this.readonly=="true")
                    return 0;
                var regs = this.getListRegs(text.length+1);
                for (var c=0;c<regs.length;c++) {
                    var reg = regs[c];
                    if (reg.exec(this.processVariables(text+String.fromCharCode(event.which)))!=null) {
                        found = false;
                        break;
                    }
                }
            }
            var regs = this.regs;
            for (var c=0;c<regs.length;c++) {
                var reg = regs[c];
                if (reg.exec(this.processVariables(text+String.fromCharCode(event.which)))!=null) {
                    found = false;
                    break;
                }
            }
            if (this.regs.length == 0)
                found = false;
            if (found) {
                if (this.type == "listedit") {
                    var item = $I(this.node.id+"_value");
                    var x = item.offsetLeft+1;
                    var y = item.parentNode.parentNode.parentNode.offsetTop+item.offsetHeight-4;
                    var w = item.offsetWidth;
                    var item_string = this.getListByKeypress(this.processVariables(text+String.fromCharCode(event.which)));
                    if (item_string != "") {
                        removeContextMenu();
                        var args = new Object;
                        args["hook"] = "3";
                        args["item_string"] = item_string;
                        args["width"] = w;
                        this.show_context_menu("ListBoxContextMenu_menu",x,y,this.node.id,args);
                    }
                }
                if (event.preventDefault) event.preventDefault();
                if (event.returnValue) event.returnValue = false;
                return false;
            } else {
                if (this.type == "listedit") {
                    var pos = getElementPosition(this.node.id+"_value");
                    var pos1 = getElementPosition(this.node.id+"_openlistbtn");
                    var x = pos.left-5;
                    var y = pos.top + pos.height-5;
                    var w = pos.width+pos1.width;
                    var item_string = this.getListByKeypress(this.processVariables(text+String.fromCharCode(event.which)));
                    if (item_string != "") {
                        removeContextMenu();
                        var args = new Object;
                        args["hook"] = "3";
                        args["item_string"] = item_string;
                        args["width"] = w;
                        this.show_context_menu("ListBoxContextMenu_menu",x,y,this.node.id,args);
                    }
                }
            }
            if (this.node.getAttribute("showOnKeyPress")=="true") {
            	this.getSelectedValue(event);                        
            }
        } else if (this.type=="decimal") {
            var goout = false;
            //if (event.charCode == 0) goout = true;
            if (event.ctrlKey || event.altKey) goout = true;
            if (code < 32) goout = true;
            if (goout) {
                if (this.type == "listedit") {
                    var pos = getElementPosition(this.node.id+"_value");
                    var pos1 = getElementPosition(this.node.id+"_openlistbtn");
                    var x = pos.left-5;
                    var y = pos.top + pos.height-5; 
                    var w = pos.width+pos1.width;
                    var item_string = this.getListByKeypress(this.processVariables(text+String.fromCharCode(event.which)));
                    if (item_string != "") {
                        removeContextMenu();
                        var args = new Object;
                        args["hook"] = "3";
                        args["item_string"] = item_string;
                        args["width"] = w;
                        this.show_context_menu("ListBoxContextMenu_menu",x,y,this.node.id,args);
                    }
                }
                if (code==38 || code==40 || code==13) {
                  event = event || window.event;
                  event.cancelBubble = true;
                  if (event.preventDefault)
                     event.preventDefault();
                  else
                     event.returnValue= false;
                }
                return goout;
            }
            if (text!="") {
                var res = parseFloat(text);
                if (!isNaN(res)) {
                    if (res!=text) {
                        text = res;
                    }                    
                    $I(this.node.id+"_value").value = text;
                } else
                    return false;
            }
            if (this.node.getAttribute("showOnKeyPress")=="true")
            	this.getSelectedValue(event);                        
        } else {
            if (this.node.getAttribute("showOnKeyPress")=="true")
            	this.getSelectedValue(event);                                	
        }
    },

    checkValue: function(text,is_title) {
        var err = "";
        if (text == "") {
            if (this.must_set!=true && this.must_set!="true")
                return "";
            else {
                err = 'Поле не заполнено !';
                return err;
            }
        }
        if ((this.type == "integer" || this.type == "string" || this.edittype == "string" || this.edittype == "integer" || this.edittype == "decimal" || this.edittype == "null") && err == "") {
            var found = true;
            if (this.type=="listedit") {
                if (is_title) {
                    if (this.bytitle[text.toUpperCase()]!=null)
                        found = false;
                } else {
                    if (this.byvalue[text.toUpperCase()]!=null)
                        found = false;
                }
            }
            regs = this.regs;
            for (var c=0;c<regs.length;c++) {
                var reg = regs[c];                
                if (reg.exec(text)!=null) {
                    found = false;
                    break;
                }
            }
            if (this.regs.length == 0)
                found = false;
            if (found) {
                if (text!=0)
                    err = 'Введено неверное значение поля !';
            }
            if (this.is_ruleset && text.substr(0,1)=="/" && this.type!="string" && err=="") {
                var reg = /^\/.*\.rules$/;
                if (reg.exec(text)==null) {
                    err = 'Введено неверное значение поля !';
                }
            }
        }
        if ((this.type == "file" || this.type == "path" || this.edittype == "file" || this.edittype == "path") && err == "") {
            var found = true;
            if (this.type=="listedit") {
                if (this.bytitle[text.toUpperCase()]!=null)
                    found = false;
            }
            var regs = this.regs;
            for (var c=0;c<regs.length;c++) {
                var reg = regs[c];
                if (reg.exec(text)!=null) {
                    found = false;
                    break;
                }
            }
            if (found) {
                err = 'Введено неверное значение поля !';
            }
        }
        if (this.type=="decimal") {
        	if (this.node.getAttribute("empty")!="true" && text!="-") {
        		var res = parseFloat(text);
        		if (res!="NaN" && !isNaN(res)) {            	
        			if (res!=text) {
        				text = res;
        				$I(this.node.id+"_value").value = text; 
        			}                                    
        		} else
        			err = "Введено неверное значение поля";
        	}
        };
        return err;
    },

    on_blur: function(event) {
        this.focused=false;
    	if (this.readonly=="true")
    		return 0;
        this.variables = $O(this.parent_object_id,'').variables;
        var text = this.processVariables(this.getValue());
        blur_object = this;
        blur_item = eventTarget(event);
        blur_error = this.checkValue(text,true);
        if (this.type == "list" && blur_error=="") {
            if (this.readonly=="true") {
                var val = this.node.getAttribute("value");
                this.node.value = val;
                var lst = $I(this.node.id+"_value");
                for (var i3=0;i3<lst.length;i3++) {
                    if (lst.options[i3].value == val) {
                        lst.selectedIndex = i3;
                    }
                }
            }
        }
        if (this.deactivated=="true") {
            var input = $I(this.node.id+"_value");
            if (this.type=="listedit") {
                input.setAttribute("class",this.deactivate_class);
            } else {
                input.setAttribute("class",this.deactivate_class);
            }
        }
        if (blur_error == "") {
            if (this.type =="date")
                this.changeDateField(event);
            if (this.type!="list")
                this.raiseEvent("CONTROL_VALUE_CHANGED",$Arr("object_id="+this.node.id+",old_value="+this.value+",value="+this.getValue().replace(/,/g,"xoxoxo").replace(/=/g,'xox')));
        }
        removeContextMenu();
    },

    on_focus: function(event) {
    	if (this.readonly=="true")
    		return 0;
        if (blur_object!=null) {
            if (blur_error!="") {
                var object_id = blur_item.getAttribute("object");
                if (object_id!=this.object_id) {
                    event.cancelBubble = true;
                    var be = blur_error;
                    blur_object.setFocus();
                    if (event.preventDefault) event.preventDefault();
                    if (event.returnValue) event.returnValue = false;
                    this.reportMessage(be,"error",true);
                    return false;
                }
            }
        }

        if (this.deactivated=="true") {
            var input = $I(this.node.id+"_value");
            if (this.type=="listedit") {
                input.setAttribute("class",this.input_class);
            } else {
                input.setAttribute("class",this.input_class);
            }
        }
        this.focused=true;        
        this.raiseEvent("CONTROL_HAS_FOCUSED",$Arr("parent_object_id="+this.parent_object_id+",object_id="+this.object_id+",old_value="+this.value+",value="+this.getValue()));

    },

    on_change: function(event) {
        if (this.readonly == "true") {
            event.cancelBubble = true;
            if (event.preventDefault) event.preventDefault();
            if (event.returnValue) event.returnValue = false;
            return false;
    	} else {
    		this.raiseEvent("CONTROL_VALUE_CHANGED",$Arr("object_id="+this.node.id+",parent_object_id="+this.parent_object_id+",old_value="+this.value+",value="+this.getValue().replace(/,/g,"xoxoxo").replace(/=/g,'xox')));
    	}
    },

    onListChange: function(event) {
        if (this.readonly == "true") {
            event.cancelBubble = true;
            if (event.preventDefault) event.preventDefault();
            if (event.returnValue) event.returnValue = false;
            return false;
        } else {
            this.raiseEvent("CONTROL_VALUE_CHANGED",$Arr("object_id="+this.node.id+",parent_object_id="+this.parent_object_id+",old_value="+this.value+",value="+this.getValue().replace(/,/g,"xoxoxo").replace(/=/g,'xox')));
        }
    },

    getListRegs: function(count_symbols) {
        var result = new Array;
        for (var c=0;c<this.titles.length;c++) {
            var reg = this.titles[c];
            if (count_symbols>0) {
                if (reg.length>count_symbols)
                    reg = reg.substr(0,count_symbols)+".*";

            }
            reg = "^"+reg+"$";
            result[result.length] = new RegExp(reg,"i");
        }
        return result;
    },

    getListByKeypress: function(val) {
        var reg = new RegExp(val+".*","i");
        var result = new Array;
        for (var c=0;c<this.titles.length;c++) {
            if (reg.exec(this.titles[c]))
                result[result.length] = this.titles[c];
        }
        if (result.length>0)
            return result.join("~")+"|"+result.join("~");
        else
            return '';
    },

    getValue: function() {
        if (this.type == "integer" || this.type == "string" || this.type == "list" || this.type == "path" || this.type=="file" || this.type=="hidden" || this.type=="decimal" || this.type=="boolean" || this.type=="entity" || this.type=="array") {
            return getElementById(this.node,this.node.id+"_value").value;
        }
        else if (this.type=="date") {
            return getElementById(this.node,this.node.id+"_value").value;
        }
        else if (this.type=="text") {
            if (this.node.getAttribute("control_type")=="tinyMCE")
                return tinyMCE.get(this.node.id+"_value").getContent();
            else if (this.node.getAttribute("control_type")=="editArea")
                return editAreaLoader.getValue(this.node.id+"_value");
			else
                return getElementById(this.node,this.node.id+"_value").value;
			
        }
        if (this.type == "plaintext")
            return this.node.innerHTML;
        if (this.type == "static")
        	return getElementById(this.node,this.node.id+"_value").innerHTML;
        if (this.type == "listedit") {
            if (this.bytitle[getElementById(this.node,this.node.id+"_value").value.toUpperCase()]!=null)
                return this.bytitle[getElementById(this.node,this.node.id+"_value").value.toUpperCase()];
            else
                return getElementById(this.node,this.node.id+"_value").value;
        }
    },

    setValue: function(value,notGenEvent) {
        if (this.type == "integer" || this.type == "string" || this.type == "list" || this.type == "path" || this.type=="file" || this.type=="hidden" || this.type=="array" || this.type=="boolean" || this.type=="decimal" || this.type=="entity") {
        	if (value!=null) {
        		if (this.type=="integer" || this.type=="decimal") {
        			if (!isNaN(value))
        				getElementById(this.node,this.node.id+"_value").value = value;
        			else
        				value = "0";
        		} else {
        			if (this.type=="file") {
        				if (this.node.getAttribute("absolutePath")=="false") {
        					value = value.replace(this.node.getAttribute("root_dir")+"/","");
        				}
        			}
        			getElementById(this.node,this.node.id+"_value").value = value;
        		}
        	}
            if (this.type == "list") {
                for (var ce=0;ce<getElementById(this.node,this.node.id+"_value").options.length;ce++) {
                    if (getElementById(this.node,this.node.id+"_value").options[ce]==value) {
                        getElementById(this.node,this.node.id+"_value").selectedIndex = ce;
                    }
                }
            }
        }
        if (this.type == "static") {
    		getElementById(this.node,this.node.id+"_value").innerHTML = value;        	
        }
        if (this.type == "boolean") {
            if (value == "1") {
              $I(this.node.id+"_checkbox").checked = true;
            } else {
              $I(this.node.id+"_checkbox").checked = false;                
            }
        }
        if (this.type == "plaintext") {
            this.node.innerHTML = value;
        }
        if (this.type == "listedit") {            
                getElementById(this.node,this.node.id+"_value").value = value;
        }
        if (this.type == "text") {
        	if (this.node.getAttribute("control_type")=="editArea")
        		editAreaLoader.setValue(this.node.id+"_value",value);
        	else if (this.node.getAttribute("control_type")=="tinyMCE")
        		tinyMCE.get(this.node.id+"_value").setContent(value);
        	else {
        		$I(this.node.id+"_value").value = value;
        	}
        }
        if (notGenEvent==null) {
        	if (value!=null) {
        		if (typeof(value)=="number")
        			value = value.toString();
        		this.raiseEvent("CONTROL_VALUE_CHANGED",$Arr("object_id="+this.object_id+",parent_object_id="+this.parent_object_id+",value="+value.replace(/,/g,'xoxoxo')));
        	}
        	else
        		this.raiseEvent("CONTROL_VALUE_CHANGED",$Arr("object_id="+this.object_id+",parent_object_id="+this.parent_object_id+",value="));
        }
    },

    getPresentation: function() {
        if (this.type == "integer" || this.type == "decimal" || this.type == "string" || this.type == "path" || this.type=="file" || this.type=="hidden") {
            return getElementById(this.node,this.node.id+"_value").value;
        }
        if (this.type == "list") {
            return getElementById(this.node,this.node.id+"_value").item(getElementById(this.node,this.node.id+"_value").selectedIndex).text;
        }
        if (this.type == "plaintext")
            return this.node.innerHTML;
        if (this.type == "listedit") {
            return getElementById(this.node,this.node.id+"_value").value;
        }
        if (this.type == "entity") {
            return this.node.getAttribute("valueTitle");
        }
    },

    img_mouseOver: function(event) {
        eventTarget(event).src = eventTarget(event).src.replace("_clicked.png",".png");
        eventTarget(event).src = eventTarget(event).src.replace(".png","_hover.png");
    },

    img_mouseOut: function(event) {
        eventTarget(event).src = eventTarget(event).src.replace("_clicked.png",".png");
        eventTarget(event).src = eventTarget(event).src.replace("_hover.png",".png");
    },

    img_mouseDown: function(event) {
        eventTarget(event).src = eventTarget(event).src.replace("_hover.png","_clicked.png");
    },

    img_mouseUp: function(event) {
        eventTarget(event).src = eventTarget(event).src.replace("_hover.png",".png");
        eventTarget(event).src = eventTarget(event).src.replace("_clicked.png",".png");
    },

    openListBox: function(event) {
    	if (this.readonly=="true")
    		return 0;
        var pos = getElementPosition(this.node.id+"_value");
        var pos1 = getElementPosition(this.node.id+"_openlistbtn");
        var x = pos.left-5;
        var y = pos.top + pos.height-5;
        var w = pos.width+pos1.width;
        removeContextMenu();
        var args = new Object;
        args["hook"] = "3";
        args["item_string"] = this.item_string;
        args["width"] = w;
        this.show_context_menu("ListBoxContextMenu_menu_"+this.object_id,x,y,this.node.id,args);
    },

    openRulesetWindow: function(event) {
    	if (this.readonly=="true")
    		return 0;
        var reg = /^\/.*\.rules$/;
        if (reg.exec(this.getValue())==null) {
            return 0;
        }
        var params = new Array;
        params[params.length] = "$object->init_string='$object->valueType=\""+this.full_type+"\";$object->rulesFile=\""+this.getValue()+"\";$object->load();';";
        params[params.length] = "$object->parent_object_id='"+this.object_id+"';";
        var obid = this.object_id.split("_");
        var elem_id = "MailScannerRulesetEditor_"+this.module_id+"_"+obid.pop();
        var window_elem_id = "Window_"+getClientId(elem_id).replace(/_/g,"");
        getWindowManager().show_window(window_elem_id,elem_id,params,this.object_id,elem.id);
    },

    openSelectPathWindow: function() {
    	if (this.readonly=="true")
    		return 0;
    	if (this.control_type==null)
    		this.control_type = this.node.getAttribute("control_type");
        if (this.control_type == "fileManager") {
            var params = new Object;
            params["useCase"] = "selectPath";
            params["hook"] = "3";
            if (this.node.getAttribute("rootPath")!=null)
            	params["rootPath"] = this.node.getAttribute("rootPath");
            else
            	params["rootPath"] = "/";
            params["currentPath"] = this.getValue();
            getWindowManager().show_window("Window_FileManagerSelectPath","FileManager_"+this.module_id+"_SelectPath",params,this.object_id,this.node.id);
        } else {
            var leftPosition = (screen.availWidth-250)/2;
            var topPosition = (screen.availHeight-300)/2;
            var absolutePath = "true";
            var rootPath = "/";
            if (this.node.getAttribute("absolutePath")!=null)
                absolutePath = this.node.getAttribute("absolutePath");
            if (this.node.getAttribute("rootPath")!=null)
                rootPath = this.node.getAttribute("rootPath");
            var params = new Object;
            params["title"] = rootPath;
            params["target_item"] = this.node_id+"_value";
            params["absolute_path"] = absolutePath;
            var args = new Array;
            this.selectParentWindow = window.showModalDialog("index.php?object_id=DirectoryTree_"+this.module_id+"_Tree1&hook=show&arguments="+Object.toJSON(params),args,"dialogWidth:250px; dialogHeight:300px; dialogLeft:"+leftPosition+"px; dialogTop:"+topPosition+"px;");
        }
    },

    openSelectFileWindow: function() {
    	if (this.readonly=="true")
    		return 0;
    	if (this.control_type==null)
    		this.control_type = this.node.getAttribute("control_type");
        if (this.control_type == "fileManager" || this.control_type=="fileManagerImage") {
            var params = new Object;
            params["useCase"] = "selectFile";
            params["hook"] = "3";
            if (this.node.getAttribute("rootPath")!=null)
            	params["rootPath"] = this.node.getAttribute("rootPath");
            else
            	params["rootPath"] = "/";
            
            params["currentPath"] = this.getValue();
            if (params["currentPath"]!="") {
            	var arr = params["currentPath"].split("/");
            	arr.pop();
            	params["currentPath"] = arr.join("/");
            }
            getWindowManager().show_window("Window_FileManagerSelectPath","FileManager_"+this.module_id+"_SelectPath",params,this.object_id,this.node.id);
        } else {
	        var leftPosition = (screen.availWidth-250)/2;
	        var topPosition = (screen.availHeight-300)/2;
	        var absolutePath = "true";
	        var rootPath = "/";
	        var root_dir = rootPath;
	        if (this.node.getAttribute("absolutePath")!=null)
	            absolutePath = this.node.getAttribute("absolutePath");
	        if (this.node.getAttribute("rootPath")!=null) 
	            rootPath = this.node.getAttribute("rootPath");
	        if (this.node.getAttribute("root_dir")!=null) 
	            root_dir = this.node.getAttribute("root_dir");
	        else
	            root_dir = rootPath;
	        var params = new Object;
	        params["title"] = rootPath;
	        params["target_item"] = this.node_id+"_value";
	        params["absolute_path"] = absolutePath;
	        params["root_dir"] = root_dir;
	        args = new Array;
	        this.selectParentWindow = window.showModalDialog("index.php?object_id=DirectoryTree_"+this.module_id+"_Tree1&hook=show&arguments="+Object.toJSON(params),args,"dialogWidth:250px; dialogHeight:300px; dialogLeft:"+leftPosition+"px; dialogTop:"+topPosition+"px;");
        }
    },

    openImagePreviewWindow: function() {
        if (this.getValue()!=null && this.getValue()!="" && this.getValue()!=0) {
            var leftPosition = (screen.availWidth-250)/2;
            var topPosition = (screen.availHeight-300)/2;
            var args = new Array;
            this.selectParentWindow = window.showModalDialog(this.getValue(),args,"dialogWidth:350px; dialogHeight:350px; dialogLeft:"+leftPosition+"px; dialogTop:"+topPosition+"px;");
        }
    },

    setFocus: function() {
        if ($I(this.node.id+"_value")!=0) {
            $I(this.node.id+"_value").focus();
        } else if ($I(this.node.id+"_checkbox")!=0) {
            $I(this.node.id+"_checkbox").focus();
        }
        this.raiseEvent("CONTROL_HAS_FOCUSED",$Arr("parent_object_id="+this.parent_object_id+",object_id="+this.object_id+",old_value="+this.value+",value="+this.getValue()));
    },

    plainTextClick: function(event) {
	      eventTarget(event).focus();
	      if (this.control_type=="email" && this.node.getAttribute("isGroup")!="true")
	    	  location.href = "mailto:"+this.value;
	      if (this.control_type=="file" && this.node.getAttribute("isGroup")!="true")
	    	  window.open("root/"+this.value);
	      event = event || window.event;
	      event.cancelBubble = true;
	      this.raiseEvent("CONTROL_CLICKED",$Arr("object_id="+this.object_id));
	      if (event.preventDefault)
	         event.preventDefault();
	      else
	         event.returnValue= false;
    },

    selectValue: function(event) {
      if (this.type == "hidden") {
          this.setFocus();
          if (this.node.getAttribute("actionButton")=="true") {
              this.raiseEvent("DATATABLE_ACTIONBUTTON_CLICKED",$Arr("object_id="+this.object_id+",parent_object_id="+this.parent_object_id));
              event = event || window.event;
              event.cancelBubble = true;
              if (event.preventDefault)
                 event.preventDefault();
              else
                 event.returnValue= false;
              return false;
          } else {
              if (this.node.getAttribute("editorType")!=null)
                  this.editorType = this.node.getAttribute("editorType");
              if (this.editorType=="window") {
            	  var params = new Object;
            	  params["editorType"] = this.editorType;
            	  params["control_id"] = this.object_id;
            	  params["fieldName"] = this.node.getAttribute("fieldName");
            	  params["entity_id"] = this.node.getAttribute("entity_id");
                  var args = new Array;
                  var leftPosition = (screen.availWidth-400)/2;
                  var topPosition = (screen.availHeight-400)/2;
                  var options = "dialogWidth:400px; dialogHeight:400px; dialogLeft:"+leftPosition+"px; dialogTop:"+topPosition+"px;";
                  this.selectValueWindow = window.showModalDialog(str,args,options);
              }
              if (this.editorType=="div") {
                    var div = $I(this.node.getAttribute("divName"));
                    var obj = this;
                    var params = new Object;
                    params["editorType"] = this.editorType;
                    params["control_id"] = this.object_id;
                    params["fieldName"] = this.node.getAttribute("fieldName");
                    params["entity_id"] = this.node.getAttribute("entity_id");
                    
                    new Ajax.Request("index.php",
                    {
                        method: "post",
                        parameters: {ajax: true, object_id: obj.node.getAttribute("editorObject"),
                                     hook: "show", arguments: Object.toJSON(params)},
                        onSuccess: function(transport) {
                            var response = transport.responseText;

                            if (response != "")
                            {
                                var response_object = response.evalJSON();
                                div.innerHTML = response_object["css"].concat(response_object["html"]);
                                eval(response_object["javascript"].replace("<script>","").replace("<\/script>",""));
                                var arr = response_object["args"].toString().split('\n');
                                var args = new Array;
                                for (var counter=0;counter<arr.length;counter++)
                                {
                                    var arg_parts = arr[counter].split('=');
                                    args[arg_parts[0]]=arg_parts[1];
                                }
                            }
                        }
                    });
                  }
                  if (this.editorType == "WABWindow") {
                	  var arr = this.object_id.split("_");
                	  var module_id = arr[1]+"_"+arr[2];
                      var elem_id = this.node.getAttribute("editorObject")+module_id+"_";
                      var window_elem_id = "Window_Window"+elem_id.replace(/_/g,"");
                      var params = new Object;
                      params["editorType"] = this.editorType;
                      params["hook"] = "setParams";
                      params["control_id"] = this.object_id;
                      params["fieldName"] = this.node.getAttribute("fieldName");
                      params["entity_id"] = this.node.getAttribute("entity_id");
                      getWindowManager().show_window(window_elem_id,elem_id,params,$O($O(this.parent_object_id).parent_object_id),$O($O(this.parent_object_id).parent_object_id).node.id,null,true);
                  }
                  event = event || window.event;
                  event.cancelBubble = true;
                  if (event.preventDefault)
                     event.preventDefault();
                  else
                     event.returnValue= false;
                  return false;
            }
          }
    },

    dispatchEvent: function($super,event,params) {
        if (event=="CONTROL_VALUE_CHANGED") {
            var obje = $O(params["object_id"],"");
            if (obje!=0 && obje!=null) {
                if (obje.object_id == this.object_id && params["value"]!=null) {
                    params["value"] = params["value"].replace(/xox/g,"=");
                    if (this.type=="hidden") {
                        this.value = params["value"];
                        if (this.node.getAttribute("showValue")=="true") {
                            $I(this.node.id+"_title").readonly = false;
                            $I(this.node.id+"_title").value = params["value"];
                            $I(this.node.id+"_value").value = params["value"];
                            $I(this.node.id+"_title").readonly = true;
                        }
                    } else if (this.type=="entity" || this.type=="array") {
                            this.value = params["value"];
                            $I(this.node.id).setAttribute("value",params["value"]);
                            $I(this.node.id+"_value").setAttribute("value",params["value"]);
                            var title_field = $I(this.node.id+"_valueTitle"); 
                        if (params["valueTitle"]!=null && params["valueTitle"]!=params["value"]) {
                            $I(this.node.id+"_valueTitle").value = params["valueTitle"];
                        } else if (this.type=="entity" && this.node.getAttribute("list")!="true") {
                        	var args = new Object;
                        	args["adapterId"] = this.adapterId;
                        	var objid = "";
                            if (this.module_id!=null)
                                objid = this.value.split("_").shift()+"_"+this.module_id+"_"+this.value.split("_").pop();
                            else
                                objid = this.value;
                            var obj = this;
                            new Ajax.Request("index.php",
                            {
                                method: "post",
                                parameters: {ajax: true, object_id: objid,
                                             hook: "getPresentation", arguments: Object.toJSON(args)},
                                onSuccess: function(transport) {
                                    var response = transport.responseText;
                                    obj.valueTitle = response;
                                    title_field.value = obj.valueTitle;
                                }
                            });                        	
                        }
                    } else {
                        this.value = params["value"];
                        $I(this.node.id).setAttribute("value",params["value"]);   
						if (this.type=="boolean") {
							$I(this.node.id+"_value").click();
						}                        
                        if ($I(this.node.id+"_value")!=0) {
                            if (this.type=="text") {
                                $I(this.node.id+"_value").innerHTML = params["value"];
                            }
                            else {
                                $I(this.node.id+"_value").setAttribute("value",params["value"]);
                                $I(this.node.id+"_value").value = params["value"];
                            }
                        }
                        if (this.node.getAttribute("show_preview")=="true") {
                        	if (params["value"]!="")
                        		$I(this.node.id+"_preview").src = params["value"];
                        }
                    }
                }
            }
        }
        $super(event,params);
    },

    cellClick: function(event) {
        this.raiseEvent("TABLE_HEADER_CLICKED",$Arr('object_id='+this.object_id));
    },

    dblClick: function(event) {
        this.raiseEvent("CONTROL_DOUBLE_CLICKED",$Arr('object_id='+this.object_id+",parent_object_id="+this.parent_object_id));
    },

    on_submit: function(event) {
        return false;
    },
    
    getSelectedValue: function(event) {
    	if (this.readonly=="true")
    		return 0;
    	var opener_item = "";
    	var currentMenu = $O("SelectValueContextMenu_"+$O(this.parent_object_id,'').module_id+"_"+this.object_id.replace($O(this.parent_object_id,'').module_id+"_","").replace(/_/g,''),"");
    	if (this.node.getAttribute("opener_item")!=null)
    		opener_item = this.node.getAttribute("opener_item");
    	else
    		opener_item = this.node.id;
    	if (this.node.getAttribute("selectOptions")!=null)
    		this.selectOptions = this.node.getAttribute("selectOptions")+"~opener_item="+opener_item;
    	else
    		this.selectOptions = "opener_item="+this.node.id;
    	this.selectClass = this.node.getAttribute("selectClass");
    	if (currentMenu!=current_context_menu)
    		removeContextMenu();
        var pos = getElementPosition(this.node.id+"_value");
        var pos1 = null;
        if ($I(this.node.id+"_selectBtn")!=0)
        	pos1 = getElementPosition(this.node.id+"_selectBtn");        
        var x = pos.left-5;
        var y = pos.top + pos.height-5;
        var w = 0;
        if (pos1!=null)
        	w = pos.width+pos1.width;
        else
        	w = pos.width;
        if (x+w>window.innerWidth) {
            w = w - (window.innerWidth-x-10);
        }
    	var args = new Object;
    	args["selectClass"] = this.selectClass;
    	args["selectOptions"] = this.selectOptions;
    	args["value"] = eventTarget(event).value;//+String.fromCharCode(event.which);
        args["width"] = w;
        args["height"] = this.windowHeight;
    	if (currentMenu!=current_context_menu || current_context_menu==null) {
    		if ($O(this.parent_object_id,'').module_id!="")
    			this.show_context_menu("SelectValueContextMenu_"+$O(this.parent_object_id,'').module_id+"_"+this.object_id.replace($O(this.parent_object_id,'').module_id+"_","").replace(/_/g,''),x,y,this.node.id,args);
    		else
    			this.show_context_menu("SelectValueContextMenu_"+this.object_id.replace(/_/g,''),x,y,this.node.id,args);
    	} else {
    		currentMenu.selectClass = this.selectClass;
    		currentMenu.selectOptions = this.selectOptions;
    		currentMenu.value = args["value"];
    		currentMenu.load();
    	}
    },
    
    calc: function(params) {
    	if (this.readonly=="true")
    		return 0;
    	if (this.calcAlgo=="")
    		return 0;
    	
    	// Формируем массив входных параметров для расчета    	
    	
    	// Сначала попадают значения всех полей текущего контекста (то есть, текущей формы ввода)
    	var args = new Object;
    	args["params"] = $O(this.parent_object_id,"").getValues().toObject();
    	args["params"]["obj"] = this.parent_object_id;
    	
    	// Затем попадают параметры, которые есть в свойстве calcProperties данного элемента
    	var o = null;
    	for (o in this.calcProperties) {
    		if (typeof this.calcProperties[o] != "function") {
    			args["params"][o] = this.calcProperties[o];
    		}
    	}
    	
    	// И в конце концов добавляем к этому списку параметры, переданные напрямую в эту функцию
    	for (o in params) {
    		if (typeof params != "function")
    			args["params"][o] = params[o];
    	}    	    	
    	// Выполняем запрос к серверу на выполнение алгоритма с переданными параметрами
    	var obj = this;
    	var algo_id = "MetadataObjectCode_"+this.module_id+"_10_"+this.calcAlgo;
        new Ajax.Request("index.php", {
            method:"post",
            parameters: {ajax: true, object_id: algo_id, hook: '6', arguments: Object.toJSON(args)},
            onSuccess: function(transport)
            {
                var response = transport.responseText;
                obj.setValue(response);
            }
        });  	
    },
    
    textAreaKeyUp :function(event) {
    	var txt = $I(this.node.id+"_value");
    	var div = $I(this.node.id+"_hiddendiv");
    	div.innerHTML = this.getValue().replace(/\n/g,"<br/>")+"<br/><br/>";
    	var pos = getElementPosition(div.id);
    	txt.style.height = pos.height;    	
    },    	    	

    lcs: function(event) {
    	var ielem = eventTarget(event);
    	event.cancelBubble = true;
        updobj=ielem;
        getObj('fc').style.left=Left(ielem);
        getObj('fc').style.top=Top(ielem)+ielem.offsetHeight;
        getObj('fc').style.display='';

        var curdt=ielem.value.split(" ").shift();
        var curdtarr=curdt.split('.');
        isdt=true;
        for(var k=0;k<curdtarr.length;k++) {
                if (isNaN(curdtarr[k]))
                        isdt=false;
        }
        if (isdt&(curdtarr.length==3)) {
                ccm=curdtarr[1]-1;
                ccy=curdtarr[2];
                prepcalendar(curdtarr[0],curdtarr[1]-1,curdtarr[2]);
        }
        event = new Array;
        event.target = ielem;
        $O(this.object_id,this.instance_id).changeDateField(event);
    }   
});