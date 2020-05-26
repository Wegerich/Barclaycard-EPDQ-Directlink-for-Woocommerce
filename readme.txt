
== Changelog ==


= v2.1.10 - 07/10/19 =
* New           - Code to reject Amex cards (currently commented out)
* New           - Add 3DSecure Card security check outcome to note in order comment
* New           - Count failed payment attempts in logs
* Fix           - Tidying of notes in order comments.
* Change        - Do not change status of emails when refund is processed, in case of partial refund. 


= v2.1.4 - 02/10/19 =
* New           - New fallback warning for missing PAYID from order when trying to refund.
* New           - Use failed order data/reason to show to customer and log as an order note.
* New           - New admin URL constant.
* Fix           - Get correct admin URL when admin is not in root.
* Change        - Catch and use any if not all data send back from ePDQ when failing order.


= v2.1.2 - 16/09/19 =
* New           - Activated a new affiliate program for plugin, clients can sign up within the plugin welcome screen and earn commission. 
* New           - New affiliate section added to welcome screen. 
* New           - New debug logging for AG pre-checks before sending data to ePDQ (Catch and log customer error before submission to ePDQ). 
* Tweak         - Changed location of AG3DSForms and cleanup.
* Fix           - Fixed issue with SHASIGN.
* Fix           - Fixed issue with wrong AG image for plugin.
* Support       - Support for Sequential Order Numbers Pro plugin.
* Change        - Added a "-" between customer name sent to ePDQ.

= v2.1.0 - 02/09/19 =
* Feature       - The plugin is now 3DS 2.0 and PSD2 compatible. 
* Tweak         - Changed 3D secure test card in test checkout to 3D secure v2 card number to test the new PSD2 features.
* Fix           - Fixed issue some users had with debug mode and warning levels.
* Fix           - Catch WP error on wp_remote_post().
* Change        - Change back to if/else for Frictionless flow from switch/case.
* New           - New handling of order data for Frictionless transactions.

= v2.0.2 - 01/07/19 =
* Enhancement   - Improvements to debug mode & log logic, logs are not part of WooCommerce log system.
* Feature       - Users can now enrol to become a beta tester of new versions of plugin.
* Tweak         - New define_constants() to help loading plugin files.
* Update        - Update to FS SDK 

= v2.0.1 - 20/05/19 =
* Dev           - New fallback for invoice orders (WC require user to be signed in to make payment due to sessions).
* Dev           - New notice shown to users not signed in making invoice payment.

= v2.0.0 - 11/05/19 =
* Tweak         - Limit characters in customer fields (ePDQ has a max limit).
* Tweak         - Change how 3D secure is shown to customer (default or popup display).
* Tweak         - Update API call to use wp_remote_post(). 
* Tweak         - PHP 7.3 compatibility improvements.
* Dev           - Added new folder for better structure for plugin classes. 
* Dev           - New helper, settings, subscriptions and crypt classes. 
* Enhancement   - Improvements to RIPEMD encryption.
* Enhancement   - Improvements to transaction order data.
* Enhancement   - New error logging for not setup correctly.
* Enhancement   - New order note with 3D secure checks data only.
* Checking      - Checking support for new WooCommerce version 3.6+ (working with latest version).
* Change        - Changed location of debug file location.
* Removed       - Removed XMLtoArray() as this is no longer needed. 
* Fix           - Fixed bug with some users unable to process refunds. 

= v1.5.4 - 12/04/19 =
* Tweak     - Remove apostrophe from customer last name.
* Tweak     - Changed ag_log function to static method.


= v1.5.2 - 01/04/19 =
* Tweak     - Convert all accent customer name characters to ASCII characters.
* New       - New debug feature to log any errors in a error log file (Helps to find any issues with setup).
* Update    - Update to AG core classes.

= v1.5.1 - 18/03/19 =
* Fix     - WP nonce issue was replaced with custom security hash. 

= v1.5.0 - 06/03/19 =
* Feature - Implemented both SHA in and out to the plugin.
* Feature - Implemented second level of security using nonces, this is on by default. 
* Feature - Implementation of update early warning feature, able to display warnings about updates and security patches.

= v1.4.9 - 27/02/19 =
* Tweak - Text changes, typo's fixed.
* Change - Tweak for product titles with ampersands to display in the ePDQ back office 
* Change - Changes to required fields for ePDQ 
* Enhancement - Improvements to AG core classes 
* Update - Update to FS SDK 

= v1.4.6 - 30/01/19 =
* Feature - Form validation before submit improvements. 
* Feature  - New algorithm (Luhn) to check card numbers before submit. 
* Enhancement  - Performance improvements (Clean up) 
* Enhancement  - Improvements to welcome screen 
* New  - Two new functions, one for order notes and the other for order meta data. 

= v1.4.0 - 22/01/19 =
* Dev  - PHP compatibility issues fixed. 
* Feature  - Multicurrency support (the use of an multicurrency ePDQ account is needed) 
* Checking  - Checking support for WooCommerce and WordPress version 5.0+ (working with latest versions). 
* Update - Update to FS SDK 

= v1.3.7 - 20/11/18 =
* Dev  - Plugin has been rewritten. 
* Change  - Brand new welcome page with help and info for new/old users. 
* Enhancement  - Improvements to refund error notices. 
* Checking  - Checking support for new WooCommerce version 3.5+ (working with latest version). 

= 1.3.1 - 2018-09-23 =
* Feature - Adding of two new buttons at checkout while in test mode. These will prefil in the card details for testing.
* Feature - Mobile checkout is enhanced, offering automated card formatting and numerical (tel type) fields for easier input.
* Fix - Change how failed order work.
* Tweak - Text changes, typo's fixed.
* Tweak - Adding of JCB logo to the select field.
* Tweak - New warnings for 3D secure issues.


= 1.2.0 - 2018-08-30 =
* Feature - New, Whats this next to CVC to give end user tool tip on what CVC is.
* Tweak - Changed URL endpoint for EXCEPTIONURL.
* Fix - PHP standards issues.
