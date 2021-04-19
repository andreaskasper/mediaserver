<?php

class Bucket {

    private $_slug = null;

    public function __construct($type, $id) {
        switch ($type) {
            case "slug":
                $this->_slug = $id;
                return;
        }
    }

    public function __get($name) {
        switch ($name) {
            case "slug": return $this->_slug;
        }
        return null;
    }

    public function should_convert($key) {
        
    }


    
}