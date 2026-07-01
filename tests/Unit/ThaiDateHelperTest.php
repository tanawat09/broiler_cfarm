<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ThaiDateHelperTest extends TestCase
{
    public function test_thai_date_uses_buddhist_year(): void
    {
        $this->assertSame('09/04/2569', thai_date('2026-04-09'));
        $this->assertSame('-', thai_date(null));
    }
}
