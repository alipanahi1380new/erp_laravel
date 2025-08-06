<?php

namespace Tests\Feature;

use App\Models\ProductUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductUnitApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate', ['--env' => 'testing']);
    }

    public function test_can_create_product_unit()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/product-units', [
            'title' => 'Test Store',
            'unit_type' => 'barcode',
            'description' => 'Test description',
            'can_have_float_value' => true,
        ]);

        $response->assertStatus(201)
                ->assertJsonFragment(['title' => 'Test Store']);

        $this->assertDatabaseHas('product_units', [
            'title' => 'Test Store',
            'user_id_maker' => $user->id,
        ]);
    }

    public function test_can_retrieve_product_units()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        ProductUnit::factory()->create([
            'title' => 'Test Store',
            'unit_type' => 'barcode',
            'user_id_maker' => $user->id,
        ]);

        $response = $this->getJson('/api/product-units');

        $response->assertStatus(200)
                ->assertJsonCount(1);
    }

    public function test_can_show_product_unit()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $productUnit = ProductUnit::factory()->create([
            'user_id_maker' => $user->id,
        ]);

        $response = $this->getJson("/api/product-units/{$productUnit->id}");

        $response->assertStatus(200)
                ->assertJsonFragment(['title' => $productUnit->title]);
    }

    public function test_can_update_product_unit()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $productUnit = ProductUnit::factory()->create([
            'user_id_maker' => $user->id,
        ]);

        $response = $this->putJson("/api/product-units/{$productUnit->id}", [
            'title' => 'Updated Store',
            'unit_type' => 'not_barcode',
            'description' => 'Updated description',
            'can_have_float_value' => false,
        ]);

        $response->assertStatus(200)
                ->assertJsonFragment(['title' => 'Updated Store']);

        $this->assertDatabaseHas('product_units', [
            'id' => $productUnit->id,
            'title' => 'Updated Store',
        ]);
    }

    public function test_can_delete_product_unit()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $productUnit = ProductUnit::factory()->create([
            'user_id_maker' => $user->id,
        ]);

        $response = $this->deleteJson("/api/product-units/{$productUnit->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('product_units', [
            'id' => $productUnit->id,
        ]);
    }

    public function test_unauthorized_access_to_other_user_product_unit()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $productUnit = ProductUnit::factory()->create([
            'user_id_maker' => $user1->id,
        ]);

        Sanctum::actingAs($user2);

        $response = $this->getJson("/api/product-units/{$productUnit->id}");

        $response->assertStatus(403)
                ->assertJson(['error' => 'Unauthorized']);
    }

    public function test_unauthenticated_access_fails()
    {
        $response = $this->postJson('/api/product-units', [
            'title' => 'Test Store',
            'unit_type' => 'barcode',
            'description' => 'Test description',
            'can_have_float_value' => true,
        ]);

        $response->assertStatus(401);
    }
}