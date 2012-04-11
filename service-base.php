<?php
/**
 * アフィリエイトサービス基底クラス
 * User: cottonspace
 * Date: 12/04/11
 */

/**
 * インターフェイス
 */
require_once "service.php";

/**
 * Cache_Lite クラス
 */
require_once "Cache/Lite.php";

/**
 * アフィリエイトサービスの実装クラス
 */
class ServiceBase implements IService
{
    /**
     * アカウント情報
     * @var array アフィリエイト登録情報の連想配列
     */
    protected $account;

    /**
     * キャッシュオブジェクト
     * @var object キャッシュオブジェクト
     */
    protected $cache;

    /**
     * GET 要求ダウンロード処理(WordPress関数利用)
     * @param string $id キャッシュID(通常は $url と同様だが Amazon 対応で必要)
     * @param string $url 要求先URL
     * @param int $timeout タイムアウト秒数
     * @param int $retry 再試行回数
     * @return string 取得したコンテンツ
     */
    protected function download($id, $url, $timeout = 10, $retry = 10)
    {
        // キャッシュの存在確認
        if (!empty($id) && $contents = $this->cache->get($id)) {
            return $contents;
        }

        // 指定回数まで再試行
        for ($i = 0; $i < $retry; $i++) {

            // 再試行待ち時間(1秒)
            if (0 < $i) sleep(1);

            // ダウンロード実行
            $response = wp_remote_get($url, array('timeout' => $timeout));

            // ダウンロード結果の判定
            if (!is_wp_error($response) && $response["response"]["code"] === 200) {

                // 成功時(コンテンツを取得)
                $contents = $response['body'];

                // キャッシュに格納(コンテンツが空では無い場合)
                if (!empty($id) && !empty($contents)) {
                    $this->cache->save($contents, $id);
                }

                // コンテンツを返却
                return $contents;
            }
        }

        // ダウンロードに失敗した場合
        return FALSE;
    }

    /**
     * コンストラクタ
     * @param array $account アフィリエイト登録情報の連想配列
     */
    public function __construct($account)
    {
        // アフィリエイト登録情報の設定
        $this->account = $account;

        // キャッシュディレクトリ名の生成
        $cache_base = dirname(__FILE__) . "/cache-temp/";

        // キャッシュディレクトリが存在しない場合は作成
        if (!file_exists($cache_base)) {
            mkdir($cache_base, 0777);
        }

        // キャッシュオブジェクト作成
        $this->cache = new Cache_Lite(
            array(
                'cacheDir' => $cache_base,
                'lifeTime' => 3600,
                'automaticSerialization' => TRUE,
                'automaticCleaningFactor' => 20,
                'hashedDirectoryLevel' => 1
            )
        );
    }

    /**
     * サービス識別名
     * @return string サービス識別名
     */
    public function serviceName()
    {
        return "";
    }

    /**
     * 商品検索ソート方法取得
     * @return array ソート指定の連想配列
     */
    public function getSortTypes()
    {
        return array();
    }

    /**
     * 商品検索ページ総数
     * @return int 商品検索ページ総数
     */
    public function getPageCount()
    {
        return 0;
    }

    /**
     * カテゴリ検索
     * @param string $parent 基底カテゴリ
     * @return array カテゴリ情報の連想配列
     */
    public function getCategories($parent = "")
    {
        return array();
    }

    /**
     * 商品検索
     * @param array $search 商品検索条件
     * @return array 商品情報の連想配列
     */
    public function getItems(&$search)
    {
        return array();
    }
}

?>
