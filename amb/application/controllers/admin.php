<?php



if (!defined('BASEPATH'))

    exit('No direct script access allowed');



class Admin extends CI_Controller {



    function __construct() {

        parent::__construct();

        error_reporting(E_ERROR | E_PARSE);

       

        $this->load->library("pagination");

        $this->load->helper('url');

         $this->load->model("users_new");



    }

    // function __construct() {

    //     // Call the Model constructor

    //     parent::__construct();

    //     error_reporting(E_ERROR | E_PARSE);



    //     $this->load->model("users_new");

        

    //     $this->load->library("common");

    //     $this->load->library('session');

    // }



    public function index($par = NULL, $par2 = NULL) {

        $session = $this->session->userdata('user');

        if (!empty($session->id)) {

            if (!empty($par)) {

                $qry = $this->db->query("select u.*,r.pickup_adress,r.pikup_location as pickup_location,r.drop_locatoin as drop_location,r.drop_address,r.amount from rides r join users u on r.driver_id = u.id where ride_id = $par");

                $res = $qry->result_array();

                //$str = '[';

                foreach ($res as $val) {

                    //$str .= "{position:new google.maps.LatLng(" . floatval($val['latitude']) . ", " . floatval($val['longitude']) . ") , avatar:'" . $val['avatar'] . "', name:'" . $val['name'] . "', email:'" . $val['email'] . "', mobile:'" . $val['mobile'] . "'},";

                    $a[] = array("u_lat" => floatval($val['latitude']), "u_lon" => floatval($val['longitude']), "email" => $val['email'], "u_name" => $val['name'], "avatar" => $val['avatar'], "mobile" => $val['mobile']);

                }

                // $str = rtrim($str, ',');

                //$str .= ']';

                if (!empty($par2)) {

                    echo json_encode($a);

                    die;

                }

                $z = explode(',',$res[0]['pickup_location']);

                $b['res'] = $a;

                

                $b['pickup_address'] = $res[0]['pickup_adress'];

                $b['drop_address'] = $res[0]['drop_address'];

                

                $b['pickup_start'] = $z[0];

                $b['pickup_end'] = $z[1];

                 $z = explode(',',$res[0]['drop_location']);

                $b['drop_start'] = $z[0];

                $b['drop_end'] = $z[1];

                //$a['res'] = preg_replace('/"([^"]+)"\s*:\s*/', '$1:', json_encode($data));

                $this->load->view('layout/header');

                $this->load->view('layout/sidebar');

                $this->load->view('index_new', $b);

                $this->load->view('layout/footer');

            } else {

                $qry = $this->db->query("select * from users where utype = 1");

                $res = $qry->result_array();

                $str = '[';

                foreach ($res as $val) {

                    $str .= "{position:new google.maps.LatLng(" . floatval($val['latitude']) . ", " . floatval($val['longitude']) . ") , avatar:'" . $val['avatar'] . "', name:'" . $val['name'] . "', email:'" . $val['email'] . "', mobile:'" . $val['mobile'] . "'},";

                }

                $str = rtrim($str, ',');

                $str .= ']';

                $a['res'] = $str;

                //$a['res'] = preg_replace('/"([^"]+)"\s*:\s*/', '$1:', json_encode($data));

                $this->load->view('layout/header');

                $this->load->view('layout/sidebar');

                $this->load->view('admin/index', $a);

                $this->load->view('layout/footer');

            }

        } else {

            redirect($this->config->base_url());

        }

    }



    public function login() {

        if (!empty($_POST)) {

            $res = $this->db->get_where("admin", array("username" => $_POST['username'], "password" => md5($_POST['password'])))->row();

            if (!empty($res->username)) {

                unset($res->password);

                $this->session->set_userdata("user", $res);

                redirect($this->config->base_url() . 'admin/users', 'refresh');

            } else {

                redirect($this->config->base_url(), 'refresh');

            }

        } else {

            $this->load->view("admin/login");

        }

    }



    public function drivers($type = NULL) {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        if ($type == 'update') {

            unset($_POST['confirmpass']);

            unset($_POST['submit-register']);

            if (!empty($_FILES['avatar']['name'])) {

                $path = $_FILES['avatar']['name'];

                $ext = pathinfo($path, PATHINFO_EXTENSION);

                $rand = 'img_' . rand(1, 500) . rand(500, 1000) . rand(1, 500);

                $config['upload_path'] = BASEPATH . "../avatar/";

                $config['allowed_types'] = '*';

                $config['file_name'] = $rand;

                $this->load->library('upload', $config);

                $this->upload->overwrite = true;

                $this->upload->do_upload('avatar');

                $data = $this->upload->data();

                $_POST['avatar'] = $this->config->base_url() . "avatar/" . $rand . '.' . $ext;

            }

            if (empty($_POST['password'])) {

                unset($_POST['password']);

            }else{

                $_POST['password'] = md5($_POST['password']);

            }

            $this->db->where('id', $_POST['user_id']);

            $this->db->update('users', $_POST);

            $this->session->set_userdata(array("msg" => "data successfully updated", "type" => "success"));

            header('location:' . $this->config->base_url() . 'admin/drivers');

        } else {

            $config = array();

            $config["base_url"] = $this->config->base_url() . "admin/drivers";

            $config["total_rows"] = count($this->db->get_where("users", array("utype" => 1))->result());

            $config["per_page"] = 10;

            $config["uri_segment"] = 3;



            $config['full_tag_open'] = '<ul class="pagination">';

            $config['full_tag_close'] = '</ul>';

            $config['first_link'] = false;

            $config['last_link'] = false;

            $config['first_tag_open'] = '<li>';

            $config['first_tag_close'] = '</li>';

            $config['prev_link'] = '&laquo';

            $config['prev_tag_open'] = '<li class="prev">';

            $config['prev_tag_close'] = '</li>';

            $config['next_link'] = '&raquo';

            $config['next_tag_open'] = '<li>';

            $config['next_tag_close'] = '</li>';

            $config['last_tag_open'] = '<li>';

            $config['last_tag_close'] = '</li>';

            $config['cur_tag_open'] = '<li class="active"><a href="#">';

            $config['cur_tag_close'] = '</a></li>';

            $config['num_tag_open'] = '<li>';

            $config['num_tag_close'] = '</li>';



            $this->pagination->initialize($config);

            $data["links"] = $this->pagination->create_links();





            $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

            !empty($config["per_page"]) ? $this->db->limit($config["per_page"], $page) : '';

            $data["result"] = $this->db->get_where("users", array("utype" => 1))->result(); //employee is a table in the database





            $this->load->view('layout/header');

            $this->load->view('layout/sidebar');

            $this->load->view('admin/drivers', $data);

            $this->load->view('layout/footer');

        }

    }



    public function drivers_search() {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        $search_term = $this->searchterm_handler($_POST, TRUE);



        $config_src = array();

        $config_src["base_url"] = $this->config->base_url() . "admin/drivers_search";



        $str = "select * from users where utype = 1";

        if (!empty($search_term['email'])) {

            $str .= " and (email like '%" . $search_term['email'] . "%' or name like '%" . $search_term['email'] . "%')";

        }

        if ($search_term['is_active'] == '1' || $search_term['is_active'] == '0') {

            $str .= " and status = " . $search_term['is_active'] . "";

        }



        $qry = $this->db->query($str);



        $config_src["total_rows"] = count($qry->result());

        $config_src["per_page"] = 10;

        $config_src["uri_segment"] = 3;

        $choice = $config_src["total_rows"] / $config_src["per_page"];

        $config_src["num_links"] = round($choice);

//config for bootstrap pagination class integration

        $config_src['full_tag_open'] = '<ul class="pagination">';

        $config_src['full_tag_close'] = '</ul>';

        $config_src['first_link'] = false;

        $config_src['last_link'] = false;

        $config_src['first_tag_open'] = '<li>';

        $config_src['first_tag_close'] = '</li>';

        $config_src['prev_link'] = '&laquo';

        $config_src['prev_tag_open'] = '<li class="prev">';

        $config_src['prev_tag_close'] = '</li>';

        $config_src['next_link'] = '&raquo';

        $config_src['next_tag_open'] = '<li>';

        $config_src['next_tag_close'] = '</li>';

        $config_src['last_tag_open'] = '<li>';

        $config_src['last_tag_close'] = '</li>';

        $config_src['cur_tag_open'] = '<li class="active"><a href="#">';

        $config_src['cur_tag_close'] = '</a></li>';

        $config_src['num_tag_open'] = '<li>';

        $config_src['num_tag_close'] = '</li>';

        $this->pagination->initialize($config_src);

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;



        $str = "select * from users where utype = 1";

        if (!empty($search_term['email'])) {

            $str .= " and (email like '%" . $search_term['email'] . "%' or name like '%" . $search_term['email'] . "%')";

        }

        if ($search_term['is_active'] == '1' || $search_term['is_active'] == '0') {

            $str .= " and status = " . $search_term['is_active'] . "";

        }



        $str .= " LIMIT $page," . $config_src["per_page"] . "";

        $qry = $this->db->query($str);

        $data['result'] = $qry->result();



        $data['data'] = $search_term;



        $data["links"] = $this->pagination->create_links();

        $data['pg'] = $page;

        $data['chk'] = "search";



        $this->load->view('layout/header');

        $this->load->view('layout/sidebar');

        $this->load->view('admin/drivers', $data);

        $this->load->view('layout/footer');

    }







  public function Ambulance_serch() {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        $search_term = $this->searchterm_handler($_POST, TRUE);



        $config_src = array();

        $config_src["base_url"] = $this->config->base_url() . "admin/Ambulance_serch";



        $str = "select * from users where utype = 2";

        if (!empty($search_term['email'])) {

            $str .= " and (email like '%" . $search_term['email'] . "%' or name like '%" . $search_term['email'] . "%')";

        }

        if ($search_term['is_active'] == '1' || $search_term['is_active'] == '0') {

            $str .= " and status = " . $search_term['is_active'] . "";

        }
           if ($search_term['is_onlines'] == '1' || $search_term['is_onlines'] == '0') {


            $str .= " and is_online = " . $search_term['is_onlines'] . "";

        }

    // $stre = $this->db->last_query();
    // echo $stre;
    // exit;

        $qry = $this->db->query($str);



        $config_src["total_rows"] = count($qry->result());

        $config_src["per_page"] = 10;

        $config_src["uri_segment"] = 3;

        $choice = $config_src["total_rows"] / $config_src["per_page"];

        $config_src["num_links"] = round($choice);

//config for bootstrap pagination class integration

        $config_src['full_tag_open'] = '<ul class="pagination">';

        $config_src['full_tag_close'] = '</ul>';

        $config_src['first_link'] = false;

        $config_src['last_link'] = false;

        $config_src['first_tag_open'] = '<li>';

        $config_src['first_tag_close'] = '</li>';

        $config_src['prev_link'] = '&laquo';

        $config_src['prev_tag_open'] = '<li class="prev">';

        $config_src['prev_tag_close'] = '</li>';

        $config_src['next_link'] = '&raquo';

        $config_src['next_tag_open'] = '<li>';

        $config_src['next_tag_close'] = '</li>';

        $config_src['last_tag_open'] = '<li>';

        $config_src['last_tag_close'] = '</li>';

        $config_src['cur_tag_open'] = '<li class="active"><a href="#">';

        $config_src['cur_tag_close'] = '</a></li>';

        $config_src['num_tag_open'] = '<li>';

        $config_src['num_tag_close'] = '</li>';

        $this->pagination->initialize($config_src);

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;



        $str = "select * from users where utype = 2";

        if (!empty($search_term['email'])) {

            $str .= " and (email like '%" . $search_term['email'] . "%' or name like '%" . $search_term['email'] . "%')";

        }

        if ($search_term['is_active'] == '1' || $search_term['is_active'] == '0') {

            $str .= " and status = " . $search_term['is_active'] . "";

        }
         if ($search_term['is_onlines'] == '1' || $search_term['is_onlines'] == '0') {


            $str .= " and is_online = " . $search_term['is_onlines'] . "";

        }



        $str .= " LIMIT $page," . $config_src["per_page"] . "";

        $qry = $this->db->query($str);

        $data['result'] = $qry->result();



        $data['data'] = $search_term;



        $data["links"] = $this->pagination->create_links();

        $data['pg'] = $page;

        $data['chk'] = "search";



        $this->load->view('layout/header');

        $this->load->view('layout/sidebar');

        $this->load->view('admin/Ambulance', $data);

        $this->load->view('layout/footer');

    }



