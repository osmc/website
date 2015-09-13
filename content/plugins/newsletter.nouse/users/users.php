<?php

require_once NEWSLETTER_INCLUDES_DIR . '/module.php';

class NewsletterUsers extends NewsletterModule {

    static $instance;

    /**
     * @return NewsletterUsers
     */
    static function instance() {
        if (self::$instance == null) {
            self::$instance = new NewsletterUsers();
        }
        return self::$instance;
    }

    function __construct() {
        parent::__construct('users', '1.0.4');
    }

    function upgrade() {
        global $wpdb, $charset_collate;

        parent::upgrade();

        $this->upgrade_query("create table if not exists " . NEWSLETTER_USERS_TABLE . " (id int auto_increment, `email` varchar(100) not null default '', primary key (id), unique key email (email)) $charset_collate");

        // User personal data
        $this->upgrade_query("alter table " . NEWSLETTER_USERS_TABLE . " add column name varchar(100) not null default ''");
        $this->upgrade_query("alter table " . NEWSLETTER_USERS_TABLE . " add column surname varchar(100) not null default ''");
        $this->upgrade_query("alter table " . NEWSLETTER_USERS_TABLE . " add column sex char(1) not null default 'n'");
        $this->upgrade_query("alter table " . NEWSLETTER_USERS_TABLE . " change column sex sex char(1) not null default 'n'");

        $this->upgrade_query("alter table " . NEWSLETTER_USERS_TABLE . " add column status char(1) not null default 'S'");
        $this->upgrade_query("alter table " . NEWSLETTER_USERS_TABLE . " add column created timestamp not null default current_timestamp");
        $this->upgrade_query("alter table " . NEWSLETTER_USERS_TABLE . " add column token varchar(50) not null default ''");

        // Feed by mail
        $this->upgrade_query("alter table " . NEWSLETTER_USERS_TABLE . " add column feed tinyint(4) not null default 0");
        $this->upgrade_query("alter table " . NEWSLETTER_USERS_TABLE . " add column feed_time bigint(20) not null default 0");
        $this->upgrade_query("alter table " . NEWSLETTER_USERS_TABLE . " add column country varchar(4) not null default ''");

        // List/Preferences
        for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
            $this->upgrade_query("alter table " . NEWSLETTER_USERS_TABLE . " add column list_$i tinyint(4) not null default 0");
        }

        // Profiles
        for ($i = 1; $i <= NEWSLETTER_PROFILE_MAX; $i++) {
            $this->upgrade_query("alter table " . NEWSLETTER_USERS_TABLE . " add column profile_$i varchar(255) not null default ''");
        }

        // TODO: Still makes sense the referrer?
        $this->upgrade_query("alter table " . NEWSLETTER_USERS_TABLE . " add column referrer varchar(50) not null default ''");
        $this->upgrade_query("alter table " . NEWSLETTER_USERS_TABLE . " add column http_referer varchar(255) not null default ''");
        $this->upgrade_query("alter table " . NEWSLETTER_USERS_TABLE . " add column wp_user_id int not null default 0");
        $this->upgrade_query("alter table " . NEWSLETTER_USERS_TABLE . " add column ip varchar(50) not null default ''");
        $this->upgrade_query("alter table " . NEWSLETTER_USERS_TABLE . " add column test tinyint(4) not null default 0");

        // TODO: Flow module should add that it self (?)
        $this->upgrade_query("alter table " . NEWSLETTER_USERS_TABLE . " add column flow tinyint(4) not null default 0");

        // Old problems...
        $this->upgrade_query("alter table " . NEWSLETTER_USERS_TABLE . " convert to character set utf8");

