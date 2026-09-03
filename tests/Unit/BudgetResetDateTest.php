<?php

namespace Tests\Unit;

use App\Services\BudgetResetDate;
use Carbon\Carbon;
use Tests\TestCase;

class BudgetResetDateTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_reset_happens_on_the_chosen_day_when_the_month_has_that_day(): void
    {
        $this->assertTrue(BudgetResetDate::isResetDay(31, Carbon::parse('2026-05-31')));
        $this->assertFalse(BudgetResetDate::isResetDay(31, Carbon::parse('2026-05-15')));
        $this->assertFalse(BudgetResetDate::isResetDay(31, Carbon::parse('2026-04-30')));
    }

    public function test_reset_falls_to_the_first_when_the_previous_month_lacks_the_day(): void
    {
        $this->assertTrue(BudgetResetDate::isResetDay(31, Carbon::parse('2026-05-01')));
        $this->assertTrue(BudgetResetDate::isResetDay(31, Carbon::parse('2026-07-01')));
        $this->assertFalse(BudgetResetDate::isResetDay(31, Carbon::parse('2026-01-01')));
        $this->assertTrue(BudgetResetDate::isResetDay(1, Carbon::parse('2026-05-01')));
    }

    public function test_february_overflow_resets_on_march_first(): void
    {
        $this->assertTrue(BudgetResetDate::isResetDay(29, Carbon::parse('2026-03-01')));
        $this->assertTrue(BudgetResetDate::isResetDay(30, Carbon::parse('2026-03-01')));
        $this->assertTrue(BudgetResetDate::isResetDay(31, Carbon::parse('2026-03-01')));
        $this->assertFalse(BudgetResetDate::isResetDay(28, Carbon::parse('2026-03-01')));
        $this->assertTrue(BudgetResetDate::isResetDay(28, Carbon::parse('2026-02-28')));
    }

    public function test_leap_february_keeps_the_29th_and_overflows_30_and_31_to_march_first(): void
    {
        $this->assertTrue(BudgetResetDate::isResetDay(29, Carbon::parse('2028-02-29')));
        $this->assertFalse(BudgetResetDate::isResetDay(29, Carbon::parse('2028-03-01')));
        $this->assertTrue(BudgetResetDate::isResetDay(30, Carbon::parse('2028-03-01')));
        $this->assertTrue(BudgetResetDate::isResetDay(31, Carbon::parse('2028-03-01')));
    }

    public function test_next_occurrence_uses_the_first_of_the_next_month_when_the_day_is_missing(): void
    {
        $this->assertSame(
            '2026-05-01',
            BudgetResetDate::nextOccurrence(31, Carbon::parse('2026-04-15'))->toDateString()
        );
        $this->assertSame(
            '2026-03-01',
            BudgetResetDate::nextOccurrence(31, Carbon::parse('2026-02-10'))->toDateString()
        );
        $this->assertSame(
            '2026-03-01',
            BudgetResetDate::nextOccurrence(29, Carbon::parse('2026-02-10'))->toDateString()
        );
        $this->assertSame(
            '2026-05-31',
            BudgetResetDate::nextOccurrence(31, Carbon::parse('2026-05-01'))->toDateString()
        );
        $this->assertSame(
            '2026-07-01',
            BudgetResetDate::nextOccurrence(31, Carbon::parse('2026-05-31'))->toDateString()
        );
    }
}
