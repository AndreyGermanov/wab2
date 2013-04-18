{object_id}tbl = $O(object_id,instance_id);
{object_id}tbl.win='{window_id}';
{object_id}tbl.module_id='{module_id}';

{object_id}tbl.table = $I({object_id}tbl.node.id+"_table");
wm = getWindowManager();
if (wm!=null) {
    if ({object_id}tbl.win!="")
    {
        {object_id}tbl.win = wm.windows['{window_id}'];
    }
}
{object_id}tbl.parentTabset = '{parentTabset}';
{object_id}tbl.parent_object_id = '{parent_object_id}';
{object_id}tbl.name = '{name}';
{object_id}tbl.skinPath = '{skinPath}';
{object_id}tbl.properties = "bgcolor=#000000,cellpadding=0,cellspacing=1,width=100%";
{object_id}tbl.sortOrder = "{sortOrder}";
{object_id}tbl.condition = "{condition}";
{object_id}tbl.childCondition = '{childCondition}';
{object_id}tbl.additionalCondition = "{additionalCondition}";
{object_id}tbl.tagsCondition = "{tagsCondition}";
{object_id}tbl.parentEntity = "{parentEntity}";
{object_id}tbl.className = "{className}";
{object_id}tbl.classNameForReg = {object_id}tbl.className.replace(/\*/g,".*");
{object_id}tbl.defaultClassName = '{defaultClassName}';
if ({object_id}tbl.defaultClassName=="")
    {object_id}tbl.defaultClassName = {object_id}tbl.className;
{object_id}tbl.fieldList = "{fieldList}";
{object_id}tbl.allFieldList = "{allFieldList}";
{object_id}tbl.printFieldList = "{printFieldList}";
{object_id}tbl.conditionFields = "{conditionFields}";
{object_id}tbl.sortField = "{sortField}";
{object_id}tbl.adapterId = '{adapterId}';
{object_id}tbl.additionalFields = '{additionalFields}';
{object_id}tbl.currentControl = 0;
{object_id}tbl.itemsPerPage = {itemsPerPage};
{object_id}tbl.currentPage = {currentPage};
{object_id}tbl.hierarchy = {hierarchy};
{object_id}tbl.showHierarchy = {showHierarchy};
{object_id}tbl.pagePanelId = '{pagePanelId}';
{object_id}tbl.numPages = '{numPages}';
{object_id}tbl.staticNumPages = {object_id}tbl.numPages;
{object_id}tbl.module_id = '{module_id}';
{object_id}tbl.collection = '{collection}';
{object_id}tbl.collectionLoadMethod = '{collectionLoadMethod}';
{object_id}tbl.collectionGetMethod = '{collectionGetMethod}';
{object_id}tbl.fieldAccess = '{fieldAccessStr}';
{object_id}tbl.receiveBarCodes = '{receiveBarCodes}';

if ({object_id}tbl.fieldAccess[0]=="{")
	{object_id}tbl.fieldAccess - {object_id}tbl.fieldAccess.evalJSON();
{object_id}tbl.fieldDefaults = '{fieldDefaultsStr}'; 
if ({object_id}tbl.fieldDefaults[0]=="{")
{object_id}tbl.fieldDefaults = {object_id}tbl.fieldDefaults.evalJSON();
{object_id}tbl.entityImagesStr = '{entityImagesStr}'; 
if ({object_id}tbl.entityImagesStr[0]=="{")
	{object_id}tbl.entityImages = {object_id}tbl.entityImagesStr.evalJSON();
{object_id}tbl.additionalLinksStr = '{additionalLinksStr}'; 
if ({object_id}tbl.additionalLinksStr[0]=="{")
	{object_id}tbl.additionalLinks = {object_id}tbl.additionalLinksStr.evalJSON();
{object_id}tbl.topLinkObject = '{topLinkObject}';
{object_id}tbl.ownerObject = '{ownerObject}';
{object_id}tbl.selectGroup = '{selectGroup}';
{object_id}tbl.entityImage = '{entityImage}';
{object_id}tbl.entityGroupImage = '{groupImage}';

{object_id}tbl.editorType = '{editorType}';
{object_id}tbl.windowWidth = {windowWidth};
{object_id}tbl.windowHeight = {windowHeight};
{object_id}tbl.tableClassName = '{tableClassName}';
{object_id}tbl.classTitle = '{classTitle}';
{object_id}tbl.windowTitle = "{windowTitle}";

{object_id}tbl.divName = "{divName}";
{object_id}tbl.destroyDiv = {destroyDivStr};

{object_id}tbl.tableId = "{tableId}";
{object_id}tbl.forEntitySelect = {forEntitySelectStr};
{object_id}tbl.entityId = '{entityId}';
{object_id}tbl.autoload = '{autoload}';
{object_id}tbl.defaultListProfile = '{defaultListProfile}';
{object_id}tbl.tble = '{table}';

if ({object_id}tbl.autoload=="false")
	{object_id}tbl.autoload = false;
else
{object_id}tbl.autoload = true;

{data}

{object_id}tbl.rows = new Array;
if (this.autoload) {
	{object_id}tbl.rows = {object_id}Rows;
	{object_id}tbl.entityCount = {object_id}EntityCount;
}
if ($I({object_id}tbl.node.id+"_insertButton") != null && $I({object_id}tbl.node.id+"_insertButton") != 0 && $I({object_id}tbl.node.id+"_insertButton")!="") {
    $I({object_id}tbl.node.id+"_insertButton").src = {object_id}tbl.skinPath+'images/Buttons/changeButton.png';
    $I({object_id}tbl.node.id+"_insertButton").setAttribute("title","Изменить");
    $I({object_id}tbl.node.id+"_insertButton").setAttribute("alt","Изменить");
}
{object_id}tbl = $O(object_id,instance_id);
if ({object_id}tbl.entityId!='' && {object_id}tbl.entityId!='-1') {
    {object_id}tbl.entityControl = {object_id}tbl.findControlByValue({object_id}tbl.entityId);
    if ({object_id}tbl.entityControl!=0 && {object_id}tbl.entityControl!=null) {
        {object_id}tbl.entityRow = {object_id}tbl.entityControl.node.parentNode.getAttribute("row");
        {object_id}tbl.entityControl = {object_id}tbl.getItem({object_id}tbl.entityRow,2);
        if ({object_id}tbl.entityControl!=0 && {object_id}tbl.entityControl!=null) {
            {object_id}tbl.entityControl.setFocus();
            {object_id}tbl.entityControl.node.scrollIntoView(true);
        }
    }
};
{object_id}tbl = $O(object_id,instance_id);
args = new Object;
args["items_per_page"] = '{itemsPerPage}';
args["window_id"] = '{window_id}';
args["current_page"] = "{currentPage}";
args["num_pages"] = "{numPages}";
args["parent_item"] = "{object_id}";
args["parent_object_id"] = "{object_id}"; 
{object_id}tbl.frameSrc = "index.php?object_id={pagePanelId}&hook=show&arguments="+Object.toJSON(args);
{object_id}tbl.onLoad();
if ({object_id}tbl.autoload)
	{object_id}tbl.rebuild();