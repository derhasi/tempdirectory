<?php

/**
 * @file
 * Contains \derhasi\tempdirectory\TempDirectory.
 */

namespace derhasi\tempdirectory;

/**
 * Class TempDirectory.
 */
class TempDirectory {

  /**
   * Root path for the temporary directory.
   *
   * @var string
   */
  protected $root;

  /**
   * Name for the temp directory.
   *
   * @var string
   */
  protected $name;

  /**
   * Unique ID for building the root.
   *
   * @var string
   */
  protected $subdir;

  /**
   * Constructor.
   *
   * @param string $name
   *   An optional name for building multiple
   */
  function __construct($name = "") {
    $this->name = $name;
    $this->setSubdir();
    $this->createDirectory();
  }

  /**
   * Destructor.
   */
  public function __destruct() {
    $this->destroyDirectory();
  }

  /**
   * Provides the temp directory root for this instance.
   *
   * @return string
   */
  public function getRoot() {
    return $this->root;
  }

  /**
   * Provides the absolute path for a temp directory subfolder.
   *
   * @param string $subfolder
   *   Relative path of the subfolder within the temporary directory.
   * @return string
   *   Absolute path to the subfolder within the directory.
   */
  public function getPath($subfolder = '') {
    // Remove leading and trailing slashes.
    trim($subfolder, '/');
    if (strpos($subfolder, '..') !== FALSE) {
      throw new \Exception('Subfolder must not contain any "..".');
    }

    if (strlen($subfolder)) {
      return $this->root . '/'. $subfolder;
    }
    return $this->root;
  }

  protected function getSubdir() {
    if (!isset($this->subdir)) {
      $this->setSubdir();
    }
    return $this->subdir;
  }
  /**
   * Creates a unique name for the subdirectory.
   */
  protected function setSubdir() {
    $sha = sha1($this->name);
    $prefix = preg_replace('/[^A-z0-9]/', '', $this->name);
    $time = time();
    $this->subdir = "$prefix-$sha-$time";
  }

  /**
   * Creates the directory for this instance.
   */
  protected function createDirectory() {
    if (!isset($this->root)) {
      $this->root = sys_get_temp_dir() . '/' . $this->getSubdir();
      mkdir($this->root);
    }
  }

  /**
   * Removes directory for this instance.
   */
  protected function destroyDirectory() {
    TempDirectory::removeRecursive($this->root);
  }

  /**
   * This function recursively deletes all files and folders under the given
   * directory, and then the directory itself.
   *
   * equivalent to Bash: rm -r $path
   * @param string $path
   */
  public static function removeRecursive($path) {
    // If the path is not a directory we can simply unlink it.
    if (!is_dir($path)) {
      return unlink($path);
    }

    // Otherwise we go through the whole directory.
    $it = new \RecursiveDirectoryIterator($path);
    $it = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($it as $file) {
      if ('.' === $file->getBasename() || '..' === $file->getBasename()) {
        continue;
      }
      if ($file->isDir()) {
        rmdir($file->getPathname());
      }
      else {
        unlink($file->getPathname());
      }
    }
    return rmdir($path);
  }
}