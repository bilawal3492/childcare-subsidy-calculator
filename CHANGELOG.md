# Changelog

All notable changes to The Child Care Subsidy Calculator plugin will be documented in this file.

## [2.2.2] - 2026-06-26

### 🏗️ Phase 3 — Admin refactor (no functional change)
- Split the 7,081-line `includes/Admin/Menu.php` into a 177-line controller plus ten view partials under `includes/Admin/views/` (settings, form integration, appearance, custom CSS, email template, submissions, single submission, how-to-use, dashboard, changelog). Each admin page's markup is byte-identical — only relocated — so behaviour is unchanged. The file is now maintainable instead of a single 7k-line blob.

## [2.2.1] - 2026-06-26

### 🧹 Phase 2 — Cleanup (no functional change)
- Removed dead files: `includes/Ajax/Email.backup.php`, `assets/css/childcare-backup.css`, and the unused `includes/Autoloader.php` (the latter also had a latent case-sensitivity bug that would have failed on Linux had it been used).
- Front-end `console.log` debug output is now gated behind `WP_DEBUG`, so production stays quiet (genuine `console.error` reporting is kept).
- Stopped tracking `.DS_Store` files in the repository.

## [2.2.0] - 2026-06-26

Stable release consolidating the security hardening and calculation-engine work from the 2.2.0 beta series. No change to the HubSpot submission or automated-email behaviour.

### 🔒 Security
- All front-end AJAX (summary submission + suburb search) now require a valid nonce, closing the previously open/unauthenticated endpoints.
- Email recipient validated with `is_email()`, per-IP rate limiting, and a honeypot field to stop spam/abuse of the public endpoint.
- Submitted summary HTML is sanitised through a strict tag/attribute allowlist before it reaches emails, the database, or wp-admin.
- Removed a debug tool and verbose logging from the shipped plugin.

### 🧮 Calculation engine
- New server-side `CCSEngine` mirrors the calculator exactly and is covered by 59 automated tests.
- Each submission is recomputed server-side and stored as an authoritative figures record; the admin submission screen shows a Match/Mismatch check plus a "Verified Figures" panel.

### 🎯 Accuracy
- **Per-child "Type of Care" selector** (Centre Based Day Care, Family Day Care, OSHC, In Home Care) applies the correct hourly-rate cap per care type and age. Centre Based Day Care (the default) reproduces previous numbers exactly.
- Robust calendar-based age calculation replacing the previous epoch-based method.
- End-of-year figure floored at $0 (defensive).

## [2.2.0-beta7] - 2026-06-26

### 🔧 Phase 1B — Small correctness fixes
- **Robust age calculation** - Replaced the fragile epoch-based `getAge` (which could mis-handle leap years/timezones) with a proper calendar-age calculation that matches the server engine: subtracts a year if the birthday hasn't occurred yet, and returns 0 for empty/invalid/future dates.
- **After-EOY floored at $0 (defensive)** - The end-of-year figure is now floored at zero in both the browser and the engine. (Mathematically it was already always ≥ 0 because the subsidy is capped at the fee, so no displayed number changes — this just future-proofs the formula.)
- Test suite expanded to 59 assertions (added calendar-age birthday/leap-year edge cases).

## [2.2.0-beta6] - 2026-06-26

### 🐛 Fix
- **Server recompute ignored care type** - The server-side validation rebuilt each child without the `care_type` field, so it defaulted every child to Centre Based Day Care and produced a false mismatch (e.g. a Family Day Care child was capped at the centre rate). The server now passes care type through, so Verified Figures and the match check are correct for all care types.

## [2.2.0-beta5] - 2026-06-26

### 🎯 Phase 1B — Care type selector + correct hourly caps
- **Per-child "Type of Care" selector** - Each child can now be set to Centre Based Day Care (default), Family Day Care, Outside School Hours Care (OSHC), or In Home Care. The correct hourly-rate cap is applied per care type and age, replacing the previous age-only cap that ignored care type.
- **Behaviour preserved for existing use** - Centre Based Day Care (the default) reproduces the previous numbers exactly, so existing submissions are unaffected; only FDC/OSHC/In-Home selections change the cap.
- **Care type shown everywhere** - Appears in the on-page summary, the emailed/stored summary, and is included in the per-child details sent to HubSpot.
- **Engine + tests** - The server engine mirrors the new cap logic; test suite expanded to 56 assertions covering every care-type/age cap.

