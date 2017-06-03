<?php

namespace AdminBundle\Utils;

/**
 * Description of TableModel
 *
 * @author Luis
 */
class TableModel {
    protected $icon;
    protected $title;
    protected $columns;
    protected $actions;
    protected $data;
    protected $rowcount;
    protected $rowsperpage;
    protected $newpath;
    protected $editpath;
    protected $deletepath;

    /**
     * creaye new table model
     * 
     * @param string $icon font awesome for table identification 
     * @param string $title table title
     */
    public function __construct($icon = null, $title = null, $rowcount = 10) {
        $this->icon = $icon;
        $this->title = $title;
        $this->rowcount = $rowcount;
        $this->rowsperpage = array(10, 20, 30);
    }
    
    public function getIcon() {
        return $this->icon;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getColumns() {
        return $this->columns;
    }

    public function getActions() {
        return $this->actions;
    }

    public function getData() {
        return $this->data;
    }

    public function setIcon($icon) {
        $this->icon = $icon;
        return $this;
    }

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    public function setColumns($columns) {
        $this->columns = $columns;
        return $this;
    }

    public function setActions($actions) {
        $this->actions = $actions;
        return $this;
    }

    public function setData($data) {
        $this->data = $data;
        return $this;
    }
    
    public function getRowcount() {
        return $this->rowcount;
    }

    public function getRowsperpage() {
        return $this->rowsperpage;
    }

    public function setRowcount($rowcount) {
        $this->rowcount = $rowcount;
        return $this;
    }

    public function setRowsperpage($rowsperpage) {
        $this->rowsperpage = $rowsperpage;
        return $this;
    }
    
    public function getNewpath() {
        return $this->newpath;
    }

    public function getEditpath() {
        return $this->editpath;
    }

    public function getDeletepath() {
        return $this->deletepath;
    }

    public function setNewpath($newpath) {
        $this->newpath = $newpath;
        return $this;
    }

    public function setEditpath($editpath) {
        $this->editpath = $editpath;
        return $this;
    }

    public function setDeletepath($deletepath) {
        $this->deletepath = $deletepath;
        return $this;
    }
}
