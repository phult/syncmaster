<?php

namespace Backend;

use Controller;
use View;
use Image;
use Parameter;
use Redirect;
use Session;

class BaseController extends Controller {

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

    protected function getFriendlyString( $string ) {
        $lowerCaseString = strtolower($string);
        $lowerCaseAsciiString = strtr($lowerCaseString, BaseController::$VIETNAMESE_TO_ASCII_MAP);
        //remove duplicated spaces
        $removedDuplicatedSpacesString = preg_replace("/\s+/i", " ", $lowerCaseAsciiString);
        //remove special character
        $retVal = preg_replace("/[^a-z0-9]/i", "-", $removedDuplicatedSpacesString);
        return $retVal;
    }

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout() {
        if ( !is_null($this->layout) ) {
            $this->layout = View::make($this->layout);
        }
    }

    protected function recordsCountToPagesCount( $recordsCount, $pageSize ) {
        $retVal = (int) ($recordsCount / $pageSize);
        if ( $recordsCount % $pageSize > 0 ) {
            $retVal++;
        }
        //return
        return $retVal;
    }

    /**
     * Store image and create images with differrent sizes (l,m,s)
     * This method will create 1 subdirectory for image storage each 10 days
     * @param type $image
     * @param type array|string $sizes
     */
    protected function storeImage( $image, $type = "parameter", $title = "" ) {
        //get-image-root-directory, create directory if it doesn't exist
        $rootImageDirectoryPath = $this->getParameter("rootImageDirectory.path", public_path() . DIRECTORY_SEPARATOR . "upload");
        if ( !file_exists($rootImageDirectoryPath) ) {
            mkdir($rootImageDirectoryPath);
        }
        if ( $type == "product" ) {
            $imageDirectoryNames = ["small", "medium", "large" ];
        } elseif ( $type == "news" ) {
            $imageDirectoryNames = ["news" ];
        } else {
            $imageDirectoryNames = ["other" ];
        }
        $fileName = $this->getFriendlyString($title) . '-' . date("dmYHis") . '.' . strtolower($image->getClientOriginalExtension());
        $image->move($rootImageDirectoryPath . DIRECTORY_SEPARATOR . "original", $fileName);
        foreach ( $imageDirectoryNames as $imageDirectoryName ) {
            if ( $type == "product" ) {
                $imageDirectoryPath = $rootImageDirectoryPath . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . $imageDirectoryName;
            } else {
                $imageDirectoryPath = $rootImageDirectoryPath . DIRECTORY_SEPARATOR . $imageDirectoryName;
            }
            if ( !file_exists($imageDirectoryPath) ) {
                mkdir($imageDirectoryPath);
            }
            switch ( $imageDirectoryName ) {
                case "small": {
                        $maxWidth = 163;
                        $maxHeight = 131;
                        break;
                    }
                case "medium": {
                        $maxWidth = 150;
                        $maxHeight = 200;
                        break;
                    }
                case "large": {
                        $maxWidth = 230;
                        $maxHeight = 250;
                        continue;
                    }
                case "news": {
                        $maxWidth = 140;
                        $maxHeight = 140;
                        break;
                    }
                default : {
                        $maxWidth = "";
                        $maxHeight = "";
                    }
            }
            if ( !$maxWidth && !$maxHeight ) {
                return $fileName;
            }
            $originalImage = Image::make($rootImageDirectoryPath . DIRECTORY_SEPARATOR . "original" . DIRECTORY_SEPARATOR . $fileName);
            $currentWidth = $originalImage->width();
            $currentHeight = $originalImage->height();
            if ( $currentWidth > $maxWidth || $currentHeight > $maxHeight ) {
                $ratioX = $currentWidth / $maxWidth;
                $ratioY = $currentHeight / $maxHeight;
                if ( $ratioX > $ratioY ) {
                    $originalImage->widen($maxWidth);
                } else {
                    $originalImage->heighten($maxHeight);
                }
            }
            $originalImage->save($imageDirectoryPath . DIRECTORY_SEPARATOR . $fileName);
        }
        //return
        return $fileName;
    }

    protected function getParameter( $key, $defaultValue = null ) {
        $retVal = $defaultValue;
        $parameter = Parameter::where("key2", "=", $key)
                ->first();
        if ( $parameter != null ) {
            $retVal = $parameter->value2;
        }
        return $retVal;
    }

    protected function getUser() {
        if ( Session::has("user") ) {
            return Session::get("user");
        } else {
            return Redirect::action("Frontend\HomeController@index", array( 'login' => true ));
        }
    }

    /**
     * 
     * @param type $url
     * @param type $params :parameters as string. Example: id=1&pageId=0&pageSize=10
     * @param type $method
     */
    protected function triggerAsyncRequest( $url, $params = "", $method = "get" ) {
        $channel = curl_init();
        curl_setopt($channel, CURLOPT_URL, $url);
        curl_setopt($channel, CURLOPT_POST, $method == "post" || $method == "POST" ? true : false);
        curl_setopt($channel, CURLOPT_POSTFIELDS, $params);
        curl_setopt($channel, CURLOPT_NOSIGNAL, 1);
        curl_setopt($channel, CURLOPT_TIMEOUT_MS, 200);
        curl_exec($channel);
        curl_close($channel);
    }

}
