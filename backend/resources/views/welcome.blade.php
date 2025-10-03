<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }} API</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 0; padding: 40px; background: #f8f9fa; color: #333; }
            .container { max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            h1 { color: #333; margin-bottom: 20px; }
            h3 { color: #555; margin-top: 30px; }
            p { color: #666; line-height: 1.6; margin-bottom: 15px; }
            .status { padding: 15px; background: #d4edda; color: #155724; border-radius: 4px; margin: 20px 0; border: 1px solid #c3e6cb; }
            .api-section { background: #e3f2fd; padding: 20px; border-radius: 4px; margin: 20px 0; border: 1px solid #bbdefb; }
            .endpoint { font-family: monospace; background: #f8f9fa; padding: 8px 12px; border-radius: 4px; margin: 5px 0; display: block; }
            .public { border-left: 4px solid #28a745; }
            .admin { border-left: 4px solid #dc3545; }
            .method { font-weight: bold; color: #007bff; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>E-commerce API Backend</h1>
            <div class="status">
                ✅ Laravel API Server is Running Successfully
            </div>
            <p>This is a <strong>pure API backend</strong> - no login forms or web interface. Access all functionality through API endpoints.</p>
            
            <div class="api-section">
                <h3>🌐 Public API Endpoints (No Authentication)</h3>
                <p><strong>Base URL:</strong> http://127.0.0.1:8000/api</p>
                
                <div class="endpoint public"><span class="method">GET</span> /api/test</div>
                <div class="endpoint public"><span class="method">GET</span> /api/public/categories</div>
                <div class="endpoint public"><span class="method">GET</span> /api/public/categories/{id}</div>
                <div class="endpoint public"><span class="method">GET</span> /api/public/products</div>
                <div class="endpoint public"><span class="method">GET</span> /api/public/products/{id}</div>
                <div class="endpoint public"><span class="method">POST</span> /api/public/orders</div>
            </div>
            
            <div class="api-section">
                <h3>🔒 Admin API Endpoints (Requires Bearer Token)</h3>
                
                <h4>Authentication:</h4>
                <div class="endpoint admin"><span class="method">POST</span> /api/admin/login</div>
                <div class="endpoint admin"><span class="method">POST</span> /api/admin/register</div>
                <div class="endpoint admin"><span class="method">POST</span> /api/admin/logout</div>
                <div class="endpoint admin"><span class="method">GET</span> /api/admin/user</div>
                <div class="endpoint admin"><span class="method">GET</span> /api/admin/dashboard</div>
                
                <h4>Full CRUD Operations:</h4>
                <div class="endpoint admin"><span class="method">GET|POST|PUT|DELETE</span> /api/categories</div>
                <div class="endpoint admin"><span class="method">GET|POST|PUT|DELETE</span> /api/products</div>
                <div class="endpoint admin"><span class="method">GET|POST|PUT|DELETE</span> /api/orders</div>
            </div>
            
            <div class="api-section">
                <h3>📖 Usage Examples</h3>
                <p><strong>Get Categories:</strong></p>
                <div class="endpoint">GET http://127.0.0.1:8000/api/public/categories</div>
                
                <p><strong>Get Products:</strong></p>
                <div class="endpoint">GET http://127.0.0.1:8000/api/public/products</div>
                
                <p><strong>Create Order:</strong></p>
                <div class="endpoint">POST http://127.0.0.1:8000/api/public/orders</div>
            </div>
            
            <p><strong>💡 Note:</strong> No web forms - everything is API-based!</p>
        </div>
    </body>
</html>