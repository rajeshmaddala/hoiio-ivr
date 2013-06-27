<?php
    ////////////////////////////////////////////////////////////////////////////// 
    // Busiassistant: Helps you to connect to required people when you are busy.
    //                Also has the option of taking an sms from them.
    //////////////////////////////////////////////////////////////////////////////

    // Configuration file
    include 'config.php';

    // Setup Hoiio SDK
    require 'hoiio-php/Services/HoiioService.php';
    $hoiio = new HoiioService($HOIIO_APP_ID, $HOIIO_ACCESS_TOKEN);
    
    // Logging all Hoiio notifications
    $post_body = file_get_contents('php://input');
    appendToNotificationFile(date("[Y-m-d H:i:s] ") . $post_body);

    // Checking the Call state
    $call_state = $_POST["call_state"];
    if ($call_state == "ended") {
        // Log the call
        $call_record = ">> " . $_POST["from"]. " to " . $_POST["to"] . " for " . $_POST["duration"] . " min [" . $_POST["date"] . "]. Cost: " . $_POST["debit"] . " " . $_POST["currency"];
        appendToCallRecordFile($call_record);
        return;
    }
    
    // Main functionality
    $app_state = $_POST["app_state"];
    switch ($app_state) {
        case NULL:
            // State: A call comes in
            $notify = $hoiio->parseIVRNotify($_POST);
            $session = $notify->getSession();
            $text = $TEXT_WELCOME_MESSAGE . formDirectoryText($directory);
            // Gather for a single digit. Repeat 3 times max.
            $hoiio->ivrGather($session, $THIS_SERVER_URL . '?app_state=gather', $text, 1, 10, 3);
            break;
                      
        case 'gather':
            // State: User has pressed a key
            $notify = $hoiio->parseIVRNotify($_POST);
            $session = $notify->getSession();
            $key = $notify->getDigits();
            $transfer_to = $directory[$key];
            switch ($transfer_to[0]) {
                case NULL:
                    // Invalid key. Retry gather
                    $text = $TEXT_INVALID_KEY . ' ' . formDirectoryText($directory);
                    $hoiio->ivrGather($session, $THIS_SERVER_URL . '?app_state=gather', $text, 1, 10, 3);
                    break;
                    
                case 'SMSALERT':
                    // Send an SMS Alert and hangup
                    $hoiio->sms($MY_MOBILE_NUMBER, 'You have received a call from ' . $_POST['from'], $SMS_SENDER_NAME);
                    $hoiio->ivrHangup($session, $THIS_SERVER_URL . '?app_state=hangup', $TEXT_SMS_ALERT_SENT);
                    break;
                    
                default:
                    // Transfer call
                    $hoiio->ivrTransfer($session, $transfer_to[0], $THIS_SERVER_URL . '?app_state=transfer', $TEXT_TRANSFERRING, '', '', 'continue');
                    break;
            }
            break;
            
        case 'transfer':
            // State: Transferring
            if ($_POST['transfer_status'] != 'answered') {
                $notify = $hoiio->parseIVRNotify($_POST);
                $session = $notify->getSession();
                // If could not transfer, retry
                $hoiio->ivrGather($session, $THIS_SERVER_URL . '?app_state=gather', $TEXT_TRANSFER_FAILED, 1, 10, 3);       
            }
            break;        
         
         default:
            // Do nothing       
    }
    
    // Text Formation
    function formDirectoryText($directory) {
        $text = '';
        foreach ($directory as $key => $value) {
            $text = $text . ' Press ' . $key . ' ' . $value[1]. '.';
        }
        return $text;
    }

    function appendToCallRecordFile($text) {
        appendToFile($text, 'calls.log');
    }

    function appendToNotificationFile($text) {
        appendToFile($text, 'notifications.log');
    }

    function appendToFile($text, $filename = "others.log") {
        $fh = fopen($filename, 'a') or die("can't open file");
        fwrite($fh, $text . "\n");
        fclose($fh);
    }
?>
