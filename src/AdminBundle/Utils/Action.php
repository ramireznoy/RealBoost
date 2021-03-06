<?php

namespace AdminBundle\Utils;

/**
 * Description of Action
 *
 * @author Luis
 */
class Action {
    protected $name;
    protected $path;
    
    public function __construct($name, $path) {
        $this->name = $name;
        $this->path = $path;
    }
    
    public function getName() {
        return $this->name;
    }

    public function getPath() {
        return $this->path;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function setPath($path) {
        $this->path = $path;
        return $this;
    }
}