     public function Doctor_serch() {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        $search_term = $this->searchterm_handler($_POST, TRUE);



        $config_src = array();

        $config_src["base_url"] = $this->config->base_url() . "admin/Doctor_serch";



        $str = "select * from users where utype = 3";

        if (!empty($search_term['email'])) {

            $str .= " and (email like '%" . $search_term['email'] . "%' or name like '%" . $search_term['email'] . "%')";

        }

        if ($search_term['is_active'] == '1' || $search_term['is_active'] == '0') {

            $str .= " and status = " . $search_term['is_active'] . "";

        }
           if ($search_term['is_onlines'] == '1' || $search_term['is_onlines'] == '0') {


            $str .= " and is_online = " . $search_term['is_onlines'] . "";

        }



        $qry = $this->db->query($str);



        $config_src["total_rows"] = count($qry->result());

        $config_src["per_page"] = 10;

        $config_src["uri_segment"] = 3;

        $choice = $config_src["total_rows"] / $config_src["per_page"];

        $config_src["num_links"] = round($choice);

//config for bootstrap pagination class integration

        $config_src['full_tag_open'] = '<ul class="pagination">';

        $config_src['full_tag_close'] = '</ul>';

        $config_src['first_link'] = false;

        $config_src['last_link'] = false;

        $config_src['first_tag_open'] = '<li>';

        $config_src['first_tag_close'] = '</li>';

        $config_src['prev_link'] = '&laquo';

        $config_src['prev_tag_open'] = '<li class="prev">';

        $config_src['prev_tag_close'] = '</li>';

        $config_src['next_link'] = '&raquo';

        $config_src['next_tag_open'] = '<li>';

        $config_src['next_tag_close'] = '</li>';

        $config_src['last_tag_open'] = '<li>';

        $config_src['last_tag_close'] = '</li>';

        $config_src['cur_tag_open'] = '<li class="active"><a href="#">';

        $config_src['cur_tag_close'] = '</a></li>';

        $config_src['num_tag_open'] = '<li>';

        $config_src['num_tag_close'] = '</li>';

        $this->pagination->initialize($config_src);

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;



        $str = "select * from users where utype = 3";

        if (!empty($search_term['email'])) {

            $str .= " and (email like '%" . $search_term['email'] . "%' or name like '%" . $search_term['email'] . "%')";

        }

        if ($search_term['is_active'] == '1' || $search_term['is_active'] == '0') {

            $str .= " and status = " . $search_term['is_active'] . "";

        }
           if ($search_term['is_onlines'] == '1' || $search_term['is_onlines'] == '0') {


            $str .= " and is_online = " . $search_term['is_onlines'] . "";

        }



        $str .= " LIMIT $page," . $config_src["per_page"] . "";

        $qry = $this->db->query($str);

        $data['result'] = $qry->result();



        $data['data'] = $search_term;



        $data["links"] = $this->pagination->create_links();

        $data['pg'] = $page;

        $data['chk'] = "search";



        $this->load->view('layout/header');

        $this->load->view('layout/sidebar');

        $this->load->view('admin/Doctor', $data);

        $this->load->view('layout/footer');

    }



         public function Nurse_serch() {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        $search_term = $this->searchterm_handler($_POST, TRUE);



        $config_src = array();

        $config_src["base_url"] = $this->config->base_url() . "admin/Nurse_serch";



        $str = "select * from users where utype = 4";

        if (!empty($search_term['email'])) {

            $str .= " and (email like '%" . $search_term['email'] . "%' or name like '%" . $search_term['email'] . "%')";

        }

        if ($search_term['is_active'] == '1' || $search_term['is_active'] == '0') {

            $str .= " and status = " . $search_term['is_active'] . "";

        }
           if ($search_term['is_onlines'] == '1' || $search_term['is_onlines'] == '0') {


            $str .= " and is_online = " . $search_term['is_onlines'] . "";

        }



        $qry = $this->db->query($str);



        $config_src["total_rows"] = count($qry->result());

        $config_src["per_page"] = 10;

        $config_src["uri_segment"] = 3;

        $choice = $config_src["total_rows"] / $config_src["per_page"];

        $config_src["num_links"] = round($choice);

//config for bootstrap pagination class integration

        $config_src['full_tag_open'] = '<ul class="pagination">';

        $config_src['full_tag_close'] = '</ul>';

        $config_src['first_link'] = false;

        $config_src['last_link'] = false;

        $config_src['first_tag_open'] = '<li>';

        $config_src['first_tag_close'] = '</li>';

        $config_src['prev_link'] = '&laquo';

        $config_src['prev_tag_open'] = '<li class="prev">';

        $config_src['prev_tag_close'] = '</li>';

        $config_src['next_link'] = '&raquo';

        $config_src['next_tag_open'] = '<li>';

        $config_src['next_tag_close'] = '</li>';

        $config_src['last_tag_open'] = '<li>';

        $config_src['last_tag_close'] = '</li>';

        $config_src['cur_tag_open'] = '<li class="active"><a href="#">';

        $config_src['cur_tag_close'] = '</a></li>';

        $config_src['num_tag_open'] = '<li>';

        $config_src['num_tag_close'] = '</li>';

        $this->pagination->initialize($config_src);

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;



        $str = "select * from users where utype = 4";

        if (!empty($search_term['email'])) {

            $str .= " and (email like '%" . $search_term['email'] . "%' or name like '%" . $search_term['email'] . "%')";

        }

        if ($search_term['is_active'] == '1' || $search_term['is_active'] == '0') {

            $str .= " and status = " . $search_term['is_active'] . "";

        }
           if ($search_term['is_onlines'] == '1' || $search_term['is_onlines'] == '0') {


            $str .= " and is_online = " . $search_term['is_onlines'] . "";

        }



        $str .= " LIMIT $page," . $config_src["per_page"] . "";

        $qry = $this->db->query($str);

        $data['result'] = $qry->result();



        $data['data'] = $search_term;



        $data["links"] = $this->pagination->create_links();

        $data['pg'] = $page;

        $data['chk'] = "search";



        $this->load->view('layout/header');

        $this->load->view('layout/sidebar');

        $this->load->view('admin/Nurse', $data);

        $this->load->view('layout/footer');

    }

    public function users($type = NULL) {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        if ($type == 'update') {

            unset($_POST['confirmpass']);

            unset($_POST['submit-register']);

            if (!empty($_FILES['avatar']['name'])) {

                $path = $_FILES['avatar']['name'];

                $ext = pathinfo($path, PATHINFO_EXTENSION);

                $rand = 'img_' . rand(1, 500) . rand(500, 1000) . rand(1, 500);

                $config['upload_path'] = BASEPATH . "../avatar/";

                $config['allowed_types'] = '*';

                $config['file_name'] = $rand;

                $this->load->library('upload', $config);

                $this->upload->overwrite = true;

                $this->upload->do_upload('avatar');

                $data = $this->upload->data();

                $_POST['avatar'] = $this->config->base_url() . "avatar/" . $rand . '.' . $ext;

            }

            if (empty($_POST['password'])) {

                unset($_POST['password']);

            }else{

            	$_POST['password'] = md5($_POST['password']);

            }

            $this->db->where('id', $_POST['user_id']);

            $this->db->update('users', $_POST);

            $this->session->set_userdata(array("msg" => "data successfully updated", "type" => "success"));

            header('location:' . $this->config->base_url() . 'admin/users');

        } elseif ($type == "delete") {

            $this->db->where('id', $_POST['user_id']);

            $this->db->delete('users');

        } else {



            $config = array();

            $config["base_url"] = $this->config->base_url() . "admin/users";

            $config["total_rows"] = count($this->db->get_where("users", array("utype" => 0))->result());

            $config["per_page"] = 10;

            $config["uri_segment"] = 3;



            $config['full_tag_open'] = '<ul class="pagination">';

            $config['full_tag_close'] = '</ul>';

            $config['first_link'] = false;

            $config['last_link'] = false;

            $config['first_tag_open'] = '<li>';

            $config['first_tag_close'] = '</li>';

            $config['prev_link'] = '&laquo';

            $config['prev_tag_open'] = '<li class="prev">';

            $config['prev_tag_close'] = '</li>';

            $config['next_link'] = '&raquo';

            $config['next_tag_open'] = '<li>';

            $config['next_tag_close'] = '</li>';

            $config['last_tag_open'] = '<li>';

            $config['last_tag_close'] = '</li>';

            $config['cur_tag_open'] = '<li class="active"><a href="#">';

            $config['cur_tag_close'] = '</a></li>';

            $config['num_tag_open'] = '<li>';

            $config['num_tag_close'] = '</li>';



            $this->pagination->initialize($config);

            $data["links"] = $this->pagination->create_links();





            $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

            !empty($config["per_page"]) ? $this->db->limit($config["per_page"], $page) : '';

            $data["result"] = $this->db->get_where("users", array("utype" => 0))->result(); //employee is a table in the database







            $this->load->view('layout/header');

            $this->load->view('layout/sidebar');

            $this->load->view('admin/users', $data);

            $this->load->view('layout/footer');

        }

    }



