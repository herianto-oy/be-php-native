<?php
class User {
    public $id;
    public $name;
    public $email;
    public $password;
    public $access_token;
    public $image;

    public static function getUserById($db, $id){
        $query = "SELECT * FROM user WHERE id=?";
        $stmt = $db->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'User'); 
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result;
    }

    public static function getUserByEmail($db, $data){
        $query = "SELECT * FROM user WHERE email=:email";
        $stmt = $db->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'User'); 
        $stmt->execute(['email' => $data['email']]);
        $result = $stmt->fetch();
        return $result;
    }

    public static function getUserByEmailAndPassword($db, $data){
        $query = "SELECT * FROM user WHERE email=:email AND password=:password";
        $stmt = $db->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'User'); 
        $stmt->execute($data);
        $result = $stmt->fetch();
        return $result;
    }

    public static function getAllUser($db){
        $query = "SELECT * FROM user";
        $stmt = $db->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'User'); 
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }

    public static function createUpdateUser($db, $data){
        if(isset($data['id'])){
            $sql = "UPDATE user SET name=?, email=? WHERE id=?";
            $stmt= $db->prepare($sql);
            $stmt->execute([$data['name'], $data['email'],  $data['id']]);
            return User::getUserById($db, $data['id']);
        } else {
            $sql = "INSERT INTO user (name, email, password) VALUES (:name,:email,:password)";
            $stmt= $db->prepare($sql);
            $stmt->execute($data);
            $id = $db->lastInsertId();
            return User::getUserById($db, $id);
        }   
    }

    public static function deleteUserById($db, $id){
        $query = "DELETE FROM user WHERE id=?";
        $stmt = $db->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'User'); 
        $result = $stmt->execute([$id]);
        return $result;
    }

    public static function updateUserToken($db, $token, $data){
        if(isset($data->id)){
            $sql = "UPDATE user SET access_token=? WHERE id=?";
            $stmt= $db->prepare($sql);
            $x = $stmt->execute([$token, $data->id]);
            return User::getUserById($db, $data->id);
        }   
    }

    public static function updateUserImage($db, $data){
        if(isset($data['id'])){
            $sql = "UPDATE user SET image=? WHERE id=?";
            $stmt= $db->prepare($sql);
            $x = $stmt->execute([$data['image'], $data['id']]);
            return User::getUserById($db, $data['id']);
        }   
    }

    public static function checkToken($db, $token){
        $sql = "SELECT id FROM user WHERE access_token=?";
        $stmt= $db->prepare($sql);
        $x = $stmt->execute([$token]);
        $result = $stmt->fetch();

        if($result){
            return true;
        } else {
            return false;
        } 
    }
}