=== LIQUID TOOLS - Simple Custom Fields & Custom Post Types ===
Contributors: lqd
Donate link: https://lqd.jp/wp/plugin.html
Tags: custom fields, custom post types, custom taxonomies
Requires at least: 6.0
Tested up to: 6.6.2
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Very simple tool to set up Custom Fields, Custom Post Types, Custom Taxonomies.

== Description ==

LIQUID TOOLS is a simple plugin for WordPress that enables easy management of custom post types, taxonomies, and custom fields.

Features:
- **Custom Post Types**: Create and manage custom post types with unique slugs, menu icons, and capabilities.
- **Custom Taxonomies**: Create hierarchical or non-hierarchical taxonomies and link them to any post type.
- **Custom Fields**: Add custom fields to posts, including text fields, image uploads, URLs, and more.
- **Repeating Fields**: Supports repeating fields for text, images, and other field types.
- **Flexible Display Options**: Control the display order, position, and priority of custom fields in the post editor.
- **Field Background Colors**: Set background colors for custom fields to visually distinguish fields in the post editor.
- **Thumbnail Support**: Add thumbnails to the post admin list for selected post types.

= Custom Fields =

* text
* textarea
* url
* email
* number
* image
* checkbox
* radio
* select

= Custom Post Types =

* Capability
* Archive
* Hierarchical
* Thumbnail
* Revisions
* Excerpt

= Custom Taxonomies =

* Apply to Post Types
* Hierarchical

= Others =

Add thumbnails to the post admin list.
latest information on [LIQUID PRESS](https://lqd.jp/wp/).

== How to get custom field data ==

[LIQUID BLOCKS](https://wordpress.org/plugins/liquid-blocks/) plugin for easy to get field data.
**Custom field block** allows you to output custom field data with no code.
Supports block editors and block themes.

Another way, Get field data securely using WordPress core functions.

= Text field =

`get_post_meta( $post->ID, 'key', true );`

= Repeat text fields =

`$key = get_post_meta( $post->ID, 'key', true );
foreach ( $key as $value ) {
    echo esc_html( $value );
}`

= Image field =

`wp_get_attachment_url( get_post_meta( $post->ID, 'key', true ) );`

= Repeat image fields =

`$key = get_post_meta( $post->ID, 'key', true );
foreach ( $key as $value ) {
    echo wp_get_attachment_url( esc_html( $value ) );
}`

== Installation ==

1. Upload 'liquid-tools' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Set 'LIQUID TOOLS' in the admin menu

== Frequently Asked Questions ==

= Q: Can I use this plugin with other custom post type plugins? =

A: Yes, LIQUID TOOLS works independently and can be used alongside other plugins that manage custom post types.

== Screenshots ==

1. Custom Fields on Editor
2. Custom Post Types
3. Custom Taxonomies
4. Custom Fields
5. Others

== Changelog ==

= 1.0.0 =

* A first version.
