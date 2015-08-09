<?php
    class WBS_Loader
    {
        public static function loadWbs($pluginFile)
        {
            if (!self::$loaded)
            {
                self::$loaded = true;
                new WBS_Loader($pluginFile);
            }
        }

        public function __construct($pluginFile)
        {
            $this->pluginFile = $pluginFile;
            add_action('plugins_loaded', array($this, 'load'), 0);
        }

        public function load()
        {
            if (!$this->woocommerceAvailable()) return;
            $this->loadLanguage();
            $this->loadFunctions();
            $this->loadClasses();
            WBS_Profile_Manager::setup();
            WBS_Upgrader::setup($this->pluginFile);
        }

        private static $loaded;
        private $pluginFile;

        private function woocommerceAvailable()
        {
            return class_exists('WC_Shipping_Method');
        }

        private function loadLanguage()
        {
            load_plugin_textdomain('woowbs', false, dirname(plugin_basename($this->pluginFile)).'/lang/');
        }

        private function loadFunctions()
        {
            require_once(dirname(__FILE__) . "/functions.php");
        }

        private function loadClasses()
        {
            $wbsdir = dirname(__FILE__);
            require_once("{$wbsdir}/WBS_Profile_Manager.php");
            require_once("{$wbsdir}/Upgrade/WBS_Upgrade_Notice.php");
            require_once("{$wbsdir}/Upgrade/WBS_Upgrade_Notices.php");
            require_once("{$wbsdir}/WBS_Upgrader.php");
            require_once("{$wbsdir}/WC_Weight_Based_Shipping.php");
            require_once("{$wbsdir}/Model/WBS_Shipping_Class_Override.php");
            require_once("{$wbsdir}/Model/WBS_Shipping_Class_Override_Set.php");
            require_once("{$wbsdir}/Model/WBS_Cart_Item_Bucket.php");
        }
    }
?>