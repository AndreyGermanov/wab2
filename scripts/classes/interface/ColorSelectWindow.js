var ColorSelectWindow = Class.create(Entity, {	
	colorCell_onClick: function(event) {
		$O(this.opener_item.id,'').setValue(eventTarget(event).getAttribute("bgcolor").replace('#',''));
		this.raiseEvent("CONTROL_VALUE_CHANGED",$Arr("object_id="+this.opener_item.id+",value="+eventTarget(event).getAttribute("bgcolor").replace('#','')));
		$O(this.opener_item.id,'').setFocus();
    	removeContextMenu();		
	}
});