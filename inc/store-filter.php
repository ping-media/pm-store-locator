<div class="store_filter_wrap">
    <h1>Find Nearby Store</h1>
    <div class="row store_filter_wrap_row">
        <div class="col-9">
            <div class="store_filter_results">
                <div class="stores">
                    <?php 
                        global $wpdb;
                        $results = $wpdb->get_results( "SELECT * FROM wp_stores"); 
                        if(!empty($results)){
                            foreach($results as $result){ 
                            $image = !empty($result->image) ? $result->image : 'https://placehold.co/600x400';    
                            ?>
                            <div class="store_card">
                                <div class="store_info">
                                    <img src="<?= $image ?>" alt="<?= $result->store_name?>"/>
                                    <h3 class="store_name"><?= $result->store_name?></h3>
                                    <p class="store_address"><i class="fa fa-map-marker-alt "></i> <?= $result->store_address?></p>
                                    <p class="store_phone"><i class="fa fa-phone-alt "></i> <?= $result->phone?></p>
                                    <p class="store_email"><i class="fa fa-envelope "></i> <?= $result->email?></p>
									<p class="opening_time"><i class="fa fa-clock "></i> <?= DateTime::createFromFormat('H:i:s', $result->open_timing)->format('h:i A')  . ' - ' . DateTime::createFromFormat('H:i:s', $result->close_timing)->format('h:i A');?></p>
                                </div>
                                <div class="store_btns">
                                    <a href="<?= $result->google_map?>">Directions</a>
                                    <a href="<?= $result->store_url?>">View Store</a>
                                </div>
                            </div>

                            <?php }
                        }
                    ?>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="store_filter_wrap">
                <form action="" id="search_form">
                    <div class="form-group">
                        <h3>Search By Location</h3>
                        <input type="text" placeholder="Enter Location or Pincode" name="search_input" class="form-control" id="search_input" />
                    </div>
                    <div class="form-group">
                        <h3>Select State</h3>
                        <select id="state" name="state" class="form-control">
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
					
					<div class="form-group">
                        <h3>Select City</h3>
                        <select id="city" name="city" class="form-control">
							<option value="">--Select City--</option>
							<?php if(!empty($results)): 
							$unique_cities = array_unique(array_map(function($result) {
												return $result->city;
											}, $results));
							?>
							<?php foreach($unique_cities as $city): ?>
							<option value="<?= $city ?>"><?= $city ?></option>
							<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</div>
					<button class="search_btn" type="submit">
						Search
					</button>
                </form>
                
            </div>
        </div>
    </div>
</div>
<style>
    .store_filter_wrap h1{
        text-align: center;
        margin-bottom: 30px;
		font-weight: 600;
    }
    .row{
        display: flex;
    }
    .row .col-9{
        flex-basis: 75%;
    }
    .row .col-3{
        flex-basis: 25%;
		width: 25%;
        border-left: 1px solid #828181;
        padding-left: 20px;
    }
    .store_filter_results .stores{
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }
    .store_filter_results .stores > .store_card{
        flex-basis: calc(25% - 15px);
		border: 1px solid #b8b8b8;
		border-radius: 13px;
		background: #fff8e5;
		padding: 10px;
		display: flex;
		flex-direction: column;
		justify-content: space-between;
		box-shadow: 0px 0px 7px 2px #d7d7d7;
    }
    .store_card img{
        width: 80%;
		height: 80px;
		object-fit: cover;
		border-radius: 7px;
		margin: 0 auto;
		display: block;
		margin-bottom: 9px;
    }
    .store_card p{
        font-size: 12px;
        margin-bottom: 5px;
		font-weight: 600;
		line-height: 1.4;
    }
    .store_info{
        margin-bottom: 5px;
    }
    .store_btns{
        display: flex;
        justify-content: center;
        gap: 10px;
		flex-direction: column;
   		text-align: center;
    }
    .store_btns a{
        background: #e41e23;
        border:1px solid #e41e23;
        color: #fff;
        border-radius: 5px;
        padding: 2px 10px;
        text-decoration: none !important;
    }
    .store_btns a:last-child{
        background: transparent;
        color: #e41e23
    }
    .form-control{
        border: 1px solid #747474;
        border-radius: 3px;
        outline: none;
        padding: 8px;
        width: 100%;
        font-weight: 400 !important;
    }
	.form-control:focus{
		border: 1px solid #747474;
	}
    .form-group{
        margin-bottom: 20px;
    }
    .form-group h3{
        margin-bottom: 8px;
		margin-top: 0;
    }
	.store_name{
		margin-top: 0;
		padding-top: 0;
		text-align: center;
		font-size: 16px;
		font-weight: 600;
		margin-bottom: 5px;
	}
	.search_btn{
		border-color: #431844 !important;
		padding: 5px 10px;
		border-radius: 5px;
		background: #431844 !important;
		color: #fff !important;
		width: 100%;
		font-weight: 600;
	}

    @media(max-width: 992px){
		.store_filter_wrap{
			padding: 0 15px;
		}
        .row {
            flex-wrap: wrap;
        }
        .row > .col-9, .row > .col-3  {
            flex-basis: 100%;
			width: 100%
        }
        .store_filter_results .stores > .store_card {
            flex-basis: calc(100%);
            width: 100%;
        }
        .store_filter_wrap  .store_filter_wrap_row{
            flex-direction: column-reverse;
        }
        .store_filter_wrap_row .col-3 {
            border-left: none;
			padding-left: 0;
			border-bottom: 1px solid #d3d3d3;
			margin-bottom: 30px;
			padding-bottom: 20px;
        }
		.form-group h3{
			font-size: 16px
		}
		.store_filter_results .stores{
			gap: 30px;
		}
		.store_card img{
			height: 200px;
			width: 100%;
		}
    }
</style>
<script>
    jQuery(document).ready(function($){
        $('#search_form').submit(function(e){
            e.preventDefault()
            make_search();

        })

        $('#state, #city').change(function(){
            make_search();
        })


        function make_search(){
            var formData = $('#search_form').serialize()
            $.ajax({
                type: "post",
                url: PM_ajax.ajax_url,
                data: {
                    action: "make_store_search",
                    formData: formData
                },
                success: function(res){
                    $('.store_filter_results .stores').empty();
                    if(res && res.success){
                        let result_stores = res.data.stores || [];
                        let result_count = res.data.count || 0;
                        if (result_count > 0) {
                            // Generate HTML for the stores
                            let html = result_stores.map((item) => {
                                return `
                                <div class="store_card">
                                    <div class="store_info">
                                        <img src="${item.store_image ? item.store_image : 'default_image.png'}" alt="${(item.store_name)}" />
                                        <h3 class="store_name">${(item.store_name)}</h3>
                                        <p class="store_address"><i class="fa fa-map-marker-alt"></i> ${(item.store_address)}</p>
                                        <p class="store_phone"><i class="fa fa-phone-alt"></i> ${(item.phone)}</p>
                                        <p class="store_email"><i class="fa fa-envelope"></i> ${(item.email)}</p>
                                    </div>
                                    <div class="store_btns">
                                        <a href="${(item.google_map)}" target="_blank">Directions</a>
                                        <a href="#">View Store</a>
                                    </div>
                                </div>`;
                            }).join('');

                            $(".store_filter_results .stores").append(html);
                        } else {
                            // Handle no results found
                            $(".store_filter_results .stores").append("<p>No Results Found</p>");
                        }
                    }else{
                        $('.store_filter_result').append("<h2>No Results Found</h2>");
                    }
                },
                error: function(res){
                    console.log(res)
                }
            })
        }
    })
</script>