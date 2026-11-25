<?php
/**
 * System Test & Configuration Check
 * Akses file ini di browser untuk memeriksa konfigurasi sistem
 * HAPUS FILE INI SETELAH TESTING!
 */

header('Content-Type: text/html; charset=utf-8');

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Test - TikTok Downloader</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; }
        .card { background: white; border-radius: 12px; padding: 24px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h1 { font-size: 24px; margin-bottom: 8px; color: #1a1a1a; }
        h2 { font-size: 18px; margin-bottom: 16px; color: #333; border-bottom: 2px solid #f97316; padding-bottom: 8px; }
        .subtitle { color: #666; margin-bottom: 24px; }
        .status { display: inline-block; padding: 4px 12px; border-radius: 6px; font-size: 14px; font-weight: 600; }
        .status.ok { background: #dcfce7; color: #166534; }
        .status.error { background: #fee2e2; color: #991b1b; }
        .status.warning { background: #fef3c7; color: #92400e; }
        table { width: 100%; border-collapse: collapse; }
        td, th { padding: 10px; text-align: left; border-bottom: 1px solid #e5e5e5; }
        th { font-weight: 600; color: #666; width: 200px; }
        .warning-box { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 16px; border-radius: 8px; margin: 16px 0; }
        code { background: #f5f5f5; padding: 2px 6px; border-radius: 4px; font-family: monospace; }
        .btn { display: inline-block; padding: 10px 20px; background: #f97316; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; margin-top: 16px; }
        .btn:hover { background: #ea580c; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>🔧 System Configuration Test</h1>
            <p class="subtitle">TikTok Downloader - PHP Native Version</p>
            
            <?php
            $allOk = true;
            
            // PHP Version Check
            $phpVersion = phpversion();
            $phpOk = version_compare($phpVersion, '7.4.0', '>=');
            if (!$phpOk) $allOk = false;
            ?>
            
            <table>
                <tr>
                    <th>PHP Version</th>
                    <td>
                        <?php echo $phpVersion; ?>
                        <span class="status <?php echo $phpOk ? 'ok' : 'error'; ?>">
                            <?php echo $phpOk ? '✓ OK' : '✗ Minimal PHP 7.4'; ?>
                        </span>
                    </td>
                </tr>
                
                <?php
                // Required Extensions
                $extensions = ['curl', 'mbstring', 'json', 'session'];
                foreach ($extensions as $ext) {
                    $loaded = extension_loaded($ext);
                    if (!$loaded) $allOk = false;
                    echo '<tr>';
                    echo '<th>Extension: ' . $ext . '</th>';
                    echo '<td><span class="status ' . ($loaded ? 'ok' : 'error') . '">';
                    echo $loaded ? '✓ Loaded' : '✗ Not Loaded';
                    echo '</span></td>';
                    echo '</tr>';
                }
                
                // FFmpeg Check
                $ffmpegPath = $_ENV['FFMPEG_PATH'] ?? 'ffmpeg';
                exec($ffmpegPath . ' -version 2>&1', $ffmpegOutput, $ffmpegReturn);
                $ffmpegOk = $ffmpegReturn === 0;
                if (!$ffmpegOk) $allOk = false;
                ?>
                
                <tr>
                    <th>FFmpeg</th>
                    <td>
                        <?php if ($ffmpegOk): ?>
                            <span class="status ok">✓ Installed</span>
                            <br><small style="color: #666;"><?php echo htmlspecialchars($ffmpegOutput[0] ?? ''); ?></small>
                        <?php else: ?>
                            <span class="status error">✗ Not Found</span>
                            <br><small style="color: #991b1b;">MP3 conversion will not work</small>
                        <?php endif; ?>
                    </td>
                </tr>
                
                <?php
                // Session Check
                $sessionOk = session_status() === PHP_SESSION_ACTIVE;
                ?>
                
                <tr>
                    <th>Session</th>
                    <td>
                        <span class="status <?php echo $sessionOk ? 'ok' : 'error'; ?>">
                            <?php echo $sessionOk ? '✓ Active' : '✗ Not Active'; ?>
                        </span>
                    </td>
                </tr>
                
                <?php
                // Temp Directory
                $tmpDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tiktok-downloader';
                $tmpWritable = is_writable(sys_get_temp_dir());
                ?>
                
                <tr>
                    <th>Temp Directory</th>
                    <td>
                        <span class="status <?php echo $tmpWritable ? 'ok' : 'error'; ?>">
                            <?php echo $tmpWritable ? '✓ Writable' : '✗ Not Writable'; ?>
                        </span>
                        <br><small style="color: #666;"><code><?php echo htmlspecialchars($tmpDir); ?></code></small>
                    </td>
                </tr>
                
                <?php
                // .env file
                $envExists = file_exists(__DIR__ . '/.env');
                ?>
                
                <tr>
                    <th>.env File</th>
                    <td>
                        <span class="status <?php echo $envExists ? 'ok' : 'warning'; ?>">
                            <?php echo $envExists ? '✓ Found' : '⚠ Not Found'; ?>
                        </span>
                    </td>
                </tr>
                
                <?php
                // mod_rewrite check (Apache only)
                $modRewrite = false;
                if (function_exists('apache_get_modules')) {
                    $modRewrite = in_array('mod_rewrite', apache_get_modules());
                }
                ?>
                
                <tr>
                    <th>URL Rewrite</th>
                    <td>
                        <?php if (function_exists('apache_get_modules')): ?>
                            <span class="status <?php echo $modRewrite ? 'ok' : 'error'; ?>">
                                <?php echo $modRewrite ? '✓ mod_rewrite enabled' : '✗ mod_rewrite not enabled'; ?>
                            </span>
                        <?php else: ?>
                            <span class="status warning">⚠ Cannot detect (Nginx or non-Apache)</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="card">
            <h2>📋 Environment Variables</h2>
            <?php
            if ($envExists) {
                echo '<table>';
                echo '<tr><th>Variable</th><th>Value</th></tr>';
                
                $envVars = ['TIKTOK_PROVIDER', 'API_TIMEOUT_MS', 'RAPIDAPI_KEY', 'FFMPEG_PATH'];
                foreach ($envVars as $var) {
                    $value = $_ENV[$var] ?? 'Not set';
                    if ($var === 'RAPIDAPI_KEY' && $value !== 'Not set') {
                        $value = substr($value, 0, 8) . '...'; // Hide key
                    }
                    echo '<tr>';
                    echo '<th>' . htmlspecialchars($var) . '</th>';
                    echo '<td><code>' . htmlspecialchars($value) . '</code></td>';
                    echo '</tr>';
                }
                
                echo '</table>';
            } else {
                echo '<div class="warning-box">';
                echo '<strong>⚠ File .env tidak ditemukan!</strong><br>';
                echo 'Salin <code>.env.example</code> ke <code>.env</code> dan sesuaikan konfigurasi.';
                echo '</div>';
            }
            ?>
        </div>
        
        <div class="card">
            <h2>🧪 Test API Connection</h2>
            <p style="margin-bottom: 16px;">Test koneksi ke TikTok provider (TikWM):</p>
            
            <?php
            $testUrl = 'https://www.tikwm.com/api/';
            $testOk = false;
            $testMessage = '';
            
            $ch = curl_init($testUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query(['url' => 'https://www.tiktok.com']),
                CURLOPT_TIMEOUT => 10,
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200) {
                $testOk = true;
                $testMessage = 'Koneksi ke TikWM API berhasil!';
            } else {
                $testMessage = 'Gagal terhubung ke TikWM API (HTTP ' . $httpCode . ')';
            }
            ?>
            
            <div style="padding: 16px; background: <?php echo $testOk ? '#dcfce7' : '#fee2e2'; ?>; border-radius: 8px;">
                <strong style="color: <?php echo $testOk ? '#166534' : '#991b1b'; ?>;">
                    <?php echo htmlspecialchars($testMessage); ?>
                </strong>
            </div>
        </div>
        
        <?php if ($allOk): ?>
        <div class="card" style="background: #dcfce7; border: 2px solid #22c55e;">
            <h2 style="color: #166534; border-color: #22c55e;">✅ System Ready!</h2>
            <p style="color: #166534;">Semua konfigurasi sudah benar. Aplikasi siap digunakan.</p>
            <a href="/" class="btn">Buka Aplikasi</a>
        </div>
        <?php else: ?>
        <div class="card" style="background: #fee2e2; border: 2px solid #ef4444;">
            <h2 style="color: #991b1b; border-color: #ef4444;">❌ Configuration Issues</h2>
            <p style="color: #991b1b;">Terdapat masalah konfigurasi. Perbaiki error di atas sebelum menggunakan aplikasi.</p>
        </div>
        <?php endif; ?>
        
        <div class="warning-box">
            <strong>⚠️ PENTING: HAPUS FILE INI SETELAH TESTING!</strong><br>
            File <code>test-config.php</code> menampilkan informasi sensitif sistem. Hapus setelah selesai testing.
        </div>
    </div>
</body>
</html>
