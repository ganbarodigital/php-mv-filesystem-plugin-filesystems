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
 * @package   Filesystems/V1
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2017-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://ganbarodigital.github.io/php-mv-filesystem-plugin-filesystems
 */

namespace GanbaroDigital\Filesystems\V1;

use GanbaroDigital\Filesystem\V1\FileInfo;
use GanbaroDigital\Filesystem\V1\Filesystem;
use GanbaroDigital\Filesystem\V1\FilesystemContents;
use GanbaroDigital\Filesystem\V1\PathInfo;
use GanbaroDigital\Filesystem\V1\TypeConverters;
use GanbaroDigital\Filesystems\V1\Internal;
use GanbaroDigital\MissingBits\ErrorResponders\OnFatal;

/**
 * looks like one filesystem, but delegates everything to any registered
 * filesystems using a path prefix
 */
class Filesystems implements Filesystem
{
    /**
     * the filesystems that we know about
     *
     * @var array
     */
    protected $filesystems = [];

    /**
     * our constructor
     *
     * @param array $filesystems
     *        a list of filesystems for us to support
     */
    public function __construct(array $filesystems = [])
    {
        foreach ($filesystems as $fs)
        {
            $this->registerFilesystem($fs);
        }
    }

    /**
     * add a filesystem to our list
     *
     * @param  Filesystem $fs
     *         the filesystem to add
     * @return void
     */
    public function registerFilesystem(Filesystem $fs)
    {
        $this->filesystems[$fs->getFilesystemPrefix()] = $fs;
    }

    /**
     * which filesystem is $fullPath pointing to?
     *
     * @param  string|PathInfo $fullPath
     *         the path to examine
     * @return Filesystem
     *         the filesystem that $fullPath points to
     */
    public function getFilesystemForPath($fullPath) : Filesystem
    {
        $pathInfo = TypeConverters\ToPathInfo::from($fullPath);
        return $this->filesystems[$pathInfo->getFilesystemPrefix()];
    }

    // ==================================================================
    //
    // Filesystem API
    //
    // ------------------------------------------------------------------

    /**
     * which prefix should we use for paths on this filesystem?
     *
     * @return string
     */
    public function getFilesystemPrefix() : string
    {
        return '';
    }

    /**
     * retrieve a folder from the filesystem
     *
     * @param  string|PathInfo $fullPath
     *         path to the folder
     * @param  OnFatal $onFatal
     *         what do we do if we do not have the folder?
     * @return FilesystemContents
     */
    public function getFolder($fullPath, OnFatal $onFatal) : FilesystemContents
    {
        $pathInfo = TypeConverters\ToPathInfo::from($fullPath);
        $fs = $this->getFilesystemForPath($pathInfo);
        return $fs->getFolder($pathInfo, $onFatal);
    }

    /**
     * get detailed information about something on the filesystem
     *
     * @param  string|PathInfo $fullPath
     *         the full path to the thing you are interested in
     * @param  OnFatal $onFailure
     *         what do we do if we do not have it?
     * @return FileInfo
     */
    public function getFileInfo($fullPath, OnFatal $onFatal) : FileInfo
    {
        $pathInfo = TypeConverters\ToPathInfo::from($fullPath);
        $fs = $this->getFilesystemForPath($pathInfo);
        return $fs->getFileInfo($pathInfo, $onFatal);
    }

    // ==================================================================
    //
    // PluginProvider interface
    //
    // ------------------------------------------------------------------

    /**
     * return the __NAMESPACE__ for classes provided by this plugin
     *
     * @return string
     *         the __NAMESPACE__ constant
     */
    public function getPluginNamespace() : string
    {
        return __NAMESPACE__;
    }
}
