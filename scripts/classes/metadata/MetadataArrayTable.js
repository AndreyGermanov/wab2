var MetadataArrayTable = Class.create(DataTable, {
    getSingleValue: function() {
        var fp = new Object;
        for (var r=1;r<this.rows.length;r++) {            
            fp[this.rows[r]['cells'][0]['value']] = this.rows[r]['cells'][0]['value'];
        }        
        return fp;
    }
});