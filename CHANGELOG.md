# Changelog

All notable changes to this project will be documented in this file.
 
## [1.4.0] - 2026-04-24
 
### Added
- **Advanced Payment System**:
    - New `paid_amount` and `due_amount` tracking for all orders.
    - Added `partial` status to Order status workflow.
    - Automatic data synchronization for historical orders (Completed orders marked as fully paid).
    - Payment status indicators in Order management and POS.
    - Partial payment support in Checkout and Admin Order edits.
 
### Improved
- Order PDF and Print templates now include Payment Summary (Paid, Due, Total).
- POS interface updated to handle advanced payment tracking.
 
---


## [1.3.0] - 2026-04-04

### Added
- **Social Media**: New TikTok profile field in Admin Settings.
- **Sitemap Management**: 
    - Dedicated Sitemap Indexing tab in Backend Settings.
    - View current sitemap status and URL.
    - Manual "Regenerate Now" button for instant sitemap updates.
    - Frontend/Icon integration for TikTok in footer and email templates.
    - Dynamic SEO Meta tags for Homepage (Title, Description, Keywords).

### Improved
- Sitemap generation logic now includes all Products, Categories, Solutions, and Dynamic Pages.
- Daily automated sitemap generation via Cron.

---

## [1.2.1] - 2026-02-01
### Fixed
- General bug fixes and UI improvements.