## [2.2.0-beta4] - 2026-06-26

### 🔐 Phase 1B (Option A) — Authoritative server figures
- **Server-computed figures stored as source of truth** - On each submission the server now records its own computed CCS percentages, hours, and per-fortnight totals (`ccs_server_figures`) as the trustworthy record, independent of the browser. The email/display still uses the parity-verified browser HTML, so appearance is unchanged.
- **Admin "Verified Figures" panel** - The submission detail screen shows the server-computed Standard/Higher CCS %, CCS hours, and fortnightly fees/subsidy/out-of-pocket alongside the existing match/mismatch badge.

## [2.2.0-beta3] - 2026-06-26

### 🔧 Shadow-validation parity
- **Matched the browser's percentage rounding** - The browser stores the calculated CCS percentages in form fields via `toFixed(2)` (2 decimals) and reads them back. The server engine now replicates that exact round-trip, eliminating ~6-cent shadow-validation mismatches caused by the server using full-precision percentages. Engine test suite expanded to 48 assertions.

## [2.2.0-beta2] - 2026-06-26

### 🐛 Fix
- **Submit stuck on "Sending…" (custom form)** - Fixed `ReferenceError: ccsCollectShadowData is not defined`. The shadow-validation helper was defined inside the HubSpot form scope and was unreachable from the custom-form submit handler; it is now defined at the top scope so both submit paths work.

## [2.2.0-beta1] - 2026-06-26

### 🧪 Phase 1A — Server-side calculation engine (shadow mode)
This is a STAGING beta. No user-facing numbers change; it adds a server-side engine that silently double-checks the browser's calculation.

- **New `CCSEngine` (server-side)** - PHP re-implementation of the CCS calculation that mirrors the existing browser logic exactly (income→subsidy %, higher-CCS bands, 3-Day Guarantee hours, age-based caps, withholding, out-of-pocket). Covered by 46 automated tests.
- **Shadow validation** - On each submission the server recomputes the figures and compares them to the browser's totals within a 1-cent tolerance. The result is stored on the submission and never blocks or alters it.
- **Admin visibility** - The submission detail screen now shows a "Calculation Check: ✓ Match / ✗ Mismatch" badge (with a field-by-field diff on mismatch) so parity can be verified on real data before the engine is made authoritative (Phase 1B).

## [2.1.2] - 2026-06-26

### 🐛 Fix
- **Honeypot false positive** - The anti-spam honeypot field could be auto-filled by browsers/password managers (it was an off-screen text input rendered first in the form), causing legitimate custom-form submissions to be wrongly rejected with "Submission rejected." The field is now hidden with `display:none` and placed last in the form so it is never auto-filled, while still catching bots.

## [2.1.1] - 2026-06-26

### 🔒 Security Hardening (Phase 0)
No changes to calculator behaviour, HubSpot submission, or email delivery — these fixes wrap the existing flow with protection only.

- **CSRF / nonce verification** - The summary-submission (`send_summary_email`) and suburb-search (`ccs_search_suburbs`) AJAX endpoints now require a valid WordPress nonce, closing the previously open, unauthenticated endpoints.
- **Email recipient validation** - Submissions are now validated with `is_email()` before any mail is sent, preventing the endpoint from being used as an open relay.
- **Rate limiting** - Per-IP throttling (max 10 submissions / 10 minutes) deters flooding and database-spamming abuse.
- **Honeypot anti-spam** - Hidden field on the custom form silently rejects bot submissions (invisible to real users).
- **Stricter summary sanitization** - Submitted summary HTML is now filtered through a tight tag/attribute allowlist (rendering is unchanged) to strip any script/event-handler injection before it reaches emails, the database, or wp-admin.
- **Removed debug logging** - Stripped informational `error_log()` calls that ran on every submission.
- **Removed debug tool from production** - Deleted `check-email-settings.php` from the shipped plugin.

## [2.1.0] - 2025-11-27

### 🚀 Major HubSpot Integration Update
- **Individual CCS Fields** - Now submits 31 individual fields to HubSpot instead of one combined summary field
- **Better Data Segmentation** - Each calculator value is stored in its own HubSpot property for improved reporting and workflows
- **Child Details Fields** - Separate multi-line fields for up to 5 children's details (`ccs_child_1_details` through `ccs_child_5_details`)

