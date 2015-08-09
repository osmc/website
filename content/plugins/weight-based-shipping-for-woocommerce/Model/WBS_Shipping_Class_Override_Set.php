<?php
    class WBS_Shipping_Class_Override_Set
    {
        /** @var WBS_Shipping_Rate_Override[] */
        private $overrides;

        public function __construct()
        {
            $this->overrides = array();
        }

        public function add(WBS_Shipping_Rate_Override $override)
        {
            $this->overrides[$override->getClass()] = $override;
        }

        public function findByClass($class)
        {
            return @$this->overrides[$class];
        }

        public function listAll()
        {
            return $this->overrides;
        }
    }
?>