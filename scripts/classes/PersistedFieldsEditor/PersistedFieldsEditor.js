var PersistedFieldsEditor = Class.create(Mailbox, {

    fill: function() {
        $I(this.node.id+"_value").value = this.control.getValue();
    },

    OK_onClick: function(event) {
      var oldValue="";
      if (this.editorType=="window") {
        window.opener.$I(this.control.node.id+"_value").value = $I(this.node.id+"_value").value;
        oldValue = window.opener.$I(this.control.node.id+"_value").value;
      }
    else {
        $I(this.control.node.id+"_value").value = $I(this.node.id+"_value").value;
        oldValue = $I(this.control.node.id+"_value").value;
    }
      this.control.raiseEvent("CONTROL_VALUE_CHANGED",$Arr("object_id="+this.control.object_id+",old_value="+oldValue+",value="+$I(this.node.id+"_value").value.replace(/=/g,"xox")));
      if (this.editorType=="window")
        window.close();
      if (this.editorType=="div" && this.control.node.getAttribute("destroyDiv")=="true") {
        this.raiseEvent("DESTROY",$Arr("object_id="+this.object_id));
        delete objects.objects[this.object_id];
        this.node.parentNode.innerHTML="";
      } else {
    	  getWindowManager().remove_window(this.win.id,this.instance_id);    	  
      }
    },

    cancel_onClick: function(event) {
        if (this.editorType=="window")
            window.close();
        if (this.editorType=="div" && this.control.node.getAttribute("destroyDiv")=="true") {
            this.raiseEvent("DESTROY",$Arr("object_id="+this.object_id));
            delete objects.objects[this.object_id];
            this.node.parentNode.innerHTML="";
        }
        if (this.editorType == "WABWindow") {
            getWindowManager().remove_window(this.win.id,this.instance_id);            
        }
    }
});