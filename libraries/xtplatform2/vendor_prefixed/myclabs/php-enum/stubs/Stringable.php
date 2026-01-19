<?php /* This file has been prefixed by <PHP-Prefixer> for "XT Platform" */

if (\PHP_VERSION_ID < 80000 && !interface_exists('Stringable')) {
    interface Stringable
    {
        /**
         * @return string
         */
        public function __toString();
    }
}
