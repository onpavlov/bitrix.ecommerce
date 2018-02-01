<?php
namespace BxEcommerce;

use Bitrix\Iblock;

class Product
{
    public $id = "";
    public $name = "";
    public $price = "";
    public $brand = "";
    public $category = "";
    public $position = "";
    public $variant = "";
    public $dimension1 = "";
    public $quantity = "";

    public function __construct($data = [])
    {
        foreach ($data as $name => $val) {
            if ($name === 'category') $val = $this->getCategoryName($val);
            if ($name === 'brand') $val = $this->getBrandName($val);

            $this->{$name} = $val;
        }
    }

    /**
     * @return array
     */
    public function getProduct()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'brand' => $this->brand,
            'category' => $this->category,
            'position' => $this->position
        ];
    }

    /**
     * @return array
     */
    public function getFullProduct()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'brand' => $this->brand,
            'category' => $this->category,
            'position' => $this->position,
            'variant' => $this->variant,
            'dimension1' => $this->dimension1,
            'quantity' => $this->quantity
        ];
    }

    /**
     * @param $id
     * @return string
     */
    private function getCategoryName($id)
    {
        if (is_array($id)) $id = end($id);
        if (!is_numeric($id)) return $id;

        $section = Iblock\SectionTable::getById($id)->fetch();
        return empty($section['NAME']) ? '' : $section['NAME'];
    }

    /**
     * @param $id
     * @return string
     */
    private function getBrandName($id)
    {
        if (!is_numeric($id)) return $id;
        $brand = Iblock\ElementTable::getById($id)->fetch();
        return empty($brand['NAME']) ? '' : $brand['NAME'];
    }
}