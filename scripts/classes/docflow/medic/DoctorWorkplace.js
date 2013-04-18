var DoctorWorkplace = Class.create(Entity, {		
	CONTROL_VALUE_CHANGED_processEvent: function(params) {
		if (params["object_id"]==this.node.id+"_patient") {
			var args = new Object;
			args["patient"] = params["value"];
			var obj = this;
            new Ajax.Request("index.php",
            {
                method: "post",
                parameters: {ajax: true, object_id: 'DoctorWorkplace_'+this.module_id+'_workplace',
                             hook: '3',arguments: Object.toJSON(args)},
                onSuccess: function(transport) {
                    var response = transport.responseText.toString();
                    if (response[0]=='{') {
                    	obj.setValuesFromArray(response.evalJSON());
                    }
                }
            });			
		}
	},
	
	linkClick: function(elem) {
		if ($I(this.node.id+"_"+elem+"Div")!=0) {
			if ($I(this.node.id+"_"+elem+"Div").style.display=='none')
				$I(this.node.id+"_"+elem+"Div").style.display = '';
			else
				$I(this.node.id+"_"+elem+"Div").style.display = 'none';
		}
	},
	
	birthDateLink_onClick: function(event) {
		this.linkClick("birthDate");
	},
	
	addressLink_onClick: function(event) {
		this.linkClick("address");
	},
	
	diagnozeLink_onClick: function(event) {
		this.linkClick("diagnoze");
	},
	
	pcrLink_onClick: function(event) {
		var wm = getWindowManager();
		var doc = $O(this.node.id+"_pcrDocument","").getValue();
		wm.show_window("Window_"+doc.replace(/_/g,""),doc,null);
	},

	phLink_onClick: function(event) {
		var wm = getWindowManager();
		var doc = $O(this.node.id+"_phDocument","").getValue();
		wm.show_window("Window_"+doc.replace(/_/g,""),doc,null);
	}
});