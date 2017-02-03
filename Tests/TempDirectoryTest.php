<?php

/**
 * @file
 * Contains derhasi\tempdirectory\Tests\TempDirectoryTest.
 */

namespace derhasi\tempdirectory\Tests;

use derhasi\tempdirectory\TempDirectory;

/**
 * Class TempDirectoryTest
 */
class TempDirectoryTest extends \PHPUnit_Framework_TestCase {

  /**
   * Tests the TempDirectory class.
   *
   * @dataProvider directoryNames
   */
  public function testTempDirectoryClass($name) {
    $dir = new TempDirectory($name);

    $root = $dir->getRoot();
    $this->assertIsDir($root, 'Temp directory created.');

    unset($dir);
    $this->assertFileNotExists($root, 'Temp directory removed on destruction');
  }

  /**
   * Test if directory with same name is generated, two differn directories are
   * created.
   *
   * @depends testTempDirectoryClass
   */
  public function testTempSimultanious() {
    $t1 = new TempDirectory('test2');
    $t2 = new TempDirectory('test2');

    $dir1 = $t1->getRoot();
    $this->assertIsDir($dir1, 'Temp directory 1 created.');
    unset($t1);
    $this->assertFileNotExists($dir1, 'Temp directory 1 removed.');

    $dir2 = $t2->getRoot();
    $this->assertIsDir($dir2, 'Temp directory 2 created.');
    unset($t2);
    $this->assertFileNotExists($dir2, 'Temp directory 2 removed.');

  }

  /**
   * Test if temp directory can be removed if it holds files with changed mode.
   *
   * @depends testTempDirectoryClass
   */
  public function testTempPermissions() {
    $dir = new TempDirectory('testTempPermissions');
    $root = $dir->getRoot();

    $protectedFolder = $dir->getPath('protectedFolder');
    $protectedFile = $dir->getPath('protectedFile.txt');
    $fileInProtectedFolder = $dir->getPath('protectedFolder/file.txt');
    $protectedFileInProtectedFolder = $dir->getPath('protectedFolder/protectedFile.txt');

    mkdir($protectedFolder);
    file_put_contents($protectedFile, '');
    file_put_contents($fileInProtectedFolder, '');
    file_put_contents($protectedFileInProtectedFolder, '');

    $this->assertIsDir($root);
    $this->assertIsDir($protectedFolder);
    $this->assertFileExists($protectedFile);
    $this->assertFileExists($protectedFileInProtectedFolder);
    $this->assertFileExists($fileInProtectedFolder);

    chmod($protectedFile, 0400);
    chmod($protectedFileInProtectedFolder, 0400);
    chmod($protectedFolder, 0400);

    // After unsetting the temp directory, the folder should be removed completely.
    unset($dir);
    $this->assertFileNotExists($root);
  }

  /**
   * Test symlinked content.
   *
   * @depends testTempDirectoryClass
   */
  public function testSymlinks() {
    $dir = new TempDirectory('testSymlinks');
    $root = $dir->getRoot();

    $folder = $dir->getPath('folder');
    $file = $dir->getPath('file.txt');
    // Symlinks start with a, so they get processed first.
    $symlinkedFolder = $dir->getPath('asymfolder');
    $symlinkedFile = $dir->getPath('asymfile.txt');

    mkdir($folder);
    file_put_contents($folder . '/test.txt', '');
    file_put_contents($file, '');
    symlink($folder, $symlinkedFolder);
    symlink($file, $symlinkedFile);

    $this->assertIsDir($root);
    $this->assertIsDir($folder);
    $this->assertFileExists($file);
    $this->assertIsDir($symlinkedFolder);
    $this->assertFileExists($symlinkedFile);

    // After unsetting the temp directory, the folder should be removed completely.
    unset($dir);
    $this->assertFileNotExists($root);
  }

  /**
   * Tests if temp directories have a simple name and really are sub
   *
   * @param $name
   *
   * @dataProvider directoryNames
   */
  public function testTempDirectoryName($name) {
    $dir = sys_get_temp_dir();
    $temp = new TempDirectory($name);
    $root = $temp->getRoot();
    $basename = basename($root);
    $this->assertRegExp('/^[\w]+\-[\w]+\-[\w]+$/', $basename, 'Folder name does not match alphanumeric pattern.');
    $parent = dirname($root);
    $this->assertEquals($dir, $parent, 'Folder is not subfolder of system temp dir');
  }

  /**
   * Provides names for a temp directory.
   *
   * @return array
   */
  public function directoryNames() {
    return array(
      array('temp'),
      array('Dir::test'),
      array('!"§$%&/()=?`1'),
      array('derhasi\Component\hello'),
      array('derhasi/component/hello'),
      array(__METHOD__),
    );
  }

  /**
   * Custom assertion for existing directory.
   *
   * @param $path
   * @param string $message
   */
  protected function assertIsDir($path, $message = '') {
    $this->assertTrue(file_exists($path) && is_dir($path), $message);
  }
}
