@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<section class="content-main">
    <div class="content-header">
        <h2 class="content-title">Edit Product</h2>
        <div class="">
            <a href="{{ route('product.all') }}" class="btn btn-primary"><i class="material-icons md-plus"></i> Product List</a>
        </div>
    </div>
	<div class="row">
        <div class="col-md-10 mx-auto">
			<form method="post" action="{{ route('product.update',$product->id) }}" enctype="multipart/form-data">
				@csrf
				<div class="card">
					<div class="card-header">
						<h3>Basic Info</h3>
					</div>
		        	<div class="card-body">
		        		<div class="row">
		                	<div class="col-md-6 mb-4">
		                        <label for="product_name_en" class="col-form-label" style="font-weight: bold;">Product Name (En):<span class="text-danger"> *</span></label>
		                        <input class="form-control" id="product_name_en" type="text" name="name_en" placeholder="Write product name english" value="{{ $product->name_en }}">
		                        @error('product_name_en')
		                            <p class="text-danger">{{$message}}</p>
		                        @enderror
		                    </div>
		                    <div class="col-md-6 mb-4">
	                           	<label for="product_name_bn" class="col-form-label" style="font-weight: bold;">Product Name (Bn):</label>
	                           	<input class="form-control" id="product_name_bn" type="text" name="name_bn" placeholder="Write product name bangla" value="{{ $product->name_bn }}">
		                    </div>
		        		</div>

		        		<div class="row">
		        			<div class="col-md-6 mb-4 d-none">
	                          <label for="product_code" class="col-form-label" style="font-weight: bold;">Product Code:</label>
	                            <input class="form-control" id="product_code" type="text" name="product_code" placeholder="Write product code" value="{{ $product->product_code }}">
	                        </div>

							<div class="col-md-6 mb-4">
								<label for="product_category" class="col-form-label" style="font-weight: bold;">Category:<span class="text-danger"> *</span></label>
								<a style="background-color: #3BB77E; "class="btn btn-sm float-end" data-bs-toggle="modal" data-bs-target="#category"><i class="fa-solid fa-plus text-white"></i></a>
								@php
									$selectedCategory = $product->category_id;
								@endphp
								<div class="custom_select">
									<select class="form-control select-active w-100 form-select select-nice" name="category_id" id="product_category">
										@foreach ($categories as $category)
											<option value="{{ $category->id }}" @if($category->id==$selectedCategory) selected  @endif>{{ $category->name_en }}</option>
											@foreach ($category->childrenCategories as $childCategory)
												@include('backend.include.child_category', ['child_category' => $childCategory])
											@endforeach
										@endforeach
									</select>
								</div>
							</div>

							<div class="col-md-6 mb-4">
		        				<a style="background-color: #3BB77E; " type="button" class="btn btn-sm float-end" id="closeModal1" data-bs-toggle="modal" data-bs-target="#brand"><i class="fa-solid fa-plus text-white"></i></a>
	                           <label for="brand_id" class="col-form-label" style="font-weight: bold;">Brand:</label>
				                <div class="custom_select">
                                    <select class="form-control select-active w-100 form-select select-nice" name="brand_id" id="brand_id">
                                    	<option value="">--Select Brand--</option>
		                                @foreach ($brands as $brand)
		                                    <option value="{{ $brand->id }}" {{ $brand->id == $product->brand_id ? 'selected' : '' }}>{{ $brand->name_en }}</option>
		                                @endforeach
                                    </select>
									@error('brand_id')
										<p class="text-danger">{{$message}}</p>
									@enderror
                                </div>
	                        </div>

		        			@if(get_setting('multi_vendor')->value)
		        			    @if(Auth::guard('admin')->user()->role == '2')
		        			        <input type="hidden" name="vendor_id" id="vendor_id" value="{{ Auth::guard('admin')->user()->id }}" />
		        			    @else
								<div class="col-md-6 mb-4">
									<label for="vendor_id" class="col-form-label" style="font-weight: bold;">Vendor:</label>
									<div class="custom_select">
										<select class="form-control select-active w-100 form-select select-nice" name="vendor_id" id="vendor_id">
											<option value="0">--Select Vendor--</option>
												@foreach($vendors as $vendor)
													<option value="{{ $vendor->user_id }}" {{ $vendor->user_id == $product->vendor_id ? 'selected' : '' }}>{{ $vendor->shop_name ?? 'Null' }}</option>
												@endforeach
										</select>
									</div>
								</div>
								@endif
							@endif

	                        <div class="col-md-6 mb-4">
	                         	<label for="supplier_id" class="col-form-label" style="font-weight: bold;">Supplier:</label>
				                <div class="custom_select">
                                    <select class="form-control select-active w-100 form-select select-nice" name="supplier_id" id="supplier_id">
                                    	<option value="">--Select Supplier--</option>
					                	@foreach($suppliers as $supplier)
					                		<option value="{{ $supplier->id }}" @if($product->supplier_id == $supplier->id) selected @endif>{{ $supplier->name ?? 'Null' }}</option>
					               		@endforeach
                                    </select>
                                </div>
				            </div>
							<div class="col-md-6 mb-4">
								<label for="unit_id" class="col-form-label" style="font-weight: bold;">Unit Type:</label>
								<div class="custom_select">
									<select class="form-control select-active w-100 form-select select-nice" name="unit_id" id="unit_id">
										<option disabled hidden {{old('unit_id') ? '' : 'selected'}} readonly value="">--Select Unit Type--</option>
										@foreach($units as $unit)
											<option value="{{ $unit->id }}" @if($product->unit_id == $unit->id) selected @endif>{{ $unit->name }}</option>
										@endforeach
									</select>
								</div>
						   	</div>

							{{-- <div class="col-md-6 mb-4">
								<label for="unit_weight" class="col-form-label" style="font-weight: bold;">Unit Weight (e.g. 10 mg, 1 Carton, 15 Pcs)</label>
								<input class="form-control" id="unit_weight" type="number" name="unit_weight" placeholder="Write unit weight" value="{{ $product->unit_weight }}">
						   	</div> --}}

	                        <div class="col-md-6 mb-4">
		                        <label for="product_name_en" class="col-form-label" style="font-weight: bold;">Tags:</label>
			                    <input class="form-control tags-input" type="text"name="tags[]" value="{{ $product->tags }}" placeholder="Type and hit enter to add a tag">
			                    <small class="text-muted d-block">This is used for search. </small>
		                    </div>
		                    
		                    <div class="col-md-6 mb-4">
								<label for="product_sku" class="col-form-label" style="font-weight: bold;">Product SKU: <span class="text-danger">*</span></label>
								<input class="form-control" id="product_sku" type="text" name="product_sku" placeholder="Write Product SKU" value="{{ $product->product_sku }}">
								@error('product_sku')
									<p class="text-danger">{{$message}}</p>
								@enderror
						   	</div>
		        		</div>
		        		<!-- row //-->
		        	</div>
		        	<!-- card body .// -->
			    </div>
			    <!-- card .// -->

			    <div class="card">
					<div class="card-header" style="background-color: #fff !important;">
						<h3 style="color: #4f5d77 !important">Product Variation</h3>
					</div>
		        	<div class="card-body">
		        		<div class="row">
	                        <!-- Variation Start -->
	                        <div class="col-md-6 mb-4">
				                <div class="custom_select cit-multi-select">
				                	<label for="choice_attributes" class="col-form-label" style="font-weight: bold;">Attributes:</label>
                                    <select class="form-control select-active w-100 form-select select-nice" name="choice_attributes[]" id="choice_attributes" multiple="multiple" data-placeholder="Choose Attributes">
					                	@foreach($attributes as $attribute)
					                		@if($product->is_varient==1 && count(json_decode($product->attributes)) > 0)
					                			<option @if(in_array($attribute->id, json_decode($product->attributes))) selected @endif value="{{ $attribute->id }}">{{ $attribute->name }}</option>
					                		@else
					                			<option value="{{ $attribute->id }}">{{ $attribute->name }}</option>
					                		@endif
					               		@endforeach
                                    </select>
                                </div>
	                        </div>

	                        <div class="col-md-12 mb-4">
	                        	<div class="customer_choice_options" id="customer_choice_options">
	                        		<div class="mb-4">
										@foreach ($attributes as $attribute)
											@if($product->is_varient==1 && count(json_decode($product->attributes)) > 0)

												@if(in_array($attribute->id, json_decode($product->attributes)))
													@php
														$attr_values = array();
														foreach(json_decode($product->attribute_values) as $attribute_value){
															if($attribute_value->attribute_id == $attribute->id){
																$attr_values = $attribute_value->values;
															}
														}
													@endphp
													<div class="custom_select cit-multi-select mb-4">
														<label for="choice_attributes" class="col-form-label" style="font-weight: bold;">{{$attribute->name}} :</label>
												    	<select class="form-control form-select js-example-basic-multiple" name="choice_options{{$attribute->id}}[]" id="choice_options{{$attribute->id}}" onchange="makeCombinationTable(this)" multiple data-placeholder="Nothing selected">
												        	@foreach($attribute->attribute_values as $value)
												        		<option @if(in_array($value->value, $attr_values)) selected @endif value="{{ $value->value }}">{{ $value->value }}</option>
												       		@endforeach
												        </select>
													</div>
												@endif
											@endif
										@endforeach
									</div>

									<script>
										$(document).ready(function() {
									    	$('.js-example-basic-multiple').select2();
										});
									</script>
	                        	</div>
	                        </div>
	                        <!-- Variation End -->
		        		</div>
		        	</div>
		        </div>
		        <!-- card //-->

		        <div class="card">
					<div class="card-header" style="background-color: #fff !important;">
						<h3 style="color: #4f5d77 !important">Pricing</h3>
					</div>
		        	<div class="card-body">
		        		<div class="row">
		        		    @if(Auth::guard('admin')->user()->role != '5')
		        			<div class="col-md-12 mb-4">
	                          	<label for="bying_price" class="col-form-label" style="font-weight: bold;">Product Buying Price: <span class="text-danger"> *</span></label>
	                            <input class="form-control" id="purchase_price" type="number" name="purchase_price" placeholder="Write product bying price" value="{{ $product->purchase_price }}">
		                        @error('purchase_price')
                                    <p class="text-danger">{{$message}}</p>
                                @enderror
		                    </div>
		                    @endif

		                    {{-- <div class="col-md-6 mb-4">
	                          	<label for="wholesell_price" class="col-form-label" style="font-weight: bold;">Whole Sell Price:</label>
	                            <input class="form-control" id="wholesell_price" type="number" name="wholesell_price" placeholder="Write product whole sell price" value="{{ $product->wholesell_price }}">
	                        </div>
	                        <div class="col-md-6 mb-4">
	                          	<label for="wholesell_minimum_qty" class="col-form-label" style="font-weight: bold;">Whole Sell Minimum Quantity:</label>
	                            <input class="form-control" id="wholesell_minimum_qty" type="number" name="wholesell_minimum_qty" placeholder="Write product whole sell qty" value="{{ $product->wholesell_minimum_qty }}">
	                        </div> --}}
		        		</div>
		        		<!-- Row //-->
		        		<div class="row">
			        		<div class="col-md-4 mb-4">
	                          	<label for="regular_price" class="col-form-label" style="font-weight: bold;">Regular Price: <span class="text-danger"> *</span></label>
	                            <input class="form-control" id="regular_price" type="number" name="regular_price" placeholder="Write product regular price" value="{{ $product->regular_price }}" min="0">
		                        @error('regular_price')
	                                <p class="text-danger">{{$message}}</p>
	                            @enderror
	                        </div>
	                        <div class="col-md-4 mb-4">
	                          	<label for="discount_price" class="col-form-label" style="font-weight: bold;">Discount Price:</label>
	                            <input class="form-control" id="discount_price" type="text" name="discount_price" value="{{ $product->discount_price }}" min="0" placeholder="Write product discount price">
	                        </div>
	                        <div class="col-md-4 mb-4">
	                         	<label for="discount_type" class="col-form-label" style="font-weight: bold;">Discount Type:</label>
				                <div class="custom_select">
                                    <select class="form-control select-active w-100 form-select select-nice" name="discount_type" id="discount_type">
					                	<option value="1" <?php if ($product->discount_type == '1') echo "selected"; ?>>Flat</option>
	                            		<option value="2" <?php if ($product->discount_type == '2') echo "selected"; ?>>Parcent %</option>
                                    </select>
                                </div>
	                        </div>
	                        <div class="col-md-4 mb-4">
								<label for="minimum_buy_qty" class="col-form-label" style="font-weight: bold;">Minimum Buy Quantity:</label>
								<input class="form-control" id="minimum_buy_qty" type="number" name="minimum_buy_qty" placeholder="Write product qty" value="{{ $product->minimum_buy_qty }}" min="1">
								@error('minimum_buy_qty')
									<p class="text-danger">{{$message}}</p>
								@enderror
							</div>
							<div class="col-md-4 mb-4">
								<label for="stock_qty" class="col-form-label" style="font-weight: bold;">Stock Quantity: <span class="text-danger"> *</span></label>
								<input class="form-control" id="stock_qty" type="number" name="stock_qty" value="{{ $product->stock_qty }}" min="0" placeholder="Write product stock qty">
								@error('stock_qty')
									<p class="text-danger">{{$message}}</p>
								@enderror
							</div>
                            <div class="col-md-4 mb-4">
								<label for="low_qty" class="col-form-label" style="font-weight: bold;">Low Quantity: <span class="text-danger">*</span></label>
								<input class="form-control" id="low_qty" type="number" name="low_qty" value="{{ $product->low_qty }}" min="0" placeholder="Write product low qty">
								@error('low_qty')
								   <p class="text-danger">{{$message}}</p>
							   	@enderror
							</div>

                            <div class="col-md-4 mb-4">
								<label for="reseller_price" class="col-form-label" style="font-weight: bold;">Reseller Price: <span class="text-danger">*</span></label>
								<input class="form-control" id="reseller_price" type="number" name="reseller_price" placeholder="Write product regular price" min="0" value="{{ $product->reseller_price }}" required>
								@error('reseller_price')
									<p class="text-danger">{{$message}}</p>
								@enderror
						  	</div>

							<!-- Product Attribute Price combination Starts -->
							<div class="col-12 mt-2 mb-2" id="variation_wrapper">
								<label for="" class="col-form-label" style="font-weight: bold;">Price Variation:</label>
								<input type="hidden" id="is_variation_changed" name="is_variation_changed" value="0">
								<table class="table table-active table-success table-bordered" id="combination_table">
									<thead>
										<tr>
											<th>Variant</th>
											<th>Price</th>
											<th>Reseller Price</th>
											<th>SKU</th>
											<th>Quantity</th>
											<th>Photo</th>
										</tr>
									</thead>
									<tbody>
										@foreach($product->stocks as $stock)
											<tr>
												<td>{{ $stock->varient }}<input type="hidden" name="{{ $stock->id }}_variant" class="form-control" value="{{ $stock->varient }}" required></td>
												<td><input type="text" name="{{ $stock->id }}_price" class="form-control vdp" value="{{ $stock->price }}" required></td>
												<td><input type="text" name="{{ $stock->id }}_resell_price" class="form-control revdp" value="{{ $stock->resell_price }}" required></td>
												<td><input type="text" name="{{ $stock->id }}_sku" class="form-control" required value="{{ $stock->sku }}"></td>
												<td><input type="text" name="{{ $stock->id }}_qty" class="form-control" value="{{ $stock->qty }}" required></td>
												<td>
													<img src="{{ asset($stock->image) }}" alt="{{ $stock->varient }}-image" style="width: 15%; float: left;">
													<input type="file" name="{{ $stock->id }}_image" class="form-control" style="width: 80%; float: left; margin-left: 5%;">
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
							<!-- Product Attribute Price combination Ends -->
			        	</div>
			        	<!-- Row //-->
		        	</div>
		        </div>
		        <!-- card //-->

		        <div class="card">
		        	<div class="card-header" style="background-color: #fff !important;">
						<h3 style="color: #4f5d77 !important">Description</h3>
					</div>
		        	<div class="card-body">
		        		<div class="row">
		        			<!-- Description Start -->
	                        <div class="col-md-6 mb-4">
	                          	<label for="short_descp_en" class="col-form-label" style="font-weight: bold;">Short Description (En):</label>
	                            <textarea name="short_description_en" id="short_descp_en" rows="2" cols="2" class="form-control summernote" placeholder="Write Short Description English">{{ $product->short_description_en }}</textarea>
	                        </div>
	                        <div class="col-md-6 mb-4">
	                          	<label for="short_descp_bn" class="col-form-label" style="font-weight: bold;">Short Description (Bn):</label>
	                            <textarea name="short_description_bn" id="short_descp_bn" rows="2" cols="2" class="form-control summernote" placeholder="Write Short Description Bangla">{{ $product->short_description_bn }}</textarea>
	                        </div>
	                        <!-- Description End -->
		        		</div>

						<div class="row">
		        			<!-- Description Start -->
	                        <div class="col-md-6 mb-4">
	                          	<label for="long_descp_en" class="col-form-label" style="font-weight: bold;">Long Description (En):</label>
	                            <textarea name="description_en" id="long_descp_en" rows="2" cols="2" class="form-control summernote" placeholder="Write Long Description English">{{ $product->description_en }}</textarea>
	                        </div>
	                        <div class="col-md-6 mb-4">
	                          	<label for="long_descp_bn" class="col-form-label" style="font-weight: bold;">Long Description (Bn):</label>
	                            <textarea name="description_bn" id="long_descp_bn" rows="2" cols="2" class="form-control summernote" placeholder="Write Long Description Bangla">{{ $product->description_bn }}</textarea>
	                        </div>
	                        <!-- Description End -->
		        		</div>
		        	</div>
		        </div>
		        <!-- card //-->

		        <div class="card">
		        	<div class="card-header" style="background-color: #fff !important;">
						<h3 style="color: #4f5d77 !important">Product Image <span class="text-danger"> *</span></h3>
					</div>
		        	<div class="card-body">
	        			<!-- Porduct Image Start -->
                        <div class="mb-4">
                          	<label for="product_thumbnail" class="col-form-label" style="font-weight: bold;">Product Image:</label>
                            <input type="file" name="product_thumbnail" class="form-control" id="product_thumbnail" onChange="mainThamUrl(this)">
							<img src="{{ asset($product->product_thumbnail) }}" width="100" height="100" class="p-2" id="mainThmb">
						</div><br><br>
						<div class="col-md-12 mb-3">
		                	<div class="box-header mb-3 d-flex">
						        <h4 class="box-title">Product Multiple Image <strong>Update:</strong></h4>
						    </div>
		                	<div class="box bt-3 border-info">
					         	<div class="row row-sm">

						            @foreach($multiImgs as $img)
						            <div class="col-md-3">
						               <div class="card">
						                  <img src="{{ asset($img->photo_name) }}" class="showImage{{$img->id}}" style="height: 270px; width: 280px;">
						                  	<div class="card-body">
						                     <h5 class="card-title">
						                        <a id="{{ $img->id }}" onclick="productRemove(this.id)" class="btn btn-sm btn-danger" title="Delete Data">Delete</a>
						                     </h5>
						                  </div>
						               </div>
						            </div>
						            <!--  end col md 3		 -->
					           		@endforeach
					           		<div class="mb-4">
					           			<div class="row  p-2" id="preview_img">

										</div>
			                          	<label for="multiImg" class="col-form-label" style="font-weight: bold;">Add More:</label>
			                            <input type="file" name="multi_img[]" class="form-control" multiple="" id="multiImg" >
									</div>
					         	</div>
						   </div>

						   <div class="mb-4">
								<label for="youtube_link" class="col-form-label" style="font-weight: bold;">YouTube Link</label>
								<input class="form-control" id="youtube_link" type="text" name="youtube_link" placeholder="Write Product YouTube Link" value="{{ $product->youtube_link }}">
						   	</div>
		                </div>
						<!-- Porduct Image End -->
		        		<!-- Checkbox Start -->
                        <div class="mb-4">
                        	<div class="row">
                          		<div class="custom-control custom-switch">
                                    <input type="checkbox" class="form-check-input me-2 cursor" name="is_deals" id="is_deals" {{ $product->is_deals == 1 ? 'checked': '' }} value="1">
                                    <label class="form-check-label cursor" for="is_deals">Today's Deal</label>
                                </div>
                          	</div>
                          	<div class="row">
                          		<div class="custom-control custom-switch">
                                    <input type="checkbox" class="form-check-input me-2 cursor" name="is_featured" id="is_featured" {{ $product->is_featured == 1 ? 'checked': '' }} value="1">
                                    <label class="form-check-label cursor" for="is_featured">Featured</label>
                                </div>
                          	</div>
                          	<div class="row">
                          		<div class="custom-control custom-switch">
                                    <input type="checkbox" class="form-check-input me-2 cursor" name="status" id="status" {{ $product->status == 1 ? 'checked': '' }} value="1">
                                    <label class="form-check-label cursor" for="status">Status</label>
                                </div>
                          	</div>
                          	<div class="row">
                          		<div class="custom-control custom-switch">
                                    <input type="checkbox" class="form-check-input me-2 cursor" name="show_stock" id="show_stock" {{ $product->show_stock == 1 ? 'checked': '' }} value="1">
                                    <label class="form-check-label cursor" for="show_stock">Show Stock</label>
                                </div>
                          	</div>
                          	<div class="row">
                          		<div class="custom-control custom-switch">
                                    <input type="checkbox" class="form-check-input me-2 cursor" name="top_selling" id="top_selling" {{ $product->top_selling == 1 ? 'checked': '' }} value="1">
                                    <label class="form-check-label cursor" for="top_selling">Top Selling</label>
                                </div>
                          	</div>
                          	<div class="row">
                          		<div class="custom-control custom-switch">
                                    <input type="checkbox" class="form-check-input me-2 cursor" name="new_arrival" id="new_arrival" {{ $product->new_arrival == 1 ? 'checked': '' }} value="1">
                                    <label class="form-check-label cursor" for="new_arrival">New arrival</label>
                                </div>
                          	</div>
                        </div>
                        <!-- Checkbox End -->
		        	</div>
		        </div>
		        <!-- card -->

			    <div class="row mb-4 justify-content-sm-end">
					<div class="col-lg-2 col-md-4 col-sm-5 col-6">
						<input type="submit" class="btn btn-primary" value="Update">
					</div>
				</div>
		    </form>
		</div>
	</div>