### 📱 Phone Number Improvements
- **Fixed Phone Capture** - Resolved issue where phone numbers were not being captured in HubSpot
- **Dial Code Fallback** - Added fallback logic to combine country dial code with phone number when international formatting fails
- **Better International Support** - Improved handling of international phone numbers with intl-tel-input

### ✅ Form Validation
- **Email Validation** - Added RFC-compliant email validation to prevent spam submissions
- **Phone Validation** - Added phone number validation using intl-tel-input's validation methods
- **User-Friendly Errors** - Clear error messages displayed below fields when validation fails

### 🧹 Code Cleanup
- **Removed Unused Code** - Removed `generateSummaryText()`, `updateHubSpotHiddenField()` and related unused functions
- **Removed Hidden Field Setting** - Removed the "Hidden Field Name" admin setting (no longer needed with individual fields)
- **Updated Documentation** - Admin panel now shows complete list of HubSpot fields to configure

### Technical Details
- Updated files: `Shortcode.php`, `Settings.php`, `Menu.php`
- Removed ~220 lines of unused code
- HubSpot Forms API v3 direct submission maintained

## [2.0.4] - 2025-11-05

### Enhanced Summary Display
- **Childcare Subsidy Holding Fields** - Added comprehensive subsidy holding information in summary section
- **Improved Summary Layout** - Enhanced summary display with better organization and readability
- **Loading Improvements** - Added loader functionality for better user experience during calculations

### UI/UX Enhancements
- **Enhanced Admin Interface** - Improved admin menu structure and navigation
- **Better Visual Feedback** - Added loading states and progress indicators
- **Refined CSS Styling** - Updated styles for cleaner, more modern appearance

### Technical Improvements
- **Code Optimization** - Improved code structure in Shortcode.php for better maintainability
- **Settings Enhancement** - Enhanced settings management in admin panel
- **Bug Fixes** - Resolved various minor issues for improved stability

## [2.0.3] - 2025-10-28

### Email Template Improvements
- **Removed Call-to-Action Buttons** - Removed "View Detailed Estimate" button from user email template for cleaner design
- **Removed Admin Button** - Removed "View Full Submission in WordPress" button from admin email template
- **Cleaned Up Settings** - Removed button configuration options from admin panel (Email Template settings)
- **Code Cleanup** - Removed unused button variables and settings registration from codebase

### UI/UX Enhancements
- **Updated Calculator Title** - Changed from "Child Care Subsidy Calculator" to "Childcare Subsidy Calculator" for consistency
- **Improved Summary Layout** - Removed borders from summary columns for cleaner appearance
- **Enhanced Summary Cards** - Updated summary row styling with better spacing, flex layout, and color-coded backgrounds
- **Better Mobile Responsiveness** - Summary rows now wrap properly on smaller screens with flex-wrap

### Code Quality
- **Removed Unnecessary Documentation** - Deleted `EMAIL_TEMPLATE_CHANGES.md` file to reduce clutter
- **Maintained Essential Docs** - Kept `CHANGELOG.md` and `data/README.md` for important reference

### Technical Details
- Updated files: `Email.php`, `Menu.php`, `Settings.php`, `Shortcode.php`
- Improved email template HTML structure for better email client compatibility
- Enhanced CSS with flexbox for responsive summary display

## [2.0.2] - 2025-10-15

### Documentation & Maintenance
- Consolidated all email template documentation into `EMAIL_TEMPLATE_CHANGES.md` for easier reference.
- Removed redundant markdown files: `EMAIL_TEMPLATES_MIGRATION.md`, `EMAIL_TEMPLATE_IMPROVEMENTS.md`, `TEST_EMAIL_SETTINGS.md`, `TAB_SWITCHING_FIX.md`.
- No code changes; documentation and project structure improvements only.

## [2.0.1] - 2025-10-06

### Major Updates & Refinements

**🎯 Core Enhancements**
- **Enhanced Plugin Stability** - Improved error handling and compatibility across different server configurations
- **Performance Optimizations** - Faster loading times and reduced memory footprint
- **UI/UX Improvements** - Refined styling and better user experience throughout the calculator
- **Email System Enhancements** - More reliable email delivery with improved template rendering
- **Admin Interface Polish** - Streamlined admin panels with better navigation and clearer settings

