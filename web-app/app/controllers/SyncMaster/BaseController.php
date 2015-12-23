<?php

namespace SyncMaster;

use SyncMaster\Message;
use SyncMaster\Contact;
use Response;

class BaseController extends \Controller {

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

    protected function getFriendlyString($string) {
        $lowerCaseString = strtolower($string);
        $lowerCaseAsciiString = strtr($lowerCaseString, BaseController::$VIETNAMESE_TO_ASCII_MAP);
        //remove duplicated spaces
        $removedDuplicatedSpacesString = preg_replace("/\s+/i", " ", $lowerCaseAsciiString);
        //remove special character
        $retVal = preg_replace("/[^a-z0-9]/i", "-", $removedDuplicatedSpacesString);
        return $retVal;
    }

    public function findMessages() {
        $result = [];
        $messages = Message::orderBy("create_time", "desc")->get();
        $result["status"] = "successful";
        $result["result"] = $messages;
        return Response::json($result);
    }

    public function findContacts($keyword) {
        $result = [];
        $keyword = $this->getFriendlyString($keyword);
        $query = Contact::getQuery();
        if ($keyword != "null") {
            $query->where("name", "LIKE", "%" . $keyword . "%")
                    ->orWhere("phone", "LIKE", "%" . $keyword . "%")
                    ->orWhere("email", "LIKE", "%" . $keyword . "%");
        }
        $contacts = $query->get();
        $result["status"] = "successful";
        $result["result"] = $contacts;
        return Response::json($result);
    }

}
