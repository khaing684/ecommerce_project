<?php



use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

// Admin Authentication routes (only admins can access)
Route::post('/admin/register', [AdminAuthController::class, 'register']); // For creating admin accounts
Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/admin/logout', [AdminAuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/admin/user', [AdminAuthController::class, 'admin']);
Route::middleware('auth:sanctum')->get('/admin/dashboard', [AdminAuthController::class, 'dashboard']);

// Regular user authentication routes (disabled for admin-only system)
// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);
// Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
// Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'user']);

// Public test endpoint
Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});

// Database test endpoint
Route::get('/db-test', function () {
    try {
        $count = \App\Models\Category::count();
        return response()->json(['message' => 'Database connected!', 'categories_count' => $count]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// Simple categories test
Route::get('/categories-simple', function () {
    try {
        $categories = \App\Models\Category::all();
        return response()->json($categories);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// Direct public access (clean URLs without /public prefix)
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::post('/orders', [OrderController::class, 'store']);

// Public API endpoints with /public prefix (alternative access)
Route::prefix('public')->group(function () {
    // Public Categories - Read only
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    
    // Public Products - Read only
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    
    // Public Orders - Create only (for customer orders)
    Route::post('/orders', [OrderController::class, 'store']);
});

// Admin-only protected routes (all business operations require admin authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Categories (Admin Only) - excluding index and show which are public
    Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
    
    // Products (Admin Only) - excluding index and show which are public
    Route::apiResource('products', ProductController::class)->except(['index', 'show']);
    
    // Orders (Admin Only)
    Route::apiResource('orders', OrderController::class);
});


