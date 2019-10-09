<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2019, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter PingMeLive Class
 *
 * 	This is a library which helps you to get LIVE notifications of actions taking place on your webistes and applications.
	Just Copy past the below codes and get live updates of errors and actions. Make Categories of pings based on projects (etc) and assign it to your team mates.
	That's what PingMeLive does.
	Easy right!.
 *
 * @package		PingMeLive-CodeIgniter Integration
 * @subpackage	Libraries
 * @category	Libraries
 * @author		PingMeLive Team
 * @link		https://pingmelive.com/
 * @lastModified		08-OCT, 2019 2:05 PM
 */
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
public function startErrorPings()
    {
        set_error_handler(function($errno, $errstr, $errfile, $errline)
        {
            $this->pingError($errno, $errstr, $errfile, $errline);
            return true;
        }, E_ALL);
        register_shutdown_function(function()
        {
            $last_error = error_get_last();
            if ($last_error['type'] === E_ERROR) {
                $this->pingError(E_ERROR, $last_error['message'], $last_error['file'], $last_error['line']);
            }
            return true;
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
