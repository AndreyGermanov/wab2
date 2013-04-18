//include scripts/handlers/core/WABEntity.js
obj = entity;
obj.hasChildren = {hasChildrenStr};
obj.siteId = {siteId};
obj.asAdminTemplate = {asAdminTemplateStr};
obj.siteName = '{siteName}';

obj.systemTabsetName = '{systemTabsetName}';
obj.systemTabset = $O('{systemTabsetName}');
if (obj.systemTabset!=0 && obj.systemTabset!=null)
    obj.systemTabset.activateTab('main');

obj.fieldsTabsetName = '{fieldsTabsetName}';
obj.fieldsTabset = $O('{fieldsTabsetName}');
if (obj.fieldsTabset!=0 && obj.fieldsTabset!=null)
    obj.fieldsTabset.activateTab('fieldsMain');

obj.cachedepsTabsetName = '{cachedepsTabsetName}';
obj.cachedepsTabset = $O('{cachedepsTabsetName}');
if (obj.cachedepsTabset!=0 && obj.cachedepsTabset!=null)
    obj.cachedepsTabset.activateTab('cacheDepsMain');

obj.rightsTabsetName = '{rightsTabset_id}';
obj.rightsTabset = $O('{rightsTabset_id}');
if (obj.rightsTabset!=0 && obj.rightsTabset!=null)
    obj.rightsTabset.activateTab('usersRights');

obj.currentTabset = $O('{tabsetName}');
if (obj.currentTabset!=0 && obj.fieldsTabset!=null)
    if (obj.hasChildren) {    	
		obj.currentTabset.activateTab('childrenTable');
    }
    else {
        if (obj.active_tab=="" || obj.active_tab=='childrenTable')
            obj.currentTabset.activateTab('text');        
        else
            obj.currentTabset.activateTab(obj.active_tab);        
    }
    
if ($I(entity.node.id+"_tagsTable")!=0 && typeof {tagsTableId}tbl != "undefined")
	$I(entity.node.id+"_tagsTable").value = {tagsTableId}tbl.getSingleValue();

if ($I(entity.node.id+"_usersRightsTable")!=0 && typeof {usersRightsTableId}tbl != "undefined")
	$I(entity.node.id+"_usersRightsTable").value = {usersRightsTableId}tbl.getSingleValue();

if ($I(entity.node.id+"_rolesRightsTable")!=0 && typeof {rolesRightsTableId}tbl != "undefined")
	$I(entity.node.id+"_rolesRightsTable").value = {rolesRightsTableId}tbl.getSingleValue();