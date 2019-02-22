#jaak-php API Library

### Scope & Mission
This PHP library is intended to work within jaak.js frontend interface.

The main functionality of this library is to register frontend devices
(which run jaak.js or other frontend library).

The side feature is metadata mining in order to provide to this backend's
clients a rich experience such: albums grouping and classification,
statistics, new releases etc. etc., unloading the client from this
"supply-chain" work. In this way you can off-load some code from
your clients implementation, keeping the central logic in your backend.


### JAAK App Keys and Device Keys
* This library help you only store App Keys
* Never ever store Device Keys, *never*

### Device Registration
Your backend should ideally listens for a */registerDevice [POST]* request
coming from your users. When this happens you should make sure the user
that made the request is fully authenticated on your system, and only
after you are sure you leverage this library methods to register the
user's device.