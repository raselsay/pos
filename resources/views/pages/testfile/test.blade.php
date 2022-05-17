@extends('layouts.master')
@section('content')
@section('link')
<style>
  #date{
    margin:0 auto;
  }
  #submit{
    margin:0 auto;
    margin-top: 20px;
  }
</style>
@endsection
   <button onclick="pdf()">pdf</button>
@endsection
@section('script')
<script src="{{asset('js/pdf.js')}}"></script>
<script>
function pdf(){
  var text = `আমার সোনার বাংলা আমি তোমায় ভালোবাসি
                Bangladesh is Small Country @ $ 
                ৳ % ৬৭৬৫৬৪৫৪ `;
var docDefinition = {
  content: [{ text: text, style: 'header' }],
  styles: {
        header: {
            fontSize: 14,
            bold: false
        }
   },
   defaultStyle: {
         font: 'Verdana'
         }
   };
   pdfMake.fonts = {
         Verdana: {
               normal: '{{asset('fonts/KalPurush.ttf')}}',
         },
};
pdfMake.createPdf(docDefinition).open();
}
</script>
@endsection

