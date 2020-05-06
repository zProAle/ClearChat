<?php
// check madaline
if (!file_exists('madeline.php')) {
    copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
}
include 'madeline.php';

// start session
$MadelineProto = new \danog\MadelineProto\API('session.madeline');
$MadelineProto->async(true);
$MadelineProto->loop(function () use ($MadelineProto) {
	
	$times = 2629743; // time of the last message in second ex. 2629743 is a month
	
    yield $MadelineProto->start();

    $me = yield $MadelineProto->getSelf();

    $MadelineProto->logger($me);
	
	$dialogs = yield $MadelineProto->getDialogs(); // get all chat

	foreach ($dialogs as $peer) {
		try{
			$messages = yield $MadelineProto->messages->getHistory([ // get last message from all chat
				'peer' => $peer, 
				'offset_id' => 0, 
				'offset_date' => 0, 
				'add_offset' => 0,
				'limit' => 1,
				'max_id' => 999, 
				'min_id' => 0, 
			]);
			
			$messages = $messages['messages'];
			
			foreach(array_reverse($messages) as $i => $message){ 
				$datacheck = time() - $message['date'];
				$type = $message['to_id']['_'];
			}
			if($type != "peerChannel"){ // check that it is not a channel
				if($datacheck >= $times){ // check a ultimate messages
					$test = yield $MadelineProto->messages->deleteHistory(['just_clear' => true, 'revoke' => true, 'peer' => $peer, 'max_id' => 99999,]);
					echo "I delete chat! \n ";
					sleep(2);
				} else {
					echo "This chat is recent I don't delete it! \n";
					sleep(2);
				}
			} else {
				echo "Channel detected, i skip this! \n";
				sleep(1);
			}
		} catch (Exception $e) {
			echo 'Exception error: ',  $e->getMessage(), "\n";
		}
	}

});
?>
