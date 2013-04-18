//include scripts/handlers/mail/Mailbox.js

mbox.hosts_access_rules_table = '{hosts_access_rules_table}';
mbox.users_access_rules_table = '{users_access_rules_table}';
mbox.groups_access_rules_table = '{groups_access_rules_table}';
mbox.tabset_id = '{tabset_id}';
mbox.idnumber = '{idnumber}';
mbox.shares_root = '{shares_root}';
mbox.skinPath = '{skinPath}';
if (mbox.idnumber=="0") {
    $I(mbox.node.id+"_name").disabled = true;
    $I(mbox.node.id+"_path").disabled = true;
    $I(mbox.node.id+"_sharepath_selectButton").disabled = true;
}
mbox.parent_object_id = '{parent_object_id}';
mbox.usersFrameSrc = "{usersFrameSrc}";
mbox.groupsFrameSrc = "{groupsFrameSrc}";