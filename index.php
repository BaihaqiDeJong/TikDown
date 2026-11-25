<?php
/**
 * TikTok Downloader - PHP Native Version
 * Entry point untuk semua requests
 */

// Error reporting untuk development (matikan di production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
session_start();

// Load environment variables
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

// Autoload classes
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/php/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Set security headers
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');
header_remove('X-Powered-By');

// Get request URI and method
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Router
try {
    // API routes
    if (strpos($requestUri, '/api/') === 0) {
        require_once __DIR__ . '/php/router.php';
        exit;
    }
    
    // Serve static files from public directory
    $publicPath = __DIR__ . '/public' . $requestUri;
    
    // If requesting root, serve index.html
    if ($requestUri === '/') {
        $publicPath = __DIR__ . '/public/index.html';
    }
    
    // Check if file exists
    if (file_exists($publicPath) && is_file($publicPath)) {
        $ext = pathinfo($publicPath, PATHINFO_EXTENSION);
        $mimeTypes = [
            'html' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'webp' => 'image/webp',
        ];
        
        $mimeType = $mimeTypes[$ext] ?? 'application/octet-stream';
        header('Content-Type: ' . $mimeType);
        
        // Cache control untuk static assets
        if (in_array($ext, ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'webp'])) {
            header('Cache-Control: public, max-age=3600');
        }
        
        readfile($publicPath);
        exit;
    }
    
    // 404 for other routes
    http_response_code(404);
    echo '404 - Not Found';
    
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Terjadi kesalahan pada server.']);
    error_log('[error] ' . $e->getMessage());
}
