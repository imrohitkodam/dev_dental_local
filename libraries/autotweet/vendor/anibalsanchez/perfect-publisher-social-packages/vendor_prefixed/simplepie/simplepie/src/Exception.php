<?php /* This file has been prefixed by <PHP-Prefixer> for "XT Social Libraries" */

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace XTS_BUILD\SimplePie;

use Exception as NativeException;

/**
 * General SimplePie exception class
 */
class Exception extends NativeException
{
}

class_alias('XTS_BUILD\SimplePie\Exception', 'XTS_SimplePie_Exception');
