//include scripts/handlers/mail/Mailbox.js
tbl = $O(object_id,instance_id);
tbl.idnumber = "{idnumber}";
tbl.table_rows = '{table_rows}';
tbl.table_rows = tbl.table_rows.replace(/xox/g,"'").replace(/yoy/g,"\n").replace(/zoz/g,'"').replace(/oao/g,',');
tbl.table_rows.replace('\"','"');
tbl.parent_object_id = '{parent_object_id}';
tbl.build();