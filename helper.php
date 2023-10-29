<?php
 class Helper {
    public static function responseJson($res){
        header("Content-Type: application/json");
        http_response_code($res['code']);
        echo json_encode([
            "code" => $res['code'],
            "data" => $res['data'],
            "msg" => $res['msg']
        ]);
    }
 }