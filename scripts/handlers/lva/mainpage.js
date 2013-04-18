//include scripts/handlers/WABEntity.js

         var prevScrollY = 0;
         var firstTop = 0;
         var currentMenu = null;
         var currentTitle = "";
         
         function checkMainMenu() {
            var pos = getElementPosition('{object_id}_menu_table');            
            if (getScrollY()<=firstTop) {
                var el = document.getElementById('{object_id}_menu_table');
                el.style.top = firstTop;
                document.getElementById('{object_id}_menu_table').style.position = "";
                document.getElementById('{object_id}_menu_table').setAttribute("class","");
                if (currentMenu!=null && currentMenu.node.style.display!='none') {
                    var poss = getElementPosition(currentTitle);
                    var leftt = poss.left;
                    var topp =poss.top+poss.height+3;
                    $O("Menu_MainMenu","").showSubMenu(leftt,topp,currentMenu.object_id);                    
                }
                return 0;
            }
            if (getScrollY()<prevScrollY) {
                document.getElementById('{object_id}_menu_table').style.top = pos.top-(prevScrollY-getScrollY());
                prevScrollY = getScrollY();                
            }
            if (getScrollY()-pos.top>0) {
                document.getElementById('{object_id}_menu_table').style.top = pos.top+(getScrollY()-pos.top);
                document.getElementById('{object_id}_menu_table').style.position = "absolute";
                document.getElementById('{object_id}_menu_table').setAttribute("class","menu_table");
                el = document.getElementById('{object_id}_menu_table');
                el.style.left = pos.left;  
                prevScrollY = getScrollY();
            }
            if (currentMenu!=null && currentMenu.node.style.display!='none') {
                    var poss = getElementPosition(currentTitle);
                    var leftt = poss.left;
                    var topp =poss.top+poss.height+3;
                    $O("Menu_MainMenu","").raiseEvent("ON_CLICK");
                    $O("Menu_MainMenu","").showSubMenu(leftt,topp,currentMenu.object_id);                    
            }
        }
        
        function onload(event) {
            pos = getElementPosition('{object_id}_menu_table');
            firstTop = pos.top;
        }

    if ($O("Menu_MainMenu","")!=null) {
        $O("Menu_MainMenu","").aboutMenu_onMouseOver = function(event) {
            var pos = getElementPosition(eventTarget(event).getAttribute("item_id"));
            var leftt = pos.left;
            var topp = pos.top+pos.height;
            currentMenu=$O("Menu_MainMenu","").showEntitySubMenu(leftt,topp,"EntityMenu_topMenu");
            currentTitle="EntityMenu_MainMenu_topMenu";
        };

        $O("Menu_MainMenu","").productsMenu_onMouseOver = function(event) {
            var pos = getElementPosition(eventTarget(event).getAttribute("item_id"));
            var leftt = pos.left;
            var topp = pos.top+pos.height;
            currentMenu=$O("Menu_MainMenu","").showSubMenu(leftt,topp,"Menu_ProductsMenu");
            currentTitle="Menu_MainMenu_productsMenu";
        };
        $O("Menu_MainMenu","").build();
    }
    if ($O("Menu_ProductsMenu","")!=null) {
        $O("Menu_ProductsMenu","").workstationsMenu_onMouseOver = function(event) {
            var pos = getElementPosition(eventTarget(event).getAttribute("item_id"));
            var leftt = pos.left+pos.width+3;
            var topp = pos.top;
            if ($O(eventTarget(event).getAttribute("object"),"")!=null) {
                $O(eventTarget(event).getAttribute("object"),"").showSubMenu(leftt,topp,"Menu_WorkstationsMenu");
            }
        };
        $O("Menu_ProductsMenu","").build();
    }
    if ($O("Menu_WorkstationsMenu","")!=null) {
        $O("Menu_WorkstationsMenu","").desktopsMenu_onClick = function(event) {
            alert("CLICKED");
        };
    };
