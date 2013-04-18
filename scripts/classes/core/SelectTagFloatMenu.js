var SelectTagFloatMenu = Class.create(Entity, {	
	selectValue: function(event) {
		var elem = eventTarget(event);
		var values = elem.getAttribute("selectedValues");
		if (values[0]=="{") {
			values = values.evalJSON();
			var o = null;
			for (o in values) {
				if (typeof values[o] != "function") {
					if ($O(o,'')!=null)
						$O(o,'').setValue(values[o]);					
				}
			}
		}
		removeContextMenu();
	}
});