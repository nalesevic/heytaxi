$(document).ready(function() {

  $("main#spapp > section").height($(document).height() - 60);

  var app = $.spapp({pageNotFound : 'error_404'}); // initialize

  // define routes

  app.route({view: 'ride', load: 'ride.html' });
  app.route({view: 'driver', load: 'driver.html' });
  app.route({view: 'vehicle', load: 'vehicle.html' });
  app.route({
      view: 'logout',
      onCreate: function() {
        window.localStorage.removeItem("token");
        window.location.href = "index.html";
      },
    });

  // run app
  app.run();

});
