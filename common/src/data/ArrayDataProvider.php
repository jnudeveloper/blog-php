<?php

/**
 * Created by PhpStorm.
 * User: michael.shi
 * Date: 2018/1/26
 * Time: 15:16
 */
namespace src\data;

use Yii;
use yii\base\Component;
use yii\data\Pagination;
use yii\base\InvalidParamException;

class ArrayDataProvider extends Component
{
    public  $allModels;

    private $_models;
    private $_keys;
    private $_pagination;
    private $_totalCount;

    public function prepare(){
        if($this->_models === null){
            $this->_models = $this->prepareModels();
        }
        if($this->_keys === null){
            $this->_keys = $this->prepareKeys($this->_models);
        }
    }

    protected function prepareKeys($models){
        $this->_keys = array_keys($models);
    }

    protected function prepareModels(){
        if (($models = $this->allModels) === null) {
            return [];
        }

        return $models;
    }

    public function getCount(){
        return count($this->getModels());
    }

    public function getTotalCount(){
        $pagination = $this->getPagination();
        if ($pagination instanceof Pagination) {
            return $this->_pagination->totalCount;
        } else{
            return $this->getCount();
        }
    }

    public function setTotalCount($value){
        $this->_totalCount = $value;
    }

    public function getModels(){
        $this->prepare();
        return $this->_models;
    }

    public function setModels($models){
        $this->_models = $models;
    }

    public function getKeys(){
        $this->prepare();
        return $this->_keys;
    }

    public function setKeys($keys){
        $this->_keys = $keys;
    }

    public function getPagination(){
        return $this->_pagination;
    }

    public function setPagination($value)
    {
        if (is_array($value)) {
            $config = ['class' => Pagination::className()];
            $this->_pagination = Yii::createObject(array_merge($config, $value));
        } elseif ($value instanceof Pagination || $value === false) {
            $this->_pagination = $value;
        } else {
            throw new InvalidParamException('Only Pagination instance, configuration array or false is allowed.');
        }
    }

}