# Company Viewer Plugin

**Company Viewer** is a WordPress plugin that allows you to manage and display company details such as logo, contact information, and address in a searchable table format. It also provides a popup view that shows additional company information when the user clicks on the company name.

## Features

- Add companies with a name, logo, contact information, and address.
- List companies in a searchable table on the front end.
- Display company details in a popup when clicking on the company name.
- Easy to use and customize fields for adding company details.
- Searchable company table with dynamic filtering.

## Installation

1. **Download the plugin** from the GitHub repository.
2. Upload the plugin files to the `/wp-content/plugins/company-viewer` directory, or install the plugin through the WordPress plugins screen directly.
3. Activate the plugin through the 'Plugins' screen in WordPress.
4. After activation, a new **Companies** menu will appear in the WordPress dashboard.

## Usage

1. Navigate to the **Companies** menu in the WordPress dashboard.
2. Add new companies by filling in the required fields:
   - **Company Name**: The name of the company.
   - **Logo**: Upload a company logo.
   - **Contact Information**: Enter the company's contact details.
   - **Address**: Add the company's address.
3. To display the company list on the front end, create or edit a page and add the following shortcode:

   ```plaintext
   [company_viewer_widget]
4. The shortcode will generate a searchable table that lists all companies with their name, contact information, and address.
5. Clicking on a company name will display a popup with additional details (logo, contact info, address).
