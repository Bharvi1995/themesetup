<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;
use App\TransactionSession;


class DebugController extends Controller
{
	public function __construct()
    {
        $this->Transaction = new Transaction;
        $this->TransactionSession = new TransactionSession;
    }
    // ================================================
    /* method : getCurrencyRate
    * @param  : 
    * @description : get currency rate cron
    */// ==============================================
	public function logfileread(Request $request)
	{
		$realm = 'Restricted area';
		$users = json_decode(env('DEBUG_LOGIN_DETAILS'), true);
		if(empty($_SERVER['PHP_AUTH_DIGEST'])) {
            header('HTTP/1.1 401 Unauthorized');
            header('WWW-Authenticate: Digest realm="'.$realm.'",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');
            die('Text to send if user hits Cancel button');
        }

        // analyze the PHP_AUTH_DIGEST variable
       if (!($data = $this->http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) || !isset($users[$data['username']])) {
       		header("Status:401 Logout");
       		header("WWW-Authenticate: Invalidate, Basic realm=logout");
       		die('Wrong Credentials!');
        }
        // generate the valid response
        $A1 = md5($data['username'] . ':' . $realm . ':' . $users[$data['username']]);
        $A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
        $valid_response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);
        if ($data['response'] != $valid_response){
			die('Wrong Credentials!');
        }
		// storage\logs\laravel.log
		$files = storage_path('logs/laravel.log');
		$Numberoflineread = 500;
		$file = file($files);
		$readLines = max(0, count($file)-$Numberoflineread);
		if($readLines > 0) {
			for ($i = $readLines; $i < count($file); $i++) {
				echo $file[$i];
				echo nl2br("\n");
			}
		} else {
			echo 'file does not have required no. of lines to read';
		}
		exit("End");
	}
	
	public function http_digest_parse($txt) {
        // protect against missing data
        $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
        $data = array();
        $keys = implode('|', array_keys($needed_parts));
        preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);
        foreach ($matches as $m) {
            $data[$m[1]] = $m[3] ? $m[3] : $m[4];
            unset($needed_parts[$m[1]]);
        }
        return $needed_parts ? false : $data;
    }

    public function debug($flag=null)
    {

    	$realm = 'Restricted area';
    	$users = json_decode(env('DEBUG_LOGIN_DETAILS'), true);

        if(empty($_SERVER['PHP_AUTH_DIGEST'])) {
            header('HTTP/1.1 401 Unauthorized');
            header('WWW-Authenticate: Digest realm="'.$realm.'",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');
            die('Text to send if user hits Cancel button');
        }

        // analyze the PHP_AUTH_DIGEST variable
       if (!($data = $this->http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) || !isset($users[$data['username']])) {
       		header("Status:401 Logout");
       		header("WWW-Authenticate: Invalidate, Basic realm=logout");
       		die('Wrong Credentials!');
        }
        // generate the valid response
        $A1 = md5($data['username'] . ':' . $realm . ':' . $users[$data['username']]);
        $A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
        $valid_response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);
        if ($data['response'] != $valid_response){
			die('Wrong Credentials!');
        }
        // ok, valid username & password
        if($flag == "on"){
        	//echo 'You are logged in as: ' . $data['username'];
            $this->addIpInDebugeFile(1);
            return redirect('/');
            exit();
        } else if($flag == "off") {
            $this->addIpInDebugeFile(0);
            return redirect('/');
            exit();
        } else {
            header('HTTP/1.1 403 Forbidden');
            return redirect('/');
            exit();
        }
    }

    public function addIpInDebugeFile($addIP = 0) {

        $ip 		= get_client_ip();
        $filePath 	= storage_path("debug_ip_list_cs.json");;
        $ips 		= array();
        
        if(is_file($filePath)) {
            $data = file_get_contents($filePath);
            $data = json_decode($data, true);
            $data[$ip] = $ip;
            $ips = $data;
        } else {
            $ips[$ip] = $ip;
        }
        
        if($addIP == 0) {
            if(isset($ips[$ip])) {
            	unset($ips[$ip]);
            }
        }
        $json = json_encode($ips);
        $myfile = fopen($filePath, "w") or die("Unable to open file!");
        fwrite($myfile, $json);
        fclose($myfile);
    }




}
