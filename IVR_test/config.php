<?php
  
  //Basic Information
  $HOIIO_APP_ID             = "O268X1iSTxCwWlHq";
  $HOIIO_ACCESS_TOKEN       = "pyQQoUkGyrX5lnFO";
  $MY_MOBILE_NUMBER         = "+16594606788";
  $MY_MANAGER_NUMBER        = "+16594606788";                               //for testing purposes, same mobile number here
  $MY_ASSISTANT_NUMBER      = "+16594606788";                               //for testing purposes, same mobile number here
  $THIS_SERVER_URL          = "http://localhost:8080/ivr_redirection.php";
  
  //Basic Text
  $MY_NAME              = "Raj";
  $TEXT_WELCOME_MESSAGE = "Hi there ! I am " . $MY_NAME . "'s Phone Assistant.";
  $TEXT_TRANSFERRING    = 'Please wait while I transfer your call.';
  $TEXT_TRANSFER_FAILED = 'Sorry, the call was not answered. Please try again.';
  $TEXT_INVALID_KEY     = 'You have entered an invalid option. Please try again.';
  $TEXT_SMS_ALERT_SENT  = $MY_NAME . ' has received your SMS. He will call you back shortly. Goodbye!';
  
  //Telephone Directory
  $directory = array (
        '1'=>array($MY_MANAGER_NUMBER,    "to reach " . $MY_NAME . "'s manager's number in case of official emergency."),
        '2'=>array($MY_ASSISTANT_NUMBER,  "to reach " . $MY_NAME . "'s assistant's number for official work."),
        '3'=>array('SMSALERT',            "to send "  . $MY_NAME . " an SMS, and he will call you back shortly."),
  );   
  
?>
