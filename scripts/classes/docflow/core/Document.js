var Document = Class.create(Entity, {

	TAB_CHANGED_processEvent: function($super,params) {
		if (params["tabset_id"]==this.tabsetName) {
			if (params["tab"]=="files") {
				if (!this.filesTabLoaded) {
					var tbl = $O(this.filesTableId,'');
					tbl.rebuild();
					this.filesTabLoaded = true;
				}
			}
			if (params["tab"]=="notes") {
				if (!this.notesTabLoaded) {
					var tbl = $O(this.notesTableId,'');
					tbl.rebuild();
					this.notesTabLoaded = true;
				}
			}
		}
	},	
	
	afterSave: function($super) {
		var res=$super();
		if (res==0) {
			if (this.old_registered!=null)
				$I(this.node.id+"_registered").value = this.old_registered;
			if (this.old_registered==0) {
				if ($I(this.node.id+"_Print")!=0)
					$I(this.node.id+"_Print").style.display = "none";
				else
					$I(this.node.id+"_Print").style.display = "none";
			}
			return 0;
		}
		if ($I(this.node.id+"_Register")!=0 && $I(this.node.id+"_Register")!=null && $I(this.node.id+"_registered")!=0 && $I(this.node.id+"_registered")!=null) {
			registered = $I(this.node.id+"_registered").value;
			if (registered==1)
				$I(this.node.id+"_Register").value = "Отмена проведения";
			else
				$I(this.node.id+"_Register").value = "Провести";
		};
	},
	
    Register_onClick: function(event) {
		var registered = $I(this.node.id+"_registered").value;
		this.old_registered = registered;
		var deleted = $I(this.node.id+"_deleted").value;
		if (deleted==1) {
			this.reportMessage("Помеченный на удаление документ провести нельзя","error",true);
			return 0;
		}
		if (registered==1) {
			if (this.role["canUnregister"]==null)
				registered = 0;			
			else if (this.role["canUnregister"]!="false" && (this.role["canUnregister"]=="true" ||  this.user=="" || (this.role["canUnregister"]=="onlyMy" && this.user==this.appUser))) {
				registered=0;
			} else {
				this.reportMessage("Не достаточно прав для выполнения операции !","error",true);
				return 0;
			}
		} else {
			if (this.role["canRegister"]==null)
				registered=1;			
			else if (this.role["canRegister"]!="false" && (this.role["canRegister"]=="true" ||  this.user=="" || (this.role["canRegister"]=="onlyMy" && this.user==this.appUser))) {
				if (this.registerConfirmation=="true") {
					var rs = prompt("Если Вы действительно хотите провести документ, введите слово ПРОВЕСТИ");
					if (rs!='ПРОВЕСТИ')
						return 0;
				}
				registered=1;
				//$I(this.node.id+"_registered").value = registered;
			} else {
				this.reportMessage("Не достаточно прав для выполнения операции !","error",true);
				return 0;					
			}
		}
		$I(this.node.id+"_registered").value = registered;
		$I(this.node.id+"_form").submit();
	},	
	
	Movements_onClick: function(event) {
        if (this.role["canViewMovements"]=="false") {
        	this.reportMessage("Не достаточно прав доступа !","error",true);
        	return 0;
        }		
        var ent = this.object_id.replace(this.module_id+"_","");
       	var args = new Object;
       	args["document"] = ent;   
    	args["hook"] = "setParams";
		getWindowManager().show_window("Window_RegistryMovementsWindow"+this.module_id.replace(/_/g,"")+ent.split("_").pop().replace(this.module_id.replace(/_/g,""),""),"RegistryMovementsWindow_"+this.module_id+"_"+ent.split("_").pop().replace(this.module_id.replace(/_/g,""),""),args,this.object_id,this.node.id,null,true);        	
	},
	
	Print_onClick: function(event) {
		if (this.win.node.getAttribute("changed")=="true") {
			this.reportMessage("Перед печатью документ необходимо сохранить !","error",true);
			return 0;
		}
		if (this.printFormsCount==1) {
			var formName = this.firstPrintForm;
			var opObject = "PrintWindow_"+this.module_id+"_"+this.object_id+"_"+formName.replace(/ /g,"");
			var params = new Array;
			getWindowManager().show_window("Window_"+opObject.replace(/_/g,""),opObject,params,this.object_id,eventTarget(event).id,true);
		} else
			$O(this.object_id,this.instance_id).show_context_menu("PrintContextMenu_"+this.module_id+"_print",cursorPos(event).x-10,cursorPos(event).y-20*this.printFormsCount,eventTarget(event).id,"$object->opener_object='"+this.object_id+"';");
	},
	
	Create_onClick: function(event) {
		$O(this.object_id,this.instance_id).show_context_menu("CreateObjectContextMenu_"+this.module_id+"_print",cursorPos(event).x-10,cursorPos(event).y-20*this.createObjectsCount,eventTarget(event).id,"$object->opener_object='"+this.object_id+"';");
	}	
});