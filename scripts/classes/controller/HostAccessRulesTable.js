var HostAccessRulesTable = Class.create(GroupMembersTable, {

    checkAllRead: function(event) {
        var item = eventTarget(event);
        var tbl = $I(this.node.id+"_table");
        var elems = tbl.getElementsByTagName('input');
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox' && elems[c].getAttribute("column")=='read') {
                elems[c].checked = item.checked;
                if (!elems[c].checked) {
                    getElementById(elems[c].id.replace("Read","Write"),tbl).checked = item.checked;
                    if (this.className=="HostAccessRulesTable") {
                        getElementById(elems[c].id.replace("Read","SMB"),tbl).checked = item.checked;
                        getElementById(elems[c].id.replace("Read","NFS"),tbl).checked = item.checked;
                        getElementById(elems[c].id.replace("Read","AFP"),tbl).checked = item.checked;
                    }
                }
                if (this.className=="HostAccessRulesTable" && elems[c].checked && !getElementById(elems[c].id.replace("Read","SMB"),tbl).checked && !getElementById(elems[c].id.replace("Read","NFS"),tbl).checked  && !getElementById(elems[c].id.replace("Read","AFP"),tbl).checked) {
                    getElementById(elems[c].id.replace("Read","SMB"),tbl).checked = item.checked;
                    getElementById(elems[c].id.replace("Read","NFS"),tbl).checked = item.checked;                
                    getElementById(elems[c].id.replace("Read","AFP"),tbl).checked = item.checked;                
                }
            }
        }
        this.checkAllChecked(item);
        var ev = new Array;
        ev["target"] = this.opener_item;
        this.opener_object.onChange(ev);
    },

    checkAllWrite: function(event) {
        var item = eventTarget(event);
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox' && elems[c].getAttribute("column")=='write') {
                elems[c].checked = item.checked;
                if (this.className=="HostAccessRulesTable" && !document.getElementById(elems[c].id.replace("Write","Read")).checked) {
                    document.getElementById(elems[c].id.replace("Write","SMB")).checked = elems[c].checked;                    
                    document.getElementById(elems[c].id.replace("Write","NFS")).checked = elems[c].checked;                    
                    document.getElementById(elems[c].id.replace("Write","AFP")).checked = elems[c].checked;                    
                }
            }
            if (elems[c].checked) {
                document.getElementById(elems[c].id.replace("Write","Read")).checked = elems[c].checked;
            }
        }
        this.checkAllChecked(item);
        var ev = new Array;
        ev["target"] = this.opener_item;
        this.opener_object.onChange(ev);
    },

    checkAllSMB: function(event) {
        var item = eventTarget(event);        
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox' && elems[c].getAttribute("column")=='SMB') {
                elems[c].checked = item.checked;
                var ev = new Array;
                ev["target"] = elems[c];
                this.checkSMB(ev);
            }
        }
        this.checkAllChecked(item);
        var ev = new Array;
        ev["target"] = this.opener_item;
        this.opener_object.onChange(ev);
    },

    checkAllNFS: function(event) {
        var item = eventTarget(event);
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox' && elems[c].getAttribute("column")=='NFS') {
                elems[c].checked = item.checked;
                var ev = new Array;
                ev["target"] = elems[c];
                this.checkNFS(ev);
            }
        }
        this.checkAllChecked(item);
        var ev = new Array;
        ev["target"] = this.opener_item;
        this.opener_object.onChange(ev);
    },

    checkAllAFP: function(event) {
        var item = eventTarget(event);
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox' && elems[c].getAttribute("column")=='AFP') {
                elems[c].checked = item.checked;
                var ev = new Array;
                ev["target"] = elems[c];
                this.checkAFP(ev);
            }
        }
        this.checkAllChecked(item);
        var ev = new Array;
        ev["target"] = this.opener_item;
        this.opener_object.onChange(ev);
    },
    
    checkRead: function(event) {
        var target = eventTarget(event);
        if (!target.checked) {
            getElementById(target.id.replace("Read","Write"),this.node).checked = target.checked;
            if (this.className=="HostAccessRulesTable") {
                getElementById(target.id.replace("Read","SMB"),this.node).checked = target.checked;
                getElementById(target.id.replace("Read","NFS"),this.node).checked = target.checked;
                getElementById(target.id.replace("Read","AFP"),this.node).checked = target.checked;
            }
        }
        if (target.checked) {
            if (this.className=="HostAccessRulesTable" && !getElementById(target.id.replace("Read","SMB"),this.node).checked && !getElementById(target.id.replace("Read","NFS"),this.node).checked && !getElementById(target.id.replace("Read","AFP"),this.node).checked) {
                getElementById(target.id.replace("Read","SMB"),this.node).checked = target.checked;
                getElementById(target.id.replace("Read","NFS"),this.node).checked = target.checked;                
                getElementById(target.id.replace("Read","AFP"),this.node).checked = target.checked;                
            }
        }
        this.checkAllChecked(target);
        var ev = new Array;
        ev["target"] = this.opener_item;
        this.opener_object.onChange(ev);
    },

    checkWrite: function(event) {
        target = eventTarget(event);
        if (target.checked) {
            document.getElementById(target.id.replace("Write","Read")).checked = target.checked;
            if (this.className=="HostAccessRulesTable" && !document.getElementById(target.id.replace("Write","SMB")).checked && !document.getElementById(target.id.replace("Write","NFS")).checked && !document.getElementById(target.id.replace("Write","AFP")).checked) {
                document.getElementById(target.id.replace("Write","SMB")).checked = target.checked;
                document.getElementById(target.id.replace("Write","NFS")).checked = target.checked;                
                document.getElementById(target.id.replace("Write","AFP")).checked = target.checked;                
            }
        }
        this.checkAllChecked(target);
        var ev = new Array;
        ev["target"] = this.opener_item;
        this.opener_object.onChange(ev);
    },

    checkSMB: function(event) {
        var target = eventTarget(event);
        if (!target.checked) {
            if (!document.getElementById(target.id.replace("SMB","NFS")).checked && !document.getElementById(target.id.replace("SMB","AFP")).checked) {
                document.getElementById(target.id.replace("SMB","Read")).checked = target.checked;
                document.getElementById(target.id.replace("SMB","Write")).checked = target.checked;                
            }
        }
        if (target.checked) {
            if (!document.getElementById(target.id.replace("SMB","Read")).checked && !document.getElementById(target.id.replace("SMB","NFS")).checked && !document.getElementById(target.id.replace("SMB","AFP")).checked) {
                document.getElementById(target.id.replace("SMB","Read")).checked = target.checked;
            }
        }
       	this.checkAllChecked(target);
       	var ev = new Array;
       	ev["target"] = this.opener_item;
       	this.opener_object.onChange(ev);
    },

    checkNFS: function(event) {
        var target = eventTarget(event);
        if (!target.checked) {
            if (!document.getElementById(target.id.replace("NFS","SMB")).checked && !document.getElementById(target.id.replace("NFS","AFP")).checked) {
                document.getElementById(target.id.replace("NFS","Read")).checked = target.checked;
                document.getElementById(target.id.replace("NFS","Write")).checked = target.checked;                
            }
        }
        if (target.checked) {
            if (!document.getElementById(target.id.replace("NFS","Read")).checked && !document.getElementById(target.id.replace("NFS","SMB")).checked && !document.getElementById(target.id.replace("NFS","AFP")).checked) {
                document.getElementById(target.id.replace("NFS","Read")).checked = target.checked;
            }
        }
        this.checkAllChecked(target);
        var ev = new Array;
        ev["target"] = this.opener_item;
        this.opener_object.onChange(ev);
    },

    checkAFP: function(event) {
        target = eventTarget(event);
        if (!target.checked) {
            if (!document.getElementById(target.id.replace("AFP","SMB")).checked && !document.getElementById(target.id.replace("AFP","NFS")).checked) {
                document.getElementById(target.id.replace("AFP","Read")).checked = target.checked;
                document.getElementById(target.id.replace("AFP","Write")).checked = target.checked;                
            }
        }
        if (target.checked) {
            if (!document.getElementById(target.id.replace("AFP","Read")).checked && !document.getElementById(target.id.replace("AFP","SMB")).checked && !document.getElementById(target.id.replace("AFP","NFS")).checked) {
                document.getElementById(target.id.replace("AFP","Read")).checked = target.checked;
                document.getElementById(target.id.replace("AFP","Write")).checked = target.checked;
            }
        }
        this.checkAllChecked(target);
        var ev = new Array;
        ev["target"] = this.opener_item;
        this.opener_object.onChange(ev);
    },
    
    checkChecked: function(column) {
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var all_checked = true;
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox' && elems[c].getAttribute("column")==column) {    
                if (!elems[c].checked) {
                    all_checked=false;
                    return all_checked;
                }
            }
        }
        return all_checked;
    },
    
    checkAllChecked: function() {
        var id=this.object_id;
        getElementById(id+"_checkAllRead",this.node).checked = this.checkChecked("read");
        getElementById(id+"_checkAllWrite",this.node).checked = this.checkChecked("write");
        if (this.className=="HostAccessRulesTable") {
            getElementById(id+"_checkAllSMB",this.node).checked = this.checkChecked("SMB");
            getElementById(id+"_checkAllNFS",this.node).checked = this.checkChecked("NFS");
            getElementById(id+"_checkAllAFP",this.node).checked = this.checkChecked("AFP");
        }
    },

    getChecked: function() {
        var res = new Array;
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var c=0;
        for (c=0;c<elems.length;c++) {
            var smb='';var nfs='';var afp='';
            var readonly="";
            if (elems[c].getAttribute("type")=='checkbox' && elems[c].getAttribute("column")=='read') {
                if (elems[c].checked) {
                    if (document.getElementById(elems[c].id.replace("Read","Write")).checked)
                        readonly = "no";
                    else
                        readonly = "yes";
                    if (this.className=="HostAccessRulesTable") {
                        if (document.getElementById(elems[c].id.replace("Read","SMB")).checked)
                            smb = "smb";
                        else
                            smb = "";                    
                        if (document.getElementById(elems[c].id.replace("Read","NFS")).checked)
                            nfs = "nfs";
                        else
                            nfs = "";                    
                        if (document.getElementById(elems[c].id.replace("Read","AFP")).checked)
                            afp = "afp";
                        else
                            afp = "";                    
                        res[res.length] = elems[c].getAttribute("share")+"~"+elems[c].getAttribute("path")+"~"+readonly+"~"+smb+"~"+nfs+"~"+afp;
                    } else {
                        res[res.length] = elems[c].getAttribute("share")+"~"+elems[c].getAttribute("path")+"~"+readonly;                        
                    }
                }
            }
        }
        return res.join("|");
    }
});