<?php
/**
 * アフィリエイトサービス実装クラス(LinkShare)
 * User: cottonspace
 * Date: 12/04/28
 */

/**
 * 基底クラス
 */
require_once "service-base.php";

/**
 * アフィリエイトサービスの実装クラス
 */
class LinkShare extends ServiceBase
{
    /**
     * 商品検索ソート方法
     * @var array 商品ソート指定の配列
     */
    private $sortTypes = array(
        "+price" => "retailprice,asc",
        "-price" => "retailprice,dsc",
        "+name" => "productname,asc",
        "-name" => "productname,dsc"
    );

    /**
     * FTP ダウンロード処理
     * @param string $host FTPサーバ
     * @param string $user FTPユーザ
     * @param string $pass FTPパスワード
     * @param string $path 取得するファイル名
     * @param int $timeout タイムアウト秒数
     * @return string 取得したコンテンツ
     */
    private function ftp_get_contents($host, $user, $pass, $path, $timeout = 10)
    {
        // キャッシュIDの生成
        $id = "ftp://" . $host . "/" . $path;

        // キャッシュの存在確認
        if (!empty($id) && $contents = $this->cache->get($id)) {
            return $contents;
        }

        // FTPサーバに接続
        if ($conn = @ftp_connect($host, 21, $timeout)) {

            // FTPサーバにログイン
            if (@ftp_login($conn, $user, $pass)) {

                // 取得用一時ファイルハンドル生成
                $temp = fopen("php://temp", "r+");

                // ファイル取得
                if (@ftp_fget($conn, $temp, $path, FTP_ASCII, 0)) {

                    // 取得用一時ファイルポインタを先頭に戻す
                    rewind($temp);

                    // コンテンツを取得
                    if ($contents = stream_get_contents($temp)) {

                        // キャッシュに格納(コンテンツが空では無い場合)
                        if (!empty($id) && !empty($contents)) {
                            $this->cache->save($contents, $id);
                        }

                        // コンテンツを返却
                        return $contents;
                    }
                }
            }
        }

        // ダウンロードに失敗した場合
        return FALSE;
    }

    /**
     * 価格表示フォーマット処理
     * @param string $price 商品価格
     * @param string $currency 通貨単位
     * @return string 価格表示文字列
     */
    private function formatPrice($price, $currency)
    {
        $ret = "";
        if (!empty($price)) {
            $ret = number_format(floatval($price));
            if ($currency == "JPY") {
                $ret .= " 円";
            } else {
                $ret .= " " . $currency;
            }
        }
        return $ret;
    }

    /**
     * 商品検索クエリ生成
     * @link http://linkshare.okweb3.jp/EokpControl?&tid=207339&event=FE0006
     * @return string RESTクエリ文字列
     */
    private function queryItems()
    {
        $baseurl = "http://productsearch.linksynergy.com/productsearch";
        $params = array();
        $params["token"] = $this->account["token"];
        if (!empty($this->requests["keyword"])) {
            $params["keyword"] = $this->requests["keyword"];
        }
        if (!empty($this->requests["shop"])) {
            $params["mid"] = $this->requests["shop"];
        }
        if (!empty($this->requests["category"])) {
            $params["cat"] = $this->requests["category"];
        }
        $params["max"] = $this->requests["pagesize"];
        $params["pagenumber"] = $this->requests["page"];
        if (!empty($this->requests["sort"]) && array_key_exists($this->requests["sort"], $this->sortTypes)) {
            $sort_array = explode(',', $this->sortTypes[$this->requests["sort"]], 2);
            $params["sort"] = $sort_array[0];
            $params["sorttype"] = $sort_array[1];
        }
        ksort($params);
        return $baseurl . "?" . http_build_query($params);
    }

    /**
     * サービス識別名
     * @return string サービス識別名
     */
    public function serviceName()
    {
        return "linkshare";
    }

    /**
     * 商品検索ソート方法取得
     * @return array ソート指定の連想配列
     */
    public function getSortTypes()
    {
        return $this->sortTypes;
    }

    /**
     * カテゴリ検索(マーチャンダイザー使用)
     * @link http://linkshare.okweb3.jp/EokpControl?&tid=50604&event=FE0006
     * @param string $category 基底カテゴリ
     * @return array カテゴリ情報の連想配列
     */
    public function getCategories($category = "")
    {
        // カテゴリ情報の初期化
        $hash = array();

        // カテゴリが選択されていない場合のみ(LinkShareクロスオーバーサーチは主カテゴリのみ指定可能)
        if (empty($category)) {

            // ECサイトID(MID)が指定された場合(MIDは数字のみ許可)
            if (!empty($this->requests["shop"]) && preg_match("/^[0-9]+$/", $this->requests["shop"])) {

                // マーチャンダイザー設定情報の取得
                $md_host = $this->account["md_host"];
                $md_user = $this->account["md_user"];
                $md_pass = $this->account["md_pass"];

                // マーチャンダイザー登録情報が設定されている場合のみ実行
                if (!empty($md_host) && !empty($md_user) && !empty($md_pass)) {

                    // 対象ECサイトのカテゴリ一覧ファイルのファイル名(<MID>/<MID>_category_list.txt)
                    $category_list_path = $this->requests["shop"] . "/" . $this->requests["shop"] . "_category_list.txt";

                    // カテゴリ一覧ファイルを FTP で取得
                    $category_list_contents = $this->ftp_get_contents($md_host, $md_user, $md_pass, $category_list_path);

                    // カテゴリ一覧の取得に成功した場合
                    if (!empty($category_list_contents)) {

                        // カテゴリ名を抽出
                        foreach (explode("\n", $category_list_contents) as $line) {
                            $cols = explode("|", $line, 3);
                            if (is_array($cols) && !empty($cols[1])) {
                                $hash[$cols[1]] = $cols[1];
                            }
                        }
                    }
                }
            }
        }
        return $hash;
    }

    /**
     * 商品検索
     * @link http://linkshare.okweb3.jp/EokpControl?&tid=207339&event=FE0006
     * @return array 商品情報の連想配列
     */
    public function getItems()
    {
        // RESTクエリ情報を取得
        $query = $this->queryItems();

        // RESTクエリ実行
        $strxml = $this->download($query, $query);
        $objxml = simplexml_load_string($strxml);
        $hash = array();
        if (isset($objxml->item)) {
            $this->pages = intval($objxml->TotalPages);
            foreach ($objxml->item as $node) {
                array_push($hash, array(
                        "name" => (string)$node->productname,
                        "price" => $this->formatPrice((string)$node->price, (string)$node->price->attributes()->currency),
                        "desc" => empty($node->description->long) ? (string)$node->description->short : (string)$node->description->long,
                        "shop" => (string)$node->merchantname,
                        "score" => 0,
                        "aurl" => (string)$node->linkurl,
                        "iurl" => (string)$node->imageurl,
                        "surl" => substr((string)$node->imageurl, 0, strpos((string)$node->imageurl, '/', 7)) . '/' // 商品画像URLから生成
                    )
                );
            }
        } else {
            $this->pages = 0;
        }
        return $hash;
    }
}

?>