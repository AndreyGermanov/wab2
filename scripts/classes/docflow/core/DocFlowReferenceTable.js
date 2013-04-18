var DocFlowReferenceTable = Class.create(DocFlowDocumentTable, {
	
    getClassName: function() {
    	return "DocFlowReferenceTable";
    },	

	onLoad: function() {
		$I(this.node.id+"_registerButton").style.display = "none";
		$I(this.node.id+"_unregisterButton").style.display = "none";
		if ($I(this.node.id+"_registryButton")!=0)
			$I(this.node.id+"_registryButton").style.display = "none";
		if (this.objRole["canCreateBy"]=="false")
			$I(this.node.id+"_createbyButton").style.display = 'none';
		
		$I(this.node.id+"_datesButton").style.display = "none";		
		if (this.objRole["canPrintList"]=="false")
			$I(this.node.id+"_printButton").style.display = 'none';
		if (this.objRole["canSetProperties"]=="false")
			$I(this.node.id+"_optionsButton").style.display = 'none';
		if (this.objRole["canFilter"]=="false")
			$I(this.node.id+"_filterButton").style.display = 'none';
		if (this.objRole["canUnfilter"]=="false")
			$I(this.node.id+"_deleteFilterButton").style.display = 'none';
		if (this.objRole["canSaveListSettings"]=="false") {
			$I(this.node.id+"_saveSettingsButton").style.display = 'none';
			$I(this.node.id+"_newProfileButton").style.display = 'none';			
			$I(this.node.id+"_deleteProfileButton").style.display = 'none';	
			if ($I(this.node.id+"_profilesList_value")!=0 && $I(this.node.id+"_profilesList_value").options.length==1) {
				$I(this.node.id+"_profilesList_value").style.display = 'none';
			}
		}
		if (this.objRole["canAdd"]=="false")
			$I(this.node.id+"_addButton").style.display = 'none';
		if (this.objRole["canGlobalSearch"]=="false")
			$I(this.node.id+"_globalSearch").style.display = 'none';
		if (this.objRole["canAddCopy"]=="false")
			$I(this.node.id+"_copyButton").style.display = 'none';
		if (this.objRole["canEdit"]=="false")
			$I(this.node.id+"_insertButton").style.display = 'none';		
		if (this.objRole["canDelete"]=="false")
			$I(this.node.id+"_deleteButton").style.display = 'none';		
		if (this.objRole["canUndelete"]=="false")
			$I(this.node.id+"_undeleteButton").style.display = 'none';	
		if (!this.hierarchy || this.objRole["canAddGroup"]=="false") {
			$I(this.node.id+"_addGroupButton").style.display = 'none';
			$I(this.node.id+"_groupList").style.display = 'none';
		}		
		else {
			if (this.showHierarchy) {
				$I(this.node.id+"_hierarchyButtonOn").style.display = '';
				$I(this.node.id+"_hierarchyButtonOff").style.display = 'none';
			} else {
				$I(this.node.id+"_hierarchyButtonOn").style.display = 'none';
				$I(this.node.id+"_hierarchyButtonOff").style.display = '';			
			}			
		}		
		if (this.topLinkObject=="" || this.topLinkObject==null || this.topLinkRole["canEditLinks"]=="false") {
			$I(this.node.id+"_linkButton").style.display = 'none';	
			$I(this.node.id+"_unlinkButton").style.display = 'none';
		}
		if (this.helpButtonDisplay=="none")
			if ($I(this.node.id+"_helpButton")!=null && $I(this.node.id+"_helpButton").style!=null)
				$I(this.node.id+"_helpButton").style.display = 'none';			
	}	
});