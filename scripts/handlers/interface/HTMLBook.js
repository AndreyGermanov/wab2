//include scripts/handlers/mail/Mailbox.js
obj = $O(object_id,instance_id);
{toc_js}
obj.current_id = '{current_id}';
obj.doc_path = '{doc_path}';
obj.history = new Array;
obj.history[obj.history.length] = obj.current_id;