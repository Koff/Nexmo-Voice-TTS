<?php
    include ( "NexmoVoice.php" );

	/**
	 * To send a voice message.
	 * Refer to documentation https://docs.nexmo.com/index.php/voice-api/text-to-speech
	 *
	 */

	// Step 1: Declare new NexmoVoice object.
	$nexmo_voice = new NexmoVoice('', '');
	
	// Step 2: Use sendVoice( $to, $from, $message, $lg (optional)[en-us], $voice(optional)[female], $repeat(optional)[1], $drop_if_machine(optional)[0], $callback(optional), $callback_method[GET]) method to send a voice message. 
	$info = $nexmo_voice->sendVoice( '', '', 'Hello, World, from Nexmo','en-us', 'male', 1);

	// Step 3: Display an overview of the message
	echo $nexmo_voice->displayOverview($info);

	// Done!

?>