<?php
    if (!function_exists('WC'))
    {
        /**
         * @return Woocommerce
         */
        function WC()
        {
            return $GLOBALS['woocommerce'];
        }
    }

    function wbst($template, $args)
    {
        $result = null;

        if (!empty($template))
        {
            if (is_array($args))
            {
                $replacements = array();
                foreach ($args as $key => $value)
                {
                    $replacements["{{{$key}}}"] = $value;
                }

                $result = strtr($template, $replacements);
            }
            else
            {
                $result = preg_replace('/\{\{.*?\}\}/', $args, $template);
            }
        }

        return $result;
    }
?>