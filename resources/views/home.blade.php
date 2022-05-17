@extends('layouts.master')
@section('content')
    <!-- Content Header (Page header) -->
  @php
  $lang=App\MultiSetting::select('value')->where('name','language')->first();
  App::setLocale(isset($lang->value) ? $lang->value : '' );
  @endphp
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">{{__('key.dashboard.title')}} <button class="btn btn-sm btn-info" onclick="loadData()">{{__('key.dashboard.refresh')}}</button></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">{{__('key.dashboard.home')}}</a></li>
              <li class="breadcrumb-item active">{{__('key.dashboard.title')}}</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3 id="recieve"></h3>
                <p>{{__('key.dashboard.todays_receive')}}</p>
              </div>
              <div class="icon">
                <i class="nav-icon fas fa-donate"></i>
              </div>
              <a href="#" class="small-box-footer">{{__('key.dashboard.more_info')}} <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3 id="expence"></h3>

                <p>{{__('key.dashboard.todays_expence')}}</p>
              </div>
              <div class="icon">
                <i class="nav-icon fas fa-donate"></i>
              </div>
              <a href="#" class="small-box-footer">{{__('key.dashboard.more_info')}} <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3 id='total_inv'></h3>

                <p>{{__('key.dashboard.todays_invoice_ammount')}}</p>
              </div>
              <div class="icon">
                <i class="nav-icon fas fa-donate"></i>
              </div>
              <a href="#" class="small-box-footer">{{__('key.dashboard.more_info')}} <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3 id="access_days"></h3>
                <p>{{__('key.dashboard.access_permission')}}</p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="#" class="small-box-footer">{{__('key.dashboard.more_info')}} <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        </div> <!-- ./row -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-primary">
              <div class="inner">
                <h3 id="customer"></h3>
                <p>{{__('key.dashboard.customer')}}</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="#" class="small-box-footer">{{__('key.dashboard.more_info')}} <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-dark">
              <div class="inner">
                <h3 id="supplier"></h3>

                <p>{{__('key.dashboard.supplier')}}</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add text-light"></i>
              </div>
              <a href="#" class="small-box-footer">{{__('key.dashboard.more_info')}} <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-secondary">
              <div class="inner">
                <h3 id='installment'></h3>
                <p>{{__('key.dashboard.installment')}}</p>
              </div>
              <div class="icon">
                <i class="fas fa-credit-card"></i>
              </div>
              <a href="#" class="small-box-footer">{{__('key.dashboard.more_info')}} <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3 id="c_balance"></h3>
                <p>{{__('key.dashboard.total_customer_balance')}}</p>
              </div>
              <div class="icon">
                <i class="nav-icon fas fa-donate"></i>
              </div>
              <a href="#" class="small-box-footer">{{__('key.dashboard.more_info')}} <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        </div> <!-- ./row -->
        <div class="card card-success">
          <div class="card-header">
            <h3 class="card-title">{{__('key.dashboard.sale_chart')}} </h3><strong> {{__('key.dashboard.sale_chart_content')}}</strong>

            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
              </button>
              <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
            </div>
          </div>
          <div class="card-body">
            <div class="chart">
              <canvas id="barChart" style="min-height: 250px; height: 250px; max-height: 450px; max-width: 100%;"></canvas>
            </div>
          </div>
          <!-- /.card-body -->
        </div>
        <div class="row">
          <div class="col-12 col-md-6">
            <!-- PIE CHART -->
            <div class="card card-danger">
              <div class="card-header">
                <h3 class="card-title">{{__('key.dashboard.sale_pie_chart')}}<strong> {{__('key.dashboard.sale_pie_chart_content')}}</strong></h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
                </div>
              </div>
              <div class="card-body">
                <canvas id="salePieChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <div class="col-12 col-md-6">
            <!-- PIE CHART -->
            <div class="card card-info">
              <div class="card-header">
                <h3 class="card-title">{{__('key.dashboard.purchase_pie_chart')}}<strong> {{__('key.dashboard.purchase_pie_chart_content')}}</strong></h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
                </div>
              </div>
              <div class="card-body">
                <canvas id="purchasePieChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>
        
    </div>
    </section>
@endsection

@section('script')
<script src="{{asset('js/Chart.js')}}"></script>
<script>
$(document).ready(function(){
   loadData();
})
function loadData(){
    axios.get('{{route('dashboard')}}')
  .then(function(response){
    console.log(response);
    $('#recieve').text(response.data.deposit);
    $('#expence').text(response.data.expence);
    $('#total_inv').text(response.data.total_sales);
    $('#access_days').text(response.data.access_days);
    $('#customer').text(response.data.customer);
    $('#supplier').text(response.data.supplier);
    $('#installment').text(response.data.installment);
    if(parseFloat(response.data.customer_balance)<0){
      $('#c_balance').parent().parent().removeClass('bg-success')
      $('#c_balance').parent().parent().addClass('bg-danger')
      $('#c_balance').text(response.data.customer_balance);
    }else{
      $('#c_balance').parent().parent().removeClass('bg-danger')
      $('#c_balance').parent().parent().addClass('bg-success')
      $('#c_balance').text(response.data.customer_balance);
    }
    // sale chart
    sale_ctx=$('#barChart');
    data={
      labels:Object.keys(response.data.sale_chart),
      datasets:[
            {
              label:'Total',
              data:Object.values(response.data.sale_chart),
              backgroundColor:'blue',
              width:20,
            },
      ]
    }
    saleChart=new Chart(sale_ctx,{
      type:'bar',
      data:data,
      options:{}
    })
    // end sale chart
    // sale pie chart
    pie_sale_ctx=$('#salePieChart');
        data={
          labels:(Object.keys(response.data.pie_sale_chart)).map(function(x){ 
            const arr=x.replace('_'," ");
            return arr;
          }),
          datasets:[
                {
                  label:'Total',
                  data:Object.values(response.data.pie_sale_chart).map(function(x){ 
                    return (parseFloat(x)).toFixed(2);
                  }),
                  backgroundColor:[
                  'blue','green','red','yellow'],
                  borderColor:'black',
                  borderWidth:1,
                },
          ]
        }
        options={
          legend:{
            display:true,
            position:'bottom',
          }
        }
        pie_sale_chart=new Chart(pie_sale_ctx,{
          type:'pie',
          data:data,
          options:options
        })
        // end sale pie chart
        // purchase pie Chart
        purchase_pie_ctx=$('#purchasePieChart');
        data={
          labels:(Object.keys(response.data.purchase_pie_chart)).map(function(x){ 
            const arr=x.replace('_'," ");
            return arr;
          }),
          datasets:[
                {
                  label:'Total',
                  data:Object.values(response.data.purchase_pie_chart),
                  backgroundColor:[
                  'blue','green','red','yellow'],
                  borderColor:'black',
                  borderWidth:1,
                },
          ]
        }
        options={
          legend:{
            display:true,
            position:'bottom',
          }
        }
        pie_sale_chart=new Chart(purchase_pie_ctx,{
          type:'pie',
          data:data,
          options:options
        })
        // end purchase pie chart
  })
  .catch(function(error){
    console.log(error.request.response);
  })
}
 
</script>
@endsection