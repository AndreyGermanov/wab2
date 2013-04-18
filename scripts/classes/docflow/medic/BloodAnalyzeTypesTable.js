var BloodAnalyzeTypesTable = Class.create(DataTable, {
    getSingleValue: function() {
        var fp = new Array;
        for (var r=1;r<this.rows.length;r++) {
            if ((this.rows[r]['cells'][0]['value']!=""))
            fp[fp.length] = this.rows[r]['cells'][0]['value'].split("_").pop();
        }
        fp = fp.join("~");
        return fp;
    }
});