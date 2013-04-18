var EntityDataTable = Class.create(DataTable, {

    getSingleValue: function() {
        return "";
    },

    getFieldListArray: function() {        
    },
    
    getClassName: function() {
    	return "EntityDataTable";
    },

    selectSortColumn: function () {
        if (this.sortOrder!="") {
            var sort_parts = this.sortOrder.split(" ");
            var sortField = sort_parts[0];
            this.sortField = sortField;
            this.sortColumn = this.getColumnNumber(sortField);
            var sortDirection="";
            if (sort_parts[1]!=null) {
                sortDirection = sort_parts[1];
                var imgsrc="";
                if (sortDirection=='DESC')
                    imgsrc = this.skinPath+"images/Buttons/sortdesc.png";
                else
                    imgsrc = this.skinPath+"images/Buttons/sortasc.png";
            }
            else {
                sortDirection = "ASC";
                imgsrc = this.skinPath+"images/Buttons/sortasc.png";
            }
            var header_ctl = this.getItem(this.sortHeaderRow,this.sortColumn);
            if (header_ctl!=0) {
                $I(header_ctl.node.id+"_image").src = imgsrc;
            }
        }
    },

    sort: function(del) {
        if (this.sortOrder!="") {
            var sort_parts = this.sortOrder.split(" ");
            var sortField = sort_parts[0];
            this.sortField = sortField;
            this.sortColumn = this.getColumnNumber(sortField);
            var ctl1 = this.getItem(0,this.sortColumn);
            if (ctl1!=null && ctl1.node!=null)
				this.sortOrder = this.sortOrder+" "+ctl1.node.getAttribute("fieldType");
			if (del) {				
				this.deleteRows();
				this.numPages=1;
			}
            if (this.numPages>1 && $O(this.pagePanelId,'')!=null) {
                this.currentPage = 1;
                event = new Array;
                event.target = $I($O(this.pagePanelId,'').node.id+"_p1");
                $O(this.pagePanelId,'').changePage(event,this.currentPage);
            } else
                this.rebuild();            
        }
    },

    rebuild: function(rebuild) {
        var obj1 = this;
        if (this.currentPage==null)
			this.currentPage = 1;
        if (this.hierarchy)
            this.h = "true";
        else
            this.h = "false";
        
		var args = new Object;

		this.condition = this.condition.replace(/AND  AND/g,"AND");
		this.condition = this.condition.replace(/AND AND/g,"AND");
		this.condition = this.condition.replace(/@parent IS NOT EXISTS AND @parent IS NOT EXISTS/g,"@parent IS NOT EXISTS");
		
		args["className"] = this.className;
		args["sortOrder"] = this.sortOrder;
		args["adapterId"] = this.adapterId;
		args["fieldList"] = this.fieldList;
		args["entityImages"] = this.entityImages;
		args["additionalFields"] = this.additionalFields;
		args["additionalCondition"] = this.additionalCondition;
		args["additionalLinks"] = this.additionalLinks;
		args["conditionFields"] = this.conditionFields;
		args["topLinkObject"] = this.topLinkObject;
		args["itemsPerPage"] = this.itemsPerPage;
		args["hierarchy"] = this.h;
		args["autoload"] = "true";
		args["table"] = this.tble;
		args["parent_object_id"] = this.parent_object_id;
		args["currentPage"] = this.currentPage;
		args["selectGroup"] = this.selectGroup;
		args["collection"] = this.collection;
		args["collectionLoadMethod"] = this.collectionLoadMethod;
		args["collectionGetMethod"] = this.collectionGetMethod;
		args["showHierarchy"] = this.showHierarchy;
  	    args["tagsCondition"] = this.tagsCondition;
  	    if (this.showQRCode!=null)
  	    	args["showQRCode"] = this.showQRCode;
  	    var o=null;
		for (o in this.params) {
			if (typeof this.params[o] != "function") {
				args[o] = this.params[o];
			}
		}

        // Получаем старый список сущностей из прошлого массива tbl.rows
        var old_values = obj1.getColValues(obj1.rows,0);
		if (this.advancedCondition!=null)
			args["advancedCondition"] = this.advancedCondition;
		args["condition"] = this.condition;
  	    args["defaultListProfile"] = this.defaultListProfile;
  	    if (this.prevListProfile!=null)
  	  	    args["prevListProfile"] = this.prevListProfile;
        new Ajax.Request("index.php", {
            method: "post",
            parameters: {ajax: true, object_id: obj1.object_id,
                         hook: '3',arguments: Object.toJSON(args)},
            onSuccess: function(transport) {            	
                var response = transport.responseText;
                // Получаем новый массив строк в переменную rws;
                var rsp = "var rws="+obj1.object_id+"Rows;var np="+obj1.object_id+"NumPages;";
                eval(response+"\n"+rsp);
                
                obj1.numPages = np;
                // Получаем новый список сущностей из нового массива
                var new_values = obj1.getColValues(rws,0);
                
                // Определяем строки для изменения
                var values_to_change = new_values.intersect(old_values);
                // Определяем строки для добавления
                var values_to_add = old_values.diff(new_values);

                // Определяем строки для удаления
                var values_to_delete = new_values.diff(old_values);
                
                // Добавляем недостающие строки
                var cc=obj1.rows.length;
                var ce=null;
                for (ce in values_to_add) {
                    if (typeof values_to_add[ce] != "function") {
                        obj1.addRow(rws[new_values.indexOf(values_to_add[ce])],cc);
                        cc = cc+1;
                    }
                }
                // Удаляем устаревшие (включая таблицы развернутых сущностей)
                var inner_table = null;
                for (ce in values_to_delete) {
                    if (typeof values_to_delete[ce] != "function") {
                        v = values_to_delete[ce];
                        var row = obj1.rows[old_values.indexOf(values_to_delete[ce])]["node"];//ctl.node.parentNode.parentNode;
                        if (row != null) {
                            var rn = 0;
                            var els = row.getElementsByTagName("*");
                            for (rn=0;rn<els.length;rn++) {
                                if (els[rn].getAttribute("control")=="yes" || els[rn].getAttribute("type") == "plaintext") {
                                    var ctll = $O(els[rn].getAttribute("object"),'');
                                    if (ctll!=0) {
                                        delete objects.objects[ctll.object_id];
                                        delete ctll;
                                    }
                                }
                            }
                        }
                        if (row.nextSibling!=null) {
                            if (row.nextSibling.id.length==0) {
                                inner_table = row.nextSibling;
                            }
                        }                       
                        if (inner_table!=null) {
                            inner_table.parentNode.removeChild(inner_table);
                            if (obj1.module_id=="" || obj1.module_id==null) {
                                $O(obj1.tableClassName+"_"+v.replace(/_/g,'')+obj1.object_id.replace(/_/g,'')).raiseEvent("DESTROY",$Arr("object_id="+obj1.tableClassName+"_"+v.replace(/_/g,'')+obj1.object_id.replace(/_/g,'')));
                                delete objects.objects[obj1.tableClassName+"_"+v.replace(/_/g,'')+obj1.object_id.replace(/_/g,'')];
                            } else {
                                if ($O(obj1.tableClassName+"_"+obj1.module_id+"_"+v.replace(obj1.module_id+"_","").replace(/_/g,'')+obj1.object_id.replace(obj1.module_id+"_","").replace(/_/g,''))!=null && $O(obj1.tableClassName+"_"+obj1.module_id+"_"+v.replace(obj1.module_id+"_","").replace(/_/g,'')+obj1.object_id.replace(obj1.module_id+"_","").replace(/_/g,''))!="") {
                                    delete $O($O(obj1.tableClassName+"_"+obj1.module_id+"_"+v.replace(obj1.module_id+"_","").replace(/_/g,'')+obj1.object_id.replace(obj1.module_id+"_","").replace(/_/g,''),'').pagePanelId,'');
                                    delete objects.objects[$O(obj1.tableClassName+"_"+obj1.module_id+"_"+v.replace(obj1.module_id+"_","").replace(/_/g,'')+obj1.object_id.replace(obj1.module_id+"_","").replace(/_/g,''),'').pagePanelId];
                                    $O(obj1.tableClassName+"_"+obj1.module_id+"_"+v.replace(obj1.module_id+"_","").replace(/_/g,'')+obj1.object_id.replace(obj1.module_id+"_","").replace(/_/g,'')).raiseEvent("DESTROY",$Arr("object_id="+obj1.tableClassName+"_"+obj1.module_id+"_"+v.replace(obj1.module_id+"_","").replace(/_/g,'')+obj1.object_id.replace(obj1.module_id+"_","").replace(/_/g,'')));
                                    delete objects.objects[obj1.tableClassName+"_"+obj1.module_id+"_"+v.replace(obj1.module_id+"_","").replace(/_/g,'')+obj1.object_id.replace(obj1.module_id+"_","").replace(/_/g,'')];
                                }
                            }                     
                            inner_table = null;
                        }
                        row.parentNode.removeChild(row);
                    }
                }
               // Обновляем значения в элементах управления столбцов существующих строк
                for (ce=0;ce<values_to_change.length;ce++) {
                    rws[new_values.indexOf(values_to_change[ce])]["node"] = obj1.rows[old_values.indexOf(values_to_change[ce])]["node"];
                    var ce1=0;
                    for (ce1=0;ce1<obj1.rows[old_values.indexOf(values_to_change[ce])]["cells"].length;ce1++) {
                        rws[new_values.indexOf(values_to_change[ce])]["cells"][ce1]["node"] = obj1.rows[old_values.indexOf(values_to_change[ce])]["cells"][ce1]["node"];
                        rws[new_values.indexOf(values_to_change[ce])]["cells"][ce1]["control_node"] = obj1.rows[old_values.indexOf(values_to_change[ce])]["cells"][ce1]["control_node"];
                        delete obj1.rows[old_values.indexOf(values_to_change[ce])]["cells"][ce1]["node"];
                        delete obj1.rows[old_values.indexOf(values_to_change[ce])]["cells"][ce1]["control_node"];
                    }
                    delete obj1.rows[old_values.indexOf(values_to_change[ce])]["node"];
                }
                delete obj1.rows;
                obj1.rows = null;
                // Подменяем прошлый массив строк на новый (к этому моменту строки таблицы уже привязаны к этому новому массиву)
                obj1.rows = rws;
                // перестраиваем таблицу
                obj1.build(rebuild);

                // Упорядочиваем строки таблицы в соответствии с порядком нового массива
                obj1.rows.reverse();
                var firstRow = obj1.rows[0].node;
                ce = obj1.rows.length-1;
                while (ce>=0) {
                    var sible = obj1.rows[ce].node.nextSibling;
                    if (obj1.rows[ce].node.parentNode!=null) {
                        if (sible!=null && sible.id=="") {
                            obj1.rows[ce].node.parentNode.insertBefore(sible,firstRow);
                            obj1.rows[ce].node.parentNode.insertBefore(obj1.rows[ce].node,sible);
                        } else
                            obj1.rows[ce].node.parentNode.insertBefore(obj1.rows[ce].node,firstRow);
                    } else {
                    }
                    row = obj1.rows.length-ce-1;
                    obj1.rows[ce].node.id = obj1.node.id+"_row_"+row;
                    for (ce1=0;ce1<obj1.rows[ce].node.childNodes.length;ce1++) {
                        if (obj1.rows[ce].node.childNodes[ce1].tagName=="TD") {
                            obj1.rows[ce].node.childNodes[ce1].setAttribute("row",row);
                            var controls = obj1.rows[ce].node.childNodes[ce1].childNodes;
                            if (controls[0]!=null)
                                controls[0].setAttribute("row",row);
                        }
                    }
                    ce = ce - 1;
                };                
                // устанавливаем заголовок столбца сортировки
                obj1.selectSortColumn();
                obj1.previousRow = null;
                if (obj1.profileData!=null) {
                	obj1.profileData = obj1.profileData.evalJSON();
                	for (o in obj1.profileData) {
                		if (typeof obj1.profileData[o] != "function") {
                			obj1[o] = obj1.profileData[o].replace(/xoxoxo/g,"'");
                		}
                	}
                };                
                delete $O(obj1.pagePanelId,'');
                delete objects.objects[obj1.pagePanelId];
                $I(obj1.node.id+"_innerFrame").src = obj1.frameSrc.replace('"current_page":"1"','"current_page":"'+obj1.currentPage+'"').replace('"num_pages":"'+obj1.staticNumPages+'"','"num_pages":"'+obj1.numPages+'"');
                obj1.afterRebuild();
                // подсвечиваем текущую строку
                if (obj1.currentControl!=null && obj.currentControl!=0) {
                    var cctl = obj1.currentControl;
                    if (obj1.findControlByValue(obj1.currentControl.getValue())!=null)
                    	obj1.raiseEvent("CONTROL_HAS_FOCUSED",$Arr("parent_object_id="+cctl.parent_object_id+",object_id="+cctl.object_id+",old_value="+cctl.value+",value="+cctl.getValue()+",skip_activation=true"));
                }
            }
        });
    },
    
    afterRebuild: function() {
    	this.onLoad();
		this.selectCurrentEntity();
    },
    
    ACTIVATE_WINDOW_processEvent: function(params) {
    	if (params["object_id"]==this.parent_object_id) {
    		if (this.currentControl!=null && this.currentControl!=0)
    			this.currentControl.setFocus();
    	}
    },

    dispatchEvent: function($super,event,params) {
        $super(event,params);
        if (event=="DATATABLE_ACTIONBUTTON_CLICKED") {
            if (params['parent_object_id']==this.object_id) {
                var ctrl = $O(params['object_id']);
                if (ctrl.node.getAttribute("explode")=="true") {
                    var current_row = ctrl.node.parentNode.parentNode;
                    var next_row = current_row.nextSibling;
                    var col_count = this.fieldList.split(",").length+3;
                    if (ctrl.node.getAttribute("opened")==null) {
                        var inserted_row = document.createElement("tr");
                        inserted_row.setAttribute("class","cell");
                        inserted_row.setAttribute("object",this.object_id);
                        inserted_col = document.createElement("td");
                        inserted_col.setAttribute("colspan",col_count);
                        inserted_col.setAttribute("object",this.object_id);
                        inserted_col.setAttribute("width","100%");
                        inserted_col.innerHTML = "&nbsp;";
                        inserted_row.appendChild(inserted_col);
                        var row = ctrl.node.parentNode.getAttribute("row");
                        if (next_row!=null)
                            current_row.parentNode.insertBefore(inserted_row,next_row);
                        else
                            current_row.parentNode.appendChild(inserted_row);
                        $I(ctrl.node.id+"_selectBtn").src = $I(ctrl.node.id+"_selectBtn").src.replace("_clicked.png","").replace(".png","_clicked.png");
                        ctrl.node.setAttribute("opened","true");
                        var entityName = this.getItem(row,0).getValue();
                        var ename = entityName;
                        if (this.module_id!="")
                            entityName = entityName.replace(this.module_id+"_","");
                        var entName = entityName;
                        entityName = entityName.split("_");                        
                        entityName.shift();
                        entityName = entityName.join("_");
                        entityName = entityName.split("_").pop();
                        if (this.hierarchy)
                            this.h = "true";
                        else
                            this.h = "false";

						 var args =  new Object;
						 args["className"] = this.className;
						 this.condition = this.condition.replace(/ AND \@parent IS NOT EXISTS/g,"");
						 this.condition = this.condition.replace(/AND \@parent IS NOT EXISTS/g,"");
						 this.condition = this.condition.replace(/ AND \@parent\.\@name\=[0-9]+/g,"");
						 this.condition = this.condition.replace(/AND \@parent\.\@name\=[0-9]+/g,"");
						 this.condition = this.condition.replace(/\@parent\.\@name\=[0-9]+/g,"");
						 if (this.condition=="")
							 args["condition"] = "@parent.@name="+entityName;
						 else if (this.condition!="@parent IS NOT EXISTS")
							 args["condition"] = this.condition+" AND @parent.@name="+entityName;
						 else
							 args["condition"] = "@parent.@name="+entityName;
						 if (this.childCondition!="")
							 args["condition"] = args["condition"]+" AND "+this.childCondition;

						 args["fieldList"] = ctrl.node.getAttribute("fieldList").replace(/,/g,"~");
						 args["itemsPerPage"] = this.itemsPerPage;
						 args["adapterId"] = this.adapterId;
						 args["parent_object_id"] = this.parent_object_id;
						 args["editorType"] = this.editorType;
						 args["parentEntity"] = ename;
						 args["selectGroup"] = this.selectGroup;
						 args["divName"] = this.divName;
						 args["additionalFields"] = this.additionalFields;
						 args["additionalLinks"] = this.additionalLinks;
						 args["topLinkObject"] = this.topLinkObject;
						 args["destroyDiv"] = this.destroyDiv;
						 args["tableId"] = this.tableId;
						 args["className"] = this.className;
						 args["collection"] = this.collection;						 
						 args["collectionGetMethod"] = this.collectionGetMethod;
						 args["collectionLoadMethod"] = this.collectionLoadMethod;
						 args["sortOrder"] = this.sortOrder;
						 args["windowWidth"] = this.windowWidth;
						 args["windowHeight"] = this.windowHeight;
						 args["windowTitle"] = this.windowTitle;
						 args["tagsCondition"] = this.tagsCondition;
						 if (this.tableClassName=="" || this.tableClassName==null)
							 this.tableClassName=this.getClassName();
						 if (this.win!=null)
							args["window_id"] = this.win.id;
						 args["hierarchy"] = this.h;
						 var objid="";
                        if (this.module_id=="")
                            objid = this.tableClassName+"_"+entName.replace(/_/g,'')+this.object_id.replace(this.module_id+"_","").replace(/_/g,'');//.replace(/_/g,'')+this.object_id.replace(/_/g,'');
                        else
                            objid = this.tableClassName+"_"+this.module_id+"_"+entName.replace(this.module_id+"_","").replace(/_/g,'')+this.object_id.replace(this.module_id+"_","").replace(/_/g,'');//.replace(/_/g,'')+this.object_id.replace(/_/g,'');
                        new Ajax.Request("index.php",
                            {
                                method: "post",
                                parameters: {ajax: true, object_id: objid,
                                             hook: '4', arguments: Object.toJSON(args)},
                                onSuccess: function(transport) {                                    
                                    var response = transport.responseText;
                                    if (response!="") {
                                        var response_object = response.evalJSON();
                                        inserted_col.innerHTML = response_object["css"].concat(response_object["html"]);                                        
                                        eval(response_object["javascript"].replace("<script>","").replace("<\/script>",""));
                                        var arr = response_object["args"].toString().split('\n');
                                        var args = new Array;
                                        var counter=0;
                                        for (counter=0;counter<arr.length;counter++)
                                        {
                                            var arg_parts = arr[counter].split('=');
                                            args[arg_parts[0]]=arg_parts[1];
                                        }
                                        $O(objid,'').build(true);
                                        $I(tbl.node.id+"_innerFrame").src = tbl.frameSrc;
                                    }
                                }
                            });
                    } else if (ctrl.node.getAttribute("opened")=="true") {
                        next_row.style.display = "none";
                        ctrl.node.setAttribute("opened","false");
                        $I(ctrl.node.id+"_selectBtn").src = $I(ctrl.node.id+"_selectBtn").src.replace("_clicked.png",".png");
                    } else {
                        next_row.style.display = "";
                        ctrl.node.setAttribute("opened","true");
                        $I(ctrl.node.id+"_selectBtn").src = $I(ctrl.node.id+"_selectBtn").src.replace("_clicked.png","").replace(".png","_clicked.png");
                    }
                }
            }
        }
        if (event=="CONTROL_DOUBLE_CLICKED") {
            if (params["parent_object_id"]==this.object_id) {
            	if (window.getSelection()!=null)
            		window.getSelection().removeAllRanges();
                var elem_node = $O(params["object_id"],'').node;
                var elem_id = this.getItem($O(params["object_id"],'').node.parentNode.getAttribute("row"),0).getValue();
                var elem_title = this.getItem($O(params["object_id"],'').node.parentNode.getAttribute("row"),'title').getValue();
                var window_elem_id = "Window_Window"+elem_id.replace(/_/g,"");
                if (this.editorType == "entityDataTable") {
                    var explode_elem = this.getItem($O(params["object_id"].node.parentNode.getAttribute("row")),2);
                    if (explode_elem.getAttribute("explode") == "true") {
                        var dt = $O(this.tableId);
                        dt.currentPage = 1;
                        dt.condition = "@parent.@name="+elem_end;
                        dt.rebuild();
                        $I(this.tableId+"_innerFrame").src = $I(this.tableId+"_innerFrame").src;
                        this.raiseEvent("NODE_CLICKED",$Arr("object_id="+this.object_id+",node_id="+elem_id));
                    } else if (this.forEntitySelect) {
                        this.raiseEvent("NODE_CLICKED",$Arr("object_id="+this.object_id+",node_id="+elem_id));
                    }
                } else if (this.editorType == "window") {
                  if (this.objRole!=null && this.objRole["canRead"]!=null && this.objRole["canRead"]=="false") {
                    	this.reportMessage("Не достаточно прав доступа","error",true);
                    	return 0;
                  };                	
				  params = new Object;
				  params["asAdminTemplate"] = "true";
                  params["fieldAccess"] = this.fieldAccess;
	              params["fieldDefaults"] = this.fieldDefaults;
                  var str = "index.php?object_id="+elem_id+"&hook=1&arguments="+Object.toJSON(params);
                  var args = "modal";
                  var leftPosition = (screen.availWidth-this.windowWidth)/2;
                  var topPosition = (screen.availHeight-this.windowHeight)/2;
                  var options = "dialogWidth:"+this.windowWidth+"px; dialogHeight:"+this.windowHeight+"px; dialogLeft:"+leftPosition+"px; dialogTop:"+topPosition+"px;";
                  this.raiseEvent("NODE_CLICKED",$Arr("object_id="+this.object_id+",node_id="+elem_id));
                  this.selectValueWindow = window.showModalDialog(str,args,options);
                } else if (this.editorType == "div") {
                    if (this.objRole!=null && this.objRole["canRead"]!=null && this.objRole["canRead"]=="false") {
                    	this.reportMessage("Не достаточно прав доступа","error",true);
                    	return 0;
                    };                	
                    var div = $I(this.divName);
					var args = new Object;
					args["asAdminTemplate"] = "true";
	                args["fieldAccess"] = this.fieldAccess;
		            args["fieldDefaults"] = this.fieldDefaults;
	
                    new Ajax.Request("index.php", {
                        method: "post",
                        parameters: {ajax: true, object_id: elem_id,
                                     hook: 'admTpl', arguments: Object.toJSON(args)},
                        onSuccess: function(transport) {
                            response = transport.responseText;
                            if (response != "")
                            {
                                var response_object = response.evalJSON();
                                var divs = div.getElementsByTagName("DIV");
                                if (divs[0]!=null) {
                                    var object = $O(divs[0].getAttribute("object"),'');
                                    object.raiseEvent("DESTROY",$Arr("object_id="+object.object_id));
                                    delete objects.objects[object.object_id];
                                }
                                div.innerHTML = response_object["css"].concat(response_object["html"]);
                                eval(response_object["javascript"].replace("<script>","").replace("<\/script>",""));
                                var arr = response_object["args"].toString().split('\n');
                                var args = new Array;
                                var counter=0;
                                for (counter=0;counter<arr.length;counter++) {
                                    var arg_parts = arr[counter].split('=');
                                    args[arg_parts[0]]=arg_parts[1];
                                }
                            }
                        }
                    });
                } else if (this.editorType=="WABWindow") {  
                    if (this.objRole!=null && this.objRole["canRead"]!=null && this.objRole["canRead"]=="false") {
                    	this.reportMessage("Не достаточно прав доступа","error",true);
                    	return 0;
                    };                	
                    params = new Object;
					params["asAdminTemplate"] = "true";
					params["hook"] = "admTpl";
	                params["fieldAccess"] = this.fieldAccess;
		            params["fieldDefaults"] = this.fieldDefaults;
			        var loading_img = document.createElement("img");
			        loading_img.id = "loadImg";
			        loading_img.src = this.skinPath+"images/Tree/loading2.gif";
			        loading_img.style.zIndex = "100";
			        loading_img.style.position = "absolute";
			        loading_img.style.top=(window.innerHeight/2-33);
			        loading_img.style.left=(window.innerWidth/2-33);        
			        this.node.appendChild(loading_img);	
                    getWindowManager().show_window(window_elem_id,elem_id,params,null,null);
                } else if (this.editorType=="none") {
                	if (this.selectGroup!="1" && elem_node.getAttribute("isGroup")=="true")
                		return 0;                	
                    this.raiseEvent("ENTITY_SELECTED_FROM_DATATABLE",$Arr("object_id="+this.object_id+",parent_object_id="+this.parent_object_id+",entity_id="+elem_id+",entity_title="+elem_title));
                }
            }
        }
        if (event == "ENTITY_CHANGED" || event == "ENTITY_ADDED") {
            var reg = new RegExp(this.classNameForReg);
            var classname = params["object_id"].split("_").shift();
            if (classname.search(reg)!=-1 || classname==this.className) {
				this.rebuild();                  
            }
        }
        if (event == "ENTITY_DELETED" || event == "ENTITY_MARK_DELETED" || event == "ENTITY_MARK_UNDELETED") {
            var object_ids = params["object_id"].split("~");
            var o=null;
            for (o in object_ids) {                
                var ob = $O(object_ids[o],'');
                if (ob!=null) {
                    if (ob.win!=null) {
                        getWindowManager().remove_window(ob.win.id,'',true);
                    }
                }
                this.raiseEvent("DESTROY",$Arr("object_id="+object_ids[o]));
            }
            for (o in object_ids) {
                if (this.findControlByValue(object_ids[o])!=0) {
                    this.rebuild();                  
                    $I(this.node.id+"_innerFrame").src = this.frameSrc;
                    return 0;
                }
            }
        }
        if (event == "ENTITY_REGISTERED" || event == "ENTITY_MARK_REGISTERED" || event == "ENTITY_MARK_UNREGISTERED") {
            var object_ids = params["object_id"].split("~");
            var o=null;
            for (o in object_ids) {
                if (this.findControlByValue(object_ids[o])!=0) {
                    this.rebuild();                  
                    $I(this.node.id+"_innerFrame").src = this.frameSrc;
                    return 0;
                }
            }
        }
        
        if (event=="CONTROL_KEYPRESS") {
            this.obje = $O(params["object_id"],"");
            if (this.obje!=0) {
                if (this.obje.parent_object_id == this.object_id) {
                    var key_code = params["keycode"];
                    var alt_key = params["alt"];
                    if (key_code==45) {
                    	if (this.readonly=="true")
                    		return 0;
                        if (alt_key=="true")
                            this.insertButton_onClick(event);
                        else
                            this.addButton_onClick(event);
                    }
                    if (alt_key=="true") {
                    	if (this.readonly=="true")
                    		return 0;
                        if (key_code==43) {
                            this.copyButton_onClick(event);
                        }
                        if (key_code==46)
                            this.deleteButton_onClick(event);
                    }
                    if (key_code=="40") {
                        if (alt_key=="true") {
                            var col_node = this.obje.node.parentNode;
                            var row_id = parseInt(col_node.getAttribute("row"));
                            var ctl_obj = this.getItem(row_id,this.obje.node.parentNode.getAttribute("column"));
                            this.moveDownButton_onClick(event);
                            ctl_obj.setFocus();
                        }
                        else {
                            var col_node = this.obje.node.parentNode;
                            var row_id = parseInt(col_node.getAttribute("row"))+1;
                            var ctl_obj = this.getItem(row_id,this.obje.node.parentNode.getAttribute("column"));
                            if (ctl_obj!=0 && ctl_obj.type!="plaintext")
                                ctl_obj.setFocus();
                        }
                    }
                    if (key_code=="38") {
                        if (alt_key=="true") {
                        	if (this.readonly=="true")
                        		return 0;
                            var col_node = this.obje.node.parentNode;
                            var row_id = parseInt(col_node.getAttribute("row"));
                            var ctl_obj = this.getItem(row_id,this.obje.node.parentNode.getAttribute("column"));
                            this.moveUpButton_onClick(event);
                            ctl_obj.setFocus();
                        }
                        else {
                            var col_node = this.obje.node.parentNode;
                            var row_id = parseInt(col_node.getAttribute("row"))-1;
                            var ctl_obj = this.getItem(row_id,this.obje.node.parentNode.getAttribute("column"));
                            if (ctl_obj!=0 && ctl_obj.type!="plaintext")
                                ctl_obj.setFocus();
                        }
                    }
                    if (key_code=="39") {
                        var col_node = this.obje.node.parentNode;
                        var row_id = parseInt(col_node.getAttribute("row"));
                        var ctl_obj = this.getItem(row_id,this.getColumnNumber(this.obje.node.parentNode.getAttribute("column"))+1);
                        if (ctl_obj!=0 && ctl_obj.type!="plaintext")
                            ctl_obj.setFocus();                    	
                    }
                    if (key_code=="37") {
                        var col_node = this.obje.node.parentNode;
                        var row_id = parseInt(col_node.getAttribute("row"));
                        var ctl_obj = this.getItem(row_id,this.getColumnNumber(this.obje.node.parentNode.getAttribute("column"))-1);
                        if (ctl_obj!=0 && ctl_obj.type!="plaintext")
                            ctl_obj.setFocus();                    	
                    }                    
                    if (key_code=="46") {
                        var col_node = this.obje.node.parentNode;
                        var row_id = parseInt(col_node.getAttribute("row"));
                        var ctl_obj = this.getItem(row_id,this.getColumnNumber(this.obje.node.parentNode.getAttribute("column"))-1);
                        if (ctl_obj!=0 && ctl_obj.type!="plaintext")
                            if (this["setDeletionMark"]!=undefined) {
                            	if (alt_key!="true")
                            		this.setDeletionMark(1);
                            	else
                            		this.setDeletionMark(0);
                            }
                    }                    
                    if (key_code=="13") {                    	
                        var col_node = this.obje.node.parentNode;
                        var column = parseInt(this.getColumnNumber(col_node.getAttribute("column")))+1;
                        var row = parseInt(col_node.getAttribute("row"));                        
                        var ctl_obj = this.getItem(row,column);
                        if (ctl_obj==0) {
                            var col = 0;
                            while (1==1) {
                                ctl_obj = this.getItem(row+1,col);
                                if (ctl_obj.type!="plaintext") {
                                    break;
                                }
                                if (ctl_obj==0) {
                                    break;
                                }
                                col++;
                            }
                        }
                        if (ctl_obj!=0) {
                        	this.raiseEvent("CONTROL_DOUBLE_CLICKED",$Arr("object_id="+ctl_obj.object_id+",parent_object_id="+this.object_id));
                        }
                    }
                }
            }
        }
        
    },
    
    selectCurrentEntity: function(focus) {
        var tbl = this;
        if (tbl.entityId=='') {
            if (tbl.currentControl!=null && tbl.currentControl!=0 && tbl.currentControl!="" && tbl.findControlByValue(tbl.currentControl.getValue())!=null) {
                var row = this.currentControl.node.parentNode.getAttribute("row");
                var it = this.getItem(row,0);
                if (it!=0)
                    this.entityId = it.getValue();             
            }
        }
        if (tbl.entityId!='') {
            tbl.entityControl = tbl.findControlByValue(tbl.entityId);
            if (tbl.entityControl!=0 && tbl.entityControl!=null) {
            	if (focus==true) {
					tbl.entityControl.setFocus();
					tbl.entityControl.node.scrollIntoView(true);
            	} else {
            		tbl.raiseEvent("CONTROL_HAS_FOCUSED",$Arr("object_id="+tbl.entityControl.object_id+",skip_activation=true"));
            	}
            }
        }        
    },
    
    addButton_onClick: function(event) {
        var item = this.parentEntity;
        event.cancelBubble = true;
        if (this.objRole!=null && this.objRole["canRead"]!=null && this.objRole["canAdd"]=="false") {
        	this.reportMessage("Не достаточно прав доступа !","error",true);
        	return 0;
        }
        var obj = this;
        
        new Ajax.Request("index.php",
        {
            method: "post",
            parameters: {ajax: true, object_id: item,
                         hook: "childClass"},
            onSuccess: function(transport) {
                var response = transport.responseText;
                var item_array = item.split("_");
                item_array = item.split("_");
                item_array.pop();
                item_array.shift();
                var new_item = "";
                if (response!="")
                    new_item = response+"_"+item_array.join("_")+"_";
                else
                    new_item = obj.className+"_"+obj.module_id+"_";
                var params = new Object;
                params["hook"] = "afterInit";
                params["item"] = item;
                params["fieldAccess"] = obj.fieldAccess;
                params["fieldDefaults"] = obj.fieldDefaults;
                params["additionalLinks"] = obj.additionalLinks;
                params["topLinkObject"] = obj.topLinkObject;
                params["ownerObject"] = obj.ownerObject;
                getWindowManager().show_window("Window_Window"+new_item.replace(/_/g,''),new_item,params,obj.parentEntity,obj.parentEntity);
            }
        });
    },

    addGroupButton_onClick: function(event) {
        var item = this.parentEntity;
        event.cancelBubble = true;
        if (this.objRole!=null && this.objRole["canRead"]!=null && this.objRole["canAdd"]=="false") {
        	this.reportMessage("Не достаточно прав доступа !","error",true);
        	return 0;
        }
        var obj = this;
        
        new Ajax.Request("index.php",
        {
            method: "post",
            parameters: {ajax: true, object_id: item,
                         hook: "childClass"},
            onSuccess: function(transport) {
                var response = transport.responseText;
                var item_array = item.split("_");
                item_array = item.split("_");
                item_array.pop();
                item_array.shift();
                if (response!="")
                    var new_item = response+"_"+item_array.join("_")+"_";
                else
                    new_item = obj.className+"_"+obj.module_id+"_";
                var params = new Object;
                params["hook"] = "afterInit";
                params["item"] = item;
                params["isGroup"] = 1;                
                params["fieldAccess"] = obj.fieldAccess;
                params["fieldDefaults"] = obj.fieldDefaults;
                params["additionalLinks"] = obj.additionalLinks;
                params["topLinkObject"] = obj.topLinkObject;
                params["ownerObject"] = obj.ownerObject;
                getWindowManager().show_window("Window_Window"+new_item.replace(/_/g,''),new_item,params,obj.parentEntity,obj.parentEntity);
            }
        });
    },
    
    copyButton_onClick: function(event) {
    	event.cancelBubble = true;
        if (this.objRole!=null && this.objRole["canRead"]!=null && this.objRole["canAddCopy"]=="false") {
        	this.reportMessage("Не достаточно прав доступа !","error",true);
        	return 0;
        }
        if (this.currentControl==null)
            return 0;
        var row_num = this.currentControl.node.parentNode.getAttribute("row");
        var item = this.getItem(row_num,0).getValue();
        var item_array = item.split("_");
        var obj = this;
        item_array = item.split("_");
        item_array.pop();
        var new_item = item_array.join("_")+"_";
        params = new Object;
		params["hook"] = "2";
		params["item"] = item;       
        params["fieldAccess"] = this.fieldAccess;
        params["fieldDefaults"] = this.fieldDefaults;
        params["additionalLinks"] = obj.additionalLinks;
        params["topLinkObject"] = obj.topLinkObject;
        params["ownerObject"] = obj.ownerObject;
        getWindowManager().show_window("Window_Window"+new_item.replace(/_/g,''),new_item,params,this.parentEntity,this.parentEntity);        
    },

    insertButton_onClick: function(event) {
    	event.cancelBubble = true;
        if (this.currentControl==null)
            return 0;
        var row_num = this.currentControl.node.parentNode.getAttribute("row");
        this.raiseEvent("CONTROL_DOUBLE_CLICKED",$Arr("object_id="+this.getItem(row_num,0).object_id+",parent_object_id="+this.object_id));
    },
    
    moveUpButton_onClick: function(event) {
    	event.cancelBubble = true;
        if (this.currentControl==null)
            return 0;
        if (this.currentControl.node.parentNode.getAttribute("row")<2)
            return 0;
        var item = this.getItem(this.currentControl.node.parentNode.getAttribute("row"),0);
        if (item==0)
            return 0;        
        var current_target = item.getValue();
        var sible = this.getItem(parseInt(this.currentControl.node.parentNode.getAttribute("row"))-1,0);
        if (sible==0)
            return 0;
        if (this.objRole!=null && this.objRole["canRead"]!=null && this.objRole["canEdit"]=="false") {
        	this.reportMessage("Не достаточно прав доступа !","error",true);
        	return 0;
        }
        var sible_target = sible.getValue();
		var args = new Object;
		args["sible_target"] = sible_target;
		
        var obj = this;
        new Ajax.Request("index.php", {
            method:"post",
            parameters: {ajax: true, object_id: current_target,hook: 'move', arguments: Object.toJSON(args)},
            onSuccess: function(transport)
            {                            
                var response = trim(transport.responseText.replace("\n",""));
                var params = new Array;
                params['object_id'] = current_target;
                params['parent'] = response;
                params['old_name'] = current_target.split("_").pop();
                params['old_parent'] = '';
                params['name'] = current_target.split("_").pop();
                params["action"] = "move";
                params['sible_id'] = sible_target;
                obj.raiseEvent("ENTITY_CHANGED",params,true);
            }
        });          
    },
    
    moveDownButton_onClick: function(event) {
    	event.cancelBubble = true;
        if (this.currentControl==null)
            return 0;
        var item = this.getItem(this.currentControl.node.parentNode.getAttribute("row"),0);        
        if (item==0)
            return 0;        
        var current_target = item.getValue();
        var sible = this.getItem(parseInt(this.currentControl.node.parentNode.getAttribute("row"))+1,0);
        if (sible==0)
            return 0;
        if (this.objRole!=null && this.objRole["canRead"]!=null && this.objRole["canEdit"]=="false") {
        	this.reportMessage("Не достаточно прав доступа !","error",true);
        	return 0;
        }
        var sible_target = sible.getValue();
		var args = new Object;
		args["sible_target"] = sible_target;
        var obj = this;
        new Ajax.Request("index.php", {
            method:"post",
            parameters: {ajax: true, object_id: current_target,hook: 'move', arguments: Object.toJSON(args)},
            onSuccess: function(transport)
            {                            
                var response = trim(transport.responseText.replace("\n",""));
                var params = new Array;
                params['object_id'] = current_target;
                params['parent'] = response;
                params['old_name'] = current_target.split("_").pop();
                params['old_parent'] = '';
                params['name'] = current_target.split("_").pop();
                params["action"] = "move";
                var sible = obj.getItem(parseInt(obj.currentControl.node.parentNode.getAttribute("row"))+2,0);
                if (sible!=0)                    
                    params['sible_id'] = sible.getValue();
                else
                    params['sible_id'] = '';
                obj.raiseEvent("ENTITY_CHANGED",params,true);
            }
        });          
    },

    deleteButton_onClick: function(event) {
    	event.cancelBubble = true;
        if (this.objRole!=null && this.objRole["canRead"]!=null && this.objRole["canDelete"]=="false") {
        	this.reportMessage("Не достаточно прав доступа !","error",true);
        	return 0;
        }
        var delete_checks = this.getColValues(this.rows,1,true);
        if (!confirm("Вы действительно хотите удалить выбранные элементы?"))
            return 0;
        var ids = this.getColValues(this.rows,0,true);
        var deleted_entities = new Array;
        var vl=null;
        for (vl in delete_checks) {
            if (delete_checks[vl]==1) {
                deleted_entities[deleted_entities.length] = ids[vl];
            }
        }
        if (deleted_entities.length==0) {
        	var ent=null;
            if (this.currentControl!=null & this.currentControl!=0)
                ent = this.getItem(this.currentControl.node.parentNode.getAttribute("row"),0);
            if (ent!=null && ent!=0)
                deleted_entities[0] = this.getItem(this.currentControl.node.parentNode.getAttribute("row"),0).getValue();
        }
        if (deleted_entities.length>0)  {
			var args = new Object;
			args['deleted_entities'] = deleted_entities.join(',');            
            var obj = this;            
            new Ajax.Request("index.php", {
                method:"post",
                parameters: {ajax: true, object_id: obj.object_id,hook: 'removelist', arguments: Object.toJSON(args)},
                onSuccess: function(transport) {                            
                    var response = trim(transport.responseText);
                    if (response!="") {
                        var rsp = response.evalJSON();
                        if (rsp) {
                            var errors = "";
                            errors = rsp["error_text"];
                            var removed_objects = rsp["removed_objects"];
                            if (errors!=null && errors!="") {
                                obj.reportMessage(errors,"error",true);
                            }
                            if (removed_objects!="") {
                                obj.raiseEvent("ENTITY_DELETED",$Arr("object_id="+removed_objects+",action=delete"));
                            }
                         } else
                             alert(response);
                    }
                }
            });
        }        
    },
        
    onLoad: function() {
    	if (this.adapterId=="") {
    		if ($I(this.node.id+"_moveUpButton")!=0)
    			$I(this.node.id+"_moveUpButton").style.display='none';
    		if ($I(this.node.id+"_moveDownButton")!=0)
    			$I(this.node.id+"_moveDownButton").style.display='none';
    	}
    }
});