<?php
/**
 * @package   T4_BLANK
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

if (class_exists('\T4\T4')) {
    \T4\T4::render($this);
} else {
    include 'error-t4.php';
}