  public function Earnings($type = NULL) {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        if ($type == 'update') {

            unset($_POST['confirmpass']);

            unset($_POST['submit-register']);

        

            $this->db->where('ride_id', $_POST['ride_id']);

            $this->db->update('rides', $_POST);

            // $sql = $this->db->last_query();
            // echo $sql;

            $this->session->set_userdata(array("msg" => "data successfully updated", "type" => "success"));

            header('location:' . $this->config->base_url() . 'admin/Earnings');

        }  else {



            $config = array();

            $config["base_url"] = $this->config->base_url() . "admin/Earnings";

            $config["total_rows"] = count($this->db->get_where("rides", array("status" => 'COMPLETED'))->result());

            $config["per_page"] = 10;

            $config["uri_segment"] = 3;



            $config['full_tag_open'] = '<ul class="pagination">';

            $config['full_tag_close'] = '</ul>';

            $config['first_link'] = false;

            $config['last_link'] = false;

            $config['first_tag_open'] = '<li>';

            $config['first_tag_close'] = '</li>';

            $config['prev_link'] = '&laquo';

            $config['prev_tag_open'] = '<li class="prev">';

            $config['prev_tag_close'] = '</li>';

            $config['next_link'] = '&raquo';

            $config['next_tag_open'] = '<li>';

            $config['next_tag_close'] = '</li>';

            $config['last_tag_open'] = '<li>';

            $config['last_tag_close'] = '</li>';

            $config['cur_tag_open'] = '<li class="active"><a href="#">';

            $config['cur_tag_close'] = '</a></li>';

            $config['num_tag_open'] = '<li>';

            $config['num_tag_close'] = '</li>';



            $this->pagination->initialize($config);

            $data["links"] = $this->pagination->create_links();





            $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

            !empty($config["per_page"]) ? $this->db->limit($config["per_page"], $page) : '';

            $data["result"] = $this->db->get_where("rides", array("status" => 'COMPLETED'))->result();



            $this->load->view('layout/header');

            $this->load->view('layout/sidebar');

            $this->load->view('admin/Commission', $data);

            $this->load->view('layout/footer');

        }

    }





public function Ambulance($type = NULL) {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        if ($type == 'update') {

            unset($_POST['confirmpass']);

            unset($_POST['submit-register']);

            if (!empty($_FILES['avatar']['name'])) {

                $path = $_FILES['avatar']['name'];

                $ext = pathinfo($path, PATHINFO_EXTENSION);

                $rand = 'img_' . rand(1, 500) . rand(500, 1000) . rand(1, 500);

                $config['upload_path'] = BASEPATH . "../avatar/";

                $config['allowed_types'] = '*';

                $config['file_name'] = $rand;

                $this->load->library('upload', $config);

                $this->upload->overwrite = true;

                $this->upload->do_upload('avatar');

                $data = $this->upload->data();

                $_POST['avatar'] = $this->config->base_url() . "avatar/" . $rand . '.' . $ext;

            }

            if (empty($_POST['password'])) {

                unset($_POST['password']);

            }else{

                $_POST['password'] = md5($_POST['password']);

            }

          $if_update =  $this->db->where('id', $_POST['user_id']);

           $if_update = $this->db->update('users', $_POST);

             $mobile = $_POST['mobile'];

 

             $res = $this->db->query("SELECT * FROM `users` where `id` = ".$_POST['user_id'])->row();

           $token =  $res->gcm_token;

           if($if_update){

            if($_POST['status'] == '1'){

                 $msg = array

          (  

                'title' => 'Account',

                'body'  => 'your account is now activated',

                'icon'  => 'myicon',

                'sound' => 'mySound'

          );



                define( 'API_ACCESS_KEY', 'AIzaSyBdtoqZnDtDfLWElaGmRi9GrTy0t364SUs');

    $fields = array

            (

            'registration_ids' => array($token),

                'notification'  => $msg

            );

    

    

    $headers = array

            (

                'Authorization: key=' . API_ACCESS_KEY,

                'Content-Type: application/json'

            );

          // print_r($fields);

          // exit();

#Send Reponse To FireBase Server    

        $ch = curl_init();

        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );

        curl_setopt( $ch,CURLOPT_POST, true );

        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );

        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );

        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );

        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );

        $result = curl_exec($ch );

        

    $responses=json_decode($result);

    // print_r($result);

    // exit();

   //  echo $result;exit;



    curl_close( $ch );

     if($responses){

                   $curl = curl_init();



curl_setopt_array($curl, array(

  CURLOPT_URL => "http://api.msg91.com/api/sendhttp.php?sender=MSGIND&route=4&mobiles=$mobile&authkey=219571AuhqogaI25b1a764a&country=0&message=your Account is now Approved .",

  CURLOPT_RETURNTRANSFER => true,

  CURLOPT_ENCODING => "",

  CURLOPT_MAXREDIRS => 10,

  CURLOPT_TIMEOUT => 30,

  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

  CURLOPT_CUSTOMREQUEST => "GET",

  CURLOPT_SSL_VERIFYHOST => 0,

  CURLOPT_SSL_VERIFYPEER => 0,

));



$response = curl_exec($curl);

$err = curl_error($curl);



curl_close($curl);

}

}

else{



     

                       $curl = curl_init();



curl_setopt_array($curl, array(

  CURLOPT_URL => "http://api.msg91.com/api/sendhttp.php?sender=MSGIND&route=4&mobiles=$mobile&authkey=219571AuhqogaI25b1a764a&country=0&message=your Account is now Deactivated .",

  CURLOPT_RETURNTRANSFER => true,

  CURLOPT_ENCODING => "",

  CURLOPT_MAXREDIRS => 10,

  CURLOPT_TIMEOUT => 30,

  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

  CURLOPT_CUSTOMREQUEST => "GET",

  CURLOPT_SSL_VERIFYHOST => 0,

  CURLOPT_SSL_VERIFYPEER => 0,

));



$response = curl_exec($curl);

$err = curl_error($curl);



curl_close($curl);

}





if($response){

     $this->session->set_userdata(array("msg" => "data successfully updated", "type" => "success"));

}

}

            header('location:' . $this->config->base_url() . 'admin/Ambulance');

             // $this->load->view('admin/Ambulance');

        } elseif ($type == "delete") {

            $this->db->where('id', $_POST['user_id']);

            $this->db->delete('users');

        } else {



            $config = array();

            $config["base_url"] = $this->config->base_url() . "admin/Ambulance";

            $config["total_rows"] = count($this->db->get_where("users", array("utype" => 2))->result());

            $config["per_page"] = 10;

            $config["uri_segment"] = 3;



            $config['full_tag_open'] = '<ul class="pagination">';

            $config['full_tag_close'] = '</ul>';

            $config['first_link'] = false;

            $config['last_link'] = false;

            $config['first_tag_open'] = '<li>';

            $config['first_tag_close'] = '</li>';

            $config['prev_link'] = '&laquo';

            $config['prev_tag_open'] = '<li class="prev">';

            $config['prev_tag_close'] = '</li>';

            $config['next_link'] = '&raquo';

            $config['next_tag_open'] = '<li>';

            $config['next_tag_close'] = '</li>';

            $config['last_tag_open'] = '<li>';

            $config['last_tag_close'] = '</li>';

            $config['cur_tag_open'] = '<li class="active"><a href="#">';

            $config['cur_tag_close'] = '</a></li>';

            $config['num_tag_open'] = '<li>';

            $config['num_tag_close'] = '</li>';



            $this->pagination->initialize($config);

            $data["links"] = $this->pagination->create_links();





            $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

            !empty($config["per_page"]) ? $this->db->limit($config["per_page"], $page) : '';

            $data["result"] = $this->db->get_where("users", array("utype" => 2))->result(); //employee is a table in the database







            $this->load->view('layout/header');

            $this->load->view('layout/sidebar');

            $this->load->view('admin/Ambulance', $data);

            $this->load->view('layout/footer');

        }

    }





public function Doctor($type = NULL) {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        if ($type == 'update') {

            unset($_POST['confirmpass']);

            unset($_POST['submit-register']);

            if (!empty($_FILES['avatar']['name'])) {

                $path = $_FILES['avatar']['name'];

                $ext = pathinfo($path, PATHINFO_EXTENSION);

                $rand = 'img_' . rand(1, 500) . rand(500, 1000) . rand(1, 500);

                $config['upload_path'] = BASEPATH . "../avatar/";

                $config['allowed_types'] = '*';

                $config['file_name'] = $rand;

                $this->load->library('upload', $config);

                $this->upload->overwrite = true;

                $this->upload->do_upload('avatar');

                $data = $this->upload->data();

                $_POST['avatar'] = $this->config->base_url() . "avatar/" . $rand . '.' . $ext;

            }

            if (empty($_POST['password'])) {

                unset($_POST['password']);

            }else{

                $_POST['password'] = md5($_POST['password']);

            }

                     $if_update =  $this->db->where('id', $_POST['user_id']);

           $if_update = $this->db->update('users', $_POST);

             $mobile = $_POST['mobile'];

                  $res = $this->db->query("SELECT * FROM `users` where `id` = ".$_POST['user_id'])->row();

           $token =  $res->gcm_token;

            

          if($if_update){

            if($_POST['status'] == '1'){

                 $msg = array

          (  

                'title' => 'Account',

                'body'  => 'your account is now activated',

                'icon'  => 'myicon',

                'sound' => 'mySound'

          );



                define( 'API_ACCESS_KEY', 'AIzaSyBdtoqZnDtDfLWElaGmRi9GrTy0t364SUs');

    $fields = array

            (

            'registration_ids' => array($token),

                'notification'  => $msg

            );

    

    

    $headers = array

            (

                'Authorization: key=' . API_ACCESS_KEY,

                'Content-Type: application/json'

            );

          // print_r($fields);

          // exit();

#Send Reponse To FireBase Server    

        $ch = curl_init();

        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );

        curl_setopt( $ch,CURLOPT_POST, true );

        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );

        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );

        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );

        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );

        $result = curl_exec($ch );

        

    $responses=json_decode($result);

    // print_r($result);

    // exit();

   //  echo $result;exit;



    curl_close( $ch );

     if($responses){

                   $curl = curl_init();



curl_setopt_array($curl, array(

  CURLOPT_URL => "http://api.msg91.com/api/sendhttp.php?sender=MSGIND&route=4&mobiles=$mobile&authkey=219571AuhqogaI25b1a764a&country=0&message=your Account is now Approved .",

  CURLOPT_RETURNTRANSFER => true,

  CURLOPT_ENCODING => "",

  CURLOPT_MAXREDIRS => 10,

  CURLOPT_TIMEOUT => 30,

  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

  CURLOPT_CUSTOMREQUEST => "GET",

  CURLOPT_SSL_VERIFYHOST => 0,

  CURLOPT_SSL_VERIFYPEER => 0,

));



$response = curl_exec($curl);

$err = curl_error($curl);



curl_close($curl);

}

}

else{



     

                       $curl = curl_init();



curl_setopt_array($curl, array(

  CURLOPT_URL => "http://api.msg91.com/api/sendhttp.php?sender=MSGIND&route=4&mobiles=$mobile&authkey=219571AuhqogaI25b1a764a&country=0&message=your Account is now Deactivated .",

  CURLOPT_RETURNTRANSFER => true,

  CURLOPT_ENCODING => "",

  CURLOPT_MAXREDIRS => 10,

  CURLOPT_TIMEOUT => 30,

  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

  CURLOPT_CUSTOMREQUEST => "GET",

  CURLOPT_SSL_VERIFYHOST => 0,

  CURLOPT_SSL_VERIFYPEER => 0,

));



$response = curl_exec($curl);

$err = curl_error($curl);



curl_close($curl);

}





