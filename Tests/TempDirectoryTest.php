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
}