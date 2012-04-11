<?php
/**
 * 画面描画用関数ライブラリ
 * User: cottonspace
 * Date: 12/04/11
 */

/**
 * 最上位カテゴリ一覧
 * @param object $service サービス情報
 * @return string 出力コンテンツ
 */
function showRootCategories(&$service)
{
    // 出力コンテンツ
    $output = "";

    // 基準URLの生成
    $url = $_SERVER['REQUEST_URL'] . "?service=" . urlencode($service->serviceName()) . "&action=search";

    // 第2階層まで表示
    foreach ($service->getCategories() as $k1 => $v1) {
        $output .= "<p><h3>" . o_escape($v1) . "</h3>\n";
        $output .= "<a href=\"$url&category=" . urlencode($k1) . "\">カテゴリ全体</a>";
        foreach ($service->getCategories($k1) as $k2 => $v2) {
            $output .= " | <a href=\"$url&category=" . urlencode($k2) . "\">" . o_escape($v2) . "</a>";
        }
        $output .= "</p>\n";
    }

    // コンテンツの返却
    return $output;
}

/**
 * カテゴリ選択用ドロップダウンリスト
 * @param object $service サービス情報
 * @param string $current 現在のカテゴリ
 * @return string 出力コンテンツ
 */
function showCategorySelector(&$service, $current)
{
    // 出力コンテンツ
    $output = "";

    // ドロップダウンリストの表示
    if (empty($current)) {

        // 最上位カテゴリの取得
        $rootCategories = $service->getCategories();

        // 最上位カテゴリが取得できる場合のみ
        if (!empty($rootCategories)) {
            $output .= "<select name=\"category\" onchange=\"submit();\">\n";
            $output .= "<option value=\"\">すべてのカテゴリ</option>\n";
            foreach ($rootCategories as $k1 => $v1) {
                $output .= "<option value=\"" . o_escape($k1) . "\">" . o_escape($v1) . "</option>\n";
            }
            $output .= "</select>\n";
        }
    } else {

        // 絞り込みカテゴリの取得
        $subCategories = $service->getCategories($current);

        // 絞り込みカテゴリの表示
        $output .= "<select name=\"category\" onchange=\"submit();\">\n";
        if (!empty($subCategories)) {
            $output .= "<option value=\"" . o_escape($current) . "\">カテゴリを絞り込む</option>\n";
            foreach ($subCategories as $k1 => $v1) {
                $output .= "<option value=\"" . o_escape($k1) . "\">　" . o_escape($v1) . "</option>\n";
            }
        } else {
            $output .= "<option value=\"" . o_escape($current) . "\">変更しない</option>\n";
        }
        $output .= "<option value=\"\">すべてのカテゴリ</option>\n";
        $output .= "</select>\n";
    }

    // コンテンツの返却
    return $output;
}

/**
 * 並び替え方法選択用ドロップダウンリスト
 * @param object $service サービス情報
 * @param string $current 現在の並び替え方法
 * @return string 出力コンテンツ
 */
function showSortTypeSelector(&$service, $current)
{
    // 出力コンテンツ
    $output = "";

    // 並び替え方法の表示名
    $sortTypeNames = array(
        "" => "指定なし",
        "+price" => "価格が安い順",
        "-price" => "価格が高い順",
        "-reviews" => "レビューが多い順",
        "+reviews" => "レビューが少ない順",
        "-score" => "評価が高い順",
        "+score" => "評価が低い順"
    );

    // 対応している並び替え方法の取得
    $supportTypes = $service->getSortTypes();

    // 並び替え方法の表示
    $output .= "<select name=\"sort\" onchange=\"submit();\">\n";
    foreach ($supportTypes as $k1 => $v1) {
        $selected = ($current == $k1) ? " selected" : "";
        $output .= "<option value=\"" . o_escape($k1) . "\"$selected>" . $sortTypeNames[$k1] . "</option>\n";
    }
    $output .= "</select>\n";

    // コンテンツの返却
    return $output;
}

/**
 * 商品検索条件入力フォーム
 * @param object $service サービス情報
 * @param array $params 要求パラメタ
 * @return string 出力コンテンツ
 */
function showSearchForm(&$service, &$params)
{
    // 出力コンテンツ
    $output = "";

    // 要求パラメタの出力エスケープ処理(指定キーのみ)
    $work = array();
    foreach (array("service", "action", "pagesize", "keyword", "shop", "category", "sort") as $k) {
        if (array_key_exists($k, $params) && !empty($params[$k])) {
            $work[$k] = o_escape($params[$k]);
        } else {
            $work[$k] = "";
        }
    }

    // 検索フォームの開始
    $output .= <<< EOT
<div class="csshop-search-form">
<form method="get" action="{$_SERVER['REQUEST_URL']}">
<input type="hidden" name="service" value="{$work["service"]}" />
<input type="hidden" name="action" value="{$work["action"]}" />
<input type="hidden" name="shop" value="{$work["shop"]}" />
<input type="hidden" name="pagesize" value="{$work["pagesize"]}" />
<input type="text" name="keyword" value="{$work["keyword"]}" />\n
EOT;

    // カテゴリ選択リストの表示
    $output .= showCategorySelector($service, $work["category"]);

    // 並び替え方法変更リストの表示
    $output .= showSortTypeSelector($service, $work["sort"]);

    // 検索フォームの終了
    $output .= <<< EOT
</form>\n
</div>
EOT;

    // コンテンツの返却
    return $output;
}

/**
 * 商品一覧
 * @param array $params 要求パラメタ
 * @param array $items 商品情報の連想配列を格納した配列
 * @return string 出力コンテンツ
 */
