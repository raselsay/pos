function CustomModalClose(){
  $('#CustomModalForm').modal('hide');
}
function CustomModalForm(data={
  setting:{
    title:'',
    unique:'',
    SubmitButton:{
      text:'Submit',
      class:'btn btn-sm btn-primary',
      type:'submit'
    }
  },
  forms:false,
}){
  function MakeForm(){
    len=Object.keys(data.forms).length;
    keys=Object.keys(data.forms);
    form=''
    for (var i = 0; i < len; i++) {
      
      options="<opiton value=''>Select</option>";
      if (data.forms[keys[i]].option) {
        option=data.forms[keys[i]].option
        option.forEach(function(d){ options+="<option value='"+d.id+"'>"+d.text+"</option>";})
      }
      switch(true){
        case data.forms[keys[i]].category=='select' :
        form+=`
        <div class="input-group">
           <label class="control-label col-sm-3 text-lg-right font-weight-bold" for="name">`+data.forms[keys[i]].label+` :</label>
           <div class="col-sm-9">
               <select class="`+data.forms[keys[i]].class+`" name='`+data.forms[keys[i]].id+`' placeholder="`+data.forms[keys[i]].placeholder+`">
               `+options+`
               </select>
               <div id="`+data.forms[keys[i]].id+`_msg" class="invalid-feedback">
               </div>
            </div>
         </div>
      `
      break
         case data.forms[keys[i]].category=='input':
          form+=`
        <div class="input-group">
           <label class="control-label col-sm-3 text-lg-right font-weight-bold" for="name">`+data.forms[keys[i]].label+` :</label>
           <div class="col-sm-9">
               <input type='`+data.forms[keys[i]].type+`' class='`+data.forms[keys[i]].class+`' name='`+data.forms[keys[i]].id+`' placeholder='`+data.forms[keys[i]].placeholder+`'>
               <div id="`+data.forms[keys[i]].id+`_msg" class="invalid-feedback">
               </div>
            </div>
         </div>
      `
      }
  }
  return form;
}
 html=`<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="CustomModalForm">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">`+data.setting.title+`</h5>
                <button type="button" class="close"  aria-label="Close" onclick="CustomModalClose()">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <!--modal body-->
              <div class="modal-body mr-3" id="forms">
                <input type="hidden" name='unique_id' id='unique_id' value='`+data.setting.unique+`'>
                <form id='myCustomForm'>
                `+MakeForm()+`
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="CustomModalClose()">Close</button>
                <button type="`+data.setting.SubmitButton.type+`" class="`+data.setting.SubmitButton.class+`" >`+data.setting.SubmitButton.text+`</button>
              </div>
          </div>
      </div>
  </div>`;
  return html;
}