if($response){

     $this->session->set_userdata(array("msg" => "data successfully updated", "type" => "success"));

}

}

            header('location:' . $this->config->base_url() . 'admin/Doctor');

             

        } elseif ($type == "delete") {

            $this->db->where('id', $_POST['user_id']);

            $this->db->delete('users');

        } else {



            $config = array();

            $config["base_url"] = $this->config->base_url() . "admin/Doctor";

            $config["total_rows"] = count($this->db->get_where("users", array("utype" => 3))->result());

            $config["per_page"] = 10;

            $config["uri_segment"] = 3;



            $config['full_tag_open'] = '<ul class="pagination">';

            $config['full_tag_close'] = '</ul>';

            $config['first_link'] = false;

            $config['last_link'] = false;

            $config['first_tag_open'] = '<li>';

            $config['first_tag_close'] = '</li>';

            $config['prev_link'] = '&laquo';

            $config['prev_tag_open'] = '<li class="prev">';

            $config['prev_tag_close'] = '</li>';

            $config['next_link'] = '&raquo';

            $config['next_tag_open'] = '<li>';

            $config['next_tag_close'] = '</li>';

            $config['last_tag_open'] = '<li>';

            $config['last_tag_close'] = '</li>';

            $config['cur_tag_open'] = '<li class="active"><a href="#">';

            $config['cur_tag_close'] = '</a></li>';

            $config['num_tag_open'] = '<li>';

            $config['num_tag_close'] = '</li>';



            $this->pagination->initialize($config);

            $data["links"] = $this->pagination->create_links();





            $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

            !empty($config["per_page"]) ? $this->db->limit($config["per_page"], $page) : '';

            $data["result"] = $this->db->get_where("users", array("utype" => 3))->result(); //employee is a table in the database







            $this->load->view('layout/header');

            $this->load->view('layout/sidebar');

            $this->load->view('admin/Doctor', $data);

            $this->load->view('layout/footer');

        }

    }





    public function Nurse($type = NULL) {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        if ($type == 'update') {

            unset($_POST['confirmpass']);

            unset($_POST['submit-register']);

            if (!empty($_FILES['avatar']['name'])) {

                $path = $_FILES['avatar']['name'];

                $ext = pathinfo($path, PATHINFO_EXTENSION);

                $rand = 'img_' . rand(1, 500) . rand(500, 1000) . rand(1, 500);

                $config['upload_path'] = BASEPATH . "../avatar/";

                $config['allowed_types'] = '*';

                $config['file_name'] = $rand;

                $this->load->library('upload', $config);

                $this->upload->overwrite = true;

                $this->upload->do_upload('avatar');

                $data = $this->upload->data();

                $_POST['avatar'] = $this->config->base_url() . "avatar/" . $rand . '.' . $ext;

            }

            if (empty($_POST['password'])) {

                unset($_POST['password']);

            }else{

                $_POST['password'] = md5($_POST['password']);

            }

               $if_update =  $this->db->where('id', $_POST['user_id']);

           $if_update = $this->db->update('users', $_POST);

             $mobile = $_POST['mobile'];

             $res = $this->db->query("SELECT * FROM `users` where `id` = ".$_POST['user_id'])->row();

           $token =  $res->gcm_token;

            

            if($if_update){

            if($_POST['status'] == '1'){

                 $msg = array

          (  

                'title' => 'Account',

                'body'  => 'your account is now activated',

                'icon'  => 'myicon',

                'sound' => 'mySound'

          );



                define( 'API_ACCESS_KEY', 'AIzaSyBdtoqZnDtDfLWElaGmRi9GrTy0t364SUs');

    $fields = array

            (

            'registration_ids' => array($token),

                'notification'  => $msg

            );

    

    

    $headers = array

            (

                'Authorization: key=' . API_ACCESS_KEY,

                'Content-Type: application/json'

            );

          // print_r($fields);

          // exit();

#Send Reponse To FireBase Server    

        $ch = curl_init();

        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );

        curl_setopt( $ch,CURLOPT_POST, true );

        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );

        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );

        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );

        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );

        $result = curl_exec($ch );

        

    $responses=json_decode($result);

    // print_r($result);

    // exit();

   //  echo $result;exit;



    curl_close( $ch );

     if($responses){

                   $curl = curl_init();



curl_setopt_array($curl, array(

  CURLOPT_URL => "http://api.msg91.com/api/sendhttp.php?sender=MSGIND&route=4&mobiles=$mobile&authkey=219571AuhqogaI25b1a764a&country=0&message=your Account is now Approved .",

  CURLOPT_RETURNTRANSFER => true,

  CURLOPT_ENCODING => "",

  CURLOPT_MAXREDIRS => 10,

  CURLOPT_TIMEOUT => 30,

  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

  CURLOPT_CUSTOMREQUEST => "GET",

  CURLOPT_SSL_VERIFYHOST => 0,

  CURLOPT_SSL_VERIFYPEER => 0,

));



$response = curl_exec($curl);

$err = curl_error($curl);



curl_close($curl);

}

}

else{



     

                       $curl = curl_init();



curl_setopt_array($curl, array(

  CURLOPT_URL => "http://api.msg91.com/api/sendhttp.php?sender=MSGIND&route=4&mobiles=$mobile&authkey=219571AuhqogaI25b1a764a&country=0&message=your Account is now Deactivated .",

  CURLOPT_RETURNTRANSFER => true,

  CURLOPT_ENCODING => "",

  CURLOPT_MAXREDIRS => 10,

  CURLOPT_TIMEOUT => 30,

  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

  CURLOPT_CUSTOMREQUEST => "GET",

  CURLOPT_SSL_VERIFYHOST => 0,

  CURLOPT_SSL_VERIFYPEER => 0,

));



$response = curl_exec($curl);

$err = curl_error($curl);



curl_close($curl);

}





