## 【プラグインなし】Wordpressで外部URLにも対応したブログカードを作成した話

[brandnew.work](https://brandnew.work/) の記事より  
詳細は[こちら](https://brandnew.work/column/wordpress/wordpress-blogcard/)から  


## 事前準備
- 子テーマ・もしくはテーマ内にfunctionsフォルダをアップロード
- custom_functions.php がある場合はそちらに。
ない場合は、functions.php の最下部に下記コードを入力
```php
// ブログカード機能追加
include 'functions/addBlogCard/addBlogCard.php';
```

---

## 使用方法
- 上記設定が完了すると、Wisywigエディタの上部に「ブログカード」のプルダウンが表示されます。  
必要な選択肢は用意していますが、確認のためargumentは以下のとおりです。
- 他サイトのURLを指定した場合はtitleとdescriptionのみ取得してきます。  
※画像の直リンクは該当サイトに迷惑なので止めておく

| 引数 | 用途 |
| --- | --- |
| url | リンク先（必須） |
| title | カスタムタイトル |
| excerpt | カスタム説明文 |
| thumb | カスタムサムネイル |
| tax | タクソノミー（category, custom_taxonomy_name etc...） |
| id | タクソノミーID（タクソノミー設定時のみ必要） |

taxonomyやterm一覧をURLに指定する場合、tax, idの設定は必須です

---

## ディレクトリ構造
| file / directory | 用途 |
| --- | --- |
| assets | 使用アイコン画像 |
| addBlogCard-template.php | htmlテンプレート |
| addBlogCard-tinymce.php | tinymce設定 |
| addBlogCard.css | デフォルトcss |
| addBlogCard.js | tinymce設定 |
| addBlogCard.php | メインファイル |
