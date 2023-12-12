<?php
if (!file_exists('madeline.php')) {
    copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
}
include 'madeline.php';

// Your account session, follow this guide to get it https://docs.madelineproto.xyz/docs/LOGIN.html
$MadelineProto = new \danog\MadelineProto\API('session.madeline');
$MadelineProto->start();

// Seconds to delay between each message
$times = 2629743; //1 month

/* Get all chat */
$dialogs = $MadelineProto->getDialogs();
foreach ($dialogs as $peer) {
    if($peer['_'] === "peerUser"){ // check that it is not a channel or group
        // Get last message in chat for check time
        $messages_Messages = $MadelineProto->messages->getHistory(['peer' => $peer, 'offset_id' => 0, 'offset_date' => 0, 'add_offset' => 0, 'limit' => 1, 'max_id' => 0, 'min_id' => 0, 'hash' => 0 ]);
        $messages = $messages_Messages['messages'];
        foreach(array_reverse($messages) as $i => $message){ 
            $datacheck = time() - $message['date'];
        }

        // Check if the last message is older than the time set
        if($datacheck >= $times){
            $delete = $MadelineProto->messages->deleteHistory(peer: $peer['user_id'], just_clear: true, revoke: true);
            $MadelineProto->logger("Deleted chat: ".$peer['user_id']);
            sleep(1);
        } else {
            $MadelineProto->logger("Not deleted chat because it is not old enough: ".$peer['user_id']);
        }
    } else {
        $MadelineProto->logger("Not deleted channel or group");
    }
}
$MadelineProto->echo('OK, done!');