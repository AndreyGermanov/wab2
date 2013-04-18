var DataTablePagePanel = Class.create(PagePanel, {

    changePage: function(event) {
      var elem = eventTarget(event);
      var page_num = elem.id.replace(this.node.id+"_p","");
      this.parent_object.currentPage = page_num;
      this.parent_object.rebuild();
      page_panel.current_target.setAttribute('class','page');
      page_panel.current_target = eventTarget(event);
      page_panel.current_target.setAttribute('class','selected_page');
    }
});