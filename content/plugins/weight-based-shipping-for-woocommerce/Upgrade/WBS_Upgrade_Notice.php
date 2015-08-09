<?php
    class WBS_Upgrade_Notice
    {
        private $shortHtml;
        private $longHtml;

        public static function createBehaviourChangeNotice($since, $message)
        {
            $shortHtml =
                "Behavior of Weight Based Shipping for WooCommerce changed since {$since}.";

            $longHtml =
                '<p>'.
                    esc_html($message).
                '</p>
                <p>'.
                    'Please <a href="'.esc_html(WC_Weight_Based_Shipping::edit_profile_url()).'">review settings</a>
                    and make appropriate changes if it\'s needed.'.
                '</p>';

            return new self($shortHtml, $longHtml);
        }

        public function __construct($shortHtml, $longHtml)
        {
            if (empty($shortHtml) || empty($longHtml)) {
                throw new InvalidArgumentException();
            }

            $this->shortHtml = $shortHtml;
            $this->longHtml = $longHtml;
        }

        public function getShortHtml()
        {
            return $this->shortHtml;
        }

        public function getLongHtml()
        {
            return $this->longHtml;
        }
    }
?>