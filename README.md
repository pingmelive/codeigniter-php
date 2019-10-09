# What is PingMeLive?

This is a library which helps you to get LIVE notifications of actions taking place on your webistes and applications.
Just Copy past the below codes and get live updates of errors and actions. Make Categories of pings based on projects (etc) and assign it to your team mates.
That's what PingMeLive does.
Easy right!.

## How to use

### Register yourself on

[https://pingmelive.com](https://pingmelive.com) **and get your `API KEY`**.


### Integration
* **Step 1**  Copy PingMeLive Library File (**PingMeLive.php**) in `{BASEROOT}/system/libraries`


* **Step 2** Autoload the PingMeLive Library in `{BASEROOT}/application/config/autoload.php`


```php 
<?php 
$autoload['libraries'] = array('pingmelive');
?>
```


* **Step 3** Define the Crendentials in Constant in `{BASEROOT}/application/config/constant.php`


```php 
<?php 
// Copy the code below and simply paste in the constant.php 
$pingmeliveConf = array("apiKey" =>"Your API KEY","projectID" =>"PROJECT ID","errorLogStatus"=>true,"errorTitle"=>"CodeIgnitor PHP Error");

defined('PingMeLiveConfig') OR define('PingMeLiveConfig', json_encode($pingmeliveConf));

?>
```
### ** To enable PingMelive Error Logging , you have to disable the Default Codeigniter Error Logging Handlers.


For disabling, comment this lines in `{BASEROOT}/system/core/CodeIgniter.php`

```php
<?php 
// set_error_handler('_error_handler');
// set_exception_handler('_exception_handler');
// register_shutdown_function('_shutdown_handler');
 ?>
 ```
...and you are done!


## Custom Events

You can also use pingMeLive for sending custom events.

### 1.Simple event

```php
<?php 
//To trigger Simple Event, Just call the below function.
$this->pingmelive->simpleEvent("groupTitle","eventMessage");
?>
 ```    

If you want to send some detailed long description you can use `Detailed event`

### 2.Detailed event

```php
<?php 
//To trigger Detailed Event, Just call the below function.
$this->pingmelive->detailedEvent("groupTitle","eventMessage","detailDescription");
?>
```

### Options
* **apiKey** : To get an `API KEY` , register on pingmelive.com. Its free to use.
* **projectID** : Once registered, Click on New Project to create. 
* **errorStatus** : `true` / `false` (Boolen Value).
* **errorName** : This will be your `Group Title/Name` where all the error will be pinged.(This works when `errorStatus` is set as `true`.
* **groupTitle** : This will be your `Group Tilte/Name` under which , you will get all your pings.
* **eventMessage** : The `errorMessage` is limited to 360 character in length. Any additional characters beyond 360 character will be truncated.
* **detailDescription** : The `detailDescription` is does not have any length limitation. You can also send JSON Formatted String / or simple plain string.

## Some usefull information

* If you only want error pings, Just include the `pingMeLive library` and and `Intialize` it.
* You can smartly use `groupTitle` for creating uniques group for your pings.

