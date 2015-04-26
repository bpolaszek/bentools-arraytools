<?php
/**
 * MIT License (MIT)
 *
 * Copyright (c) 2014 Beno!t POLASZEK
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * Array tools
 * @author Beno!t POLASZEK - 2014
 */

namespace BenTools;

class ArrayTools {

    /**
     * Checks wether or not this array is an indexed one.
     *
     * @param array $array
     * @return bool
     */
    public static function IsAnIndexedArray(array $array) {
        return array_values($array) === $array;
    }

    /**
     * Checks wether or not this array is an associative one.
     *
     * @param array $array
     * @return bool
     */
    public static function IsAnAssociativeArray(array $array) {
        return !static::IsAnIndexedArray($array);
    }

    /**
     * Adds quotes to each element of the array.
     * @param array  $array
     * @param string $quote
     * @return array
     */
    public static function QuotiFy(array $array, $quote = '\'') {
        return array_map(function($item) use($quote) { return sprintf('%s%s%s', $quote, $item, $quote); }, $array);
    }

    /**
     * Sorts a multi-dimensionnal array.
     * Example usage :
     * ArrayTools::SortMultiple($products, ['category' => SORT_ASC, 'position' => SORT_DESC]);
     *
     * @param array $array
     * @param array $cols
     * @return array
     */
    public static function SortMultiple(array $array, array $cols) {
        $colarr = [];
        foreach ($cols as $col => $order) {
            $colarr[$col] = [];
            foreach ($array as $k => $row) {
                $colarr[$col]['_' . $k] = strtolower($row[$col]);
            }
        }
        $eval = 'array_multisort(';
        foreach ($cols as $col => $order)
            $eval .= '$colarr[\'' . $col . '\'],' . $order . ',';

        $eval = substr($eval, 0, -1) . ');';

        eval($eval);

        $ret = [];
        foreach ($colarr as $col => $arr) {
            foreach ($arr as $k => $v) {
                $k = substr($k, 1);
                if (!isset($ret[$k]))
                    $ret[$k] = $array[$k];
                $ret[$k][$col] = $array[$k][$col];
            }
        }

        return array_values($ret);
    }

    /**
     * Performs a search on a multidimensionnal array, like in_array()
     * @param       $search
     * @param array $array
     * @param bool  $strict
     * @param       $key - the key to search into
     * @return bool
     */
    public static function InArrayMd($search, array $array, $strict = false, $key) {
        foreach ($array AS $item) {
            if (is_array($item)) {
                if (static::InArrayMd($search, $item, $strict, $key))
                    return true;
            }
            else {
                if ((!$strict && array_key_exists($key, $array) && $array[$key] == $search) || ($strict && array_key_exists($key, $array) && $array[$key] === $search))
                    return true;
            }
        }
        return false;
    }

    /**
     * Finds an element in an array like array_search, on a multidimensionnal array
     * @param      $needle
     * @param      $haystack
     * @param bool $strict
     * @param      $key - the key to search into
     * @return int|string
     */
    public static function SearchMd($needle, $haystack, $strict = false, $key) {
        foreach ((array) $haystack AS $i => $item)
            if (($strict && $item[$key] === $needle) || (!$strict && $item[$key] == $needle))
                return $i;
        return false;
    }

    /**
     * Removes all elements where the key doesn't fit in $keys.
     * @param array $array
     * @param array $keys
     * @return array
     */
    public static function FilterKey(array $array, array $keys) {
        return array_intersect_key($array, array_flip($keys));
    }

    /**
     * It's like array_map, but on keys instead of values.
     * @param $callback
     * @param $array
     * @return array
     */
    public static function KeyMap(callable $callback, array $array) {
        return array_combine(array_map($callback, array_keys($array)), $array);
    }

    /**
     * It's like array_walk, but on keys instead of values.
     * @param array    $array
     * @param callable $callback
     */
    public static function KeyWalk(array &$array, callable $callback) {
        $array = static::KeyMap($callback, $array);
        return true;
    }

    /**
     * Flattens an array.
     * Example : [0 => ['banana', 'apple'], 1 => ['watermelon']] becomes ['banana', 'apple', 'watermelon']
     * @param array $array
     * @return array
     */
    public static function Flatten(array $array) {
        $out    =   [];
        foreach ($array AS $key => $value)
            if (is_array($value))
                $out        =   array_merge((array) $out, static::Flatten($value));
            else
                $out[$key]  =   $value;
        return $out;

    }

    /**
     * Inserts a value before a specific key.
     * @param $array
     * @param $key
     * @param null $data
     * @return array
     */
    public static function InsertBeforeKey($array, $key, $data = null) {
        if (($offset = array_search($key, array_keys($array))) === false)
            $offset = count($array);
        return array_merge(array_slice($array, 0, $offset), (array) $data, array_slice($array, $offset));
    }

    /**
     * Inserts a value after a specific key.
     * @param      $array
     * @param      $key
     * @param null $data
     * @return array
     */
    public static function InsertAfterKey($array, $key, $data = null) {
        if (($offset = array_search($key, array_keys($array))) === false)
            $offset = count($array);
        return array_merge(array_slice($array, 0, $offset + 1), (array) $data, array_slice($array, $offset));
    }
}