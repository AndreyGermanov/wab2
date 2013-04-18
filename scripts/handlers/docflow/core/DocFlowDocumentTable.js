{object_id}tbl = $O(object_id,instance_id);
{object_id}tbl.periodStart = '{periodStart}';
{object_id}tbl.periodEnd = '{periodEnd}';
{object_id}tbl.objRole = '{objRole}';
{object_id}tbl.helpGuideId = '{helpGuideId}';
{object_id}tbl.helpButtonDisplay = '{helpButtonDisplay}';
{object_id}tbl.topLinkRole = '{topLinkRole}';
{object_id}tbl.profileClass = '{profileClass}';
{object_id}tbl.classTitle = '{classTitle}';
{object_id}tbl.classListTitle = '{classListTitle}';
{object_id}tbl.showQRCode = '{showQRCode}';
{object_id}tbl.serverName = '{serverName}';

if ({object_id}tbl.objRole[0]=="{")
{object_id}tbl.objRole = {object_id}tbl.objRole.evalJSON();
if ({object_id}tbl.topLinkRole[0]=="{")
{object_id}tbl.topLinkRole = {object_id}tbl.topLinkRole.evalJSON();

//include scripts/handlers/core/EntityDataTable.js