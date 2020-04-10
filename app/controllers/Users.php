<?php

  class Users extends Controller {
    public function __construct() {
      $this->userModel = $this->model('User');
    }

    public function register() {
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = [
          'firstName' => $_POST['userFirstName'],
          'lastName' => $_POST['userLastName'],
          'username' => $_POST['username'],
          'email' => $_POST['email'],
          'pass' => $_POST['firstPassword'],
          'repass' => $_POST['secondPassowrd'],
          'phone' => $_POST['phone'],
          'gender' => $_POST['gender'],
          'firstName_err' => '',
          'lastName_err' => '',
          'username_err' => '',
          'email_err' => '',
          'pass_err' => '',
          'repass_err' => '',
          'phone_err' => '',
        ];
        if (empty($data['firstName'])) {
          $data['firstName_err'] = 'Please fill the first name field';
        }
        if (empty($data['lastName'])) {
          $data['lastName_err'] = 'Please fill the last name field';
        }
        if (empty($data['username'])) {
          $data['username_err'] = 'Please fill the username field';
        } elseif ($this->userModel->usernameExist($data['username'])) {
          $data['username_err'] = 'The username exist';
        }
        if (empty($data['email'])) {
          $data['email_err'] = 'Please fill the email field';
        } elseif ($this->userModel->emailExist($data['email'])) {
          $data['email_err'] = 'The email exist';
        }
        if (empty($data['pass'])) {
          $data['pass_err'] = 'Please fill the password field';
          $data['repass_err'] = 'Please fill the password field';
        } elseif ($data['pass'] != $data['repass']) {
          $data['repass_err'] = 'Password doesn\'t match';
        }
        if (empty($data['phone'])) {
          $data['phone_err'] = 'Please fill the phone number field';
        }
        if (!empty($data['firstName_err']) || !empty($data['lastName_err']) || !empty($data['email_err']) || !empty($data['pass_err']) || !empty($data['repass_err']) || !empty($data['phone_err'])) {
          $this->view('users/register', $data);
        } else {
          $data['pass'] = password_hash($data['pass'], PASSWORD_DEFAULT);
          if ($this->userModel->register($data)) {
            $_SESSION['username'] = $data['username'];
            redirect('pages');
          } else {
            die('something went wrong');
          }
        }
      } else {
        $data = [
          'firstName' => '',
          'lastName' => '',
          'username' => '',
          'email' => '',
          'pass' => '',
          'repass' => '',
          'phone' => '',
          'gender' => '1',
          'firstName_err' => '',
          'lastName_err' => '',
          'username_err' => '',
          'email_err' => '',
          'pass_err' => '',
          'repass_err' => '',
          'phone_err' => '',
        ];
        $this->view('users/register', $data);
      }

    }

    public function login() {
      if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = [
          'username' => $_POST['username'],
          'password' => $_POST['password'],
          'username_err' => '',
          'password_err' => ''
        ];
        if (empty($data['username'])) {
          $data['username_err'] = 'please fill the email field';
        } elseif (!$this->userModel->usernameExist($data['username'])) {
          $data['username_err'] = 'The username not exist';
        }
        if (empty($data['password'])) {
          $data['password_err'] = 'please fill the password field';
        }
        if (empty($data['password_err']) && empty($data['username_err'])) {
          if ($this->userModel->login($data)) {
            $_SESSION['username'] = $data['username'];
            redirect('pages');
          } else {
            $data['password_err'] = 'the password is wrong';
            $this->view('users/login', $data);
          }
        } else {
          $this->view('users/login', $data);
        }
      } else {
        $data = [
          'username' => '',
          'password' => '',
          'username_err' => '',
          'password_err' => ''
        ];
        $this->view('users/login', $data);
      }
    }

    public function logout() {
      unset($_SESSION['username']);
      session_destroy();
      redirect('users/register');
    }

    public function edit() {
      if(isLoggedIn()) {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
          $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
          $data = [
            'firstName' => $_POST['userFirstName'],
            'lastName' => $_POST['userLastName'],
            'username' => getUsername(),
            'email' => $_POST['email'],
            'password' => $_POST['firstPassword'],
            'phone' => $_POST['phone'],
            'photo' => '',
            'gender' => $_POST['gender'],
            'firstName_err' => '',
            'lastName_err' => '',
            'username_err' => '',
            'email_err' => '',
            'phone_err' => '',
          ];
          $photoName = $_FILES['photo']['name'];
          $photoSize = $_FILES['photo']['name'];
          $photoTmp = $_FILES['photo']['tmp_name'];
          $photoType = $_FILES['photo']['type'];

          $photoAllowedExtention = array('jpeg', 'jpg', 'png', 'gif');
          $photoExtention = explode('.', $photoName);
          $photoExtention = end($photoExtention);
          $photoExtention = strtolower($photoExtention);
          if (empty($data['firstName'])) {
            $data['firstName_err'] = 'Please fill the first name field';
          }
          if (empty($data['lastName'])) {
            $data['lastName_err'] = 'Please fill the last name field';
          }
          if (empty($data['email'])) {
            $data['email_err'] = 'Please fill the email field';
          }
          if (empty($data['phone'])) {
            $data['phone_err'] = 'Please fill the phone number field';
          }
          if (!in_array($photoExtention, $photoAllowedExtention) && !empty($photoName)) {
            $data['photo_err'] = 'Sorry, The Extention Not Allowed :(';
          }

          if (empty($data['firstName_err']) && empty($data['lastName_err']) && empty($data['email_err']) && empty($data['phone_err']) && empty($data['photo_err'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            if (!empty($photoName)) {
              $randomNum = rand(0, 100000);
              move_uploaded_file($photoTmp, 'img/uploads/' . $randomNum . '_' . $photoName);
              $data['photo'] = $randomNum . '_' . $photoName;
            }
            if ($this->userModel->update($data)) {
              flash('sucess-edit', 'Changes Saved Successfully');
              redirect('users/edit');
            } else {
              flash('error', 'something went wrong', 'alert alert-danger');
              redirect('pages/index');
            }
          } else {
            $this->view('users/edit', $data);
          }
  
  
  
        } else {
          $row = $this->userModel->usernameExist($_SESSION['username']);
          $data= [
            'firstName' => $row->firstName,
            'lastName' => $row->lastName,
            'username' => $row->username,
            'email' => $row->email,
            'pass' => '',
            'phone' => $row->telephone,
            'gender' => $row->gender,
            'firstName_err' => '',
            'lastName_err' => '',
            'username_err' => '',
            'email_err' => '',
            'pass_err' => '',
            'repass_err' => '',
            'phone_err' => '',
          ];
          $this->view('users/edit', $data);
        }

      } else {
        flash('error', 'Sorry, You need to login first', 'alert alert-danger');
        redirect('pages/index');
      }

    }
    public function service() {
      if (isLoggedIn()) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
          $data = [
            'serviceDate' => $_POST['serviceDateTime'],
            'serviceDate_err' => '',
            'services' => $this->userModel->allServiceForUsername(getUsername())
          ];
          $dateTime =  $_POST['serviceDateTime'];
          $date = substr($dateTime, 0, 10);
          $time = substr($dateTime, 11, 5);
          $dateTime = $date . ' ' . $time . ':00';
          if($this->userModel->checkServiceDate($dateTime)) {
            $this->userModel->addService($dateTime);
            flash('service-added', 'Service Added Successfully :)');
            redirect('users/service');
          } else {
            flash('service-added', 'This time isn\'t available try another time :(', 'alert alert-danger');
            $this->view('users/service', $data);
          }
        } else {
          $data = [
            'serviceDate' => date("Y-m-d") . 'T' . date("H:i") ,
            'serviceDate_err' => '',
            'services' => $this->userModel->allServiceForUsername(getUsername())
          ];
          $this->view('users/service', $data);
        }
      } else {
        die("sorry you are not allow to get to this page");
      }

    }
  }