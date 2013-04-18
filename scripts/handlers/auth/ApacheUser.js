//include scripts/handlers/mail/Mailbox.js
obj.appconfig = $O('{appconfigTab}','');
obj.rolesTable = $O('{rolesTable}','');
obj.rolesTable.build();
obj.moduleTitles = new Array('{modulesString}');
obj.modulesTable = $O('{modulesTable}','');
obj.modulesTable.build();
obj.modules = new Array;
obj.banned = +'{banned}';
for (var o=0;o<obj.moduleTitles.length;o++) {
	obj.modules[obj.moduleTitles[o]] = $O(obj.moduleTitles[o],'');
}