//include scripts/handlers/core/WABEntity.js
entity.old_name = '{old_name}';
entity.old_title = '{old_title}';
entity.old_file = '{old_file}';
entity.rnd = '{rnd}';
entity.group = '{group}';
entity.fullGroup = '{fullGroup}';
entity.fieldsTable = $O('{fieldsTable}','');
entity.fieldsTable.build();
entity.old_fields = entity.fieldsTable.getSingleValue();
entity.groupsTable = $O('{groupsTable}','');
entity.groupsTable.build();
entity.old_groups = entity.groupsTable.getSingleValue();