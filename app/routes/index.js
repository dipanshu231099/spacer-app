var express = require('express');
var router = express.Router();
const date = require('date-and-time');
const validator = require('express-validator');
const dateformat = "MMM DD YYYY HH:mm:ss"
var entry = require('../models/entry')

function isAlphabeticOnly(string){
  return /^[a-z]+$/i.test(string);
}

function availableTimes(){
  var presentTime = new Date();
  var month = presentTime.getMonth();
  var day = presentTime.getDate();
  var year = presentTime.getFullYear();
  var hour = presentTime.getHours();
  var minutes = presentTime.getMinutes();
  var possibles=[]
  for(var i=hour+1;i<=hour+24;i++){
    if((i%24)>19 || (i%24<10)){
      continue;
    }
    for(var j=0;j<=40;j+=20){
      var to_append = new Date(year,month,day+(i>=24?1:0),i%24,j,0,0);
      entry.find({'collect_time':to_append}).count(function(err,result){
        if(err){res.send(err)}
        if(result<=12){console.log(true);possibles.push(date.format(to_append, dateformat));}
      });
      
      // entry.count({'collect_time':to_append}, function(err,result){
      //   if(err){res.send(err);return;}
      //   if(result<=12){
      //     possibles.push(date.format(to_append, dateformat));
      //   }
      // })
    }
  }
  return possibles;
}


/* GET home page. */
router.get('/', function(req, res, next) {
  res.render('index', {
    title: 'Express',
    availableTimes: availableTimes(),
    errors:{name:"", email:"", contact:""}
  });
});

//Post request for the form
router.post('/create_timing',function(req,res,next){
  var name = req.body.name.trim();
  var email = req.body.email.trim();
  var contact = req.body.contact.trim();
  var timestamp = new Date(req.body.timestamp);
  var groceries = req.body.groceries;
  var liquor = req.body.liquor;

  var existing_req_error="";
  var name_error="";
  var email_error="";
  var contact_error="";

  if(!isAlphabeticOnly(name)){
    name_error="name must be only alphabets and no whitespaces";
  }
  if(contact.length!=10){
    contact_error='contact number must be 10 digit long only';
  }


  if(name_error===email_error && email_error===contact_error){
    console.log(name);
    //check if email already exists
    entry.findOne({'customer_id':req.body.email})
    .exec(function(err,found){
      if(err){return next(err);}
      if(found) {existing_req_error="entry for this email id already exists at time: "+found.timestamp;}
      
    })
  }

  console.log(name_error+email_error+contact_error)
  res.render('index', {
    title: 'Express',
    availableTimes: availableTimes(),
    errors:{name:name_error, email:email_error, contact:contact_error, red_email:existing_req_error}
  });

})

module.exports = router;
