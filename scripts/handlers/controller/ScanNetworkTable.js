//include scripts/handlers/mail/Mailbox.js
mbox = $O(object_id,instance_id);
mbox.module_id='{module_id}';

mbox.opener_item = $I(getClientId('{opener_item}'));
opener_object = mbox.opener_item.getAttribute("object");
opener_instance = mbox.opener_item.getAttribute("instance");
mbox.opener_object = $O(opener_object,opener_instance);
window.onmousedown = mbox.onMouseDown;
window.onchange = mbox.onChange;

mbox.network_table = '{network_table}';
mbox.subnet = '{subnet}';
mbox.parent_object_id = '{parent_object_id}';
mbox.build();