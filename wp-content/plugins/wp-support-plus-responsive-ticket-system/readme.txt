=== WP Support Plus Responsive Ticket System ===
Contributors: pradeepmakone07
License: GPL v3
Tags: ticket,support,help,helpdesk,crm,chat,contact,form,ticket system,support ticket,support desk
Requires at least: 4.0
Tested up to: 4.9
Stable tag: 9.1.2

== Description ==

= Important Notice : =

This plugin is being maintained for existing users of the plugin. New users are strongly recommended to use our latest ticketing system [SupportCandy](https://wordpress.org/plugins/supportcandy/).


== Installation ==

This plugin is almost plug and play! Just activate it and then go to WP Support Plus menu. You will learn automatically.

== Screenshots ==

1. screenshot-1.png
2. screenshot-2.png
3. screenshot-3.png
4. screenshot-4.png
5. screenshot-5.png
6. screenshot-6.png
7. screenshot-7.png
8. screenshot-8.png


== Changelog ==

= V 9.1.2 =
* Fix : HTML injection security issues fixed

= V 9.1.1 =
* Fix : Ticket created on behalf of user bug
* Fix : Private note email notification not working
* Fix : Html not save in Thank You page setting

= V 9.1.0 =
* New Setting : Enable/Disable captcha
* Fix      : Unable reply bug for Russian language

= V 9.0.9 =
* Enhahcement : Recapta for create ticket page
* New Feature : Create ticket from thread (ticket reply)
* New Feature : Do not allow customer reply if ticket status is Closed
* Enhahcement : Add custom login link
* New Feature : Add description for custom fields
* New Feature : Close ticket after x days of inactivity
* Enhahcement : Add shortcode in footer text
* Fix      : Images not displayed in email 
* Fix      : RTL not support for Persian language
* Fix      : URLs are not stored in footer
* Fix      : Attachments name does not show in russian or chinese languages
* Fix      : Showing file missing notice on support page
* Fix      : Filter does not save on IE11

= V 9.0.8 =
* Fix   : jQuery fixes

= V 9.0.7 =
* Enhancement : View Deleted Tickets. Administrator can see the deleted tickets using new filter. Deleted tickets can be restored.
* Enhancement : Ticket widget re-order. Open tickets widgets can be re-ordered from the settings. To re-order go to Support Plus > Ticket List > Open Ticket Widgets.
* Enhancement : Change lenght of Raised By string in ticket list.
* Enhancement : Show Biography of user. Staff can see biography of user. There is option added in Raised By widget in open ticket. 
* Enhancement : See all the tickets by user. Staff can see all the tickets of the user. There is option added in Raised By widget in open ticket. 
* Fix   : IP address bug fix on some servers
* Fix   : Clone ticket issue fixed
* Fix   : Knowledgebase and FAQ issue for non logged in users
* Fix   : Time format in {ticket_history} template.

= V 9.0.6 =
* Enhancement : Silent Creation option for Create Ticket (Does not send notification to customer on Create Ticket while creating ticket on behalf)
* Enhahcement : Added Translation for Ukrainian, Polish, German (Switzerland)
* New Feature : Know your customer's location using IP address (Privacy Compliant) 
* New Setting : Hide the Dashboard Tab
* New Setting : Hide the Login Screen
* New Setting : Disable Sign-in and Sign-out Buttons
* Minor Tweak : Change the length of the Subject string in the Ticket List
* Minor Tweak : Redirect to another page after Create Ticket is Success
* Fix         : Showing Wrong count on the dashboard (Fixed) 
* Fix         : Attachment does not work on some servers (Fixed) 
* Fix         : WPML compatibility for Thank You page (Fixed) 
* Fix         : File upload issue on IE11 (Fixed) 
* Fix         : Multiple Redirect after Logout on some servers (Fixed) 
* Fix         : Notification on Close Ticket was not working (Fixed) 
* Fix         : Ticket duplications (Fixed) 

= V 9.0.5 =
* Fix   : Saved filters not working
* Fix   : User search not working

= V 9.0.4 =
* Tweak : Support Plus is now WPML compatible!
* Tweak : Public Ticket feature added
* Tweak : RTL support for support page
* Fix   : Missing strings for translation are added
* Fix   : Create custom field issue fixed
* Fix   : Agent assign on multisite bug fixed

= V 9.0.3 =
* Fix : Vulnerability issues fixed
* Fix : Forgot Password link does not work fixed
* Fix : Conflict with cache most of cache plugin
* Fix : Text file download issue fixed
* Fix : Wrong dashboard ticket count bug fixed
* Fix : Guest login-404 error fixed
* Tweak : Translation for German,Czech,Croatian,Persian,Swedish,Russian

= V 9.0.2 =
* Tweak : Shortcode available for theme integration interface. Now theme integration interface will print out via shortcode.
* Tweak : Translation added for French(France), Solvenian, Turkish, Portuguese(Portugal), Arabic
* Fix   : IE 11 compatibility issues fixed.

= V 9.0.1 =
* Tweak : Theme integration setting added. Go to gerneral setting to see this.
* Tweak : Show/Hide filters sidebar option added for ticket list
* Tweak : Custom css setting to customize support page 
* Tweak : User support page visibility setting
* Tweak : Default login module setting added. Now you can disable support plus default login and use your website default login interface. 
* Tweak : Setting to enable/disable "Powered By" text
* Tweak : Translation for Dutch,Italian,Japanese,Portuguese (Brazil),Spanish (Spain) added
* Fix   : Spelling mistakes corrected

= V 9.0.0 =
* Tweak : Design changes for overall support functionality. It will not use website theme. Added own designes for support page.
* Tweak : Improved filters. Apply multiple filters at a time. Save filters.
* Tweak : Set default filters for agents and users
* Tweak : Ticket Status After Agent Reply setting added
* Tweak : Allow Support Staff to read all ticket setting added
* Tweak : Set Default Priority setting
* Tweak : Set Supervisors to Categories
* Tweak : Custom Support Page Menu Setting 
* Tweak : Setting to make support ticket form fields full or half width
* Tweak : New Custom Field types Attachment, URL and Email added
* Tweak : new template tag {ticket_history} added
* Note  : Email Notifications feature is removed
* Note  : Agent Assign to category feature is removed
* Note  : Canned Reply feature is removed
* Note  : FAQ'S feature is removed

= V 8.0.8 =
* Fix : Urgent security bug fix

= V 8.0.7 =
* Tweak : Setting to disable edit ticket information for agents
* Tweak : Change User list increased t0 20 results for search on create ticket on behalf of another user in backend
* Tweak : System Information file download for us to attach in ticket for better support
* Fix : jQuery UI loading issue
* Fix : Statistic and assigned to filter wrong results
* Fix : Status change to default if user reply ticket
* Fix : Filters not available for hidden ticket status for front-tend
* Fix : Charset added to table creation query
* Fix : Repeating headers in Non-Accordion view

= V 8.0.6 =
* Fix: Avatars not working on front-end if email is disabled for thread in front-end display settings.
* Fix: Priority issue for email piping created tickets fixed.

= V 8.0.5 =
* Fix: Canned reply not working on multisite blogs
* Fix: Extra HTML append to .txt files for attachments
* Fix: Documentation link changed

= V 8.0.4 =
* Fix: Urgent fix for Email Piping Add-on

= V 8.0.3 =
* Tweak: WPML Support for Email Templates
* Tweak: Setting to hide multiple statuses from frontend
* Tweak: Dashboard Tab added for [wp_support_plus] shortcode showing stats for logged in user and for future use applications
* Fix: Translation improved
* Fix: Can't Edit Links while editing reply in backend
* Fix: Captcha checkbox disappear for some theme like Divi
* Fix: Incorrect spelling for email template
* Small bug fixes

= V 8.0.2 =
* Fix: Canned reply urgent bug fix
* Fix: Ticket list not showing on some servers. MySQL table index repair for WP Support Plus

= V 8.0.1 =
* Tweak: Deleting tickets functionality improved. Now tickets are not actually deleted but will be in deleted status for restore.
* Tweak: Canned reply insert text at cursor position instead at the end.
* Tweak: Edit thread improved
* Small bug fixes

= V 8.0.0 =
* Pro version migration to add-on structure (applicable to all our customers using old pro version method)
* Fix: Auto update add-ons issue fixed

= V 7.1.5 =
* Tweak: inbuilt nocaptcha added
* Tweak: ticket link functionality improved. Now you can get ticket link from site anytime if you have ticket ID and correct email address.
* Fix: Ticket create improved and fixed few bugs
* Fix: Ticket reply improved and fixed few bugs
* Fix: Few Urgent Security bug fixes
* Google noCaptcha removed as inbuild anti spam functionality added like google noCaptcha
* Facebook login removed due to conflict with some social login plugins. You can use any free social login login plugin, that will recongnizable by Support Plus.

= V 7.1.4 =
* Fix: Guest tickets not creating for some installation

= V 7.1.3 =
* Tweak: Upload progress bar color picker setting in advanced setings
* Tweak: Setting to try opening attachment in browser first (not default, available in advanced setting)
* Tweak: Delete thread option for admin only in backend view
* Tweak: Change raised by log
* Tweak: Icon indicating attachment available in thread
* Fix: Email notification issues fixed
* Fix: Filter on front-end not being remembered
* Fix: Attachment download issue on some installation because site url is different, used home_url() instead site_url()
* Fix: Edit custom fields having unrelated fields showing to edit
* Fix: Custom Filter Front End not showing buttons for some conditions

= V 7.1.2 =
* Tweak: Multiple file uploads allowed in attachment
* Tweak: Ajax upload with progress bar for attachment
* Tweak: Show full subject in title on ticket list when hour cursor on it
* Fix  : Attachment not working if file name has underscore
* Fix  : Attachment download file name prefix added
* Fix  : Custom status do not show selected in reply ticket form if it is having capital second word
* Fix  : Scroll going top everytime for ticket event
* Fix  : Wrong result for assigned to filter, e.g. if search for assigned agent of user id 2 also return results for user id 22
* Fix  : Update time not visible in ticket thread

= V 7.1.1 =
* Tweak: Variable custom fields feature (custom fields only visible to support staff for ticket)
* Tweak: Change Raised By made available to front end
* Tweak: Edit Custom Fields made available to front-end
* Tweak: Edit Custom Fields available to agents as well
* Tweak: Ticket log added actions change status, priority, category
* Tweak: Ticket filter created by to front end
* Fix: Raised By showing to customer ticket list
* Fix: Ticket URL made HTML Link, it was just text and could not loaded as link in few browsers
* Fix: Change Raised By bug fix for guest user
* Fix: Front end display fix for status change

= V 7.1.0 =
* Tweak: Ticket assign history shown in open ticket.
* Fix: Security fix for other agent or user can hack to other tickets if they know ticket number
* Fix: Showing custom fields assigned to other category in open ticket. This fix will be applicable for new tickets after update.
* Fix: Custom Field with digit options not go through default filter.
* Fix: Custom Field with digit options not filter for correct values. This fix will be applicable for new tickets after update.

= V 7.0.9 =
* Tweak: Translation Fix, available to translate in translate.wordpress.org
* Tweak: Front End display settings to choose action button colors, custom labels, hide or show actions etc.
* Tweak: Setting to automatic redirect guest user when ticket submitted in advanced setting
* Tweak: Setting to stay in open ticket after taken any action (need to activate from advanced settings)
* Tweak: Raised By to front end ticket list (need to activate from ticket list settings)
* Tweak: Ignore email address setting in email notification, these will be ignored while sending any emails
* Fix: redeclare Encrypt() error
* Fix: Disable datepicker if no date custom field in create ticket form
* Fix: Guest thank you page link issue
* Fix: attachment for ticket not send with Assign agent email notification
* Fix: Disable link reply when current ticket status set to hide for front end via advanced settings
* Fix: Disable signature in private note thread
* Fix: Hide more actions if there is no action available in it for front end
* Fix: Notices & warnings in debugging
* Fix: Increase custom field label character limit to varchar(200)
* Fix: Skype Call & Chat in new pop-up

= V 7.0.8 =
* Canned Reply button visibility fixed for backend
* Email Template agent created added to know who created ticket on behalf of customer
* Subject default value if disabled made editable
* Setting to register guest user in general settings
* Add-ons introduced (no more pro version)

= V 7.0.7 =
* Leave warning if try to leave page while typing description
* IMAP pipe empty message bug fix (pro only)
* Front end filter not saving bug fix
* Divi theme conflict on statistics page fixed
* Assign agent email always sending to administrator even disabled fixed
* Some Spelling mistakes fixed
* Spell-check for CKEditor enabled
* Added ticket created time templete for email templates

= V 7.0.6 =
* Urgent bug fixes

= V 7.0.5 =
* Added Date custom field with datepicker
* Added Conditional Custom Fields feature by Category selection
* Added setting to choose default tab in wp_support_plus shortcode in Advanced Settings
* Added canned reply for backend create ticket
* Attachment of any file type except php, exe files allowed for ticket
* Close Ticket button available, need to activate from general settings
* IMAP bug fix for echoing blank message
* change user in backend create ticket form made mobile compatible
* Some bug fixes

= V 7.0.4 =
* Few urgent bug fix: 404 error after create ticket, default characterset for cpanel email piping

= V 7.0.3 =
* Ticket id can set sequential(default) or random for create new ticket, setting given under advanced settings
* Support Button, Support Panel image change setting given in advanced settings
* Enable or Disable Subject from create ticket form, setting given under Re-Order Fields
* All Drop down first option made blank in create new ticket form in order to force customer to choose if field is required
* Clone Ticket feature added
* First FAQ category made editable
* Canned Reply pagination added
* Ticket ID prefix setting given in advanced settings
* Bulk ticket action bug fix
* Few CSS changes

= V 7.0.2 =
* IMAP manual cron removed and used default WP Cron
* Export ticket to CSV
* Customizable Email templates for Change Status, Assign Agent and Delete Ticket as like Create Ticket and Reply Ticket
* Ticket URL with encryption used for ticket id, this will allow login or guest user to see his ticket or can reply ticket without need to login
* Allowed MS Office documents file attachments via email piping (Word, Excel, Power Point)
* Setting to enable or disable CKEditor for description on front-end
* Setting to Support Plus admin bar link optional
* Setting to make welcome & logout optional for front-end
* Few Bug Fixes

= V 7.0.1 =
* Logout link for logged in users for front-end
* Some important bug fixes

= V 7.0.0 =
* IMAP Support for email piping
* Agents can share canned reply with each other
* Canned reply enabled for front-end for Agents
* Remember filter for front-end user
* Support Plus Menu text in back-end made editable
* Setting added to to hide selected status tickets in 'All' filter for back-end
* Admin or allowed role can change 'Raised By' from back-end of a ticket
* Some bug fixes

= V 6.1.9 =
* Two new shortcodes introduced [wp_support_plus_all_tickets] and [wp_support_plus_create_ticket] for ticket list and create ticket form respectively
* Canned reply feature added for backend
* Setting for Reply form position(top/bottom) added
* Setting for Accordion view enable/disable added
* Setting for hide selected status ticket from front end added (e.g. hide closed tickets from front-end)
* CKEditor update version 4.5.4
* Facebook login bug fix
* Create ticket success email bug fix
* Setting given to login module to replace by default WP login link
* Shortcode page builder compatibility given (See in plugin advanced settings)
* Some translation issues fixed

= V 6.1.8 =
* User avatar support (buddypress profile picture etc.)
* Few bug fixes

= V 6.1.7 =
* Custom Priority small bug fixed

= V 6.1.6 =
* Added ability to rename custom and default status
* Added ability to rename custom and default priority
* Added Add Note facility for front-end
* Bug fixed: custom status and Custom priority not adding

= V 6.1.5 =
* Added Settings to replace word "Ticket"
* Added Settings to replace default form labels (e.g. Subject, Description, etc.)
* Add note made private to support staff (hidden from customer view)
* Added support for HTML reply in email piping
* Few Bug fixes

= V 6.1.4 =
* Add create time, update time table list field with date and time to show using custom format
* Status and Priority re-ordering settings
* Add link to WP Support Plus to WordPress Admin Bar
* Allow additional recipients to ticket replies (CC and BCC)
* Create a new ticket from an existing thread
* Add a note without notification to a ticket
* Change ticket status without notification
* Add accordion to threads
* Improved registered user search
* Create ticket without notification from back-end on behalf of user or guest
* Search by Registered user name not work in backend bug fix
* Filter with priority not working in back-end bug fix
* Single or doble quote in Custom CSS textarea problem bug fix
* Email Template editor bug fixed for links and images
* multiple default statuses showing bug fixed
* issues with default status and priority text translations fixed

= V 6.1.3 =
* ability to enable, disable, change order of create ticket form except name, email, subject and discription
* ability to enable, disable, change order of ticket listing table for front-end
* ability to enable, disable, change order of ticket listing table for back-end
* ability to enable, disable ticket filter for front-end including filter for custom fields
* ability to create custom priority
* ability to set color for default status as well as custom status
* ability to set color for default priority as well as custom priority
* ability to sort ticket listing table on back-end on table header click
* added "All Active" in Status Filter that will get all tickets except closed status
* ability to set default 'reply to' email address for case where reply email must send to email other than send
* added thread date time along with 'time ago' for each thread
* email piping nl2br bug fixed
* automatically set mailPipe.php permissions on install or update
* disable front end new ticket submission for case if anyone want to create new ticket only via email piping and later he can see his ticket after login or register with same email
* handle email attachments for email piping (supported file type-jpg, png, gif, zip, pdf)
* added Email Templates with ability to choose applicable users for sending email for 'create new ticket' and 'reply ticket'
* added ability to choose users for sending email for 'change status', 'assign agent', 'delete ticket'
* added ability to set subject character length in ticket listing table for both front-end and back-end
* bug fixed- Search by Registered user name not work in backend
* bug fixed- Statistics page not showing custom statuses
* bug fixed- Bulk delete for agent not working if given athority to delete ticket

= V 6.1.2 =
* Required fields bug fixed introduced in 6.1.1
* Some spell correction

= V 6.1.1 =
* Added 3 more custom field type- Checkbox, Radio Button, Textarea
* Hide other plugin update bug fix for automatic update
* PEAR.php file included for email piping and no plain/text body found for email piping bug fixed
* Anti Spam technique for login
* Polish translation by Slawek Gdak(http://www.blog.gdaq.pl)

= V 6.0 =
* Automatic Update in dashboard for pro version
* Login area for non logged-in user in place of login link
* Registration link for guest to create new account if allowed by Wordpress installation
* New filter for back-end Support Plus having ability to remember filter for individual user until he reset/change
* New filter for back-end can also filter tickets by custom fields
* If guest create account(sign-up), he can see his old tickets he created as guest using same email address
* Email piping made even better
* Email piping multisite compatibility added (can use 1 blog for piping)
* Bulk Change Status, Category, Priority of ticket from back-end
* Bulk Assign ticket to Agents from back-end
* Bulk Delete tickets from back-end
* Changed HTML editor
* Agent Signature made HTML friendly (used editor)
* Traditional Chinese translation by Tiffany Lee
* Few bug fixes

= V 5.5 =
* Added Google No CAPTCHA reCAPTCHA for guest tickets
* FAQ shown to guest fixed
* Given setting to automatically change ticket status to status selected (gives more clearity to reply remaining tickets)
* Single ticket/category can be assigned to multiple agents 
* Many bug fixes

= V 5.4 =
* Admin can allow guest ticket file attachment
* Custom Status (addition to open, pending and closed)
* Custom message for guest after submitting ticket
* Many bug fixes
* Russian translation by Vladimir Kudashev

= V 5.3 =
* Agent can support from front-end
* Administrator can set whether or not agent can assign ticket to other.
* Administrator can set whether or not agent can delete ticket.
* Custom field bug fix.
* Administrator/supervisor can edit ticket fields of any ticket
* Create ticket mail sent to creator.
* Ability to create ticket for non-regitered user from back-end
* Ability to add custom css

= V 5.2 =
* Ability to add public/private ticket
* Added Drop-down custom field
* Added FAQ functionality
* Ability to hide login link for guest user on front-end
* Jquery trigger bug fix
* chdir bug fix for email piping
* Divi Tamplete compatibility bug fix
* Brazilian Translation by Daniel Silveira
* Persian Translation by Behrooz Sedqi (www.apktops.ir)
* Arabic Translation by Arab façade
* German Translation by Michael L. Jaegers
* Romania translation by Mihai

= V 5.1 =
* Added new ID based and created by search filters to backend ticket list
* Added filters for front end tickets
* Added an option for making custom fields optional/required
* Pre-assign Agent to category
* Bug fix for email piping
* Bug fix for back-end filter
* Swedish translation by rickeclaesson

= V 5.0 =
* Email piping feature added
* Hide Assigned task to an Agent from other agent
* Restrict front-end ticket to specific user role by giving setting to administrator to choose them
* Delete all attached files to ticket from server if we delete ticket in order to save server space
* Email Notifications for multiple administrator and agents email notifications as per the ticket assigned.
* Setting to select default category for new tickets
* Traslation text update

= V 4.4 =
* Custom Field Settings added
* Traslation text update
* Translation Serbian by Cherry

= V 4.3 =
* Role Management Settings added
* Now add agent/supervisor capability to your existing user roles
* Translation Portuguese by creativeangels

= V 4.2 =
* Multiple Vulnerabilities fix added
* SQL Injection security
* Full Path desclosure security
* Directory traversal security
* Brocken authentication security
* Removed downloadAttachment.php file for security reasons

= V 4.1 =
* Javascript Injection security bug fix
* Shift front-end styles to header
* Change front-end width for 100% of the page
* In Plugin Support added
* website changed to http://pradeepmakone.com/wpsupportplus/ for security reasons.

= V 4.0 =
* ticket submission on behalf of users added
* attachment sent with email added
* shortcode bug fixed
* turn off slider only settings added
* removed third party images and keep in plugin folder
* link in mail changed to support page instead of site url

= V 3.9 =
* Guest Ticket Form added
* Custom Sliding Panel Menu feature added
* Bug fixes

= V 3.7 =
* HTML Editing of tickets
* Hide "OR" login via facebook if not app details present
* Translation Dutch by Ben
* Translation French by X-Raym
* Translation Spanish by 3ways

= V 3.6 =
* plugin is now translation ready.

= V 3.5 =
* Skype Click to Chat added
* Skype Click to Call added
* Fix for secure gravtar images

= V 3.4 =
* fix fancybox width and height for skype call and chat

= V 3.3 =
* minor bug fixes

= V 3.2 =
* Skype chat
* Skype call

* Display phone number

= V 3.1 =
* Facebook login for guest user to submit ticket

= V 2.3 =
* Minor bug fix

= V 2.2 =
* Bug fix: Quotes in mail Every apostrophe ' is replace by \' in ticket title in mails and ticket list (but not ticket itself) 

= V 2.1 =
* Added setting of set email from and from name in support for Jason
* This setting is available for our free and paid both versions

= V 2.0 =
* supervisor user roll added
* ticket can be assigned to agent by supervisor or admin
* assign to filter added
* agent can filter ticket assigned to him
* ticket assignment mail notification
* statistics menu for management overview
* minor bug fixes

= V 1.0 =
* Initial release.
