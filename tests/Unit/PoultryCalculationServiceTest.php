<?php

namespace Tests\Unit;

use App\Services\PoultryCalculationService;
use PHPUnit\Framework\TestCase;

class PoultryCalculationServiceTest extends TestCase
{
    public function test_loss_and_remaining_birds_are_calculated(): void
    {
        $service = new PoultryCalculationService();

        $this->assertSame(5, $service->calculateDeadTotal(2, 3));
        $this->assertSame(5, $service->calculateCullTotal(1, 4));
        $this->assertSame(10, $service->calculateLossTotal(2, 3, 1, 4));
        $this->assertSame(1690, $service->calculateRemainingBirds(1700, 10));
    }

    public function test_mortality_rate_and_fcr_are_calculated(): void
    {
        $service = new PoultryCalculationService();

        $this->assertSame(0.59, $service->calculateMortalityRate(1700, 10));
        $this->assertSame(2550.0, $service->calculateTotalWeight(1700, 1.5));
        $this->assertSame(0.11, $service->calculateFcr(270, 1700, 1.5));
        $this->assertNull($service->calculateFcr(270, 1700, null));
    }

    public function test_remaining_feed_is_calculated(): void
    {
        $service = new PoultryCalculationService();

        $this->assertSame(374.5, $service->calculateRemainingFeed(500, 125.5));
    }
}
