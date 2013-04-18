var ReportBloodAnalyze = Class.create(Report, {

    button_onMouseDown: function(event) {
        var src = eventTarget(event).src;
        var arr = src.split("/");
        src = arr.pop();
        src = src.split(".");
        var ext = src[1];
        src = src[0];
        src = src.split("_");
        if (src.length>1) {
            src = src.shift();
        }
        else
            src = src.join("_");
        src = arr.join("/")+"/"+src+"_clicked."+ext;
        eventTarget(event).src = src;
    },

    button_onMouseUp: function(event) {
        var src = eventTarget(event).src;
        var arr = src.split("/");
        src = arr.pop();
        src = src.split(".");
        var ext = src[1];
        src = src[0];
        src = src.split("_");
        if (src.length>1) {
            src = src.shift();
        } else
            src = src.join("_");
        src = arr.join("/")+"/"+src+"."+ext;
        eventTarget(event).src = src;
    },

    button_onMouseOver: function(event) {
        var src = eventTarget(event).src;
        var arr = src.split("/");
        src = arr.pop();
        src = src.split(".");
        var ext = src[1];
        src = src[0];
        src = src.split("_");
        if (src.length>1) {
            src = src.shift();
        }
        else
            src = src.join("_");
        src = arr.join("/")+"/"+src+"_hover."+ext;
        eventTarget(event).src = src;
    },

    button_onMouseOut: function(event) {
        var src = eventTarget(event).src;
        var arr = src.split("/");
        src = arr.pop();
        src = src.split(".");
        var ext = src[1];
        src = src[0];
        src = src.split("_");
        if (src.length>1) {
            src = src.shift();
        } else
            src = src.join("_");
        src = arr.join("/")+"/"+src+"."+ext;
        eventTarget(event).src = src;
    },

    printButton_onClick: function(event) {
		$I(this.node.id+"_tableInnerFrame").contentWindow.print();
	},
	
	tableRefreshButton_onClick: function(event) {
		var data = this.getValues();
		if (trim(data["patient"]) == "") {
			this.reportMessage("Не заполнено поле 'Пациент'","error",true);
			return 0;
		}
		if (trim(data["def"]) == "") {
			this.reportMessage("Не заполнено поле 'Показатель'","error",true);
			return 0;
		}
		var args = data.toObject();
		args["reportType"] = "table";
		var frameSrc = "?object_id="+this.object_id+"&hook=3&arguments="+Object.toJSON(args).replace(/#/g,'');
		$I(this.object_id+"_tableInnerFrame").src = frameSrc;
	},
	
	diagramRefreshButton_onClick: function(event) {
		var data = this.getValues();
		if (trim(data["patient"]) == "") {
			this.reportMessage("Не заполнено поле 'Пациент'","error",true);
			return 0;
		}
		if (trim(data["def"]) == "") {
			this.reportMessage("Не заполнено поле 'Показатель'","error",true);
			return 0;
		}
		var args = data.toObject();
		args["reportType"] = "diagram";
		var frameSrc = "?object_id="+this.object_id+"&hook=3&arguments="+Object.toJSON(args).replace(/#/g,'');
		$I(this.object_id+"_diagramInnerFrame").src = frameSrc;
	},
	
	CONTROL_VALUE_CHANGED_processEvent: function(params) {
		if (params["object_id"] == this.object_id+"_reportType") {
			if (params["value"] == "diagram")
				$I(this.node.id+"_printButton").style.display = "none";
			else
				$I(this.node.id+"_printButton").style.display = "";
		}
	}
});