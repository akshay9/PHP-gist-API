<?php

class GistEdit {

	private $data;
	
	private static $_instance = NULL ;
	
	public static function init () {
		if (self::$_instance === NULL) {
            self::$_instance = new self;
        }
		self::$_instance->data = array();
        return self::$_instance;
	}
	
	public function edit ($file, $newContent = NULL, $newFileName = NULL) {
		
		if ($newContent !== NULL) {
			$this->data[$file]['content'] = $newContent ;
		}
		if ($newFileName !== NULL) {
			$this->data[$file]['filename'] = $newFileName ;
		}
		return $this;
	}
	
	public function deleteFile ($file) {
		$this->data[$file] = NULL ;
		return $this;
	}
	
	public function newFile ($file, $content){
		$this->data[$file]['content'] = $content;
		return $this;
	}
	
	public function get () {
		return $this->data;
	}

}

class gistAPI {
	
	private $url = "https://api.github.com" ;
	
	private $user = "github" ;
	
	public $ch ;
	
	private $response ;
	
	public $loginInfo ;
	
	
	function __construct($id = NULL, $pass = NULL) {
		if($id === NULL || $pass === NULL){
			$loginInfo = NULL;
		} else {
			$loginInfo = array('username' => $id,
							   'password' => $pass);
		}
		$this->loginInfo = $loginInfo;
		$this->chReset();
	}
	
	public function listGists ($type = "public", $user = NULL) {
	
		switch ($type) {
			case "public":
				curl_setopt($this->ch, CURLOPT_URL, $this->url . "/gists/public");
				break;
			case "user":
				curl_setopt($this->ch, CURLOPT_URL, $this->url . "/users/" . ($user === NULL ? $this->user:$user) ."/gists");
				break;
			case "starred":
				curl_setopt($this->ch, CURLOPT_URL, $this->url . "/gists/starred");
				break;
		}
		return $this->returnCode();
	
	}
	
	public function getGist ($gistId) {
	
		curl_setopt($this->ch, CURLOPT_URL, $this->url . "/gists/".$gistId);
		return $this->returnCode();
	
	}
	
	public function createGist ($files, $description = "", $public = false) {
	
		$filesArray = array();
		foreach ($files as $fileName => $content)
			$filesArray[$fileName]['content'] = $content;
		$postArray = array(
					"files"		  => $filesArray,
					"description" => $description,
					"public"	  => $public
					);
		$jsonArray = json_encode($postArray);
		
		curl_setopt($this->ch, CURLOPT_URL, $this->url . "/gists");
		curl_setopt($this->ch, CURLOPT_POST, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $jsonArray);
		return $this->returnCode();
	
	}
	
