/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var validations=[{
    "input":"text",
    "validations":["required","maxlen","minlen","email","numeric"]
},
{
    "input":"select",
    "validations":["noselect"]
},

{
    "input":"checkboxlist",
    "validations":["minselect","maxselect"]
},
{
    "input":"checkbox",
    "validations":["mustcheck"]
},{
    "input":"date",
    "validations":["required"]
}];
var rows= ["tr_required","tr_minlen","tr_maxlen","tr_minselect","tr_maxselect","tr_noselect","tr_email","tr_numeric","tr_mustcheck"];
function showHide(validations){
    var active=false;
    for(var i=0;i<rows.length;i++){
        for(var b=0;b<validations.length;b++){
            if(rows[i].substring(3)==validations[b])
            {
                active=true;
                break;
            }
        }
        if(active)jQuery('#'+rows[i]).show();
        else jQuery('#'+rows[i]).hide();
        active=false;
    }
}
function selectValidations(input_type){
    switch(input_type){
        case 'text':
            showHide(validations[0].validations);
            break;
        case 'textarea':
            showHide(validations[0].validations);
            break;
        case 'wysiwyg':
            showHide(validations[0].validations);
            break;
        case 'select':
            showHide(validations[1].validations);
            break;
        case 'checkboxlist':
            showHide(validations[2].validations);
            break;
        case 'checkbox':
            showHide(validations[3].validations);
            break;
        case 'date':
            showHide(validations[4].validations);
            break;
    }
}
jQuery(function(){
    // var vali=document.getElementById('type').value;
    var vali=jQuery('#type').val();
        
    selectValidations(vali);
    jQuery('#type').change(function(){
        var val=this.value;
        selectValidations(val);

    });
});


