//include scripts/handlers/core/WABEntity.js
entity.tabs = '{tabsCode}';
entity.tabsLoaded = new Array;
if (entity.tabs[0] == "{") {
	entity.tabs = entity.tabs.evalJSON();
}
entity.topLinkObject = "{topLinkObject}";
entity.ownerObject = "{ownerObject}";
entity.linksWindow = "{linksWindow}";
entity.tabTitles = "{tabTitles}";
entity.raiseEvent("TAB_CHANGED",$Arr("tabset_id="+entity.tabsetName+",tab="+entity.active_tab));