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
        $this->artisan('passport:install');
    }

    public function test_can_get_customers_list()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $customers = Customer::factory()->count(3)->create();

        $response = $this->getJson('/api/customers');

        $response->assertStatus(200)
                ->assertJsonCount(3, 'data');
    }

    public function test_can_create_customer()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $customerData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'age' => 30,
            'dob' => '1993-01-01',
            'email' => 'john@example.com'
        ];

        $response = $this->postJson('/api/customers', $customerData);

        $response->assertStatus(201)
                ->assertJsonFragment($customerData);

        $this->assertDatabaseHas('customers', $customerData);
    }

    public function test_can_update_customer()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $customer = Customer::factory()->create();
        $updateData = [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'age' => 25,
            'dob' => '1998-01-01',
            'email' => 'jane@example.com'
        ];

        $response = $this->putJson("/api/customers/{$customer->id}", $updateData);

        $response->assertStatus(200)
                ->assertJsonFragment($updateData);

        $this->assertDatabaseHas('customers', $updateData);
    }

    public function test_can_delete_customer()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $customer = Customer::factory()->create();

        $response = $this->deleteJson("/api/customers/{$customer->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }

    public function test_validation_on_create_customer()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->postJson('/api/customers', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['first_name', 'last_name', 'age', 'dob', 'email']);
    }
}
