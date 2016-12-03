<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 03/12/16
 * Time: 09:03
 */

class Manipulator {

	private $params;
	private $image;
	private $output_image;
	public  $errors = [];

	public function __construct(Image $image, $params = null) {
		$this->image = $image;
		$this->set_params($params);
		$this->manipulate();
	}


	private function set_params($manipulation_params){
		$params = [];
		if(!isset($manipulation_params) || $manipulation_params == '' ){
			return false;
		}

		foreach(explode(',',$manipulation_params) as $param){
			$param = explode(':',$param);
			$key = $param[0];
			$value = $param[1];
			switch ($key){
				case 'w':
					if(is_numeric(substr($value,-1))){
						$params['width'] = $value;
						$params['constrain'] = false;
					}else{
						$params['width'] = substr($value,0,-1);
						$params['constrain'] = true;
					}
					break;
				case 'h':
					if(is_numeric(substr($value,-1))){
						$params['height'] = $value;
						$params['constrain'] = false;
					}else{
						$params['height'] = substr($value,0,-1);
						$params['constrain'] = true;
					}
					break;
				case 'c':
					$crop_array = explode('x',$value);
					$params['crop_width'] = $crop_array[0];
					$params['crop_height'] = $crop_array[1];
					break;
				case 'fill':
					$res_array = explode('x',$value);
					$params['fill_width'] = $res_array[0];
					$params['fill_height'] = $res_array[1];
					break;
				case 'fit':
					$res_array = explode('x',$value);
					$params['fit_width'] = $res_array[0];
					$params['fit_height'] = $res_array[1];
					break;
				case 'q':
					$params['quality'] = $value;
					break;
				default:
					break;

			}
		}
		$this->params = $params;
	}

	private function manipulate(){
		if(isset($this->params['width']) || isset($this->params['height'])){
			$width = null;
			$height = null;
			if(isset($this->params['width'])){
				$width = $this->params['width'];
			}

			if(isset($this->params['height'])){
				$height = $this->params['height'];
			}
			if($this->params['constrain']){
				$this->image->process_image()->resize($width,$height,function ($constraint) {
					$constraint->aspectRatio();
				});
			}else{
				$this->image->process_image()->resize($width,$height);
			}
		}


		if(isset($this->params['crop_width'])){
			$this->image->process_image()->crop($this->params['crop_width'],$this->params['crop_height']);
		}

		if(isset($this->params['fill_width'])){
			$this->image->process_image()->fill($this->params['fill_width'],$this->params['fill_height']);
		}

		if(isset($this->params['fit_width'])){
			$this->image->process_image()->fit($this->params['fit_width'],$this->params['fit_height']);
		}

		$quality = isset($this->params['quality']) ? $this->params['quality'] : '100' ;
		//print_r( $this->params);
		if($quality != '100'){
			$params['content'] = $this->image->process_image()->encode(null ,$quality)->response();
		}else{
			$params['content'] = $this->image->process_image()->response();
		}

		$params['mime'] = $this->image->getMime();
		$this->output_image = new Image($params);
		return $this->output_image;
	}

	/**
	 * @return mixed
	 */
	public function getOutputImage() {
		return $this->output_image;
	}
}