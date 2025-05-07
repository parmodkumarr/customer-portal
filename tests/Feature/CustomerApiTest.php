<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CustomerApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a personal access client for testing
        // $this->artisan('passport:client', [
        //     '--personal' => true,
        //     '--name' => 'Test Client',
        //     '--no-interaction' => true,
        //     '--quiet' => true
        // ]);
    }

    protected function getTestUser()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);
        return $user;
    }

    public function test_can_get_customers_list()
    {
        $this->getTestUser();
        $customers = Customer::factory()->count(3)->create();

        $response = $this->getJson('/api/customers');

        $response->assertStatus(200)
                ->assertJsonCount(3, 'data');
    }

    public function test_can_create_customer()
    {
        $this->getTestUser();

        $customerData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'age' => "30",
            'dob' => '1993-01-01',
            'email' => 'john@example.com'
        ];

        $response = $this->postJson('/api/customers', $customerData);

        $response->assertStatus(201)
             ->assertJsonStructure([
                 'data' => [
                     'id',
                     'first_name',
                     'last_name',
                     'age',
                     'dob',
                     'email',
                     'created_at',
                     'updated_at'
                 ]
             ]);

        $this->assertDatabaseHas('customers',$customerData);
    }

    public function test_can_update_customer()
    {
        $this->getTestUser();
        $customer = Customer::factory()->create();

        $updateData = [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'age' => '25',
            'dob' => '1998-01-01',
            'email' => 'jane@example.com'
        ];

        $response = $this->putJson("/api/customers/{$customer->id}", $updateData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'first_name',
                        'last_name',
                        'age',
                        'dob',
                        'email',
                        'created_at',
                        'updated_at'
                    ]
                ]);

        $this->assertDatabaseHas('customers', $updateData);
    }

    public function test_can_delete_customer()
    {
        $this->getTestUser();
        $customer = Customer::factory()->create();

        $response = $this->deleteJson("/api/customers/{$customer->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }

    public function test_validation_on_create_customer()
    {
        $this->getTestUser();

        $response = $this->postJson('/api/customers', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['first_name', 'last_name', 'age', 'dob', 'email']);
    }
}
