//include scripts/handlers/core/WABEntity.js
entity.tbl = $O("FTPUsersTable_{module_id}_{objectid}");
if (entity.tbl!=null) {
	entity.tbl.build(true);
};
entity.tbl2 = $O("FTPActiveUsersTable_{module_id}_{objectid}");
if (entity.tbl2!=null) {
	entity.tbl2.build(true);
};