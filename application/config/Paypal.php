<?php
/** set your paypal credential **/

$config['client_id'] = 'AeMAEmLzQFlWE5Avcf9QMWIF0eO2_gfZvERBcYd1zXCZO4wm39-Rknc7y55umT4Y_DI0GbCmDuMPi6Ia';
$config['secret'] = 'EMthm9EPRBR0oe35HHpKsLbc_UuEKNib0IcIOb2jBUKjznwWIjAq2r2JOPIsKf2VzHZHNedzJKh0lBcG';

/**
 * SDK configuration
 */
/**
 * Available option 'sandbox' or 'live'
 */
$config['settings'] = array(

    'mode' => 'sandbox',
    /**
     * Specify the max request time in seconds
     */
    'http.ConnectionTimeOut' => 1000,
    /**
     * Whether want to log to a file
     */
    'log.LogEnabled' => true,
    /**
     * Specify the file that want to write on
     */
    'log.FileName' => 'application/logs/paypal.log',
    /**
     * Available option 'FINE', 'INFO', 'WARN' or 'ERROR'
     *
     * Logging is most verbose in the 'FINE' level and decreases as you
     * proceed towards ERROR
     */
    'log.LogLevel' => 'FINE'
);
