# WPPOOL Hybrid License System

A robust, product-agnostic licensing system that supports Fluent licensing with automatic updates, admin interface, and comprehensive hooks.

## Table of Contents

- [Quick Start](#quick-start)
- [Configuration](#configuration)
- [Methods](#methods)
- [Hooks & Actions](#hooks--actions)
- [Development & Staging](#development--staging)

---

## Quick Start

### 1. Load the License Class

```php
if ( ! class_exists( '\\WPPOOL\\License' ) ) {
    require_once YOUR_PLUGIN_PATH . '/license/class-license.php';
}
```

### 2. Initialize and Connect

```php
$license = (new \WPPOOL\License([
    // Required Configuration
    'plugin_file'    => YOUR_PLUGIN_FILE,
    'plugin_version' => YOUR_PLUGIN_VERSION,
    'plugin_name'    => 'YOUR PLUGIN NAME',
    'item_id'        => 30,

    // License System Configuration (Optional)
    'appsero_client_id' => 'your-appsero-client-id',

    // Menu Configuration (Optional)
    'parent_slug'      => 'parent-menu-slug',
    'menu_slug'        => 'custom-license-page-slug',
    'menu_title'       => 'License Settings',
    'show_action_link' => true,

    // External URLs (Optional)
    'pricing_page_url'        => 'https://wppool.dev/your-plugin-pricing/',
    'activate_redirect_url'   => admin_url('admin.php?page=your-plugin-settings'),
    'deactivate_redirect_url' => admin_url('admin.php?page=your-plugin-license'),
]))->connect();

if ( $license->is_valid() ) {
    // Unlock premium features
}
```

---

## Configuration

### Required Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `plugin_file` | string | Full path to main plugin file |
| `plugin_version` | string | Plugin version number |
| `plugin_name` | string | Human-readable plugin name |
| `item_id` | int | Product item ID from Fluent Commerce |

### Optional Parameters

#### Appsero Integration for Insight.
| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `appsero_client_id` | string | `''` | Appsero client ID for dual licensing support |

#### Menu Configuration
| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `parent_slug` | string | `''` | Parent menu slug for license page |
| `menu_slug` | string | `{plugin-slug}-license` | Custom menu slug |
| `menu_title` | string | `'License'` | Menu title for license page |
| `show_action_link` | bool | `true` | Show action link in plugins page |

#### External URLs
| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `pricing_page_url` | string | `''` | URL to plugin pricing page |
| `account_url` | string | `'https://portal.wppool.dev/account'` | Account management URL |
| `api_url` | string | `'https://portal.wppool.dev/'` | Fluent API URL |
| `activate_redirect_url` | string | `''` | Redirect URL after successful license activation |
| `deactivate_redirect_url` | string | `''` | Redirect URL after license deactivation |

---

## Methods

### Core Methods

#### `connect()`
Initialize and connect the licensing system.
```php
$license->connect(); // Returns: License instance (chainable)
```

#### `is_valid()`
Check if the current license is valid.
```php
$license->is_valid(); // Returns: bool
```

#### `get_data()`
Get complete license data array.
```php
$license->get_data(); // Returns: array
```

#### `get_license_info()`
Get formatted license information for display.
```php
$license->get_license_info(); // Returns: array
```

#### `get_config( $key = null )`
Get plugin configuration.
```php
$license->get_config();           // Returns: Full config array
$license->get_config('item_id');  // Returns: Specific config value
```

---

## Hooks & Actions

### License Events

#### `wppool_license_activated`
Fired when a license is successfully activated.
```php
add_action('wppool_license_activated', function($plugin_slug, $license_data) {
    if ($plugin_slug === 'your-plugin-slug') {
        // Handle license activation
        // $license_data contains: status, expires, variation_id, etc.
    }
});
```

#### `wppool_license_deactivated`
Fired when a license is deactivated.
```php
add_action('wppool_license_deactivated', function($plugin_slug) {
    if ($plugin_slug === 'your-plugin-slug') {
        // Handle license deactivation
        // Clean up premium features, cache, etc.
    }
});
```

### UI Customization

#### `wppool_license_settings_render`
Add custom content to license settings page.
```php
add_action('wppool_license_settings_render', function($plugin_slug, $license_status) {
    if ($plugin_slug === 'your-plugin-slug') {
        echo '<div class="custom-license-content">';
        echo '<h3>Additional License Information</h3>';
        echo '<p>Custom content here...</p>';
        echo '</div>';

        // Or add custom JavaScript
        echo '<script>console.log("License page loaded");</script>';
    }
});
```

### JavaScript Events

Custom JavaScript events are dispatched on the license settings page for client-side integrations.

#### `wppool_license_activated`
Fired when a license is successfully activated.
```javascript
document.addEventListener('wppool_license_activated', function(event) {
    const licenseInfo = event.detail; // Contains: slug
    console.log('License activated for:', licenseInfo.slug);
    // Show success notification, trigger analytics, etc.
});
```

#### `wppool_license_activated_error`
Fired when license activation fails.
```javascript
document.addEventListener('wppool_license_activated_error', function(event) {
    const error = event.detail; // Contains: message, status
    console.error('License activation failed:', error.message);
    // Handle error, show custom notification, etc.
});
```

#### `wppool_license_deactivated`
Fired when a license is successfully deactivated.
```javascript
document.addEventListener('wppool_license_deactivated', function(event) {
    const licenseInfo = event.detail; // Contains: slug
    console.log('License deactivated for:', licenseInfo.slug);
    // Clean up client-side state, show notification, etc.
});
```

#### `wppool_license_deactivated_error`
Fired when license deactivation fails.
```javascript
document.addEventListener('wppool_license_deactivated_error', function(event) {
    const error = event.detail;
    console.error('License deactivation failed:', error);
    // Handle error gracefully
});
```

#### `wppool_license_status_error`
Fired when license status check encounters an error.
```javascript
document.addEventListener('wppool_license_status_error', function(event) {
    const response = event.detail; // Contains: error_notice, remote_data
    console.warn('License status error:', response.error_notice);
    // Display warning to user, trigger status refresh, etc.
});
```

---

## Development & Staging

### Staging Configuration

For development and staging environments, use the staging API URL:

```php
public function connect_license() {
    if ( ! class_exists( '\\WPPOOL\\License' ) ) {
        require_once YOUR_PLUGIN_PATH . '/license/class-license.php';
    }

    $license = (new \WPPOOL\License([
        'plugin_file'    => YOUR_PLUGIN_FILE,
        'plugin_version' => YOUR_PLUGIN_VERSION,
        'plugin_name'    => 'YOUR PLUGIN NAME',
        'parent_slug'    => 'parent-menu-slug',
        'item_id'        => 30,
        'pricing_page_url'        => 'https://wppool.dev/your-plugin-pricing/',
        'activate_redirect_url'   => admin_url('admin.php?page=your-plugin-settings'),
        'deactivate_redirect_url' => admin_url('admin.php?page=your-plugin-license'),

        // License System Configuration (Optional)
        'appsero_client_id' => 'your-appsero-client-id',

        // Staging API URL (remove in production)
        'api_url' => 'https://fc-staging.wppool.dev/',
    ]))->connect();

    if ( $license->is_valid() ) {
        // Unlock features
    }
}
add_action('init', 'connect_license');
```

### Product Item IDs (Staging)

| Product | Item ID |
|---------|---------|
| ArchiveMaster | 72 |
| Multi-vendor for FlexStock | 61 |
| EchoRewards | 35 |
| FormyChat | 34 |
| Jitsi Meet | 33 |
| FlexOrder | 32 |
| FlexStock | 31 |
| WP Dark Mode | 30 |
| FlexTable | 26 |

---

## Best Practices

1. **Always check license validity** before enabling premium features
2. **Use hooks** to handle license state changes gracefully  
3. **Cache license status** to avoid repeated API calls
4. **Provide clear messaging** to users about license status
5. **Test thoroughly** in staging environment before production