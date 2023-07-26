<?php

namespace Stephenchen\LineBot\Tests\Unit;

use Orchestra\Testbench\TestCase;
use Stephenchen\LineBot\LineBot;
use Stephenchen\LineBot\LineBotTrait;

class LineBotTest extends TestCase
{
    use LineBotTrait;

    public function test_echo()
    {
        $result = 'Stephenchen\LineBot\LineBot';                               // Arrange
        $object = new LineBot();                           // Act
        $this->assertSame($object->echo(), $result);  // Assert
    }

    public function test_22()
    {
        $this->getNumberOfSentThisMonth();

    }
}