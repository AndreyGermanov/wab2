var ReferenceContragents = Class.create(Reference, {
	
	CONTROL_VALUE_CHANGED_processEvent: function(params) {
		if (params["object_id"]==this.node.id+"_type") {
			if (params["value"]=="1") {
				$I(this.node.id+"_titleField").innerHTML = "Наименование";
				$I(this.node.id+"_fizDocumentRow").style.display = "none";
				$I(this.node.id+"_KPPRow").style.display = "";
			} else {
				$I(this.node.id+"_titleField").innerHTML = "ФИО";
				$I(this.node.id+"_fizDocumentRow").style.display = "";
				$I(this.node.id+"_KPPRow").style.display = "none";				
			}
		}
	},

	TAB_CHANGED_processEvent: function($super,params) {

		if (params["tabset_id"]==this.tabsetName) {
			if (params["tab"]=="banks") {
				if (!this.accountsTabLoaded) {
					var tbl = $O(this.bankAccountsTableId,'');
					tbl.fieldAccess = this.accountsTableFieldAccess;
					tbl.fieldDefaults = this.accountsTableFieldDefaults;
					tbl.entityImages = this.entityImages;
					tbl.rebuild();
					this.accountsTabLoaded = true;
				}
			}
			if (params["tab"]=="emails") {
				if (!this.emailsTabLoaded) {
					var tbl = $O(this.emailsTableId,'');
					tbl.entityImages = this.emailEntityImages;
					tbl.rebuild();
					this.emailsTabLoaded = true;
				}
			}
			if (params["tab"]=="quickSales") {
				if (!this.quickSaleTabLoaded) {
					var tbl = $O(this.quickSaleTableId,'');
					tbl.fieldAccess = this.quickSaleTableFieldAccess;
					tbl.fieldDefaults = this.quickSaleTableFieldDefaults;
					tbl.entityImages = this.entityImages;
					tbl.rebuild();
					this.quickSaleTabLoaded = true;
				}
			}
		}
		$super(params);
	},
		
	setDefaultAccount_onClick: function(event) {		
		 var tbl = $O(this.bankAccountsTableId,'');
		 if (tbl.currentControl!=null && tbl.currentControl!=0) { 
			 var row = tbl.currentControl.node.parentNode.getAttribute("row");
			 var it = tbl.getItem(row,0);
			 if (it!=0) {
				 $I(this.node.id+"_defaultBankAccount").value = it.getValue();
				 var entImages = new Object;
				 entImages[it.getValue()] = this.skinPath+"images/Buttons/RegisteredDocumentEntityImage.png";
				 this.entityImages = entImages;
				 tbl.entityImages = entImages;
				 tbl.rebuild();
			 }
		 }				 
	},
	
	setDefaultEmail_onClick: function(event) {		
		 var tbl = $O(this.emailsTableId,'');
		 if (tbl.currentControl!=null && tbl.currentControl!=0) { 
			 var row = tbl.currentControl.node.parentNode.getAttribute("row");
			 var it = tbl.getItem(row,0);
			 if (it!=0) {
				 $I(this.node.id+"_defaultEmail").value = it.getValue();
				 var entImages = new Object;
				 entImages[it.getValue()] = this.skinPath+"images/Buttons/RegisteredDocumentEntityImage.png";
				 this.entityImages = entImages;
				 tbl.entityImages = entImages;
				 tbl.rebuild();
			 }
		 }				 
	},
	
    button_onMouseDown: function(event) {
        var src = eventTarget(event).src;
        var arr = src.split("/");
        src = arr.pop();
        src = src.split(".");
        var ext = src[1];
        src = src[0];
        src = src.split("_");
        if (src.length>1) {
            src = src.shift();
        }
        else
            src = src.join("_");
        src = arr.join("/")+"/"+src+"_clicked."+ext;
        eventTarget(event).src = src;
    },

    button_onMouseUp: function(event) {
        var src = eventTarget(event).src;
        var arr = src.split("/");
        src = arr.pop();
        src = src.split(".");
        var ext = src[1];
        src = src[0];
        src = src.split("_");
        if (src.length>1) {
            src = src.shift();
        } else
            src = src.join("_");
        src = arr.join("/")+"/"+src+"."+ext;
        eventTarget(event).src = src;
    },

    button_onMouseOver: function(event) {
        var src = eventTarget(event).src;
        var arr = src.split("/");
        src = arr.pop();
        src = src.split(".");
        var ext = src[1];
        src = src[0];
        src = src.split("_");
        if (src.length>1) {
            src = src.shift();
        }
        else
            src = src.join("_");
        src = arr.join("/")+"/"+src+"_hover."+ext;
        eventTarget(event).src = src;
    },

    button_onMouseOut: function(event) {
        var src = eventTarget(event).src;
        var arr = src.split("/");
        src = arr.pop();
        src = src.split(".");
        var ext = src[1];
        src = src[0];
        src = src.split("_");
        if (src.length>1) {
            src = src.shift();
        } else
            src = src.join("_");
        src = arr.join("/")+"/"+src+"."+ext;
        eventTarget(event).src = src;
    },
    
    printButton_onClick: function(event) {
		$I(this.node.id+"_discountReportFrame").contentWindow.print();
	},
	
	refreshButton_onClick: function(event) {
		$I(this.node.id+"_discountReportFrame").src = $I(this.node.id+"_discountReportFrame").src;
	}			
});