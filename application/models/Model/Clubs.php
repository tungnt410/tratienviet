<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Audio
 *
 * @author YINLONG
 */
Zend_Loader::loadClass('Model_Application');
class Model_Clubs extends Model_Application{
    //put your code here
    protected $_name = "clubs";
    protected $_primary = "id";
}
