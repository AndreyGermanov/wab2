var AuditConditionsFloatDiv = Class.create(Entity, {
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
     obj.eventIP = data["eventIP"];
     obj.eventFilePath = data["eventFilePath"];
     obj.eventFileNewPath = data["eventFileNewPath"];
     if (obj.eventDateStart!="") {
     	var startDate = new Date;
      	startDate.setTime(obj.eventDateStart);
      	startDate.setHours(0);
      	startDate.setMinutes(0);
      	startDate.setSeconds(0);
      }
      if (obj.eventDateEnd!="") {
     	 var endDate = new Date;
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
     if (obj.eventIP!="")
         cond[cond.length] = "eventIP='"+ip2int(obj.eventIP)+"'";
     if (obj.eventType!="" && obj.eventType!="0")
         cond[cond.length] = "eventType='"+obj.eventType+"'";
     if (obj.eventFilePath!="")
         cond[cond.length] = "eventFilePath LIKE '"+obj.eventFilePath.replace(/\*/g,"%")+"'";
     if (obj.eventFileNewPath!="")
         cond[cond.length] = "eventFileNewPath LIKE '"+obj.eventFileNewPath.replace(/\*/g,"%")+"'";
     obj.tbl.condition = cond.join(" AND ");
     obj.tbl.sort();
     getWindowManager().remove_window(this.win.id);
   }    
});