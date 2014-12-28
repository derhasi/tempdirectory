<?php

namespace derhasi\tempdirectory;

class TempDirectory {

  function __destruct() {
    print "Zerstoere " . $this->name . "\n";
  }
}