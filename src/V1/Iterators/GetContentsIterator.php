<?php

/**
 * Copyright (c) 2017-present Ganbaro Digital Ltd
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Libraries
 * @package   Filesystems\V1\Iterators
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2017-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://ganbarodigital.github.io/php-mv-filesystem-plugin-filesystems
 */

namespace GanbaroDigital\Filesystems\V1\Iterators;

use GanbaroDigital\AdaptersAndPlugins\V1\Operations\CallPlugin;
use GanbaroDigital\AdaptersAndPlugins\V1\PluginTypes\PluginClass;
use GanbaroDigital\Filesystem\V1\PathInfo;
use GanbaroDigital\Filesystem\V1\TypeConverters;
use GanbaroDigital\Filesystems\V1\Filesystems;
use GanbaroDigital\MissingBits\ErrorResponders\OnFatal;

use RecursiveIterator;

/**
 * get an iterator for searching the local filesystem
 */
class GetContentsIterator implements PluginClass
{
    /**
     * get an iterator for searching the local filesystem
     *
     * @param  Filesystems $fs
     *         our filesystem
     * @param  string|PathInfo $path
     *         where we want to search from
     * @param  OnFatal $onFatal
     *         what do we do if we cannot create the iterator?
     * @return RecursiveIterator
     *         the iterator to use
     */
    public static function for(Filesystems $fs, $path, OnFatal $onFatal) : RecursiveIterator
    {
        // what are we looking at?
        $pathInfo = TypeConverters\ToPathInfo::from($path);

        // which filesystem do we really want?
        $realFs = $fs->getFilesystemForPath($pathInfo);

        // ask that filesystem for the iterator
        return CallPlugin::using($realFs, 'Iterators\\GetContentsIterator', 'for', $realFs, $pathInfo, $onFatal);
    }
}