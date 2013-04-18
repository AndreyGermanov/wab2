var CacheDepsTable = Class.create(DataTable, {
    getSingleValue: function() {
        var fp = new Array;
        var r=0;
        for (r=1;r<this.rows.length;r++) {
            fp[fp.length] = this.rows[r]['cells'][0]['value'].split("_").pop();
        }
        fp = fp.join("~");
        return fp;
    }
});