if($response){

     $this->session->set_userdata(array("msg" => "data successfully updated", "type" => "success"));

}

}

            header('location:' . $this->config->base_url() . 'admin/Nurse');

               

        } elseif ($type == "delete") {

            $this->db->where('id', $_POST['user_id']);

            $this->db->delete('users');

        } else {



            $config = array();

            $config["base_url"] = $this->config->base_url() . "admin/Nurse";

            $config["total_rows"] = count($this->db->get_where("users", array("utype" => 4))->result());

            $config["per_page"] = 10;

            $config["uri_segment"] = 3;



            $config['full_tag_open'] = '<ul class="pagination">';

            $config['full_tag_close'] = '</ul>';

            $config['first_link'] = false;

            $config['last_link'] = false;

            $config['first_tag_open'] = '<li>';

            $config['first_tag_close'] = '</li>';

            $config['prev_link'] = '&laquo';

            $config['prev_tag_open'] = '<li class="prev">';

            $config['prev_tag_close'] = '</li>';

            $config['next_link'] = '&raquo';

            $config['next_tag_open'] = '<li>';

            $config['next_tag_close'] = '</li>';

            $config['last_tag_open'] = '<li>';

            $config['last_tag_close'] = '</li>';

            $config['cur_tag_open'] = '<li class="active"><a href="#">';

            $config['cur_tag_close'] = '</a></li>';

            $config['num_tag_open'] = '<li>';

            $config['num_tag_close'] = '</li>';



            $this->pagination->initialize($config);

            $data["links"] = $this->pagination->create_links();





            $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

            !empty($config["per_page"]) ? $this->db->limit($config["per_page"], $page) : '';

            $data["result"] = $this->db->get_where("users", array("utype" => 4))->result(); //employee is a table in the database







            $this->load->view('layout/header');

            $this->load->view('layout/sidebar');

            $this->load->view('admin/Nurse', $data);

            $this->load->view('layout/footer');

        }

    }

    public function users_search() {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        $search_term = $this->searchterm_handler($_POST, TRUE);



        $config_src = array();

        $config_src["base_url"] = $this->config->base_url() . "admin/user_search";



        $str = "select * from users where utype = 0";

        if (!empty($search_term['email'])) {

            $str .= " and (email like '%" . $search_term['email'] . "%' or name like '%" . $search_term['email'] . "%')";

        }

        if ($search_term['is_active'] == '1' || $search_term['is_active'] == '0') {

            $str .= " and status = " . $search_term['is_active'] . "";

        }



        $qry = $this->db->query($str);



        $config_src["total_rows"] = count($qry->result());

        $config_src["per_page"] = 10;

        $config_src["uri_segment"] = 3;

        $choice = $config_src["total_rows"] / $config_src["per_page"];

        $config_src["num_links"] = round($choice);

        //config for bootstrap pagination class integration

        $config_src['full_tag_open'] = '<ul class="pagination">';

        $config_src['full_tag_close'] = '</ul>';

        $config_src['first_link'] = false;

        $config_src['last_link'] = false;

        $config_src['first_tag_open'] = '<li>';

        $config_src['first_tag_close'] = '</li>';

        $config_src['prev_link'] = '&laquo';

        $config_src['prev_tag_open'] = '<li class="prev">';

        $config_src['prev_tag_close'] = '</li>';

        $config_src['next_link'] = '&raquo';

        $config_src['next_tag_open'] = '<li>';

        $config_src['next_tag_close'] = '</li>';

        $config_src['last_tag_open'] = '<li>';

        $config_src['last_tag_close'] = '</li>';

        $config_src['cur_tag_open'] = '<li class="active"><a href="#">';

        $config_src['cur_tag_close'] = '</a></li>';

        $config_src['num_tag_open'] = '<li>';

        $config_src['num_tag_close'] = '</li>';

        $this->pagination->initialize($config_src);

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;



        $str = "select * from users where utype = 0";

        if (!empty($search_term['email'])) {

            $str .= " and (email like '%" . $search_term['email'] . "%' or name like '%" . $search_term['email'] . "%')";

        }

        if ($search_term['is_active'] == '1' || $search_term['is_active'] == '0') {

            $str .= " and status = " . $search_term['is_active'] . "";

        }



        $str .= " LIMIT $page," . $config_src["per_page"] . "";

        $qry = $this->db->query($str);

        $data['result'] = $qry->result();



        $data['data'] = $search_term;



        $data["links"] = $this->pagination->create_links();

        $data['pg'] = $page;

        $data['chk'] = "search";



        $this->load->view('layout/header');

        $this->load->view('layout/sidebar');

        $this->load->view('admin/users', $data);

        $this->load->view('layout/footer');

    }



     public function Com_search() {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        $search_term = $this->searchterm_handler($_POST, TRUE);



        $config_src = array();

        $config_src["base_url"] = $this->config->base_url() . "admin/Com_search";



      $str = "select * from rides where `status` = 'COMPLETED'";

        if (!empty($search_term['driver_name'])) {

            $str .= " and (driver_name like '%" . $search_term['driver_name'] . "%' or driver_name like '%" . $search_term['driver_name'] . "%')";

        }

        if ($search_term['type_id'] == '2' || $search_term['type_id'] == '3' || $search_term['type_id'] == '4' ) {

            $str .= " and type_id = " . $search_term['type_id'] . "";

        }



        $qry = $this->db->query($str);



        $config_src["total_rows"] = count($qry->result());

        $config_src["per_page"] = 10;

        $config_src["uri_segment"] = 3;

        $choice = $config_src["total_rows"] / $config_src["per_page"];

        $config_src["num_links"] = round($choice);

        //config for bootstrap pagination class integration

        $config_src['full_tag_open'] = '<ul class="pagination">';

        $config_src['full_tag_close'] = '</ul>';

        $config_src['first_link'] = false;

        $config_src['last_link'] = false;

        $config_src['first_tag_open'] = '<li>';

        $config_src['first_tag_close'] = '</li>';

        $config_src['prev_link'] = '&laquo';

        $config_src['prev_tag_open'] = '<li class="prev">';

        $config_src['prev_tag_close'] = '</li>';

        $config_src['next_link'] = '&raquo';

        $config_src['next_tag_open'] = '<li>';

        $config_src['next_tag_close'] = '</li>';

        $config_src['last_tag_open'] = '<li>';

        $config_src['last_tag_close'] = '</li>';

        $config_src['cur_tag_open'] = '<li class="active"><a href="#">';

        $config_src['cur_tag_close'] = '</a></li>';

        $config_src['num_tag_open'] = '<li>';

        $config_src['num_tag_close'] = '</li>';

        $this->pagination->initialize($config_src);

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;



        $str = "select * from rides where `status` = 'COMPLETED'";

        if (!empty($search_term['driver_name'])) {

            $str .= " and (driver_name like '%" . $search_term['driver_name'] . "%' or driver_name like '%" . $search_term['driver_name'] . "%')";

        }

          if ($search_term['type_id'] == '2' || $search_term['type_id'] == '3' || $search_term['type_id'] == '4' ) {

            $str .= " and type_id = " . $search_term['type_id'] . "";

        }



        $str .= " LIMIT $page," . $config_src["per_page"] . "";

        $qry = $this->db->query($str);

        $data['result'] = $qry->result();



        $data['data'] = $search_term;



        $data["links"] = $this->pagination->create_links();

        $data['pg'] = $page;

        $data['chk'] = "search";



        $this->load->view('layout/header');

        $this->load->view('layout/sidebar');

        $this->load->view('admin/Commission', $data);

        $this->load->view('layout/footer');

    }





    public function searchterm_handler($searchterm) {

        if ($searchterm) {

            $this->session->set_userdata('searchterm', $searchterm);

            return $searchterm;

        } elseif ($this->session->userdata('searchterm')) {

            $searchterm = $this->session->userdata('searchterm');

            return $searchterm;

        } else {

            $searchterm = '';

            return $searchterm;

        }

    }



    public function getUser() {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        $res['post'] = $this->db->get_where("users", array("id" => $this->input->post("user_id")))->row_array();

        $this->load->view('admin/ajax/edit_user', $res);

    }

    public function get_Comm() {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        $res['post'] = $this->db->get_where("rides", array("ride_id" => $this->input->post("ride_id")))->row_array();

        $this->load->view('admin/ajax/edit_Comm', $res);

    }
       public function getsubtype() {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        $res['post'] = $this->db->get_where("subtype", array("id" => $this->input->post("id")))->row_array();

        $this->load->view('admin/ajax/edit_subtype', $res);

    }

    public function getDriver() {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        $res['post'] = $this->db->get_where("users", array("id" => $this->input->post("user_id")))->row_array();

        $this->load->view('admin/ajax/edit_driver', $res);

    }

    public function getAmbulance() {

       $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        $res['post'] = $this->db->get_where("users", array("id" => $this->input->post("user_id")))->row_array();

        $this->load->view('admin/ajax/edit_Ambulance', $res);

    }

    public function getDoctor() {

       $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        $res['post'] = $this->db->get_where("users", array("id" => $this->input->post("user_id")))->row_array();

        $this->load->view('admin/ajax/edit_Doctor', $res);

    } 

       public function getNurse() {

       $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        $res['post'] = $this->db->get_where("users", array("id" => $this->input->post("user_id")))->row_array();

        $this->load->view('admin/ajax/edit_Nurse', $res);

    }  

    public function viewDriver() {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        $res['post'] = $this->db->get_where("users", array("id" => $this->input->post("user_id")))->row_array();

        $this->load->view('admin/ajax/view_driver', $res);

    }

    public function viewAmbulance() {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        $res['post'] = $this->db->get_where("users", array("id" => $this->input->post("user_id")))->row_array();

        $this->load->view('admin/ajax/view_Ambulance', $res);

    }

     public function viewDoctor() {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        $res['post'] = $this->db->get_where("users", array("id" => $this->input->post("user_id")))->row_array();

        $res['postride'] = $this->db->get_where("rides", array("id" => $this->input->post("user_id")))->row_array();

        $this->load->view('admin/ajax/view_Doctor', $res);

    }

       public function viewNurse() {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        $res['post'] = $this->db->get_where("users", array("id" => $this->input->post("user_id")))->row_array();



        $this->load->view('admin/ajax/view_Nurse', $res);

    }





    public function getPayments() {

        

        $qry = $this->db->query("SHOW COLUMNS FROM `rides` LIKE 'pay_driver'");

        $exists = $qry->row();

        if(!$exists) {

            $this->db->query("ALTER TABLE `rides`  ADD `pay_driver` TINYINT(1) NULL DEFAULT '0'  AFTER `payment_status`;");

        }

        

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        $this->db->select("SUM(r.amount) as amount,l.name as driver,l.id,l.paypal_id");

        $this->db->from("rides r");

        $this->db->join("users l", "l.id = r.driver_id");

        $this->db->where("r.pay_driver", 0);

	    $this->db->where("r.status", "COMPLETED");

        $this->db->group_by('r.driver_id');

        $this->db->order_by('ride_id', 'desc');

        $qry = $this->db->get();

        $data["result"] = $qry->result();





        $this->load->view('layout/header');

        $this->load->view('layout/sidebar');

        $this->load->view('admin/get_payments', $data);

        $this->load->view('layout/footer');

    }



    public function getrides() {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        $config = array();

        $config["base_url"] = $this->config->base_url() . "admin/getrides";

        $config["total_rows"] = count($this->db->get("rides")->result());

        $config["per_page"] = 10;

        $config["uri_segment"] = 3;



        $config['full_tag_open'] = '<ul class="pagination">';

        $config['full_tag_close'] = '</ul>';

        $config['first_link'] = false;

        $config['last_link'] = false;

        $config['first_tag_open'] = '<li>';

        $config['first_tag_close'] = '</li>';

        $config['prev_link'] = '&laquo';

        $config['prev_tag_open'] = '<li class="prev">';

        $config['prev_tag_close'] = '</li>';

        $config['next_link'] = '&raquo';

        $config['next_tag_open'] = '<li>';

        $config['next_tag_close'] = '</li>';

        $config['last_tag_open'] = '<li>';

        $config['last_tag_close'] = '</li>';

        $config['cur_tag_open'] = '<li class="active"><a href="#">';

        $config['cur_tag_close'] = '</a></li>';

        $config['num_tag_open'] = '<li>';

        $config['num_tag_close'] = '</li>';



        $this->pagination->initialize($config);

        $data["links"] = $this->pagination->create_links();





        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

        !empty($config["per_page"]) ? $this->db->limit($config["per_page"], $page) : '';

        $this->db->select("r.*,u.name as customer,l.name as driver");

        $this->db->from("rides r");

        $this->db->join("users u", "u.id = r.user_id");

        $this->db->join("users l", "l.id = r.driver_id");

        $qry = $this->db->get();

        $data["result"] = $qry->result();

// echo $this->db->last_query(); die;

        $this->load->view('layout/header');

        $this->load->view('layout/sidebar');

        $this->load->view('admin/get_rides', $data);

        $this->load->view('layout/footer');

    }



 public function Ambulance_getrides() {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        $config = array();

        $config["base_url"] = $this->config->base_url() . "admin/Ambulance_getrides";

        $config["total_rows"] = count($this->db->get("rides")->result());

        $config["per_page"] = 10;

        $config["uri_segment"] = 3;



        $config['full_tag_open'] = '<ul class="pagination">';

        $config['full_tag_close'] = '</ul>';

        $config['first_link'] = false;

        $config['last_link'] = false;

        $config['first_tag_open'] = '<li>';

        $config['first_tag_close'] = '</li>';

        $config['prev_link'] = '&laquo';

        $config['prev_tag_open'] = '<li class="prev">';

        $config['prev_tag_close'] = '</li>';

        $config['next_link'] = '&raquo';

        $config['next_tag_open'] = '<li>';

        $config['next_tag_close'] = '</li>';

        $config['last_tag_open'] = '<li>';

        $config['last_tag_close'] = '</li>';

        $config['cur_tag_open'] = '<li class="active"><a href="#">';

        $config['cur_tag_close'] = '</a></li>';

        $config['num_tag_open'] = '<li>';

        $config['num_tag_close'] = '</li>';



        $this->pagination->initialize($config);

        $data["links"] = $this->pagination->create_links();





        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

        !empty($config["per_page"]) ? $this->db->limit($config["per_page"], $page) : '';

        $this->db->select("r.*,u.name as customer,l.name as driver");

        $this->db->from("rides r");

        $this->db->join("users u", "u.id = r.user_id");

        $this->db->join("users l", "l.id = r.driver_id");

        $this->db->where("ride_type",2);

        $qry = $this->db->get();

        $data["result"] = $qry->result();

// echo $this->db->last_query(); die;

        $this->load->view('layout/header');

        $this->load->view('layout/sidebar');

        $this->load->view('admin/Amb_get_rides', $data);

        $this->load->view('layout/footer');

    }







    public function Doctor_getrides() {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        $config = array();

        $config["base_url"] = $this->config->base_url() . "admin/Doctor_getrides";

        $config["total_rows"] = count($this->db->get("rides")->result());

        $config["per_page"] = 10;

        $config["uri_segment"] = 3;



        $config['full_tag_open'] = '<ul class="pagination">';

        $config['full_tag_close'] = '</ul>';

        $config['first_link'] = false;

        $config['last_link'] = false;

        $config['first_tag_open'] = '<li>';

        $config['first_tag_close'] = '</li>';

        $config['prev_link'] = '&laquo';

        $config['prev_tag_open'] = '<li class="prev">';

        $config['prev_tag_close'] = '</li>';

        $config['next_link'] = '&raquo';

        $config['next_tag_open'] = '<li>';

        $config['next_tag_close'] = '</li>';

        $config['last_tag_open'] = '<li>';

        $config['last_tag_close'] = '</li>';

        $config['cur_tag_open'] = '<li class="active"><a href="#">';

        $config['cur_tag_close'] = '</a></li>';

        $config['num_tag_open'] = '<li>';

        $config['num_tag_close'] = '</li>';



        $this->pagination->initialize($config);

        $data["links"] = $this->pagination->create_links();





        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

        !empty($config["per_page"]) ? $this->db->limit($config["per_page"], $page) : '';

        $this->db->select("r.*,u.name as customer, r.user_id, l.name as driver");

        $this->db->from("rides r");

        $this->db->join("users u", "u.id = r.user_id");

        $this->db->join("users l", "l.id = r.driver_id");

        $this->db->where("ride_type",3);

        $qry = $this->db->get();

        $data["result"] = $qry->result();

// echo $this->db->last_query(); die;

        // $data['ress'] = $this->db->query("SELECT * FROM `rides` where `user_id`=")->result();

        $this->load->view('layout/header');

        $this->load->view('layout/sidebar');

        $this->load->view('admin/Doc_get_rides', $data);

        $this->load->view('layout/footer');

    }





    public function Nurse_getrides() {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        $config = array();

        $config["base_url"] = $this->config->base_url() . "admin/Nurse_getrides";

        $config["total_rows"] = count($this->db->get("rides")->result());

        $config["per_page"] = 10;

        $config["uri_segment"] = 3;



        $config['full_tag_open'] = '<ul class="pagination">';

        $config['full_tag_close'] = '</ul>';

        $config['first_link'] = false;

        $config['last_link'] = false;

        $config['first_tag_open'] = '<li>';

        $config['first_tag_close'] = '</li>';

        $config['prev_link'] = '&laquo';

        $config['prev_tag_open'] = '<li class="prev">';

        $config['prev_tag_close'] = '</li>';

        $config['next_link'] = '&raquo';

        $config['next_tag_open'] = '<li>';

        $config['next_tag_close'] = '</li>';

        $config['last_tag_open'] = '<li>';

        $config['last_tag_close'] = '</li>';

        $config['cur_tag_open'] = '<li class="active"><a href="#">';

        $config['cur_tag_close'] = '</a></li>';

        $config['num_tag_open'] = '<li>';

        $config['num_tag_close'] = '</li>';



        $this->pagination->initialize($config);

        $data["links"] = $this->pagination->create_links();





        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

        !empty($config["per_page"]) ? $this->db->limit($config["per_page"], $page) : '';

        $this->db->select("r.*,u.name as customer,l.name as driver");

        $this->db->from("rides r");

        $this->db->join("users u", "u.id = r.user_id");

        $this->db->join("users l", "l.id = r.driver_id");

        $this->db->where("ride_type",4);

        $qry = $this->db->get();

        $data["result"] = $qry->result();

// echo $this->db->last_query(); die;

        $this->load->view('layout/header');

        $this->load->view('layout/sidebar');

        $this->load->view('admin/Nur_get_rides', $data);

        $this->load->view('layout/footer');

    }

    public function rides_search() {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        $search_term = $this->searchterm_handler($_POST, TRUE);



        $config_src = array();

        $config_src["base_url"] = $this->config->base_url() . "admin/rides_search";



        $this->db->select("r.*,u.name as customer,l.name as driver");

        $this->db->from("rides r");

        $this->db->join("users u", "u.id = r.user_id");

        $this->db->join("users l", "l.id = r.driver_id");

        $where = "r.ride_id != ''";

        if (!empty($search_term['email'])) {

            $where .= " and (u.name like '%" . $search_term['email'] . "%' OR l.name like '%" . $search_term['email'] . "%' OR r.pickup_adress like '%" . $search_term['email'] . "%' OR r.drop_address like '%" . $search_term['email'] . "%')";

        }

        if (!empty($search_term['is_active'])) {

            $where .= " and r.status = '" . $search_term['is_active'] . "'";

        }

        $this->db->where($where);

        $qry = $this->db->get();





        $config_src["total_rows"] = count($qry->result());

        $config_src["per_page"] = 10;

        $config_src["uri_segment"] = 3;

        $choice = $config_src["total_rows"] / $config_src["per_page"];

        $config_src["num_links"] = round($choice);

//config for bootstrap pagination class integration

        $config_src['full_tag_open'] = '<ul class="pagination">';

        $config_src['full_tag_close'] = '</ul>';

        $config_src['first_link'] = false;

        $config_src['last_link'] = false;

        $config_src['first_tag_open'] = '<li>';

        $config_src['first_tag_close'] = '</li>';

        $config_src['prev_link'] = '&laquo';

        $config_src['prev_tag_open'] = '<li class="prev">';

        $config_src['prev_tag_close'] = '</li>';

        $config_src['next_link'] = '&raquo';

        $config_src['next_tag_open'] = '<li>';

        $config_src['next_tag_close'] = '</li>';

        $config_src['last_tag_open'] = '<li>';

        $config_src['last_tag_close'] = '</li>';

        $config_src['cur_tag_open'] = '<li class="active"><a href="#">';

        $config_src['cur_tag_close'] = '</a></li>';

        $config_src['num_tag_open'] = '<li>';

        $config_src['num_tag_close'] = '</li>';

        $this->pagination->initialize($config_src);

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;



        !empty($config["per_page"]) ? $this->db->limit($config["per_page"], $page) : '';

        $this->db->select("r.*,u.name as customer,l.name as driver");

        $this->db->from("rides r");

        $this->db->join("users u", "u.id = r.user_id");

        $this->db->join("users l", "l.id = r.driver_id");

        $where = "r.ride_id != ''";



        if (!empty($search_term['email'])) {

            $where .= " and (u.name like '%" . $search_term['email'] . "%' OR l.name like '%" . $search_term['email'] . "%' OR r.pickup_adress like '%" . $search_term['email'] . "%' OR r.drop_address like '%" . $search_term['email'] . "%')";

        }

        if (!empty($search_term['is_active'])) {

            $where .= " and r.status = '" . $search_term['is_active'] . "'";

        }

        $this->db->where($where);

        $qry = $this->db->get();

        $data["result"] = $qry->result();



        $data['data'] = $search_term;



        $data["links"] = $this->pagination->create_links();

        $data['pg'] = $page;

        $data['chk'] = "search";



        $this->load->view('layout/header');

        $this->load->view('layout/sidebar');

        $this->load->view('admin/get_rides', $data);

        $this->load->view('layout/footer');

    }

  public function Amb_rides_search() {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        $search_term = $this->searchterm_handler($_POST, TRUE);



        $config_src = array();

        $config_src["base_url"] = $this->config->base_url() . "admin/Amb_get_rides";



        $this->db->select("r.*,u.name as customer,l.name as driver");

        $this->db->from("rides r");

        $this->db->join("users u", "u.id = r.user_id");

        $this->db->join("users l", "l.id = r.driver_id");

        $where = "r.ride_id != ''";

        if (!empty($search_term['email'])) {

            $where .= " and (u.name like '%" . $search_term['email'] . "%' OR l.name like '%" . $search_term['email'] . "%' OR r.pickup_adress like '%" . $search_term['email'] . "%' OR r.drop_address like '%" . $search_term['email'] . "%')";

        }

        if (!empty($search_term['is_active'])) {

            $where .= " and r.status = '" . $search_term['is_active'] . "' and ride_type= 2";

        }

        $this->db->where($where);

        $qry = $this->db->get();





        $config_src["total_rows"] = count($qry->result());

        $config_src["per_page"] = 10;

        $config_src["uri_segment"] = 3;

        $choice = $config_src["total_rows"] / $config_src["per_page"];

        $config_src["num_links"] = round($choice);

//config for bootstrap pagination class integration

        $config_src['full_tag_open'] = '<ul class="pagination">';

        $config_src['full_tag_close'] = '</ul>';

        $config_src['first_link'] = false;

        $config_src['last_link'] = false;

        $config_src['first_tag_open'] = '<li>';

        $config_src['first_tag_close'] = '</li>';

        $config_src['prev_link'] = '&laquo';

        $config_src['prev_tag_open'] = '<li class="prev">';

        $config_src['prev_tag_close'] = '</li>';

        $config_src['next_link'] = '&raquo';

        $config_src['next_tag_open'] = '<li>';

        $config_src['next_tag_close'] = '</li>';

        $config_src['last_tag_open'] = '<li>';

        $config_src['last_tag_close'] = '</li>';

        $config_src['cur_tag_open'] = '<li class="active"><a href="#">';

        $config_src['cur_tag_close'] = '</a></li>';

        $config_src['num_tag_open'] = '<li>';

        $config_src['num_tag_close'] = '</li>';

        $this->pagination->initialize($config_src);

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;



        !empty($config["per_page"]) ? $this->db->limit($config["per_page"], $page) : '';

        $this->db->select("r.*,u.name as customer,l.name as driver");

        $this->db->from("rides r");

        $this->db->join("users u", "u.id = r.user_id");

        $this->db->join("users l", "l.id = r.driver_id");

        $where = "r.ride_id != ''";



        if (!empty($search_term['email'])) {

            $where .= " and (u.name like '%" . $search_term['email'] . "%' OR l.name like '%" . $search_term['email'] . "%' OR r.pickup_adress like '%" . $search_term['email'] . "%' OR r.drop_address like '%" . $search_term['email'] . "%')";

        }

        if (!empty($search_term['is_active'])) {

            $where .= " and r.status = '" . $search_term['is_active'] . "' and ride_type= 2";

        }

        $this->db->where($where);

        $qry = $this->db->get();

        $data["result"] = $qry->result();



        $data['data'] = $search_term;



        $data["links"] = $this->pagination->create_links();

        $data['pg'] = $page;

        $data['chk'] = "search";



        $this->load->view('layout/header');

        $this->load->view('layout/sidebar');

        $this->load->view('admin/Amb_get_rides', $data);

        $this->load->view('layout/footer');

    }











    public function Docs_rides_search() {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        $search_term = $this->searchterm_handler($_POST, TRUE);



        $config_src = array();

        $config_src["base_url"] = $this->config->base_url() . "admin/Doc_get_rides";



        $this->db->select("r.*,u.name as customer,l.name as driver");

        $this->db->from("rides r");

        $this->db->join("users u", "u.id = r.user_id");

        $this->db->join("users l", "l.id = r.driver_id");

        $where = "r.ride_id != ''";

        if (!empty($search_term['email'])) {

            $where .= " and (u.name like '%" . $search_term['email'] . "%' OR l.name like '%" . $search_term['email'] . "%' OR r.pickup_adress like '%" . $search_term['email'] . "%' OR r.drop_address like '%" . $search_term['email'] . "%')";

        }

        if (!empty($search_term['is_active'])) {

            $where .= " and r.status = '" . $search_term['is_active'] . "' and ride_type= 1";

        }

        $this->db->where($where);

        $qry = $this->db->get();





        $config_src["total_rows"] = count($qry->result());

        $config_src["per_page"] = 10;

        $config_src["uri_segment"] = 3;

        $choice = $config_src["total_rows"] / $config_src["per_page"];

        $config_src["num_links"] = round($choice);

//config for bootstrap pagination class integration

        $config_src['full_tag_open'] = '<ul class="pagination">';

        $config_src['full_tag_close'] = '</ul>';

        $config_src['first_link'] = false;

        $config_src['last_link'] = false;

        $config_src['first_tag_open'] = '<li>';

        $config_src['first_tag_close'] = '</li>';

        $config_src['prev_link'] = '&laquo';

        $config_src['prev_tag_open'] = '<li class="prev">';

        $config_src['prev_tag_close'] = '</li>';

        $config_src['next_link'] = '&raquo';

        $config_src['next_tag_open'] = '<li>';

        $config_src['next_tag_close'] = '</li>';

        $config_src['last_tag_open'] = '<li>';

        $config_src['last_tag_close'] = '</li>';

        $config_src['cur_tag_open'] = '<li class="active"><a href="#">';

        $config_src['cur_tag_close'] = '</a></li>';

        $config_src['num_tag_open'] = '<li>';

        $config_src['num_tag_close'] = '</li>';

        $this->pagination->initialize($config_src);

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;



        !empty($config["per_page"]) ? $this->db->limit($config["per_page"], $page) : '';

        $this->db->select("r.*,u.name as customer,l.name as driver");

        $this->db->from("rides r");

        $this->db->join("users u", "u.id = r.user_id");

        $this->db->join("users l", "l.id = r.driver_id");

        $where = "r.ride_id != ''";



        if (!empty($search_term['email'])) {

            $where .= " and (u.name like '%" . $search_term['email'] . "%' OR l.name like '%" . $search_term['email'] . "%' OR r.pickup_adress like '%" . $search_term['email'] . "%' OR r.drop_address like '%" . $search_term['email'] . "%')";

        }

        if (!empty($search_term['is_active'])) {

            $where .= " and r.status = '" . $search_term['is_active'] . "' and ride_type= 1";

        }

        $this->db->where($where);

        $qry = $this->db->get();

        $data["result"] = $qry->result();



        $data['data'] = $search_term;



        $data["links"] = $this->pagination->create_links();

        $data['pg'] = $page;

        $data['chk'] = "search";



        $this->load->view('layout/header');

        $this->load->view('layout/sidebar');

        $this->load->view('admin/Doc_get_rides', $data);

        $this->load->view('layout/footer');

    }







  public function Nurse_rides_search() {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        $search_term = $this->searchterm_handler($_POST, TRUE);



        $config_src = array();

        $config_src["base_url"] = $this->config->base_url() . "admin/Nur_get_rides";



        $this->db->select("r.*,u.name as customer,l.name as driver");

        $this->db->from("rides r");

        $this->db->join("users u", "u.id = r.user_id");

        $this->db->join("users l", "l.id = r.driver_id");

        $where = "r.ride_id != ''";

        if (!empty($search_term['email'])) {

            $where .= " and (u.name like '%" . $search_term['email'] . "%' OR l.name like '%" . $search_term['email'] . "%' OR r.pickup_adress like '%" . $search_term['email'] . "%' OR r.drop_address like '%" . $search_term['email'] . "%')";

        }

        if (!empty($search_term['is_active'])) {

            $where .= " and r.status = '" . $search_term['is_active'] . "' and ride_type= 3";

        }

        $this->db->where($where);

        $qry = $this->db->get();





        $config_src["total_rows"] = count($qry->result());

        $config_src["per_page"] = 10;

        $config_src["uri_segment"] = 3;

        $choice = $config_src["total_rows"] / $config_src["per_page"];

        $config_src["num_links"] = round($choice);

//config for bootstrap pagination class integration

        $config_src['full_tag_open'] = '<ul class="pagination">';

        $config_src['full_tag_close'] = '</ul>';

        $config_src['first_link'] = false;

        $config_src['last_link'] = false;

        $config_src['first_tag_open'] = '<li>';

        $config_src['first_tag_close'] = '</li>';

        $config_src['prev_link'] = '&laquo';

        $config_src['prev_tag_open'] = '<li class="prev">';

        $config_src['prev_tag_close'] = '</li>';

        $config_src['next_link'] = '&raquo';

        $config_src['next_tag_open'] = '<li>';

        $config_src['next_tag_close'] = '</li>';

        $config_src['last_tag_open'] = '<li>';

        $config_src['last_tag_close'] = '</li>';

        $config_src['cur_tag_open'] = '<li class="active"><a href="#">';

        $config_src['cur_tag_close'] = '</a></li>';

        $config_src['num_tag_open'] = '<li>';

        $config_src['num_tag_close'] = '</li>';

        $this->pagination->initialize($config_src);

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;



        !empty($config["per_page"]) ? $this->db->limit($config["per_page"], $page) : '';

        $this->db->select("r.*,u.name as customer,l.name as driver");

        $this->db->from("rides r");

        $this->db->join("users u", "u.id = r.user_id");

        $this->db->join("users l", "l.id = r.driver_id");

        $where = "r.ride_id != ''";



        if (!empty($search_term['email'])) {

            $where .= " and (u.name like '%" . $search_term['email'] . "%' OR l.name like '%" . $search_term['email'] . "%' OR r.pickup_adress like '%" . $search_term['email'] . "%' OR r.drop_address like '%" . $search_term['email'] . "%')";

        }

        if (!empty($search_term['is_active'])) {

            $where .= " and r.status = '" . $search_term['is_active'] . "' and ride_type= 3";

        }

        $this->db->where($where);

        $qry = $this->db->get();

        $data["result"] = $qry->result();



        $data['data'] = $search_term;



        $data["links"] = $this->pagination->create_links();

        $data['pg'] = $page;

        $data['chk'] = "search";



        $this->load->view('layout/header');

        $this->load->view('layout/sidebar');

        $this->load->view('admin/Nur_get_rides', $data);

        $this->load->view('layout/footer');

    }

    public function pay() {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        $data = $this->input->post();

        $ids = $data['ids'];



        $vEmailSubject = 'Paypal payment';

        $environment = 'sandbox'; // or 'beta-sandbox' or 'live'.

// Set request-specific fields.

        $emailSubject = urlencode($vEmailSubject);

        $receiverType = urlencode('EmailAddress');

        $currency = urlencode('USD'); // or other currency ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')



        $keywords = explode(',', $ids);

        $receivers = array();

        $cnt = 1;

        foreach ($keywords as $keyword) {

            $keyword = trim($keyword);

            // $res = $this->db->get_where("users", array("user_id" => $keyword))->row();

            $this->db->select("SUM(r.amount) as amount,l.paypal_id");

            $this->db->from("rides r");

            $this->db->join("users l", "l.id = r.driver_id");

            $this->db->where("r.pay_driver", 0);

            $this->db->where("r.status", "COMPLETED");

            $this->db->where("l.user_id", $keyword);

            $this->db->group_by('r.driver_id');

            $qry = $this->db->get();

            $res = $qry->row();



            $receivers[] = array(

                'receiverEmail' => $res->paypal_id,

                'amount' => number_format((float) ($res->amount * $this->session->userdata("user")->driver_rate) / 100, 2, '.', ''),

                'uniqueID' => "id_" . ++$cnt,

                'note' => " pagamento de comiss Fashiontuts"

            );

            $cnt++;

        }





        $receiversLenght = count($receivers);



// Add request-specific fields to the request string.

        $nvpStr = "&EMAILSUBJECT=$emailSubject&RECEIVERTYPE=$receiverType&CURRENCYCODE=$currency";



        $receiversArray = array();



        for ($i = 0; $i < $receiversLenght; $i++) {

            $receiversArray[$i] = $receivers[$i];

        }



        foreach ($receiversArray as $i => $receiverData) {

            $receiverEmail = urlencode($receiverData['receiverEmail']);

            $amount = urlencode($receiverData['amount']);

            $uniqueID = urlencode($receiverData['uniqueID']);

            $note = urlencode($receiverData['note']);

            $nvpStr .= "&L_EMAIL$i=$receiverEmail&L_Amt$i=$amount&L_UNIQUEID$i=$uniqueID&L_NOTE$i=$note";

        }



// Execute the API operation; see the PPHttpPost function above.

        $httpParsedResponseAr = self::PPHttpPost('MassPay', $nvpStr);



        if ("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {

//            echo 'MassPay Completed Successfully: ' . $httpParsedResponseAr;



            $date = date('Y-m-d H:i:s');

            $keywords = explode(',', $ids);

            foreach ($keywords as $keyword) {

                $keyword = trim($keyword);

                $this->db->select("SUM(r.amount) as amount,l.paypal_id");

                $this->db->from("rides r");

                $this->db->join("users l", "l.id = r.driver_id");

                $this->db->where("r.pay_driver", 0);

                $this->db->where("r.status", "COMPLETED");

                $this->db->where("l.user_id", $keyword);

                $this->db->group_by('r.driver_id');

                $qry = $this->db->get();

                $res = $qry->row();

                $this->db

                        ->set('pay_driver', 1)

                        ->where('user_id', $keyword)

                        ->update('rides');

                $data = array(

                    'driver_id' => $keyword,

                    'amount' => ($res->amount * 10) / 100

                );

                $this->db->insert("payment_history", $data);

            }

            //$this->session->set_flashdata('flashPaySuccess', 'Added');

            echo 'ok';

        } else {

            // echo '\nMassPay failed: ';

           // print_r($httpParsedResponseAr);

            $this->session->set_flashdata('flashPayError', 'Added');

            echo 'fail';

        }

       // echo 1;

    }



    function PPHttpPost($methodName_, $nvpStr_) {

        global $environment;



// Set up your API credentials, PayPal end point, and API version.

// How to obtain API credentials:

// https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_NVPAPIBasics#id084E30I30RO

        $API_UserName = urlencode($this->session->userdata('user')->paypal_id);

        $API_Password = urlencode($this->session->userdata('user')->paypal_password);

        $API_Signature = urlencode($this->session->userdata('user')->signature);

//$API_Endpoint = "https://api-3t.paypal.com/nvp";



        $API_Endpoint = "https://api-3t.sandbox.paypal.com/nvp";



        $version = urlencode('2.3');



// Set the curl parameters.

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $API_Endpoint);

        curl_setopt($ch, CURLOPT_VERBOSE, 1);



