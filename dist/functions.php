<?php 
// === 基本設定 ===
// meta name=”generator”を非表示
remove_action('wp_head', 'wp_generator');

// link rel="EditURI"を非表示
remove_action('wp_head', 'rsd_link');

// link rel="wlwmanifest"を非表示
remove_action('wp_head', 'wlwmanifest_link');

// 絵文字用CSS・JSを非表示
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_styles', 'print_emoji_styles');

// rel="next"・rel="prev"を非表示
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');

// コメントフィードを非表示
remove_action('wp_head', 'feed_links_extra', 3);

// DNSプリフェッチを非表示
function remove_dns_prefetch( $hints, $relation_type ) {
	if ('dns-prefetch' === $relation_type) {
		return array_diff(wp_dependencies_unique_hosts(), $hints);
	}
	return $hints;
}
add_filter('wp_resource_hints', 'remove_dns_prefetch', 10, 2);

// 本文・抜粋のタグ自動整形機能を無効化
remove_filter('the_content', 'wpautop');
remove_filter('the_excerpt', 'wpautop');

// 画像のsrcset属性・size属性自動挿入を無効化
add_filter('wp_calculate_image_srcset_meta', '__return_null');

//画像アップロードの画質変更
add_filter( 'jpeg_quality', function( $arg ){ return 100; } );

// SVGアップロードを有効化
add_filter('wp_check_filetype_and_ext', function ($data, $file, $filename, $mimes) {
	$filetype = wp_check_filetype($filename, $mimes);
	return [
		'ext' => $filetype['ext'],
		'type' => $filetype['type'],
		'proper_filename' => $data['proper_filename']
	];
}, 10, 4);

function cc_mime_types($mimes)
{
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');

function custom_theme_setup() {
	// アイキャッチを有効化
	add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'custom_theme_setup');

// === プロジェクト設定 ===
// CSSの読み込み
function add_my_styles() {
	wp_register_style(
		'base-style',
		get_stylesheet_uri(),
		array(),
		'1.0',
		'all'
	);

	wp_enqueue_style('base-style');
}
add_action('wp_enqueue_scripts', 'add_my_styles');

// ブロックエディタ用CSSの読み込み
function add_my_editor_style() {
	add_theme_support( 'editor-styles' );
	add_editor_style( 'css/custom_editor_style.css' );
}
add_action( 'after_setup_theme', 'add_my_editor_style' );

// JSの読み込み
function add_my_scripts() {
	wp_register_script(
		'main',
		esc_url(get_theme_file_uri('js/main.bandle.js')),
		array(),
		'1.0',
		false
	);

	wp_enqueue_script('main');
}
add_action('wp_enqueue_scripts', 'add_my_scripts');

// scriptタグに属性を追加
function remove_script_type($tag) {
	return str_replace("type='text/javascript'", "type='module' async='defer'", $tag);
}
add_filter('script_loader_tag','remove_script_type');

?>