var UserService = {
    init: function(){
        var token = localStorage.getItem("token");
        if (token){
            window.location.replace("home.html");
        }

        $('#loginForm').validate({
            submitHandler: function(form) {
            var entity = Object.fromEntries((new FormData(form)).entries());
            UserService.login(entity);
            }
        });

        $('#registerForm').validate({
            submitHandler: function(form) {
            var entity = Object.fromEntries((new FormData(form)).entries());
            UserService.register(entity);
            }
        });
    },
    
    login: function(){
        var username = $('#username').val();
        var password = $('#password').val();

        var entity = {
          username: username,
          password: password
        };

        $.ajax({
            url: '../api/login',
            type: 'POST',
            data: JSON.stringify(entity),
            contentType: "application/json",
            dataType: "json",
            success: function(result) {
              localStorage.removeItem("token");
              localStorage.setItem("token", result.token);
              window.location.replace("home.html");
            },
            error: function(jqXHR, textStatus, errorThrown) {
              var errorMessage;
              if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                errorMessage = jqXHR.responseJSON.message;
              } else {
                errorMessage = "An error occurred.";
              }
              $('#errorMessage').text(errorMessage);
            }
          });
          
    },

    register: function(entity){
        $.ajax({
            url: '../api/register',
            type: 'POST',
            data: JSON.stringify(entity),
            contentType: "application/json",
            dataType: "json",
            success: function(result) {
              localStorage.removeItem("token");
              localStorage.setItem("token", result.token);
              window.location.replace("login.html");
            },
            error: function(jqXHR, textStatus, errorThrown) {
              var errorMessage;
              if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                errorMessage = jqXHR.responseJSON.message;
              } else {
                errorMessage = "An error occurred.";
              }
              $('#errorMessage').text(errorMessage);
            }
          });
          
    },
  
  
    logout: function(){
      localStorage.clear();
      window.location.replace("login.html");
    }
  }