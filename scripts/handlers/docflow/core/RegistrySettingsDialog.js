//include scripts/handlers/core/WABEntity.js

entity.printProfileJSON = '{printProfileJSON}';
if (entity.printProfileJSON[0]=="{")
	entity.printProfile = entity.printProfileJSON.evalJSON();
else
	entity.printProfile = new Array;
entity.allFieldsJSON = '{allFieldsJSON}';
if (entity.allFieldsJSON[0]=="{")
	entity.allFields = entity.allFieldsJSON.evalJSON();
else
	entity.allFields = new Array;
entity.fillDialog();