<?php /* This file has been prefixed by <PHP-Prefixer> for "XT Social Libraries" */

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

use XTS_BUILD\SimplePie\File;

class_exists('SimplePie\File');

// @trigger_error(sprintf('Using the "XTS_SimplePie_File" class is deprecated since SimplePie 1.7.0, use "XTS_BUILD\SimplePie\File" instead.'), \E_USER_DEPRECATED);

/** @phpstan-ignore-next-line */
if (\false) {
    /** @deprecated since SimplePie 1.7.0, use "XTS_BUILD\SimplePie\File" instead */
    class XTS_SimplePie_File extends File
    {
    }
}
