<?php
    class WBS_Cart_Item_Bucket
    {
        /** @var WBS_Shipping_Rate_Override */
        private $override;
        private $weight;

        public function __construct(WBS_Shipping_Rate_Override $override, $weight)
        {
            $this->override = $override;
            $this->weight = (float)$weight;
        }

        public function getOverride()
        {
            return $this->override;
        }

        public function getWeight()
        {
            return $this->weight;
        }

        public function addWeight($amount)
        {
            $this->setWeight($this->getWeight() + $amount);
        }

        public function setWeight($weight)
        {
            $this->weight = $weight;
        }
    }
?>