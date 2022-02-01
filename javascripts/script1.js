console.log('connected')
var user_name = document.getElementById('user_name')
var dropdown_groceries = document.getElementById('dropdown_groceries')
var dropdown_groceriesliquor = document.getElementById('dropdown_groceriesliquor')
var dropdown_liquor = document.getElementById('dropdown_liquor')
var contact = document.getElementById('contact_number')
var button = document.getElementById('submit')
var grocery = document.getElementById('groceries')
var liquor = document.getElementById('liquor')
var groceriesliquor = document.getElementById('groceriesliquor')
var liquor_card = document.getElementById('liquor_card')
var grocery_card = document.getElementById('grocery_card')
var liq_card = document.getElementById('liq_card')
var gro_card = document.getElementById('gro_card')
var card_name = document.getElementById('card_name')







document.addEventListener('keyup', logKey_submit);
document.addEventListener('click', logKey_submit);



grocery.addEventListener('keyup',logKey_dropdown_groceries);
grocery.addEventListener('click',logKey_dropdown_groceries);
grocery.addEventListener('keyup',logKey_gcard);
grocery.addEventListener('click',logKey_gcard);


liquor.addEventListener('keyup',logKey_dropdown_liquor);
liquor.addEventListener('click',logKey_dropdown_liquor);
liquor.addEventListener('keyup',logKey_lcard);
liquor.addEventListener('click',logKey_lcard);


groceriesliquor.addEventListener('keyup',logKey_dropdown_groceriesliquor);
groceriesliquor.addEventListener('click',logKey_dropdown_groceriesliquor);
groceriesliquor.addEventListener('keyup',logKey_glcard);
groceriesliquor.addEventListener('click',logKey_glcard);

grocery_card.addEventListener('keyup',logKey_gcard);
grocery_card.addEventListener('click',logKey_gcard);

liquor_card.addEventListener('keyup',logKey_lcard);
liquor_card.addEventListener('click',logKey_lcard);


gro_card.addEventListener('keyup',logKey_glcard);
gro_card.addEventListener('click',logKey_glcard);
liq_card.addEventListener('keyup',logKey_glcard);
liq_card.addEventListener('click',logKey_glcard);




function checkAlpha(str){
    return (/^[a-zA-Z ]+$/i.test(str));
}

function checkMobile(str){
    return ((/^[0-9]+$/.test(str)) && (str.length===10));
}

function ValidateEmail(str) 
{
    if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(str)){
        return (true)
    }
    return (false)
}

function logKey_name(e){
    if(checkAlpha(user_name.value)){
        user_name.classList.remove('is-invalid')
        user_name.classList.add('is-valid')
    }
    else {
        user_name.classList.remove('is-valid')
        user_name.classList.add('is-invalid')
    }
}

function logKey_card_name(e){
    if(checkAlpha(card_name.value)){
        card_name.classList.remove('is-invalid')
        card_name.classList.add('is-valid')
    }
    else {
        card_name.classList.remove('is-valid')
        card_name.classList.add('is-invalid')
    }
}

function gcard(e){
    if(grocery_card.value.length==17){
        grocery_card.classList.remove('is-invalid')
        grocery_card.classList.add('is-valid')
    }
    else{
        grocery_card.classList.remove('is-valid')
        grocery_card.classList.add('is-invalid')
    }
}

function lcard(e){
    if(liquor_card.value.length==17){
        liquor_card.classList.remove('is-invalid')
        liquor_card.classList.add('is-valid')
    }
    else{
        liquor_card.classList.remove('is-valid')
        liquor_card.classList.add('is-invalid')
    }
}

function glcard(e){
    if(gro_card.value.length==17 && liq_card.value.length==17){
        gro_card.classList.remove('is-invalid')
        gro_card.classList.add('is-valid')
        liq_card.classList.remove('is-invalid')
        liq_card.classList.add('is-valid')
    }
    else{
        gro_card.classList.remove('is-valid')
        gro_card.classList.add('is-invalid')
        liq_card.classList.remove('is-valid')
        liq_card.classList.add('is-invalid')
    }
}

function logKey_number(e){
    if(checkMobile(contact.value)){
        contact.classList.remove('is-invalid')
        contact.classList.add('is-valid')
    }
    else {
        contact.classList.remove('is-valid')
        contact.classList.add('is-invalid')
    }
}

function logKey_email(e){
    if(ValidateEmail(email.value)){
        email.classList.remove('is-invalid')
        email.classList.add('is-valid')
    }
    else {
        email.classList.remove('is-valid')
        email.classList.add('is-invalid')
    }
}

function logKey_gcard(e){
    if(grocery.checked){
        if(grocery_card.value.length==19){
            grocery_card.classList.remove('is-invalid')
            grocery_card.classList.add('is-valid')
        }
        else {
            grocery_card.classList.remove('is-valid')
            grocery_card.classList.add('is-invalid')
        }
    }
    else {
        grocery_card.classList.remove('is-invalid')
        grocery_card.classList.remove('is-valid')
    }
}

function logKey_lcard(e){
    if(liquor.checked){
        if(liquor_card.value.length==19){
            liquor_card.classList.remove('is-invalid')
            liquor_card.classList.add('is-valid')
        }
        else {
            liquor_card.classList.remove('is-valid')
            liquor_card.classList.add('is-invalid')
        }
    }
    else {
        liquor_card.classList.remove('is-invalid')
        liquor_card.classList.remove('is-valid')
    }
}

function logKey_glcard(e){
    if(groceriesliquor.checked){
        if(gro_card.value.length==19 && liq_card.value.length==19){
            gro_card.classList.remove('is-invalid')
            gro_card.classList.add('is-valid')
            liq_card.classList.remove('is-invalid')
            liq_card.classList.add('is-valid')
        }
        else {
            gro_card.classList.remove('is-invalid')
            gro_card.classList.add('is-valid')
            liq_card.classList.remove('is-valid')
            liq_card.classList.add('is-invalid')
            
        }
    }
    else {
        gro_card.classList.remove('is-invalid')
        gro_card.classList.remove('is-valid')
        liq_card.classList.remove('is-invalid')
        liq_card.classList.add('is-valid')
    }
}


function logKey_submit(e){
    if( (liquor.checked || grocery.checked || groceriesliquor.checked)
     && !(liquor.checked && liquor_card.value.length!=19)
     && !(grocery.checked && grocery_card.value.length!=19)
     && !(groceriesliquor.checked && gro_card.value.length!=19 && liq_card.value.length!=19)
     ){
        button.disabled=false;
    }
    else {
        button.disabled=true;
    }
}

function logKey_dropdown_groceries(e){
    if(grocery.checked){

        grocery_card.disabled=false;
        liquor.disabled=true;
        groceriesliquor.disabled=true;
    }
    else{

        grocery_card.disabled=true;
        liquor.disabled=false;
        groceriesliquor.disabled=false;
    }
}

function logKey_dropdown_liquor(e){
    if(liquor.checked){
        
        liquor_card.disabled=false;
        grocery.disabled=true;
        groceriesliquor.disabled=true;
    }
    else {
        liquor_card.disabled=true;
        grocery.disabled=false;
        groceriesliquor.disabled=false;
        
    }
}

function logKey_dropdown_groceriesliquor(e){
    if(groceriesliquor.checked){

        gro_card.disabled=false;
        liq_card.disabled=false;
        liquor.disabled=true;
        grocery.disabled=true;
    }
    else{

        gro_card.disabled=true;
        liq_card.disabled=true;
        liquor.disabled=false;
        grocery.disabled=false;
    }
}

