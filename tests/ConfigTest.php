<?php

declare(strict_types=1);

use Core\Exception\ConfigFileException;
use PHPUnit\Framework\TestCase;
use App\Libs\Config;
use App\Libs\PhpFileConfig;

final class ConfigTest extends TestCase
{

    private Config $config;

    private const CONFIG_FILE = __DIR__ . '/config.php';

    /**
     * @throws ConfigFileException
     */
    public function setup(): void
    {
        $this->config = new Config(PhpFileConfig::load($this::CONFIG_FILE));

        parent::setUp();
    }

    public function testGetWithExistingKey(): void
    {
        $this->assertEquals('john doe', $this->config->get('user'));
    }

    public function testGetWithBadKey(): void
    {
        $this->assertNull($this->config->get('unknown-param'));
    }

    public function testGetKeyWithDefaultValue(): void
    {
        $this->assertEquals('john doe', $this->config->get('user', 'Chuck'));
    }

    public function testGetBadKeyWithDefaultValue(): void
    {
        $this->assertEquals('Mike', $this->config->get('unknown-param', 'Mike'));
    }

    public function testHasWithExistingKey()
    {
        $this->assertTrue($this->config->has('user'));
    }

    public function testHasWithNonExistingKey()
    {
        $this->assertFalse($this->config->has('unknown-key'));
    }

    public function testAll()
    {
        $this->assertIsArray($this->config->all());
    }

    public function testSet()
    {
        $this->config->set('user', 'Bob');
        $this->assertEquals('Bob', $this->config->get('user'));
    }

    public function testWrite()
    {
        $this->assertIsInt($this->config->write($this::CONFIG_FILE));
    }

    public function testWriteWithSet()
    {
        $this->config->set('user', 'Bob');
        $this->config->write(__DIR__ . '/config-new.php');

        $newConfig = new Config(PhpFileConfig::load(__DIR__ . '/config-new.php'));
        $this->assertEquals('Bob', $newConfig->get('user'));
    }

    public function testCountWithoutArrays()
    {
        $this->assertEquals(0, $this->config->countArrays());
    }
}

