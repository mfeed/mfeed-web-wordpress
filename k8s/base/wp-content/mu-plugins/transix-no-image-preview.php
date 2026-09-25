<?php
/**
 * Plugin Name: transix: 検索結果のサムネイル抑止
 * Description: /transix/ 配下のページに限り robots メタタグへ max-image-preview:none を出力し、Bing をはじめとする検索エンジンの検索結果にサムネイル画像が表示されないようにする。
 * Author: INTERNET MULTIFEED CO.
 * Version: 1.0.0
 *
 * mu-plugins に置いているのは、テーマの切り替えとは無関係に効かせるため。
 * 対象はマルチサイトのうちパスが /transix/ のサイト（www.mfeed.ad.jp/transix/）
 * だけで、ルートの www.mfeed.ad.jp/ 側は今までどおりサムネイルが出る。
 *
 * Yoast SEO が robots メタタグを組み立てており、既定では
 * max-image-preview:large を出力する。Yoast は wp_robots に
 * PHP_INT_MAX - 10 でフックするので、こちらは PHP_INT_MAX、つまり
 * その後ろで最終的な値を上書きする。Yoast を停止しても、コアの
 * wp_robots だけで同じ結果になる。
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 表示中のサイトが /transix/ かどうか。
 *
 * @return bool
 */
function mfeed_is_transix_site() {
	if ( ! is_multisite() ) {
		return false;
	}

	$site = get_site();

	return ( $site instanceof WP_Site ) && '/transix/' === $site->path;
}

add_filter(
	'wp_robots',
	/**
	 * 画像プレビューの最大サイズを none にする。
	 *
	 * @param array $robots robots ディレクティブの配列。
	 * @return array 変更後の配列。
	 */
	static function ( $robots ) {
		if ( ! is_array( $robots ) || ! mfeed_is_transix_site() ) {
			return $robots;
		}

		// noindex のページではコアも Yoast も max-image-preview を落とす。
		// ここで付け直すと矛盾した出力になるので、そのまま返す。
		if ( ! empty( $robots['noindex'] ) ) {
			return $robots;
		}

		$robots['max-image-preview'] = 'none';

		return $robots;
	},
	PHP_INT_MAX
);
