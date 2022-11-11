<?php declare(strict_types=1);

use Core\Utils\DateTimeUtil;
use PHPUnit\Framework\TestCase;

final class DateTimeUtilTest extends TestCase
{
    public function testCheckDate(): void
    {
        self::assertEquals(true, DateTimeUtil::checkDate('2022-09-24 15:00:00'));
    }

    public function testCheckDateWithBadParameter(): void
    {
        self::assertEquals(false, DateTimeUtil::checkDate('foo bar'));
    }
}
