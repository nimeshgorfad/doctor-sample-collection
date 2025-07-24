# Docty Clinic Sample Collection

A WordPress plugin that adds a required "Sample Collection" radio field to the WooCommerce checkout page, allowing customers to choose how they want to provide their sample (from home or at a clinic center). The plugin also provides an admin settings page to configure the Clinic Profile ID used to fetch clinic locations.

## Features
- Adds a required sample collection method field to WooCommerce checkout
- Option to provide home address or select a clinic location
- Fetches clinic locations dynamically using the Clinic Profile ID
- Saves the selected method and address/location to the order meta
- Displays the selected method and address/location in the WooCommerce admin order page
- Admin settings page ("Collection Setting") to set the Clinic Profile ID

## Installation
1. Upload the plugin files to the `/wp-content/plugins/doctor-sample-collection` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Go to **Collection Setting** in the WordPress admin menu and enter your Clinic Profile ID.

## Usage
- On the WooCommerce checkout page, customers will be prompted to select a sample collection method:
  - **From Home**: Requires the customer to enter their home address.
  - **Visit At Center**: Requires the customer to select a clinic location (fetched using the Clinic Profile ID).
- The selected information is saved with the order and visible in the admin order details.

## Settings
- Go to **Collection Setting** in the WordPress admin menu.
- Enter your Clinic Profile ID (provided by Docty).
- The plugin will use this ID to fetch available clinic locations.

## Requirements
- WordPress
- WooCommerce

## Support
For support, contact the plugin author or open an issue in your project repository.
