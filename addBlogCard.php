<?php

//////////////////////////////////////////////////
//  ブログカード追加
//////////////////////////////////////////////////

// 初期設定 ------------------------------------------------------------

$img_width  = 90;  // 画像サイズの幅指定
$img_height = 90;  // 画像サイズの高さ指定
$length     = 100; // post_excerptがなかった場合の文字数
$site_icon  = addBlogCard_get_template_url().'/assets/site-icon.png'; // サイトアイコンの画像URLを指定
$no_image   = addBlogCard_get_template_url().'/assets/no-image.png';  // アイキャッチ画像がない場合の画像URLを指定


// 関数設定 ------------------------------------------------------------

require_once 'addBlogCard-tinymce.php';

// stylesheet_directory, template_directoryの判別
function addBlogCard_get_template_url() {
  if(is_child_theme()) {
    return get_stylesheet_directory_uri().'/functions/addBlogCard';
  } else {
    return get_template_directory_uri().'/functions/addBlogCard';
  }
}

// css読み込み
add_action( 'wp_enqueue_scripts', 'addBlogCardScript' );
function addBlogCardScript() {
  $css = addBlogCard_get_template_url().'/addBlogCard.css';
	wp_enqueue_style( 'addBlogCard_style', $css, array() );
}


// 外部サイトの場合の情報取得
function addBlogCard_get_data( $url ) {
  $headers = array(
    "HTTP/1.0",
    "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
    "Accept-Language:ja,en-US;q=0.9,en;q=0.8",
    "Connection:keep-alive",
    "User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36"
  );
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  $result =  curl_exec($ch);
  curl_close($ch);
  $dom   =  new DOMDocument();
  $html  =  mb_convert_encoding($result, "HTML-ENTITIES", "auto");
  @$dom  -> loadHTML($html);
  $xpath =  new DOMXPath($dom);
  $xpath -> registerNamespace("php", "http://php.net/xpath");
  $xpath -> registerPHPFunctions();
  return $xpath;
}


// 不要文字列の削除
function addBlogCard_flat_text( $text ) {
  $text = strip_shortcodes($text);         // ショートコードの削除
  $text = wp_strip_all_tags($text);  // htmlの削除
  $rp   = array('　', ' ', "\xc2\xa0", "&nbsp;");
  $text = str_replace($rp, '', $text);
  return $text;
}


// 記事IDを指定して抜粋文を取得する
function addBlogCard_excerpt($post_id) {
  global $length;
  $post   = get_post($post_id);
  if($post->post_excerpt) {
    // excerptが入力されている場合
    $return = $post->post_excerpt;
  } else {
    // excerptが入力されていない場合
    $return = $post->post_content;
    $return = addBlogCard_flat_text($return);
    // $lengthよりも文字数が多い場合の対処
    if(mb_strlen($return) > $length) {
      $return = mb_substr($return, 0, $length);
      $return .= '...';
    }
  }
  return $return;
}


// 普通にget_template_partしたら変数に格納されないため用意
function addBlogCard_template( $args ) {
  ob_start();
  get_template_part( 'functions/addBlogCard/addBlogCard', 'template', $args );
  return ob_get_clean();
}


