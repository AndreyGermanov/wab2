//include scripts/handlers/core/WABEntity.js
if ('{template_file}'!="")
    entity.putFileContent('{template_file}',"template_text");
if ('{css_file}'!="")
    entity.putFileContent('{css_file}',"css_text");
if ('{handler_file}'!="")
    entity.putFileContent('{handler_file}',"handler_text");
if ('{class_file}'!="")
    entity.putFileContent('{class_file}',"class_text");
//editAreaLoader.toggle(entity.object_id+"_template_text_value","off");
//editAreaLoader.toggle(entity.object_id+"_template_text_value","on");