var EventLogConditionsFloatDiv = Class.create(Entity, {
       load: function() {
           
       },
          
   cancel_onClick: function(event) {
       getWindowManager().remove_window(this.win.id);
   },
   
   OK_onClick: function(event) {     
     var obj = $O(this.opener_object.parent_object_id,"");
     var data = this.getValues();
     obj.eventType = data["eventType"];
     obj.eventDateStart = data["eventDateStart"];
     obj.eventDateEnd = data["eventDateEnd"];
     obj.eventUser = data["eventUser"];
     obj.eventMessage = data["eventMessage"];
     var startDate = null;
     var endDate = null;
     
     if (obj.eventDateStart!="") {
    	startDate = new Date;
     	startDate.setTime(obj.eventDateStart);
     	startDate.setHours(0);
     	startDate.setMinutes(0);
     	startDate.setSeconds(0);
     }
     if (obj.eventDateEnd!="") {
    	 endDate = new Date;
    	 endDate.setTime(obj.eventDateEnd);
    	 endDate.setHours(23);
    	 endDate.setMinutes(59);
    	 endDate.setSeconds(59);
     }
     
     var cond = new Array;
     if (startDate!=null && typeof startDate != "undefined")
    	 cond[cond.length] = "eventDate>='"+(startDate.getTime()/1000)+"'";
     if (endDate!=null && typeof endDate != "undefined")
    	 cond[cond.length] = "eventDate<='"+(endDate.getTime()/1000)+"'";
     if (obj.eventUser!=" ")
         cond[cond.length] = "eventUser='"+obj.eventUser+"'";
     if (obj.eventType!=" " && obj.eventType!="0")
         cond[cond.length] = "eventType='"+obj.eventType+"'";
     if (obj.eventMessage!="")
         cond[cond.length] = "eventMessage LIKE '"+obj.eventMessage.replace(/\*/g,"%")+"'";
     obj.tbl.condition = cond.join(" AND ");
     obj.tbl.sort();
     getWindowManager().remove_window(this.win.id);
   }
});