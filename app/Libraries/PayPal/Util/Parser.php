<?php

namespace Payment\PayPal\Util
{
    class Parser
    {
        public static function getDateTimeFromPayPal($value) {
            return date_parse_from_format("yyyy-MM-ddTHH:mm:ssZ", $value);
        }
    }
}
