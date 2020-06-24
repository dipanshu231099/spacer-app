var mongoose = require('mongoose');
var Schema = mongoose.Schema;

var entrySchema = new Schema(
    {
        customer_id: {type: String, required: true, maxlength:100},
        customer_name: {type: String, required: true, maxlength: 100},
        collect_time: {type: Date, required: true},
        groceries: {type:Boolean, required:true},
        liquor: {type:Boolean, required:true},
    }
)

//virtual for each entry
entrySchema
.virtual('url')
.get(function () {
  return '/entry/' + this._id;
});

//lets export this shit
module.exports=mongoose.model('entry',entrySchema,'entry')