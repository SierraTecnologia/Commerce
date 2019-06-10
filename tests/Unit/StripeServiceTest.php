<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use SierraTecnologia\Commerce\Services\SierraTecnologiaService;

class SierraTecnologiaServiceTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();

        $sitecpayment = Mockery::mock(\SierraTecnologia\SierraTecnologia::class);
        $plan = Mockery::mock(\SierraTecnologia\Plan::class);
        $refund = Mockery::mock(\SierraTecnologia\Refund::class);
        $coupon = Mockery::mock(\SierraTecnologia\Coupon::class);
        $this->transaction = factory(\SierraTecnologia\Commerce\Models\Transaction::class)->create();

        $planObject = Mockery::mock('StdClass');
        $planObject->shouldReceive('delete')->andReturn(true);

        $sitecpayment->shouldReceive('setApiKey')->andReturn(true);
        $plan->shouldReceive('all')->andReturn([]);
        $plan->shouldReceive('create')->andReturn(true);
        $plan->shouldReceive('retrieve')->andReturn($planObject);
        $plan->shouldReceive('create')->andReturn(true);

        $refund->shouldReceive('create')->with([
            'charge' => $this->transaction->provider_id,
            'amount' => $this->transaction->amount
        ])->andReturn(true);

        $this->service = new SierraTecnologiaService($sitecpayment, $plan, $coupon, $refund);
    }

    public function testGetSierraTecnologiaPlans()
    {
        $response = $this->service->collectSierraTecnologiaPlans();
        $this->assertEquals($response, []);
    }

    public function testCreatePlan()
    {
        $response = $this->service->createPlan([
            'amount' => 99,
            'interval' => 'month',
            'name' => 'Wayne Gretzky',
            'currency' => 'cad',
            'descriptor' => 'Hockey',
            'trial_days' => 99,
            'sitecpayment_id' => 'wayne-gretzky',
        ]);

        $this->assertEquals($response, true);
    }

    public function testDeletePlan()
    {
        $response = $this->service->deletePlan('wayne-gretzky');
        $this->assertEquals($response, true);
    }

    public function testRefund()
    {
        $response = $this->service->refund($this->transaction->provider_id, $this->transaction->amount);
        $this->assertEquals($response, true);
    }
}
