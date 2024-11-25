<?php // Plugin Functions

function is_selected_option($selected_state, $state){
    echo ($selected_state == $state) ? 'selected' : '';
}


function add_store($store_name, $address, $pincode, $phone, $email, $city, $state, $open_timing, $close_timing, $google_map_url, $store_image_url, $store_url, $editid){
	global $wpdb;
    $table_name = $wpdb->prefix . 'stores'; 
    $currentDateTime = current_time('Y-m-d H:i:s');
    $data = array(
        'store_name' => $store_name,
        'store_address' => $address,
        'pincode' => $pincode,
        'phone' => $phone,
        'email' => $email,
        'city' => $city,
        'state' => $state,
        'open_timing' => $open_timing,
        'close_timing' => $close_timing,
        'google_map' => $google_map_url,
		'store_url' => $store_url,
    );

    if( $editid && intval($editid) > 0) {
        if($store_image_url && !empty($store_image_url)){
            $data['image'] = $store_image_url;
        }
        $where = array('id' => intval($editid));
        $result = $wpdb->update($table_name, $data, $where);
    } else {
        $data['created'] = $currentDateTime;
        $data['image'] = $store_image_url;
        $result = $wpdb->insert($table_name, $data);
    }

    return $result;
}

function delete_store($editid) {
    global $wpdb;
    $result = false;
    if( is_numeric($editid) && $editid != '' ) {
        $result = $wpdb->delete('wp_stores', array( 'id' => $editid ));
    }
    return $result;
}

add_action('wp_ajax_make_store_search', 'make_store_search_func');
add_action('wp_ajax_nopriv_make_store_search', 'make_store_search_func');
function make_store_search_func(){
    if(isset($_POST['formData'])){
        parse_str($_POST['formData'], $search_array);
		$search_input = $search_array['search_input'];
		$state = $search_array['state'];
		$city = $search_array['city'];

        global $wpdb;
        $table_name = $wpdb->prefix . 'stores';
        $search_term = '%' . $wpdb->esc_like($search_input) . '%';

        $query = $wpdb->prepare("SELECT * FROM $table_name WHERE 1 = 1 ");

        // Conditional clauses
        $clauses = [];

        if (is_numeric($search_input) && intval($search_input) > 0) {
            $clauses[] = $wpdb->prepare("pincode = %s", $search_input);
        }

        if (!empty($search_input)) {
            $clauses[] = $wpdb->prepare("store_name LIKE %s", $search_term);
        }

        if (!empty($state)) {
            $clauses[] = $wpdb->prepare("state LIKE %s", $state);
        }
		
		if (!empty($city)) {
            $clauses[] = $wpdb->prepare("city LIKE %s", $city);
        }

        // Combine clauses into the query
        if (!empty($clauses)) {
            $query .= " AND (" . implode(" AND ", $clauses) . ")";
        }


//         print_r($query );

        $results = $wpdb->get_results($query);
        $result_stores = [];
        if(!empty($results)){
            $result_stores['count'] = count($results);
            foreach($results as $result){
                $store_data = [
                    'id' => $result->id,
                    'store_name' => $result->store_name,
                    'store_address' => $result->store_address,
                    'phone' => $result->phone,
                    'email' => $result->email,
                    'store_image' => $result->image,
                    'google_map' => $result->google_map
                ];
                $result_stores['stores'][] = $store_data;
                
            }
        }else{
            $result_stores['count'] = 0;

        }

        wp_send_json_success($result_stores);
    }else{
        wp_send_json_error("Details missing");
    }
}