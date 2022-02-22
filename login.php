<!DOCTYPE html>
<html lang="en">
   <style>
      body,html{height:100%;width:auto;margin:auto;}
      .main_page{height:100%;}
      .aside_bg{z-index:-1;}
      .sign_up_btn{min-width: 46% !important;}
      .login_btn{min-width: 46% !important;}
      @media only screen and (max-width: 600px) {
        .aside_menu_class {display:none !important;}
        .main_content_class {width:100% !important;}
        .btns_container{display:flex !important; flex-flow:column;}
       /* .sign_up_btn{display:block !mportant;margin-bottom:5px;width:100% !important;} */
        .sign_up_btn{width:100% !important;margin-bottom:10px !important;}
        .login_btn{width:100% !important;}
      }
   </style>
   <head>
      <title>Bootstrap Example</title>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
   </head>
   <body>
      <!-- Control the column width, and how they should appear on different devices -->
      <div class="row main_page">
         <!-- aside start -->
         <div class="col-sm-9 bg-primary text-white aside_menu_class">
           <!-- bg aside -->
           <img class="aside_bg" src="uploads/images/signup_background.jpg" height="100%" >
           <!-- bg aside end -->
         </div>
         <!-- aside end -->
         <!-- sign up form start -->
         <div class="col-sm-3 bg-dark text-white main_content_class">
            <div class="container mt-3">
               <div class="text-center">
                  <h3 class="mt-2 mb-3">Login</h3>
                  <div class="border border-light rounded">
                    <img class="aside_bg" src="uploads/images/signup_background.jpg" height="150" width="100%" >
                  </div>
               </div>
               <form action="/action_page.php" autocomplete="true">
                  <div class="mb-3 mt-3">
                     <label for="email">Email:</label>
                     <input type="email" autocomplete="current-username" class="form-control" id="login_email" placeholder="Enter email" name="login_email" required>
                  </div>
                  <div class="mb-3">
                     <label for="pwd">Password:</label>
                     <input type="password" autocomplete="current-password" class="form-control" id="login_pwd" placeholder="Enter password" name="login_pwd" required>
                  </div>

                  <div class="d-flex justify-content-between align-items-center btns_container flex-wrap">
                    <button type="submit" class="btn btn-outline-primary sign_up_btn">Register</button>
                    <button type="button" class="btn btn-outline-light login_btn">Login</button>
                  </div>
               </form>
            </div>
         </div>
         <!-- signup form end -->
      </div>
   </body>
</html>
