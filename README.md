# Link Poster for Telegram

A Chrome extension that posts links to Telegram groups via context menu.

![Chrome Extension](https://img.shields.io/badge/Chrome-Extension-4285F4?logo=googlechrome&logoColor=white)
![Manifest V3](https://img.shields.io/badge/Manifest-V3-green)
![License](https://img.shields.io/badge/License-MIT-blue)

## Features

- 🔗 **Right-click on any link** → "Post this link to Telegram"
- 📄 **Right-click on page** → "Post page URL to Telegram"
- 📋 **Group selector** dropdown with multiple Telegram groups
- ✅ **Success/Error feedback** with visual indicators
- 📝 **Request logging** to `postlink.log`

## Installation

1. Download or clone this repository
2. Open `chrome://extensions/` in Chrome
3. Enable **Developer mode** (toggle in top-right)
4. Click **Load unpacked**
5. Select the `linkposter` folder

## Configuration

### Backend Setup

1. Copy `postlink_dummy.php` to `postlink.php`
2. Edit `postlink.php` and configure:

```php
// Your Telegram Bot Token (get from @BotFather)
$BOT_TOKEN = 'YOUR_BOT_TOKEN_HERE';

// Array of Telegram Chat IDs
$GROUPS = [
    '-1001234567890',  // Index 0: Group1
    '-1001234567891',  // Index 1: Group2
    // Add more groups...
];
```

3. Host `postlink.php` on a PHP-enabled web server
4. Update `BACKEND_URL` in `popup.js` to point to your server

### Getting Telegram Credentials

1. **Bot Token**: Message [@BotFather](https://t.me/BotFather) on Telegram → `/newbot`
2. **Chat IDs**: 
   - Add your bot to a group
   - Send a message in the group
   - Visit `https://api.telegram.org/bot<YOUR_TOKEN>/getUpdates`
   - Find the `chat.id` value (negative number for groups)

## Project Structure

```
linkposter/
├── manifest.json       # Chrome extension manifest (v3)
├── background.js       # Context menu handler
├── popup.html          # Popup UI
├── popup.css           # Styling
├── popup.js            # Frontend logic
├── help.html           # Help page
├── postlink_dummy.php  # Backend template
└── icons/              # Extension icons (16, 48, 128px)
```

## Usage

1. Browse to any webpage
2. Right-click on a link (or anywhere on the page)
3. Select "Post this link to Telegram" or "Post page URL to Telegram"
4. Choose a group from the dropdown
5. Click **Post**

## Customization

### Adding More Groups

1. Add group names to the `<select>` in `popup.html`
2. Add corresponding Chat IDs to `$GROUPS` array in `postlink.php`
3. Add group names to `$GROUP_NAMES` in the logging function

## License

MIT License - feel free to use and modify.

## Author

[Gyanmarg Portal Services](https://gyanmarg.guru)

---

**Open Source Project**: [Gyanmarg @ Github](https://github.com/GyanmargGuru)
