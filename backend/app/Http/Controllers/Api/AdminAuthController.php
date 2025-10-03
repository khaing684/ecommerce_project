<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    /**
     * Register a new admin (usually restricted in production)
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:admins',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $admin = Admin::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'admin',
            ]);

            $token = $admin->createToken('admin_token')->plainTextToken;

            return response()->json([
                'admin' => $admin,
                'token' => $token,
                'token_type' => 'Bearer',
            ], Response::HTTP_CREATED);

        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Registration failed',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Admin login with hashed password verification
     */
    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $admin = Admin::where('email', $validated['email'])->first();

            if (!$admin || !Hash::check($validated['password'], $admin->password)) {
                return response()->json([
                    'error' => 'Invalid admin credentials'
                ], Response::HTTP_UNAUTHORIZED);
            }

            $token = $admin->createToken('admin_token')->plainTextToken;

            return response()->json([
                'admin' => $admin,
                'token' => $token,
                'token_type' => 'Bearer',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Login failed',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Admin logout
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Admin successfully logged out'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Logout failed',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get authenticated admin
     */
    public function admin(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Get admin dashboard data
     */
    public function dashboard(Request $request)
    {
        try {
            $admin = $request->user();
            
            // Get dashboard statistics
            $stats = [
                'admin' => $admin,
                'modules' => [
                    'products' => [
                        'name' => 'Products',
                        'url' => '/products',
                        'icon' => 'box',
                    ],
                    'categories' => [
                        'name' => 'Categories',
                        'url' => '/categories',
                        'icon' => 'folder',
                    ],
                    'orders' => [
                        'name' => 'Orders',
                        'url' => '/orders',
                        'icon' => 'shopping-cart',
                    ],
                ],
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Dashboard data fetch failed',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}