<?php
/*
Plugin Name: Advanced Custom Fields - get_acfkey_by_name
Description: Lookup ACF key name from ACF name. This plugin just adds a function.
Author: Yousan_O
License: GPL
*/


/**
 * Advanced Custom Field (ACF) rquires 'key' to update the acf field.
 * It is easy to use ACF name as field_name such as the_field($field_name);
 * But we have to use ACF key since updating field.
 * This function lookups key from name.
 *
 * The second argument, $post_id is 'ACF group ID'.
 * ACF can be defined several conditions with same name.
 * It causes an error that more than one ACF keys from ONE name.
 * Then this function throws an exception.
 */
function get_acfkey_by_name($name, $post_id=0) {
    global $wpdb;
    if (is_numeric($post_id) && $post_id) { // ID指定あり
        $query = "SELECT * FROM $wpdb->postmeta ".
            "WHERE meta_key like %s ".
            " AND meta_value like %s " .
            " AND post_id = %d;";
        $rows = $wpdb->get_results($wpdb->prepare($query, 'field_%', '%'.$name.'%', $post_id), OBJECT);
    } else { // ID指定無し
        $query = "SELECT * FROM $wpdb->postmeta ".
            "WHERE meta_key like %s ".
            " AND meta_value like %s;";
        $rows = $wpdb->get_results(
            $wpdb->prepare($query, 'field_%', '%'.$name.'%'), OBJECT);
    }
    $found = false; $key = false;
    foreach ($rows as $row) {
        $meta_value = unserialize($row->meta_value);
        if ($name == $meta_value['name']) {
            if ($found) { // 二個以上同じ物が見つかった場合にはエラー
                $msg = 'More than 1 ACF keys found. It may cause an error.'.
                    'Please specify ACF group ID';
                throw new Exception($msg);
            } else { // 同じ物がヒットしていないかチェックする
                $key = $meta_value['key'];
            }
            $found = true;
        }
    }
    return $key;
}
?>
