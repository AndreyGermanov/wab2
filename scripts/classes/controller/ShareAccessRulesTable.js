var ShareAccessRulesTable = Class.create(GroupMembersTable, {

    checkAllRead: function(event) {
        var item = eventTarget(event);
        var item_checked = item.checked;
        var ev = new Array;
        var c=0;
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox' && ( elems[c].getAttribute("column")=='groupRead' || elems[c].getAttribute("column")=='hostRead' || elems[c].getAttribute("column")=="defaultRead" || elems[c].getAttribute("column")=="allRead" )) {
                if (elems[c].getAttribute("column")=='groupRead') {
                    elems[c].parentNode.setAttribute("class","expandable_cell1");
                }
                if ((elems[c].getAttribute("column")=='hostRead' || elems[c].getAttribute("column")=='defaultRead') && elems[c]!=item) {
                    elems[c].checked = item_checked;
                    ev["target"] = elems[c];
                    this.checkRead(ev,true);
                }
                if (elems[c]!=null)
                    elems[c].checked = item_checked;
            }
            if (!elems[c].checked && (elems[c].getAttribute("column")=='groupRead' || elems[c].getAttribute("column")=="defaultRead" || elems[c].getAttribute("column")=="allRead")) {
                if (getElementById(this.node,elems[c].id.replace("Read","Write")).getAttribute("column")=='groupWrite') {
                    getElementById(this.node,elems[c].id.replace("Read","Write")).parentNode.setAttribute("class","expandable_cell1");
                }
                getElementById(this.node,elems[c].id.replace("Read","Write")).checked = elems[c].checked;
            }

        }
        this.checkAllChecked();
        var ev = new Array;
        ev["target"] = this.opener_item;
        if (this.opener_object!=null)
            this.opener_object.onChange(ev);
    },

    checkAllSMB: function(event,group_number) {
        var item = eventTarget(event);
        var item_checked = item.checked;
        var ev = new Array;
        var c=0;
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox' && ( elems[c].getAttribute("column")=='hostSMB' || elems[c].getAttribute("column")=='defaultSMB')) {
                elems[c].checked = item_checked;
                ev["target"] = elems[c];
                this.checkSMB(ev,true);
            }
        }
        this.checkAllChecked();
        ev = new Array;
        ev["target"] = this.opener_item;
        if (this.opener_object!=null)
            this.opener_object.onChange(ev);
    },

    checkAllNFS: function(event,group_number) {
        var item = eventTarget(event);
        var item_checked = item.checked;
        var ev = new Array;
        var c=0;
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox' && ( elems[c].getAttribute("column")=='hostNFS' || elems[c].getAttribute("column")=='defaultNFS')) {
                elems[c].checked = item_checked;
                ev["target"] = elems[c];
                this.checkNFS(ev,true);
            }
        }
        this.checkAllChecked();
        ev["target"] = this.opener_item;
        if (this.opener_object!=null)
            this.opener_object.onChange(ev);
    },

    checkAllAFP: function(event,group_number) {
        var item = eventTarget(event);
        var item_checked = item.checked;
        var ev = new Array;
        var c=0;
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox' && ( elems[c].getAttribute("column")=='hostAFP' || elems[c].getAttribute("column")=='defaultAFP')) {
                elems[c].checked = item_checked;
                ev["target"] = elems[c];
                this.checkAFP(ev,true);
            }
        }
        this.checkAllChecked();
        ev["target"] = this.opener_item;
        if (this.opener_object!=null)
            this.opener_object.onChange(ev);
    },
    
    checkGroupRead: function(event,group_number) {
        var item = eventTarget(event);
        var item_checked = item.checked;
        var ev = new Array;
        var c=0;
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox' && elems[c].getAttribute('parent_group') == group_number && ( elems[c].getAttribute("column")=='hostRead')) {
                elems[c].checked = item_checked;
                ev["target"] = elems[c];
                this.checkRead(ev);
            }
        }
        item.checked = item_checked;
        item.parentNode.setAttribute("class","expandable_cell1");
        if (!item.checked) {
            getElementById(this.node,item.id.replace("Read","Write")).parentNode.setAttribute("class","expandable_cell1");
        }       
        ev["target"] = this.opener_item;
        if (this.opener_object!=null)
            this.opener_object.onChange(ev);
    },

    checkGroupSMB: function(event,group_number) {
        var item = eventTarget(event);
        var item_checked = item.checked;
        var ev = new Array;
        var c=0;
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox' && elems[c].getAttribute('parent_group') == group_number && ( elems[c].getAttribute("column")=='hostSMB')) {
                elems[c].checked = item_checked;
                ev["target"] = elems[c];
                this.checkSMB(ev);
            }
        }
        item.checked = item_checked;
        item.parentNode.setAttribute("class","expandable_cell1");
        ev["target"] = this.opener_item;
        if (this.opener_object!=null)
            this.opener_object.onChange(ev);
    },

    checkGroupNFS: function(event,group_number) {
        var item = eventTarget(event);
        var item_checked = item.checked;
        var ev = new Array;
        var c=0;
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox' && elems[c].getAttribute('parent_group') == group_number && ( elems[c].getAttribute("column")=='hostNFS')) {
                elems[c].checked = item_checked;
                ev["target"] = elems[c];
                this.checkNFS(ev);
            }
        }
        item.checked = item_checked;
        item.parentNode.setAttribute("class","expandable_cell1");
        ev["target"] = this.opener_item;
        if (this.opener_object!=null)
            this.opener_object.onChange(ev);
    },

    checkGroupAFP: function(event,group_number) {
        var item = eventTarget(event);
        var item_checked = item.checked;
        var ev = new Array;
        var c=0;
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox' && elems[c].getAttribute('parent_group') == group_number && ( elems[c].getAttribute("column")=='hostAFP')) {
                elems[c].checked = item_checked;
                ev["target"] = elems[c];
                this.checkAFP(ev);
            }
        }
        item.checked = item_checked;
        item.parentNode.setAttribute("class","expandable_cell1");
        ev["target"] = this.opener_item;
        if (this.opener_object!=null)
            this.opener_object.onChange(ev);
    },
    
    isAllGroupRead: function(group_number) {
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox' && elems[c].getAttribute('parent_group') == group_number && ( elems[c].getAttribute("column")=='groupRead' || elems[c].getAttribute("column")=='hostRead')) {
                if (!elems[c].checked)
                    return false;
            };
        };   
        return true;
    },
    
    isAnyGroupRead: function(group_number) {
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox' && elems[c].getAttribute('parent_group') == group_number && ( elems[c].getAttribute("column")=='groupRead' || elems[c].getAttribute("column")=='hostRead')) {
                if (elems[c].checked)
                    return true;
            }
        }        
        return false;        
    },

    checkAllWrite: function(event) {
        var item = eventTarget(event);
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var item_checked = item.checked;
        var ev = new Array;
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox' && ( elems[c].getAttribute("column")=='groupWrite' || elems[c].getAttribute("column")=='hostWrite' || elems[c].getAttribute("column")=='defaultWrite' || elems[c].getAttribute("column")=='allWrite')) {
                if (elems[c].getAttribute("column")=='groupWrite') {
                    elems[c].parentNode.setAttribute("class","expandable_cell1");
                }
                if ((elems[c].getAttribute("column")=='hostWrite' || elems[c].getAttribute("column")=='defaultWrite') && elems[c]!=item) {
                    elems[c].checked = item_checked;
                    ev["target"] = elems[c];
                    this.checkWrite(ev,true);
                }
                elems[c].checked = item.checked;
            }
            if (elems[c].checked && ( elems[c].getAttribute("column")=='groupWrite' || elems[c].getAttribute("column")=='hostWrite' || elems[c].getAttribute("column")=='defaultWrite' || elems[c].getAttribute("column")=='allWrite')) {
                if (getElementById(this.node,elems[c].id.replace("Write","Read")).getAttribute("column")=='groupRead') {
                    getElementById(this.node,elems[c].id.replace("Write","Read")).parentNode.setAttribute("class","expandable_cell1");
                }                
                getElementById(this.node,elems[c].id.replace("Write","Read")).checked = elems[c].checked;
            }
        }
        this.checkAllChecked();
        ev["target"] = this.opener_item;
        if (this.opener_object!=null)
            this.opener_object.onChange(ev);
    },

    isAllGroupWrite: function(group_number) {
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox' && elems[c].getAttribute('parent_group') == group_number && ( elems[c].getAttribute("column")=='groupWrite' || elems[c].getAttribute("column")=='hostWrite')) {
                if (!elems[c].checked)
                    return false;
            }
        }        
        return true;
    },
    
    isAnyGroupWrite: function(group_number) {
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox' && elems[c].getAttribute('parent_group') == group_number && ( elems[c].getAttribute("column")=='groupWrite' || elems[c].getAttribute("column")=='hostWrite')) {
                if (elems[c].checked)
                    return true;
            }
        }        
        return false;        
    },

    isAllGroupSMB: function(group_number) {
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox' && elems[c].getAttribute('parent_group') == group_number && ( elems[c].getAttribute("column")=='groupSMB' || elems[c].getAttribute("column")=='hostSMB')) {
                if (!elems[c].checked)
                    return false;
            }
        }        
        return true;
    },
    
    isAnyGroupSMB: function(group_number) {
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox' && elems[c].getAttribute('parent_group') == group_number && ( elems[c].getAttribute("column")=='groupSMB' || elems[c].getAttribute("column")=='hostSMB')) {
                if (elems[c].checked)
                    return true;
            }
        }        
        return false;        
    },

    isAllGroupNFS: function(group_number) {
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox' && elems[c].getAttribute('parent_group') == group_number && ( elems[c].getAttribute("column")=='groupNFS' || elems[c].getAttribute("column")=='hostNFS')) {
                if (!elems[c].checked)
                    return false;
            }
        }        
        return true;
    },
    
    isAnyGroupNFS: function(group_number) {
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox' && elems[c].getAttribute('parent_group') == group_number && ( elems[c].getAttribute("column")=='groupNFS' || elems[c].getAttribute("column")=='hostNFS')) {
                if (elems[c].checked)
                    return true;
            }
        }        
        return false;        
    },

    isAllGroupAFP: function(group_number) {
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox' && elems[c].getAttribute('parent_group') == group_number && ( elems[c].getAttribute("column")=='groupAFP' || elems[c].getAttribute("column")=='hostAFP')) {
                if (!elems[c].checked)
                    return false;
            }
        }        
        return true;
    },
    
    isAnyGroupAFP: function(group_number) {
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox' && elems[c].getAttribute('parent_group') == group_number && ( elems[c].getAttribute("column")=='groupAFP' || elems[c].getAttribute("column")=='hostAFP')) {
                if (elems[c].checked)
                    return true;
            }
        }        
        return false;        
    },
    
    checkGroupWrite: function(event,group_number) {
        var item = eventTarget(event);
        var item_checked = item.checked;
        var ev = new Array;
        var c=0;
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox' && elems[c].getAttribute('parent_group') == group_number && (elems[c].getAttribute("column")=='hostWrite')) {
                elems[c].checked = item_checked;
                ev["target"] = elems[c];
                this.checkWrite(ev);
            }
        }
        item.parentNode.setAttribute("class","expandable_cell1");
        if (item.checked) {
            getElementById(this.node,item.id.replace("Write","Read")).parentNode.setAttribute("class","expandable_cell1");
        }       
        ev["target"] = this.opener_item;
        if (this.opener_object!=null)
            this.opener_object.onChange(ev);
    },

    checkRead: function(event,notCheckAll) {
        var target = eventTarget(event);
        var target_checked = target.checked;
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (!target.checked) {
                if (elems[c]["id"]==getElementById(this.node,target.id.replace("Read","Write")).id) {
                    elems[c].checked = target_checked;
                }      
                if (this.type=="hosts") {
                    if (elems[c]["id"]==getElementById(this.node,target.id.replace("Read","SMB")).id) {
                        elems[c].checked = target_checked;
                    }                
                    if (elems[c]["id"]==getElementById(this.node,target.id.replace("Read","NFS")).id) {
                        elems[c].checked = target_checked;
                    }                
                    if (elems[c]["id"]==getElementById(this.node,target.id.replace("Read","AFP")).id) {
                        elems[c].checked = target_checked;
                    }                
                }
            } else {
                if (this.type=="hosts") {
                    if (elems[c]["id"]==getElementById(this.node,target.id.replace("Read","SMB")).id) {
                        var td1 = elems[c].parentNode.nextSibling;
                        var el1=td1.getElementsByTagName("input");
                        el1=el1[0];
                        var td1 = elems[c].parentNode.nextSibling.nextSibling;
                        var el2=td1.getElementsByTagName("input");
                        el2=el2[0];
                        if (el1!=null && el2!=null) {
                            if (!el1.checked && !el2.checked && !elems[c].checked) {
                                el1.checked=true;
                                el2.checked=true;
                                elems[c].checked=true;
                            }                    
                        }
                    }                                
                }
            }                
        }
        var type = this.object_id.split("_").pop();
        if (type=="hosts" && target.getAttribute("column")!="defaultRead") {            
            var parent_group = target.getAttribute("parent_group");
            var item = $I(this.parent_object_id+"_ShareAccessRulesTable_"+this.module_id+"_"+this.idnumber+"_hosts_"+parent_group+"_checkGroupRead");
            var group_item = item.parentNode;
            if (this.isAllGroupRead(parent_group))
                item.checked = true;
            else
                item.checked = false;
            if (this.isAnyGroupRead(parent_group) && !this.isAllGroupRead(parent_group))
                group_item.setAttribute("class","expandable_cell3");            
            else
                group_item.setAttribute("class","expandable_cell1");            
            var item = $I(this.parent_object_id+"_ShareAccessRulesTable_"+this.module_id+"_"+this.idnumber+"_hosts_"+parent_group+"_checkGroupWrite");
            var group_item = item.parentNode;
            if (this.isAllGroupWrite(parent_group))
                item.checked = true;
            else
                item.checked = false;
            if (this.isAnyGroupWrite(parent_group) && !this.isAllGroupWrite(parent_group))
                group_item.setAttribute("class","expandable_cell3");            
            else
                group_item.setAttribute("class","expandable_cell1");            
            
            var item = $I(this.parent_object_id+"_ShareAccessRulesTable_"+this.module_id+"_"+this.idnumber+"_hosts_"+parent_group+"_checkGroupSMB");
            var group_item = item.parentNode;
            if (this.isAllGroupSMB(parent_group))
                item.checked = true;
            else
                item.checked = false;
            if (this.isAnyGroupSMB(parent_group) && !this.isAllGroupSMB(parent_group))
                group_item.setAttribute("class","expandable_cell3");            
            else
                group_item.setAttribute("class","expandable_cell1");            
            var item = $I(this.parent_object_id+"_ShareAccessRulesTable_"+this.module_id+"_"+this.idnumber+"_hosts_"+parent_group+"_checkGroupNFS");
            var group_item = item.parentNode;
            if (this.isAllGroupNFS(parent_group))
                item.checked = true;
            else
                item.checked = false;
            if (this.isAnyGroupNFS(parent_group) && !this.isAllGroupNFS(parent_group))
                group_item.setAttribute("class","expandable_cell3");            
            else
                group_item.setAttribute("class","expandable_cell1");                        
            var item = $I(this.parent_object_id+"_ShareAccessRulesTable_"+this.module_id+"_"+this.idnumber+"_hosts_"+parent_group+"_checkGroupAFP");
            var group_item = item.parentNode;
            if (this.isAllGroupAFP(parent_group))
                item.checked = true;
            else
                item.checked = false;
            if (this.isAnyGroupAFP(parent_group) && !this.isAllGroupAFP(parent_group))
                group_item.setAttribute("class","expandable_cell3");            
            else
                group_item.setAttribute("class","expandable_cell1");                        
        }
        var ev = new Array;
        ev["target"] = this.opener_item;
        if (this.opener_object!=null)
            this.opener_object.onChange(ev);
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (elems[c].id == target.id && elems[c]!=target && elems[c].checked!=target_checked) {
                elems[c].checked = target_checked;
                ev["target"] = elems[c];
                this.checkRead(ev,notCheckAll);
            }
        }        
        if (!notCheckAll)
            this.checkAllChecked();
    },
    
    checkSMB: function(event,notCheckAll) {
        var target = eventTarget(event);
        var target_checked = target.checked;
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var c=0;
        var afpCheck=null;
        var smbCheck=null;
        var nfsCheck=null;
        for (c=0;c<elems.length;c++) {
            if (!target.checked) {
                if (elems[c]["id"]==getElementById(this.node,target.id.replace("SMB","NFS")).id) {
                    nfsCheck=elems[c];
                    smbCheck=target;
                }      
                if (elems[c]["id"]==getElementById(this.node,target.id.replace("SMB","AFP")).id) {
                    afpCheck=elems[c];
                    smbCheck=target;
                    var td1 = elems[c].parentNode.previousSibling.previousSibling.previousSibling;                    
                    var writeCheck=td1.getElementsByTagName("input");
                    writeCheck=writeCheck[0];
                    td1 = td1.previousSibling;                    
                    var readCheck=td1.getElementsByTagName("input");
                    readCheck=readCheck[0];                    
                    if (!smbCheck.checked && !nfsCheck.checked && !afpCheck.checked) {
                        writeCheck.checked=false;
                        readCheck.checked=false;
                    }                    
                }      
            } else {
                if (elems[c]["id"]==getElementById(this.node,target.id.replace("SMB","NFS")).id) {
                    nfsCheck=elems[c];
                    smbCheck=target;
                    var td1 = elems[c].parentNode.previousSibling.previousSibling;                    
                    var writeCheck=td1.getElementsByTagName("input");
                    writeCheck=writeCheck[0];
                    td1 = td1.previousSibling;                    
                    var readCheck=td1.getElementsByTagName("input");
                    readCheck=readCheck[0];                    
                    if (!readCheck.checked && !writeCheck.checked) {
                        readCheck.checked=true;
                    }                    
                }                      
                if (elems[c]["id"]==getElementById(this.node,target.id.replace("SMB","AFP")).id) {
                    afpCheck=elems[c];
                    smbCheck=target;
                    var td1 = elems[c].parentNode.previousSibling.previousSibling;                    
                    var writeCheck=td1.getElementsByTagName("input");
                    writeCheck=writeCheck[0];
                    td1 = td1.previousSibling;                    
                    var readCheck=td1.getElementsByTagName("input");
                    readCheck=readCheck[0];                    
                    if (!readCheck.checked && !writeCheck.checked) {
                        readCheck.checked=true;
                    }                    
                }                      
            }                
        }
        var type = this.object_id.split("_").pop();
        if (type=="hosts" && target.getAttribute("column")!="defaultSMB") {            
            var parent_group = target.getAttribute("parent_group");
            var item = $I(this.parent_object_id+"_ShareAccessRulesTable_"+this.module_id+"_"+this.idnumber+"_hosts_"+parent_group+"_checkGroupRead");
            var group_item = item.parentNode;
            if (this.isAllGroupRead(parent_group))
                item.checked = true;
            else
                item.checked = false;
            if (this.isAnyGroupRead(parent_group) && !this.isAllGroupRead(parent_group))
                group_item.setAttribute("class","expandable_cell3");            
            else
                group_item.setAttribute("class","expandable_cell1");            
            var item = $I(this.parent_object_id+"_ShareAccessRulesTable_"+this.module_id+"_"+this.idnumber+"_hosts_"+parent_group+"_checkGroupWrite");
            var group_item = item.parentNode;
            if (this.isAllGroupWrite(parent_group))
                item.checked = true;
            else
                item.checked = false;
            if (this.isAnyGroupWrite(parent_group) && !this.isAllGroupWrite(parent_group))
                group_item.setAttribute("class","expandable_cell3");            
            else
                group_item.setAttribute("class","expandable_cell1");            
            
            var item = $I(this.parent_object_id+"_ShareAccessRulesTable_"+this.module_id+"_"+this.idnumber+"_hosts_"+parent_group+"_checkGroupSMB");
            var group_item = item.parentNode;
            if (this.isAllGroupSMB(parent_group))
                item.checked = true;
            else
                item.checked = false;
            if (this.isAnyGroupSMB(parent_group) && !this.isAllGroupSMB(parent_group))
                group_item.setAttribute("class","expandable_cell3");            
            else
                group_item.setAttribute("class","expandable_cell1");            
            var item = $I(this.parent_object_id+"_ShareAccessRulesTable_"+this.module_id+"_"+this.idnumber+"_hosts_"+parent_group+"_checkGroupNFS");
            var group_item = item.parentNode;
            if (this.isAllGroupNFS(parent_group))
                item.checked = true;
            else
                item.checked = false;
            if (this.isAnyGroupNFS(parent_group) && !this.isAllGroupNFS(parent_group))
                group_item.setAttribute("class","expandable_cell3");            
            else
                group_item.setAttribute("class","expandable_cell1");                        
            var item = $I(this.parent_object_id+"_ShareAccessRulesTable_"+this.module_id+"_"+this.idnumber+"_hosts_"+parent_group+"_checkGroupAFP");
            var group_item = item.parentNode;
            if (this.isAllGroupAFP(parent_group))
                item.checked = true;
            else
                item.checked = false;
            if (this.isAnyGroupAFP(parent_group) && !this.isAllGroupAFP(parent_group))
                group_item.setAttribute("class","expandable_cell3");            
            else
                group_item.setAttribute("class","expandable_cell1");                        
        }
        var ev = new Array;
        ev["target"] = this.opener_item;
        if (this.opener_object!=null)
            this.opener_object.onChange(ev);
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (elems[c].id == target.id && elems[c]!=target && elems[c].checked!=target_checked) {
                elems[c].checked = target_checked;
                ev["target"] = elems[c];
                this.checkSMB(ev,notCheckAll);
            }
        }        
        if (!notCheckAll)
            this.checkAllChecked();
    },
    
    checkNFS: function(event,notCheckAll) {
        var target = eventTarget(event);
        var target_checked = target.checked;
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var c=0;
        var afpCheck=null;
        var smbCheck=null;
        var nfsCheck=null;
        for (c=0;c<elems.length;c++) {
            if (!target.checked) {
                if (elems[c]["id"]==getElementById(this.node,target.id.replace("NFS","SMB")).id) {
                    smbCheck=elems[c];
                    nfsCheck=target;
                }      
                if (elems[c]["id"]==getElementById(this.node,target.id.replace("NFS","AFP")).id) {
                    afpCheck=elems[c];
                    nfsCheck=target;
                    var td1 = elems[c].parentNode.previousSibling.previousSibling.previousSibling;                    
                    var writeCheck=td1.getElementsByTagName("input");
                    writeCheck=writeCheck[0];
                    td1 = td1.previousSibling;                    
                    var readCheck=td1.getElementsByTagName("input");
                    readCheck=readCheck[0];                    
                    if (!smbCheck.checked && !nfsCheck.checked && !afpCheck.checked) {
                        writeCheck.checked=false;
                        readCheck.checked=false;
                    }                    
                }      
            } else {
                if (elems[c]["id"]==getElementById(this.node,target.id.replace("NFS","SMB")).id) {
                    smbCheck=elems[c];
                    nfsCheck=target;
                    var td1 = elems[c].parentNode.previousSibling;                    
                    var writeCheck=td1.getElementsByTagName("input");
                    writeCheck=writeCheck[0];
                    td1 = td1.previousSibling;                    
                    var readCheck=td1.getElementsByTagName("input");
                    readCheck=readCheck[0];                    
                    if (!readCheck.checked && !writeCheck.checked) {
                        readCheck.checked=true;
                    }                    
                }                      
                if (elems[c]["id"]==getElementById(this.node,target.id.replace("NFS","AFP")).id) {
                    afpCheck=elems[c];
                    nfsCheck=target;
                    var td1 = elems[c].parentNode.previousSibling;                    
                    var writeCheck=td1.getElementsByTagName("input");
                    writeCheck=writeCheck[0];
                    td1 = td1.previousSibling;                    
                    var readCheck=td1.getElementsByTagName("input");
                    readCheck=readCheck[0];                    
                    if (!readCheck.checked && !writeCheck.checked) {
                        readCheck.checked=true;
                    }                    
                }                      
            }                
        }
        var type = this.object_id.split("_").pop();
        if (type=="hosts" && target.getAttribute("column")!="defaultNFS") {            
            var parent_group = target.getAttribute("parent_group");
            var item = $I(this.parent_object_id+"_ShareAccessRulesTable_"+this.module_id+"_"+this.idnumber+"_hosts_"+parent_group+"_checkGroupRead");
            var group_item = item.parentNode;
            if (this.isAllGroupRead(parent_group))
                item.checked = true;
            else
                item.checked = false;
            if (this.isAnyGroupRead(parent_group) && !this.isAllGroupRead(parent_group))
                group_item.setAttribute("class","expandable_cell3");            
            else
                group_item.setAttribute("class","expandable_cell1");            
            var item = $I(this.parent_object_id+"_ShareAccessRulesTable_"+this.module_id+"_"+this.idnumber+"_hosts_"+parent_group+"_checkGroupWrite");
            var group_item = item.parentNode;
            if (this.isAllGroupWrite(parent_group))
                item.checked = true;
            else
                item.checked = false;
            if (this.isAnyGroupWrite(parent_group) && !this.isAllGroupWrite(parent_group))
                group_item.setAttribute("class","expandable_cell3");            
            else
                group_item.setAttribute("class","expandable_cell1");            
            
            var item = $I(this.parent_object_id+"_ShareAccessRulesTable_"+this.module_id+"_"+this.idnumber+"_hosts_"+parent_group+"_checkGroupSMB");
            var group_item = item.parentNode;
            if (this.isAllGroupSMB(parent_group))
                item.checked = true;
            else
                item.checked = false;
            if (this.isAnyGroupSMB(parent_group) && !this.isAllGroupSMB(parent_group))
                group_item.setAttribute("class","expandable_cell3");            
            else
                group_item.setAttribute("class","expandable_cell1");            
            var item = $I(this.parent_object_id+"_ShareAccessRulesTable_"+this.module_id+"_"+this.idnumber+"_hosts_"+parent_group+"_checkGroupNFS");
            var group_item = item.parentNode;
            if (this.isAllGroupNFS(parent_group))
                item.checked = true;
            else
                item.checked = false;
            if (this.isAnyGroupNFS(parent_group) && !this.isAllGroupNFS(parent_group))
                group_item.setAttribute("class","expandable_cell3");            
            else
                group_item.setAttribute("class","expandable_cell1");                        
            var item = $I(this.parent_object_id+"_ShareAccessRulesTable_"+this.module_id+"_"+this.idnumber+"_hosts_"+parent_group+"_checkGroupAFP");
            var group_item = item.parentNode;
            if (this.isAllGroupAFP(parent_group))
                item.checked = true;
            else
                item.checked = false;
            if (this.isAnyGroupAFP(parent_group) && !this.isAllGroupAFP(parent_group))
                group_item.setAttribute("class","expandable_cell3");            
            else
                group_item.setAttribute("class","expandable_cell1");                        
        }
        var ev = new Array;
        ev["target"] = this.opener_item;
        if (this.opener_object!=null)
            this.opener_object.onChange(ev);
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (elems[c].id == target.id && elems[c]!=target && elems[c].checked!=target_checked) {
                elems[c].checked = target_checked;
                ev["target"] = elems[c];
                this.checkNFS(ev,notCheckAll);
            }
        } 
        if (!notCheckAll)
            this.checkAllChecked();   
    },

    checkAFP: function(event,notCheckAll) {
        var target = eventTarget(event);
        var target_checked = target.checked;
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var afpCheck=null;
        var smbCheck=null;
        var nfsCheck=null;
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (!target.checked) {
                if (elems[c]["id"]==getElementById(this.node,target.id.replace("AFP","SMB")).id) {
                    smbCheck=elems[c];
                    afpCheck=target;
                }      
                if (elems[c]["id"]==getElementById(this.node,target.id.replace("AFP","NFS")).id) {
                    nfsCheck=elems[c];
                    afpCheck=target;
                    var td1 = elems[c].parentNode.previousSibling.previousSibling;                    
                    var writeCheck=td1.getElementsByTagName("input");
                    writeCheck=writeCheck[0];
                    td1 = td1.previousSibling;                    
                    var readCheck=td1.getElementsByTagName("input");
                    readCheck=readCheck[0];                    
                    if (!smbCheck.checked && !nfsCheck.checked && !afpCheck.checked) {
                        writeCheck.checked=false;
                        readCheck.checked=false;
                    }                    
                }      
            } else {
                if (elems[c]["id"]==getElementById(this.node,target.id.replace("AFP","SMB")).id) {
                    smbCheck=elems[c];
                    nfsCheck=target;
                    var td1 = elems[c].parentNode.previousSibling;                    
                    var writeCheck=td1.getElementsByTagName("input");
                    writeCheck=writeCheck[0];
                    td1 = td1.previousSibling;                    
                    var readCheck=td1.getElementsByTagName("input");
                    readCheck=readCheck[0];                    
                    if (!readCheck.checked && !writeCheck.checked) {
                        readCheck.checked=true;
                    }                    
                }                      
                if (elems[c]["id"]==getElementById(this.node,target.id.replace("AFP","NFS")).id) {
                    afpCheck=elems[c];
                    nfsCheck=target;
                    var td1 = elems[c].parentNode.previousSibling;                    
                    var writeCheck=td1.getElementsByTagName("input");
                    writeCheck=writeCheck[0];
                    td1 = td1.previousSibling;                    
                    var readCheck=td1.getElementsByTagName("input");
                    readCheck=readCheck[0];                    
                    if (!readCheck.checked && !writeCheck.checked) {
                        readCheck.checked=true;
                    }                    
                }                      
            }                
        }
        var type = this.object_id.split("_").pop();
        if (type=="hosts" && target.getAttribute("column")!="defaultAFP") {            
            var parent_group = target.getAttribute("parent_group");
            var item = $I(this.parent_object_id+"_ShareAccessRulesTable_"+this.module_id+"_"+this.idnumber+"_hosts_"+parent_group+"_checkGroupRead");
            var group_item = item.parentNode;
            if (this.isAllGroupRead(parent_group))
                item.checked = true;
            else
                item.checked = false;
            if (this.isAnyGroupRead(parent_group) && !this.isAllGroupRead(parent_group))
                group_item.setAttribute("class","expandable_cell3");            
            else
                group_item.setAttribute("class","expandable_cell1");            
            var item = $I(this.parent_object_id+"_ShareAccessRulesTable_"+this.module_id+"_"+this.idnumber+"_hosts_"+parent_group+"_checkGroupWrite");
            var group_item = item.parentNode;
            if (this.isAllGroupWrite(parent_group))
                item.checked = true;
            else
                item.checked = false;
            if (this.isAnyGroupWrite(parent_group) && !this.isAllGroupWrite(parent_group))
                group_item.setAttribute("class","expandable_cell3");            
            else
                group_item.setAttribute("class","expandable_cell1");            
            
            var item = $I(this.parent_object_id+"_ShareAccessRulesTable_"+this.module_id+"_"+this.idnumber+"_hosts_"+parent_group+"_checkGroupSMB");
            var group_item = item.parentNode;
            if (this.isAllGroupSMB(parent_group))
                item.checked = true;
            else
                item.checked = false;
            if (this.isAnyGroupSMB(parent_group) && !this.isAllGroupSMB(parent_group))
                group_item.setAttribute("class","expandable_cell3");            
            else
                group_item.setAttribute("class","expandable_cell1");            
            var item = $I(this.parent_object_id+"_ShareAccessRulesTable_"+this.module_id+"_"+this.idnumber+"_hosts_"+parent_group+"_checkGroupNFS");
            var group_item = item.parentNode;
            if (this.isAllGroupNFS(parent_group))
                item.checked = true;
            else
                item.checked = false;
            if (this.isAnyGroupNFS(parent_group) && !this.isAllGroupNFS(parent_group))
                group_item.setAttribute("class","expandable_cell3");            
            else
                group_item.setAttribute("class","expandable_cell1");                        
            var item = $I(this.parent_object_id+"_ShareAccessRulesTable_"+this.module_id+"_"+this.idnumber+"_hosts_"+parent_group+"_checkGroupAFP");
            var group_item = item.parentNode;
            if (this.isAllGroupAFP(parent_group))
                item.checked = true;
            else
                item.checked = false;
            if (this.isAnyGroupAFP(parent_group) && !this.isAllGroupAFP(parent_group))
                group_item.setAttribute("class","expandable_cell3");            
            else
                group_item.setAttribute("class","expandable_cell1");                        
        }
        var ev = new Array;
        ev["target"] = this.opener_item;
        if (this.opener_object!=null)
            this.opener_object.onChange(ev);
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (elems[c].id == target.id && elems[c]!=target && elems[c].checked!=target_checked) {
                elems[c].checked = target_checked;
                ev["target"] = elems[c];
                this.checkAFP(ev,notCheckAll);
            }
        } 
        if (!notCheckAll)
            this.checkAllChecked();   
    },
    
    checkWrite: function(event,notCheckAll) {
        var target = eventTarget(event);
        var target_checked = target.checked;
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (target.checked) {
                if (elems[c]["id"]==getElementById(this.node,target.id.replace("Write","Read")).id)
                    elems[c].checked = target_checked;
                if (this.type=="hosts") {
                    if (elems[c]["id"]==getElementById(this.node,target.id.replace("Write","SMB")).id) {
                        var td1 = elems[c].parentNode.nextSibling;
                        var el1=td1.getElementsByTagName("input");
                        el1=el1[0];
                        var td1 = elems[c].parentNode.nextSibling.nextSibling;
                        var el2=td1.getElementsByTagName("input");
                        el2=el2[0];
                        if (el1!=null && el2!=null) {
                            if (!el1.checked && !elems[c].checked && !el2.checked) {
                                el1.checked=true;
                                el2.checked=true;
                                elems[c].checked=true;
                            }                    
                        }
                    }   
                }
            }
        }        
        if (target.checked) {
            getElementById(this.node,target.id.replace("Write","Read")).checked = target.checked;
        }
        var type = this.object_id.split("_").pop();
        if (type=="hosts" && target.getAttribute("column")!="defaultWrite") {            
            var parent_group = target.getAttribute("parent_group");
            var item = $I(this.parent_object_id+"_ShareAccessRulesTable_"+this.module_id+"_"+this.idnumber+"_hosts_"+parent_group+"_checkGroupRead");
            var group_item = item.parentNode;
            if (this.isAllGroupRead(parent_group))
                item.checked = true;
            else
                item.checked = false;
            if (this.isAnyGroupRead(parent_group) && !this.isAllGroupRead(parent_group))
                group_item.setAttribute("class","expandable_cell3");            
            else
                group_item.setAttribute("class","expandable_cell1");            
            var item = $I(this.parent_object_id+"_ShareAccessRulesTable_"+this.module_id+"_"+this.idnumber+"_hosts_"+parent_group+"_checkGroupWrite");
            var group_item = item.parentNode;
            if (this.isAllGroupWrite(parent_group))
                item.checked = true;
            else
                item.checked = false;
            if (this.isAnyGroupWrite(parent_group) && !this.isAllGroupWrite(parent_group))
                group_item.setAttribute("class","expandable_cell3");            
            else
                group_item.setAttribute("class","expandable_cell1");            
            
            var item = $I(this.parent_object_id+"_ShareAccessRulesTable_"+this.module_id+"_"+this.idnumber+"_hosts_"+parent_group+"_checkGroupSMB");
            var group_item = item.parentNode;
            if (this.isAllGroupSMB(parent_group))
                item.checked = true;
            else
                item.checked = false;
            if (this.isAnyGroupSMB(parent_group) && !this.isAllGroupSMB(parent_group))
                group_item.setAttribute("class","expandable_cell3");            
            else
                group_item.setAttribute("class","expandable_cell1");            
            var item = $I(this.parent_object_id+"_ShareAccessRulesTable_"+this.module_id+"_"+this.idnumber+"_hosts_"+parent_group+"_checkGroupNFS");
            var group_item = item.parentNode;
            if (this.isAllGroupNFS(parent_group))
                item.checked = true;
            else
                item.checked = false;
            if (this.isAnyGroupNFS(parent_group) && !this.isAllGroupNFS(parent_group))
                group_item.setAttribute("class","expandable_cell3");            
            else
                group_item.setAttribute("class","expandable_cell1");                        
            var item = $I(this.parent_object_id+"_ShareAccessRulesTable_"+this.module_id+"_"+this.idnumber+"_hosts_"+parent_group+"_checkGroupAFP");
            var group_item = item.parentNode;
            if (this.isAllGroupAFP(parent_group))
                item.checked = true;
            else
                item.checked = false;
            if (this.isAnyGroupAFP(parent_group) && !this.isAllGroupAFP(parent_group))
                group_item.setAttribute("class","expandable_cell3");            
            else
                group_item.setAttribute("class","expandable_cell1");                        
        }
        var ev = new Array;
        ev["target"] = this.opener_item;
        if (this.opener_object!=null)
            this.opener_object.onChange(ev);
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (elems[c].id == target.id && elems[c]!=target && elems[c].checked!=target_checked) {
                elems[c].checked = target_checked;
                ev["target"] = elems[c];
                this.checkWrite(ev,notCheckAll);
            }
        }        
        if (!notCheckAll)
            this.checkAllChecked();
    },

    expandGroup: function(event,group_number) {
        var elems = $I(this.node.id+"_table").getElementsByTagName('tr');
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("parent_group")==group_number) {
                if (elems[c].style.display == '')
                    elems[c].style.display = 'none';
                else
                    elems[c].style.display = '';
            }
        }
    },
    
    syncCheckboxes: function() {
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (elems[c].getAttribute("type")=='checkbox' && elems[c].getAttribute("column")=='hostRead' || elems[c].getAttribute("type")=='checkbox' && elems[c].getAttribute("column")=='defaultRead' ) {
            	if (elems[c].checked)
            		elems[c].setAttribute("value","checked");
            	else
            		elems[c].setAttribute("value","");
            }
        }
    },

    getChanged: function() {
        var res = new Array;
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var c=0;
        for (c=0;c<elems.length;c++) {
            var add = false;
            if (elems[c].getAttribute("type")=='checkbox' && elems[c].getAttribute("column")=='hostRead' || elems[c].getAttribute("type")=='checkbox' && elems[c].getAttribute("column")=='defaultRead' ) {
                var readonly="";var smb="";var nfs="";var afp="";
                if (elems[c].checked && elems[c].getAttribute("value")!="checked") {
                    add = true;
                    if (getElementById(this.node,elems[c].id.replace("Read","Write")).checked)
                        readonly = "no";
                    else
                        readonly = "yes";
                    if (getElementById(this.node,elems[c].id.replace("Read","NFS")).checked)
                        nfs="nfs";
                    else
                        nfs="";
                    if (getElementById(this.node,elems[c].id.replace("Read","SMB")).checked)
                        smb="smb";
                    else
                        smb="";
                    if (getElementById(this.node,elems[c].id.replace("Read","AFP")).checked)
                        afp="afp";
                    else
                        afp="";
                }
                if (!getElementById(this.node,elems[c].id.replace("Read","Write")).checked && getElementById(this.node,elems[c].id.replace("Read","Write")).getAttribute("value")=="checked") {
                    add = true;
                    readonly = "yes";
                    if (getElementById(this.node,elems[c].id.replace("Read","NFS")).checked)
                        nfs="nfs";
                    else
                        nfs="";
                    if (getElementById(this.node,elems[c].id.replace("Read","AFP")).checked)
                        afp="afp";
                    else
                        afp="";
                    if (getElementById(this.node,elems[c].id.replace("Read","SMB")).checked)
                        smb="smb";
                    else
                        smb="";
                }
                if (getElementById(this.node,elems[c].id.replace("Read","Write")).checked && getElementById(this.node,elems[c].id.replace("Read","Write")).getAttribute("value")!="checked") {
                    add = true;
                    readonly = "no";
                    if (getElementById(this.node,elems[c].id.replace("Read","NFS")).checked)
                        nfs="nfs";
                    else
                        nfs="";
                    if (getElementById(this.node,elems[c].id.replace("Read","SMB")).checked)
                        smb="smb";
                    else
                        smb="";
                    if (getElementById(this.node,elems[c].id.replace("Read","AFP")).checked)
                        afp="afp";
                    else
                        afp="";
                }                
                if (!elems[c].checked && elems[c].getAttribute("value")=="checked") {
                    add = true;
                    readonly = "delete";
                     if (getElementById(this.node,elems[c].id.replace("Read","NFS")).checked)
                        nfs="nfs";
                    else
                        nfs="";
                    if (getElementById(this.node,elems[c].id.replace("Read","SMB")).checked)
                        smb="smb";
                    else
                        smb="";
                    if (getElementById(this.node,elems[c].id.replace("Read","AFP")).checked)
                        afp="afp";
                    else
                        afp="";
               }
               if (!getElementById(this.node,elems[c].id.replace("Read","SMB")).checked && getElementById(this.node,elems[c].id.replace("Read","SMB")).getAttribute("value")=="checked") {
                    add = true;                    
                    smb = "";
                    if (readonly=="") {
                        if (getElementById(this.node,elems[c].id.replace("Read","Write")).checked)
                            readonly = "no";
                        else
                            readonly = "yes";
                    }
                    if (getElementById(this.node,elems[c].id.replace("Read","NFS")).checked)
                        nfs="nfs";
                    else
                        nfs="";
                    if (getElementById(this.node,elems[c].id.replace("Read","AFP")).checked)
                        afp="afp";
                    else
                        afp="";
                }
                if (getElementById(this.node,elems[c].id.replace("Read","SMB")).checked && getElementById(this.node,elems[c].id.replace("Read","SMB")).getAttribute("value")!="checked") {
                    add = true;
                    smb = "smb";
                    if (readonly=="") {
                        if (getElementById(this.node,elems[c].id.replace("Read","Write")).checked)
                            readonly = "no";
                        else
                            readonly = "yes";
                    }
                    if (getElementById(this.node,elems[c].id.replace("Read","NFS")).checked)
                        nfs="nfs";
                    else
                        nfs="";
                    if (getElementById(this.node,elems[c].id.replace("Read","AFP")).checked)
                        afp="afp";
                    else
                        afp="";
                }                                
                if (!getElementById(this.node,elems[c].id.replace("Read","NFS")).checked && getElementById(this.node,elems[c].id.replace("Read","NFS")).getAttribute("value")=="checked") {
                    add = true;                    
                    nfs = "";
                    if (readonly=="") {
                        if (getElementById(this.node,elems[c].id.replace("Read","Write")).checked)
                            readonly = "no";
                        else
                            readonly = "yes";
                    }
                    if (getElementById(this.node,elems[c].id.replace("Read","SMB")).checked)
                        smb="smb";
                    else
                        smb="";
                    if (getElementById(this.node,elems[c].id.replace("Read","AFP")).checked)
                        afp="afp";
                    else
                        afp="";
                }
                if (getElementById(this.node,elems[c].id.replace("Read","NFS")).checked && getElementById(this.node,elems[c].id.replace("Read","NFS")).getAttribute("value")!="checked") {
                    add = true;
                    nfs = "nfs";
                    if (readonly=="") {
                        if (getElementById(this.node,elems[c].id.replace("Read","Write")).checked)
                            readonly = "no";
                        else
                            readonly = "yes";
                    }
                    if (getElementById(this.node,elems[c].id.replace("Read","SMB")).checked)
                        smb="smb";
                    else
                        smb="";
                    if (getElementById(this.node,elems[c].id.replace("Read","AFP")).checked)
                        afp="afp";
                    else
                        afp="";
                }                                
                if (!getElementById(this.node,elems[c].id.replace("Read","AFP")).checked && getElementById(this.node,elems[c].id.replace("Read","AFP")).getAttribute("value")=="checked") {
                    add = true;                    
                    afp = "";
                    if (readonly=="") {
                        if (getElementById(this.node,elems[c].id.replace("Read","Write")).checked)
                            readonly = "no";
                        else
                            readonly = "yes";
                    }
                    if (getElementById(this.node,elems[c].id.replace("Read","SMB")).checked)
                        smb="smb";
                    else
                        smb="";
                    if (getElementById(this.node,elems[c].id.replace("Read","NFS")).checked)
                        nfs="nfs";
                    else
                        nfs="";
                }
                if (getElementById(this.node,elems[c].id.replace("Read","AFP")).checked && getElementById(this.node,elems[c].id.replace("Read","AFP")).getAttribute("value")!="checked") {
                    add = true;
                    afp = "afp";
                    if (readonly=="") {
                        if (getElementById(this.node,elems[c].id.replace("Read","Write")).checked)
                            readonly = "no";
                        else
                            readonly = "yes";
                    }
                    if (getElementById(this.node,elems[c].id.replace("Read","SMB")).checked)
                        smb="smb";
                    else
                        smb="";
                    if (getElementById(this.node,elems[c].id.replace("Read","NFS")).checked)
                        nfs="nfs";
                    else
                        nfs="";
                }                                
                if (add) {
                    if (elems[c].getAttribute("type")=='checkbox' && elems[c].getAttribute("column")=='hostRead') {
                        if (res.indexOf(elems[c].getAttribute("id").replace(this.parent_object_id+"_ShareAccessRulesTable_"+this.module_id+"_"+this.idnumber+"_"+this.type+"_","").replace("_checkRead",""))==-1) {
                            var val = "";
                            if (readonly!="")
                                val += elems[c].getAttribute("id").replace(this.parent_object_id+"_ShareAccessRulesTable_"+this.module_id+"_"+this.idnumber+"_"+this.type+"_","").replace("_checkRead","")+"="+readonly;
                            val += ","+smb;
                            val += ","+nfs;
                            val += ","+afp;
                            if (val.search("=")!=-1)
                                res[res.length] = val;
                        }
                    }
                    else {
                        var val = "";
                        if (readonly!="")
                            val += "default="+readonly;
                        val += ","+smb;
                        val += ","+nfs;
                        val += ","+afp;
                        if (val.search("=")!=-1)
                            res[res.length] = val;
                    }
                }
            }
        }
        return res.join("|");
    },

    getChecked: function() {
        var res = new Array;
        var elems = $I(this.node.id+"_table").getElementsByTagName('input');
        var read_found = false;
        var write_found = false;
        var default_read_found = false;
        var default_write_found = false;
        var read = "";
        var write = "";
        var c=0;
        var column = "";
        var id="";
        for (c=0;c<elems.length;c++) {
            if (elems[c].id!=null) {
                id = elems[c].id.replace("FileShare_"+this.module_id+"_"+this.idnumber+"_","").split("_").shift();
                column = elems[c].getAttribute("column");
            } else {
                 id = "";
                 column = "";
            }
            if (elems[c].type == "checkbox" && column=="hostRead") {
                if (elems[c].checked)
                    read = "r";
                read_found = true;
            }
            if (elems[c].type == "checkbox" && column=="hostWrite") {
                if (elems[c].checked)
                    write = "w";
                write_found = true;
            }
            if (elems[c].type == "checkbox" && column=="defaultRead") {
                if (elems[c].checked)
                    read = "r";
                default_read_found = true;
            }
            if (elems[c].type == "checkbox" && column=="defaultWrite") {
                if (elems[c].checked)
                    write = "w";
                default_write_found = true;
            }
            if (read_found && write_found) {
                res[res.length] = id+"~"+read+write;
                read_found = false;write_found = false;read="";write = "";
            }
            if (default_read_found && default_write_found) {
                res[res.length] = "default~"+read+write;
                default_read_found = false;default_write_found = false;read="";write = "";
            }
        }
        return res.join("|");
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
        if (this.type=="hosts") {
            var elems = $I(this.node.id+"_table").getElementsByTagName('tr');
            var tr = elems[0];
            var td1 = tr.getElementsByTagName("td");
            td1=td1[0];
            if (td1.nextSibling==null)
                return 0;
            var readTd = td1.nextSibling.nextSibling;
            var writeTd = readTd.nextSibling;        
            var smbTd = writeTd.nextSibling;
            var nfsTd = smbTd.nextSibling;
            var afpTd = nfsTd.nextSibling;
            var readCheck = readTd.getElementsByTagName("input");readCheck=readCheck[0];
            var writeCheck = writeTd.getElementsByTagName("input");writeCheck=writeCheck[0];
            var smbCheck = smbTd.getElementsByTagName("input");smbCheck=smbCheck[0];
            var nfsCheck = nfsTd.getElementsByTagName("input");nfsCheck=nfsCheck[0];
            var afpCheck = afpTd.getElementsByTagName("input");afpCheck=afpCheck[0];
            readCheck.checked = (this.checkChecked("hostRead") && this.checkChecked("defaultRead"));
            writeCheck.checked = (this.checkChecked("hostWrite") && this.checkChecked("defaultWrite"));
            smbCheck.checked = (this.checkChecked("hostSMB") && this.checkChecked("defaultSMB"));
            nfsCheck.checked = (this.checkChecked("hostNFS") && this.checkChecked("defaultNFS"));
            afpCheck.checked = (this.checkChecked("hostAFP") && this.checkChecked("defaultAFP"));
        } else {
            var elems = $I(this.node.id+"_table").getElementsByTagName('tr');
            var tr = elems[0];
            var td1 = tr.getElementsByTagName("td");
            td1=td1[0];
            var readTd = td1.nextSibling.nextSibling;
            var writeTd = readTd.nextSibling;        
            var readCheck = readTd.getElementsByTagName("input");readCheck=readCheck[0];
            var writeCheck = writeTd.getElementsByTagName("input");writeCheck=writeCheck[0];
            readCheck.checked = (this.checkChecked("hostRead") && this.checkChecked("defaultRead"));
            writeCheck.checked = (this.checkChecked("hostWrite") && this.checkChecked("defaultWrite"));            
        }
    }    
});