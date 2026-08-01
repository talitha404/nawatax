<?php

namespace Tests;

use App\Http\Request\CalculateRequest;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CalculateRequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::post('/test-calculate', function (CalculateRequest $request) {
            return response()->json([
                'validated' => true,
                'pkp_agen' => $request->boolean('pkp_agen'),
                'pkp_shipowner' => $request->boolean('pkp_shipowner'),
                'pkp_shipper' => $request->boolean('pkp_shipper'),
                'subbroker_split_active' => $request->boolean('subbroker_split_active'),
            ]);
        })->middleware('web');
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'transaction_scheme' => 'undisclosed',
            'freight_owner' => '100000000',
            'freight_shipper' => '110000000',
            'reimbursable_costs' => '5000000',
            'shipowner_status' => 'siupal',
            'pkp_agen' => '1',
            'pkp_shipowner' => '1',
            'pkp_shipper' => '1',
            'subbroker_split_active' => null,
        ], $overrides);
    }

    public function test_valid_request_without_split(): void
    {
        $response = $this->postJson('/test-calculate', $this->validPayload());

        $response->assertOk();
        $response->assertJson(['validated' => true]);
        $response->assertSessionDoesntHaveErrors();
    }

    public function test_valid_request_with_percentage_split(): void
    {
        $response = $this->postJson('/test-calculate', $this->validPayload([
            'subbroker_split_active' => '1',
            'split_type' => 'percentage',
            'split_value' => '40',
            'sub_broker_entity' => 'corporate',
        ]));

        $response->assertOk();
        $response->assertSessionDoesntHaveErrors();
    }

    public function test_freight_owner_negative_should_fail(): void
    {
        $response = $this->postJson('/test-calculate', $this->validPayload([
            'freight_owner' => '-1000',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['freight_owner']);
    }

    public function test_freight_shipper_negative_should_fail(): void
    {
        $response = $this->postJson('/test-calculate', $this->validPayload([
            'freight_shipper' => '-1000',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['freight_shipper']);
    }

    public function test_split_value_over_100_should_fail(): void
    {
        $response = $this->postJson('/test-calculate', $this->validPayload([
            'subbroker_split_active' => '1',
            'split_type' => 'percentage',
            'split_value' => '101',
            'sub_broker_entity' => 'corporate',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['split_value']);
    }

    public function test_split_active_without_split_type_should_fail(): void
    {
        $response = $this->postJson('/test-calculate', $this->validPayload([
            'subbroker_split_active' => '1',
            'split_type' => null,
            'split_value' => '20',
            'sub_broker_entity' => 'corporate',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['split_type']);
    }

    public function test_split_active_without_split_value_should_fail(): void
    {
        $response = $this->postJson('/test-calculate', $this->validPayload([
            'subbroker_split_active' => '1',
            'split_type' => 'percentage',
            'split_value' => null,
            'sub_broker_entity' => 'corporate',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['split_value']);
    }

    public function test_invalid_shipowner_status_should_fail(): void
    {
        $response = $this->postJson('/test-calculate', $this->validPayload([
            'shipowner_status' => 'invalid',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['shipowner_status']);
    }

    public function test_inactive_split_does_not_require_split_fields(): void
    {
        $response = $this->postJson('/test-calculate', $this->validPayload([
            'subbroker_split_active' => null,
            'split_type' => null,
            'split_value' => null,
            'sub_broker_entity' => null,
        ]));

        $response->assertOk();
        $response->assertSessionDoesntHaveErrors();
    }

    public function test_checkbox_handling_normalizes_to_false_when_empty(): void
    {
        $response = $this->postJson('/test-calculate', $this->validPayload([
            'pkp_agen' => null,
            'pkp_shipowner' => null,
            'pkp_shipper' => null,
            'subbroker_split_active' => null,
        ]));

        $response->assertOk();
        $response->assertExactJson([
            'validated' => true,
            'pkp_agen' => false,
            'pkp_shipowner' => false,
            'pkp_shipper' => false,
            'subbroker_split_active' => false,
        ]);
    }
}
