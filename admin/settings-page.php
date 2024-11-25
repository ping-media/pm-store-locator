<?php
//Exit if file called directly
if (! defined( 'ABSPATH' )) {
	exit;
}

// display the plugin settings page
function course_certificate_admin_certificate_ui() {

	if ( ! current_user_can( 'manage_options' ) ) return;
	$error = "";
	if( isset($_POST['add_store']) ) {
		if(!wp_verify_nonce($_POST['course_nonce'], 'admin_certificate_ui')) {
			echo '<div class="alert alert-danger">Try Again Verification Failed!!</div>';
		} else if( isset($_POST['add_store']) && $_POST['add_store'] == "Delete" ) {
			$editid = sanitize_text_field($_POST['editid']);
			if (strpos($editid, ',') !== false) {
				$editid = explode(",", $editid);
				foreach( $editid as $edt ) {
					$result = delete_store( $edt );
				}
			} else {
				$result = delete_store($editid);
			}
			if( $result == 1 ) {
                $error = '<div class="alert alert-success hide-alert">Store deleted successfully!<button type="button" class="close" data-dismiss="alert">x</button></div>';
            } else {
                $error = '<div class="alert alert-danger hide-alert">Error while deleting!<button type="button" class="close" data-dismiss="alert">x</button></div>';
            }
		} else if( 
			empty($_POST['store_name']) || 
			empty($_POST['address']) || 
			empty($_POST['phone']) || 
			empty($_POST['email']) || 
			empty($_POST['pincode']) ||
			empty($_POST['city']) ||
			empty($_POST['state']) ||
			empty($_POST['open_timing']) ||
			empty($_POST['close_timing']) ||
			empty($_POST['google_map_url']) ||
			empty($_POST['store_url'])
			) {
			$error = '<div class="alert alert-danger hide-alert">All fields are required!<button type="button" class="close" data-dismiss="alert">x</button></div>';
		} else {
			$store_name = sanitize_text_field($_POST['store_name']) ?? '';
			$address = sanitize_text_field($_POST['address']) ?? '';
			$pincode = sanitize_text_field($_POST['pincode']) ?? '';
			$phone = sanitize_text_field($_POST['phone']) ?? '';
			$email = sanitize_email($_POST['email']) ?? '';
			$city = sanitize_text_field($_POST['city']) ?? '';
			$state = sanitize_text_field($_POST['state']) ?? '';
			$open_timing = sanitize_text_field($_POST['open_timing']) ?? '';
			$close_timing = sanitize_text_field($_POST['close_timing']) ?? '';
			$google_map_url = esc_url_raw($_POST['google_map_url']) ?? '';
			$store_url = esc_url_raw($_POST['store_url']) ?? '';
			$editid = isset($_POST['editid']) ? intval($_POST['editid']) : 0;
			
			$store_image_url = '';
			if (isset($_FILES['store_image']) && !empty($_FILES['store_image']['name'])) {
				$file = $_FILES['store_image'];
				// Verify file type
				$allowed_file_types = ['image/jpeg', 'image/png', 'image/jpg'];
				if (in_array($file['type'], $allowed_file_types)) {
					$upload_dir = wp_upload_dir();
					$store_image_dir = $upload_dir['basedir'] . '/store-images/';

					if (!file_exists($store_image_dir)) {
						if (!wp_mkdir_p($store_image_dir)) {
							error_log("Failed to create directory: " . $store_image_dir);
						}
					}

					if (is_uploaded_file($file['tmp_name'])) {
						$safe_filename = sanitize_file_name($file['name']);
						
						if (move_uploaded_file($file['tmp_name'], $store_image_dir . $safe_filename)) {
							$store_image_url = $upload_dir['baseurl'] . '/store-images/'  . $safe_filename;
						} else {
							echo "Failed to move file.<br>";
						}
					}
				}
			}

			
			$result = add_store($store_name, $address, $pincode, $phone, $email, $city, $state, $open_timing, $close_timing, $google_map_url, $store_image_url, $store_url, $editid);

			// print_r($_FILES['store_image']);
			// echo $store_name;
			// wp_die();
			
			if( $result == 1 ) {
				if( $editid != "" ) {
	                $error = '<div class="alert alert-success hide-alert">Store updated successfully!<button type="button" class="close" data-dismiss="alert">x</button></div>';
				} else {
	                $error = '<div class="alert alert-success hide-alert">Store added successfully!<button type="button" class="close" data-dismiss="alert">x</button></div>';
				}
            } else {
                $error = '<div class="alert alert-danger hide-alert">Submission failed!<button type="button" class="close" data-dismiss="alert">x</button></div>';
            }
		}
	}
	global $wpdb;
    $stores = $wpdb->get_results( "SELECT * FROM wp_stores");
    $cpage = isset($_GET['pg']) ? $_GET['pg'] : 1;
    $cpage = $cpage != '' ? (($cpage-1)*10) : 0;
	$storesNew = array_slice($stores, $cpage, 10, true);
    ?>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<style>
		@font-face {
		    font-family: Varela Round;
		    src: url("../wp-content/plugins/pm-store-locator/assets/css/fonts/VarelaRound-Regular.otf") format("opentype");
		}
	    body {
	        color: #566787;
			background: #f5f5f5;
			font-family: 'Varela Round', sans-serif;
			font-size: 13px;
		}
		.table-wrapper {
	        background: #fff;
	        padding: 20px 25px;
	        margin: 30px 0;
			border-radius: 3px;
	        box-shadow: 0 1px 1px rgba(0,0,0,.05);
	    }
		.table-title {        
			padding-bottom: 15px;
			background: #435d7d;
			color: #fff;
			padding: 16px 30px;
			margin: -20px -25px 10px;
			border-radius: 3px 3px 0 0;
	    }
	    .table-title h2 {
			margin: 5px 0 0;
			font-size: 24px;
		}
		.table-title .btn-group {
			float: right;
		}
		.table-title .btn {
			color: #fff;
			float: right;
			font-size: 13px;
			border: none;
			min-width: 50px;
			border-radius: 2px;
			border: none;
			outline: none !important;
			margin-left: 10px;
		}
		.table-title .btn i {
			float: left;
			font-size: 21px;
			margin-right: 5px;
		}
		.table-title .btn span {
			float: left;
			margin-top: 2px;
		}
	    table.table tr th, table.table tr td {
	        border-color: #e9e9e9;
			padding: 12px 15px;
			vertical-align: middle;
	    }
		table.table tr th:first-child {
			width: 60px;
		}
		table.table tr th:last-child {
			width: 100px;
		}
	    table.table-striped tbody tr:nth-of-type(odd) {
	    	background-color: #fcfcfc;
		}
		table.table-striped.table-hover tbody tr:hover {
			background: #f5f5f5;
		}
	    table.table th i {
	        font-size: 13px;
	        margin: 0 5px;
	        cursor: pointer;
	    }	
	    table.table td:last-child i {
			opacity: 0.9;
			font-size: 22px;
	        margin: 0 5px;
	    }
		table.table td a {
			font-weight: bold;
			color: #566787;
			display: inline-block;
			text-decoration: none;
			outline: none !important;
		}
		table.table td a:hover {
			color: #2196F3;
		}
		table.table td a.edit {
	        color: #FFC107;
	    }
	    table.table td a.delete {
	        color: #F44336;
	    }
	    table.table td i {
	        font-size: 19px;
	    }
		table.table .avatar {
			border-radius: 50%;
			vertical-align: middle;
			margin-right: 10px;
		}
	    .pagination {
	        float: right;
	        margin: 0 0 5px;
	    }
	    .pagination li a {
	        border: none;
	        font-size: 13px;
	        min-width: 30px;
	        min-height: 30px;
	        color: #999;
	        margin: 0 2px;
	        line-height: 30px;
	        border-radius: 2px !important;
	        text-align: center;
	        padding: 0 6px;
	    }
	    .pagination li a:hover {
	        color: #666;
	    }	
	    .pagination li.active a, .pagination li.active a.page-link {
	        background: #03A9F4;
	    }
	    .pagination li.active a:hover {        
	        background: #0397d6;
	    }
		.pagination li.disabled i {
	        color: #ccc;
	    }
	    .pagination li i {
	        font-size: 16px;
	        padding-top: 6px
	    }
	    .hint-text {
	        float: left;
	        margin-top: 10px;
	        font-size: 13px;
	    }    
		/* Custom checkbox */
		.custom-checkbox {
			position: relative;
		}
		.custom-checkbox input[type="checkbox"] {    
			opacity: 0;
			position: absolute;
			margin: 5px 0 0 3px;
			z-index: 9;
		}
		.custom-checkbox label:before{
			width: 18px;
			height: 18px;
		}
		.custom-checkbox label:before {
			content: '';
			margin-right: 10px;
			display: inline-block;
			vertical-align: text-top;
			background: white;
			border: 1px solid #bbb;
			border-radius: 2px;
			box-sizing: border-box;
			z-index: 2;
		}
		.custom-checkbox input[type="checkbox"]:checked + label:after {
			content: '';
			position: absolute;
			left: 6px;
			top: 0px;
			width: 6px;
			height: 11px;
			border: solid #000;
			border-width: 0 3px 3px 0;
			transform: inherit;
			z-index: 3;
			transform: rotateZ(45deg);
		}
		.custom-checkbox input[type="checkbox"]:checked + label:before {
			border-color: #03A9F4;
			background: #03A9F4;
		}
		.custom-checkbox input[type="checkbox"]:checked + label:after {
			border-color: #fff;
		}
		.custom-checkbox input[type="checkbox"]:disabled + label:before {
			color: #b8b8b8;
			cursor: auto;
			box-shadow: none;
			background: #ddd;
		}
		/* Modal styles */
		.modal .modal-dialog {
			max-width: 600px;
		}
		.modal .modal-header, .modal .modal-body, .modal .modal-footer {
			padding: 20px 30px !important;
		}
		.modal .modal-content {
			border-radius: 3px;
		}
		.modal .modal-footer {
			background: #ecf0f1;
			border-radius: 0 0 3px 3px;
		}
	    .modal .modal-title {
	        display: inline-block;
	    }
		.modal .form-control {
			border-radius: 2px;
			box-shadow: none;
			border-color: #dddddd;
		}
		.modal textarea.form-control {
			resize: vertical;
		}
		.modal .btn {
			border-radius: 2px;
			min-width: 100px;
		}	
		.modal form label {
			font-weight: normal;
		}
		.store_name_url p{
			margin-bottom: 0
		}
	</style>

	<div class="container-fluid">
	  <div class="table-wrapper">
	    <div class="table-title">
	      <div class="row">
	        <div class="col-sm-6">
	          <h2 style="color: white;">Manage <b>Stores</b></h2>
	        </div>
	        <div class="col-sm-6">
	          <a href="#addEmployeeModal" class="btn btn-success" data-toggle="modal"><i class="material-icons">&#xE147;</i> <span>Add New Store</span></a>
	          <a href="javascript:void(0);" class="btn btn-danger deleteMultiple" data-toggle="modal"><i class="material-icons">&#xE15C;</i> <span>Delete</span></a>
	        </div>
	      </div>
	    </div>
	    <?php echo $error;?>
		<div class="alert alert-info" role="alert">
		  	<strong>[store_filter]</strong> Copy and paste the shortcode on the page you want the search bar and result of the stores to be.
		</div>
	    <table class="table table-striped table-hover">
	      <thead>
	        <tr>
	          <th>
	            <span class="custom-checkbox">
					<input type="checkbox" id="selectAll">
					<label for="selectAll"></label>
				</span>
	          </th>
	          <th>Store Name</th>
	          <th>Address</th>
	          <th>City/State</th>
	          <th>Pincode</th>
	          <th>Phone</th>
	          <th>Email</th>
	          <th>Timings</th>
	          <th>Map</th>
	          <th>Actions</th>
	        </tr>
	      </thead>
	      <tbody>
	        <?php foreach ($storesNew as $value) { ?>
	    	 	<tr>
	    	 		<td>
			            <span class="custom-checkbox">
							<input type="checkbox" id="checkbox<?php echo $value->id;?>" value="<?php echo $value->id;?>" class="checkedcert">
							<label for="checkbox<?php echo $value->id;?>"></label>
						</span>
			        </td>
	                <td class="store_name_url">
						<p><?php echo $value->store_name; ?></p>
						<a href="<?= $value->store_url; ?>">View</a>
					</td>
	                <td class="address"><?php echo $value->store_address; ?></td>
	                <td class="city-state">
						<span class="city"><?php echo $value->city; ?></span>, 
						<span class="state"><?php echo $value->state; ?></span>
					</td>
	                <td class="pincode"><?php echo $value->pincode; ?></td>
	                <td class="phone"><?php echo $value->phone; ?></td>
	                <td class="email"><?php echo $value->email; ?></td>
	                <td class="timing">
						<span class="open_timing" data-val="<?= $value->open_timing ?>"><?php echo DateTime::createFromFormat('H:i:s', $value->open_timing)->format('h:i A'); ?></span> -
						<span class="close_timing" data-val="<?= $value->close_timing ?>"><?php echo DateTime::createFromFormat('H:i:s', $value->close_timing)->format('h:i A'); ?></span>
					</td>
					<td class="google_map"><img src="<?= $value->image; ?>" width="20"/><a href="<?= $value->google_map; ?>">Link</a></td>
			        <td>
			           <a href="javascript:void();" class="edit editModal" data-id="<?php echo $value->id;?>"><i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i></a>
			           <a href="javascript:void(0);" class="delete deleteModal" data-id="<?php echo $value->id;?>"><i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i></a>
			        </td>
	            </tr>
	        <?php } ?>
	      </tbody>
	    </table>
	    <div class="clearfix">
	    	<?php if( count($stores) > 0 ) { ?>
		      <div class="hint-text">Showing <b><?php echo count($stores);?></b> out of <b><?php echo count($stores);?></b> entries</div>
		      <ul class="pagination">
		        <!--<li class="page-item disabled"><a href="#">Previous</a></li>-->
		        <?php
		        $pages = ceil(count($stores)/10);
		        $currentpage = isset($_GET['pg']) ? $_GET['pg'] : 1;
		        $currentpage = $currentpage != '' ? $currentpage : 1;
		        for($i=1;$i<=$pages;$i++) { ?>
			        <li class="page-item <?php echo ($currentpage==$i) ? 'active' : '';?>"><a href="<?php echo admin_url().'admin.php?page=certificate-codes&pg='.$i;?>" class="page-link"><?php echo $i;?></a></li>
		        <?php } ?>
		        <!--<li class="page-item"><a href="#" class="page-link">Next</a></li>-->
		      </ul>
	    	<?php } ?>
	    </div>
	  </div>
	</div>
	<!-- Edit Modal HTML -->
	<div id="addEmployeeModal" class="modal fade">
	  <div class="modal-dialog">
	    <div class="modal-content">
		<form class="mt-40" method="POST" enctype="multipart/form-data">
			<?php wp_nonce_field( 'admin_certificate_ui', 'course_nonce' );?>
	        <div class="modal-header">
	          <h4 class="modal-title">Add Store</h4>
	          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        </div>
	        <div class="modal-body">
			<div class="form-group">
	            <label>Store Name</label>
	            <input type="text" class="form-control" required name="store_name">
	          </div>
	          <div class="form-group">
				<label>Address</label>
				<input type="text" required class="form-control" name="address">
	          </div>
	          
			  <div class="row">
				<div class="col-4">
					<div class="form-group">
						<label>Pincode</label>
						<input type="text" required class="form-control" value="" name="pincode">
					</div>
				</div>
				<div class="col-4">
					<div class="form-group">
						<label>City</label>
						<input type="text" required class="form-control" value="" name="city">
					</div>
				</div>
				<div class="col-4">
					<div class="form-group">
						<label>State</label>
						<select required class="form-control" name="state">
							<option value="">--Select State--</option>
                            <option value="Andhra Pradesh">Andhra Pradesh</option>
                            <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                            <option value="Assam">Assam</option>
                            <option value="Bihar">Bihar</option>
                            <option value="Chhattisgarh">Chhattisgarh</option>
                            <option value="Goa">Goa</option>
                            <option value="Gujarat">Gujarat</option>
                            <option value="Haryana">Haryana</option>
                            <option value="Himachal Pradesh">Himachal Pradesh</option>
                            <option value="Jharkhand">Jharkhand</option>
                            <option value="Karnataka">Karnataka</option>
                            <option value="Kerala">Kerala</option>
                            <option value="Madhya Pradesh">Madhya Pradesh</option>
                            <option value="Maharashtra">Maharashtra</option>
                            <option value="Manipur">Manipur</option>
                            <option value="Meghalaya">Meghalaya</option>
                            <option value="Mizoram">Mizoram</option>
                            <option value="Nagaland">Nagaland</option>
                            <option value="Odisha">Odisha</option>
                            <option value="Punjab">Punjab</option>
                            <option value="Rajasthan">Rajasthan</option>
                            <option value="Sikkim">Sikkim</option>
                            <option value="Tamil Nadu">Tamil Nadu</option>
                            <option value="Telangana">Telangana</option>
                            <option value="Tripura">Tripura</option>
                            <option value="Uttar Pradesh">Uttar Pradesh</option>
                            <option value="Uttarakhand">Uttarakhand</option>
                            <option value="West Bengal">West Bengal</option>
                            <option value="Andaman and Nicobar Islands">Andaman and Nicobar Islands</option>
                            <option value="Chandigarh">Chandigarh</option>
                            <option value="Dadra and Nagar Haveli and Daman and Diu">Dadra and Nagar Haveli and Daman and Diu</option>
                            <option value="Delhi">Delhi</option>
                            <option value="Jammu and Kashmir">Jammu and Kashmir</option>
                            <option value="Ladakh">Ladakh</option>
                            <option value="Lakshadweep">Lakshadweep</option>
                            <option value="Puducherry">Puducherry</option>
						</select>
					</div>
				</div>
			  </div>
	          
			  <div class="row">
				<div class="col-6">
					<div class="form-group">
						<label>Phone</label>
						<input type="tel" required class="form-control" value="" name="phone">
					</div>
				</div>
				<div class="col-6">
					<div class="form-group">
						<label>Email</label>
						<input type="email" required class="form-control" value="" name="email">
					</div>
				</div>
			  </div>

			  <div class="row">
				<div class="col-6">
					<div class="form-group">
						<label>Store Opening Timing</label>
						<input type="time" required class="form-control" value="" name="open_timing">
					</div>
				</div>
				<div class="col-6">
					<div class="form-group">
						<label>Store Closing Timing</label>
						<input type="time" required class="form-control" value="" name="close_timing">
					</div>
				</div>
			  </div>
	          
	          
	          <div class="form-group">
				<label>Google Map URL</label>
				<input type="url" required class="form-control" value="" name="google_map_url">
	          </div>
				
			  <div class="form-group">
				<label>Store URL</label>
				<input type="url" required class="form-control" value="" name="store_url">
	          </div>

	          <div class="form-group">
				<label>Store Image</label>
				<input type="file" required class="form-control" name="store_image" accept="image/jpeg, image/png, image/jpg">
	          </div>

	        </div>
	        <div class="modal-footer">
	          <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
	          <input type="submit" class="btn btn-success" value="Add" name="add_store">
	        </div>
	      </form>
	    </div>
	  </div>
	</div>
	<!-- Edit Modal HTML -->
	<div id="editEmployeeModal" class="modal fade">
	  <div class="modal-dialog">
	    <div class="modal-content">
		<form class="mt-40" method="POST" enctype="multipart/form-data">
			<?php wp_nonce_field( 'admin_certificate_ui', 'course_nonce' );?>
	        <div class="modal-header">
	          <h4 class="modal-title">Edit Store</h4>
	          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        </div>
	        <div class="modal-body">
	          <div class="form-group">
	            <label>Store Name</label>
	            <input type="text" class="form-control" required name="store_name">
	          </div>
	          <div class="form-group">
				<label>Address</label>
				<input type="text" required class="form-control" name="address">
	          </div>
	          
			  <div class="row">
				<div class="col-4">
					<div class="form-group">
						<label>Pincode</label>
						<input type="text" required class="form-control" value="" name="pincode">
					</div>
				</div>
				<div class="col-4">
					<div class="form-group">
						<label>City</label>
						<input type="text" required class="form-control" value="" name="city">
					</div>
				</div>
				<div class="col-4">
					<div class="form-group">
						<label>State</label>
						<select required class="form-control" name="state">
							<option value="">--Select State--</option>
                            <option value="Andhra Pradesh">Andhra Pradesh</option>
                            <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                            <option value="Assam">Assam</option>
                            <option value="Bihar">Bihar</option>
                            <option value="Chhattisgarh">Chhattisgarh</option>
                            <option value="Goa">Goa</option>
                            <option value="Gujarat">Gujarat</option>
                            <option value="Haryana">Haryana</option>
                            <option value="Himachal Pradesh">Himachal Pradesh</option>
                            <option value="Jharkhand">Jharkhand</option>
                            <option value="Karnataka">Karnataka</option>
                            <option value="Kerala">Kerala</option>
                            <option value="Madhya Pradesh">Madhya Pradesh</option>
                            <option value="Maharashtra">Maharashtra</option>
                            <option value="Manipur">Manipur</option>
                            <option value="Meghalaya">Meghalaya</option>
                            <option value="Mizoram">Mizoram</option>
                            <option value="Nagaland">Nagaland</option>
                            <option value="Odisha">Odisha</option>
                            <option value="Punjab">Punjab</option>
                            <option value="Rajasthan">Rajasthan</option>
                            <option value="Sikkim">Sikkim</option>
                            <option value="Tamil Nadu">Tamil Nadu</option>
                            <option value="Telangana">Telangana</option>
                            <option value="Tripura">Tripura</option>
                            <option value="Uttar Pradesh">Uttar Pradesh</option>
                            <option value="Uttarakhand">Uttarakhand</option>
                            <option value="West Bengal">West Bengal</option>
                            <option value="Andaman and Nicobar Islands">Andaman and Nicobar Islands</option>
                            <option value="Chandigarh">Chandigarh</option>
                            <option value="Dadra and Nagar Haveli and Daman and Diu">Dadra and Nagar Haveli and Daman and Diu</option>
                            <option value="Delhi">Delhi</option>
                            <option value="Jammu and Kashmir">Jammu and Kashmir</option>
                            <option value="Ladakh">Ladakh</option>
                            <option value="Lakshadweep">Lakshadweep</option>
                            <option value="Puducherry">Puducherry</option>
						</select>
					</div>
				</div>
			  </div>
	          
			  <div class="row">
				<div class="col-6">
					<div class="form-group">
						<label>Phone</label>
						<input type="tel" required class="form-control" value="" name="phone">
					</div>
				</div>
				<div class="col-6">
					<div class="form-group">
						<label>Email</label>
						<input type="email" required class="form-control" value="" name="email">
					</div>
				</div>
			  </div>

			  <div class="row">
				<div class="col-6">
					<div class="form-group">
						<label>Store Opening Timing</label>
						<input type="time" required class="form-control" value="" name="open_timing">
					</div>
				</div>
				<div class="col-6">
					<div class="form-group">
						<label>Store Closing Timing</label>
						<input type="time" required class="form-control" value="" name="close_timing">
					</div>
				</div>
			  </div>
	          
	          <div class="form-group">
				<label>Google Map URL</label>
				<input type="url" required class="form-control" value="" name="google_map_url">
	          </div>
				
			   <div class="form-group">
				<label>Store URL</label>
				<input type="url" required class="form-control" value="" name="store_url">
	          </div>

			  <div class="form-group">
				<img src="" class="store_image" width="70"/>
			  </div>

			  <div class="form-group">
				<label>Store Image</label>
				<input type="file" class="form-control" name="store_image" accept="image/jpeg, image/png, image/jpg">
	          </div>
			
	        </div>
	        <div class="modal-footer">
				<input type="hidden" name="editid" value="">
	        	<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
	        	<input type="submit" class="btn btn-success" value="Update" name="add_store">
	        </div>
	      </form>
	    </div>
	  </div>
	</div>
	<!-- Delete Modal HTML -->
	<div id="deleteEmployeeModal" class="modal fade">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <form method="POST">
			<?php wp_nonce_field( 'admin_certificate_ui', 'course_nonce' );?>
	        <div class="modal-header">
	          <h4 class="modal-title">Delete Store</h4>
	          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        </div>
	        <div class="modal-body">
	          <p>Are you sure you want to delete these Records?</p>
	          <p class="text-warning"><small>This action cannot be undone.</small></p>
	        </div>
	        <div class="modal-footer">
	          <input type="hidden" name="editid" value="">
	          <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
	          <input type="submit" class="btn btn-danger" value="Delete" name="add_store">
	        </div>
	      </form>
	    </div>
	  </div>
	</div>

	<script type="text/javascript">
		jQuery(document).ready(function() {
        	jQuery('#certificates-table').DataTable();
        	jQuery( function() {
			    jQuery( "#dob" ).datepicker({ dateFormat: 'dd/M/yy', changeMonth: true, changeYear: true, yearRange: '1940:' + new Date().getFullYear(), altField: "#adob", altFormat: "mm/dd/yy" });
			    jQuery( "#award_date" ).datepicker({ dateFormat: 'dd/M/yy', changeMonth: true, changeYear: true, yearRange: '1940:' + new Date().getFullYear(), altField: "#aaward_date", altFormat: "mm/dd/yy" });
			    // jQuery( "#editdob" ).datepicker({ dateFormat: 'dd/M/yy', changeMonth: true, changeYear: true, yearRange: '1940:' + new Date().getFullYear(), altField: "#eeditdob", altFormat: "mm/dd/yy" });
			    jQuery( "#editaward_date" ).datepicker({ dateFormat: 'dd/M/yy', changeMonth: true, changeYear: true, yearRange: '1940:' + new Date().getFullYear(), altField: "#eeditaward_date", altFormat: "mm/dd/yy" });

				jQuery('.certificate_code').blur(function(e){
					let certificate_code = jQuery(this).val()
					
					if(certificate_code !== ''){
						console.log(certificate_code)
						jQuery('.qr-code-wrap img').attr('src', 'https://qrcode.tec-it.com/API/QRCode?data=https%3A%2F%2Flearning.pingmedia.in%2Fcertificate%3Fid%3D' + certificate_code)
					}
				})
				
			} );

			

			jQuery(document).on("click", ".editModal", function() {
				var id = jQuery(this).data("id");
				var store_name = jQuery(".store_name_url p", jQuery(this).closest("tr")).text();
				var store_url = jQuery(".store_name_url a", jQuery(this).closest("tr")).attr('href');
				var address = jQuery(".address", jQuery(this).closest("tr")).html();
				var pincode = jQuery(".pincode", jQuery(this).closest("tr")).html();
				var state = jQuery(".city-state .state", jQuery(this).closest("tr")).text();
				var city = jQuery(".city-state .city", jQuery(this).closest("tr")).html();
				var phone = jQuery(".phone", jQuery(this).closest("tr")).html();
				var email = jQuery(".email", jQuery(this).closest("tr")).html();
				var google_map = jQuery(".google_map a", jQuery(this).closest("tr")).attr('href');
				var store_image = jQuery(".google_map img", jQuery(this).closest("tr")).attr('src');
				var open_timing = jQuery(".timing .open_timing", jQuery(this).closest("tr")).data('val');
				var close_timing = jQuery(".timing .close_timing", jQuery(this).closest("tr")).data('val');
				jQuery("#editEmployeeModal input[name=editid]").val( id );
				jQuery("#editEmployeeModal input[name=store_name]").val(store_name);
				jQuery("#editEmployeeModal input[name=address]").val(address);
				jQuery("#editEmployeeModal input[name=pincode]").val(pincode);
				jQuery("#editEmployeeModal select[name=state]").val(state);
				jQuery("#editEmployeeModal input[name=city]").val(city);
				jQuery("#editEmployeeModal input[name=phone]").val(phone);
				jQuery("#editEmployeeModal input[name=email]").val(email);
				jQuery("#editEmployeeModal input[name=open_timing]").val(open_timing);
				jQuery("#editEmployeeModal input[name=close_timing]").val(close_timing);
				jQuery("#editEmployeeModal input[name=google_map_url]").val(google_map);
				jQuery("#editEmployeeModal input[name=store_url]").val(store_url);
				jQuery("#editEmployeeModal .store_image").attr('src', store_image);
				jQuery("#editEmployeeModal").modal();
			});

			jQuery(document).on("click", ".deleteModal", function() {
				var id = jQuery(this).data("id");
				jQuery("#deleteEmployeeModal input[name=editid]").val( id );
				jQuery("#deleteEmployeeModal").modal();
			});
			jQuery(document).on("click", ".deleteMultiple", function() {
				var allIds = [];
				jQuery('.checkedcert:checkbox:checked').each(function () {
				    allIds.push(this.checked ? jQuery(this).val() : "");
				});
				jQuery("#deleteEmployeeModal input[name=editid]").val( allIds.join(",") );
				jQuery("#deleteEmployeeModal").modal();
			});

			// Select/Deselect checkboxes
			var checkbox = jQuery('table tbody input[type="checkbox"]');
			jQuery("#selectAll").click(function() {
				if(this.checked) {
					checkbox.each(function() {
						this.checked = true;
					});
				} else {
					checkbox.each(function() {
						this.checked = false;
					});
				}
			});
			checkbox.click(function() {
				if(!this.checked){
					jQuery("#selectAll").prop("checked", false);
				}
			});

        } );
	</script>
<?php }

