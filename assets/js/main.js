$(document).ready(function() {

  $("main#spapp > section").height($(document).height() - 60);

  var app = $.spapp({pageNotFound : 'error_404'}); // initialize

  // define routes

  app.route({view: 'service', load: 'service.html' });
  app.route({view: 'welcome', load: 'welcome.html' });
  app.route({view: 'register', load: 'register.html' });
  app.route({view: 'companies', load: 'companies.html' });
  // run app
  app.run();

});
