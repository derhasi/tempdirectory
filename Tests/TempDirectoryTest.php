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
   */
  public function testTempDirectoryClass() {
    $dir = new TempDirectory('test');

    $root = $dir->getRoot();
    $this->assertTrue(file_exists($root) && is_dir($root), 'Temp directory created.');

    unset($dir);
    $this->assertFalse(file_exists($root), 'Temp directory removed on destruction');
  }

  /**
   * Test if directory with same name is generated, two differn directories are
   * created.
   */
  public function testTempSimultanious() {
    $t1 = new TempDirectory('test2');
    $t2 = new TempDirectory('test2');

    $dir1 = $t1->getRoot();
    $this->assertTrue(file_exists($dir1) && is_dir($dir1), 'Temp directory 1 created.');
    unset($t1);
    $this->assertFalse(file_exists($dir1), 'Temp directory 1 removed.');

    $dir2 = $t2->getRoot();
    $this->assertTrue(file_exists($dir2) && is_dir($dir2), 'Temp directory 2 created.');
    unset($t2);
    $this->assertFalse(file_exists($dir2) && is_dir($dir2), 'Temp directory 2 removed.');

  }
}