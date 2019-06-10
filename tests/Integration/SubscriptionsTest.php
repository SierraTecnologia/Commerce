<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use SierraTecnologia\Commerce\Services\SierraTecnologiaService;

class SubscriptionsTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();

        $this->user = factory(App\Models\User::class)->create([
            'id' => 1,
        ]);
        $this->role = factory(App\Models\Role::class)->create([
            'name' => 'admin',
        ]);

        $this->user->roles()->attach($this->role);
        $this->actingAs($this->user);

        factory(\SierraTecnologia\Commerce\Models\Cart::class)->create();
        factory(\SierraTecnologia\Commerce\Models\Product::class)->create();
        factory(\SierraTecnologia\Commerce\Models\Plan::class)->create(['id' => 1]);
        factory(\SierraTecnologia\Commerce\Models\Plan::class)->create(['id' => 2]);
        factory(\SierraTecnologia\Commerce\Models\Plan::class)->create(['id' => 3]);
        factory(\SierraTecnologia\Commerce\Models\Plan::class)->create(['id' => 4]);

        $sitecpayment = Mockery::mock(\SierraTecnologia\SierraTecnologia::class);
        $plan = Mockery::mock(\SierraTecnologia\Plan::class);
        $refund = Mockery::mock(\SierraTecnologia\Refund::class);
        $coupon = Mockery::mock(\SierraTecnologia\Coupon::class);

        $planObject = Mockery::mock('StdClass');
        $planObject->shouldReceive('delete')->andReturn(true);

        $sitecpayment->shouldReceive('setApiKey')->andReturn(true);
        $plan->shouldReceive('all')->andReturn((object) ['data' => []]);
        $plan->shouldReceive('create')->andReturn(true);
        $plan->shouldReceive('retrieve')->andReturn($planObject);
        $plan->shouldReceive('create')->andReturn(true);

        $refund->shouldReceive('create')->with(['charge' => 999])->andReturn(true);

        app()->bind(SierraTecnologiaService::class, function ($app) use ($sitecpayment, $plan, $coupon, $refund) {
            return new SierraTecnologiaService($sitecpayment, $plan, $coupon, $refund);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Views
    |--------------------------------------------------------------------------
    */

    public function testIndex()
    {
        $response = $this->call('GET', 'cms/plans');

        $this->assertEquals(200, $response->getStatusCode());
        $response->assertViewHas('plans');
        $response->assertSee('Subscription Plans');
    }

    public function testCreate()
    {
        $response = $this->call('GET', 'cms/plans/create');

        $this->assertEquals(200, $response->getStatusCode());
        $response->assertSee('Name');
    }

    public function testEdit()
    {
        $response = $this->call('GET', 'cms/plans/2/edit');

        $this->assertEquals(200, $response->getStatusCode());
        $response->assertViewHas('plan');
        $response->assertSee('#');
    }

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    */

    public function testSearch()
    {
        $response = $this->call('POST', 'cms/plans/search', ['term' => 'wtf']);

        $response->assertViewHas('plans');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testUpdate()
    {
        $response = $this->call('PATCH', 'cms/plans/4', [
            'name' => 'Batman Plan',
        ]);

        $this->assertDatabaseHas('plans', ['name' => 'Batman Plan']);
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testDelete()
    {
        $response = $this->call('DELETE', 'cms/plans/1');
        $this->assertEquals(302, $response->getStatusCode());
    }
}
