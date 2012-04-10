<?php
/*
Plugin Name: CS Shop
Plugin URI: http://www.csync.net/category/blog/wp-plugin/cs-shop/
Description: You can easily create a product search page from the affiliate services of Japan.
Version: 0.9.1
Author: cottonspace
Author URI: http://www.csync.net/
License: GPL2
*/
/*  Copyright 2012 cottonspace

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?php
/**
 * 表示用ショートコード [csshop] 実行処理
 * @param array $atts ショートコードで指定された属性情報
 * @param string $content ショートコードで囲まれたコンテンツ
 * @return string 出力コンテンツ
 */
function csshop_view($atts, $content = null)
{
    // 関連ファイルの読み込み
    require_once 'function-common.php';
    require_once 'function-view.php';

    // 出力コンテンツ
    $output = "";

    // 要求パラメタを WordPress ショートコード属性値で設定
    $params = shortcode_atts(array(
            "service" => "",
            "action" => "",
            "shop" => "",
            "pagesize" => "",
            "keyword" => "",
            "category" => "",
            "sort" => ""),
        $atts);

    // 要求パラメタに GET クエリ文字列要求値を設定(ショートコード属性値を上書き)
    getQueryParams($params);

    // PC・携帯電話判定
    if ((function_exists('is_mobile') && is_mobile()) || (function_exists('is_ktai') && is_ktai())) {
        $params["mobile"] = 1;
    }

    // アフィリエイトサービス選択(WordPress プラグイン設定を取得してサービス別のインスタンスを生成)
    switch ($params["service"]) {
        case "rakuten":

            // 楽天アフィリエイト
            require_once 'service-rakuten.php';
            $service = new Rakuten(array(
                "affiliateId" => get_option("csshop_rakuten_aid"),
                "developerId" => get_option("csshop_rakuten_did")
            ));
            break;
        default:

            // 定義されていないサービスの場合(何も出力しない)
            return $output;
            break;
    }

    // アクション別処理
    switch ($params["action"]) {
        case "search":

            // 現在ページ位置の補正
            if (!isset($params["page"]) || empty($params["page"])) {
                $params["page"] = "1";
            }

            // 商品検索実行
            $items = $service->getItems($params);

            // 検索フォーム表示
            $output .= showSearchForm($service, $params);

            // 検索結果の存在確認
            if (0 < count($items)) {

                // ページナビゲータ生成
                $pagelinks = showPageLinks($service, $params);

                // 上部ページナビゲータ表示
                $output .= $pagelinks;

                // 商品一覧表示
                $output .= showItems($params, $items);

                // 下部ページナビゲータ表示
                $output .= $pagelinks;
            } else {

                // 検索結果が 0 件の場合(キーワードが指定されている場合のみ)
                if (!empty($params["keyword"])) {

                    // 検索結果が無いメッセージ
                    $output .= "<p>検索条件に該当する商品はありませんでした。</p>";

                    // 最上位カテゴリ一覧を表示
                    $output .= showRootCategories($service);
                }
            }
            break;
        default:

            // 指定されていない場合(最上位カテゴリ一覧を表示)
            $output .= showRootCategories($service);
            break;
    }

    // サービス署名表示
    $output .= showSignature($service->serviceName());

    // コンテンツの返却
    return $output;
}

/**
 * 表示用スタイルシート設定処理
 */
function csshop_css()
{
    // プラグインの基準URL
    $pluginBaseUrl = WP_PLUGIN_URL . "/cs-shop";

    // スタイルシートリンクの表示
    echo  <<<EOF
<link rel="stylesheet" href="{$pluginBaseUrl}/cs-shop.css" type="text/css" />\n
EOF;
}

// WordPress 管理画面判定
if (is_admin()) {

    // 関連ファイルの読み込み(管理画面を表示)
    require_once 'cs-shop-admin.php';

} else {
    // WordPress ショートコード登録(表示用)
    add_shortcode("csshop", "csshop_view");

    // CSSをヘッダに追加
    add_action('wp_head', 'csshop_css');
}
?>