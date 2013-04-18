var DocumentBloodAnalyzeTable = Class.create(DataTable, {
    getSingleValue: function() {
        var fp = new Array;
        for (var r=1;r<this.rows.length;r++) {
            if ((this.rows[r]['cells'][0]['value']!=""))
            fp[fp.length] = this.rows[r]['cells'][0]['value'].split("_").pop()+"~"+this.rows[r]['cells'][1]['value'];
        }
        fp = fp.join("|");
        return fp;
    },
    
    CONTROL_VALUE_CHANGED_processEvent: function(params) {
        var obje = $O(params["object_id"],"");
        var obj = this;
        if (obje.parent_object_id == this.object_id) {
            var id_arr = obje.object_id.split("_");
            id_arr.pop();
            var row_num = id_arr.pop();
            if (obje.object_id.split("_").pop()=="definition") {
                var patient = $O(this.parent_object_id+"_patient",'').getValue();
                var args = new Object;
                args["patient"] = patient;                
                new Ajax.Request("index.php",
                {
                    method: "post",
                    parameters: {ajax: true, object_id: params["value"],hook:'3',arguments: Object.toJSON(args)},
                    onSuccess: function(transport) {
                        var response = transport.responseText;
                        if (response!="") {
                            var arr = response.split('~');
                            obj.getItem(row_num,2).setValue(arr[0]);
                            obj.getItem(row_num,3).setValue(arr[1]);
                            obj.getItem(row_num,4).setValue(arr[2]);
                            obj.getItem(row_num,5).setValue(arr[3]);
                            $O(obj.parent_object_id,'').checkTable();
                        }
                    }
                });                                
            }
            if (obje.object_id.split("_").pop()=="value") {            
                $O(this.parent_object_id,'').checkTable();
            }
        }
    },
    
    sort: function($super) {
        $super();
        $O(this.parent_object_id,'').checkTable();
    }
});