// Turn off the server and peer verification (TrustManager Concept).

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);



        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_POST, 1);



// Set the API operation, version, and API signature in the request.

        $nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";



// Set the request as a POST FIELD for curl.

        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq . "&" . $nvpStr_);



// Get response from the server.

        $httpResponse = curl_exec($ch);



        if (!$httpResponse) {

            echo $methodName_ . ' failed: ' . curl_error($ch) . '(' . curl_errno($ch) . ')';

        }



// Extract the response details.

        $httpResponseAr = explode("&", $httpResponse);



        $httpParsedResponseAr = array();

        foreach ($httpResponseAr as $i => $value) {

            $tmpAr = explode("=", $value);

            if (sizeof($tmpAr) > 1) {

                $httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];

            }

        }



        if ((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {

            exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");

        }

//        print_r($httpParsedResponseAr);



        return $httpParsedResponseAr;

    }



    public function logout() {



        $this->session->sess_destroy();



        redirect($this->config->base_url(), 'refresh');

    }

    public function mail_setting(){

		if (!empty($_POST)) {

		    	

			if(!empty($_POST['SMTP_HOST']) && !empty($_POST['SMTP_PORT'])  && !empty($_POST['SMTP_USER'])  && !empty($_POST['SMTP_PASS'])){

                $this->db->where('name','SMTP_HOST');

                $this->db->update("settings", array("value"=>$_POST['SMTP_HOST']));

                

                $this->db->where('name','SMTP_PORT');

                $this->db->update("settings", array("value"=>$_POST['SMTP_PORT']));

				

				$this->db->where('name','SMTP_USER');

                $this->db->update("settings", array("value"=>$_POST['SMTP_USER']));

                

                $this->db->where('name','SMTP_PASS');

                $this->db->update("settings", array("value"=>$_POST['SMTP_PASS']));

                

				$this->db->where('name','FROM');

                $this->db->update("settings", array("value"=>$_POST['FROM']));

				

            }

			$this->session->set_userdata(array("msg" => "data successfully updated", "type" => "success"));

            redirect($this->config->base_url() . 'admin/settings');

        }

	}



    public function settings() {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        if (!empty($_POST['new_password']) && !empty($_POST['old_password'])) {

        	

            $qry = $this->db->query("select * from admin where password = md5('" . $_POST['old_password'] . "')");

            $res = $qry->result();

            if (!empty($res)) {

                $_POST['password'] = md5($_POST['new_password']);

                unset($_POST['new_password']);

                unset($_POST['old_password']);

            } else {

                $this->session->set_userdata(array("msg" => "Wrong password enter", "type" => "error"));

                redirect($this->config->base_url() . 'admin/settings', 'refresh');

            }

        }

        if (!empty($_POST)) {

       

        if(!empty($_POST['FARE']) && !empty($_POST['UNIT'])){

                $this->db->where('name','FARE');

                $this->db->update("settings", array("value"=>$_POST['FARE']));

                

                $this->db->where('name','UNIT');

                $this->db->update("settings", array("value"=>$_POST['UNIT']));

                

                unset($_POST['FARE']);

                unset($_POST['UNIT']);

            }

        

            $this->db->update("admin", $_POST);

            $this->session->set_userdata(array("msg" => "data successfully updated", "type" => "success"));

            redirect($this->config->base_url() . 'admin/settings');

        }

        $data['res'] = $this->db->get("admin")->row();

        $data['set'] = $this->db->get("settings")->result();



        $this->load->view('layout/header');

        $this->load->view('layout/sidebar');

        $this->load->view("admin/settings", $data);

        $this->load->view('layout/footer');

    }



    public function addType() {



        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }



        $data['types'] = $this->db->query("SELECT * FROM `types`")->result();



        if(isset($_POST['add'])){


            $res = $this->db->query("INSERT INTO `types`(`name`, `date`) VALUES ('".$_POST['type']."','".date('Y-m-d')."')");

            if($res){

                $this->session->set_userdata(array("msg" => "Data added Successfully", "type" => "success"));

            redirect($this->config->base_url() . 'admin/addType');

        }else{

            $this->session->set_userdata(array("msg" => "Error in Adding Data", "type" => "unsuccess"));

            redirect($this->config->base_url() . 'admin/addType');

        }



        }

        $this->load->view('layout/header');

        $this->load->view('layout/sidebar');

        $this->load->view("admin/type",$data);

        $this->load->view('layout/footer');

    }

    public function add_comm() {



        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }



        $data['Comm'] = $this->db->query("SELECT * FROM `tbl_commision`  ORDER BY `commission_id` DESC LIMIT 1")->result();



        if(isset($_POST['add'])){

            $res = $this->db->query("INSERT INTO `tbl_commision`(`For_Ambulance`, `For_Doctor`,`For_Nurse`) VALUES ('".$_POST['camb']."','".$_POST['cDoc']."','".$_POST['cnurse']."')");

            if($res){

                $this->session->set_userdata(array("msg" => "Data added Successfully", "type" => "success"));

            redirect($this->config->base_url() . 'admin/add_comm');

        }else{

            $this->session->set_userdata(array("msg" => "Error in Adding Data", "type" => "unsuccess"));

            redirect($this->config->base_url() . 'admin/add_comm');

        }



        }

        $this->load->view('layout/header');

        $this->load->view('layout/sidebar');

        $this->load->view("admin/addCommission",$data);

        $this->load->view('layout/footer');

    }
