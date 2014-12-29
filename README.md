# tempdirectory

Provides a temporary directory object, that will kill remove the whole directory on destruction or shutdown.

## Installation

Simpyl require with composer: `composer require --dev derhasi/tempdirectory`.

## Usage

Example from [composer-preserver-paths](https://github.com/derhasi/composer-preserve-paths/tree/master/tests):

```php

$workingDirectory = new TempDirectory('path-preserver-test-working');
// Create directory to test.
$folder1 = $this->workingDirectory->getPath('folder1');
mkdir($folder1);
$file1 = $this->workingDirectory->getPath('file1.txt');
file_put_contents($file1, 'Test content');
```