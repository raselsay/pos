@extends("delivery.layout.app")
@section("content")
<div class="container">
  {{-- modal --}}
    <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Details</h5>
          <small class="modal-title float-right"  id="status"></small>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name='data_id'>
          <div class="form-group">
             <label for="transport">Transport</label>
             <select name="transport" id="transport">
             </select>
          </div>
          <table class="table table-sm table-bordered" id="showdetails">
            <thead>
              <tr>
                <th width="25%">Product Name</th>
                <th width="15%">Store</th>                
                <th width="15%">Stock</th>
                <th width="15%">Remaining Qty</th>
                <th width="20%">Qantity</th>
                <th width="25%">Price</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button onclick="submit()" type="button" class="btn btn-primary submit">Go Delivery</button>
        </div>
      </div>
    </div>
  </div> {{--end modal --}}
	<div class="card">
		<div class="card-header">
			<h4>Order List</h4>
		</div>
		<div class="card-body table-bordered">
			<table class="table" id="datatable">
				<thead>
					<tr>
						<th>ID</th>
						<th>Client Name</th>						
						<th>Total Item</th>	           
            <th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
	
</div>
@endsection
@section("script")
<script>
	 $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }

    });
    $('#datatable').DataTable({
        processing:true,
        serverSide:true,
        ajax:{
          url:"{{ URL::to('/delivery/all_order') }}"
        },
        columns:[
          {
            data:'DT_RowIndex',
            name:'DT_RowIndex',
            orderable:false,
            searchable:false
          },
          {
            data:'name',
            name:'name',
          },
           {
            data:'total_item',
            name:'total_item',
          },
          {
            data:'status',
            name:'status',
          },
          {
            data:'action',
            name:'action',
          }
        ],
        createdRow: function ( row, data, index ) {
          if ( data['status'] == 'Delivered' ) {
              $('td', row).eq(3).addClass('text-success');
          } else {
              $('td', row).eq(3).addClass('text-danger');
          }
      },
    });
$('#transport').select2({
      theme:"bootstrap4",
      allowClear:true,
      placeholder:'select',
      ajax:{
      url:"{{URL::to('admin/delivery/get_transport_export')}}",
      type:'post',
      dataType:'json',
      delay:20,
      data:function(params){
        return {
          searchTerm:params.term,
          _token:'{{csrf_token()}}',
          }
      },
      processResults:function(response){
        return {
          results:response,
        }
      },
      cache:true,
    }
  })
    $(document).on('click','.edit',function(){
      dataid=$(this).attr('data-id');
      let status=$(this).parent().parent().prev().text();
      $("input[name='data_id']").val(dataid);
      axios.get('admin/delivery/get_order_sales/'+dataid)
      .then((res)=>{
          console.log(res)
          html="";
          for (var i = 0; i < res.data.length; i++) {
            html+="<tr>"
            html+="<td>"+res.data[i].product_name+"</td>";
            html+=`<td class='d-none'>`+res.data[i].product_id+`</td>`;
            html+=`<td>
                      <select class='form-control form-control-sm' type='number' name='store[]' id='store`+i+`' value=''>
                      </select>
                  </td>`;
            html+=`<td></td>`;         
            html+="<td>"+res.data[i].deb_qantity+"</td>";
            html+="<td><input class='form-control form-control-sm' type='number' name='qantity[]' value='"+res.data[i].deb_qantity+"' "+((res.data[i].deb_qantity==0.00) ? 'disabled' : '')+"></td>";
            html+="<td>"+res.data[i].price+"</td>";
            html+="</tr>"
          }
          $('#showdetails tbody').html(html);

          if(status=='Delivered'){
            $('#status').text(status)
            $('#status').addClass('bg-success rounded p-1');
            $('#status').removeClass('bg-danger');
            $('.submit').attr('disabled',true);
          }else{
            $('#status').text(status)
            $('#status').addClass('bg-danger rounded p-1');        
            $('#status').removeClass('bg-success');
            $('.submit').attr('disabled',false);
          }
          for (var i = 0; i < res.data.length; i++) {
              $('#store'+i).select2({
                  theme:"bootstrap4",
                  allowClear:true,
                  placeholder:'select',
                  ajax:{
                  url:"{{URL::to('admin/delivery/store_search')}}",
                  type:'post',
                  dataType:'json',
                  delay:20,
                  data:function(params){
                    return {
                      searchTerm:params.term,
                      _token:'{{csrf_token()}}',
                      }
                  },
                  processResults:function(response){
                    return {
                      results:response,
                    }
                  },
                  cache:true,
                }
              })
          }
          $('#exampleModalLong').modal('show');
      })
    })
    function validate(){
      let x=false;
      $("input[name='qantity[]']").each(function(){
        $(this).parent().css('background-color','white');
        x=parseFloat($(this).val())<=parseFloat($(this).parent().prev().text());
        if(!x){
          $(this).parent().css('background-color','red');
          return false;
        }
      });
      $("input[name='qantity[]']").each(function(){
        $(this).parent().css('background-color','white');
        x=parseFloat($(this).val())<=parseFloat($(this).parent().prev().prev().text());
        if(!x){
          $(this).parent().css('background-color','red');
          return false;
        }
      });
      return x;
    }
    function submit(){
      console.log(validate())
      if(validate()==false){
        return false;
      }
      dataid=$("input[name='data_id']").val();      
      transport=$("#transport").val();
      qantity = $("input[name='qantity[]']")
              .map(function(){return $(this).val();}).get();
      store = $("input[name='store[]']")
              .map(function(){return $(this).val();}).get();
      axios.post("admin/delivery/confirm/"+dataid,{qantity:qantity,store:store,transport:transport})
      .then((res)=>{
        console.log(res);
        if(res.data.message){
          toastr.success(res.data.message)
          $('#exampleModalLong').modal("hide");
          $('#datatable').DataTable().ajax.reload();
        }
      })
    }
$('body').on('select2:select',"select[name='store[]']", function (e){
  store_id=e.params.data.id;
  this_cat=$(this);
  product_id=this_cat.parent().prev().text();
  if (store_id=='' || product_id=='') {
    return false;
  }
 axios.get('admin/delivery/get_qantity/'+product_id+'/'+store_id)
      .then(function(response){
            this_cat.parent().next().text(response.data[0].total);
          })
          .catch(function(error){
          console.log(error.request);
        })
 })
</script>
@endsection