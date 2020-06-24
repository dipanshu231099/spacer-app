var express = require('express');
var router = express.Router();
const date = require('date-and-time');
const validator = require('express-validator');
const dateformat = "MMM DD YYYY hh:mm A"

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
      possibles.push(date.format(to_append, dateformat));
    }
  }
  return possibles;
}


/* GET home page. */
router.get('/', function(req, res, next) {
  res.render('index', {
    title: 'Express',
    availableTimes: availableTimes(),
    valid: {name:"is-valid",email:"is-valid",contact:"is-valid"},
    errors:{name:"", email:"", contact:""}
  });
});

//Post request for the form
router.get('/create_timing',[
  //validation and sanitisation
  validator.body('name','dipanshu').trim().isLength({min:1}),
  validator.sanitizeBody('name').escape(),

  (req,res,next) => {
    console.log(req.params.name)
    const errors = validator.validationResult(req);
    if(!errors.isEmpty()){
      console.log(errors.array());
      return;
    }
  }

])

module.exports = router;
