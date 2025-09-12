<?php

/**
 * @author    andrey-tech
 * @copyright 2025 andrey-tech
 * @link      https://github.com/andrey-tech/
 * @license   MIT
 */

declare(strict_types=1);

use DG\BypassFinals;

require dirname(__DIR__) . '/vendor/autoload.php';

BypassFinals::enable();
BypassFinals::setCacheDirectory('/tmp');
