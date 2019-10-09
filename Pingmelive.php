<?php
/*
 	* CodeIgniter PingMeLive Class
 	*
	*This is a library which helps you to get LIVE notifications of actions taking place on your webistes and applications.Just Copy past the below codes and get live updates of errors and actions. Make Categories of pings based on projects (etc) and assign it to your team mates.That's what PingMeLive does.Easy right!.
 	*
	 * @package		PingMeLive-CodeIgniter Integration
	 * @subpackage		Libraries
	 * @category		Libraries
	 * @author		PingMeLive Team
	 * @link		https://pingmelive.com/
	 * @lastModified	08-OCT, 2019 2:05 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');


class CI_PingMeLive {
public function __construct()
   {
	$PingMeLiveConfig=json_decode(PingMeLiveConfig,true);
        $this->apiKey         = $PingMeLiveConfig['apiKey'];
        $this->projectID      = $PingMeLiveConfig['projectID'];
        $this->errorLogStatus = $PingMeLiveConfig['errorLogStatus'];
        $this->errorTitle     = $PingMeLiveConfig['errorTitle'];
        $this->eventDateTime  = date('Y-m-d H:i:s');
        if ($this->errorLogStatus == true) {
            $this->startErrorPings();
        }
    }
public function pingError($errno, $errstr, $errfile, $errline)
    { 
        $eventDateTime = $this->eventDateTime;
        $groupTitle    = $this->errorTitle;
        $requestUrl    = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $userIP        = $_SERVER['REMOTE_ADDR'];
        $messageText   = "Error:[$errno] $errstr";
        $detailedText  = array(
            "Error No" => $errno,
            "Error Description" => $errstr,
            "Line No" => $errline,
            "File Location" => $errfile,
            "Event DateTime" => $eventDateTime,
            "RequestUrl" => $requestUrl,
            "User IP" => $userIP
        );
        $this->sendData($groupTitle, $messageText, json_encode($detailedText));
    }
public function sendData($groupTitle, $messageText, $detailedText = "")
    {
        $eventDateTime = $this->eventDateTime;
        $url           = "https://pingmelive.com/event/push";
        $apiKey        = $this->apiKey;
        $projectID     = $this->projectID;
        $bodyRequest   = array(
            "groupTitle" => $groupTitle,
            "message" => $messageText,
            "detailedText" => $detailedText,
            "eventDateTime" => $eventDateTime
        );
        $header        = "-H 'Content-type: application/json' -H 'apiKey:$apiKey' -H 'projectID:$projectID'";
        $curlRequest   = "curl $header -X 'POST' -d '" . json_encode($bodyRequest) . "' --url '" . $url . "' > /dev/null 2>&1 &";
        exec($curlRequest);
        return true;
    }
public function simpleEvent($groupTitle, $messageText)
    {
        $this->sendData($groupTitle, $messageText);
        return true;
    }
public function startErrorPings(){	
	error_reporting(E_ALL);
	ini_set('display_errors', 'off');
	set_error_handler(function($errno, $errstr, $errfile, $errline)
	{
	$this->pingError($errno, $errstr, $errfile, $errline);
	return true;
	}, E_ALL);
	set_exception_handler(function($exception){
	$this->pingError($exception->getCode(),'Exception: '.$exception->getMessage(), $exception->getFile(), $exception->getLine());
	return false;
	set_status_header(500);
	exit(1);
	});
	register_shutdown_function(function(){
	$last_error = error_get_last();
	if (isset($last_error) &&
	($last_error['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING)))
	{
	$this->pingError($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line']);
	}
	});
        return true;
    }
public function detailedEvent($groupTitle, $messageText, $detailedText)
    {
        $this->sendData($groupTitle, $messageText, $detailedText);
        return true;
    }
}
?>
