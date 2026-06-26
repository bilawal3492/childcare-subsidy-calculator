=== The Child Care Subsidy Calculator ===
Contributors: i9education
Tags: childcare, subsidy, calculator, australia, ccs, hubspot
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 2.2.4
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Calculate Australian Child Care Subsidy (CCS) with multi-child support, age-based caps, and professional email delivery.

== Description ==

The Child Care Subsidy Calculator is a comprehensive WordPress plugin designed to help Australian families estimate their Child Care Subsidy (CCS) entitlements. With support for multiple children, age-based hourly caps, and real-time calculations, this plugin provides accurate estimates based on current government policy.

= Key Features =

* **Multi-Child Support** - Calculate subsidies for up to 5 children simultaneously
* **Australian Suburbs Database** - 18,000+ suburbs with postcode search
* **Real-Time Calculations** - Instant CCS percentage based on income thresholds
* **Age-Based Caps** - Automatic hourly rate caps based on child age and care type
* **Professional Email Templates** - Beautiful HTML emails with customizable design
* **Submissions Management** - Track all calculator submissions in WordPress admin
* **Appearance Customization** - Complete styling control with 6 organized tabs
* **Form Integration** - HubSpot integration with custom fallback form
* **Mobile Responsive** - Works perfectly on all devices

= Perfect For =

* Childcare centers and early learning centers
* Family day care providers
* Government and community organizations
* Parenting websites and blogs
* Educational institutions

= How It Works =

1. **Location Selection** - Users enter their suburb or postcode
2. **Household Income** - Input combined family income
3. **Child Details** - Add children with age, days, hours, and fees
4. **Summary & Email** - View results and receive detailed email summary

= Email Features =

* Customizable header image
* Contact information (phone, email)
* Social media integration (Facebook, Twitter, Instagram, LinkedIn)
* Call-to-action button
* Mobile-responsive design
* Separate user and admin notifications

= Admin Features =

* **Submissions Dashboard** - View all calculator submissions
* **Email Template Settings** - Customize email design and content
* **Appearance Settings** - Control colors, fonts, and styling
* **Policy Configuration** - Update CCS rates and thresholds
* **Suburbs Management** - Import and manage Australian suburbs data

= Technical Highlights =

* Clean, modern interface with smooth animations
* RGBA color support with transparency controls
* Database-driven suburb search with AJAX autocomplete
* Secure data handling with WordPress best practices
* GPL-compatible and open source

== Installation ==

= Automatic Installation =

1. Log in to your WordPress admin panel
2. Navigate to Plugins > Add New
3. Search for "The Child Care Subsidy Calculator"
4. Click "Install Now" and then "Activate"

= Manual Installation =

1. Download the plugin ZIP file
2. Log in to your WordPress admin panel
3. Navigate to Plugins > Add New > Upload Plugin
4. Choose the ZIP file and click "Install Now"
5. Click "Activate Plugin"

= After Activation =

1. Go to **CCS Calculator > Settings** to configure policy rates
2. Go to **CCS Calculator > Email Template** to customize email design
3. Go to **CCS Calculator > Appearance** to style the calculator
4. Add the shortcode `[thechildcare_ccs_calculator]` to any page or post

== Frequently Asked Questions ==

= How accurate are the calculations? =

The calculator provides estimates based on current Australian Government CCS policy. Final entitlements are determined by Services Australia. We recommend users verify results with official sources.

= Can I customize the calculator appearance? =

Yes! The plugin includes a comprehensive Appearance settings page with 6 tabs for customizing typography, colors, buttons, layout, and more. RGBA color support allows transparency effects.

= Does it work with HubSpot? =

Yes, the plugin includes optional HubSpot form integration. You can also use the built-in custom form if you don't use HubSpot.

= How do I update the CCS rates? =

Go to **CCS Calculator > Settings** in your WordPress admin. You can update income thresholds, subsidy percentages, and hourly caps to match current government policy.

= Can I see who used the calculator? =

Yes! All submissions are saved in **CCS Calculator > Submissions** where you can view user details, location, and calculation results.

= Is the plugin mobile-friendly? =

Absolutely! The calculator and email templates are fully responsive and work perfectly on all devices.

= Can I add my logo to emails? =

Yes! Go to **CCS Calculator > Email Template** and upload your header image using the WordPress media library.

= Does it support multiple languages? =

The plugin is translation-ready with text domain support. You can translate it using standard WordPress translation tools.

== Screenshots ==

1. Location – Location selection step in the calculator.
2. Children – Entering children details.
3. Income – Entering family income information.
4. Summary – Subsidy summary and breakdown.
5. Email – Email template customization screen.

== Changelog ==

= 2.0.4 - 2025-11-05 =

**Enhanced Summary Display**
* Added comprehensive subsidy holding information in summary section
* Improved summary layout with better organization and readability
* Added loader functionality for better user experience during calculations

**UI/UX Enhancements**
* Enhanced admin interface with improved menu structure and navigation
* Added loading states and progress indicators for better visual feedback
* Refined CSS styling for cleaner, more modern appearance

**Technical Improvements**
* Optimized code structure in Shortcode.php for better maintainability
* Enhanced settings management in admin panel
* Resolved various minor issues for improved stability

= 2.0.3 - 2025-10-28 =

**Email Template Improvements**
* Removed "View Detailed Estimate" button from user email template for cleaner design
* Removed "View Full Submission in WordPress" button from admin email template
* Cleaned up button configuration options from admin panel settings
* Removed unused button variables and settings registration

**UI/UX Enhancements**
* Updated calculator title to "Childcare Subsidy Calculator" for consistency
* Improved summary layout by removing borders for cleaner appearance
* Enhanced summary cards with better spacing, flex layout, and color-coded backgrounds
* Better mobile responsiveness with flex-wrap for summary rows

