<?php

namespace FastCache\ObjectCache;

class Invalidation
{

    public static function register()
    {
        add_action('clean_post_cache', [__CLASS__, 'onCleanPostCache'], 10, 2);
        add_action('clean_term_cache', [__CLASS__, 'onCleanTermCache'], 10, 2);
        add_action('clean_user_cache', [__CLASS__, 'onCleanUserCache'], 10, 2);
        add_action('deleted_option', [__CLASS__, 'onOptionChanged'], 10, 1);
        add_action('updated_option', [__CLASS__, 'onOptionChanged'], 10, 1);
        add_action('added_option', [__CLASS__, 'onOptionChanged'], 10, 1);
        add_action('switch_theme', [__CLASS__, 'flushAll']);
    }

    public static function onCleanPostCache($postId, $post)
    {
        if (is_int($postId) || is_string($postId)) {
            wp_cache_delete($postId, 'posts');
            wp_cache_delete($postId, 'post_meta');
        }
    }

    public static function onCleanTermCache($termId, $taxonomy)
    {
        if (is_int($termId) || is_string($termId)) {
            wp_cache_delete($termId, 'terms');
            wp_cache_delete($termId, 'term_meta');
        }

        if (is_string($taxonomy) && $taxonomy !== '') {
            wp_cache_delete('all_ids', $taxonomy);
        }
    }

    public static function onCleanUserCache($userId, $user)
    {
        if (!is_int($userId) && !is_string($userId)) {
            error_log('FastCache onCleanUserCache() invalid userId: ' . print_r($userId, true));
            return;
        }

        if (!is_object($user)) {
            error_log('FastCache onCleanUserCache() invalid user object: ' . print_r($user, true));
            return;
        }

        wp_cache_delete($userId, 'users');
        wp_cache_delete($userId, 'user_meta');
        if (isset($user->user_login)) {
            wp_cache_delete($user->user_login, 'userlogins');
        }
        if (isset($user->user_email)) {
            wp_cache_delete($user->user_email, 'useremail');
        }
        if (isset($user->user_nicename)) {
            wp_cache_delete($user->user_nicename, 'userslugs');
        }
    }

    public static function onOptionChanged($optionName)
    {
        if (!is_string($optionName) || $optionName === '') {
            error_log('FastCache onOptionChanged() invalid optionName: ' . print_r($optionName, true));
            return;
        }

        wp_cache_delete('alloptions', 'options');
        wp_cache_delete($optionName, 'options');
    }

    public static function flushAll()
    {
        wp_cache_flush();
    }
}
