var PortsRedirectTable = Class.create(DataTable, {
    getSingleValue: function() {
        var fp = new Array;
        var r=0;
        for (r=1;r<this.rows.length;r++) {
            fp[fp.length] = this.rows[r]['cells'][0]['value']+"~"+this.rows[r]['cells'][1]['value']+"~"+this.rows[r]['cells'][2]['value']+"~"+this.rows[r]['cells'][3]['value'];
        }
        fp = fp.join("|");
        return fp;
    }
});