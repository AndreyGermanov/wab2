var SetPeriodWindow = Class.create(Entity, {	
    OK_onClick: function(event) {
		var data = this.getValues();
		var parentList = this.opener_object;
		parentList.periodStart = data["periodStart"];
		parentList.periodEnd = data["periodEnd"];		
		parentList.sort();
		getWindowManager().remove_window(this.win.id);
	},
	
    clearButton_onClick: function(event) {
		var parentList = this.opener_object;
		parentList.periodStart = "";
		parentList.periodEnd = "";
		parentList.sort();
		getWindowManager().remove_window(this.win.id);
	}   
});