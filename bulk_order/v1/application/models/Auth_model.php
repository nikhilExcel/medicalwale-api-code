<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model
{
    
    public function get_warehouse_by_id($id)
    {
		$this->db->select('name,lattitude,longitude');
        $this->db->where_in('id', $id);
        $this->db->order_by('name');
        $query = $this->db->get('vendor_shipping_details');
        return $query->row();
    }    
    
    public function get_address_by_id($id)
    {
		$this->db->select('name,lattitude,longitude,city');
        $this->db->where_in('id', $id);
        $this->db->order_by('name');
        $query = $this->db->get('address');
        return $query->row();
    }
    
    function getDistance($latitude1, $longitude1, $latitude2, $longitude2, $unit = 'kilometers') {
      $theta = $longitude1 - $longitude2; 
      $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta))); 
      $distance = acos($distance); 
      $distance = rad2deg($distance); 
      $distance = $distance * 60 * 1.1515; 
      switch($unit) { 
        case 'miles': 
          break; 
        case 'kilometers' : 
          $distance = $distance * 1.609344; 
      } 
      return (round($distance,2)); 
    }
    
    public function get_vendor_name($id)
    {
        $id = clean_number($id);
		$this->db->select('company_name');
        $this->db->where('vendor_id', $id);
        $query = $this->db->get('vendor_communication_details');
        $sql= $query->row();
        if($query->num_rows()>0){
         $sql= $query->row();
         return $sql->company_name;
        }
        else{
         return '';
        }
    }
	
	public function get_product_name($id)  {
        $query = $this->db->query("SELECT master_id,title FROM `products` WHERE id='$id' LIMIT 1");
        if (!empty($query)) {
            $item=$query->row_array(); 
            $product_name = $item['title'];
            $master_id = $item['master_id'];
		    if ($master_id != '0') {
                $sql_query= $this->db->query("SELECT title FROM product_master where id='$master_id'");
                 if($sql_query->num_rows()>0){
                     $sql= $sql_query->row();
                      $product_name = $sql->title;
                    }
                    else{
                      $product_name = '';
                    }
                
                
            }
        }
        return $product_name;
    } 
    
    public function get_product_weight($id)  {
        $query = $this->db->query("SELECT master_id,title FROM `products` WHERE id='$id' LIMIT 1");
        if (!empty($query)) {
            $item=$query->row_array(); 
            $product_weight = '';
            $master_id = $item['master_id'];
		    if ($master_id != '0') {
                $sql_query= $this->db->query("SELECT weight FROM product_master where id='$master_id'");
                  if($sql_query->num_rows()>0){
                     $sql= $sql_query->row();
                      $product_weight = $sql->weight;
                    }
                    else{
                      $product_weight = '';
                    }
            }
        }
        return $product_weight;
    } 
	
	public function get_school_name($id)
    {
        $id = clean_number($id);
        $this->db->where('id', $id);
        $query = $this->db->get('school');
        if($query->num_rows()>0){
         $sql= $query->row();
         return $sql->name;
        }
        else{
         return '';
        }        
    }
	
	 public function get_board_name($id){
        $id = clean_number($id);
        $this->db->where('id', $id);
        $query = $this->db->get('board');
        if($query->num_rows()>0){
         $sql= $query->row();
         return $sql->name;
        }
        else{
         return '';
        }        
    }


    
  public function get_grade_name($id)
    {
        $id = clean_number($id);
        $this->db->where('id', $id);
        $query = $this->db->get('grade_list');
         if($query->num_rows()>0){
         $sql= $query->row();
         return $sql->name;
        }
        else{
         return '';
        }
    }
    
    
    public function get_state_name($id)
    {
        $this->db->where('states.id', $id);
        $query = $this->db->get('states');
        $sql=$query->row();
        return $sql->name;
    }
    
    public function get_city_name($id)
    {
        $this->db->where('cities.id', $id);
        $query = $this->db->get('cities');
        $sql= $query->row();
        return $sql->name;
    }
   
    public function get_warehouse_details_by_id($id) {
        $resultdata = array();
        $query = $this->db->query("SELECT name,address,state_id,city_id,pincode,landmark,lattitude,longitude,contact_number FROM `vendor_shipping_details` WHERE id='$id' ORDER BY id desc");
        if (!empty($query)) {
               $item=$query->row_array(); 
                $resultdata = array(
                    "name" => $item['name'],
                    "address" => $item['address'],
                    "state" => $this->auth_model->get_state_name($item['state_id']),
                    "city" => $this->auth_model->get_city_name($item['city_id']),
                    "pincode" => $item['pincode'],
                    "landmark" => $item['landmark'],
                    "lattitude" => $item['lattitude'],
                    "longitude" => $item['longitude'],
                    "contact_number" => $item['contact_number'],
                );
            
        }
        return $resultdata;
     
    }  
    
    public function get_category_name($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('categories');
        if($query->num_rows > 0){
            $sql= $query->row();
            return $sql->name;
        }else{
            return '';
        }
        
    }

	//generate uniqe username
	public function generate_uniqe_username($username)
	{
		$new_username = $username;
		if (!empty($this->get_user_by_username($new_username))) {
			$new_username = $username . " 1";
			if (!empty($this->get_user_by_username($new_username))) {
				$new_username = $username . " 2";
				if (!empty($this->get_user_by_username($new_username))) {
					$new_username = $username . " 3";
					if (!empty($this->get_user_by_username($new_username))) {
						$new_username = $username . "-" . uniqid();
					}
				}
			}
		}
		return $new_username;
	}

	//generate uniqe slug
	public function generate_uniqe_slug($username)
	{
		$slug = str_slug($username);
		if (!empty($this->get_user_by_slug($slug))) {
			$slug = str_slug($username . "-1");
			if (!empty($this->get_user_by_slug($slug))) {
				$slug = str_slug($username . "-2");
				if (!empty($this->get_user_by_slug($slug))) {
					$slug = str_slug($username . "-3");
					if (!empty($this->get_user_by_slug($slug))) {
						$slug = str_slug($username . "-" . uniqid());
					}
				}
			}
		}
		return $slug;
	}



	//update slug
	public function update_slug($id)
	{
		$id = clean_number($id);
		$user = $this->get_user($id);

		if (empty($user->slug) || $user->slug == "-") {
			$data = array(
				'slug' => "user-" . $user->id,
			);
			$this->db->where('id', $id);
			$this->db->update('users', $data);

		} else {
			if ($this->check_is_slug_unique($user->slug, $id) == true) {
				$data = array(
					'slug' => $user->slug . "-" . $user->id
				);

				$this->db->where('id', $id);
				$this->db->update('users', $data);
			}
		}
	}

	//reset password
	public function reset_password($id)
	{
		$id = clean_number($id);
		$this->load->library('bcrypt');
		$new_password = $this->input->post('password', true);
		$data = array(
			'password' => $this->bcrypt->hash_password($new_password),
			'token' => generate_token()
		);
		//change password
		$this->db->where('id', $id);
		return $this->db->update('users', $data);
	}


	//get user by id
	public function get_user($id)
	{
		$id = clean_number($id);
		$this->db->where('id', $id);
		$query = $this->db->get('users');
		return $query->row();
	}

	//get user by email
	public function get_user_by_email($email)
	{
		$this->db->where('email', $email);
		$this->db->where('role', 'vendor');
		$query = $this->db->get('users');
		return $query->row();
	}


	//get user by mobile
	public function get_user_by_mobile($phone_number)
	{
		$this->db->where('phone_number', $phone_number);
		$this->db->where('role', 'vendor');
		$query = $this->db->get('users');
		return $query->row();
	}


	//get user by username
	public function get_user_by_username($username)
	{
		$username = remove_special_characters($username);
		$this->db->where('username', $username);
		$this->db->where('role', 'vendor');
		$query = $this->db->get('users');
		return $query->row();
	}

	//get user by shop name
	public function get_user_by_shop_name($shop_name)
	{
		$shop_name = remove_special_characters($shop_name);
		$this->db->where('shop_name', $shop_name);
		$this->db->where('role', 'vendor');
		$query = $this->db->get('users');
		return $query->row();
	}

	//get user by slug
	public function get_user_by_slug($slug)
	{
		$this->db->where('slug', $slug);
		$query = $this->db->get('users');
		return $query->row();
	}

	//get user by token
	public function get_user_by_token($token)
	{
		$token = remove_special_characters($token);
		$this->db->where('token', $token);
		$query = $this->db->get('users');
		return $query->row();
	}

	//get users
	public function get_users()
	{
		$query = $this->db->get('users');
		return $query->result();
	}

	//get users count
	public function get_users_count()
	{
		$query = $this->db->get('users');
		return $query->num_rows();
	}

	//get members
	public function get_members()
	{
		$this->db->where('role', "member");
		$query = $this->db->get('users');
		return $query->result();
	}

	//get vendors
	public function get_vendors()
	{
		$this->db->where('role', "vendor");
		$query = $this->db->get('users');
		return $query->result();
	}
	
	//check slug
	public function check_is_slug_unique($slug, $id)
	{
		$id = clean_number($id);
		$this->db->where('users.slug', $slug);
		$this->db->where('users.id !=', $id);
		$query = $this->db->get('users');
		if ($query->num_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	//check if email is unique
	public function is_unique_email($email, $user_id = 0)
	{
		$user_id = clean_number($user_id);
		$user = $this->auth_model->get_user_by_email($email);

		//if id doesnt exists
		if ($user_id == 0) {
			if (empty($user)) {
				return true;
			} else {
				return false;
			}
		}

		if ($user_id != 0) {
			if (!empty($user) && $user->id != $user_id) {
				//email taken
				return false;
			} else {
				return true;
			}
		}
	}
	
	
	
	//check if email is unique
	public function is_unique_mobile($phone_number, $user_id = 0)
	{
		$user_id = clean_number($user_id);
		$user = $this->auth_model->get_user_by_mobile($phone_number);

		//if id doesnt exists
		if ($user_id == 0) {
			if (empty($user)) {
				return true;
			} else {
				return false;
			}
		}

		if ($user_id != 0) {
			if (!empty($user) && $user->id != $user_id) {
				//email taken
				return false;
			} else {
				return true;
			}
		}
	}

	//check if username is unique
	public function is_unique_username($username, $user_id = 0)
	{
		$user = $this->get_user_by_username($username);

		//if id doesnt exists
		if ($user_id == 0) {
			if (empty($user)) {
				return true;
			} else {
				return false;
			}
		}

		if ($user_id != 0) {
			if (!empty($user) && $user->id != $user_id) {
				//username taken
				return false;
			} else {
				return true;
			}
		}
	}

	//check if shop name is unique
	public function is_unique_shop_name($shop_name, $user_id = 0)
	{
		$user = $this->get_user_by_shop_name($shop_name);

		//if id doesnt exists
		if ($user_id == 0) {
			if (empty($user)) {
				return true;
			} else {
				return false;
			}
		}

		if ($user_id != 0) {
			if (!empty($user) && $user->id != $user_id) {
				//shop name taken
				return false;
			} else {
				return true;
			}
		}
	}

	//verify email
	public function verify_email($user)
	{
		if (!empty($user)) {
			$data = array(
				'email_status' => 1,
				'token' => generate_token()
			);
			$this->db->where('id', $user->id);
			return $this->db->update('users', $data);
		}
		return false;
	}

	//ban or remove user ban
	public function ban_remove_ban_user($id)
	{
		$id = clean_number($id);
		$user = $this->get_user($id);

		if (!empty($user)) {
			$data = array();
			if ($user->banned == 0) {
				$data['banned'] = 1;
			}
			if ($user->banned == 1) {
				$data['banned'] = 0;
			}

			$this->db->where('id', $id);
			return $this->db->update('users', $data);
		}

		return false;
	}

	//open close user shop
	public function open_close_user_shop($id)
	{
		$id = clean_number($id);
		$user = $this->get_user($id);

		if (!empty($user)) {
			$data = array();
			if ($user->role == 'member') {
				$data['role'] = 'vendor';
			} else {
				$data['role'] = 'member';
			}
			$this->db->where('id', $id);
			return $this->db->update('users', $data);
		}

		return false;
	}
	
	
		

 public function generatePIN($digits = 4){
        $i = 0; //counter
        $pin = ""; 
        while($i < $digits){
            $pin .= mt_rand(0, 9);
            $i++;
        }
        return $pin;
    }
     
   public function send_sms($message_user,$sender_mobile){
        $message_user = urlencode($message_user);
        $apikey='8IFp3Q20jtu0bTGH';
        $sender = 'SKOOZO'; 
        $url =  'https://buzzify.in/V2/http-api.php?apikey='.$apikey.'&senderid='.$sender.'&number='.$sender_mobile.'&message='.$message_user.'&format=json'; 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT,5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch); 
        curl_close($ch);  
        
	}
	
	  public function sent_mail($user_msg,$user_email,$email_subject) {

       
        $this->load->library('email'); 
        $config['protocol']    = 'smtp';
    	$config['smtp_host']    = 'ssl://smtp.googlemail.com';
    	$config['smtp_port']    = '465';
    	$config['smtp_timeout'] = '7';
    	$config['smtp_user']    = 'noreply@skoozo.com';
    	$config['smtp_pass']    = 'skoozo@3221';
    	$config['charset']    = 'utf-8';
    	$config['newline']    = "\r\n";
    	$config['mailtype'] = 'html'; // or html
    	$config['validation'] = TRUE; // bool whether to validate email or not    
    	$this->email->initialize($config);
  
  
        $this->email->to($user_email);
        $this->email->from('noreply@skoozo.com', 'Skoozo');
        $this->email->subject($email_subject);
        

       

//Email content
        $message = '<style type="text/css">
              /* Client-specific Styles */
              #outlook a {padding:0;} /* Force Outlook to provide a "view in browser" menu link. */
              body{width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0;}
              /* Prevent Webkit and Windows Mobile platforms from changing default font sizes, while not breaking desktop design. */
              .ExternalClass {width:100%;} /* Force Hotmail to display emails at full width */
              .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;} /* Force Hotmail to display normal line spacing.  */
              #backgroundTable {margin:0; padding:0; width:100% !important; }
              img {outline:none; text-decoration:none;border:none; -ms-interpolation-mode: bicubic; height:auto;}
              a img {border:none;}
              .image_fix {display:block;}
              /*p {margin: 0px 0px !important;}*/
              table td {border-collapse: collapse;}
              table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }
              a {text-decoration:none!important;}
              /*STYLES*/
              table[class=full] { width: 100%; clear: both; }
              /*IPAD STYLES*/
              @media only all and (max-width: 640px) {
              a[href^="tel"], a[href^="sms"] {
              text-decoration: none;
              color: #ffffff; /* or whatever your want */
              pointer-events: none;
              cursor: default;
              }
              .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
              text-decoration: default;
              color: #ffffff !important;
              pointer-events: auto;
              cursor: default;
              }
              table[class=devicewidth] {width: 440px!important;}
              td[class=devicewidth] {width: 440px!important;}
              img[class=devicewidth] {width: 440px!important;}
              img[class=banner] {width: 440px!important;height:147px!important;}
              table[class=devicewidthinner] {width: 420px!important;}
              table[class=icontext] {width: 345px!important;text-align:center!important;}
              img[class="colimg2"] {width:420px!important;height:243px!important;}
              table[class="emhide"]{display: none!important;}
              img[class="logo"]{width:440px!important;height:110px!important;}
              img[class=responsiveImg] {
              display:inline-block;
              max-width:100% !important;
              width:100% !important;
              height:auto !important;
              }
  			h1[class="hdrTxt"]{font-size: 35px;}

              }
              /*IPHONE STYLES*/
              @media only all and (max-width: 480px) {
              a[href^="tel"], a[href^="sms"] {
              text-decoration: none;
              color: #ffffff; /* or whatever your want */
              pointer-events: none;
              cursor: default;
              }
              .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
              text-decoration: default;
              color: #ffffff !important;
              pointer-events: auto;
              cursor: default;
              }
              table[class=devicewidth] {width: 100%!important;}
              td[class=devicewidth] {width: 100%!important;}
              img[class=devicewidth] {width: 100%!important;}
              img[class=banner] {width: 100%!important;height:93px!important;}
              table[class=devicewidthinner] {width: 100%!important;}
              table[class=icontext] {width: 186px!important;text-align:center!important;}
              img[class="colimg2"] {width:260px!important;height:150px!important;}
              table[class="emhide"]{display: none!important;}
              img[class="logo"]{width:280px!important;height:70px!important;}
              h1[class="hdrTxt"]{font-size: 28px!important;}
  			td[class="mobilePad"], table[class="mobilePad"]{ padding-bottom:10px}
              }
          </style>
  <table width="100%" bgcolor="#eaeaea" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" style="">
  <tbody>
  <tr>
  <td style="padding-top:10px">
  <table width="630" cellpadding="15" bgcolor="#ffffff" cellspacing="0" border="0" align="center" class="devicewidth" style="border-top-left-radius:7px; border-top-right-radius:7px;border-bottom:1px solid #cccccc; margin:0 auto;background: rgb(234, 234, 234) none repeat scroll 0% 0%;
  padding-top: 0px;">
  <tbody>
  <tr>
  <td width="100%">
  <table width="100%" align="left" border="0" cellpadding="0" cellspacing="0">
  <tbody>
  <tr>
  <td align="center" bgcolor="#000" style="background: rgb(255, 255, 255) none repeat scroll 0% 0%;padding: 10px;">
  <a target="_blank" href="https://vendor.skoozo.com/" >
  <img border="0" alt="" style="display:block; border:none; outline:none; text-decoration:none;width:250px;" src="https://skoozo.com/uploads/logo/logo_5e04766321b5c.png" class="logo">
  </a>
  </td>
  </tr>
  <tr>
    '.$user_msg.'
  
  </tr>
  </tbody>
  </table>
  </td>
  </tr>
  </tbody>
  </table>
  </td>
  </tr>
  </tbody>
  </table>';

        
        $this->email->message($message);
        $this->email->send();
        //$this->email->print_debugger();
    }	
	
  public function sent_mail_attach($user_msg,$user_email,$email_subject,$attach) {
        $this->load->library('email'); 
        $config['protocol']    = 'smtp';
    	$config['smtp_host']    = 'ssl://smtp.googlemail.com';
    	$config['smtp_port']    = '465';
    	$config['smtp_timeout'] = '7';
    	$config['smtp_user']    = 'noreply@skoozo.com';
    	$config['smtp_pass']    = 'skoozo@3221';
    	$config['charset']    = 'utf-8';
    	$config['newline']    = "\r\n";
    	$config['mailtype'] = 'html'; // or html
    	$config['validation'] = TRUE; // bool whether to validate email or not    
    	$this->email->initialize($config);
  
  
        $this->email->to($user_email);
        $this->email->from('noreply@skoozo.com', 'Skoozo');
        $this->email->subject($email_subject);
        

       

//Email content
        $message = '<style type="text/css">
              /* Client-specific Styles */
              #outlook a {padding:0;} /* Force Outlook to provide a "view in browser" menu link. */
              body{width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0;}
              /* Prevent Webkit and Windows Mobile platforms from changing default font sizes, while not breaking desktop design. */
              .ExternalClass {width:100%;} /* Force Hotmail to display emails at full width */
              .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;} /* Force Hotmail to display normal line spacing.  */
              #backgroundTable {margin:0; padding:0; width:100% !important; }
              img {outline:none; text-decoration:none;border:none; -ms-interpolation-mode: bicubic; height:auto;}
              a img {border:none;}
              .image_fix {display:block;}
              /*p {margin: 0px 0px !important;}*/
              table td {border-collapse: collapse;}
              table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }
              a {text-decoration:none!important;}
              /*STYLES*/
              table[class=full] { width: 100%; clear: both; }
              /*IPAD STYLES*/
              @media only all and (max-width: 640px) {
              a[href^="tel"], a[href^="sms"] {
              text-decoration: none;
              color: #ffffff; /* or whatever your want */
              pointer-events: none;
              cursor: default;
              }
              .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
              text-decoration: default;
              color: #ffffff !important;
              pointer-events: auto;
              cursor: default;
              }
              table[class=devicewidth] {width: 440px!important;}
              td[class=devicewidth] {width: 440px!important;}
              img[class=devicewidth] {width: 440px!important;}
              img[class=banner] {width: 440px!important;height:147px!important;}
              table[class=devicewidthinner] {width: 420px!important;}
              table[class=icontext] {width: 345px!important;text-align:center!important;}
              img[class="colimg2"] {width:420px!important;height:243px!important;}
              table[class="emhide"]{display: none!important;}
              img[class="logo"]{width:440px!important;height:110px!important;}
              img[class=responsiveImg] {
              display:inline-block;
              max-width:100% !important;
              width:100% !important;
              height:auto !important;
              }
  			h1[class="hdrTxt"]{font-size: 35px;}

              }
              /*IPHONE STYLES*/
              @media only all and (max-width: 480px) {
              a[href^="tel"], a[href^="sms"] {
              text-decoration: none;
              color: #ffffff; /* or whatever your want */
              pointer-events: none;
              cursor: default;
              }
              .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
              text-decoration: default;
              color: #ffffff !important;
              pointer-events: auto;
              cursor: default;
              }
              table[class=devicewidth] {width: 100%!important;}
              td[class=devicewidth] {width: 100%!important;}
              img[class=devicewidth] {width: 100%!important;}
              img[class=banner] {width: 100%!important;height:93px!important;}
              table[class=devicewidthinner] {width: 100%!important;}
              table[class=icontext] {width: 186px!important;text-align:center!important;}
              img[class="colimg2"] {width:260px!important;height:150px!important;}
              table[class="emhide"]{display: none!important;}
              img[class="logo"]{width:280px!important;height:70px!important;}
              h1[class="hdrTxt"]{font-size: 28px!important;}
  			td[class="mobilePad"], table[class="mobilePad"]{ padding-bottom:10px}
              }
          </style>
  <table width="100%" bgcolor="#eaeaea" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" style="">
  <tbody>
  <tr>
  <td style="padding-top:10px">
  <table width="630" cellpadding="15" bgcolor="#ffffff" cellspacing="0" border="0" align="center" class="devicewidth" style="border-top-left-radius:7px; border-top-right-radius:7px;border-bottom:1px solid #cccccc; margin:0 auto;background: rgb(234, 234, 234) none repeat scroll 0% 0%;
  padding-top: 0px;">
  <tbody>
  <tr>
  <td width="100%">
  <table width="100%" align="left" border="0" cellpadding="0" cellspacing="0">
  <tbody>
  <tr>
  <td align="center" bgcolor="#000" style="background: rgb(255, 255, 255) none repeat scroll 0% 0%;padding: 10px;">
  <a target="_blank" href="https://skoozo.com/" >
  <img border="0" alt="" style="display:block; border:none; outline:none; text-decoration:none;width:250px;" src="https://skoozo.com/uploads/logo/logo_5e04766321b5c.png" class="logo">
  </a>
  </td>
  </tr>
  <tr>
    '.$user_msg.'
  
  </tr>
  </tbody>
  </table>
  </td>
  </tr>
  </tbody>
  </table>
  </td>
  </tr>
  </tbody>
  </table>';

        
        $this->email->message($message);
        $this->email->attach($attach);
        $this->email->send();
        //$this->email->print_debugger();
    }	




  public function sent_mail_attach_bcc($user_msg,$user_email,$email_subject,$attach,$bcc) {

       
        $this->load->library('email'); 
        $config['protocol']    = 'smtp';
    	$config['smtp_host']    = 'ssl://smtp.googlemail.com';
    	$config['smtp_port']    = '465';
    	$config['smtp_timeout'] = '7';
    	$config['smtp_user']    = 'noreply@skoozo.com';
    	$config['smtp_pass']    = 'skoozo@3221';
    	$config['charset']    = 'utf-8';
    	$config['newline']    = "\r\n";
    	$config['mailtype'] = 'html'; // or html
    	$config['validation'] = TRUE; // bool whether to validate email or not    
    	$this->email->initialize($config);
  
  
        $this->email->to($user_email);
        $this->email->from('noreply@skoozo.com', 'Skoozo');
        $this->email->bcc('accounts@skoozo.com');
        $this->email->subject($email_subject);
        

       

//Email content
        $message = '<style type="text/css">
              /* Client-specific Styles */
              #outlook a {padding:0;} /* Force Outlook to provide a "view in browser" menu link. */
              body{width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0;}
              /* Prevent Webkit and Windows Mobile platforms from changing default font sizes, while not breaking desktop design. */
              .ExternalClass {width:100%;} /* Force Hotmail to display emails at full width */
              .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;} /* Force Hotmail to display normal line spacing.  */
              #backgroundTable {margin:0; padding:0; width:100% !important; }
              img {outline:none; text-decoration:none;border:none; -ms-interpolation-mode: bicubic; height:auto;}
              a img {border:none;}
              .image_fix {display:block;}
              /*p {margin: 0px 0px !important;}*/
              table td {border-collapse: collapse;}
              table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }
              a {text-decoration:none!important;}
              /*STYLES*/
              table[class=full] { width: 100%; clear: both; }
              /*IPAD STYLES*/
              @media only all and (max-width: 640px) {
              a[href^="tel"], a[href^="sms"] {
              text-decoration: none;
              color: #ffffff; /* or whatever your want */
              pointer-events: none;
              cursor: default;
              }
              .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
              text-decoration: default;
              color: #ffffff !important;
              pointer-events: auto;
              cursor: default;
              }
              table[class=devicewidth] {width: 440px!important;}
              td[class=devicewidth] {width: 440px!important;}
              img[class=devicewidth] {width: 440px!important;}
              img[class=banner] {width: 440px!important;height:147px!important;}
              table[class=devicewidthinner] {width: 420px!important;}
              table[class=icontext] {width: 345px!important;text-align:center!important;}
              img[class="colimg2"] {width:420px!important;height:243px!important;}
              table[class="emhide"]{display: none!important;}
              img[class="logo"]{width:440px!important;height:110px!important;}
              img[class=responsiveImg] {
              display:inline-block;
              max-width:100% !important;
              width:100% !important;
              height:auto !important;
              }
  			h1[class="hdrTxt"]{font-size: 35px;}

              }
              /*IPHONE STYLES*/
              @media only all and (max-width: 480px) {
              a[href^="tel"], a[href^="sms"] {
              text-decoration: none;
              color: #ffffff; /* or whatever your want */
              pointer-events: none;
              cursor: default;
              }
              .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
              text-decoration: default;
              color: #ffffff !important;
              pointer-events: auto;
              cursor: default;
              }
              table[class=devicewidth] {width: 100%!important;}
              td[class=devicewidth] {width: 100%!important;}
              img[class=devicewidth] {width: 100%!important;}
              img[class=banner] {width: 100%!important;height:93px!important;}
              table[class=devicewidthinner] {width: 100%!important;}
              table[class=icontext] {width: 186px!important;text-align:center!important;}
              img[class="colimg2"] {width:260px!important;height:150px!important;}
              table[class="emhide"]{display: none!important;}
              img[class="logo"]{width:280px!important;height:70px!important;}
              h1[class="hdrTxt"]{font-size: 28px!important;}
  			td[class="mobilePad"], table[class="mobilePad"]{ padding-bottom:10px}
              }
          </style>
  <table width="100%" bgcolor="#eaeaea" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" style="">
  <tbody>
  <tr>
  <td style="padding-top:10px">
  <table width="630" cellpadding="15" bgcolor="#ffffff" cellspacing="0" border="0" align="center" class="devicewidth" style="border-top-left-radius:7px; border-top-right-radius:7px;border-bottom:1px solid #cccccc; margin:0 auto;background: rgb(234, 234, 234) none repeat scroll 0% 0%;
  padding-top: 0px;">
  <tbody>
  <tr>
  <td width="100%">
  <table width="100%" align="left" border="0" cellpadding="0" cellspacing="0">
  <tbody>
  <tr>
  <td align="center" bgcolor="#000" style="background: rgb(255, 255, 255) none repeat scroll 0% 0%;padding: 10px;">
  <a target="_blank" href="https://skoozo.com/" >
  <img border="0" alt="" style="display:block; border:none; outline:none; text-decoration:none;width:250px;" src="https://skoozo.com/uploads/logo/logo_5e04766321b5c.png" class="logo">
  </a>
  </td>
  </tr>
  <tr>
    '.$user_msg.'
  
  </tr>
  </tbody>
  </table>
  </td>
  </tr>
  </tbody>
  </table>
  </td>
  </tr>
  </tbody>
  </table>';

        
        $this->email->message($message);
        $this->email->attach($attach);
        $this->email->send();
        //$this->email->print_debugger();
    }
    
    public function get_uniform_size_chart($user_id,$category,$per_page,$offset)  {
        
        $arr=array();
		$products=$this->db->get_where('categories', array('parent_id'=>$category))->result_array();
		foreach($products as $prod){
		 $arr[]= $prod['id'];;			
		}
		
		$this->db->select('*');
	    $this->db->where_in('parent_id',$arr);
	    $this->db->from('categories');
		$this->db->order_by('id', 'desc');
		$query = $this->db->get();
        $count = $query->num_rows();
        
        $data  = array();
        foreach ($query->result_array() as $row) {
          $id = $row['id'];
          $get_publisher = $this->product_model->get_category_by_id($row['parent_id']);
          if($get_publisher->num_rows() > 0){
              $publisher_data=$get_publisher->row();
              $category_name= $publisher_data->name;
          }
          else{ $category_name='-'; }
          

            $uniform_chart = array();
            $count_1 = 0;
            $query_chart = $this->db->query("SELECT usc.id,size.name as size_name,size.id as size_id FROM uniform_size_chart as usc INNER JOIN size ON usc.size_id = size.id WHERE usc.category_id = '$id' and usc.user_id = '$user_id' group by size.id");
            foreach ($query_chart->result_array() as $row_chart) {
                $size_id = $row_chart['size_id'];
                $size_name = $row_chart['size_name'];
                $size_chart=array();
               
                $query_chart2 = $this->db->query("SELECT usc.name,usc.value FROM uniform_size_chart as usc INNER JOIN size ON usc.size_id = size.id WHERE size.id='$size_id' and usc.category_id = '$id' and usc.user_id = '$user_id'");
                foreach ($query_chart2->result_array() as $row_chart2) {  
                   $uniform_id = $row_chart2['name'];
                   $value = $row_chart2['value'];
                   
                   $query_ = $this->db->query("SELECT * FROM size_chart_category WHERE id = '$uniform_id' limit 1");
                   $row_ = $query_->row_array();
                   $name = $row_['name'];
                   
                   $size_chart[] = array(
                        "name" =>  $name,
                        "value" =>  $value,
                    );
                }
               
               $data[] = array(
                    "type" =>  $row['name'],
                    "category" =>  $category_name,
                    "size_id" =>  $size_id,
                    "size_name" =>  $size_name,
                    "uniform_chart" =>  $size_chart
                );
            }
        }
        
        $total_data = $count_1;
        
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data,
            'total_data' => count($data)
        );
        return $resultpost;
    }
    
    public function get_uniform_size_chart_count($user_id,$category)
	{
		$arr=array();
		$products=$this->db->get_where('categories', array('parent_id'=>$category))->result_array();
// 		echo $this->db->last_query();
		foreach($products as $prod){
		 $arr[]= $prod['id'];;			
		}
		
		$this->db->select('*');
	    $this->db->where_in('category_id',$arr);
	    $this->db->from('size');
		$query = $this->db->get();
// 		echo $this->db->last_query();
		return $query->num_rows();
	}
	
	public function size_chart_list($user_id,$category_id) {
        
        $query_chart = $this->db->query("SELECT * FROM size WHERE id = '$category_id' limit 1");
        $row_ = $query_chart->row_array();
        $category_id_ = $row_['category_id'];
		
		$this->db->select('*');
	    $this->db->where('category_id',$category_id);
	    $this->db->from('size_chart_category');
		$this->db->order_by('name', 'asc');
		$query = $this->db->get();
		
        $count = $query->num_rows();
        $data  = array();
        $i = 0;
        foreach ($query->result_array() as $row) {
          
          
            
            $data[] = array(
                "list_id" =>  $i,
                "id" =>  $row['id'],
                "name" =>  $row['name'],
            );
            $i ++;
        }
        
        
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data,
        );
        return $resultpost;
    }
    
    public function add_size_chart($user_id,$size_id,$name,$value,$category_id,$parent_id){ 
        $i=0;
        foreach($name as $names){
            $data_qty['user_id'] = $user_id;
            $data_qty['size_id'] = $size_id;
            $data_qty['category_id'] = $category_id;
            $data_qty['name'] = $names;
            $data_qty['parent_id'] = $parent_id;
            $data_qty['value'] = ($value[$i]==''? '0':$value[$i]);
            $this->db->insert('uniform_size_chart', $data_qty);  
            $i++;
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
        );
        return $resultpost;
    }
      
    public function count_size_chart($size_id)
	{
		
		$this->db->select('*');
	    $this->db->where('size_id',$size_id);
	    $this->db->from('uniform_size_chart');
		$query = $this->db->get();
		return $query->num_rows();
	}  
	
	public function delete_uniform_size_chart($user_id,$size_id)
	{
	    $this->db->where('size_id',$size_id);
	    $this->db->where('user_id',$user_id);
	    $this->db->delete('uniform_size_chart');
	    $resultpost = array(
            'status' => 200,
            'message' => 'success'
        );
        return $resultpost;
	}
	
	public function delete_size_chart($user_id,$size_id)
	{
	    $this->db->where('size_id',$size_id);
	    $this->db->where('user_id',$user_id);
	    return $this->db->delete('uniform_size_chart');
	
	}  


    
    public function check_package_duplications($school_id,$board_id,$grade_id)
	{
		$this->db->where('school_id', "$school_id");
		$this->db->where('board', "$board_id");
		$this->db->where('grade_id', "$grade_id");
		$this->db->where('is_deleted', "0");
		$query = $this->db->get('packages');
		return $query;
	}
	
	  
    public function check_package_duplications_on_update($school_id,$board_id,$grade_id,$package_id)
	{
		$this->db->where('school_id', "$school_id");
		$this->db->where('board', "$board_id");
		$this->db->where('grade_id', "$grade_id");
		$this->db->where('id!=', "$package_id");
		$this->db->where('is_deleted', "0");
		$query = $this->db->get('packages');
		return $query;
	}
	
	public function time_elapsed_string_($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
    
        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;
    
        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }
    
        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ' : 'just now';
    }
    
    
    

    
    
    
    function time_elapsed_string($timestamp)
	{
		$time_ago = strtotime($timestamp);
		$current_time = time();
		$time_difference = $current_time - $time_ago;
		$seconds = $time_difference;
		$minutes = round($seconds / 60);           // value 60 is seconds
		$hours = round($seconds / 3600);           //value 3600 is 60 minutes * 60 sec
		$days = round($seconds / 86400);          //86400 = 24 * 60 * 60;
		$weeks = round($seconds / 604800);          // 7*24*60*60;
		$months = round($seconds / 2629440);     //((365+365+365+365+366)/5/12)*24*60*60
		$years = round($seconds / 31553280);     //(365+365+365+365+366)/5 * 24 * 60 * 60
		if ($seconds <= 60) {
			return get_phrase("just_now");
		} else if ($minutes <= 60) {
			if ($minutes == 1) {
				return "1 " . get_phrase("minute_ago");
			} else {
				return "$minutes " . get_phrase("minutes_ago");
			}
		} else if ($hours <= 24) {
			if ($hours == 1) {
				return "1 " . get_phrase("hour_ago");
			} else {
				return "$hours " . get_phrase("hours_ago");
			}
		} else if ($days <= 30) {
			if ($days == 1) {
				return "1 " . get_phrase("day_ago");
			} else {
				return "$days " . get_phrase("days_ago");
			}
		} else if ($months <= 12) {
			if ($months == 1) {
				return "1 " . get_phrase("month_ago");
			} else {
				return "$months " . get_phrase("months_ago");
			}
		} else {
			if ($years == 1) {
				return "1 " . get_phrase("year_ago");
			} else {
				return "$years " . get_phrase("years_ago");
			}
		}
	}
	
	public function check_staff_email($user_id, $email, $staff_id, $type)
    {
        if ($type == 'on_create') {
            
            $this->db->where('email', $email);
            $where = '(role="vendor" or role = "staff")';
            $this->db->where($where);
            $count = $this->db->get('users')->num_rows();
            
            if ($count > 0) {
                return true;
            } else {
                return false;
            }
        }
        
        if ($type == 'on_update') {
            
            $this->db->where('id!=', $staff_id);
            $this->db->where('email', $email);
            $where = '(role="vendor" or role = "staff")';
            $this->db->where($where);
            $count = $this->db->get('users')->num_rows();
            
            if ($count > 0) {
                return true;
            } else {
                return false;
            }
        }
    }
    
    public function check_staff_mobile($user_id, $phone_number, $staff_id, $type)
    {
        if ($type == 'on_create') {
            
            $this->db->where('id!=', $staff_id);
            $this->db->where('phone_number', $phone_number);
            $where = '(role="vendor" or role = "staff")';
            $this->db->where($where);
            $count = $this->db->get('users')->num_rows();
            
            if ($count > 0) {
                return true;
            } else {
                return false;
            }
        }
        
        if ($type == 'on_update') {
            
            $this->db->where('id!=', $staff_id);
            $this->db->where('phone_number', $phone_number);
            $where = '(role="vendor" or role = "staff")';
            $this->db->where($where);
            $count = $this->db->get('users')->num_rows();
           
            if ($count > 0) {
                return true;
            } else {
                return false;
            }
        }
    }
    
    
    public function get_wow_pincode($pincode) {
        $this->db->where('pincode', $pincode);
        $query = $this->db->get('wow_pincode');
        $sql= $query->row();
        if($query->num_rows()>0){
         $sql= $query->row();
         return $sql->display_rcode;
        }
        else{
         return '';
        }
    }
    
    
    

    
    public function get_listing_name($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('users');
        $sql= $query->row();
        if($query->num_rows()>0){
         $sql= $query->row();
         return $sql->name;
        }
        else{
         return '-';
        }
    }
    
	
}
