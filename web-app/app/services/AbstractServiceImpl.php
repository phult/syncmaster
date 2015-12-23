<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AbtractServiceImpl
 *
 * @author thoqbk
 */
abstract class AbstractServiceImpl {

    protected $serviceType = "internal"; //internal or rest

    const STATUS_SUCCESSFUL = "successful";
    const STATUS_FAIL = "fail";

    private static $VIETNAMESE_TO_ASCII_MAP = array(
        "à" => "a", "ả" => "a", "ã" => "a", "á" => "a", "ạ" => "a", "ă" => "a", "ằ" => "a", "ẳ" => "a", "ẵ" => "a", "ắ" => "a", "ặ" => "a", "â" => "a", "ầ" => "a", "ẩ" => "a", "ẫ" => "a", "ấ" => "a", "ậ" => "a",
        "đ" => "d",
        "è" => "e", "ẻ" => "e", "ẽ" => "e", "é" => "e", "ẹ" => "e", "ê" => "e", "ề" => "e", "ể" => "e", "ễ" => "e", "ế" => "e", "ệ" => "e",
        "ì" => 'i', "ỉ" => 'i', "ĩ" => 'i', "í" => 'i', "ị" => 'i',
        "ò" => 'o', "ỏ" => 'o', "õ" => "o", "ó" => "o", "ọ" => "o", "ô" => "o", "ồ" => "o", "ổ" => "o", "ỗ" => "o", "ố" => "o", "ộ" => "o", "ơ" => "o", "ờ" => "o", "ở" => "o", "ỡ" => "o", "ớ" => "o", "ợ" => "o",
        "ù" => "u", "ủ" => "u", "ũ" => "u", "ú" => "u", "ụ" => "u", "ư" => "u", "ừ" => "u", "ử" => "u", "ữ" => "u", "ứ" => "u", "ự" => "u",
        "ỳ" => "y", "ỷ" => "y", "ỹ" => "y", "ý" => "y", "ỵ" => "y",
        "À" => "A", "Ả" => "A", "Ã" => "A", "Á" => "A", "Ạ" => "A", "Ă" => "A", "Ằ" => "A", "Ẳ" => "A", "Ẵ" => "A", "Ắ" => "A", "Ặ" => "A", "Â" => "A", "Ầ" => "A", "Ẩ" => "A", "Ẫ" => "A", "Ấ" => "A", "Ậ" => "A",
        "Đ" => "D",
        "È" => "E", "Ẻ" => "E", "Ẽ" => "E", "É" => "E", "Ẹ" => "E", "Ê" => "E", "Ề" => "E", "Ể" => "E", "Ễ" => "E", "Ế" => "E", "Ệ" => "E",
        "Ì" => "I", "Ỉ" => "I", "Ĩ" => "I", "Í" => "I", "Ị" => "I",
        "Ò" => "O", "Ỏ" => "O", "Õ" => "O", "Ó" => "O", "Ọ" => "O", "Ô" => "O", "Ồ" => "O", "Ổ" => "O", "Ỗ" => "O", "Ố" => "O", "Ộ" => "O", "Ơ" => "O", "Ờ" => "O", "Ở" => "O", "Ỡ" => "O", "Ớ" => "O", "Ợ" => "O",
        "Ù" => "U", "Ủ" => "U", "Ũ" => "U", "Ú" => "U", "Ụ" => "U", "Ư" => "U", "Ừ" => "U", "Ử" => "U", "Ữ" => "U", "Ứ" => "U", "Ự" => "U",
        "Ỳ" => "Y", "Ỷ" => "Y", "Ỹ" => "Y", "Ý" => "Y", "Ỵ" => "Y"
    );

    protected function getFriendlyString($lowerCaseAsciiString) {
        $lowerCaseString = strtolower($lowerCaseAsciiString);
        $removedVietnameseCharacters = strtr($lowerCaseString, AbstractServiceImpl::$VIETNAMESE_TO_ASCII_MAP);
        //remove duplicated spaces
        $removedDuplicatedSpacesString = preg_replace("/\s+/i", " ", $removedVietnameseCharacters);
        //remove special character
        $retVal = preg_replace("/[^a-z0-9]/i", "-", $removedDuplicatedSpacesString);
        return $retVal;
    }

    protected function recordsCountToPagesCount($recordsCount, $pageSize) {
        $retVal = (int) ($recordsCount / $pageSize);
        if ($recordsCount % $pageSize > 0) {
            $retVal++;
        }
        //return
        return $retVal;
    }

    protected function executeQuery($query, $metric = null, $pageId = 0, $pageSize = 0) {
        if ($this->serviceType == "internal") {
            if ($metric == "count") {
                $retVal = $query->count();
            } else {
                if ($pageSize != 0) {
                    $query->forPage($pageId + 1, $pageSize);
                }
                $retVal = $query->get();
            }
        } else if ($metric == "rest") {
            $retVal = array();
            $recordsCount = $query->count();
            if ("count" == $metric) {
                $retVal["result"] = $recordsCount;
            } else {
                if ($pageSize != 0) {
                    $query->forPage($pageId + 1, $pageSize);
                }
                $result = $query->get();
                //build result
                $retVal["result"] = $result;
                $retVal["pageId"] = $pageId;
                $retVal["recordsCount"] = $recordsCount;
                if ($pageSize != 0) {
                    $pagesCount = $this->recordsCountToPagesCount($recordsCount, $pageSize);
                    $retVal["pagesCount"] = $pagesCount;
                }
            }
            $retVal["status"] = AbstractServiceImpl::STATUS_SUCCESSFUL;
        }else{
            throw "Invalid service type value, require internal/rest";
        }
        //return
        return $retVal;
    }

    protected function getFilterValue($filter, $key, $default = null) {
        $retVal = $default;
        if (array_key_exists($key, $filter)) {
            $retVal = $filter[$key];
        }
        //return
        return $retVal;
    }

    //--------------------------------------------------------------------------
    //  Utils
}
