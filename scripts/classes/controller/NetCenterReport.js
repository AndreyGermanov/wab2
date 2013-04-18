var NetCenterReport = Class.create(Mailbox,{

    onRemoveWindow: function (topWindow) {
        delete topWindow.objects.objects[this.tabset_id];
        delete topWindow.objects.objects["NetCenterFieldValuesSelectTable_"+this.module_id+"_Values"];
    },

    selectedFieldsMoveUp_onClick: function(event) {
        move_select_item($I(this.node.id+"_selected_fields").selectedIndex,$I(this.node.id+"_selected_fields"),"up");
    },

    selectedFieldsMoveDown_onClick: function(event) {
        move_select_item($I(this.node.id+"_selected_fields").selectedIndex,$I(this.node.id+"_selected_fields"),"down");
    },

    selectedFieldsMoveLeft_onClick: function(event) {
        move_select_item_to_list($I(this.node.id+"_not_selected_fields").selectedIndex,$I(this.node.id+"_not_selected_fields"),$I(this.node.id+"_selected_fields"));
    },

    selectedFieldsMoveRight_onClick: function(event) {
        move_select_item_to_list($I(this.node.id+"_selected_fields").selectedIndex,$I(this.node.id+"_selected_fields"),$I(this.node.id+"_not_selected_fields"));
    },

    sortFieldsMoveUp_onClick: function(event) {
        move_select_item($I(this.node.id+"_sort_fields").selectedIndex,$I(this.node.id+"_sort_fields"),"up");
    },

    sortFieldsMoveDown_onClick: function(event) {
        move_select_item($I(this.node.id+"_sort_fields").selectedIndex,$I(this.node.id+"_sort_fields"),"down");
    },

    sortFieldsMoveLeft_onClick: function(event) {
        move_select_item_to_list($I(this.node.id+"_not_sort_fields").selectedIndex,$I(this.node.id+"_not_sort_fields"),$I(this.node.id+"_sort_fields"));
    },

    sortFieldsMoveRight_onClick: function(event) {
        move_select_item_to_list($I(this.node.id+"_sort_fields").selectedIndex,$I(this.node.id+"_sort_fields"),$I(this.node.id+"_not_sort_fields"));
    },

    groupFieldsMoveUp_onClick: function(event) {
        move_select_item($I(this.node.id+"_group_fields").selectedIndex,$I(this.node.id+"_group_fields"),"up");
    },

    groupFieldsMoveDown_onClick: function(event) {
        move_select_item($I(this.node.id+"_group_fields").selectedIndex,$I(this.node.id+"_group_fields"),"down");
    },

    groupFieldsMoveLeft_onClick: function(event) {
        move_select_item_to_list($I(this.node.id+"_not_group_fields").selectedIndex,$I(this.node.id+"_not_group_fields"),$I(this.node.id+"_group_fields"));
    },

    groupFieldsMoveRight_onClick: function(event) {
        move_select_item_to_list($I(this.node.id+"_group_fields").selectedIndex,$I(this.node.id+"_group_fields"),$I(this.node.id+"_not_group_fields"));
    },

    getConditionValues: function() {
        var tbl = $O("NetCenterFieldValuesSelectTable_"+this.module_id+"_Values");
        tbl = $I(tbl.node.id+"_table");
        var elems = tbl.getElementsByTagName("input");
        var arr = new Array;
        var c=0;
        for (c=0;c<elems.length;c++) {
            if (elems[c].checked)
                arr[arr.length] = elems[c].id;
        }
        return arr.join('~');
    },

    condition_fields_onChange: function(event) {
        var list = eventTarget(event);
        var topWindow = globalTopWindow;
        var field_name = list.options[list.selectedIndex].value;
        if (this.last_field_name!=field_name && this.last_field_name!="") {
            this.conditions[this.last_field_name] = this.getConditionValues();
        }
        delete topWindow.objects.objects["NetCenterFieldValuesSelectTable_"+this.module_id+"_Values"];
        this.last_field_name = field_name;
        if (this.conditions[field_name]!=null)
            values_list = "$object->values_list='"+this.conditions[field_name]+"';";
        else
            values_list = "";
        var args = new Object;
        args["field_name"] = field_name;
        $I(this.node.id+"_condition_select_frame").src = "?object_id=NetCenterFieldValuesSelectTable_"+this.module_id+"_Values&hook=show&arguments="+Object.toJSON(args);
    },

    onChange: function(event) {
        return 0;
    },

    report_onClick: function(event) {
      if ($I(this.node.id+"_selected_fields").options.length<=0) {
          this.reportMessage('Не выбрано полей для отчета !',"error",true);
          return 0;
      }
      if ($I(this.node.id+"_sort_fields").options.length<=0) {
          this.reportMessage('Не выбрано полей для сортировки !',"error",true);
          return 0;
      }
      var args = new Object;
      
      if ($I(this.node.id+"_table_report").checked) {
    	  args["report_type"] = 'table';
      } else {
    	  args["report_type"] = "list";
      }
      var selected_fields = new Object;
      var c=0;
      for (c=0;c<$I(this.node.id+"_selected_fields").options.length;c++) {
    	  selected_fields[c] = $I(this.node.id+"_selected_fields").options[c].value;
      }

      var sort_fields = new Object;
      for (c=0;c<$I(this.node.id+"_sort_fields").options.length;c++) {
    	  sort_fields[c] = $I(this.node.id+"_sort_fields").options[c].value;
      }
      var group_fields = new Object;
      for (c=0;c<$I(this.node.id+"_group_fields").options.length;c++) {
    	  group_fields[c] = $I(this.node.id+"_group_fields").options[c].value;
      }
      var list = $I(this.node.id+"_condition_fields");
      if (list.selectedIndex>-1) {
          field_name = list.options[list.selectedIndex].value;
          this.conditions[field_name] = this.getConditionValues();
      }

      var condition_fields = new Object;
      for (c in this.conditions) {
          if (this.conditions[c]==null)
            continue;
          if (typeof(this.conditions[c])!="string")
            continue;
          condition_fields[c] = this.conditions[c];
      }
      args["selected_fields"] = selected_fields;
      args["sort_fields"] = sort_fields;
      args["group_fields"] = group_fields;
      args["condition_fields"] = condition_fields;
      var obj = this;
      new Ajax.Request("index.php", {
          method:"post",
          parameters: {ajax: true, object_id: "NetCenterReport_"+this.module_id+"_Report",hook: '3', arguments: Object.toJSON(args)},
          onSuccess: function(transport)
          {
            var response = trim(transport.responseText.replace("\n",""));
            if (response.length>1)
            {
                response = response.evalJSON(true);
                if (response["error"]!=null)
                {
                    obj.reportMessage(response["error"],"error",true);
                    return 0;
                }
                else
                    obj.reportMessage(response,"error",true);
            }
            else {
                window.open("tmp/report.odt");
            }
         }
     });
  },

  help_onClick: function(event) {
      var params = new Array;
      getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"guide6.6","HTMLBook_"+this.module_id+"_controller_6.6",params,this.opener_item.getAttribute("object"),this.opener_item.id);
  }
});