**Code Quality**
* Removed unnecessary EMAIL_TEMPLATE_CHANGES.md documentation file
* Improved email template HTML structure for better email client compatibility
* Enhanced CSS with flexbox for responsive summary display

= 2.0.2 - 2025-10-15 =
* Consolidated all email template documentation
* Removed redundant .md files for clarity
* Documentation and structure improvements

= 2.0.1 - 2025-10-06 =

**Major Updates & Refinements**

* **Enhanced Plugin Stability** - Improved error handling and compatibility across different server configurations
* **Performance Optimizations** - Faster loading times and reduced memory footprint
* **UI/UX Improvements** - Refined styling and better user experience throughout the calculator
* **Email System Enhancements** - More reliable email delivery with improved template rendering
* **Admin Interface Polish** - Streamlined admin panels with better navigation and clearer settings
* **Enhanced CSS specificity** for better theme compatibility
* **Improved AJAX handling** for suburb search functionality
* **Better mobile responsiveness** across all components
* **Bug fixes** for styling inconsistencies and calculation edge cases

= 2.0.0 - 2025-10-02 =

**Major Features Added**

1. **Submissions Management System**
   - Complete admin panel for viewing all calculator submissions
   - Detailed submission view with user contact information
   - Location and enrolment details tracking
   - Full calculation summary display
   - Delete and manage submissions functionality

2. **Professional Email Templates**
   - Beautiful HTML email design with header image support
   - Customizable contact information (phone, email)
   - Social media integration (Facebook, Twitter, Instagram, LinkedIn)
   - Call-to-action button with custom text and URL
   - Mobile-responsive design for all email clients
   - Separate user and admin notification emails

3. **Appearance Settings Panel**
   - Complete styling customization interface with 6 organized tabs
   - Typography controls (fonts, sizes, colors)
   - Input field styling (backgrounds, borders, focus states)
   - Button customization (colors, hover effects, border radius)
   - Layout settings (container, padding, shadows)
   - Progress bar colors and component styling
   - RGBA color support with transparency controls
   - Tab persistence - settings saved per tab without overwriting

4. **Form Integration Enhancement**
   - HubSpot form integration with automatic data population
   - Custom fallback form for non-HubSpot users
   - Step 1 data collection (location, ATSI status, enrolment option)
   - Automatic email delivery to users and admin
   - Submission tracking in WordPress database

5. **Enhanced Summary Display**
   - Professional table layout with borders and proper spacing
   - Two-column weekly breakdown (Week 1 | Week 2)
   - Color-coded values for better readability
   - Consistent formatting across calculator, emails, and admin
   - Mobile-responsive design

**Technical Improvements**
- WordPress version compatibility (5.0+, tested up to 6.4)
- Improved email client compatibility with inline styles
- Media library integration for header image uploads
- Session storage for admin settings persistence
- RGBA color validation and sanitization
- PHP version validation on plugin load
- Improved CSS specificity with !important flags

= 1.8.0 - 2025-10-01 =
* Added Australian suburbs database with 18,000+ locations
* Added suburb autocomplete with real-time search
* Added HubSpot form integration
* Added email summary delivery system
* Improved plugin architecture and performance

= 1.7.0 - 2025-10-01 =
* Added professional email templates
* Added CRM integration capabilities
* Added dynamic form loading
* Improved database structure

= 1.4.0 - 2025-10-01 =
* Initial public release
* Multi-child CCS calculator
* Step progress indicator
* Location-based suburb autocomplete
* Real-time calculations

== Upgrade Notice ==

= 2.0.4 =
Enhanced summary display with subsidy holding information, improved loading states, and better admin interface. Safe to upgrade.

= 2.0.3 =
UI/UX improvements with cleaner email templates and enhanced summary display. Removed unnecessary buttons for better user experience. Safe to upgrade.

= 2.0.1 =
Refinement update with enhanced stability, performance optimizations, and improved user experience. Safe to upgrade.

= 2.0.0 =
Major update with submissions management, professional email templates, and complete appearance customization. Backup recommended before upgrading.

= 1.8.0 =
Significant update with Australian suburbs database and HubSpot integration. No breaking changes.

== Additional Information ==

= Support =
For support, please visit [i9 Education](https://i9.edu.au/) or use the WordPress.org support forums.

= Privacy Policy =
This plugin stores calculator submissions in your WordPress database. Each submission may include the person's name, email, phone, location/suburb, enrolment option, Aboriginal and/or Torres Strait Islander (ATSI) status, and the calculation summary. Email addresses are only used to send calculation summaries. No data is sent to external services unless you configure HubSpot integration.

**Sensitive information:** ATSI status is sensitive personal information under the Australian Privacy Act. Only collect and retain it where appropriate, and disclose its collection in your site's privacy policy.

**Consent:** When the built-in custom form is used, the user's agreement to your privacy policy (and optional contact opt-in) is recorded with each submission, along with a timestamp.

**Data subject requests:** The plugin integrates with WordPress's privacy tools (Tools → Export Personal Data / Erase Personal Data), so calculator submissions for a given email address are included in personal-data exports and erasures.

**Retention:** Under CCS Calculator → Calculator Settings → Data & Privacy you can set an auto-delete period. Submissions older than that many days are permanently removed by a daily task. The default is 0 (keep submissions forever).

= Credits =
Developed by i9 Education
Australian suburbs data sourced from official government datasets

= Disclaimer =
This calculator provides estimates only. Final Child Care Subsidy entitlements are determined by Services Australia. Always verify calculations with official government sources.
