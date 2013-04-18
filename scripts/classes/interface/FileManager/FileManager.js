var FileManager = Class.create(Entity, {
	
	ON_WINDOW_RESIZE_processEvent: function(params) {
		if (this.viewForm=='list' && params["object_id"]+"List" == this.object_id)
			this.buildTable();
	},
	
	openPath: function(event) {
		var file=eventTarget(event);
        ev = event;
		if (event.ctrlKey)
        	event.ctrlKey = false;
		if (event.shiftKey)
        	event.shfitKey = false;
        this.selectFile(ev);
        if (file.getAttribute("type")!="file") {
            this.prevPath = this.path;
            var arr = file.getAttribute("path").split("/");
            if (arr[arr.length-1]=="..") {
                    if (this.path==this.rootPath)
                            return 0;
                    arr.pop();
                    arr.pop();
                    this.path = arr.join("/");
            } else
                    this.path = file.getAttribute("path");
			$O(this.object_id.replace("List","")).path = this.path;
            this.selectedFiles = new Array;
            this.buildTable();
        } else {
			window.open("root"+file.getAttribute("path"));
		}
	},
	
	moveUpButton_onClick: function(event) {
            var obj = $O(this.object_id+"List","");
            if (obj.path==obj.rootPath)
                    return 0;
            var arr = obj.path.split("/");
            arr.pop();
            this.path = arr.join("/");
            obj.path = arr.join("/");
            obj.selectedFiles = new Array;
            obj.buildTable();
	},
	
	unselectAllFiles: function(event) {
		if (event!=null && !event.ctrlKey && !event.shiftKey) {			
			var el = eventTarget(event);
      		this.tbl = el.parentNode.parentNode.parentNode;
      		var i=0;
       		for (i in this.selectedFiles) {
           		if (typeof this.selectedFiles[i] == "function")
               		continue;
           		var el1 = this.selectedFiles[i][0];
           		var el2 = this.selectedFiles[i][1];           		
           		el1.setAttribute("selected","false");
           		el2.setAttribute("selected","false");
				if (el1.style.backgroundColor != null) {
               		el1.style.cursor = "pointer";
            		el2.style.cursor = "pointer";
					el1.style.backgroundColor = '';
					el2.style.backgroundColor = '';
				} else {
					el1.style = "cursor:pointer";
					el2.style = "cursor:pointer";
				}
			}
      		this.selectedFiles = new Array;
			if (window.event!=null)
       			event = event || window.event;
    		event.preventDefault ? event.preventDefault() : (event.returnValue=false);
		}
	},
	
	selectAll: function() {
		var i=0;
		for (i=0;i<this.filesCount;i++) {
                var el1 = getElementById(this.tbl,"fi"+i);
                var el2 = getElementById(this.tbl,"fs"+i);
				el1.setAttribute("selected","true");
				el2.setAttribute("selected","true");
				if (el1.style.backgroundColor!=null) {
					el1.style.backgroundColor = '#FFFFAA';el1.style.cursor = 'pointer';
					el2.style.backgroundColor = '#FFFFAA';el1.style.cursor = 'pointer';
				} else {
					el1.style = 'background-color:#FFFFAA;cursor:pointer';
					el2.style = 'background-color:#FFFFAA;cursor:pointer';
				}
                var arr = new Array;
                arr[0] = el1;
                arr[1] = el2;
                this.selectedFiles[i] = arr;
            }
	},
	
	selectFile: function(event) {
		var el = eventTarget(event);
            var nm = el.getAttribute("num");
            var num = parseInt(el.getAttribute("num"));
            if (event.shiftKey) {
                var newFiles = new Array;
                var arr = new Array;
                arr[0] = getElementById(this.tbl,"fi"+num);
                arr[1] = getElementById(this.tbl,"fs"+num);
                this.selectedFiles[num] = arr;
                newFiles[num] = arr;
                var nm1 = getNextArrayIndex(this.selectedFiles,nm);
                if (nm1!=null) {
                    nm1 = parseInt(nm1);
                    var i=0;
                    for (i=num;i<=nm1;i++) {
                        arr[0] = getElementById(obj.tbl,"fi"+i);
                        arr[1] = getElementById(obj.tbl,"fs"+i);
                        newFiles[i] = arr;
                    }
                } else {
                    nm1 = getPrevArrayIndex(this.selectedFiles,nm);
                    if (nm1!=null) {
                        nm1 = parseInt(nm1);
                        for (i=nm1;i<=num;i++) {
                            arr[0] = getElementById(obj.tbl,"fi"+i);
                            arr[1] = getElementById(obj.tbl,"fs"+i);
                            newFiles[i] = arr;
                        }
                    }
                }
				this.unselectAllFiles(event);
				var i=0;
				for (i in newFiles) {
                    if (typeof newFiles[i] == "function")
                            continue;
                    var el1 = getElementById(this.tbl,"fi"+i);
                    var el2 = getElementById(this.tbl,"fs"+i);
                    el1.setAttribute("selected","true");
                    el2.setAttribute("selected","true");
					if (el1.style.backgroundColor!=null) {
						el1.style.backgroundColor = '#FFFFAA';el1.style.cursor = 'pointer';
						el2.style.backgroundColor = '#FFFFAA';el2.style.cursor = 'pointer';
					} else {
						el1.style = 'background-color:#FFFFAA;cursor:pointer';
						el2.style = 'background-color:#FFFFAA;cursor:pointer';
					}
					var arr = new Array;
                    arr[0] = el1;
                    arr[1] = el2;
                    this.selectedFiles[i] = arr;
                }
                return 0;
			}
            if (!event.ctrlKey || el.getAttribute("title")==".." ) {
                this.unselectAllFiles(event);
            }
            this.tbl = el.parentNode.parentNode.parentNode;
            var el1 = getElementById(this.tbl,"fi"+num);
            var el2 = getElementById(this.tbl,"fs"+num);
            if (el1.getAttribute("selected")=="false" || el1.getAttribute("selected")==null) {
                el1.setAttribute("selected","true");
                el2.setAttribute("selected","true");
				if (el1.style.backgroundColor!=null) {
					el1.style.backgroundColor = '#FFFFAA';el1.style.cursor = 'pointer';
					el2.style.backgroundColor = '#FFFFAA';el2.style.cursor = 'pointer';
				} else {
					el1.style = 'background-color:#FFFFAA;cursor:pointer';
					el2.style = 'background-color:#FFFFAA;cursor:pointer';
				}
				var arr = new Array;
                arr[0] = el1;
                arr[1] = el2;
                this.selectedFiles[num] = arr;
            } else {
                el1.setAttribute("selected","false");
                el2.setAttribute("selected","false");
				if (el1.style.backgroundColor!=null) {
					el1.style.backgroundColor = '';
					el2.style.backgroundColor = '';
					el1.style.cursor = 'pointer';
					el2.style.cursor = 'pointer';
				} else {
					el1.style = 'cursor:pointer';
					el2.style = 'cursor:pointer';
				}
				delete this.selectedFiles[num];
            }
            if (event.stopPropagation!=null)
				event.stopPropagation();
			else
				event.cancelBubble = true;
				event = event || window.event;
	    event.preventDefault ? event.preventDefault() : (event.returnValue=false);
	},
	
	fileManagerContextMenu: function(event) {
        var elem = eventTarget(event);
        var params = new Object;
        params["useCase"] = this.useCase;
		this.show_context_menu("FileManagerContextMenu_fm",cursorPos(event).x-10,cursorPos(event).y-10,elem.id,params);
        if (event.preventDefault)
            event.preventDefault();
         else
            event.returnValue= false;
        return false;
	},
	
	buildTable: function(gotoDir,objId) {
		this.selectedFiles = new Array;
		var obj=null;
		if (objId!=null)
			obj = $O(objId,'');
		else
			obj = this;
		if (obj.node==null)
			obj.node = $I(obj.object_id);
		if (window.innerWidth==null)
				obj.colCount = parseInt(document.body.clientWidth/150);
			else
				obj.colCount = parseInt(window.innerWidth/150);
			var args = new Object;
            args["dir"] = obj.path;
            args["useCase"] = obj.useCase;
		
            var loading_img = document.createElement("img");			
            loading_img.src = obj.skinPath+"images/Tree/loading2.gif";
            loading_img.style.zIndex = "100";
            loading_img.style.position = "absolute";
			if (window.innerWidth==null) {
            	loading_img.style.top=(document.body.clientHeight/2-33);
				loading_img.style.left=(document.body.clientWidth/2-33);
			} else {
				loading_img.style.top=(window.innerHeight/2-33);
				loading_img.style.left=(window.innerWidth/2-33);
			}
            obj.node.appendChild(loading_img);
            new Ajax.Request("index.php",
            {
                method: "post",
                parameters: {ajax: true, object_id: obj.object_id,
                                         hook: '2', arguments: Object.toJSON(args)},
                onSuccess: function(transport) {
                    var response = transport.responseText;                    
                    if (response != "") {
                        var files = response.evalJSON();
                        if (files=="") {
                        	obj.node.removeChild(loading_img);
                            return 0;
                        }
                        document.body.innerHTML = "<body bgcolor='#FFFFFF'/>";
						var tbl = document.createElement("table");
                        tbl.setAttribute("cellpadding",0);
                        tbl.setAttribute("bgcolor","#FFFFFF");
                        tbl.setAttribute("cellspacing",2);
                        tbl.style.height= '100%';
						tbl.style.width = '100%';
                        var tr = document.createElement("tr");
                        tr.setAttribute("valign","top");
						tr.style.fontFamily = 'Arial';
						tr.style.fontSize = '11px';
                        var i1=0;var i2=0;var i3=0;var i=0;
						for (i in files) {
                            if (typeof files[i] == "function")
                                continue;
                            if (i1>obj.colCount) {
								for (i3=i1;i3<obj.colCount;i3++) {
									var td = document.createElement("td");
									td.setAttribute("object",obj.object_id);
									td.innerHTML = "&nbsp;";
									td.setAttribute("onclick","$O('"+this.object_id+"','"+this.instance_id+"').unselectAllFiles(event)");
									td.setAttribute("width",150);
									tr.appendChild(td);
								}
								tbl.appendChild(tr);
                                tr = document.createElement("tr");
                                tr.setAttribute("valign","top");
								tr.style.fontFamily = 'Arial';
								tr.style.fontSize = '11px';
                                i1=0;
                            }
                            td = document.createElement("td");
                            td.setAttribute("align","center");
                            td.setAttribute("object",obj.object_id);
                            td.setAttribute("id",obj.object_id+"_td");
                            var img = document.createElement("img");
                            img.src = files[i]["img"];
                            img.setAttribute("path",files[i]['path']);
                            img.setAttribute("type",files[i]['type']);
                            img.setAttribute("title",files[i]["title"]);
                            if (files[i]["share"]!=null) {
                                img.setAttribute("share",files[i]["share"]);
                                img.setAttribute("shareTypes",files[i]["sharetypes"]);
                                img.src = "utils/controller/shareimg.php?path="+files[i]["img"]+"&sharetypes="+files[i]["sharetypes"];
                            }
							if (files[i]["title"]==gotoDir)
                                new_dir_node = img;
                            img.style.cursor = 'pointer';
                            img.setAttribute("object",obj.object_id);
                            img.setAttribute("ondblclick","$O('"+this.object_id+"','"+this.instance_id+"').openPath(event)");
                            img.setAttribute("onclick","$O('"+this.object_id+"','"+this.instance_id+"').selectFile(event)");
                            img.setAttribute("num",i2);
                            img.id = "fi"+i2;
                            var arr = new Array;
                            if (obj.selectedFiles[i2]!=null) {
                                arr[0] = img;
                                img.setAttribute("selected","true");
                                img.setAttribute("style","background-color:#FFFFAA;cursor:pointer");
                            }
                            td.appendChild(img);
                            td.setAttribute("onclick","$O('"+this.object_id+"','"+this.instance_id+"').unselectAllFiles(event)");
                            td.setAttribute("oncontextmenu","$O('"+this.object_id+"','"+this.instance_id+"').fileManagerContextMenu(event)");
                            var br = document.createElement("br");
                            td.appendChild(br);
                            var span = document.createElement("span");
                            span.setAttribute("object",obj.object_id);
                            span.setAttribute("style","font-face:Arial;font-size:11px");
                            span.setAttribute('path',files[i]['path']);
                            span.setAttribute('type',files[i]['type']);
							span.style.cursor = 'pointer';
                            span.setAttribute("num",i2);
                            span.setAttribute("ondblclick","$O('"+this.object_id+"','"+this.instance_id+"').openPath(event)");
                            span.setAttribute("onclick","$O('"+this.object_id+"','"+this.instance_id+"').selectFile(event)");
                            if (files[i]["share"]!=null) {
                                span.setAttribute("share",files[i]["share"]);
                                span.setAttribute("shareTypes",files[i]["sharetypes"]);
                            }
                            span.id = "fs"+i2;
                            if (obj.selectedFiles[i2]!=null) {
                                arr[1] = span;
                                span.setAttribute("selected","true");
                                span.setAttribute("style","background-color:#FFFFAA;cursor:pointer");
                                obj.selectedFiles[i2] = arr;
                            }
                            if (files[i]['title'].length>15)
                                span.innerHTML = files[i]["title"].substring(0,12)+"...";
                            else
                                span.innerHTML = files[i]["title"];
                            td.setAttribute("width",150);
							td.style.fontFamily = 'Arial';
							td.style.fontSize = '11px';
							td.style.width = '150';
                            td.appendChild(span);
                            tr.appendChild(td);							
							i1++;i2++;
                        }
						for (i3=i1;i3<=obj.colCount;i3++) {
                        	td = document.createElement("td");
							td.innerHTML = "&nbsp;";
							td.setAttribute("object",obj.object_id);
							td.setAttribute("onclick","$O('"+this.object_id+"','"+this.instance_id+"').unselectAllFiles(event)");
							td.setAttribute("width",150);
							tr.appendChild(td);
						}
						tbl.appendChild(tr);
						tr = document.createElement("tr");
						td = document.createElement("td");
						td.innerHTML = "&nbsp;";
						td.style.width = '100%';
						td.style.height = '100%';
						td.setAttribute("object",obj.object_id);
						td.setAttribute("onclick","$O('"+this.object_id+"','"+this.instance_id+"').unselectAllFiles(event)");
						td.setAttribute("colspan",obj.colCount+1);
						tr.appendChild(td);
						tbl.appendChild(tr);
						document.body.appendChild(tbl);
						obj.tbl = tbl;
                        obj.filesCount = i2;
                        $I(obj.node.id.replace("List","")+"_bottomLine").innerHTML = obj.path.replace(/\/\//g,'/');
						document.body.innerHTML = document.body.innerHTML;
						if (gotoDir!=null && new_dir_node!=null) {
                            new_dir_node.document.parent.scrollTo(new_dir_node.offsetLeft,new_dir_node.offsetTop);
                        }
                        obj.node.removeChild(loading_img);
                    }
                }
            });
	},
	
    button_onMouseDown: function(event) {
    	var src="";var elem="";
		if (eventTarget(event)!=null) {
        	src = eventTarget(event).src;
			elem = eventTarget(event);
		} else {
			src = event.srcElement.src;
			elem = event.srcElement;
		}
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
        elem.src = src;
    },

    button_onMouseUp: function(event) {
    	var src="";var elem="";
		if (eventTarget(event)!=null) {
			src = eventTarget(event).src;
			elem = eventTarget(event);
		} else {
			src = event.srcElement.src;
			elem = event.srcElement;
		}
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
        elem.src = src;
		if (navigator.appName=="Microsoft Internet Explorer")
			eval(elem["onclick"]);
	},
							   
    button_onMouseOver: function(event) {
    	var src="";var elem="";
		if (eventTarget(event)!=null) {
			src = eventTarget(event).src;
			elem = eventTarget(event);
		} else {
			src = event.srcElement.src;
			elem = event.srcElement;
		}
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
        elem.src = src;
    },

    button_onMouseOut: function(event) {
    	var src="";var elem="";
		if (eventTarget(event)!=null) {
			src = eventTarget(event).src;
			elem = eventTarget(event);
		} else {
			src = event.srcElement.src;
			elem = event.srcElement;
		}
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
        elem.src = src;
    },
        
    addButton_onClick: function(event) {
		if (this.role["fmCanCreateFolder"]=="false")
			return 0;
        var obj = $O(this.object_id+"List","");
        var args = new Object;
        args["useCase"] = obj.useCase;
        args["path"] = obj.path;
        var dir = prompt("Введите имя папки","");
        if (!dir)
                return 0;
        args["dir"] = dir;
        var objthis = this;
        new Ajax.Request("index.php",
        {
            method: "post",
            parameters: {ajax: true, object_id: this.object_id,
                         hook: '4', arguments: Object.toJSON(args)},
            onSuccess: function(transport) {
                var response = transport.responseText;
                if (response!="") {
                    var error = response.evalJSON();
                    objthis.reportMessage(error["error"],"error",true);
                } else {
					if (navigator.appName!="Microsoft Internet Explorer")
                    	obj.unselectAllFiles(event);
					else
						obj.selectedFiles = new Array;
                    obj.buildTable(dir);
                };
            }
        });
    },

    renameButton_onClick: function(event) {
		if (this.role["fmCanRename"]=="false")
			return 0;
        var obj = $O(this.object_id+"List","");
        var i=0;
        var idx=0;
        for (o in obj.selectedFiles) {
            if (typeof obj.selectedFiles[o] == "function")
                continue;
            idx = o;
            i++;
        }
        if (i>1 || i==0)
            return 0;
        var oldDir = obj.selectedFiles[idx][0].getAttribute("title");
        var args = new Object;
        args["useCase"] = obj.useCase;
        args["path"] = obj.path;
        args["oldDir"] = oldDir;
        var dir = prompt("Введите имя папки",oldDir);
        if (!dir)
            return 0;
        if (dir==oldDir)
            return 0;
        args["dir"] = dir;
        new Ajax.Request("index.php",
            {
                method: "post",
                parameters: {ajax: true, object_id: this.object_id,
                             hook: '5', arguments: Object.toJSON(args)},
                onSuccess: function(transport) {
                    var response = transport.responseText;
                    if (response!="" && response!=parseInt(response)) {
                        var error = response.evalJSON();
                        obj.reportMessage(error["error"],"error",true);
                    } else {
						if (navigator.appName!="Microsoft Internet Explorer")
							obj.unselectAllFiles(event);
						else
							obj.selectedFiles = new Array;
						obj.buildTable();
                    };
                }
            });
	},
	
	copyButton_onClick: function(event) {
		if (this.role["fmCanCopyMove"]=="false")
			return 0;
            var obj = $O(this.object_id+"List","");
            if (obj.selectedFiles.length>0) {
                obj.copyFiles = obj.selectedFiles;
                obj.cutFiles = new Array;
            }
	},
	
	cutButton_onClick: function(event) {
		if (this.role["fmCanCopyMove"]=="false")
			return 0;
            var obj = $O(this.object_id+"List","");
            if (obj.selectedFiles.length>0) {
                obj.cutFiles = obj.selectedFiles;
                obj.copyFiles = new Array;
            }
	},
	
    pasteButton_onClick: function(event) {
		if (this.role["fmCanCopyMove"]=="false")
			return 0;
            var obj = $O(this.object_id+"List","");
            var args = new Object;
            var files=null;
            if (obj.copyFiles.length>0) {
                args["operation"] = "copy";
                files = obj.copyFiles;
            }
            if (obj.cutFiles.length>0) {
                args["operation"] = "cut";
                files = obj.cutFiles;
            }
            if (files==null)
                return 0;
            args["path"] = this.path;
            var arr = new Object;
            var i = 0;
            var o = null;
            for (o in files) {
                if (typeof files[o] == "function")
                        continue;
                arr[i] = new Object;
                arr[i]["path"] = files[o][0].getAttribute("path");
                i++;
            }
            args["files"] = arr;
            args["useCase"] = obj.useCase;
            args["path"] = obj.path;
            new Ajax.Request("index.php",
            {
                method: "post",
                parameters: {ajax: true, object_id: this.object_id,
                             hook: '6', arguments: Object.toJSON(args)},
                onSuccess: function(transport) {
                    var response = transport.responseText;
                    if (response!="" && response!=parseInt(response)) {
                        var error = response.evalJSON();
                        obj.reportMessage(error["error"],"error",true);
                    } else {
                        obj.copyFiles = new Array;
                        obj.cutFiles = new Array;
						if (navigator.appName!="Microsoft Internet Explorer")
							obj.unselectAllFiles(event);
						else
							obj.selectedFiles = new Array;
						obj.buildTable();
                    };
                }
            });
	},

    deleteButton_onClick: function(event) {
		if (this.role["fmCanDelete"]=="false")
			return 0;
        var obj = $O(this.object_id+"List","");
        var args = new Object;
        var arr = new Object;
        if (obj.selectedFiles.length==0)
            return 0;
        if (!confirm("Вы действительно хотите удалить выделенные файлы и каталоги?"))
            return 0;
        var o=null;
        for (o in obj.selectedFiles) {
            if (typeof obj.selectedFiles[o] == "function")
                    continue;
            arr[i] = new Object;
            arr[i]["path"] = obj.selectedFiles[o][0].getAttribute("path");
            i++;
        }
        args["files"] = arr;
        args["useCase"] = obj.useCase;
        args["path"] = obj.path;

        new Ajax.Request("index.php",
            {
                method: "post",
                parameters: {ajax: true, object_id: this.object_id,
                             hook: '7', arguments: Object.toJSON(args)},
                onSuccess: function(transport) {
                    var response = transport.responseText;
                    if (response!="" && response!=parseInt(response)) {
                        var error = response.evalJSON();
                        obj.reportMessage(error["error"],"error",true);
                    } else {
                        obj.copyFiles = new Array;
                        obj.cutFiles = new Array;
						if (navigator.appName!="Microsoft Internet Explorer")
							obj.unselectAllFiles(event);
						else
							obj.selectedFiles = new Array;
						obj.buildTable();
                    };
                }
            });
	},
	
	refreshButton_onClick: function(event) {
		$O(this.object_id+"List","").buildTable();
	},

	selectAllButton_onClick: function(event) {
		var obj = $O(this.object_id+"List","");
		obj.selectAll();
	},
	
	propertiesButton_onClick: function(event) {
		if (this.role["fmCanSetProperties"]=="false")
			return 0;
        var params = new Object;
        var obj = $O(this.object_id+"List","");
        var i=0;
        params["hook"] = "2";
        var arr = new Object;
        var o=null;
        for (o in obj.selectedFiles) {
            if (typeof obj.selectedFiles[o] == "function")
                    continue;
            arr[i] = new Object;
            arr[i]["title"] = obj.selectedFiles[o][0].getAttribute("title");
            arr2 = new Array;
            arr2 = obj.selectedFiles[o][0].getAttribute("path").split("/");
            arr2.pop();
            params["path"] = arr2.join("/");
            if (obj.selectedFiles[o][0].getAttribute("share")!=null) {
                arr[i]["share"] = obj.selectedFiles[o][0].getAttribute("share");
                arr[i]["sharetypes"] = obj.selectedFiles[o][0].getAttribute("sharetypes");
            }
            i++;
        }
        if (i>0) {
            params["files"] = arr;
			if (i==1)
				params["object_text"] = "Свойства "+arr[0].title;
			else
				params["object_text"] = "Свойства группы файлов и каталогов";
            var randomId = Math.round(Math.random()*10000);
            getWindowManager().show_window("Window_FileProperties"+randomId,"FileProperties_"+this.module_id+"_fp"+randomId,params,obj.object_id,obj.node.id);
        }
	},
        
    selectButton_onClick: function(event) {
        var obj = $O(this.object_id+"List","");
        if (obj.useCase!="selectPath" && obj.useCase!="selectFile")
            return 0;
        var o=null;
        for (o in obj.selectedFiles) {
            if (typeof obj.selectedFiles[o] == "function")
                    continue;
            if (obj.selectedFiles[o][0]!=null) {                	
                if (obj.useCase=="selectPath" && obj.selectedFiles[o][0].getAttribute("type")!="directory") {
                	return 0;
                }
                if (obj.useCase=="selectFile" && obj.selectedFiles[o][0].getAttribute("type")=="directory") {
                	return 0;
                }
                this.opener_object.setValue(obj.selectedFiles[o][0].getAttribute("path").replace(/\/\//g,"/"));                    
                getWindowManager().remove_window(this.win.id);
                break;
            }
        }
    },

	uploadDialogCompleteHandler: function(selNum, queuedNum, allNum) {
		var entity = $O(this.customSettings["parent_object_id"],'');
		var fileManager = $O(entity.parent_object_id,'');
		if (fileManager.role["fmCanUpload"]=="false")
			return 0;
		var params = new Object;
		if (selNum==0)
			return 0;
		params['object_id'] = entity.object_id;			
		params['hook'] = '3';
		var args = new Object;
		args['path'] = fileManager.path;
		args['user'] = fileManager.user;
		params['arguments'] = Object.toJSON(args);
		var obj = this;
		if (fileManager.useCase=="fileUpload") {
			new Ajax.Request("index.php",
				{
						 method: "post",
						 parameters: {ajax: true, object_id: fileManager.object_id,
						 hook: '9', arguments: Object.toJSON(args)},
						 onSuccess: function(transport) {
							 var response = transport.responseText;
							 if (response!="" && response!=parseInt(response)) {
								 var error = response.evalJSON();
								 obj.reportMessage(error["error"],"error",true);
							 } else {
								 args['ftpUser'] = fileManager.ftpUser;
								 params['arguments'] = Object.toJSON(args);
								 obj.setPostParams(params);
								 obj.startUpload();
							 };
						 }
			});
		} else {
			 obj.setPostParams(params);
			 obj.startUpload();
		}
	},

	uploadStartHandler: function(file) {
		var entity = $O(this.customSettings["parent_object_id"],'');
		var fileManager = $O(entity.parent_object_id,'');
		if (fileManager.role["fmCanUpload"]=="false")
			return 0;
		var progressBar = $I(fileManager.node.id+"_progressBar");
		$I(fileManager.node.id+"_progressBarContainer").style.backgroundColor="#7777FF";
		progressBar.style.display = '';
		progressBar.innerHTML = '<strong>0%</strong>';
		progressBar.style.width = '1px';
	},
	   
	uploadSuccessHandler: function(file, data, response) {
	
	},

	uploadProgressHandler: function(file, completedBytes, allBytes) {
		var entity = $O(this.customSettings["parent_object_id"],'');
		var fileManager = $O(entity.parent_object_id,'');
		var progressBar = $I(fileManager.node.id+"_progressBar");
		var percents = Math.round(completedBytes/allBytes*100);
		if (percents<100) {
			progressBar.style.width = $I(fileManager.node.id+"_progressBarContainer").offsetWidth*(percents/100)+'px';
			progressBar.innerHTML = '<strong>'+Math.round(completedBytes/1024/1024)+'Мб из '+Math.round(allBytes/1024/1024)+'Мб.</strong>';
		} else {
			progressBar.innerHTML = '<strong>Размещение файла на сервере</strong>';
		}
	},
		   
	uploadCompleteHandler: function(file) {
		var entity = $O(this.customSettings["parent_object_id"],'');
		var fileManager = $O(entity.parent_object_id,'');
		if (fileManager.role["fmCanUpload"]=="false")
			return 0;
		var progressBar = $I(fileManager.node.id+"_progressBar");
		progressBar.style.display = 'none';
		progressBar.innerHTML = '<strong>0%</strong>';
		progressBar.style.width = '1px';
		$I(fileManager.node.id+"_progressBarContainer").style.backgroundColor="";
		var entity = $O(this.customSettings["parent_object_id"],'');
		var fileManager = $O(entity.parent_object_id+"List",'');
		fileManager.buildTable();
	},

	uploadErrorHandler: function(file, error, message) {
		alert("Возникла ошибка при передаче файла ! -"+error);

	},

	uploadCancelHandler: function() {
		
	},

	onLoad: function() {
		if (this.useCase=="fileUpload") {
			if ($I(this.node.id+"_propertiesButton")!=0)
				$I(this.node.id+"_propertiesButton").style.display = 'none';
			if ($I(this.node.id+"_cutButton")!=0)
				$I(this.node.id+"_cutButton").style.display = 'none';
			if ($I(this.node.id+"_copyButton")!=0)
				$I(this.node.id+"_copyButton").style.display = 'none';
			if ($I(this.node.id+"_pasteButton")!=0)
				$I(this.node.id+"_pasteButton").style.display = 'none';
		}
		if (this.viewForm!="list" && $I(this.node.id+"_addButton")!=0) {				
			if (this.role["fmCanCreateFolder"]=="false")
				$I(this.node.id+"_addButton").style.display = 'none';
			if (this.role["fmCanRename"]=="false")
				$I(this.node.id+"_renameButton").style.display = 'none';
			if (this.role["fmCanCopyMove"]=="false") {
				$I(this.node.id+"_copyButton").style.display = 'none';
				$I(this.node.id+"_cutButton").style.display = 'none';
				$I(this.node.id+"_pasteButton").style.display = 'none';
			}				
			if (this.role["fmCanDelete"]=="false")
				$I(this.node.id+"_deleteButton").style.display = 'none';
			if (this.role["fmCanSetProperties"]=="false")
				$I(this.node.id+"_propertiesButton").style.display = 'none';
		}
	},
		
	helpButton_onClick: function(event) {
		getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"guide5.3","HTMLBook_"+this.module_id+"_controller_5.3",null,this.opener_item.getAttribute("object"),this.opener_item.id);
	}
});