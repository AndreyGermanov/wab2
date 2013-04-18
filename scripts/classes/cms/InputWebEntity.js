var InputWebEntity = Class.create(WebEntity, {
    dispatchEvent: function($super,event,params) {
        $super(event,params);        
        if (event=="CONTROL_VALUE_CHANGED") {
            if (params["object_id"]==this.node.id+"_submitSendEmail") {
                if (params["value"]=="1")
                    $I(this.node.id+"_submitEmailDisplay").style.display = "";
                else
                    $I(this.node.id+"_submitEmailDisplay").style.display = "none";
            }
        }
    },
    
    afterSave: function($super) {
    	var obj = this;
    	if (!this.asAdminTemplate) {
	        if ($(this.node.id+"_innerFrame").contentDocument.body!=null)
	            var result = $(this.node.id+"_innerFrame").contentDocument.body.innerHTML.replace(/\<script\>(.*)\<\/script\>/g,"").replace(/\n/g,"");
	        else
	            var result = 0;
	        if (result!=parseInt(result)) {
	        	result = result.evalJSON();
	        	result = result["error"];
            	obj.reportMessage(result,"error",true);
            	var src = $(obj.node.id+"_kaptcha_img").src;
	        	$(obj.node.id+"_kaptcha_img").src = ''; 
	        	$(obj.node.id+"_kaptcha_img").src = src; 
	        } else {
	        	obj.reportMessage("Ваше сообщение принято","info",true);
            	var src = $(obj.node.id+"_kaptcha_img").src;
	        	$(obj.node.id+"_kaptcha_img").src = ''; 
	        	$(obj.node.id+"_kaptcha_img").src = src; 
	        	var elems = $(obj.node.id).getElementsByTagName("INPUT");
	        	var o=0;
	        	for (o in elems) {
	        		if (typeof elems[o] != "function" && elems[o]["getAttribute"] != null && elems[o].getAttribute("type")=="text") {
	        			elems[o].value = "";
	        		}        			
	        	}
	        	var elems = $(this.node.id).getElementsByTagName("TEXTAREA");
	        	for (o in elems) {
	        		if (typeof elems[o] != "function" && elems[o]["innerHTML"] != null) {
	        			elems[o].value = "";
	        			if (elems[o].innerHTML!="")
	        				elems[o].innerHTML = "";
	        		}        			
	        	}
	        }
	       
    	} else
    		$super();
    }    
});