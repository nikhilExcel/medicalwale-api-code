<?php 

 
  function call($index_id,$path, $method = 'GET', $data = null)
    {
       $server = "https://search-mw-domain01-sr5e5n4szx6xtfbqbujw7engbe.us-east-1.es.amazonaws.com";

        $url = $server . '/' . $index_id . '/' . $path;
        $headers = array('Accept: application/json', 'Content-Type: application/json', );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        switch($method) {
            case 'GET' :
                break;
            case 'POST' :
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
            case 'PUT' :
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case 'DELETE' :
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      return json_decode($response, true);
    }
    
   
      function query_allsize3($index_id,$query, $size = 3)
    {
        return call ($index_id,'_search?' . http_build_query(array('q' =>"name:".$query, 'size' => $size)));
    }
    
    function elasticsearchsize3($index_id,$keyword){
        
         
        $returndoctor = array();
        $perc=array();
     	$data = array("query"=>array("match"=>array("name"=>"$keyword" )),"suggest"=>array("my-suggestion"=>array("text"=>$keyword, "term"=>array("field"=>"name")))); 
        $data1=json_encode($data);
        $returnresult =  call($index_id , '/_search', 'POST', $data1);
        
    	   if($returnresult['hits']['total'] > 0){
    	       
                	   foreach($returnresult['hits']['hits'] as $hi){
                	 
                	      $sim = similar_text($hi['_source']['name'], $keyword, $perc[]);
                	     $returndoctor[] =$hi['_source'];
                	   }
                	}
                 @$dataperc=max($perc);
        if($dataperc >= 50){
                	return $returndoctor;
                     }else{
     
                    	$name=array();
                    	foreach($returnresult['suggest']['my-suggestion'] as $a){
                            	if(empty($a['options'])){
                            	  $name[]=  $a['text'];
                            	}else{
                            	      $name[]=$a['options'][0]['text'];
                            	}
                        }
                        $string_version = implode(' ', $name);
                        $returnresult = query_allsize3($index_id,$string_version);
                                foreach($returnresult['hits']['hits'] as $hi){
                                	      $returndoctor[] = $hi['_source'];
                                	   }
                                	 
                      return $returndoctor;
            }
    }
    
    $user_id       = $_POST['user_id'];
    $keyword  = $_POST['keyword'];
    
        if ($user_id > 0) {
        
            // Doctor
            $field1 = '';
            $field2 = '';
            $field3 = '';
            $index_id="doctor";
              $doctor=elasticsearchsize3($index_id,$keyword);
                 
            if (!empty($doctor)) {
                $doctor_array[] = array(
                    'title' => 'Doctor',
                    'listing_type' => 5,
                    'array' => $doctor
                );
            } else {
                $doctor_array = array();
            }
            
            

            $resultpost = array_merge( $doctor_array);
        } else {
            $resultpost = array();
        }
         echo json_encode($resultpost);
    
    
    
    
    
    
       
?>
    
    
