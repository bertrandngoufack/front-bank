<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

// Fallback shim for PHP intl Locale class when the intl extension is missing.
// This avoids fatal errors like "Class 'Locale' not found" while still allowing
// the application to run. It only implements the minimal methods used by CI4.
if (!class_exists('Locale')) {
    class Locale
    {
        protected static string $default = 'en';

        public static function getDefault(): string
        {
            return static::$default;
        }

        public static function setDefault(string $locale): bool
        {
            static::$default = $locale ?: 'en';
            return true;
        }
    }
}
