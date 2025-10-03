<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CategoryCascadeDeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_delete_category_and_all_associated_products_are_deleted()
    {
        // Create an admin user
        $admin = Admin::factory()->create();
        
        // Create a category
        $category = Category::factory()->create();
        
        // Create products in this category
        $products = Product::factory()->count(3)->create([
            'category_id' => $category->id,
        ]);
        
        // Authenticate as admin
        Sanctum::actingAs($admin, ['*']);
        
        // Verify products exist before deletion
        $this->assertEquals(3, $category->products()->count());
        
        // Attempt to delete the category
        $response = $this->deleteJson("/api/categories/{$category->id}");
        
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Category deleted successfully. All associated products have also been deleted.'
        ]);
        
        // Category should be deleted
        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
        
        // All associated products should also be deleted
        $this->assertEquals(0, Product::where('category_id', $category->id)->count());
    }
}