</section>
@endsection



@push('footer-script')
<script>


    function makeCombinationTable(el) {

        $.ajax({
            url: '{{ route('admin.api.attributes.index') }}',
            type: 'get',
            dataType: 'json',
            processData: true,
            data: $(el).closest('form').serializeArray().filter(function (field) {
                return field.name.includes('choice');
            }),
            success: function (response) {
				//console.log(response);
                if (!response.success) {
                    return;
                }
				if(Object.keys(response.data).length > 0) {
					let price = $('#regular_price').val();
					let resellprice = $('#reseller_price').val();
					let qty = $('#stock_qty').val();
					$('#combination_table tbody').html($.map(response.data, function (item, index) {
						return `<tr>
									<td>${index}<input type="hidden" name="vnames[]" class="form-control" value="${index}" required></td>
									<td><input type="text" name="vprices[]" class="form-control vdp" value="`+price+`" required></td>
									<td><input type="text" name="vresellprices[]" class="form-control revdp" value="`+resellprice+`" required></td>
									<td><input type="text" name="vskus[]" class="form-control" required value="sku-${index}"></td>
									<td><input type="text" name="vqtys[]" class="form-control" value="10" required></td>
									<td><input type="file" name="vimages[]" class="form-control"></td>
								</tr>`;
					}).join());
					$('#variation_wrapper').show();
					$('#is_variation_changed').val(1);
				}else{
					$('#combination_table tbody').html();
				}

            }
        });
    }
