<?php 

class ResponseResultState {
    const OK = 0;
    const ERROR = 1;
}

class ResponseContentFormat {
    const PLAIN = 0; //string
    const JSON = 1;
    const HTML = 2;
    const OBJECT = 3;
    const COLLECTION = 4; //array or dictionary
    const MIXED = 5;
}

class ResponseResult {
    protected static $StateToString 
                        = array(
                                ResponseResultState::OK => 'ok',
                                ResponseResultState::ERROR=>'error',
                            );
    protected static $FormatToString 
                        = array(
                                ResponseContentFormat::PLAIN => 'plain',
                                ResponseContentFormat::JSON => 'json',
                                ResponseContentFormat::HTML=>'html',
                                ResponseContentFormat::OBJECT=>'object',
                                ResponseContentFormat::COLLECTION=>'collection',
                                ResponseContentFormat::MIXED=>'mixed',
                            );

    public static function create($state, $format, $content) {
        $state = self::$StateToString[$state];
        $format = self::$FormatToString[$format];
        if (!isset($state) || !isset($format)) return null;

        $result 
            = array('state' => $state,
                    'content' => $content,
                    'content_format' => $format,
                );

        return json_encode($result);
    }
}