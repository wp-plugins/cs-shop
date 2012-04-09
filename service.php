<?php
/**
 * アフィリエイトサービスインターフェイス
 * User: cottonspace
 * Date: 12/04/08
 */

interface Service
{
    /**
     * コンストラクタ
     * @param array $account アフィリエイト登録情報の連想配列
     */
    public function __construct($account);

    /**
     * サービス識別名
     * @return string サービス識別名
     */
    public function serviceName();

    /**
     * 商品検索ソート方法取得
     * @return array ソート指定の連想配列
     */
    public function getSortTypes();

    /**
     * 商品検索ページ総数
     * @return int 商品検索ページ総数
     */
    public function getPageCount();

    /**
     * カテゴリ検索
     * @param string $parent 基底カテゴリ
     * @return array カテゴリ情報の連想配列
     */
    public function getCategories($parent = "");

    /**
     * 商品検索
     * @param array $search 商品検索条件
     * @return array 商品情報の連想配列
     */
    public function getItems(&$search);
}

?>