</script>
<!-- Attribute -->
<script type="text/javascript">
	function add_more_customer_choice_option(i, name){
        $.ajax({
            type:"POST",
            url:'{{ route('products.add-more-choice-option') }}',
            data:{
               attribute_ids: i,
               _token:  "{{ csrf_token() }}"
            },
            success: function(data) {
                $('#customer_choice_options').append(data);
           }
       });
    }

	$('#choice_attributes').on('change', function() {
        $('#customer_choice_options').html(null);

    	$('#choice_attributes').val();
    	add_more_customer_choice_option($(this).val(), $(this).text());
    });

    $('#regular_price').on('keyup', function() {
    	var price = $('#regular_price').val();
    	$('.vdp').val(price);
    });
    
    $('#reseller_price').on('keyup', function() {
    	var resellprice = $('#reseller_price').val();
    	$('.revdp').val(resellprice);
    });
</script>

<!-- Attribute end -->


	<!-- Product Image -->
	<script type="text/javascript">
		function mainThamUrl(input){
			if (input.files && input.files[0]) {
				var reader = new FileReader();
				reader.onload = function(e){
					$('#mainThmb').attr('src',e.target.result).width(100).height(80);
				};
				reader.readAsDataURL(input.files[0]);
			}
		}
	</script>

	<!-- Image Show -->
    <script type="text/javascript">
        $(document).ready(function(){
            $('.image1').change(function(e){
                var reader = new FileReader();
                reader.onload = function(e){
                    $('.showImage').attr('src',e.target.result);
                }
                reader.readAsDataURL(e.target.files['0']);
            });
        });
    </script>

