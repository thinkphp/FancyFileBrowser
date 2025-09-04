# 🗂️ Beautiful File Browser

A modern, responsive web-based file browser with a stunning gradient design and intuitive user interface. Browse your server files in style with both grid and list views, search functionality, and beautiful file previews.

![File Browser Preview](https://via.placeholder.com/800x400/667eea/ffffff?text=Beautiful+File+Browser)

## ✨ Features

- **Modern Design**: Beautiful gradient background with glassmorphic elements
- **Responsive Layout**: Works perfectly on desktop, tablet, and mobile devices  
- **Dual View Modes**: Switch between grid and list views
- **Smart Search**: Real-time file and folder filtering
- **File Previews**: Image thumbnails in grid view
- **File Statistics**: Live count of total items, folders, and files
- **File Type Icons**: Visual file type recognition with emoji icons
- **Security**: Configurable allowed file extensions
- **Easy Setup**: Simple PHP backend with no database required

## 🚀 Quick Start

### Prerequisites

- Web server with PHP support (Apache, Nginx, etc.)
- PHP 7.0 or higher

### Installation

1. **Download the files**
   ```bash
   git clone https://github.com/yourusername/beautiful-file-browser.git
   cd beautiful-file-browser
   ```

2. **Upload to your web server**
   - Copy `index.html` and `files.php` to your web server directory
   - Ensure your web server can execute PHP files

3. **Configure the upload directory**
   - Edit `files.php` and modify the `$UPLOAD_DIR` variable:
   ```php
   $UPLOAD_DIR = './uploads/'; // Change to your desired folder
   ```

4. **Set permissions**
   ```bash
   chmod 755 files.php
   chmod 755 your-upload-directory/
   ```

5. **Access your file browser**
   - Open your web browser and navigate to your server
   - Example: `http://yourdomain.com/file-browser/`

## ⚙️ Configuration

### Allowed File Extensions

Edit the `$ALLOWED_EXTENSIONS` array in `files.php` to control which file types are displayed:

```php
$ALLOWED_EXTENSIONS = [
    'jpg', 'jpeg', 'png', 'gif',        // Images
    'pdf', 'txt', 'doc', 'docx',        // Documents
    'zip', 'rar',                       // Archives
    'mp4', 'avi', 'mov',               // Videos
    'mp3', 'wav',                       // Audio
    'php', 'html', 'css', 'js'         // Code files
];
```

### Upload Directory

Change the directory that the file browser displays:

```php
$UPLOAD_DIR = './your-folder/';
```

### CORS Settings

The PHP script includes CORS headers for API access. Modify these in `files.php` if needed:

```php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');
```

## 🎨 Customization

### Styling

The file browser uses CSS custom properties for easy theming. Key variables you can modify in `index.html`:

```css
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --accent-color: #667eea;
    --text-primary: #333;
    --text-secondary: #666;
    --background-glass: rgba(255, 255, 255, 0.95);
}
```

### File Icons

Add or modify file type icons in the `getFileIcon()` function in `files.php`:

```php
$icons = [
    'jpg' => '🖼️', 'jpeg' => '🖼️', 'png' => '🖼️',
    'pdf' => '📄', 'txt' => '📝', 'doc' => '📄',
    'zip' => '🗜️', 'mp4' => '🎥', 'mp3' => '🎵',
    'your-extension' => '🆕'  // Add your custom icons
];
```

## 📱 Browser Support

- ✅ Chrome 60+
- ✅ Firefox 55+
- ✅ Safari 12+
- ✅ Edge 79+
- ✅ iOS Safari 12+
- ✅ Chrome for Android 60+

## 🔒 Security Considerations

1. **File Extension Filtering**: Only allowed extensions are displayed
2. **Directory Traversal Protection**: The script only reads from the configured directory
3. **No File Upload**: This is a read-only file browser
4. **HTTPS Recommended**: Use HTTPS in production for secure file access

⚠️ **Important**: This file browser allows direct access to files in your specified directory. Ensure you only point it to directories that are safe to expose.

## 🛠️ Troubleshooting

### Files not loading
- Check that `files.php` is accessible via web browser
- Verify PHP is enabled on your server
- Check file permissions on the upload directory

### Images not displaying
- Ensure image files are accessible via HTTP
- Check the `$UPLOAD_DIR` path is correct
- Verify web server can serve static files from the directory

### Search not working
- JavaScript must be enabled in the browser
- Check browser console for any JavaScript errors

### Permission errors
```bash
# Fix directory permissions
chmod 755 /path/to/your/upload/directory

# Fix file permissions  
chmod 644 /path/to/your/files/*
```

## 📄 API Response Format

The `files.php` endpoint returns JSON in this format:

```json
{
    "success": true,
    "items": [
        {
            "name": "example.jpg",
            "type": "file",
            "extension": "jpg", 
            "icon": "🖼️",
            "size": "2.5 MB",
            "modified": "Jan 15, 2024 14:30",
            "path": "./uploads/example.jpg",
            "isImage": true
        }
    ],
    "totalCount": 1,
    "path": "./uploads/"
}
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Design inspiration from modern file management interfaces
- Icons provided by Unicode emoji standard
- CSS backdrop-filter for glassmorphic effects

## 📞 Support

If you have questions or need help:

- 📧 Email: your-email@domain.com
- 🐛 Issues: [GitHub Issues](https://github.com/thinkphp/beautiful-file-browser/issues)
- 💬 Discussions: [GitHub Discussions](https://github.com/thinkphp/beautiful-file-browser/discussions)

---

Made with ❤️ by Adrian Statescu
# FancyFileBrowser