**🔧 Technical Improvements**
- Enhanced CSS specificity for better theme compatibility
- Improved AJAX handling for suburb search functionality
- Better mobile responsiveness across all components
- Optimized database queries for submissions management
- Enhanced security measures and data validation

**📧 Email & Communication**
- Improved email template compatibility across different email clients
- Enhanced HTML rendering for better visual consistency
- Refined contact information display in email templates
- Better handling of social media integration links

**🎨 Appearance & Styling**
- Fine-tuned color schemes and typography
- Enhanced button hover effects and transitions
- Improved form field styling and focus states
- Better progress indicator visibility and animations
- Refined table layouts for calculation summaries

**🐛 Bug Fixes**
- Fixed minor styling inconsistencies across different browsers
- Resolved edge cases in calculation logic
- Improved error handling for form submissions
- Enhanced compatibility with various WordPress themes
- Fixed minor issues with suburb autocomplete functionality

## [2.0.0] - 2025-10-02

### Major Features Added

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

### Technical Improvements
- WordPress version compatibility (5.0+, tested up to 6.4)
- Improved email client compatibility with inline styles
- Media library integration for header image uploads
- Session storage for admin settings persistence
- RGBA color validation and sanitization
- PHP version validation on plugin load
- Improved CSS specificity with !important flags

## [1.8.0] - 2025-10-01

### Added
- **Australian Suburbs with Postcodes** - Complete database of 18,000+ Australian suburbs
- **Suburb Autocomplete** - Real-time search by suburb name or postcode
- **Database-Driven System** - Suburbs stored in WordPress database for fast access
- **HubSpot Form Integration** - Professional lead capture with CRM sync
- **Email Summary Delivery** - Automatic email with calculation details to users
- **Suburbs Management Page** - Admin interface to import/update suburbs data
- **AJAX Search** - Dynamic suburb suggestions without page reload
- **State Information** - Suburbs include state/territory data (NSW, QLD, VIC, etc.)

### Changed
- **Simplified Plugin Architecture** - Removed autoloader for better compatibility
- **Manual File Loading** - Direct require statements for all classes
- **Improved Activation Process** - More reliable plugin activation
- **Better Error Handling** - Graceful fallbacks for missing components
- **Optimized Performance** - Faster page loads with database queries

### Fixed
- **Fatal Activation Errors** - Plugin now activates on all server configurations
- **Class Loading Issues** - Removed autoloader dependency
- **Admin Menu Registration** - Menu appears consistently
- **Shortcode Display** - Calculator renders properly on frontend
- **Special Characters** - Proper handling of apostrophes in suburb names (O'Connor, D'Aguilar)
- **File Permissions** - Works with various server setups

### Technical Improvements
- Removed Autoloader.php dependency
- Direct class file inclusion
- Simplified initialization process
- Better WordPress compatibility
- Reduced memory footprint

## [1.7.0] - 2025-10-01

### Added
- **HubSpot Form Integration** - Professional form collection for user information
- **Automatic Email Summary** - Sends detailed calculation summary to users
- **CRM Integration** - Contacts automatically added to HubSpot
- **HubSpot Settings Page** - Configure Portal ID, Form ID, and Region in admin
- **Dynamic Form Loading** - HubSpot form loads on Step 4 (Summary)
- **Email Template** - Beautiful HTML email with full calculation details
- **Database-driven suburbs system** - Suburbs now stored in WordPress database table
- **AJAX-powered autocomplete** - Dynamic suburb search without loading large files
- **Suburbs management admin page** - Easy import/update of Australian suburbs data
- **Automatic data import** - Suburbs imported from GitHub on plugin activation
- **Debounced search** - Optimized search with 300ms debounce for better performance
- **State information** - Suburbs now include state/territory data

### Changed
- **Replaced static PHP array** with database table for 18,000+ suburbs
- **Improved search performance** - No more loading massive PHP files
- **Dynamic data fetching** - Suburbs loaded on-demand via AJAX
- **Better scalability** - Can handle unlimited suburbs without performance issues
- **Cleaner codebase** - Removed large static data files

