<!DOCTYPE html>
<html>
<head>
	<title>Welcome To Softimpire</title>
	<link rel="stylesheet" href="../css/bootstrap.css">
	<link rel="stylesheet" href="../css/font-awesome.css">
	<link rel="shortcut icon" href="https://softimpire.com/img/softimpire.jpg" type="image/ico" class="rounded-circle">
</head>
<body style="background-color: #6F42C1;">
		<div style="background-color: #0000E6;" class="p-4 text-light">
				<center><img src="https://softimpire.com/img/softimpire.jpg" alt="something" style="height: 120px;width:165px" class="rounded-circle"></center>
				<h2 class="font-weight-bold text-center mt-1">
				SOFTiMPIRE এ আপনাকে সাগতম
				</h2>
				<h4 class="font-weight-bold text-center">
					<i class="nav-icon fa fa-shopping-cart"></i> অর্ডার ফরম টি পূরন করুন
				</h4>
				<p class="text-center">
					 যে কোন প্রয়োজনে কল করুন <i class="nav-icon fa fa-phone"></i> ০১৭১৫২৭৯৪৯৮,০১৮৭৩০৭২২৫৩
				</p>
				<p class="text-center text-dark">বি: দ্র: অর্ডার সাকসেস হওয়ার ৩ ঘন্টার মধ্যে আমাদের প্রতিনিধি আপনার সাথে যোগাযোগ করবেন দয়া করে অপেক্ষা করুন</p>
		</div>
	<div class="container mt-4">
		<div class="form-group col-12 col-md-8 m-auto">
			<label for="">আপনার নাম</label>
			<input type="text" class="form-control" placeholder="আপনার নাম লিখুন" id="name">*
			<div id="name_msg" class="invalid-feedback">
             </div>
		</div>
		<div class="form-group col-12 col-md-8 m-auto">
			<label for="">আপনার ব্যবসা প্রতিষ্ঠানের নাম</label>
			<input type="text" class="form-control" placeholder='আপনার ব্যবসা প্রতিষ্ঠানের নাম লিখুন' id="business_name">*
			<div id="business_name_msg" class="invalid-feedback">
             </div>
		</div>
		<div class="form-group col-12 col-md-8 m-auto">
			<label for="">মোবাইল নম্বর</label>
			<input type="number" class="form-control" placeholder='মোবাইল নম্বর লিখুন' id="number">*
			<div id="number_msg" class="invalid-feedback">
             </div>
		</div>
		<div class="form-group col-12 col-md-8 m-auto">
			<label for="">ই-মেইল</label>
			<input type="email" class="form-control" placeholder='মোবাইল নম্বর লিখুন' id="email">
			<div id="email_msg" class="invalid-feedback">
             </div>
		</div>
		<br>
		<div class="form-group col-12 col-md-8 m-auto">
			<label for="">সম্পুর্ণ ঠিকানা লিখুন</label>
			<input type="text" class="form-control" placeholder='সম্পুর্ণ ঠিকানা লিখুন' id="adress">*
			<div id="adress_msg" class="invalid-feedback">
             </div>
		</div>
		<div class="form-group col-12 col-md-8 m-auto">
			<label for="">আপনার বর্তমান ঠিকানা লিখুন</label>
			<input type="text" class="form-control" placeholder='আপনার বর্তমান ঠিকানা লিখুন' id="current_adress">
			<div id="current_adress_msg" class="invalid-feedback">
             </div>
		</div>
		<br>
		<div class="col-12 col-md-8 m-auto h4">
			বিকাশ,রকেট নম্বর : 01731186740
		</div>
		<br>
		<div class="form-group col-12 col-md-8 m-auto">
			<label for="text">পেমেন্ট মেথড সিলেক্ট করুন</label>
			<select class="form-control" name="" id="payment_method">
				<option value="">--select--</option>
				<option value="Bkash">Bkash</option>
				<option value="Rocket">Rocket</option>
			</select>*
			<div id="payment_method_msg" class="invalid-feedback">
            </div>
		</div>
		<div class="form-group col-12 col-md-8 m-auto">
			<label for="">যে ওয়ালেট থেকে আপনি টাকা পাঠিয়েছেন সেই নম্বরটি লিখুন</label>
			<input type="number" class="form-control" placeholder='ওয়ালেট নম্বরটি লিখুন' id="wallet_number">*
			<div id="wallet_number_msg" class="invalid-feedback">
             </div>
		</div>
		<div class="form-group col-12 col-md-8 m-auto">
			<label for="">ট্রান্সঅ্যাকশন কোডটি লিখুন</label>
			<input type="text" class="form-control" placeholder='ট্রান্সঅ্যাকশন কোডটি লিখুন' id="transaction">*
			<div id="transaction_msg" class="invalid-feedback">
             </div>
		</div>
		<div class="form-group col-12 col-md-8 m-auto">
			<label for="">অ্যামাউন্ট</label>
			<input type="number" class="form-control" placeholder='অ্যামাউন্ট লিখুন' id="payment_ammount">*
			<div id="payment_ammount_msg" class="invalid-feedback">
             </div>
		</div>
		<div class="form-group col-12 col-md-8 m-auto">
			<label for="">আপনার মতামত লিখুন</label>
			<textarea rows="3" class="form-control" placeholder='আপনার মতামত লিখুন' id="note"></textarea>
			<div id="note_msg" class="invalid-feedback">
             </div>
		</div>
		<div class="text-center mt-5">
			<button onclick="Request()" class="btn btn-primary">SUBMIT</button>
		</div>
		<div class="text-center p-5">Developed by <a href="http://softimpire.com">SOFTiMPIRE</a></div>
	</div>
