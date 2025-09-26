<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    use RefreshDatabase;

    private $token;

    protected function setUp(): void
    {
        $user = User::factory()->create();
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password' // Assuming the factory sets password to 'password'
        ]);
        $this->token = $response->json('data.token');
    }

    public function test_supplier_index():void
    {
        // Seed the database with suppliers
        Supplier::factory()->count(5)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
                         ->getJson('/api/suppliers');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'address', 'phone', 'email', 'created_at', 'updated_at']
            ],
            'message',
            'success'
        ]);
        $this->assertCount(5, $response->json('data')); 
    }

    public function test_supplier_show():void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
                        ->getJson('/api/suppliers/'.$supplier->id);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => ['id', 'name', 'address', 'phone', 'email', 'created_at', 'updated_at'],
            'message',
            'success'
        ]);
        $this->assertEquals($supplier->name, $response->json('data.name'));
    }

    public function test_supplier_show_not_found():void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
                        ->getJson('/api/suppliers/99999');

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'Supplier not found',
            'success' => false
        ]);
    }

    public function test_supplier_store():void
    {
        $supplierData = [
            'name' => 'New Supplier',
            'address' => '123 Supplier St',
            'phone' => '123-456-7890',
            'email' => 'company@supplier.com'
        ];
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
                        ->postJson('/api/suppliers', $supplierData);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => ['id', 'name', 'address', 'phone', 'email', 'created_at', 'updated_at'],
            'message',
            'success'
        ]);
        $this->assertEquals('New Supplier', $response->json('data.name'));
        $this->assertDatabaseHas('suppliers', ['email' => 'company@supplier.com']);
    }

    public function test_supplier_store_validation_error():void
    {
        $supplierData = [
            'name' => '',
            'address' => '123 Supplier St',
            'phone' => 'invalid-phone',
            'email' => 'not-an-email'
        ];
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
                        ->postJson('/api/suppliers', $supplierData);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => ['name', 'phone', 'email']
        ]);
    }

    public function test_supplier_update():void
    {
        $supplier = Supplier::factory()->create();

        $updateData = [
            'name' => 'Updated Supplier',
            'address' => '456 Updated St',
            'phone' => '987-654-3210',
            'email' => 'upSupplier@supplier.com'
        ];
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
                        ->putJson('/api/suppliers/'.$supplier->id, $updateData);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => ['id', 'name', 'address', 'phone', 'email', 'created_at', 'updated_at'],
            'message',
            'success'
        ]);
        $this->assertEquals('Updated Supplier', $response->json('data.name'));
        $this->assertDatabaseHas('suppliers', ['email' => 'upSupplier@supplier.com']);  
    }

    public function test_supplier_update_not_found():void
    {
        $updateData = [
            'name' => 'Updated Supplier',
            'address' => '456 Updated St',
            'phone' => '987-654-3210',
            'email' => 'upSupplier@supplier.com'
        ];
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
                        ->putJson('/api/suppliers/99999', $updateData);
        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'Supplier not found',
            'success' => false
        ]);

    }

}
