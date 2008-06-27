<?php

error_reporting(E_ALL|E_STRICT);
if (!ini_get('date.timezone'))
	date_default_timezone_set('CET');
define('DWOO_CACHE_DIR', dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.'cache');
define('DWOO_COMPILE_DIR', dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.'compiled');

require dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'Dwoo.php';
define('TEST_DIRECTORY', dirname(__FILE__));

class DwooTests extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		PHPUnit_Util_Filter::addDirectoryToWhitelist(DWOO_DIRECTORY.'plugins/builtin');
		PHPUnit_Util_Filter::addDirectoryToWhitelist(DWOO_DIRECTORY.'Dwoo');
		PHPUnit_Util_Filter::addFileToWhitelist(DWOO_DIRECTORY.'Dwoo.php');

		$suite = new self('Dwoo - Unit Tests Report');

		foreach (new DirectoryIterator(dirname(__FILE__)) as $file) {
			if (!$file->isDot() && !$file->isDir() && (string) $file !== 'DwooTests.php' && substr((string) $file, -4) === '.php') {
				require_once $file->getPathname();
				$suite->addTestSuite(basename($file, '.php'));
			}
		}

		return $suite;
	}

		protected function tearDown() {
			$this->clearDir(TEST_DIRECTORY.'/temp/compiled', true);
	}

	protected function clearDir($path, $emptyOnly=false)
	{
			if (is_dir($path)) {
					foreach (glob($path.'/*') as $f)
							$this->clearDir($f);
					if (!$emptyOnly) {
						rmdir($path);
					}
			} else {
					unlink($path);
				}
	}
}

// Evaluates two strings and ignores differences in line endings (\r\n == \n == \r)
class DwooConstraintStringEquals extends PHPUnit_Framework_Constraint
{
	protected $target;

	public function __construct($target)
	{
		$this->target = preg_replace('#(\r\n|\r)#', "\n", $target);
	}

	public function evaluate($other)
	{
		$this->other = preg_replace('#(\r\n|\r)#', "\n", $other);
		return $this->target == $this->other;
	}

	public function toString()
	{
		return 'equals expected value.'.PHP_EOL.'Expected:'.PHP_EOL.$this->target.PHP_EOL.'Received:'.PHP_EOL.$this->other.PHP_EOL;
	}
}