// ブログカードのメイン関数
function addBlogCard($atts) {

  // 初期設定の呼び出し ------------------------------------------------------------

    global $img_width, $img_height, $site_icon, $no_image;


  // attributeの設定 ------------------------------------------------------------

    extract(shortcode_atts(array(
      'url'       => "",
      'title'     => "",
      'excerpt'   => "",
      'thumb'     => "",
      // 'site_name' => "",
      // taxonomy用
      'tax'     => "",
      'id'      => ""
    ), $atts));

    $my_site = stristr($url, $_SERVER['HTTP_HOST']); // 別ドメインのURLか判定

  // site icon の設定 ------------------------------------------------------------

    if($my_site !== false) {
      // 同サイトの場合
      $site_name = get_bloginfo( 'name' );
      $site = isset($site_icon) ? "<img src='{$site_icon}' width='16' height='16' alt='{$site_name}'>{$site_name}" : "";
    } elseif(stristr($url, 'youtube.com') || stristr($url, 'youtu.be')) {
      // youtubeの場合
      $site_name = 'youtube';
      $site  = "<img src='".addBlogCard_get_template_url()."/assets/youtube.png' width='16' height='16' alt='{$site_name}'>{$site_name}";
    }


  // URLから投稿IDを取得 ------------------------------------------------------------

    if($my_site !== false) {
      $id   = empty($id) ? url_to_postid($url) : $id;
    }

  // title / excerpt の取得 ------------------------------------------------------------

    if( $my_site === false ) {
      // youtube以外の外部サイトの場合 ------------------------------------------------------------
      // 外部サイトの情報取得
      $xpath = addBlogCard_get_data($url);
      // title取得
      if(empty($title)) {
        $title = $xpath->query('//head/title[1]') ? $xpath->query('//head/title[1]')->item(0)->nodeValue : "";
      }
      // excerpt取得
      if(empty($excerpt)) {
        if($xpath->query('//meta[@property="og:description"]')[0]) {
          $excerpt = $xpath->query('//meta[@property="og:description"]/@content')[0]->textContent;
        } else if($xpath->query('//meta[@name="description"]')[0]) {
          $excerpt = $xpath->query('//meta[@name="description"]/@content')[0]->textContent;
        } else {
          $excerpt = "";
        }
      }
    } else if(empty($tax)) {
      // タクソノミー一覧ではない場合の取得 ------------------------------------------------------------
      $title      = empty($title) ? get_the_title($id) : $title;
      $excerpt    = empty($excerpt) ? addBlogCard_excerpt($id) : $excerpt;
    } else if(!empty($tax)) {
      // タクソノミー一覧の取得 ------------------------------------------------------------
      $term       = get_term( $id, $tax );
      $title      = empty($title) ? $term->name : $title;
      $excerpt    = empty($excerpt) ? term_description($id, $tax) : $excerpt;
    }


  // imgタグ 設定 ------------------------------------------------------------

    if(wp_get_attachment_url($thumb)) {
      // サムネイルが設定されている場合 ------------------------------------------------------------
      $img     = wp_get_attachment_image_url($thumb, array($img_width,$img_height));
      $img_tag = "<img src='{$img}' alt='{$title}' width='{$img_width}' height='{$img_height}'>";
    } elseif( empty($tax) && $my_site !== false ) {
      // 通常 ------------------------------------------------------------
      if(has_post_thumbnail($id)) {
        $img     = wp_get_attachment_image_src(get_post_thumbnail_id($id), array($img_width,$img_height));
        $img_tag = "<img src='{$img[0]}' alt='{$title}' width='{$img[1]}' height='{$img[2]}'>";
      }
    } elseif(!empty($tax)) {
      // taxonomyが設定されている場合（ACFの場合） ------------------------------------------------------------
      if(get_field('image', "{$tax}_{$id}")) {
        $attachment = get_field('image', "{$tax}_{$id}");
        $img_tag    = "<img src='{$attachment["sizes"]["thumbnail"]}' alt='{$title}' width='{$img_width}' height='{$img_height}'>";
      }
    }


  // データの整理 ------------------------------------------------------------

    $title   = addBlogCard_flat_text($title);
    $excerpt = addBlogCard_flat_text($excerpt);
    $site    = isset($site) ? $site : "";
    $img_tag = isset($img_tag) ? $img_tag : "";


  // templateに渡す用のargument ------------------------------------------------------------

    $args = array(
      'url'     => $url,
      'img_tag' => $img_tag,
      'title'   => $title,
      'excerpt' => $excerpt,
      'site'    => $site,
    );

    return addBlogCard_template( $args );

}

add_shortcode("blog_card", "addBlogCard");

?>
