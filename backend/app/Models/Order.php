<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'total_amount',
        'status',
        'shipping_address',
        'payment_method',
        'order_date'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'order_date' => 'datetime'
    ];

    /**
     * Get the admin that created the order.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Legacy method for backward compatibility (if needed)
     * @deprecated Use admin() instead
     */
    public function user()
    {
        return $this->admin();
    }

    /**
     * Get the order items for the order.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the products for the order through order items.
     */
    public function products()
    {
        return $this->hasManyThrough(Product::class, OrderItem::class, 'order_id', 'id', 'id', 'product_id');
    }
}
