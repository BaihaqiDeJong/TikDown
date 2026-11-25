# TikTok Downloader - PHP Native Version

Download video dan foto TikTok tanpa watermark dalam format MP4 atau MP3. Cepat, gratis, dan tanpa aplikasi.

## Fitur

✅ Download video TikTok (MP4 HD & Normal)  
✅ Konversi audio ke MP3 (192 kbps)  
✅ Download foto dari slide TikTok  
✅ Tanpa watermark  
✅ Preview video sebelum download  
✅ **PHP Native** - Tidak menggunakan framework

## Requirements

- PHP 7.4 atau lebih tinggi
- PHP Extensions: `curl`, `mbstring`, `json`
- FFmpeg (untuk konversi MP3)
- Apache/Nginx dengan mod_rewrite

## Instalasi

### 1. Clone atau Download Repository

```bash
git clone <repository-url>
cd opencode
```

### 2. Install FFmpeg

**Windows (Laragon):**
FFmpeg sudah tersedia di Laragon. Pastikan di PATH atau set manual di `.env`:
```
FFMPEG_PATH=C:\laragon\bin\ffmpeg\ffmpeg.exe
```

**Linux/Ubuntu:**
```bash
sudo apt update
sudo apt install ffmpeg
```

**macOS:**
```bash
brew install ffmpeg
```

### 3. Konfigurasi Environment

Salin file `.env.example` ke `.env` (sudah ada `.env`):
```bash
cp .env.example .env
```

Edit `.env` sesuai kebutuhan:
```env
TIKTOK_PROVIDER=tikwm
API_TIMEOUT_MS=15000
# RAPIDAPI_KEY=your_key_here (jika pakai RapidAPI)
```

### 4. Set Permissions (Linux/Mac)

```bash
chmod -R 755 php/
chmod 666 .env
```

### 5. Apache Configuration

Pastikan `mod_rewrite` aktif di Apache:

**Laragon (Windows):**
Sudah aktif secara default.

**Linux:**
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

File `.htaccess` sudah disediakan untuk routing.

### 6. Jalankan Aplikasi

**Dengan Laragon:**
1. Start Apache di Laragon
2. Buka browser: `http://localhost/opencode`

**Dengan Apache biasa:**
1. Pindahkan folder ke `/var/www/html/` atau document root
2. Buka browser sesuai konfigurasi virtual host

## Struktur Folder

```
opencode/
├── index.php                 # Entry point utama
├── .htaccess                 # Apache rewrite rules
├── .env                      # Konfigurasi environment
├── php/                      # Backend PHP
│   ├── router.php           # API router
│   ├── lib/
│   │   ├── errors.php       # Error handling
│   │   ├── validate.php     # URL validation
│   │   └── tokens.php       # Session management
│   └── services/
│       ├── tiktok.php       # TikTok API service
│       └── media.php        # Media streaming & download
├── public/                   # Frontend assets
│   ├── index.html           # UI utama
│   ├── app.js               # Frontend JavaScript
│   └── styles.css           # Tailwind CSS compiled
└── README.md
```

## API Endpoints

### POST /api/download
Memproses URL TikTok dan mengembalikan metadata video.

**Request:**
```json
{
  "url": "https://www.tiktok.com/@user/video/1234567890"
}
```

**Response:**
```json
{
  "id": "uuid",
  "kind": "video",
  "title": "Video title",
  "duration": 30,
  "thumbnail": "/api/image/{id}?kind=cover",
  "author": {...},
  "formats": {...},
  "preview": "/api/stream/{id}",
  "images": []
}
```

### GET /api/stream/:id
Stream video untuk preview.

### GET /api/file/:id
Download file (MP4, MP3, atau Image).

**Query parameters:**
- `format`: `mp4`, `mp3`, atau `image`
- `quality`: `hd` atau `sd` (untuk MP4)
- `index`: nomor foto (untuk image)

### GET /api/image/:id
Proxy gambar (thumbnail, avatar, slide).

**Query parameters:**
- `kind`: `cover`, `avatar`, atau `slide`
- `index`: nomor slide (untuk kind=slide)

## Troubleshooting

### 1. 404 Not Found pada /api/*
- Pastikan `mod_rewrite` aktif di Apache
- Cek file `.htaccess` ada di root folder
- Cek konfigurasi `AllowOverride All` di Apache config

### 2. FFmpeg Error saat Download MP3
- Pastikan FFmpeg terinstall: `ffmpeg -version`
- Set path manual di `.env` jika perlu
- Cek log error di PHP error log

### 3. Timeout saat Fetch Video
- Tingkatkan `API_TIMEOUT_MS` di `.env`
- Tingkatkan `max_execution_time` di `php.ini`

### 4. Session Error
- Pastikan folder `/tmp` writable (Linux)
- Pastikan `session.save_path` configured di `php.ini`

## Perbedaan dengan Versi Node.js

| Fitur | Node.js | PHP Native |
|-------|---------|------------|
| Framework | Express | Tidak ada |
| Cache | In-memory Map | PHP Session |
| Async | Async/Await | Synchronous |
| Streaming | Stream API | cURL + readfile |
| Dependencies | npm packages | PHP built-in |
| Performance | Lebih cepat | Cukup untuk traffic normal |

## Lisensi

MIT License

## Credits

- UI Design: Tailwind CSS
- TikTok API: TikWM.com / RapidAPI
- Audio Processing: FFmpeg