        $this->upgrade_query("update " . NEWSLETTER_USERS_TABLE . " set sex='n' where sex='' or sex=' '");        
    }

    function admin_menu() {
        $this->add_menu_page('index', 'Subscribers');
        $this->add_admin_page('new', 'New subscriber');
        $this->add_admin_page('edit', 'Subscribers Edit');

        $this->add_admin_page('massive', 'Massive Management');
        $this->add_admin_page('export', 'Export');
        $this->add_admin_page('import', 'Import');
        $this->add_admin_page('stats', 'Statistics');
        //$this->add_admin_page('index', 'Old search');
    }

    function export($options = null) {
        global $wpdb;

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="newsletter-subscribers.csv"');

        // BOM
        echo "\xEF\xBB\xBF";
        
        $sep = ';';
        if ($options) {
            $sep = $options['separator'];
        }
        
        // CSV header
        echo '"Email"' . $sep . '"Name"' . $sep . '"Surname"' . $sep . '"Sex"' . $sep . '"Status"' . $sep . '"Date"' . $sep . '"Token";';

        // In table profiles
        for ($i = 1; $i <= NEWSLETTER_PROFILE_MAX; $i++) {
            echo '"Profile ' . $i . '"' . $sep; // To adjust with field name
        }

        // Lists
        for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
            echo '"List ' . $i . '"' . $sep;
        }

        echo '"Feed by mail"' . $sep . '"Follow up"' . $sep;
        echo '"IP"' . $sep . '"Referrer"' . $sep . '"Country"';

        echo "\n";

        $page = 0;
        while (true) {
            $query = "select * from " . NEWSLETTER_USERS_TABLE . "";
            if (!empty($_POST['options']['list'])) {
                $query .= " where list_" . $_POST['options']['list'] . "=1";
            }
            $recipients = $wpdb->get_results($query . " order by email limit " . $page * 500 . ",500");
            for ($i = 0; $i < count($recipients); $i++) {
                echo '"' . $recipients[$i]->email . '"' . $sep . '"' . $this->sanitize_csv($recipients[$i]->name) .
                '"' . $sep . '"' . $this->sanitize_csv($recipients[$i]->surname) .
                '"' . $sep . '"' . $recipients[$i]->sex .
                '"' . $sep . '"' . $recipients[$i]->status . '"' . $sep . '"' . $recipients[$i]->created . '";"' . $recipients[$i]->token . '"' . $sep;

                for ($j = 1; $j <= NEWSLETTER_PROFILE_MAX; $j++) {
                    $column = 'profile_' . $j;
                    echo '"' . $this->sanitize_csv($recipients[$i]->$column) . '"' . $sep;
                }

                for ($j = 1; $j <= NEWSLETTER_LIST_MAX; $j++) {
                    $list = 'list_' . $j;
                    echo '"' . $recipients[$i]->$list . '"' . $sep;
                }

                echo '"' . $recipients[$i]->feed . '"' . $sep;
                echo '"' . $recipients[$i]->followup . '"' . $sep;
                echo '"' . $recipients[$i]->ip . '"' . $sep;
                echo '"' . $recipients[$i]->referrer . '"' . $sep;
                echo '"' . $recipients[$i]->country . '"' . $sep;

                echo "\n";
                flush();
            }
            if (count($recipients) < 500)
                break;
            $page++;
        }
        die();
    }

    function sanitize_csv($text) {
        $text = str_replace('"', "'", $text);
        $text = str_replace("\n", ' ', $text);
        $text = str_replace("\r", ' ', $text);
        $text = str_replace(";", ' ', $text);
        return $text;
    }

    /**
     * Saves a new user on the database. Return false if the email (that must be unique) is already
     * there. For a new users set the token and creation time if not passed.
     *
     * @global Newsletter $newsletter
     * @param type $user
     * @return type
     */
    function save_user($user, $return_format = OBJECT) {
        global $newsletter;
        return $newsletter->save_user($user, $return_format);
    }

    /**
     * Returns a list of users marked as "test user".
     * @global Newsletter $newsletter
     * @return array
     */
    function get_test_users() {
        global $newsletter;
        return $newsletter->get_test_users();
    }

    /** Returns the user identify by an id or an email. If $id_or_email is an object or an array, it is assumed it contains
     * the "id" attribute or key and that is used to load the user.
     *
     * @global Newsletter $newsletter
     * @global type $wpdb
     * @param string|int|object|array $id_or_email
     * @param type $format
     * @return boolean
     */
    function get_user($id_or_email, $format = OBJECT) {
        global $wpdb, $newsletter;
        return $newsletter->get_user($id_or_email, $format);
    }

    /**
     * @global Newsletter $newsletter
     */
    function delete_user($id) {
        global $newsletter;
        return $newsletter->delete_user($id);
    }

    /**
     *
     * @global Newsletter $newsletter
     * @param int|string $id_or_email
     * @param string $status
     * @return boolean
     */
    function set_user_status($id_or_email, $status) {
        global $newsletter;
        return $newsletter->set_user_status($id_or_email, $status);
    }

    function set_user_field($id, $field, $value) {
        $this->store->set_field(NEWSLETTER_USERS_TABLE, $id, $field, $value);
    }

}

NewsletterUsers::instance();

