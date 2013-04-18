var WebItemTabset = Class.create(Tabset, {
    activateTab: function($super,tab) {
        if (blur_error!="") {
            if (blur_object!=null)
                blur_object.setFocus();
            this.reportMessage(blur_error,"error",true);
            return 0;
        }
        if ($I(this.item+"_"+this.active_tab)!=0)
        	{
        		$I(this.item+"_"+this.active_tab).style.display="none";
        	}
        if ($I(this.item+"_"+tab)!=0) {
	        $I(this.item+"_"+tab).style.opacity=0;	       
	        $I(this.item+"_"+tab).style.display="";	       
    		new Effect.Opacity(this.item+"_"+tab, { from: 0.0, to: 1.0, duration: 0.8 });
	        this.raiseEvent("TAB_CHANGED",$Arr("tabset_id="+this.object_id+",tab="+tab));
        }
        $super(tab);
    }
})