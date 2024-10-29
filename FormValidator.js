/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var formValidator={

    init:function(settings){
        // console.log(settings.rules[0][0]);
        this.form=settings.form;
        this.inputColors=settings.inputColors;
        this.errorColors=settings.errorColors;
        this.rules=settings.rules;
        //console.log(this.rules[0]);
        this.error_display=settings.error_display;
        this.form=document.forms[settings.form];
        this.errors=new Array();
        if(!this.form)
        {
            alert("Error: could not get Form object "+settings.form);
            return;
        }
        if(this.form.onsubmit)
        {
            this.form.old_onsubmit = this.form.onsubmit;
            this.form.onsubmit=null;
        }
        else
        {
            this.form.old_onsubmit = null;
        }
        this.form.onsubmit=function (){           
            formValidator.validate_form();
            
            if(formValidator.errors.length>0){
               
                if(settings.beforeErrorsDisplay){
                    settings.beforeErrorsDisplay();
                }
                formValidator.display_errors();
                return false;
            }
            return true;
        };
       
    //document.form_validator=this;
    }
    //validates form
    ,
    validate_form:function(){
        this.clear_errors();
        //console.log(this.rules,'r');
        for(i=0;i<this.rules.length;i++){
            var error = this.check_rule(this.rules[i]);
            //alert(this.rules[i]);
            if(error){
                this.errors.push(error);
            }
        }
        return this.errors;
    }//end validate
    ,
    clear_errors:function(){
        this.apply_style(this.inputColors, false)
        this.errors.length=0;
    }
    //display errors
    ,
    get_field_label:function(field_name){
        var labels=document.getElementsByTagName('label');                
        if(labels.length>0){
            for(var k=0;k<labels.length;k++){
                try{
                    if(labels[k].htmlFor==field_name){
                        return labels[k];
                    }
                }
                catch(e){}
            }
        }
        return false;
    }
    ,
    apply_style:function(colors,error){
        for(var z=0;z<this.errors.length;z++){
            var  element=document.getElementById(this.errors[z].field);
            if(element){
                if(element.type=='textarea'){
                    var ifr=null;
                    try{
                        ifr=document.getElementById(element.name+'_ifr').contentDocument;
                    }catch(e){
                        try{
                            ifr=document.getElementById(element.name+'_ifr').contentWindow;
                        }
                        catch(er){}
                        
                    }
                    
                    if(ifr)
                    {
                        ifr.body.style.background=colors[0];
                        ifr.body.style.border=" solid 1px "+colors[1];
                    }
                }
                element.style.background=colors[0];
                element.style.border=" solid 1px "+colors[1];
                var label= this.get_field_label(element.name);
                var lbcolor=error?colors[1]:'black';
                if(label)label.style.color=lbcolor;
            }
        }
    }
    ,
    display_errors:function(){
        this.apply_style(this.errorColors,true);
       
        switch(this.error_display){
            case 'alert':
                var errors_str='';
                for(i=0;i<this.errors.length;i++){
                    errors_str=errors_str + this.errors[i].message+ '\n';
                }
                alert(errors_str);
                break;
        }
    }
    ,
    check_rule:function(rule){
        //alert('check rule in progress');
        var valid,value,element;
        //alert('stop');
        try{
            //for single value input
            value=this.form[rule[0]].value?this.form[rule[0]].value:"";
        }
        catch(err){
            //for multiple values input
            element =document.getElementsByName(rule[0]+'[]');
        //console.log(element);
        // alert('gotcha');
                                            
        }
        switch(rule[1]){
            case 'required':
                valid= !isRequired(value);
                break;
            case 'numeric':
                valid= isNumeric(value);
                break;
            case 'email':
                valid= isEmail(value);
                break;
            case 'maxlen':
                
                valid= isLessThanMaxLength(value,rule[3]);
                break;
            case 'minlen':
                //console.log(rule[3]);
                valid= isMoreThanMinLength(value,rule[3]);
                break;
            case 'minval':
                //console.log(rule[3]);
                valid= isMoreThanMinValue(value,rule[3]);
                break;
            case 'maxval':
                //console.log(rule[3]);
                valid= isLessThanMaxValue(value,rule[3]);
                break;
            case 'noselect':
                valid= isValueNotSelected(value,rule[3]);
                break;
            case 'maxselect':
                valid= isLessThanMax(element,rule[3]);
                break;
            case 'minselect':
                valid= isMoreThanMin(element,rule[3]);
                break;
            case 'maxentry':
                valid= isLessEntry(rule[0],rule[3]);
                //console.log('isLessEntry '+rule[3]+'='+valid);
                break;
            case 'minentry':
                valid= isMoreEntry(rule[0],rule[3]);
                //console.log('isMoreEntry '+rule[3]+'='+valid);
                break;
            case 'mustcheck':
                valid=isChecked(rule[0]);
                break;
        }
        //alert(valid);
        if(valid==false){
            var error=new ErrorMessage(rule[0],rule[2]);
            this.errors.push(error);
        }
    }

    
}
/*Validations functions
 -------------------------------------------------------------------------------------*/
