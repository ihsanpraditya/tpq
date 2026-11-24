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

/**
 * by Gemini
 * The goal of this file is to allow you to miss grating
 * your own functions, as well as overwrite the standard
 * ones provided by the framework.
 *
 * See the user guide for more information:
 * https://codeigniter.com/user_guide/general/common_functions.html
 */

if (! function_exists('format_rupiah')) {
    function format_rupiah(int|float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
