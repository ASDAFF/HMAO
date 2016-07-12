<?php
/**
 * Created by PhpStorm.
 * User: dizinfector
 * Date: 08.05.15
 * Time: 17:17
 */

namespace Spo\Site\Util;

class JsonResponse
{
    //private $success = true;
    private $errors = array();
    private $data = array();

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function setErrors($errors)
    {
        $this->errors = is_array($errors) ? $errors : array($errors);
        return $this;
    }

    public function __toString()
    {
        $result = array(
            'success' => true
        );

        if(count($this->data) > 0){
            $result['data'] = $this->data;
        }
        if(count($this->errors) > 0){
            $result['errors'] = $this->errors;
            $result['success'] = false;
        }

        return json_encode($result);
    }
}