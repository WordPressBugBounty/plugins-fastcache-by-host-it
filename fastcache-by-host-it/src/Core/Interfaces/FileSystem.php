<?php

/**
 * FastCache - Performs several front-end optimizations for fast downloads
 *
 * @package   FastCache
 * @author    Host.it <info@host.it>
 * @copyright Copyright (c) 2025-2026 FastCache
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace FastCache\Core\Interfaces;

defined( '_FASTCACHE_EXEC' ) or die( 'Restricted access' );

interface FileSystem
{
	/**
	 *
	 * @param   string  $path
	 */
	public static function deleteFolder( $path );


	/**
	 *
	 * @param   string  $path
	 */
	public static function createFolder( $path );

	/**
	 *
	 * @param   string  $file
	 * @param   string  $contents
	 *
	 * @return bool
	 */
	public static function write( $file, $contents );

	/**
	 *
	 * @param   string   $path       Path of folder to read
	 * @param   string   $filter     A regex filter for file names
	 * @param   boolean  $recurse    True to recurse into sub-folders
	 * @param   array    $exclude    An array of files to exclude
	 *
	 * @return array        Full paths of files in the folder recursively
	 */
	public static function lsFiles(
		$path, $filter = '.', $recurse = false, $exclude = array(
		'.svn',
		'CVS',
		'.DS_Store',
		'__MACOSX'
	)
	);
}