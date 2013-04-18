var DocumentBloodAnalyze = Class.create(Document, {
    
   dispatchEvent: function($super,event,params) {        
        $super(event,params);        
        if (event == "DATATABLE_VALUE_CHANGED") {
            if (params["parent"]==this.object_id) {
                if (params["object_id"] == "DocumentBloodAnalyzeTable_"+this.module_id+"_"+this.object_id.replace(/_/g,"")) {
                    $I(this.node.id+"_documentTable").value = params["value"].replace(/xox/g,"=");
                }
            }
        }                
    },
        
    checkTable: function() {
      var c=0;
      var min=0;
      var value=0;
      var max=0;
      var ed=0;
      var v=0;
      var code_values = new Array;
      for (c=1;c<this.tbl.rows.length;c++) {
          min = this.tbl.getItem(c,3);
          ed = this.tbl.getItem(c,2);
          max = this.tbl.getItem(c,4);
          value = this.tbl.getItem(c,1);
          if (value.getValue()=="")
			v = "1000";
		  else
		    v = value.getValue();
          code_values[code_values.length] = this.tbl.getItem(c,5).value+"~"+v;
          if (parseFloat(value.getValue())<parseFloat(min.getValue()) && value.getValue()!=0 && value.getValue()!="") {
              min.node.style.color = "#008800";
              max.node.style.color = "#008800";
              ed.node.style.color = "#008800";
          } else
          if (parseFloat(value.getValue())>parseFloat(max.getValue()) && value.getValue()!=0 && value.getValue()!="") {
              min.node.style.color = "#880000";
              max.node.style.color = "#880000";
              ed.node.style.color = "#880000";
          } else {
              min.node.style.color = "#000000";
              max.node.style.color = "#000000";
              ed.node.style.color = "#000000";          
          }
      }
      if (code_values.length>0) {
		  var data = this.getValues();
		  var args = new Object;
		  args["docDate"] = data["docDate"];
		  args["code_values"] = code_values.join("|");
		  var obj = this;
		  new Ajax.Request("index.php",
		  {
			method: "post",
			parameters: {ajax: true, object_id: obj.object_id,
						 hook: '2', arguments: Object.toJSON(args)},
			onSuccess: function(transport) {
				var response = transport.responseText;
				$I(obj.node.id+"_results").innerHTML = response;
			}
		  });      
	  }
    },
    
    CONTROL_VALUE_CHANGED_processEvent: function(params) {
        var obj=this;
        if (params["object_id"]==this.object_id+"_analyzeType") {
            if (this.tbl.rows.length==1) {
                var patient = $O(this.object_id+"_patient",'').getValue();
				var args = new Object;
				args["patient"] = patient;
				args["def"] = params["value"];         
                new Ajax.Request("index.php",
                {
                    method: "post",
                    parameters: {ajax: true, object_id: obj.tbl.object_id,
                                 hook: '3', arguments: Object.toJSON(args)},
                    onSuccess: function(transport) {
                        var response = transport.responseText;
                        if (response!="") {
							resp_arr = response.split('|');
							helpTopic = resp_arr.shift();
							response = resp_arr.join('|');
                            eval('var '+obj.tbl.object_id+'tbl=new Array;\n'+response+';var tbl='+obj.tbl.object_id+'tbl;');
                            obj.tbl.deleteRows();
                            obj.tbl.columns = tbl.columns;
                            obj.tbl.rows = tbl.rows;
                            obj.tbl.build(true);
                            obj.checkTable();
                            if (helpTopic!="")
								$I(obj.node.id+"_Reference").style.display = "";
							else
								$I(obj.node.id+"_Reference").style.display = "none";
							obj.helpTopic = helpTopic;
                        }
                    }
                });
            }
        }
        if (params["object_id"]==this.object_id+"_patient") {
            if (this.tbl.rows.length>1) {
                var patient = params['value'];            	
                var c=0;
                var arr = new Array;
                for (c=1;c<this.tbl.rows.length;c++) {                   
                    if (this.tbl.getItem(c,0).getValue()!=null)
                        arr[arr.length] = this.tbl.getItem(c,0).getValue().split("_").pop()+"~"+this.tbl.getItem(c,1).getValue();
                }
            	var args = new Object;
            	args['patient'] = patient;
            	args['documentTable'] = arr.join('|');
                new Ajax.Request("index.php",
                {
                    method: "post",
                    parameters: {ajax: true, object_id: obj.tbl.object_id,hook: '4',arguments: Object.toJSON(args)},
                    onSuccess: function(transport) {
                        var response = transport.responseText;
                        if (response!="") {
                            eval('var tbl=new Array;\n'+response);
                            obj.tbl.deleteRows();
                            obj.tbl.columns = tbl.columns;
                            obj.tbl.rows = tbl.rows;
                            obj.tbl.build(true);
                            obj.checkTable();
                        }
                    }
                });                
            }
        }
    },
    
    Reference_onClick: function(event) {
		if (this.helpTopic!="")
			getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"guide"+this.helpTopic,"HTMLBook_"+this.module_id+"_medic_"+this.helpTopic,null,null,null);
	}
});