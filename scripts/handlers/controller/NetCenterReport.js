//include scripts/handlers/mail/Mailbox.js
mbox.tabset_id = '{tabset_id}';
mbox.conditions_string = '{condition_fields}';
mbox.conditions_string = mbox.conditions_string.replace(/\\'/g,"'");

mbox.conditions = new Array;
arr = mbox.conditions_string.split('|');
for (var c=0;c<arr.length;c++) {
    parts = arr[c]  .split("^");
    mbox.conditions[parts[0]] = parts[1];
}
mbox.last_field_name = "";