<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_delete_category_without_products()
    {
        // Create an admin user
        $admin = Admin::factory()->create();
        
        // Create a category
        $category = Category::factory()->create();
        
        // Authenticate as admin
        Sanctum::actingAs($admin, ['*']);
        
        // Attempt to delete the category
        $response = $this->deleteJson("/api/categories/{$category->id}");
        
        $response->assertStatus(200);
        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }
    
    /** @test */
    public function admin_cannot_delete_category_with_products()
    {
        // Create an admin user
        $admin = Admin::factory()->create();
        
        // Create a category
        $category = Category::factory()->create();
        
        // Create a product in this category
        $product = Product::factory()->create([
            'category_id' => $category->id,
        ]);
        
        // Authenticate as admin
        Sanctum::actingAs($admin, ['*']);
        
        // Attempt to delete the category
        $response = $this->deleteJson("/api/categories/{$category->id}");
        
        $response->assertStatus(409);
        $response->assertJson([
            'error' => 'Cannot delete category that has associated products'
        ]);
        
        // Category should still exist
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);
    }
}