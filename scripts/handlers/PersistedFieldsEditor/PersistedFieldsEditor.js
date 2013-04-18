//include scripts/handlers/mail/Mailbox.js
obj.entity_id = '{entity_id}';
obj.control_id = '{control_id}';
obj.parent_object_id = '{control_id}';
obj.editorType = '{editorType}';
if (obj.editorType == "window")
    obj.control = window.opener.$O(obj.control_id,"");
else
    obj.control = $O(obj.control_id,"");
obj.fieldName = '{fieldName}';
obj.fill();