<?php

class Router
{   
    private array $handlers;
    private $db;

    function __construct($db) {
        $this->db = $db;
    }

    public function get($path, $handler, $auth=false)
    {
        $this->addHandler('GET', $path, $handler, $auth);
    }
    
    public function post($path, $handler, $auth=false)
    {
        $this->addHandler('POST', $path, $handler, $auth);
    }

    public function delete($path, $handler, $auth=false)
    {
        $this->addHandler('DELETE', $path, $handler, $auth);
    }

    public function put($path, $handler, $auth = false)
    {
        $this->addHandler('PUT', $path, $handler, $auth);
    }

    public function addHandler($method, $path, $handler, $auth = false){
        $this->handlers[$method.$path] = [
            'path' => $path,
            'method' => $method,
            'handler' => $handler,
            'auth' => $auth
        ];
    }

    public function start(){
        $requestUri = parse_url($_SERVER['REQUEST_URI']);
        $bearer = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION']: false; 
        $requestPath = $requestUri['path'];
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $callback = null;
        $unauthorized = false;

        foreach ($this->handlers as $handler) {
            if($handler['path'] === $requestPath && $handler['method'] === $requestMethod){
                if($handler['auth']){
                    if($bearer){
                        $token = explode(" ", $bearer);
                        $oke = User::checkToken($this->db, $token[1]);
                        if($oke){
                            $callback = $handler['handler'];
                        } else {
                            $unauthorized = true;
                            Helper::responseJson([
                                "code" => 401,
                                "data" => null,
                                "msg" => "Unauthorized!"
                            ]);
                        }
                    } else {
                        $unauthorized = true;
                        Helper::responseJson([
                            "code" => 401,
                            "data" => null,
                            "msg" => "Unauthorized!"
                        ]);
                    }
                } else {
                    $callback = $handler['handler'];
                }
            }
        }

        if($callback) {
            $body = json_decode(file_get_contents('php://input'), true); 
            call_user_func_array($callback, [['params' =>array_merge($_GET, $_POST), 'body' => $body]]);
        } else if (!$unauthorized) {
            header("Content-Type: application/json");
            http_response_code(404);
            echo json_encode([
                "code" => 404,
                "data" => null,
                "msg" => "Not Found"
            ]);
        }
    }
}

?>