	public function editGist ($gistId, $files = NULL, $description = NULL) {
	
		curl_setopt($this->ch, CURLOPT_URL, $this->url . "/gists/". $gistId);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
		if ($files === NULL && $description !== NULL) {
			$jsonArray = json_encode(array("description" => $description));
		} elseif ($description === NULL && $files !== NULL) {
			$jsonArray = json_encode(array("files" => $files));
		} elseif ($description !== NULL && $files !== NULL) {
			$jsonArray = json_encode(array("description" => $description, "files" => $files));
		} else {
			$this->chReset();
			return 0;
		}
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $jsonArray);
		return $this->returnCode();
	
	}
	
	public function gistCommits ($gistId) {
	
		curl_setopt($this->ch, CURLOPT_URL, $this->url . "/gists/" . $gistId . "/commits");
		return $this->returnCode();
	
	}
	
	public function starGist ($gistId) {
	
		curl_setopt($this->ch, CURLOPT_URL, $this->url . "/gists/". $gistId ."/star");
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		return $this->returnCode();
	
	}
	
	public function unstarGist ($gistId) {
	
		curl_setopt($this->ch, CURLOPT_URL, $this->url . "/gists/". $gistId ."/star");
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		return $this->returnCode();
	
	}
	
	public function checkStarGist ($gistId) {
	
		curl_setopt($this->ch, CURLOPT_URL, $this->url . "/gists/". $gistId ."/star");
		return $this->returnCode();
	
	}
	
	public function forkGist ($gistId) {
	
		curl_setopt($this->ch, CURLOPT_URL, $this->url . "/gists/". $gistId ."/forks");
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'POST');
		return $this->returnCode();
	
	}
	
	public function listForkGist ($gistId) {
	
		curl_setopt($this->ch, CURLOPT_URL, $this->url . "/gists/". $gistId ."/forks");
		return $this->returnCode();
	
	}
	
	public function deleteGist ($gistId) {
	
		curl_setopt($this->ch, CURLOPT_URL, $this->url . "/gists/". $gistId);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		return $this->returnCode();
	
	}
	
	public function gistComments ($gistId) {
	
		curl_setopt($this->ch, CURLOPT_URL, $this->url . "/gists/".$gistId."/comments");
		return $this->returnCode();
	
	}
	
	public function getComment ($gistId, $commentId) {
	
		curl_setopt($this->ch, CURLOPT_URL, $this->url . "/gists/". $gistId ."/comments/". $commentId);
		return $this->returnCode();
	
	}
	
	public function createComment ($gistId, $comment){
	
		curl_setopt($this->ch, CURLOPT_URL, $this->url . "/gists/". $gistId ."/comments");
		curl_setopt($this->ch, CURLOPT_POST, 1);
		$jsonArray = json_encode(array("body" => $comment));
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $jsonArray);
		return $this->returnCode();
	
	}
	
	public function editComment ($gistId, $commentId, $comment) {
	
		curl_setopt($this->ch, CURLOPT_URL, $this->url . "/gists/". $gistId ."/comments/". $commentId);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
		$jsonArray = json_encode(array("body" => $comment));
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $jsonArray);
		return $this->returnCode();
	
	}
	
	public function deleteComment ($gistId, $commentId) {
	
		curl_setopt($this->ch, CURLOPT_URL, $this->url . "/gists/". $gistId ."/comments/". $commentId);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		return $this->returnCode();
	
	}
	
	public function getLimits() {
	
		curl_setopt($this->ch, CURLOPT_URL, $this->url . "/rate_limit");
		return $this->returnCode();
	}
	
	private function parseHeader ($header_text) {
		
		$headers = array();
		foreach (explode("\r\n", $header_text) as $i => $line){
			if (strlen($line) > 1 && $i != 0){
				list ($key, $value) = explode(': ', $line);
				$headers[$key] = $value;
			} else if ($i == 0){
				$headers['http_code'] = $line;
			}
		}
		return $headers;
	}
	
	private function returnCode () {
	
		$this->response = curl_exec($this->ch);
		$header_size = curl_getinfo($this->ch, CURLINFO_HEADER_SIZE);
		$header = substr($this->response, 0, $header_size);
		$body = substr($this->response, $header_size);
		$return = array("header" => $this->parseHeader($header),
					 "body"	  => json_decode($body, true),
					 "raw"    => $this->response);
		$this->chReset();
		return $return;
	}
	
	public function chReset () {
	
		$this->ch = curl_init();
		curl_setopt($this->ch, CURLOPT_HEADER,         true);
		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->ch, CURLOPT_TIMEOUT,        30);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		if ($this->loginInfo !== NULL){
			$this->user = $this->loginInfo['username'];
			curl_setopt($this->ch, CURLOPT_USERAGENT, $this->loginInfo['username']);
			curl_setopt($this->ch, CURLOPT_USERPWD, $this->loginInfo['username'].":".$this->loginInfo['password']);
		} else {
			curl_setopt($this->ch, CURLOPT_USERAGENT, "gistAPI v1.0");
		}
		unset($this->response);
	
	}
	
	function __destruct() {
		curl_close($this->ch);
	}

}
?>