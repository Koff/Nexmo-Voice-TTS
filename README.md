# Nexmo - TTS 

This small class was created by modyfing the [nexmo-php-prawnsalad](https://github.com/Nexmo/nexmo-php-prawnsalad) code hosted on GitHub.

You would need an `API_KEY` and `API_SECRET` from Nexmo as well as some credit in your account. If you don't have an account yet, open one! It's free and it comes with some free credits.

# Usage

Open `example.php` and modify the line 11 with your credentials:

`$nexmo_voice = new NexmoVoice('aaaaa', 'bbbbb');`

Where `aaaaa` is your Nexmo API Key and `bbbbb` is your Nexmo API secret. Also modify in line 14 the following:


`$info = $nexmo_voice->sendVoice( '111111111', '222222222', 'Hello, World, from Nexmo','en-us', 'male', 1);`

Where `1111111111` is your desitination phone number `222222222` is the number that will be displayed as caller-id.


After this upload your files to your server and execute example.php, you should received a call in your phone that will play the message *Hello, World, from Nexmo*

Refer to the class for more information and feel free to use the code as an starting point for your applications

# Note

This library is not offitially supported by Nexmo and they might not even know of their existence. The software is provided as it is and without warranties. New changes and features will be added in a best effort basis but they shouldn't be exected and there are not guarantees. Use at your own risk.