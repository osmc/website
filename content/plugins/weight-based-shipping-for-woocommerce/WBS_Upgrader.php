<?php
    class WBS_Upgrader
    {
        public static function setup($pluginFile)
        {
            if (!isset(self::$instance)) {
                $upgrader = new self($pluginFile);
                $upgrader->onLoad();
                self::$instance = $upgrader;
            }
        }

        public static function instance()
        {
            return self::$instance;
        }

        public function __construct($pluginFile)
        {
            $this->pluginFile = $pluginFile;
            $this->upgradeNotices = new WBS_Upgrade_Notices('woowbs_upgrade_notices', 'woowbs_remove_upgrade_notice');
        }

        public function onLoad()
        {
            $this->setupHooks();
        }

        public function onAdminInit()
        {
            $this->checkForUpgrade();
        }

        public function onAdminNotices()
        {
            $this->upgradeNotices->show();
        }

        public function removeUpgradeNotices()
        {
            $id = @$_GET[$this->upgradeNotices->getRemoveNoticeUrlFlagName()];
            if (!isset($id)) {
                return false;
            }

            return $this->upgradeNotices->remove($id);
        }

        /** @var WBS_Upgrader */
        private static $instance;
        private $pluginFile;
        private $upgradeNotices;

        private function checkForUpgrade()
        {
            $previousVersion = get_option('woowbs_version');
            if (empty($previousVersion)) {
                return;
            }

            //$previousVersion = '2.2.0';

            $currentVersion = get_plugin_data($this->pluginFile, false, false);
            $currentVersion = $currentVersion['Version'];

            if ($previousVersion !== $currentVersion) {
                if (version_compare($previousVersion, '2.2.1') < 0) {
                    $this->upgradeNotices->add(WBS_Upgrade_Notice::createBehaviourChangeNotice('2.2.1', '
                        Previously, weight based shipping option has not been shown to user if total
                        weight of their cart is zero. Since version 2.2.1 this is changed so shipping
                        option is available to user with price set to Handling Fee. If it does not
                        suite your needs well you can return previous behavior by setting Min Weight
                        to something a bit greater zero, e.g. 0.001, so that zero-weight orders will
                        not match constraints and the shipping option will not be shown.
                    '));
                }

                if (version_compare($previousVersion, '2.4.0') < 0) {
                    $existing_profiles = WBS_Profile_Manager::instance()->profiles();
                    foreach ($existing_profiles as $profile) {
                        $option = $profile->get_wp_option_name();
                        $config = get_option($option);
                        $config['extra_weight_only'] = 'no';
                        update_option($option, $config);
                    }
                }

                if (version_compare($previousVersion, '2.6.3') < 0) {
                    $this->upgradeNotices->add(WBS_Upgrade_Notice::createBehaviourChangeNotice('2.6.2', '
                        Previously, base Handling Fee has not been added to the shipping price if user
                        cart contained only items with shipping classes specified in the Shipping
                        Classes Overrides section. Starting from version 2.6.2 base Handling Fee is
                        applied in any case. This behavior change is only affecting you if you use
                        Shipping Classes Overrides. If you don\'t use it just ignore this message.
                    '));
                }

                update_option('woowbs_version', $currentVersion);
            }
        }

        private function setupHooks()
        {
            add_action('admin_init', array($this, 'onAdminInit'));
            add_action('admin_notices', array($this, 'onAdminNotices'));
        }
    }
?>