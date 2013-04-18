var ContextMenu = Class.create(Entity, {

    load: function() {
        var menu = getElementById(this.node,this.node.id+'_table');

        var items = menu.getAttribute("collection").split('|');
        var ids = items[0].split(',');
        var titles = items[1].split(',');
        var template = getElementById(this.node,this.node.id+'_row');
        template.style.display = 'none';
        for (var counter=0;counter<ids.length;counter++)
        {
            var new_node = template.cloneNode(true);
            new_node.id = this.full_object_id+"_"+ids[counter]+"_row";
            new_node.style.display = '';            
            var elems = new_node.getElementsByTagName('*');
            for (var counter1=0;counter1<elems.length;counter1++)
            {
                if (elems[counter1].id == this.node.id+'_td')
                {
                    elems[counter1].id = this.node.id+'_'+ids[counter]+'_td';
                    elems[counter1].setAttribute("object",this.object_id);
                    elems[counter1].setAttribute('click',"onClick");
                    elems[counter1].observe('click',this.addHandler);
                }
                if (elems[counter1].id == this.node.id+'_text')
                {
                    elems[counter1].id = this.node.id+'_'+ids[counter]+'_text';
                    elems[counter1].innerHTML = titles[counter];
                    elems[counter1].setAttribute("object",this.object_id);
                    elems[counter1].setAttribute('click',"onClick");
                    elems[counter1].observe('click',this.addHandler);
                }
                elems[counter1].style.display = '';
            }
            menu.appendChild(new_node);
        }
    }
});