<?php
    class WBS_Profile_Manager
    {
        private static $instance;
        private $ordered_profiles;

        /** @var WC_Weight_Based_Shipping[] */
        private $profile_instances;


        public static function setup()
        {
            self::instance();
        }

        public static function instance($reset_cache = false)
        {
            if (!isset(self::$instance))
            {
                self::$instance = new self();
            }

            if ($reset_cache)
            {
                unset(self::$instance->ordered_profiles);
                unset(self::$instance->profile_instances);
            }

            return self::$instance;
        }

        /** @return WC_Weight_Based_Shipping[] */
        public function profiles()
        {
            if (!isset($this->ordered_profiles))
            {
                $this->ordered_profiles = array();

                /** @var WC_Shipping $shipping */
                $shipping = WC()->shipping;
                foreach ($shipping->load_shipping_methods() as $method)
                {
                    if ($method instanceof WC_Weight_Based_Shipping)
                    {
                        $this->ordered_profiles[] = $method;
                    }
                }
            }

            return $this->ordered_profiles;
        }

        public function profile($name = null)
        {
            $this->find_suitable_id($name);
            $profiles = $this->instantiate_profiles();
            return @$profiles[$name];
        }

        public function profile_exists($name)
        {
            $profiles = $this->instantiate_profiles();
            return isset($profiles[$name]);
        }

        public function find_suitable_id(&$profile_id)
        {
            if (!$profile_id && !($profile_id = $this->current_profile_id())) {
                return $profile_id = null;
            }

            $id_base = 'WC_Weight_Based_Shipping';

            // Upgrade previous version data
            $prev_option_name = "woocommerce_{$id_base}_settings";
            if (($data = get_option($prev_option_name)) !== false)
            {
                update_option("woocommerce_{$id_base}_main_settings", $data);
                delete_option($prev_option_name);
            }

            $id = "{$id_base}_{$profile_id}";

            return $id;
        }

        public function current_profile_id()
        {
            $profile_id = null;

            if (is_admin())
            {
                if (empty($profile_id))
                {
                    $profile_id = @$_GET['wbs_profile'];
                }

                if (empty($profile_id) && ($profiles = $this->profiles()))
                {
                    $profile_id = $profiles[0]->profile_id;
                }

                if (empty($profile_id))
                {
                    $profile_id = 'main';
                }
            }

            return $profile_id;
        }

        public function new_profile_id()
        {
            if (!$this->profile_exists('main')) {
                return 'main';
            }

            $timestamp = time();

            $i = null;
            do {
                $new_profile_id = trim($timestamp.'-'.$i++, '-');
            } while ($this->profile_exists($new_profile_id));

            return $new_profile_id;
        }

        public function _register_profiles($methods)
        {
            return array_merge($methods, $this->instantiate_profiles());
        }

        private function __construct()
        {
            add_filter('woocommerce_shipping_methods', array($this, '_register_profiles'));
        }

        private function instantiate_profiles()
        {
            if (!isset($this->profile_instances))
            {
                $this->profile_instances = array();

                $profileIds = array();
                {
                    foreach (array_keys(wp_load_alloptions()) as $option)
                    {
                        $matches = array();
                        if (preg_match("/^woocommerce_WC_Weight_Based_Shipping_(\\w+)_settings$/", $option, $matches)) {
                            $profileIds[] = $matches[1];
                        }
                    }

                    if (empty($profileIds)) {
                        $profileIds[] = $this->new_profile_id();
                    }
                }

                foreach ($profileIds as $profileId) {
                    $this->profile_instances[$profileId] = new WC_Weight_Based_Shipping($profileId);
                }

                if (is_admin() &&
                    ($editingProfileId = @$_GET['wbs_profile']) &&
                    !isset($this->profile_instances[$editingProfileId]))
                {
                    $editingProfile = new WC_Weight_Based_Shipping($editingProfileId);
                    $editingProfile->_stub = true;
                    $this->profile_instances[$editingProfileId] = $editingProfile;
                }

                if ($currentProfile = $this->profile()) {
                    add_action(
                        'woocommerce_update_options_shipping_' . $currentProfile->id,
                        array($currentProfile, 'process_admin_options')
                    );
                }
            }

            return $this->profile_instances;
        }
    }
?>