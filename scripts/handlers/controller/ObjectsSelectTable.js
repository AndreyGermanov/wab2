//include scripts/handlers/mail/Mailbox.js
tbl = $O(object_id,instance_id);
tbl.table_rows = "{table_rows}";
tbl.table_rows.replace('\"','"');
tbl.build();