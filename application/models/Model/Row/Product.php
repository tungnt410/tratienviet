<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Products
 *
 * @author tungx
 */
class Model_Row_Product {

    //put your code here
    public function toArray() {
        $product = array();
        $product['id'] = $this->id;
        $product['name'] = $this->name;
        $product['slug'] = $this->slug;
        $product['description'] = $this->description;
        $imgs = explode("|", $this->images);
        $product['images'] = $imgs;
        $product['price'] = $this->price;
        $product['club_id'] = $this->club_id;
        return $product;
    }

}
