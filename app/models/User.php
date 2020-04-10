<?php

  class User {
    
    private $db;

    public function __construct() {
      global $registry;
      $this->db = $registry->get('db');
    }

    public function register($data) {
      $this->db->query('INSERT INTO users(username, firstName, lastName, email, password, gender, telephone) VALUES(:username, :fname, :lname, :email, :pass, :gender, :phone)');
      $this->db->bind(':username', $data['username']);
      $this->db->bind(':fname', $data['firstName']);
      $this->db->bind(':lname', $data['lastName']);
      $this->db->bind(':email', $data['email']);
      $this->db->bind(':pass', $data['pass']);
      $this->db->bind(':gender', $data['gender']);
      $this->db->bind(':phone', $data['phone']);

      if ($this->db->execute()) {
        return true;
      } else {
        return false;
      }
    }

    public function login($data) {
      $this->db->query('SELECT * FROM users WHERE username = :username');
      $this->db->bind(':username', $data['username']);
      $row = $this->db->single();
      if (password_verify($data['password'], $row->password)) {
        return $row;
      } else {
        return false;
      }
    }

    public function usernameExist($user) { // if the username exist will return true(the row)
      $this->db->query('SELECT * FROM users WHERE username = :user');
      $this->db->bind(':user', $user);
      $row = $this->db->single();
      if ($this->db->rowCount() > 0) {
        return $row;
      } else {
        return false;
      }
    }

    public function emailExist($email) { // if the email exist will return true
      $this->db->query('SELECT * FROM users WHERE email = :email');
      $this->db->bind(':email', $email);
      $this->db->execute();
      if ($this->db->rowCount() > 0) {
        return true;
      } else {
        return false;
      }
    }

    public function update($data) {
      if (empty($data['photo'])) {
        if (empty($data['password'])) {
          $this->db->query('UPDATE users SET firstName = :fname, lastName = :lname, email = :email, telephone = :phone WHERE username = :username');
          $this->db->bind(':fname', $data['firstName']);
          $this->db->bind(':lname', $data['lastName']);
          $this->db->bind(':email', $data['email']);
          $this->db->bind(':phone', $data['phone']);
          $this->db->bind(':username', $data['username']);
        } else {
          $this->db->query('UPDATE users SET firstName = :fname, lastName = :lname, email = :email, password = :pass, telephone = :phone WHERE username = :username');
          $this->db->bind(':fname', $data['firstName']);
          $this->db->bind(':lname', $data['lastName']);
          $this->db->bind(':email', $data['email']);
          $this->db->bind(':pass', $data['password']);
          $this->db->bind(':phone', $data['phone']);
          $this->db->bind(':username', $data['username']);
        }
    } else {
      if (empty($data['password'])) {
        $this->db->query('UPDATE users SET firstName = :fname, lastName = :lname, email = :email, telephone = :phone, avatarName = :avatar WHERE username = :username');
        $this->db->bind(':fname', $data['firstName']);
        $this->db->bind(':lname', $data['lastName']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':avatar', $data['photo']);
      } else {
        $this->db->query('UPDATE users SET firstName = :fname, lastName = :lname, email = :email, password = :pass, telephone = :phone, avatarName = :avatar WHERE username = :username');
        $this->db->bind(':fname', $data['firstName']);
        $this->db->bind(':lname', $data['lastName']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':pass', $data['password']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':avatar', $data['photo']);
      }
    }
      if ($this->db->execute()) {
        return true;
      } else {
        return false;
      }
    }

    public function addService($date) {
      $this->db->query('INSERT INTO bikeservicing(username, serviceDate) VALUES(:username, :date)');
      $this->db->bind(':date', $date);
      $this->db->bind(':username', getUsername());
      if ($this->db->execute()) {
        return true;
      } else {
        return false;
      }
    }


    public function allServiceForUsername($username) {
      $this->db->query('SELECT * FROM bikeservicing WHERE username = :username ORDER BY serviceDate DESC');
      $this->db->bind(':username', $username);
      return $this->db->resultSet();
    }

    public function checkServiceDate($date) {
      $this->db->query('SELECT * FROM bikeservicing WHERE serviceDate = :date');
      $this->db->bind(':date', $date);
      $this->db->execute();
      if ($this->db->rowCount() > 0) {
        return false;
      } else {
        return true;
      }
    }
    

  }