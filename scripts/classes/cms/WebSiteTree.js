var WebSiteTree = Class.create(EntityTree, {
    dispatchEvent: function($super,event,params) {
        if (event=="ENTITY_ADDED" || event=="ENTITY_CHANGED") {
            var arr = params["object_id"].split("_");
            arr.pop();
            var siteId = arr.pop();
            if (siteId!=this.siteId) {
                return 0;
            }
        }
        $super(event,params);
    }
})