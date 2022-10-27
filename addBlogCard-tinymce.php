<?php
/*----------------------------------------------------------
  追加ボタン設定（別途 addBlogCard.jsにて設定必要）
----------------------------------------------------------*/

// 作成したプラグインを登録 ------------------------------------------------
function register_mce_blogcard_plugins( $plugin_array ) {
  if(is_child_theme()) {
		$js = get_stylesheet_directory_uri().'/functions/addBlogCard/addBlogCard.js';
  } else {
		$js = get_template_directory_uri().'/functions/addBlogCard/addBlogCard.js';
  }
  $plugin_array[ 'blogcard_plugin' ] = $js;
  return $plugin_array;
}
add_filter( 'mce_external_plugins', 'register_mce_blogcard_plugins' );


// プラグインで作ったボタンを登録 ------------------------------------------------
function add_blogcard_buttons( $buttons ) {
  $buttons[] = 'blog_card';
  return $buttons;
}
add_filter( 'mce_buttons', 'add_blogcard_buttons' );

?>
