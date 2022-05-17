<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Barcode</title>
	<style>

	     .barcode{
			text-align: center;
			margin-left:7px;
			/*padding:20px;*/
			display: inline;
			margin-top: 10px;
		}
		.text{
			font-size: 12px;
		}
		table{
			text-align: center;
			width:100%;
		}
		td{
			border: 10px solid black;
			padding:5px;
			text-align: center;
		}
		tr{
			margin-top:20px;
		}
		p{
			line-height:2px;
		}
	</style>
</head>
<body>

	<h1 style="text-align: center;">Product Barcode</h1>
	<table>
@php
$row="<tr>";
// echo 0%3;
for($i=0;$i<count($barcode);$i++){
	switch (true) {
		case ($i==0):
			echo $row;
			break;
		case ($i%3==0 and $i/3>0):
		    echo '</tr>';
		    echo $row;
		    break;
	}
	echo "<td><img class='barcode' src='data:image/svg;base64,".base64_encode($barcode[$i])."'>
		<p class='text'>".$text[$i]."</p>
		<p class='text'>tk : ".$price[$i]."/=</p></td>";
	// echo 'xxxx:'.(($i+1)%3).'<br>';
	
}
@endphp
</table>
{{-- <table>
	<tr>
		<td style="margin-right: 20px;">
				<img class='barcode' src='data:image/svg;base64,{{base64_encode($barcode[0])}}'>
				<p class='text'>{{$text[0]}}</p>
		</td>
		<td>dfdf</td>
		<td>dfdf</td>
		<td>dfdf</td>
	</tr>
	<tr>
		<td style="margin-right: 20px;">
				<img class='barcode' src='data:image/svg;base64,{{base64_encode($barcode[0])}}'>
				<p class='text'>{{$text[0]}}</p>
		</td>
		<td>dfdf</td>
		<td>dfdf</td>
		<td>dfdf</td>
	</tr>
</table> --}}
</body>
</html>