public function update_sub(){
  $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }
         
        
              
            unset($_POST['confirmpass']);

            unset($_POST['submit-register']);

            if (!empty($_FILES['icon']['name'])) {

                $path = $_FILES['icon']['name'];

                $ext = pathinfo($path, PATHINFO_EXTENSION);

                $rand = 'img_' . rand(1, 500) . rand(500, 1000) . rand(1, 500);

                $config['upload_path'] = BASEPATH . "../avatar/icon/";

                $config['allowed_types'] = '*';

                $config['file_name'] = $rand;

                $this->load->library('upload', $config);

                $this->upload->overwrite = true;

                $this->upload->do_upload('icon');

                $data = $this->upload->data();

                $_POST['icon'] = $this->config->base_url() . "avatar/icon/" . $rand . '.' . $ext;

            }

                if (!empty($_FILES['amb_img']['name'])) {

                $path = $_FILES['amb_img']['name'];

                $ext = pathinfo($path, PATHINFO_EXTENSION);

                $rand = 'img_' . time() . rand(1, 988);

                $config['upload_path'] = BASEPATH . "../avatar/icon/";

                $config['allowed_types'] = 'gif|jpg|png|jpeg';

                $config['file_name'] = $rand;

                $this->load->library('upload', $config);

                $this->upload->initialize($config);

                $this->upload->overwrite = true;

                if ($this->upload->do_upload('amb_img')) {

                    $data = $this->upload->data();

                    $_POST['amb_img'] = $this->config->base_url() . "avatar/icon/" . $rand . '.' . $ext;
                    

                    $amb_img = $_POST['amb_img'];

                } else {

                    $this->response(array("status" => "fail", "data" => "Error during file upload: only select jpg,png or gif."));

                    die;

                }

            }
            //   $this->db->where('id', $_POST['id']);

            // $this->db->update('subtype', $_POST);
          $name = $_POST['name'];
            $imagek =  $_POST['icon'];
             $detail = $_POST['detail'];
            $features =  $_POST['features'];
             $use = $_POST['use'];
       
              $cost = $_POST['cost'];
            $amb_img =  $_POST['amb_img'];
            $ids =  $_POST['id'];
            if($imagek =='' and $amb_img==''){
                 $upsub = $this->db->query("UPDATE `subtype` set `name`='".$name."',`feature`='".$features."',`details`='".$detail."',`when_use`='".$use."',`cost`='".$cost."' where `id`=".$ids."");
        
            }else{
         $upsub = $this->db->query("UPDATE `subtype` set `name`='".$name."',`icon`='".$imagek."', `amb_img`='".$amb_img."',`feature`='".$features."',`details`='".$detail."',`when_use`='".$use."',`cost`='".$cost."' where `id`=".$ids."");
     }
       
         if($upsub == TRUE){
              $this->session->set_userdata(array("msg" => "data successfully updated", "type" => "success"));
              header('location:' . $this->config->base_url() . 'admin/addSubType');
     
         }
         else{
             $this->session->set_userdata(array("msg" => "Error", "type" => "success"));
             header('location:' . $this->config->base_url() . 'admin/addSubType');
     

         }
          
        
                   

           

        

}



    

    public function addSubType() {

   $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }
       
         
     



        $data['res'] = $this->db->query("SELECT * FROM `types`")->result();

        $data['types'] = $this->db->query("SELECT * FROM `subtype`")->result();



        if(isset($_POST['add'])){



             if (!empty($_FILES['icon']['name'])) {

                $path = $_FILES['icon']['name'];

                $ext = pathinfo($path, PATHINFO_EXTENSION);

                $rand = 'img_' . time() . rand(1, 988);

                $config['upload_path'] = BASEPATH . "../avatar/icon/";

                $config['allowed_types'] = 'gif|jpg|png|jpeg';

                $config['file_name'] = $rand;

                $this->load->library('upload', $config);

                $this->upload->initialize($config);

                $this->upload->overwrite = true;

                if ($this->upload->do_upload('icon')) {

                    $data = $this->upload->data();

                    $_POST['icon'] = $this->config->base_url() . "avatar/icon/" . $rand . '.' . $ext;
                    

                    $icon = $_POST['icon'];

                } else {

                    $this->response(array("status" => "fail", "data" => "Error during file upload: only select jpg,png or gif."));

                    die;

                }

            }
             if (!empty($_FILES['amb_img']['name'])) {

                $path = $_FILES['amb_img']['name'];

                $ext = pathinfo($path, PATHINFO_EXTENSION);

                $rand = 'img_' . time() . rand(1, 988);

                $config['upload_path'] = BASEPATH . "../avatar/icon/";

                $config['allowed_types'] = 'gif|jpg|png|jpeg';

                $config['file_name'] = $rand;

                $this->load->library('upload', $config);

                $this->upload->initialize($config);

                $this->upload->overwrite = true;

                if ($this->upload->do_upload('amb_img')) {

                    $data = $this->upload->data();

                    $_POST['amb_img'] = $this->config->base_url() . "avatar/icon/" . $rand . '.' . $ext;
                    

                    $amb_img = $_POST['amb_img'];

                } else {

                    $this->response(array("status" => "fail", "data" => "Error during file upload: only select jpg,png or gif."));

                    die;

                }

            }
            
                  if($_POST['type'] == 2 ){
                    $t_name = 'Ambulance';
                  }
                  else if ($_POST['type'] == 3 ){
                    $t_name = 'Doctor';
                  }
                  else{
                     $t_name = 'Nurse';
                  }

            $res = $this->db->query("INSERT INTO `subtype`(`tid`,`t_name`,`name`,`icon`,`amb_img` ,`details`, `feature`, `when_use` ,`cost`) VALUES (".$_POST['type'].",'".$t_name."','".$_POST['stype']."','".$icon."','".$amb_img."','".$_POST['detail']."','".$_POST['features']."','".$_POST['use']."','".$_POST['cost']."')");

            // $sql = $this->db->last_query();

            // echo $sql;

            // exit();



            if($res){

                $this->session->set_userdata(array("msg" => "Data added Successfully", "type" => "success"));

            redirect($this->config->base_url() . 'admin/addSubType');

        }else{

            $this->session->set_userdata(array("msg" => "Error in Adding Data", "type" => "unsuccess"));

            redirect($this->config->base_url() . 'admin/addSubType');

        }

  





        }

        $this->load->view('layout/header');

        $this->load->view('layout/sidebar');

        $this->load->view("admin/subtype", $data);

        $this->load->view('layout/footer');

    }





 public function amb_select()  

  {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        if (!empty($_POST['new_password']) && !empty($_POST['old_password'])) {

            

            $qry = $this->db->query("select * from admin where password = md5('" . $_POST['old_password'] . "')");

            $res = $qry->result();

            if (!empty($res)) {

                $_POST['password'] = md5($_POST['new_password']);

                unset($_POST['new_password']);

                unset($_POST['old_password']);

            } else {

                $this->session->set_userdata(array("msg" => "Wrong password enter", "type" => "error"));

                redirect($this->config->base_url() . 'admin/amb_select', 'refresh');

            }

        }

        if (!empty($_POST)) {

       

        if(!empty($_POST['FARE']) && !empty($_POST['UNIT'])){

                $this->db->where('name','FARE');

                $this->db->update("Ambulance_settings", array("value"=>$_POST['FARE'],"subtype"=>$_POST['subtype']));

                

                $this->db->where('name','UNIT');

                $this->db->update("Ambulance_settings", array("value"=>$_POST['UNIT'],"subtype"=>$_POST['subtype']));

                

                unset($_POST['FARE']);

                unset($_POST['UNIT']);

            }

        

            $this->db->update("admin", $_POST);

            $this->session->set_userdata(array("msg" => "data successfully updated", "type" => "success"));

            redirect($this->config->base_url() . 'admin/amb_select');

        }

        $data['res'] = $this->db->get("admin")->row();

        $data['set'] = $this->db->get("Ambulance_settings")->result();

          $data['ress'] = $this->db->query("SELECT * FROM `subtype` where `tid`=2")->result();

           // $query['h'] = $this->db->query("SELECT `id`,`name` FROM `subtype` where `tid`=2")->row();  

           



        $this->load->view('layout/header');

        $this->load->view('layout/sidebar');

        $this->load->view("admin/Amb_setting", $data);

        $this->load->view('layout/footer');

    }

    public function load_subtype()

      {  

         

          $this->load->view("admin/Amb_setting", $data);

           

      } 



          public function Nurse_load_subtype()

      {  

           $data['Nurse'] = $this->db->query("SELECT * FROM `subtype` where `tid`=3")->result();

           // $query['h'] = $this->db->query("SELECT `id`,`name` FROM `subtype` where `tid`=2")->row();  



          $this->load->view("admin/Nurse_setting", $data);

           

      } 

      public function Doc_select() {

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        if (!empty($_POST['new_password']) && !empty($_POST['old_password'])) {

            

            $qry = $this->db->query("select * from admin where password = md5('" . $_POST['old_password'] . "')");

            $res = $qry->result();

            if (!empty($res)) {

                $_POST['password'] = md5($_POST['new_password']);

                unset($_POST['new_password']);

                unset($_POST['old_password']);

            } else {

                $this->session->set_userdata(array("msg" => "Wrong password enter", "type" => "error"));

                redirect($this->config->base_url() . 'admin/Doc_select', 'refresh');

            }

        }

        if (!empty($_POST)) {

       

        if(!empty($_POST['FARE']) && !empty($_POST['UNIT'])){

                $this->db->where('name','FARE');

                $this->db->update("Doctor_settings", array("value"=>$_POST['FARE'],"subtype"=>$_POST['subtype']));

                

                $this->db->where('name','UNIT');

                $this->db->update("Doctor_settings", array("value"=>$_POST['UNIT'],"subtype"=>$_POST['subtype']));

                

                unset($_POST['FARE']);

                unset($_POST['UNIT']);

            }

        

            $this->db->update("admin", $_POST);

            $this->session->set_userdata(array("msg" => "data successfully updated", "type" => "success"));

            redirect($this->config->base_url() . 'admin/Doc_select');

        }

        $data['res'] = $this->db->get("admin")->row();

        $data['set'] = $this->db->get("Doctor_settings")->result();

        $data['ress'] = $this->db->query("SELECT * FROM `subtype` where `tid`=1")->result();

        $this->load->view('layout/header');

        $this->load->view('layout/sidebar');

        $this->load->view("admin/Doctor_setting", $data);

        $this->load->view('layout/footer');

    }

  public function Nur_select() {  

       

        $session = $this->session->userdata('user');

        if (empty($session->id)) {

            redirect($this->config->base_url());

        }

        if (!empty($_POST['new_password']) && !empty($_POST['old_password'])) {

            

            $qry = $this->db->query("select * from admin where password = md5('" . $_POST['old_password'] . "')");

            $res = $qry->result();

            if (!empty($res)) {

                $_POST['password'] = md5($_POST['new_password']);

                unset($_POST['new_password']);

                unset($_POST['old_password']);

            } else {

                $this->session->set_userdata(array("msg" => "Wrong password enter", "type" => "error"));

                redirect($this->config->base_url() . 'admin/Nur_select', 'refresh');

            }

        }

        if (!empty($_POST)) {

       

        if(!empty($_POST['FARE']) && !empty($_POST['UNIT'])){

                $this->db->where('name','FARE');

                $this->db->update("Nurse_settings", array("value"=>$_POST['FARE'],"subtype"=>$_POST['subtype']));

                

                $this->db->where('name','UNIT');

                $this->db->update("Nurse_settings", array("value"=>$_POST['UNIT'],"subtype"=>$_POST['subtype']));

                

                unset($_POST['FARE']);

                unset($_POST['UNIT']);

            }

        

            $this->db->update("admin", $_POST);

            $this->session->set_userdata(array("msg" => "data successfully updated", "type" => "success"));

            redirect($this->config->base_url() . 'admin/Nur_select');

        }

        $data['res'] = $this->db->get("admin")->row();

        $data['set'] = $this->db->get("Nurse_settings")->result();

        $data['ress'] = $this->db->query("SELECT * FROM `subtype` where `tid`=3")->result();

        $this->load->view('layout/header');

        $this->load->view('layout/sidebar');

        $this->load->view("admin/Nurse_setting", $data);

        $this->load->view('layout/footer');

    }

    // public function click_view(){

    //       $session = $this->session->userdata('user');

    //     if (empty($session->id)) {

    //         redirect($this->config->base_url());

    //     }

    //     $res['post'] = $this->db->get_where("users", array("user_id" => $this->input->post("user_id")))->row_array();

    //      $res['postride'] = $this->db->get_where("rides", array("user_id" => $this->input->post("user_id")))->row_array();

    //     $this->load->view('admin/ajax/view_Doc', $res);

    // }

    // }

   public function subtyp_delete(){

  

            $this->db->where('id', $_POST['id']);

            $this->db->delete('subtype');

  



}

}





/* End of file welcome.php */

/* Location: ./application/controllers/welcome.php */