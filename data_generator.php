<?php

if (!file_exists('madeline.php')) {
    copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
}
include 'madeline.php';

$MadelineProto = new \danog\MadelineProto\API('session.madeline');
$MadelineProto->start();

$me = $MadelineProto->get_self();
\danog\MadelineProto\Logger::log($me);

$channel = '@anti_reid_msk';
$offset_id = 0;
$limit = 50;

$messages = $MadelineProto->messages->getHistory([
    'peer' => $channel, 
    'offset_id' => $offset_id, 
    'offset_date' => 0, 
    'add_offset' => 0, 
    'limit' => $limit, 
    'max_id' => 0, 
    'min_id' => 0,
    'hash' => 0
]);

$messagesFiltered = [];
foreach ($messages['messages'] as $k => $v) {
    if (array_key_exists('media', $v)) {
        if (array_key_exists('geo', $v['media'])) {
            $msg = $v['message'];
            if ((strlen(trim($msg)) == 0) && $k > 0) {
                // trying to find at the next pos
                $tmp = $messages['messages'][$k-1];
                if ($tmp['from_id'] == $v['from_id']) {
                    $msg = $tmp['message'];
                }
            }
            array_push($messagesFiltered, [
                'message' => $msg,
                'date' => $v['date'],
                'long' => $v['media']['geo']['long'],
                'lat' => $v['media']['geo']['lat']
            ]);
        }
    }
}

$fp = fopen('points_dump.json', 'w');
fwrite($fp, json_encode($messagesFiltered));
fclose($fp);