<!-- Product MultiImg -->
<script>
  $(document).ready(function(){
	$('#variation_wrapper').hide();
	var stockSize = {{ count($product->stocks) }};
	if(stockSize > 0){
		$('#variation_wrapper').show();
	}
   	$('#multiImg').on('change', function(){ //on file input change
      if (window.File && window.FileReader && window.FileList && window.Blob) //check File API supported browser
      {
          var data = $(this)[0].files; //this file data

          $.each(data, function(index, file){ //loop though each file
              if(/(\.|\/)(gif|jpe?g|png)$/i.test(file.type)){ //check supported file type
                  var fRead = new FileReader(); //new filereader
                  fRead.onload = (function(file){ //trigger function on successful read
                  return function(e) {
                      var img = $('<img/>').addClass('thumb').attr('src', e.target.result) .width(100)
                  .height(80); //create image element
                      $('#preview_img').append(img); //append image to output element
                  };
                  })(file);
                  fRead.readAsDataURL(file); //URL representing the file's data.
              }
          });

      }else{
          alert("Your browser doesn't support File API!"); //if File API is absent
      }
   });
  });
</script>


<!-- Malti Tags  -->
<script type="text/javascript">
	$(document).ready(function(){
	  var tagInputEle = $('.tags-input');
	  tagInputEle.tagsinput();

	});