function showItems(&$params, &$items)
{
    // 出力コンテンツ
    $output = "";

    // PC・携帯電話の判定
    if (empty($params["mobile"])) {

        // PCの場合
        foreach ($items as $item) {
            $item_escaped["name"] = o_escape(mb_strimwidth($item["name"], 0, 128, "..", "UTF-8"));
            $item_escaped["desc"] = o_escape(mb_strimwidth($item["desc"], 0, 256, "..", "UTF-8"));
            if (!empty($item["surl"])) {
                $shopicon = "<img src=\"http://favicon.hatena.ne.jp/?url=" . urlencode($item["surl"]) . "\" /> ";
            } else {
                $shopicon = "";
            }
            $shopname = empty($item['shop']) ? "詳細" : $item['shop'];
            $output .= <<< EOT
<div class="csshop-item">
<h3>{$item_escaped['name']}</h3>
<div class="image"><a href="{$item['aurl']}" target="_blank"><img src="{$item['iurl']}" alt="{$item_escaped['name']}" width="128" /></a></div>
<div class="price">{$item['price']}</div>
<div class="shop">{$shopicon}<a href="{$item['aurl']}" target="_blank">{$shopname}</a></div>
<div class="description">{$item_escaped['desc']}</div>
</div>\n
EOT;
        }
    } else {

        // 携帯電話の場合
        foreach ($items as $item) {
            $item_escaped["name"] = o_escape(mb_strimwidth($item["name"], 0, 64, "..", "UTF-8"));
            $item_escaped["desc"] = o_escape(mb_strimwidth($item["desc"], 0, 128, "..", "UTF-8"));
            $shopname = empty($item['shop']) ? "詳細" : $item['shop'];
            $output .= <<< EOT
<h3>{$item_escaped['name']}</h3>
<a href="{$item['aurl']}" target="_blank"><img src="{$item['iurl']}" width="64" /></a>
{$item['price']}<br />
<a href="{$item['aurl']}" target="_blank">{$shopname}</a><br />
{$item_escaped['desc']}\n
EOT;
        }
    }

    // コンテンツの返却
    return $output;
}

/**
 * ページナビゲータ
 * @param object $service サービス情報
 * @param array $params 要求パラメタ
 * @return string 出力コンテンツ
 */
function showPageLinks(&$service, &$params)
{
    // 出力コンテンツ
    $output = "";

    // 要求パラメタの複製を作成(指定キーのみ)
    $work = array();
    foreach (array("service", "action", "pagesize", "keyword", "shop", "category", "sort") as $k) {
        if (array_key_exists($k, $params) && !empty($params[$k])) {
            $work[$k] = $params[$k];
        }
    }

    // 基準URLの生成
    $url = $_SERVER['REQUEST_URL'] . "?" . http_build_query($work);

    // ページ総数
    $total = $service->getPageCount();

    // 現在ページ位置
    $current = intval($params["page"]);

    // PC・携帯電話の判定
    $output .= "<p>\n";
    if (empty($params["mobile"])) {

        // PCの場合
        $output .= "<div class=\"csshop-page-navi\">\n";
        for ($i = 1; $i <= $total; $i++) {
            if ($i == $current) {
                $output .= "<span class=\"current\">$i</span>\n";
            } else {
                if ($i == 1 || (($current - 3) <= $i && $i <= ($current + 3)) || $i == $total) {
                    $output .= "<a href=\"$url&page=$i\" class=\"page\">$i</a>\n";
                } else if ($i == 2 || $i == ($total - 1)) {
                    $output .= "..\n";
                }
            }
        }
        $output .= "</div>\n";
    } else {

        // 携帯電話の場合
        if (1 < $current) {
            $output = "<a href=\"$url&page=" . ($current - 1) . "\">&laquo;前ページ</a>\n";
        }
        $output .= "$current\n";
        if ($current < $total) {
            $output .= "<a href=\"$url&page=" . ($current + 1) . "\">次ページ&raquo;</a>\n";
        }
    }
    $output .= "</p>\n";

    // コンテンツの返却
    return $output;
}

/**
 * サービス署名
 * @param $servicename 使用サービス名
 * @return string 出力コンテンツ
 */
function showSignature($servicename)
{
    // 出力コンテンツ
    $output = "";

    // シグネチャ開始
    $output .= "<p>\n";

    // プラグインの基準URL
    $pluginBaseUrl = WP_PLUGIN_URL . "/cs-shop";

    // 使用サービス指定のシグネチャ表示
    switch ($servicename) {
        case "yahoo":
            $output .= <<<EOF
<!-- Begin Yahoo! JAPAN Web Services Attribution Snippet -->
<a href="http://developer.yahoo.co.jp/about">
<img src="http://i.yimg.jp/images/yjdn/yjdn_attbtn2_105_17.gif" width="105" height="17" title="Webサービス by Yahoo! JAPAN" alt="Webサービス by Yahoo! JAPAN" border="0" style="margin:15px 15px 15px 15px"></a>
<!-- End Yahoo! JAPAN Web Services Attribution Snippet -->\n
EOF;
            break;
    }

    // プラグインのシグネチャ表示
    $output .= <<<EOF
<!-- Begin Powered by CS Shop 0.9.3 -->
<a href="http://www.csync.net/">
<img src="{$pluginBaseUrl}/cs-shop.gif" width="80" height="15" title="CS Shop" alt="CS Shop" border="0" style="margin:15px 0px"></a>
<!-- End Powered by CS Shop -->\n
EOF;

    // シグネチャ終了
    $output .= "</p>\n";

    // コンテンツの返却
    return $output;
}

?>