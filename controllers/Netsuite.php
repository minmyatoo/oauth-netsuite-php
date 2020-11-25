<?php
defined('BASEPATH') or exit('No direct script access allowed');
define("NETSUITE_URL", 'https:///*Host*/.restlets.api.netsuite.com/app/site/hosting/restlet.nl');
define("NETSUITE_SCRIPT_ID", '/*ID*/');
define("NETSUITE_DEPLOY_ID", '1');
define("NETSUITE_ACCOUNT", '/*NS Account*/');
define("NETSUITE_CONSUMER_KEY", '/*Consumer Key*/');
define("NETSUITE_CONSUMER_SECRET", '/*Consumer Secret*/');
define("NETSUITE_TOKEN_ID", '/*Token*/');
define("NETSUITE_TOKEN_SECRET", '/*Token Secret*/');
class Netsuite extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('crud_model');
        $this->load->model('login_model');
        $this->load->model('netsuite_model');
    }
    public function index()
    {
        if ($this->session->userdata('user_login_access') != 1)
            redirect(base_url() . 'login', 'refresh');
        if ($this->session->userdata('user_login_access') == 1)
            $data = array();
        redirect('crud/dashboard');
    }
    public function employees($details)
    {
        $data_string = json_encode($details);
        $oauth_nonce = md5(mt_rand());
        //echo $oauth_nonce;
        $oauth_timestamp = time();
        //echo 'Hello' . $oauth_timestamp;
        $oauth_signature_method = 'HMAC-SHA1';
        $oauth_version = "1.0";
        $base_string =
            "GET&" . urlencode(NETSUITE_URL) . "&" .
            urlencode(
                "deploy=" . NETSUITE_DEPLOY_ID
                . "&oauth_consumer_key=" . NETSUITE_CONSUMER_KEY
                . "&oauth_nonce=" . $oauth_nonce
                . "&oauth_signature_method=" . $oauth_signature_method
                . "&oauth_timestamp=" . $oauth_timestamp
                . "&oauth_token=" . NETSUITE_TOKEN_ID
                . "&oauth_version=" . $oauth_version
                . "&realm=" . NETSUITE_ACCOUNT
                . "&script=" . NETSUITE_SCRIPT_ID
            );
        $sig_string = urlencode(NETSUITE_CONSUMER_SECRET) . '&' . urlencode(NETSUITE_TOKEN_SECRET);
        $signature = base64_encode(hash_hmac("sha1", $base_string, $sig_string, true));
        $auth_header = "OAuth "
            . 'oauth_signature="' . rawurlencode($signature) . '", '
            . 'oauth_version="' . rawurlencode($oauth_version) . '", '
            . 'oauth_nonce="' . rawurlencode($oauth_nonce) . '", '
            . 'oauth_signature_method="' . rawurlencode($oauth_signature_method) . '", '
            . 'oauth_consumer_key="' . rawurlencode(NETSUITE_CONSUMER_KEY) . '", '
            . 'oauth_token="' . rawurlencode(NETSUITE_TOKEN_ID) . '", '
            . 'oauth_timestamp="' . rawurlencode($oauth_timestamp) . '", '
            . 'realm="' . rawurlencode(NETSUITE_ACCOUNT) . '"';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, NETSUITE_URL . '?&script=' . NETSUITE_SCRIPT_ID . '&deploy=' . NETSUITE_DEPLOY_ID . '&realm=' . NETSUITE_ACCOUNT);
        curl_setopt($ch, CURLOPT_POST, "GET");
        /*Post*/
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $auth_header,
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string)
        ]);
        //echo curl_exec($ch);
        return json_decode(curl_exec($ch));
        curl_close($ch);
    }

    public function employee_list()
    {
        if ($this->session->userdata('user_login_access') != False) {
            $data = array();
            $empdb = array();
            $empdb = $this->employees('');
            $userid = $this->session->userdata('user_login_id');
            $data['todolist'] = $this->crud_model->getTodoInfo($userid);
            $data['employees'] = $empdb;
            $this->load->view('netsuite/employees_list', $data);
            //echo json_encode($data['productlist']);
        } else {
            redirect(base_url(), 'refresh');
        }
    }
    /*Post Employee*/
    public function employee_post($details)
    {
        $data_string = json_encode($details);
        $oauth_nonce = md5(mt_rand());
        $oauth_timestamp = time();
        //echo 'Hello' . $oauth_timestamp;
        $oauth_signature_method = 'HMAC-SHA1';
        $oauth_version = "1.0";
        $base_string =
            "POST&" . urlencode(NETSUITE_URL) . "&" .
            urlencode(
                "deploy=" . NETSUITE_DEPLOY_ID
                . "&oauth_consumer_key=" . NETSUITE_CONSUMER_KEY
                . "&oauth_nonce=" . $oauth_nonce
                . "&oauth_signature_method=" . $oauth_signature_method
                . "&oauth_timestamp=" . $oauth_timestamp
                . "&oauth_token=" . NETSUITE_TOKEN_ID
                . "&oauth_version=" . $oauth_version
                . "&realm=" . NETSUITE_ACCOUNT
                . "&script=" . NETSUITE_SCRIPT_ID
            );
        $sig_string = urlencode(NETSUITE_CONSUMER_SECRET) . '&' . urlencode(NETSUITE_TOKEN_SECRET);
        $signature = base64_encode(hash_hmac("sha1", $base_string, $sig_string, true));
        $auth_header = "OAuth "
            . 'oauth_signature="' . rawurlencode($signature) . '", '
            . 'oauth_version="' . rawurlencode($oauth_version) . '", '
            . 'oauth_nonce="' . rawurlencode($oauth_nonce) . '", '
            . 'oauth_signature_method="' . rawurlencode($oauth_signature_method) . '", '
            . 'oauth_consumer_key="' . rawurlencode(NETSUITE_CONSUMER_KEY) . '", '
            . 'oauth_token="' . rawurlencode(NETSUITE_TOKEN_ID) . '", '
            . 'oauth_timestamp="' . rawurlencode($oauth_timestamp) . '", '
            . 'realm="' . rawurlencode(NETSUITE_ACCOUNT) . '"';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, NETSUITE_URL . '?&script=' . NETSUITE_SCRIPT_ID . '&deploy=' . NETSUITE_DEPLOY_ID . '&realm=' . NETSUITE_ACCOUNT);
        curl_setopt($ch, CURLOPT_POST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $auth_header,
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string)
        ]);
        echo curl_exec($ch);
        return json_decode(curl_exec($ch));
        curl_close($ch);
    }
    public function addEmployeeData()
    {
        if ($this->session->userdata('user_login_access') != False) {
            $catid = $this->input->post('cat_id');
            $category = $this->input->post('catname');
            $status = $this->input->post('catstatus');
            $data = array();
            $data = array(
                'id' => 'Hello Codeigniter',
            );
            $postdata = $this->employee_post($data);
            //echo $postdata;
        } else {
            redirect(base_url(), 'refresh');
        }
    }
    public function add_employee()
    {
        if ($this->session->userdata('user_login_access') != False) {
            $data = array();
            $empdb = array();
            $empdb = $this->employees('');
            $userid = $this->session->userdata('user_login_id');
            $this->load->view('netsuite/add_employee', $data);
            //echo json_encode($data['productlist']);
        } else {
            redirect(base_url(), 'refresh');
        }
    }
    /*Add Employee*/
    public function addEmployeeInfo()
    {
        if ($this->session->userdata('user_login_access') != False) {
            /*Custom Random password generator*/
            function rand_password($length)
            {
                $str = "";
                $chars = "abcdefghijklmanopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                $size = strlen($chars);
                for ($i = 0; $i < $length; $i++) {
                    $str .= $chars[rand(0, $size - 1)];
                }
                return $str;
            }
            /*Set password length*/
            $pass_hash = sha1(rand_password(7));
            /*password length 7 & convert to Secure Hash Algorithm 1(SHA1)*/
            /*custom user id generator*/
            $userid = 'U' . rand(500, 1000);
            /*generate random user ID from 500 to 1000*/
            $username = $this->input->post('name');
            $email = $this->input->post('email');
            $contact = $this->input->post('contact');
            $address = $this->input->post('address');
            $dob = $this->input->post('dob');
            $country = $this->input->post('country');
            $role = $this->input->post('role');
            $gender = $this->input->post('gender');
            $date = date('Y-m-d');
            $data = array();
            $data = array(
                'user_id' => $userid,
                'full_name' => $username,
                'email' => $email,
                'password' => $pass_hash,
                'address' => $address,
                'dob' => $dob,
                'contact' => $contact,
                'gender' => $gender,
                'country' => $country,
                'status' => 'ACTIVE',
                'user_type' => $role,
                'created_on' => $date
            );
            //$success = $this->crud_model->addUserInfo($data);
            $this->employee_post($data);
            $response['status'] = 'success';
            $response['message'] = "Successfully Created";
            $this->output->set_output(json_encode($response));
            $this->session->set_flashdata('feedback', 'Successfully Created');
        } else {
            redirect(base_url(), 'refresh');
        }
    }
}
/*End crud controller*/
