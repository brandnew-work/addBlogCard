tinymce.create('tinymce.plugins.blogcard', {
  init: function(ed, url) {
    ed.addButton('blog_card', {
      title: 'ブログカード',
      text: 'ブログカード',
      type: 'menubutton',
      menu: [
        {
          text: '簡易',
          onclick: function () {
            ed.insertContent('[blog_card url=""]');
          }
        },
        {
          text: 'カテゴリー等一覧',
          onclick: function () {
            ed.insertContent('[blog_card url="" tax="category ea etc" id="category_id"]');
          }
        },
        {
          text: 'フル',
          onclick: function () {
            ed.insertContent('[blog_card url="" title="タイトル" excerpt="説明" thumb="media_id"]');
          }
        }
      ]
    });
  },
  createControl : function(n, cm) {
    return null;
  },
});
tinymce.PluginManager.add('blogcard_plugin', tinymce.plugins.blogcard);
