<!DOCTYPE html>
<html>
<head>
	<title>Ledger</title>
	<style type="text/css">
		table thead th{
			background: red;
			border: 1px solid black;
		}
		table tbody td{
			font-size:12px;
			border:1px solid black;
		}
		table tfoot{
			border-top:2px solid black;
		}
		table{
			border-collapse: collapse;
		}
		#curr_blnc{
			/*text-align: right;*/
			margin-left: 10px;
		}
		#blnc{
			text-align: right;
			margin-right: 20px; 
		}
		#heading{
			font-size: 22px;
			font-weight: bold;
			text-align: center;
		}
		#date{
			text-align: center;
			font-size: 14px;
			font-weight: bold;
			margin-bottom: 20px;
		}
		#fromDate{
			margin-right: 20px;
		}
		#toDate{
			margin-left: 20px;
		}

	</style>
</head>
<body>
	<div id="heading">Customer Running Total Sheet</div>
	<div id="date"><span id="fromDate">{{date('d-m-Y',$fromDate)}}</span>to<span id="toDate">{{date('d-m-Y',$toDate)}}</span></div>
<table width="100%">
	<thead>
		<tr>
			<th width="5%">NO</th>
			<th width="10%">Date</th>
			<th width="15%">Details</th>
			<th width="10%">V-ID</th>
			<th width="5%">Qantity</th>
			<th width="10%">Price</th>
			<th width="15%">Debit</th>
			<th width="15%">Credit</th>
			<th width="20%">Balance</th>
		</tr>
	</thead>
	<tbody>
		@php
		$i=1;
		@endphp
		@foreach($get as $row)
		<tr>
			<td>{{$i++}}</td>
			<td>
				@php
			 		if($row->dates!=='0'){
			 			echo date('d-m-Y',(int)$row->dates);
			 		}else{
			 			echo null;
			 		}
			 	@endphp
			</td>
			<td>{{$row->product_name}}</td>
			<td>{{$row->voucer_id}}</td>
			<td>{{$row->qantity}}</td>
			<td>{{$row->price}}</td>
			<td>{{$row->debit}}</td>
			<td>{{$row->credit}}</td>
			<td>{{$row->balance}}</td>
		</tr>
		@endforeach
		<tfoot>
			<tr>
				<th colspan="6"></th>
				<th colspan="3" id="blnc">Current Balance:<span id='curr_blnc'>{{$current_blnce[0]->total}}</span></th>
			</tr>
		</tfoot>
</tbody>
</table>
</body>
</html>