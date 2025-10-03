<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Get the products for the category.
     * When a category is deleted, all associated products will be deleted as well (cascade delete).
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}