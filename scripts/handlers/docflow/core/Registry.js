//include scripts/handlers/core/WABEntity.js
entity.currentReport = '{defaultReport}';
entity.argums = '{argums}';
entity.currentPrintProfile = '{defaultPrintProfile}';
entity.template = '{template}';
if (entity.template!="renderForm") {
	entity.setPrintProfile(entity.currentReport,entity.currentPrintProfile);
}