<?php

/**
 * This is only demo code just to make the demo Feed by Mail panel work.
 */
class NewsletterFeed extends NewsletterModule {

    static $instance;

    static function instance() {
        if (self::$instance == null) {
            self::$instance = new NewsletterFeed();
        }
        return self::$instance;
    }

    function __construct() {
        parent::__construct('feed', '1.0.0');
    }

    function admin_menu() {
        $this->add_menu_page('index', 'Feed by Mail');
    }
}

NewsletterFeed::instance();
