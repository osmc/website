<?php
    class WBS_Shipping_Rate_Override
    {
        private $class;
        private $fee;
        private $rate;
        private $weightStep;

        public function __construct($class, $fee, $rate, $weightStep)
        {
            $this->setClass($class);
            $this->setFee($fee);
            $this->setRate($rate);
            $this->setWeightStep($weightStep);
        }

        public function getClass()
        {
            return $this->class;
        }

        public function getFee()
        {
            return $this->fee;
        }

        public function getRate()
        {
            return $this->rate;
        }

        public function getWeightStep()
        {
            return $this->weightStep;
        }

        private function setClass($class)
        {
            if (empty($class)) {
                throw new InvalidArgumentException("Please provide class for shipping class override");
            }

            $this->class = $class;
        }

        private function setFee($fee)
        {
            $this->fee = self::sanitize($fee);
        }

        private function setRate($rate)
        {
            $this->rate = self::sanitize($rate);
        }

        private function setWeightStep($weightStep)
        {
            if (empty($weightStep)) {
                $weightStep = null;
            }

            $this->weightStep = $weightStep;
        }

        private static function sanitize($value, $allowNnull = false, $sanitizer = 'floatval')
        {
            if ($value !== null || !$allowNnull) {
                $value = $sanitizer($value);
            }

            return $value;
        }

        private static function ifnull($one, $other)
        {
            return $one !== null ? $one : $other;
        }
    }
?>