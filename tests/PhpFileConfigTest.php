<?php

namespace App\Tests;

use App\Libs\PhpFileConfig;
use Core\Exception\ConfigFileException;
use PHPUnit\Framework\TestCase;

class PhpFileConfigTest extends TestCase
{

    /**
     * @throws ConfigFileException
     */
    public function testNewIsArray()
    {
        $this->assertIsArray(PhpFileConfig::load(__DIR__ . '/config.php'));
    }

    public function testNewWithException()
    {
        $this->expectException(ConfigFileException::class);
        PhpFileConfig::load(__DIR__ . 'unknown-config.php');
    }
}
