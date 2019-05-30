var Company = {
  // add_company : function(data, callback, error_callback){
  //   RestClient.post('rest/register', data, callback, error_callback);
  // }
  get_companies : function(){
    RestClient.get('rest/companies', function(data){
      var html = '';
      for(var i = 0; i < data.length; i++){
        html += '<tr> <td>' + data[i].companyName + '</td>';
        html += '</tr>';
      }
      $("#companies_body").html(html);
    });
  }
}
