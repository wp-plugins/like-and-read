function tinyplugin() {
    return "[like_read_plugin]";
}
(function() {
  tinymce.create('tinymce.plugins.like_read_plugin', {
	  init : function(ed, url){
      ed.addButton('like_read_plugin', {
        title : 'Like and Read shortcode',
        onclick : function() {
			ilc_sel_content = tinyMCE.activeEditor.selection.getContent();
			tinyMCE.activeEditor.selection.setContent('[like-and-read]' + ilc_sel_content + '[/like-and-read]')
        },
        image: url + "/../images/like_read.png"
      });
	  }
});
tinymce.PluginManager.add('like_read_plugin', tinymce.plugins.like_read_plugin);
})();