</script>

<!-- Ajax Update Category Store -->
<script type="text/javascript">
	$(document).ready(function (e) {

			$('#category_store').submit(function(e) {
				e.preventDefault();
				var formData = new FormData(this);

			$.ajax({
				type:'POST',
				url: "{{ route('category.ajax.store') }}",
				data: formData,
				cache:false,
				contentType: false,
				processData: false,
				success: (data) => {
					$('select[name="category_id"]').html('<option value="" selected="" disabled="">Select Category</option>');
                    $.each(data.categories, function(key, value){
                        $('select[name="category_id"]').append('<option value="'+ value.id +'">' + value.name_en + '</option>');
                        $.each(value.children_categories, function(k, sub) {
                        	var stx = '';
						    for (var i=0; i < sub.type; i++){
						        stx += '--';
						    }
                        	$('select[name="category_id"]').append('<option value="'+ sub.id +'">'+ stx + sub.name_en + '</option>');
                        });
                    });

					// console.log(data);
					$('#category' ).modal('hide');
					$('#showImage').remove();
					$('#cat{{$category->id}}').remove();
					this.reset();
					// Start Message
                    const Toast = Swal.mixin({
                          toast: true,
                          position: 'top-end',
                          icon: 'success',
                          showConfirmButton: false,
                          timer: 2000
                        })
                    if ($.isEmptyObject(data.error)) {
                        Toast.fire({
                            type: 'success',
                            title: data.success
                        })
                    }else{
                        Swal.fire({
						  icon: 'error',
						  title: data.error,
						})
                    }
                    // End Message


					// alert('Image has been uploaded using jQuery ajax successfully');
				},

				error: function(data){
					console.log(data);
				}
			});
		});
	});
