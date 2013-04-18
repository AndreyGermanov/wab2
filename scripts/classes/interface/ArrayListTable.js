var ArrayListTable = Class.create(DataTable, {

    getSingleValue: function() {
        var fp = new Array;
        for (var r=1;r<this.rows.length;r++) {
            fp[fp.length] = this.rows[r]['cells'][0]['value'];
        }
        fp = fp.join("~");
        return fp;
    },
    
    OK_onClick: function(event) {
		var value = this.getSingleValue();
		var valueTitle = "";
		if (value!="")
			valueTitle = "список...";
		this.raiseEvent("CONTROL_VALUE_CHANGED",$Arr("object_id="+this.opener_object.object_id+",value="+value+",valueTitle="+valueTitle));
		getWindowManager().remove_window(this.win.id);
	}
});