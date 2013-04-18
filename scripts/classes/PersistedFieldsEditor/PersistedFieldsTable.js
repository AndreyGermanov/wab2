var PersistedFieldsTable = Class.create(DataTable, {

    getSingleValue: function() {
        var fp = new Array;
        for (var r=1;r<this.rows.length;r++) {
            var type = this.rows[r]['cells'][1]['value'];
            if (this.rows[r]['cells'][1]['value']=="date") {
                type = "decimal";
            }
            if (typeof(this.rows[r]['cells'][2]['value'])!="undefined")
            	fp[fp.length] = this.rows[r]['cells'][0]['value']+"|"+type+"|"+this.rows[r]['cells'][2]['value']+"|"+this.rows[r]['cells'][3]['value'];
            else
            	fp[fp.length] = this.rows[r]['cells'][0]['value']+"|"+type+"||"+this.rows[r]['cells'][3]['value'];
        }
        fp = fp.join("#");
        return fp;
    },

    dispatchEvent: function($super,event,params) {
        $super(event,params);
        if (event=="CONTROL_VALUE_CHANGED") {
            if (this.object_id!=params["object_id"])
                return 0;
            this.obje = $O(params["object_id"],"");
            if (this.obje!=0) {
                var node = this.obje.node.parentNode;
                if (node.getAttribute("column")=="fieldType") {
                    var column = this.getColumnNumber(node.getAttribute("column"));
                    var row = node.getAttribute("row");
                    var options_ctl = this.getItem(row,column+1);
                    if (options_ctl!=null && options_ctl != 0) {
                        var val = options_ctl.getValue();
                        var persistedArray = val.split("~");
                        var arr = new Array;
                        for (var ce=0;ce<persistedArray.length;ce++) {
                            var arr_parts = persistedArray[ce].split('=');
                            arr[arr_parts[0]] = arr_parts[1];
                        }   
                        arr["type"] = params["value"];                        
                        persistedArray = new Array;
                        for (c in arr) {
                            if (typeof(arr[c])=="string")
                                persistedArray[persistedArray.length] = c+"xox"+arr[c];
                        }
                        persistedArray = persistedArray.join("~");
                        $I(options_ctl.node.id+"_value").value = persistedArray;
                        this.obje.raiseEvent("CONTROL_VALUE_CHANGED",$Arr("object_id="+options_ctl.object_id+",value="+persistedArray+",old_value="+val));
                    }
                }
                if (node.getAttribute("column")=="fieldName") {
                    var row = node.getAttribute("row");
                    var arr = this.rows[row]['cells']['2']['control_properties'].split(",");
                    var res = new Array;
                    for (var c=0;c<arr.length;c++) {
                        array_parts = arr[c].split("=");
                        res[array_parts[0]] = array_parts[1];
                    }
                    res["fieldName"] = params["value"];
                    arr = new Array;
                    arr = res["editorObject"].split("_");
                    res["editorObject"] = arr[0]+"_"+params["value"];
                    var result = new Array;
                    var c=null;
                    for (c in res) {
                        if (typeof(res[c])=="string")
                            result[result.length] = c+"="+res[c];
                    }
                    this.rows[row]['cells']['2']['control_properties'] = result.join(",");                    
                }
            }
        }
    }
});