(function() {
    tinymce.create('tinymce.plugins.SoccerInfo', {
        init : function(ed, url) {

            ed.addCommand('mceSoccerInfo', function() {
                ed.windowManager.open({
					file: si_site_url,
                    width : 500 + ed.getLang('SoccerInfo.delta_width', 0),
                    height : 360 + ed.getLang('SoccerInfo.delta_height', 0),
                    inline : 1
                }, {
                    plugin_url : url
                });
            });

            // Register button
            ed.addButton('SoccerInfo', {
                title : 'SoccerInfo',
                cmd : 'mceSoccerInfo',
                image : url + '/football.png',
				onPostRender: function() {
					var cm = this;
			
					ed.on('NodeChange', function(e) {
						cm.active(e.element.nodeName == 'IMG');
					});
				}
            });

            // Add a node change handler, selects the button in the UI when a image is selected
            //ed.onNodeChange.add(function(ed, cm, n) {
            //    cm.setActive('SoccerInfo', n.nodeName == 'IMG');
            //});
        },
        
        createControl : function(n, cm) {
            return null;
        },

        getInfo : function() {
            return {
                    longname  : 'SoccerInfo',
                    author    : 'Szilard Mihaly',
                    authorurl : 'http://www.mihalysoft.com/',
                    infourl   : 'http://www.mihalysoft.com/wordpress-plugins/soccer-info/',
                    version   : '1.9.5'
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('SoccerInfo', tinymce.plugins.SoccerInfo);
})();