</script>

<!-- Ajax Brand Update Store -->
<script type="text/javascript">
	$(document).ready(function (e) {

			$('#brand_store').submit(function(e) {
				e.preventDefault();
				var formData = new FormData(this);

			$.ajax({
				type:'POST',
				url: "{{ route('brand.ajax.store') }}",
				data: formData,
				cache:false,
				contentType: false,
				processData: false,
				success: (data) => {
					$('select[name="brand_id"]').html('<option value="" selected="" disabled="">Select Brand</option>');
                    $.each(data.brands, function(key, value){
                        $('select[name="brand_id"]').append('<option value="'+ value.id +'">' + value.name_en + '</option>');
                    });

					$( '#brand' ).modal('hide');
					$('.showImage').remove();
					this.reset();
					// Start Message
                    const Toast = Swal.mixin({
                          toast: true,
                          position: 'top-end',
                          icon: 'success',
                          showConfirmButton: false,
                          timer: 2000
                        })
                    if ($.isEmptyObject(data.error)) {
                        Toast.fire({
                            type: 'success',
                            title: data.success
                        })
                    }else{
                        Swal.fire({
						  icon: 'error',
						  title: data.error,
						})
                    }
                    // End Message

					// alert('Image has been uploaded using jQuery ajax successfully');
				},

				error: function(data){
					console.log(data);
				}
			});
		});
	});