### Fixed
- **Syntax errors** with apostrophes in suburb names (O'Connor, D'Aguilar, etc.)
- **Memory issues** from loading large PHP arrays
- **Page load performance** - No longer loads 18,000+ suburbs on every page
- **Special character handling** - Proper escaping in database storage

### Technical Improvements
- Created `SuburbsTable` class for database operations
- Created `SuburbSearch` AJAX handler for autocomplete
- Created `SuburbsManager` admin interface
- Implemented indexed database columns for fast searching
- Added background import scheduling

## [1.6.0] - 2025-10-01

### Added
- Complete Australian suburbs database with 18,533+ suburbs and postcodes
- Coverage for all Australian states and territories (NSW, QLD, VIC, SA, WA, TAS, ACT, NT)
- Postal code search functionality - users can search by postcode or suburb name
- Suburb autocomplete with two-column display (suburb name and postcode badge)
- Automated import tools for updating suburbs data
- Download script for fetching latest Australian postcodes
- CSV import utility for custom suburb datasets
- Comprehensive documentation for suburbs data management

### Changed
- Upgraded suburbs database from 900 to 18,533+ locations
- Enhanced suburb suggestions dropdown with styled postcode badges
- Improved search algorithm to filter by both suburb name and postcode
- Updated data structure to associative array format ('Suburb' => 'Postcode')
- Optimized autocomplete performance for large datasets

### Fixed
- Suburb data completeness - now covers entire Australia
- Postcode validation and formatting
- Special character handling in suburb names (e.g., O'Connor)
- Duplicate suburb entries across different states

## [1.5.0] - 2025-10-01

### Added
- Admin changelog page accessible via "Child Care Subsidy > Changelog" menu
- Version history tracking in CHANGELOG.md file
- Modern, professional frontend styling with enhanced UI/UX
- Smooth animations and transitions throughout the calculator
- Gradient backgrounds on buttons and cards
- Enhanced focus states for better accessibility
- Custom styled range slider with hover effects
- Hover effects on all interactive elements
- Loading spinner with modern animation

### Changed
- Complete CSS redesign with modern design system
- Upgraded typography with system font stack
- Enhanced progress bar with larger indicators and checkmarks on completed steps
- Improved form inputs with better padding and focus states
- Redesigned buttons with gradient backgrounds and lift effects
- Enhanced child detail cards with hover animations
- Improved summary section with better visual hierarchy
- Upgraded email form styling
- Better suburb autocomplete dropdown with smooth animations
- Enhanced responsive design for mobile devices
- Increased border radius throughout for softer appearance
- Replaced borders with modern box shadows

### Fixed
- Autoloader path resolution for proper class loading
- CSS appearance property compatibility for range inputs
- Mobile responsiveness improvements

## [1.4.0] - 2025-10-01

### Added
- Multi-child CCS calculator with care-type & age-based caps
- Step progress indicator with progress line
- Location-based suburb autocomplete
- Aboriginal or Torres Strait Islander identification field
- Enrolment status options (Existing family, With another care provider, Not currently enrolled)
- Dynamic child count selection (1-5 children)
- Fortnightly day selection for each child (Week 1 & Week 2)
- Hours per day slider (4-12 hours with 0.25 step)
- Daily fee input per child
- Real-time CCS percentage calculation based on income thresholds
- Age-based hourly cap application
- Multiple period views (Weekly, Fortnightly, Monthly, Yearly)
- Individual child and combined summary views
- Email summary functionality with admin BCC
- Custom Post Type for submission tracking
- Admin settings page for policy configuration
- HubSpot form integration

### Changed
- Refactored plugin structure into organized class-based architecture
- Separated functionality into logical modules (Admin, CPT, Ajax, Frontend)
- Improved autoloader for better namespace handling

### Fixed
- Autoloader path resolution for proper class loading

## [1.3.0] - Previous Version

### Added
- Basic calculator functionality
- Income threshold calculations
- Subsidy percentage calculations

## [1.2.0] - Previous Version

### Added
- Initial plugin structure
- Basic admin settings

## [1.1.0] - Previous Version

### Added
- First working version
- Simple calculator interface

## [1.0.0] - Initial Release

### Added
- Plugin foundation
- Basic WordPress integration
