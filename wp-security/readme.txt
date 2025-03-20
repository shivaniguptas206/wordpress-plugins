=== WP Security ===
Contributors: shivaniguptas206  
Tags: security, restrict access, admin security  
Requires at least: 5.6  
Tested up to: 6.4  
Stable tag: 1.0  
License: GPL-2.0  
License URI: https://www.gnu.org/licenses/gpl-2.0.html  

Restrict unauthorized theme & plugin editing by requiring a security token. 

== Description ==
🔒 **WP Security** prevents unauthorized theme and plugin modifications. Admins must enter a **security token** to gain access.  

⚡ **Key Features:**  
✅ Restricts theme & plugin editing unless a token is entered  
✅ Temporary access (30 minutes)  
✅ AJAX-based token verification  
✅ Custom admin settings page  
✅ Secure nonce validation  

== Installation ==
1. Upload `wp-security.zip` via **Plugins → Add New → Upload Plugin**.
2. Activate the plugin in **Plugins → Installed Plugins**.
3. Navigate to **Security Token** in the admin menu.
4. Enter the security token to unlock theme/plugin editing.

== Frequently Asked Questions ==

= What happens if I forget the token? =  
The token must be manually updated in the plugin's PHP file.  

= How long does access last? =  
Once verified, access remains for **30 minutes**, after which editing is disabled again.  

= Is this compatible with WordPress Multisite? =  
No, the plugin currently supports **single-site installations** only.

== Changelog ==
= 1.0 =
* Initial release 🎉

== Screenshots ==
1. **Admin Token Page** – Enter security token in the admin panel.
2. **Token Popup** – Prompt appears when editing is restricted.

== License ==
This plugin is licensed under **GPL-2.0**.  
