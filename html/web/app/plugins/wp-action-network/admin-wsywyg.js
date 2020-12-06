(function() {

    var action_network_shortcode_list = actionnetwork_shortcode;
    var action_new_array = [];
    var count = 0;
    var key = 0;

    for ( key in actionnetwork_shortcode ) {
        count++;
    }

    function action_net_list( editor ){

        var tempArray = [];


        for ( var i = 1; i <= count; i++ ) {

            var $this = action_network_shortcode_list[i];

            tempArray.push({
                text     : action_network_shortcode_list[i][1],
                content  : action_network_shortcode_list[i][0],
                onclick  : function(  ) {
                    editor.insertContent('[actionnetwork id='+ this.settings.content +']');
                }
            });
        }

       return tempArray;
    }

     tinymce.PluginManager.add('wdm_mce_button', function( editor, url ) {
         editor.addButton( 'wdm_mce_button', {
             text        : 'Action Network',
             type        : 'menubutton',
             menu        : action_net_list( editor )
         });
     });
})();