</script>


<!-- modal brand show image  -->
<script type="text/javascript">
    $(document).ready(function(){
        $('.brand_image').change(function(e){
            var reader = new FileReader();
            reader.onload = function(e){
                $('.showImage').attr('src',e.target.result);
            }
            reader.readAsDataURL(e.target.files['0']);
        });
    });
</script>

<!-- ==================== Start Gallery Image Remove =============== -->
<script type="text/javascript">
    function productRemove(id){
        // console.log(id);
        $.ajax({
           type:'GET',
           url:"/admin/product/multiimg/delete/"+id,
           dataType: 'json',
           success:function(data){
             location.reload();
           	//console.log(data);
           	// location.reload();
            // Start Message
            const Toast = Swal.mixin({
                  toast: true,
                  position: 'top-end',
                  icon: 'success',
                  showConfirmButton: false,
                  timer: 2000
                })
            if ($.isEmptyObject(data.error)) {
                Toast.fire({
                    type: 'success',
                    title: data.success
                })
            }else{
                Toast.fire({
                    type: 'error',
                    title: data.error
                })
            }
            // End Message
           }
        });
      }
</script>

<script>
    function discountamount() {
        var regular_price = parseFloat($('#regular_price').val()) || 0;
        var reseller_price = parseFloat($('#reseller_price').val()) || 0;
        var disAmount = parseFloat($('#discount_price').val()) || 0;
        var discount_type = parseFloat($('#discount_type').val()) || 0;

        if (discount_type == 1) {
            disAmount = Math.min(disAmount, regular_price);
        } else {
            disAmount = Math.min(disAmount, 100);
        }
        var discounted_price = 0;
        if (discount_type == 1) {
            discounted_price = regular_price - disAmount;
        } else {
            discounted_price = regular_price - (regular_price * (disAmount / 100));
        }

        $('#discount_price').val(disAmount);
    }

    $(document).ready(function() {
        $('#discount_price').on("keyup", discountamount);
        $('#discount_type').on("change", discountamount);
        $('#regular_price').on("keyup", discountamount);
        $('#reseller_price').on("keyup", discountamount);
    });
</script>
@endpush