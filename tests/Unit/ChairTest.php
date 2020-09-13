<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Http\Helpers\ChairHelper;
use InvalidArgumentException;

class ChairTest extends TestCase
{
    public function testCanBeGiveValidEmailAddress(): void
    {
        $this->assertTrue(
            ChairHelper::ensureIsValidEmail('user@example.com')
        );
    }

    public function testCannotBeGiveInvalidEmailAddress(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ChairHelper::ensureIsValidEmail('invalid');
    }
}
