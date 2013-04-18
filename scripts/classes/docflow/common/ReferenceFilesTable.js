var ReferenceFilesTable = Class.create(DocFlowReferenceTable, {
	
	updateFilesButton_onClick: function(event) {
		if (confirm("Будет выполнено обновление состояния файлов. Это может занять некоторое время.")) {
			var obj = this;
	        new Ajax.Request("index.php", {
	            method:"post",
	            parameters: {ajax: true, object_id: "ReferenceFiles_"+this.module_id+"_List", hook: '4'},
	            onSuccess: function(transport)
	            {                            		
	            	obj.reportMessage("Обновление состояния файлов выполнено.");
	            	obj.raiseEvent("UPDATE_FILES",$Arr("object_id="+obj.object_id),true);
	            }
	        });
		}
	},

	UPDATE_FILES_processEvent: function(params) {
		if (params["object_id"]==this.object_id) {
			this.rebuild();
		}
	}
});