<?php
/**
 * Created by PhpStorm.
 * User: mint
 * Date: 8/27/16
 * Time: 10:43 AM
 */

/**
 * @property array manipulation_params
 */
class Response
{
	private $source_image;
	private $output_image;

    function __construct() {
        $this->auth();
        $this->set_image();
	    $this->manipulate_image();
	    $this->set_response();
    }

	private function auth(){
		$authenticator = new Auth($_REQUEST);
		if(!$authenticator->is_authenticated()){
			$this->render('Error', 'Auth Error: Check your credentials');
		}
	}

	private function set_image(){
		$request = new Request($_REQUEST['source_url']);
		if(count($request->errors) > 0){
			return $this->render('Error',json_encode($request->errors));
		}
		$this->source_image = $request->getSourceImage();
	}

    private function manipulate_image(){
    	$params= isset($_REQUEST['params']) ? $_REQUEST['params'] : '';
        $manipulator = new Manipulator($this->source_image,$params);
	    if(count($manipulator->errors) > 0){
		    return $this->render('Error',json_encode($manipulator->errors));
	    }
	    $this->output_image = $manipulator->getOutputImage();
    }

	private function set_response(){

		$args = [
			'header'=> 'Content-Type: ' . $this->output_image->getMime()
		];
		$this->render('Image',$this->output_image->getContent(),$args);
	}






    private function render($type,$msg,$args = array() ){
        switch ($type) {
            case 'Error':
                header('');
                echo json_encode(array(
                    'status' => $type,
                    'message' => $msg,
                    'additional' => json_encode($args)));
                die();
                break;
            case 'Image':
                header($args['header']);
                echo $msg;
                break;
            default:
                break;
        }
    }
}