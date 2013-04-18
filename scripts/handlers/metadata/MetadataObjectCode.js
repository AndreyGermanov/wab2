//include scripts/handlers/core/WABEntity.js
entity.old_name = '{old_name}';
entity.old_title = '{old_title}';
entity.old_file = '{old_file}';
entity.rnd = '{rnd}';
entity.group = '{group}';
entity.fullGroup = '{fullGroup}';
entity.paramsTable = $O('{paramsTable}','');
if (entity.paramsTable!=null)
	entity.paramsTable.build();
entity.testTable = $O('{testTable}','');
if (entity.testTable!=null)
	entity.testTable.build();