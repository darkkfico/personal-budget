<?php

namespace Tests\Unit;

use App\Models\AutoBudget;
use App\Models\CustomBudget;
use App\Services\DailyAllowenceService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

class DailyAllowenceServiceTest extends TestCase
{
    private DailyAllowenceService $allowence;

    protected function setUp(): void
    {
        parent::setUp();

        $this->session([]);
        $this->allowence = new DailyAllowenceService();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_remaining_days_are_calendar_days_even_in_the_evening(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-04-10 20:00:00'));

        $result = $this->allowence->forAuto($this->autoBudget(13), collect());

        $this->assertSame(3, $result['daysDiff']);
        $this->assertSame(2667.0, $result['sub1']);
    }

    public function test_day_one_spending_does_not_inflate_leftover_on_day_two_or_three(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-04-10 12:00:00'));
        $budget = $this->autoBudget(30);

        $day1 = $this->allowence->forAuto($budget, collect());
        $this->assertSame(20, $day1['daysDiff']);
        $this->assertSame(400.0, $day1['sub1']);

        $afterSpend = $this->allowence->forAuto($budget, $this->grocery(100));
        $this->assertSame(300.0, $afterSpend['sub1']);

        Carbon::setTestNow(Carbon::parse('2026-04-11 12:00:00'));
        $day2 = $this->allowence->forAuto($budget, $this->grocery(100));
        $this->assertSame(19, $day2['daysDiff']);
        $this->assertSame(416.0, $day2['sub1']);

        $day2AfterSpend = $this->allowence->forAuto($budget, $this->grocery(150));
        $this->assertSame(366.0, $day2AfterSpend['sub1']);

        Carbon::setTestNow(Carbon::parse('2026-04-12 12:00:00'));
        $day3 = $this->allowence->forAuto($budget, $this->grocery(150));
        $this->assertSame(18, $day3['daysDiff']);
        $this->assertSame(436.0, $day3['sub1']);
    }

    public function test_same_calendar_day_does_not_reset_allowance_in_the_evening(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-04-10 09:00:00'));
        $budget = $this->autoBudget(30);

        $this->allowence->forAuto($budget, collect());
        $this->assertSame(300.0, $this->allowence->forAuto($budget, $this->grocery(100))['sub1']);

        Carbon::setTestNow(Carbon::parse('2026-04-10 22:00:00'));
        $evening = $this->allowence->forAuto($budget, $this->grocery(100));

        $this->assertSame(20, $evening['daysDiff']);
        $this->assertSame(300.0, $evening['sub1']);
    }

    public function test_zero_leftover_stays_frozen_for_the_day(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-04-10 12:00:00'));
        $budget = $this->autoBudget(30);

        $this->allowence->forAuto($budget, collect());
        $used = $this->allowence->forAuto($budget, $this->grocery(400));

        $this->assertSame(0.0, $used['sub1']);
        $this->assertTrue($used['warnDaily']);
        $this->assertSame(0.0, $this->allowence->forAuto($budget, $this->grocery(400))['sub1']);
    }

    public function test_auto_ignores_savings_items_when_subtracting(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-04-10 12:00:00'));
        $budget = $this->autoBudget(30);

        $this->allowence->forAuto($budget, collect());

        $result = $this->allowence->forAuto($budget, collect([
            (object) ['field_name' => 'Savings', 'item_amount' => 500],
            (object) ['field_name' => 'Groceries', 'item_amount' => 100],
        ]));

        $this->assertSame(300.0, $result['sub1']);
    }

    public function test_custom_spending_resets_to_remaining_over_days_on_day_two(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-04-10 12:00:00'));
        $budget = new CustomBudget([
            'budget_amount' => 10000,
            'currency' => 'MKD',
            'reset_date' => 30,
        ]);
        $skip = collect([2]);

        $this->allowence->forCustom($budget, collect(), $skip);
        $day1 = $this->allowence->forCustom($budget, $this->customItems(100), $skip);
        $this->assertSame(400.0, $day1['sub1']);

        Carbon::setTestNow(Carbon::parse('2026-04-11 12:00:00'));
        $day2 = $this->allowence->forCustom($budget, $this->customItems(100), $skip);
        $this->assertSame(19, $day2['daysDiff']);
        $this->assertSame(521.0, $day2['sub1']);
    }

    public function test_clear_drops_frozen_allowance(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-04-10 12:00:00'));
        $budget = $this->autoBudget(30);

        $this->allowence->forAuto($budget, collect());
        $this->allowence->forAuto($budget, $this->grocery(100));
        $this->allowence->clear();

        $fresh = $this->allowence->forAuto($budget, $this->grocery(100));

        $this->assertSame(395.0, $fresh['sub1']);
    }

    private function autoBudget(int $resetDate, float $amount = 10000, string $currency = 'MKD'): AutoBudget
    {
        return new AutoBudget([
            'budget_amount' => $amount,
            'currency' => $currency,
            'reset_date' => $resetDate,
        ]);
    }

    private function grocery(float $amount): Collection
    {
        return collect([
            (object) ['field_name' => 'Groceries', 'item_amount' => $amount],
        ]);
    }

    private function customItems(float $amount): Collection
    {
        return collect([
            (object) ['custom_budget_field_id' => 1, 'item_amount' => $amount],
            (object) ['custom_budget_field_id' => 2, 'item_amount' => 500],
        ]);
    }
}
