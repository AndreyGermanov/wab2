//include scripts/handlers/core/WABEntity.js
entity.eventsStr = '{eventsStr}';
entity.module_id = '{module_id}';
entity.events = new Array;
if (entity.eventsStr[0]=='{') {
	entity.events = entity.eventsStr.evalJSON();
}