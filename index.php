<?php
    include 'router.php';
    include 'database.php';
    include 'models.php';
    include 'helper.php';


    $database = new Database;
    $conn = $database->connect();

    if(!$conn){
        Helper::responseJson([
            "code" => 200,
            "data" => null,
            "msg" => "Database connection failed! "
        ]);
    } else {
        $router = new Router($conn);
        $router->post('/register', function($request) {
            if(isset($request['body'])){
                $checkUser = User::getUserByEmail($GLOBALS['conn'], $request['body']);
                if($checkUser){
                    Helper::responseJson([
                        "code" => 200,
                        "data" => null,
                        "msg" => "Email has been registered!"
                    ]);
                } else {
                    $request['body']['password'] = $hashing = hash('sha1', $request['body']['password']);
                    $result = User::createUpdateUser($GLOBALS['conn'], $request['body']);
                    
                    Helper::responseJson([
                        "code" => 200,
                        "data" => $result,
                        "msg" => "Create User Success!"
                    ]);
                }

            } else {
                Helper::responseJson([
                    "code" => 500,
                    "data" => null,
                    "msg" => "Create User Failed!"
                ]);
            }
        });
        $router->post('/login', function($request) {
            if(isset($request['body'])){
                if(isset($request['body']['email']) && isset($request['body']['password'])){
                    $hashing = hash('sha1', $request['body']['password']);
                    $request['body']['password'] = $hashing;
                    $result = User::getUserByEmailAndPassword($GLOBALS['conn'], $request['body']);
                    if ($result){
                        $token = base64_encode(random_bytes(50));
                        $res = User::updateUserToken($GLOBALS['conn'], $token, $result);
                        Helper::responseJson([
                            "code" => 200,
                            "data" => $res,
                            "msg" => "Login Success!"
                        ]);
                    } else {
                        Helper::responseJson([
                            "code" => 500,
                            "data" => null,
                            "msg" => "Login Failed!"
                        ]);
                    }
                } else {
                    Helper::responseJson([
                        "code" => 500,
                        "data" => null,
                        "msg" => "Login Failed!"
                    ]);
                }  
                
            } else {
                Helper::responseJson([
                    "code" => 500,
                    "data" => null,
                    "msg" => "Login Failed!"
                ]);
            }
        });
    
        $router->get('/userById', function($request) {
            $id = isset($request['params']['id']) ? $request['params']['id']: null;
            if($id){
                $result = User::getUserById($GLOBALS['conn'], $id);
                if($result){
                    Helper::responseJson([
                        "code" => 200,
                        "data" => $result,
                        "msg" => "Success!"
                    ]);
                } else {
                    Helper::responseJson([
                        "code" => 200,
                        "data" => null,
                        "msg" => "User not Found!"
                    ]);
                }
            } else {
                Helper::responseJson([
                    "code" => 200,
                    "data" => null,
                    "msg" => "User not Found!"
                ]);
            }
        }, true);
    
        $router->get('/user', function($request) {
            $result = User::getAllUser($GLOBALS['conn']);
            if($result){
                Helper::responseJson([
                    "code" => 200,
                    "data" => $result,
                    "msg" => "Success!"
                ]);
            } else {
                Helper::responseJson([
                    "code" => 200,
                    "data" => null,
                    "msg" => "User not Found!"
                ]);
            }
        }, true);
    
        $router->post('/user', function($request) {
            if(isset($request['body'])){
                $result = User::createUpdateUser($GLOBALS['conn'], $request['body']);
                Helper::responseJson([
                    "code" => 200,
                    "data" => $result,
                    "msg" => "Create or Update Success!"
                ]);
            } else {
                Helper::responseJson([
                    "code" => 500,
                    "data" => null,
                    "msg" => "Create or Update User Failed!"
                ]);
            }
        }, true);
    
        $router->delete('/user', function($request) {
            $id = isset($request['params']['id']) ? $request['params']['id']: null;
            if($id){
                $user = User::getUserById($GLOBALS['conn'], $id);
                if($user){
                    $result = User::deleteUserById($GLOBALS['conn'], $id);
                    if($result){
                        Helper::responseJson([
                            "code" => 200,
                            "data" => $user,
                            "msg" => "User has been Deteled!"
                        ]);
                    } else {
                        Helper::responseJson([
                            "code" => 200,
                            "data" => null,
                            "msg" => "User not Found!"
                        ]);
                    }
                } else {
                    Helper::responseJson([
                        "code" => 200,
                        "data" => null,
                        "msg" => "User not Found!"
                    ]);
                }
            } else {
                Helper::responseJson([
                    "code" => 200,
                    "data" => null,
                    "msg" => "User not Found!"
                ]);
            }
        }, true);
        
        $router->put('/uploadimage', function($request) {
            $id = isset($request['body']['id']) && isset($request['body']['image']);
            if($id){
                $user = User::getUserById($GLOBALS['conn'], $id);
                if($user){
                    $result = User::updateUserImage($GLOBALS['conn'], $request['body']);
                    if($result){
                        Helper::responseJson([
                            "code" => 200,
                            "data" => $result,
                            "msg" => "User Image has been Updated!"
                        ]);
                    } else {
                        Helper::responseJson([
                            "code" => 200,
                            "data" => null,
                            "msg" => "User not Found!"
                        ]);
                    }
                } else {
                    Helper::responseJson([
                        "code" => 200,
                        "data" => null,
                        "msg" => "User not Found!"
                    ]);
                }
            } else {
                Helper::responseJson([
                    "code" => 200,
                    "data" => null,
                    "msg" => "User not Found!"
                ]);
            }
        }, true);
        $router->start();
    }
    
?>
