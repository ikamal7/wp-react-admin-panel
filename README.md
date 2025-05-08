# WP React Admin Panel

A React-based admin panel for WordPress customization settings. This plugin provides a modern, user-friendly interface for managing various WordPress customization options using React components.

## Features

- **Modern React-based UI**: Built with React and WordPress components for a seamless admin experience
- **Tabbed Interface**: Organized settings in easy-to-navigate tabs
- **Multiple Setting Types**: Supports text fields, color pickers, select dropdowns, and textareas
- **Real-time Feedback**: Provides immediate feedback on save operations
- **Customization Options**:
  - General settings (site title, admin email)
  - Appearance settings (admin color, menu position)
  - Advanced settings (custom CSS, custom JavaScript)

## Installation

### Manual Installation

1. Download the plugin zip file
2. Log in to your WordPress admin panel
3. Navigate to Plugins → Add New
4. Click the "Upload Plugin" button
5. Upload the zip file and click "Install Now"
6. Activate the plugin through the "Plugins" menu


## Usage

1. After activation, a new menu item "React Admin" will appear in your WordPress admin sidebar
2. Click on "React Admin" to access the settings panel
3. Navigate through the tabs to configure different aspects of your WordPress site:
   - **General**: Configure basic site information
   - **Appearance**: Customize the look and feel of your admin panel
   - **Advanced**: Add custom CSS and JavaScript
4. Click "Save Settings" to apply your changes

## Development Setup

### Prerequisites

- [Node.js](https://nodejs.org/) (v14 or later recommended)
- [npm](https://www.npmjs.com/) (v6 or later)
- [WordPress](https://wordpress.org/) development environment

### Getting Started

1. Clone this repository into your WordPress plugins directory:

```bash
cd wp-content/plugins/
git clone https://github.com/ikamal7/wp-react-admin-panel.git
cd wp-react-admin-panel
```

2. Install dependencies:

```bash
npm install
```

3. Start the development server:

```bash
npm run start
```

This will compile your JavaScript files and watch for changes.

4. Build for production:

```bash
npm run build
```

### Project Structure

```
wp-react-admin-panel/
├── build/                  # Compiled files (generated)
├── src/                    # Source files
│   ├── index.js            # Main React application
│   └── index.css           # Styles
├── wp-react-admin-panel.php # Main plugin file
├── package.json            # npm dependencies and scripts
└── README.md               # This file
```

### Available Scripts

- `npm run start`: Start the development server with hot reloading
- `npm run build`: Build the project for production
- `npm run format`: Format code using WordPress coding standards
- `npm run lint:js`: Lint JavaScript files

## Extending the Plugin

The plugin is built with extensibility in mind. You can add new tabs or settings by modifying the React components in `src/index.js`.

Example of adding a new tab:

```javascript
// Add this to the tabs array in src/index.js
{
    name: 'new-section',
    title: __('New Section', 'wp-react-admin-panel'),
    className: 'tab-new-section',
    content: (
        <PanelBody>
            <TextControl
                label={__('New Setting', 'wp-react-admin-panel')}
                value={settings.newSection.newSetting}
                onChange={(value) => updateSettings('newSection', 'newSetting', value)}
            />
        </PanelBody>
    )
}
```

## License

GPL v2 or later

## Credits

Developed by Kamal Hosen

## Support

For support, feature requests, or bug reports, please [open an issue](https://github.com/ikamal7/wp-react-admin-panel/issues) on GitHub.