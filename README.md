# Payzen Payment gateway for WP Booking Calendar

This project enables Payzen (Caisse d'Epargne in France) Payment gateway for WP Booking Calendar (https://wpbookingcalendar.com/) Wordpress plugin.
Currently, this gateway uses only redirection to Payzen form. It does not work with IFrame or JS-based RESTful form. 

# Licence

See LICENCE file

# Installation

Copy the 'spplus' folder into your Wordpress 'wp-content/plugins/booking.bm.<version>/inc/gateways/' folder.

# Usage

In Wordpress administration panel, go into Bookings/Settings/Payments/ and in "SPPlus" tab, activate the gateway.
You will require to set the Test key and Production key (not needeed for testing, obviously) that you can get from Payzen back-office.

# Status

This gateway only works using redirection to Payzen form. IFrame and JS-based can be possible, and all files required are included in this project but are not used. You can remove them if you don't want to use them.

About JS form integration: I am not experienced enough in Wordpress plugin development, so I don't know how to integrate Javascript links in <head> tags of the page, which is apparently required. PR welcome.
