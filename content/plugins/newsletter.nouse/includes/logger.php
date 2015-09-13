<?php

if (!defined('NEWSLETTER_LOG_DIR')) {
    define('NEWSLETTER_LOG_DIR', WP_CONTENT_DIR . '/logs/newsletter/');
}

class NewsletterLogger {

    const NONE = 0;
    const FATAL = 1;
    const ERROR = 2;
    const INFO = 3;
    const DEBUG = 4;

    var $level;
    var $module;
    var $file;

    function __construct($module) {
        $this->module = $module;
        if (defined('NEWSLETTER_LOG_LEVEL')) $this->level = NEWSLETTER_LOG_LEVEL;
        else $this->level = get_option('newsletter_log_level', self::ERROR);

        $secret = get_option('newsletter_logger_secret');
        if (strlen($secret) < 8) {
            $secret = NewsletterModule::get_token(8);
            update_option('newsletter_logger_secret', $secret);
        }

        if (!wp_mkdir_p(NEWSLETTER_LOG_DIR)) {
            $this->level = self::NONE;
        }

        $this->file = NEWSLETTER_LOG_DIR . '/' . $module . '-' . $secret . '.txt';
    }

    function log($text, $level = self::ERROR) {

        if ($this->level < $level) return;

        $time = date('d-m-Y H:i:s ');
        switch ($level) {
            case self::FATAL: $time .= '- FATAL';
                break;
            case self::ERROR: $time .= '- ERROR';
                break;
            case self::INFO: $time .= '- INFO ';
                break;
            case self::DEBUG: $time .= '- DEBUG';
                break;
        }
        if (is_array($text) || is_object($text)) $text = print_r($text, true);

        // The "logs" dir is created on Newsletter constructor.
        $res = @file_put_contents($this->file, $time . ' - ' . size_format(memory_get_usage(), 1) . ' - ' . $text . "\n", FILE_APPEND | FILE_TEXT);
        if ($res === false) {
            $this->level = self::NONE;
        }
    }

    function error($text) {
        self::log($text, self::ERROR);
    }

    function info($text) {
        $this->log($text, self::INFO);
    }

    function fatal($text) {
        $this->log($text, self::FATAL);
    }

    function debug($text) {
        $this->log($text, self::DEBUG);
    }

}
