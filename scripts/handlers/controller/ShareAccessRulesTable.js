//include scripts/handlers/mail/Mailbox.js
tabl = $O(object_id,instance_id);
tabl.idnumber = "{idnumber}";
tabl.type = "{type}";
tabl.table_rows = "{table_rows}";
tabl.table_rows = tabl.table_rows.replace(/xox/g,"'").replace(/yoy/g,"\n").replace(/zoz/g,'"').replace(/oao/g,',');
tabl.table_rows = tabl.table_rows.replace('\"','"');

tabl.build();
tabl.checkAllChecked();