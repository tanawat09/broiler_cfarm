<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        if (
            ! extension_loaded('pdo_sqlite')
            && in_array(RefreshDatabase::class, class_uses_recursive(static::class), true)
        ) {
            $this->markTestSkipped('ต้องเปิด PHP extension pdo_sqlite เพื่อรัน feature tests ที่ใช้ฐานข้อมูล SQLite memory');
        }

        parent::setUp();
    }
}
