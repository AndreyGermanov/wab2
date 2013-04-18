{object_id}tbl = $O(object_id,instance_id);
{object_id}tbl.searchText = '{searchText}';
{object_id}tbl.classesList = new Array;
{object_id}tbl.fieldsList = new Array;
{object_id}tbl.classesListStr = '{classesListStr}';
if ({object_id}tbl.classesListStr[0]=="{" || {object_id}tbl.classesListStr[0]=="[")
	{object_id}tbl.classesList = {object_id}tbl.classesListStr.evalJSON();
{object_id}tbl.fieldsListStr = '{fieldsListStr}';
if ({object_id}tbl.fieldsListStr[0]=="{" || {object_id}tbl.fieldsListStr[0]=="[")
	{object_id}tbl.fieldsList = {object_id}tbl.fieldsListStr.evalJSON();
{object_id}tbl.onLoad();
//include scripts/handlers/core/EntityDataTable.js