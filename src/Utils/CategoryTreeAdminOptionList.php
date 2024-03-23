<?php
namespace App\Utils;

use App\Utils\AbstractClasses\CategoryTreeAbstract;

class CategoryTreeAdminOptionList extends CategoryTreeAbstract
{
    public function getCategoryList(array $categories_array, int $repeat = 0)
    {
        foreach ($categories_array as $value) {
            $prefix = str_repeat("-", $repeat);
            $this->categorylist[] = ['name' => $prefix . $value['name'], 'id' => $value['id']];

            if (!empty($value['children'])) {
                $repeat += 2;
                $this->getCategoryList($value['children'], $repeat);
                $repeat -= 2;
            }
        }

        return $this->categorylist;
    }
}
