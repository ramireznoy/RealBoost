<?php

namespace AdminBundle\Utils;

/**
 * Description of ColumnModel
 *
 * @author Luis
 */
class ColumnModel {
    protected $width;
    protected $text;
    protected $type;
    protected $options;
    
    public function __construct($width, $text, $type = 'text', $options = null) {
        $this->width = $width;
        $this->text = $text;
        $this->type = $type;
        $this->options = $options;
    }
    
    public function getWidth() {
        return $this->width;
    }

    public function getText() {
        return $this->text;
    }

    public function getType() {
        return $this->type;
    }

    public function setWidth($width) {
        $this->width = $width;
        return $this;
    }

    public function setText($text) {
        $this->text = $text;
        return $this;
    }

    public function setType($type) {
        $this->type = $type;
        return $this;
    }
    
    public function getOptions() {
        return $this->options;
    }

    public function setOptions($options) {
        $this->options = $options;
        return $this;
    }
}
