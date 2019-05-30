var Drivers = {
  get_drivers : function(){
    RestClient.get('rest/drivers', function(data){
      var html = '';
      for(var i = 0; i < data.length; i++){
        html += '<tr>';
        html += '<td>' + data[i].firstName + '</td>';
        html += '<td>' + data[i].lastName + '</td>';
        html += '<td>' + data[i].vehicle + '</td>';
        var rating = data[i].rating;
        if(rating < 20)
          html += '<td><span class="fa fa-star "></span><span class="far fa-star"></span><span class="far fa-star"></span><span class="far fa-star"></span><span class="far fa-star"></span></td>'
        else if(rating > 19 && rating < 40)
          html += '<td><span class="fa fa-star "></span><span class="fa fa-star "></span><span class="far fa-star"></span><span class="far fa-star"></span><span class="far fa-star"></span></td>'
        else if(rating > 39 && rating < 60)
          html += '<td><span class="fa fa-star "></span><span class="fa fa-star "></span><span class="fa fa-star "></span><span class="far fa-star "></span><span class="far fa-star"></span></td>'
        else if(rating > 59 && rating < 80)
          html += '<td><span class="fa fa-star "></span><span class="fa fa-star "></span><span class="fa fa-star "></span><span class="fa fa-star "></span><span class="far fa-star"></span></td>'
        else if(rating > 79 && rating <= 100)
          html += '<td><span class="fa fa-star "></span><span class="fa fa-star "></span><span class="fa fa-star "></span><span class="fa fa-star "></span><span class="fa fa-star "></span></td>'
        html += '<td><button type="button" class="btn btn-danger" onclick="delete_driver">Delete</button></td> </tr>';
        html += '</tr>';
      }
      $("#drivers_body").html(html);
    });
  },

  // get_drivers_count : function(){
  //   RestClient.get('rest/driver_count', function(data){
  //     $("#count_available").html(data.available);
  //     $("#count_unavailable").html(data.unavailable);
  //   });
  // },

  add_driver : function(data, callback, error_callback){
    RestClient.post('rest/add_driver', data, callback, error_callback);
  },

  delete_driver : function(id){
    RestClient.delete('rest/drivers/'+id, {}, function(){
      toastr.success('Driver has been deleted successfully');
      drivers.get_drivers();
    });
  }
}
