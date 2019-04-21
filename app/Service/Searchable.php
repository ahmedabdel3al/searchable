<?php
/**
 * Created by PhpStorm.
 * User: Code95
 * Date: 4/21/2019
 * Time: 2:18 PM
 */

namespace App\Service;


use Illuminate\Http\Request;


trait Searchable
{
    private static $Operator = [
        'equal' => '=',
        'not_equal' => '!=',
        'greater' => '>',
        'greater_or_equal' => '>=',
        'less_than' => '<',
        'less_than_or_equal' => '<=',
        'like' => 'like'
    ];
    private static $searchableKeyWithValue = [];

    public function scopeFilter($query, Request $request)
    {
        //set searchable keys with values  ['name__'=>'ahmed','password'=>123456789]
        self::$searchableKeyWithValue = self::mapRequest($request->all());
        //get searchable key ['name']
        $keys = self::getSearchableKeys();
        //empty inputs you should search
        if (empty($keys)) {
            return;
        }
        //getQueryBuilder
        foreach ($keys as $key) {
            //check if input like posts-title that mean you should search with relation
            //str searches for the first occurrence of a string inside another string
            // ex : posts-title = title => that means you should search where has relation with with posts
            // and title column in table posts table should equal to title
            if (strstr($key, '-')) {
                $relation = explode('-', $key);
                $query->whereHas($relation[0], function ($q) use ($relation, $key) {
                    $op = self::$Operator[self::$searchableKeyWithValue[$key]['operator']];
                    if ($op == self::$Operator['like']) {
                        $q->where($key, "$op",
                            '%' . self::$searchableKeyWithValue[$key]['value'] . '%');
                    } else {
                        $q->where($relation[1], "$op",
                            self::$searchableKeyWithValue[$key]['value']);
                    }
                });
            } else {
                //check input like name__like = ca
                // that means you search in same table but with like mode
                //else if input name=ca  that means search with equal value
                $op = self::$Operator[self::$searchableKeyWithValue[$key]['operator']];
                if ($op == self::$Operator['like']) {
                    $query->where($key, "$op", '%' . self::$searchableKeyWithValue[$key]['value'] . '%');
                } else {
                    $query->where($key, "$op", self::$searchableKeyWithValue[$key]['value']);
                }

            }
        }
        return $query;
    }

    /**
     * @todo MapRequest to define Our Rule which return array like that ["name" => array:2 ["operator" => "like","value" => "c"]]
     * @param array $inputs
     * @return array
     */
    public static function mapRequest(array $inputs)
    {
        $output = [];
        foreach ($inputs as $key => $value) {
            $keyWithOperator = explode('__', $key);
            $output[$keyWithOperator[0]] = ['operator' => ($keyWithOperator[1] ?? 'equal'), 'value' => $value];
        }
        return $output;
    }

    /**
     * @return array
     */
    public static function getSearchableKeys()
    {
        // check if searchingKeys exist in searchable array in Model
        //example compare ['name'] compare with ['name','password']
        //return will be ['name']
        return array_intersect(self::$searchable, array_keys(self::$searchableKeyWithValue));
    }


}