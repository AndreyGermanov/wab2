//include scripts/handlers/interface/Tree.js
tree.className = '{className}';
tree.defaultClassName = '{defaultClassName}';
if (tree.defaultClassName=='')
    tree.defaultClassName = tree.className;
tree.classNameForReg = tree.className;
tree.treeClassName = '{treeClassName}';
tree.hierarchy = '{hierarchy}';
tree.condition = "{condition}";
tree.sortOrder = '{sortOrder}';
tree.childClassName = '{childClassName}';
tree.childCondition = '{childCondition}';
tree.entityImage = '{entityImage}';
tree.groupEntityImage = '{groupEntityImage}';
tree.titleField = '{titleField}';
tree.additionalFields = '{additionalFields}';
tree.contextMenuClass = '{contextMenuClass}';
tree.rootContextMenuClass = '{rootContextMenuClass}';
tree.editorType = '{editorType}';
tree.selectGroup = '{selectGroup}';
tree.windowWidth = {windowWidth};
tree.windowHeight = {windowHeight};
tree.entityId = '{entityId}';
tree.adapterId = '{adapterId}';
tree.entityParentStr = '{entityParentStr}';
tree.windowTitle = "{windowTitle}";
tree.hide_root_context_menu = {hide_root_context_menu};
tree.result_object_id = '{result_object_id}';
tree.divName = "{divName}";
tree.destroyDiv = {destroyDivStr};

tree.tableId = "{tableId}";
tree.forEntitySelect = {forEntitySelectStr};
if (tree.entityParentStr!='') {
    if (!tree.loaded) {
        tree.fillTree();
    }
}
    tree.selectCurrentEntity();