// returns true if the string is  empty
function isRequired(str){
    return ((str == null) || (str.length == 0));
}
// returns true if the string is  empty
function isChecked(obj){
    var checkbox=document.getElementsByName(obj)[0];//console.log(document.getElementsByName(obj)[0].checked,'checked');
    return checkbox.checked;
}

// returns true if the string is a valid email
function isEmail(str){
    if(isRequired(str)) return false;
    var re = /^[^\s()<>@,;:\/]+@\w[\w\.-]+\.[a-z]{2,}$/i
    return re.test(str);
}
// returns true if the string only contains characters 0-9 and is not null
function isNumeric(str){
    if(isRequired(str)) return false;
    var re = /[\D]/g
    if (re.test(str)) return false;
    return true;
}
//returns true if the length of a string  less than max length
function  isLessThanMaxLength(str,maxLen){
    if(!isRequired(str))
        return str.length<=maxLen;
}
//returns true if the length of a string  greater than min length
function  isMoreThanMinLength(str,minLen){
    if(!isRequired(str))
        return str.length>=minLen;
}
//returns true if the value  of a decimal is  greater than the maximum value
function isLessThanMaxValue(val,maxVal){
    return val<maxVal;
}
//returns true if the value  of a decimal is  less than the minimum value
function isMoreThanMinValue(val,minVal){
    return val>minVal;
}
//returns true if the value of the  select is not selected
function isValueNotSelected(val,noselect){   
    //if(obj[obj.selectedIndex].value==noselect)
    //   return false;
    return  !(val==noselect);    
}
//return true if the number of selected items is less than max
function isLessThanMax(obj,max){
    
    var count=countSelectedValues(obj);
    return count<=max;
}
function countSelectedValues(obj){
    var count=0;
    
    if( obj.length>0){
        
        for(var i=0;i<obj.length;i++){
            if(obj[i].type=='checkbox'){
                if(obj[i].checked )
                    count=count+1;
            }
        }
    }/*
        else{
            for(var j=0;i<obj.length;j++){

                if( obj.options[j].selected)
                    count=count+1;
            }
        }*/
            
    return count;
}
//return true if the number of selected items is moore than minimum
function isMoreThanMin(obj,min){
    var count=countSelectedValues(obj)
    return count>=min;
   
}

function isLessEntry(element,max){
    var count=countEntries(element);
    return count<=max;
   
}
function isMoreEntry(element,max){
    var count=countEntries(element);
    return count>=max;
}
function countEntries(element){
    var entries=jQuery('div#tagsdiv-'+element+' div#'+element+' div.tagchecklist span');
    return entries.length;
}
//Error Message class
function ErrorMessage(field,message){
    this.field=field;
    this.message=message;

}

//
function isArray(obj) {
    if (obj.constructor.toString().indexOf("Array") == -1)
        return false;
    else
        return true;
}