<script src="../js/jquery.min.js">
</script>
<script src="../js/jquery.min.js">
</script>
<script src="../js/bootstrap.min.js">
</script>
<script>
	function Request(){
	$('.submit').attr('disabled',true);
    $('.invalid-feedback').hide();
    $('input').css('border','1px solid rgb(209,211,226)');
    $('select').css('border','1px solid rgb(209,211,226)');
    let name=$('#name').val();
    let business_name=$('#business_name').val();
    let number=$('#number').val();
    let email=$('#email').val();
    let adress=$('#adress').val();
    let current_adress=$('#current_adress').val();
    let payment_method=$('#payment_method').val();
    let wallet_number=$('#wallet_number').val();
    let transaction=$('#transaction').val();
    let ammount=$('#payment_ammount').val();
    let note=$('#note').val();
    let formData= new FormData();
    formData.append('name',name);
    formData.append('business_name',business_name);
    formData.append('number',number);
    formData.append('email',email);
    formData.append('adress',adress);
    formData.append('current_adress',current_adress);
    formData.append('payment_method',payment_method);
    formData.append('payment_ammount',ammount);
    formData.append('wallet_number',wallet_number);
    formData.append('transaction',transaction);
    formData.append('note',note);
    //axios post request
      axios.post('/order',formData)
      .then(function (response){
      	console.log(response)
        if (response.data.message) {
          window.toastr.success(response.data.message);
          $('#exampleModal').modal('hide')
          ModalClose();
          $('.data-table').DataTable().ajax.reload();
          $('.submit').attr('disabled',false);
        }
        var keys=Object.keys(response.data);
        console.log(keys)
        for(var i=0; i<keys.length;i++){
        	console.log(keys[i])
            $('#'+keys[i]+'_msg').html(response.data[keys[i]][0]);
            $('#'+keys[i]).css('border','1px solid red');
            $('#'+keys[i]+'_msg').show();
            $('.submit').attr('disabled',false);
          }
      })
       .catch(function (error) {
        $('.submit').attr('disabled',false);
      });
}
</script>
</body>
</html>