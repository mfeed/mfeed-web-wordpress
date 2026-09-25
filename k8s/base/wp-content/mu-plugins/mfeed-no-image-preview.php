<?php
/**
 * Plugin Name: mfeed: 検索結果のサムネイル抑止
 * Description: robots メタタグに max-image-preview:none を出力し、Bing をはじめとする検索エンジンの検索結果にサムネイル画像が表示されないようにする。
 * Author: INTERNET MULTIFEED CO.
 * Version: 1.0.0
 *
 * mu-plugins に置いているのは、マルチサイト（www.mfeed.ad.jp と /transix/）の
 * 両方に、テーマの切り替えとは無関係に効かせるため。
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

add_filter(
	'wp_robots',
	/**
	 * 画像プレビューの最大サイズを none にする。
	 *
	 * @param array $robots robots ディレクティブの配列。
	 * @return array 変更後の配列。
	 */
	static function ( $robots ) {
		if ( ! is_array( $robots ) ) {
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
