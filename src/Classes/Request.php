<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 03/12/16
 * Time: 09:02
 */

class Request {

	private $source_image;
	public $errors = [];

	public function __construct($request_url) {
		if($request_url != null || $request_url != ''){
			$this->get_remote_image($request_url);
		}else{
			array_merge($this->errors,['No source url defined']);
		}
	}

	private function get_remote_image($url){
		try{
			//caching goes here
			$params['content'] = file_get_contents($url);
			$params['source_url'] = $url;
			$this->source_image = new Image($params);
			return $this->source_image;
		}catch (\Exception $e){
			array_merge($this->errors,[$e->getMessage()]);
		}
		return $this->source_image;
	}

	/**
	 * @return Image
	 */
	public function getSourceImage() {
		return $this->source_image;
	}
}