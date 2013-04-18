var ReferenceFirms = Class.create(Reference, {
	
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
	}		
});