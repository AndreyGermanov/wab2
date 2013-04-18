var PagePanel = Class.create(Mailbox, {

    build: function() {
        if (this.parent_object==0 || this.parent_object==null)
            return 0;
        if (this.num_pages<2)
            return 0;
        var elemm = $I(this.node.id+"_pagepanel");
        for (var ccc=1;ccc<=this.num_pages;ccc++) {
            var link = document.createElement("A");
            link.setAttribute("object",this.object_id);
            link.id = this.node.id+"_p"+ccc;
            link.href = "#";
            link.innerHTML = ccc;
            if (ccc==this.current_page)
                link.setAttribute("class",'selected_page');
            else
                link.setAttribute("class",'page');
            link.setAttribute("click","changePage");
            link.observe("click",this.addHandler);
            elemm.appendChild(link);
            var span = document.createElement("SPAN");
            span.innerHTML = '&nbsp;';
            elemm.appendChild(span);
            if (this.current_target == null)
                this.current_target = link;
        }
    },

    changePage: function(event,page_num) {
      var elem = eventTarget(event);
      var page_num = elem.id.replace(this.node.id+"_p","");
      var tbl_object  = this.parent_object.node.getAttribute("object");
      var args = new Object;
      args["page_number"] = page_num;
      args["row_count"] = this.items_per_page;
      args["display_fields"] = this.display_fields;
      var page_panel = this;
      new Ajax.Request("index.php", {
        method:"post",
        parameters: {ajax: true, object_id: tbl_object,hook:'3',arguments: Object.toJSON(args)},
        onSuccess: function(transport)
        {
            var response = trim(transport.responseText.replace("\n",""));
            page_panel.parent_object.cells_data = response.replace('XOXOXO','\n');
            page_panel.parent_object.build();
            page_panel.current_target.setAttribute('class','page');
            page_panel.current_target = eventTarget(event);
            page_panel.current_target.setAttribute('class','selected_page');
        }
      });
    }
});