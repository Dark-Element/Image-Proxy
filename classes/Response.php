<?php
/**
 * Created by PhpStorm.
 * User: mint
 * Date: 8/27/16
 * Time: 10:43 AM
 */

use Intervention\Image\ImageManager;

/**
 * @property array manipulation_params
 */
class Response
{
    private $image_url;
    private $image_content;
    private $output_header;
    private $manipulated_image;
    private $manipulation_params;
    private $mime_type;
    private $times;


    function __construct() {
        $this->auth();
        //built params
        $this->set_image();
        $this->set_manipulation_params();
    }

    public function get_image(){
        $this->manipulate_image();
        $args = [
            'header'=> 'Content-Type: ' . $this->mime_type
            ];
        $this->render('Image',$this->manipulated_image,$args);
    }

    private function manipulate_image(){
        $image = null;
        $manipulator = new ImageManager(array('driver' => 'gd'));
        try{
            $image = $manipulator->make($this->image_content);
        }catch(\Exception $e){
            $this->render('Error',$e->getMessage());
        }

        if(!isset($this->mime_type)){
            $this->mime_type = $image->mime();
        }

        if(isset($this->manipulation_params['width']) || isset($this->manipulation_params['height'])){
            $width = null;
            $height = null;
            if(isset($this->manipulation_params['width'])){
                $width = $this->manipulation_params['width'];
            }

            if(isset($this->manipulation_params['height'])){
                $height = $this->manipulation_params['height'];
            }
            if($this->manipulation_params['constrain']){
                $image->resize($width,$height,function ($constraint) {
                    $constraint->aspectRatio();
                });
            }else{
                $image->resize($width,$height);
            }
        }

        $format = (isset($this->manipulation_params['format']) ? $this->manipulation_params['format'] : explode('/',$this->mime_type)[1] );
        $quality = (isset($this->manipulation_params['quality']) ? $this->manipulation_params['quality'] : '100' );

        $this->manipulated_image = $image->encode($format,$quality);
    }

    function set_image(){
        try{
            if(!isset($_REQUEST['source_url'])) {
                $this->render('Error', 'No source image defined');
            }
            $this->image_url = $_REQUEST['source_url'];
            $this->image_content = file_get_contents($this->image_url);

        }catch (\Exception $e){
            $this->render('Error',$e->getMessage());
        }
    }

    function set_manipulation_params(){
        $params = [];
        if(!isset($_REQUEST['params'])){
            $manipulator = new ImageManager(array('driver' => 'imagick'));
            try{
                $image = $manipulator->make($this->image_content);
            }catch(\Exception $e){
                $this->render('Error',$e->getMessage());
            }
            $args['header'] = 'Content-Type: '. $image->mime()  ;
            $this->render('Image',$this->image_content,$args);
        }
        foreach(explode(',',$_REQUEST['params']) as $param){
            $value = substr($param,2);
            switch (strtolower($param[0])){
                case 'w':
                    if(is_numeric($param[count($param)-1])){
                        $params['width'] = $value;
                        $params['constrain'] = false;
                    }else{
                        $params['width'] = substr($value,0,-1);
                        $params['constrain'] = true;
                    }
                    break;
                case 'h':
                    if(is_numeric($param[count($param)-1])){
                        $params['height'] = $value;
                        $params['constrain'] = false;
                    }else{
                        $params['height'] = substr($value,0,-1);
                        $params['constrain'] = true;
                    }
                    break;
                case 'q':
                    $params['quality'] = $value;
                    break;
                case 'f':
                    $this->mime_type = $value;
                    $params['format'] = $value;
                    break;
            }
        }


        $this->manipulation_params = $params;
    }


    private function auth(){
        $start  = microtime();
        //$this->render('Error', 'Auth Error: Check your credentials');
        $this->times['auth'] = microtime() - $start;
        return true;
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