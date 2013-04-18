//include scripts/handlers/mail/Mailbox.js
mbox.tabset_id = '{tabset_id}';
mbox.host_types_ids = '{host_types_ids}'; mbox.host_types_ids = mbox.host_types_ids.split(',');
mbox.host_types_icons = '{host_types_icons}'; mbox.host_types_icons = mbox.host_types_icons.split(',');
mbox.access_rules_table = '{access_rules_table}';
mbox.parent_object_id = '{parent_object_id}';
mbox.objectid = '{objectid}';
$O("PortsRedirectTable_{clientObjectId}").build();
