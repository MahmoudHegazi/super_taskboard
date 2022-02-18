<!DOCTYPE html>
<html lang="en">
  <head>
    <title>My Calendar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
      body, html {
      margin: 0;
      height: auto;
      font-family: georgia;
      font-size: 16px;
      width: 100%;
      }
      div.aside_container{
      box-shadow: 0 4px 8px 0 rgba(200, 200, 200), 0 6px 20px 0 rgba(0, 150, 200, .06);
      background: #e6e6fa75;
      display: block;
      }
      .nav_cont{text-align:justify;}
      div.aside_container .nav_item, div.calendar {
      box-shadow: 0 4px 8px 0 rgba(200, 200, 200, 0.2), 0 6px 20px 0 rgba(200, 200, 200, 1.2);
      cursor: pointer;
      }
      div.aside_container .nav_item:hover {
      box-shadow: 0 4px 8px 0 rgba(200, 200, 200, 0.2), 0 6px 20px 0 rgba(200, 200, 200, 1.2);
      box-shadow: 0 4px 8px 0 rgba(30, 200, 50, 0.2), 0 6px 20px 0 rgba(200, 200, 200, .6);
      cursor: pointer;
      background: azure;
      }
      .menu__item {
      text-decoration: none;
      color: black;
      font-weight: bold;
      font-family: georgia;
      }
      .active_section{
      box-shadow: 0 4px 8px 0 rgb(150 150 150 / 20%), 0 6px 10px 0 rgb(12 130 150) !important;
      }
      .active_link{
      background: royalblue;
      color: white !important;
      box-shadow: 0 4px 8px 0 rgb(200 200 200 / 20%), 0 6px 20px 0 rgb(200 200 200);
      }
      .scroll_to_btns:hover{
      background: white;
      color: black !important;
      box-shadow: 0 4px 8px 0 rgb(200 200 200 / 20%), 0 6px 20px 0 rgb(200 200 200);
      }
      .default_shadow {
      box-shadow: 0 4px 8px 0 rgb(200 200 200 / 20%), 0 6px 20px 0 rgb(200 200 200);
      }
      .bg_darkkhaki{
      background:darkkhaki;
      }
      .bg_aliceblue {
      background: aliceblue;
      }
      .bg_dodgerblue {
      background: dodgerblue;
      }
      .bg_dimgray{
      background: dimgray;
      }
      .bg_indianred{
      background: indianred;
      }
      .bg_cornsilk{
      background: cornsilk;
      color: dimgray !important;
      }
      .bg_azure{
      background: azure;
      }
      .bg_cornflowerblue{
      background: cornflowerblue;
      }
      .shadow1{
      text-shadow: 1px 1px 2px black, 8px 0 25px gray, 3px 0 5px darkblue;
      }
      .text_shadow01 {
      text-shadow: 1px 1px 2px black, 0 0 25px blue, 0 0 5px lightgray;
      }
      .text_shadow02 {
      text-shadow: 1px 1px 2px black, 0 0 25px white, 0 0 5px darkblue;
      }
      .month_arrow {
      cursor: pointer;
      }
      .month_arrow:hover {
      text-shadow: 1px 1px 2px black, 0 0 25px white, 0 0 3px white;
      }
      .month_toggle_btn {
      width: 30px;
      max-width: 30px;
      box-shadow: 0 2px 3px 0 rgb(200 200 200 / 20%), 0 6px 8px 0 rgba(0, 0, 50, 0.5);
      cursor: pointer;
      }
      .month_toggle_btn span{
      text-shadow: 1px 1px 2px lightgray, 0 0 25px blue, 0 0 5px gold;
      color: darkslategrey;
      font-weight: bold;
      }
      .month_toggle_btn:hover {
      box-shadow: 0 2px 3px 0 rgb(200 200 200 / 20%), 0 6px 20px 0 rgb(200 200 200);
      }
      .cal_card_cell {
      max-width:14.2857142857%;
      overflow:auto;
      hight: fit-content;
      max-height: fit-content;
      padding: 8px;
      background: royalblue;
      color: cornsilk;
      }
      .day_card {
      min-height: 250px;
      cursor: default;
      }

      .period_title_default{

         width: fit-content;
         margin-left: auto !important;
         margin-right: auto !important;
         padding: 2px;
       }
      .period_background_default {
        padding: 5px;
        color: black;
        border: 1px solid white;
        border-radius: 8px;
        width: 95%;
        max-width: 95%;
        margin-bottom: 5px;


      }

      .used_slot {
        opacity: 0.8;
        cursor: default !important;
      }
      .slot_background_default {
        cursor: pointer;
        background: lightgray;
        color: black;
        border: 1p solid gold;
        border-radius: 8px;
        width: 95%;
        max-width: 95%;

        display: flex !important;
        justify-content: center !important;
        align-items: center importat;
        flex-flow: row nowrap !important;
        margin-left: auto !important;
        margin-right: auto !important;

      }
       /* active section and link and scroll to */
       .active_section{
        box-shadow: 0 4px 8px 0 rgb(200 200 200 / 20%), 0 6px 20px 0 rgb(10 10 10);
      }
      .active_link{
        background: royalblue;
        color: white !important;
        box-shadow: 0 4px 8px 0 rgb(200 200 200 / 20%), 0 6px 20px 0 rgb(200 200 200);
      }


      .scroll_to_btns:hover{
        background: white;
        color: black !important;
        box-shadow: 0 4px 8px 0 rgb(200 200 200 / 20%), 0 6px 20px 0 rgb(200 200 200);
      }


      /* media query programing */
      @media only screen and (max-width: 690px) {
       /* ipad */
      .full_day {display: none !important;}
      .short_day{display: block !important;}


       .period_title_default {
          max-width:80% !important;
          overflow:hidden;
          font-size: .525rem;
          padding:0;
      }

      .period_background_default {
        padding: 0px !important;
        max-width: 100%;
        overflow: hidden;
        margin-bottom: 0px !important;
                  display: flex !important;
          justify-content: center !important;
          align-items: center importat;
          flex-flow: column nowrap !important;
       padding: 0px;
      }
      .slot_background_default {
        max-width: 80%;
        overflow: hidden;
      }


      }

      @media only screen and (max-width: 580px) {

      .period_title_default {
          max-width:100%;
          overflow:hidden;
          font-size: .925rem;
          padding:8px;
          margin-top: 4px;
          margin-bottom 2px;
      }

      .cal_cards_row{
      display: block !important;
      border-bottom: 5px solid royalblue;
      border-top: 5px solid royalblue;
      border-radius: 10px;
      margin-bottom: 20px;

      box-shadow: 0px 4px 0 4px rgba(50, 50, 50, .4), 0 0 0 1px rgba(0, 200, 150, .8);
      }
      .cal_cards_row:hover{
      box-shadow: 0px 4px 0 4px rgba(50, 50, 50, .4), 0 0 0 2px rgba(130, 255, 130, .8);
      background: rgba(240, 250, 240, .8);
      }
      .cal_days_titles {
      display: none !important;
      }
      .day_card{ max-width: 100% !important;}
      /* \a new line */
      .week_1::before {
      content: 'Week 1';
      white-space: pre;
      color: #2196f3;
      font-size: larger;
      text-shadow: 1px 1px 2px black, 0 0 25px white, 0 0 3px white;
      }
      .week_2::before {
      content: 'Week 2';
      white-space: pre;
      color: #2196f3;
      font-size: larger;
      text-shadow: 1px 1px 2px black, 0 0 25px white, 0 0 3px white;
      }
      .week_3::before {
      content: 'Week 3';
      white-space: pre;
      color: #2196f3;
      font-size: larger;
      text-shadow: 1px 1px 2px black, 0 0 25px white, 0 0 3px white;
      }
      .week_4::before {
      content: 'Week 4';
      white-space: pre;
      color: #2196f3;
      font-size: larger;
      text-shadow: 1px 1px 2px black, 0 0 25px white, 0 0 3px white;
      }
      .week_5::before {
      content: 'Week 5';
      white-space: pre;
      color: #2196f3;
      font-size: larger;
      text-shadow: 1px 1px 2px black, 0 0 25px white, 0 0 3px white;
      }
      }
      @media only screen and (max-width: 300px) {
      div#month_controler_container {
      width: 100%;
      }
      .options_parent {
      margin: 0;
      padding: 0;
      }
      .month_arrow {
      font-size: 1.5rem;
      }
      .month_name {
      font-size: 1rem;
      display: inline-block !important;
      margin-left: auto !important;
      margin-right: auto !important;
      }
      .month_small_btns{
      padding: 0;
      margin: 0;
      }
      .month_small_btns form {
      width: 30px;
      font-size: 1rem !important;
      padding: 1 !important;
      }
      .month_row  {
      display: flex !important;
      justify-content: center !important;
      align-items: center !important;
      flex-flow: column;
      }



      }
      @media only screen and (max-width: 190px) {
      .month_row {
      border: none !important;
      word-break: break-word;
      }
      .month_name {
      font-size: .825rem;
      }
      .month_arrow {
      font-size: 1.5rem;
      }
      }
    </style>
  </head>
  <body>
    <div class="container-fluid p-2 text-white text-center cal_title bg_dimgray">
      <h3 class="display-6 mt-2 mb-3 text-white p-2 bg_indianred default_shadow text_shadow03 border border-secondary">Calendar Title</h3>
      <p class="description_p bg_azure  text-black border border-secondary p-2 default_shadow">Description....</p>
    </div>
    <div class="app_main container-fluid p-2 text-black text-center" style="width: 100% !important;">
      <div class="container-fluid main_cal_display border border-secondary">
        <div class="row">
          <div class="col-sm-12  p-2 d-flex justify-content-center">
            <div class="row container-fluid options_parent  bg_dimgray p-2">
              <!-- month controller start -->
              <div id="month_controler_container" class="col-sm-6">
                <!-- month switcher start -->
                <div  class="container border border-light month_row d-flex flex-wrap align-items-center justify-content-between p-1  text-black mt-2">
                  <i class="display-6 flex-fill fa fa-arrow-circle-left text-white month_arrow"></i>
                  <h3 class="flex-fill month_name text_shadow01 text-white">September</h3>
                  <i class="display-6 flex-fill fa fa-arrow-circle-right text-white month_arrow"></i>
                </div>
                <!-- month switcher end -->
              </div>
              <!-- month controller end -->
              <div class="col-sm-5 d-flex justify-content-center align-items-center rounded p-2">
                <!-- year display start -->
                <form class="flex-fill">
                  <select class="form-control">
                    <option value="2020" selected>2020</option>
                    <option value="2021">2021</option>
                    <option value="2022">2022</option>
                  </select>
                </form>
                <!-- year display end -->
              </div>
              <div class="col-sm-12 bg_dimgray p-2">
                <div class="btn-group btn-sm d-flex flex-wrap justify-content-center align-items-center month_small_btns">
                  <!-- change month by number better UX option for old man -->
                  <form class="bg-light p-1 m-1 rounded-circle d-flex justify-content-center align-items-center month_toggle_btn">
                    <span class="p-1 text-center">1</span>
                    <input type="hidden" style="display:none;" name="cal_id">
                    <input type="hidden" style="display:none;" name="year_id">
                    <input type="hidden" style="display:none;" name="month_id">
                  </form>
                  <form class="bg-light p-1 m-1 rounded-circle d-flex justify-content-center align-items-center month_toggle_btn">
                    <span class="p-1 text-center" >2</span>
                    <input type="hidden" style="display:none;" name="cal_id">
                    <input type="hidden" style="display:none;" name="year_id">
                    <input type="hidden" style="display:none;" name="month_id">
                  </form>
                  <form class="bg-light p-1 m-1 rounded-circle d-flex justify-content-center align-items-center month_toggle_btn">
                    <span class="p-1 text-center">4</span>
                    <input type="hidden" style="display:none;" name="cal_id">
                    <input type="hidden" style="display:none;" name="year_id">
                    <input type="hidden" style="display:none;" name="month_id">
                  </form>
                  <form class="bg-light p-1 m-1 rounded-circle d-flex justify-content-center align-items-center month_toggle_btn">
                    <span class="p-1 text-center">4</span>
                    <input type="hidden" style="display:none;" name="cal_id">
                    <input type="hidden" style="display:none;" name="year_id">
                    <input type="hidden" style="display:none;" name="month_id">
                  </form>
                  <form class="bg-light p-1 m-1 rounded-circle d-flex justify-content-center align-items-center month_toggle_btn">
                    <span class="p-1 text-center">5</span>
                    <input type="hidden" style="display:none;" name="cal_id">
                    <input type="hidden" style="display:none;" name="year_id">
                    <input type="hidden" style="display:none;" name="month_id">
                  </form>
                  <form class="bg-light p-1 m-1 rounded-circle d-flex justify-content-center align-items-center month_toggle_btn">
                    <span class="p-1 text-center">6</span>
                    <input type="hidden" style="display:none;" name="cal_id">
                    <input type="hidden" style="display:none;" name="year_id">
                    <input type="hidden" style="display:none;" name="month_id">
                  </form>
                  <form class="bg-light p-1 m-1 rounded-circle d-flex justify-content-center align-items-center month_toggle_btn">
                    <span class="p-1 text-center">7</span>
                    <input type="hidden" style="display:none;" name="cal_id">
                    <input type="hidden" style="display:none;" name="year_id">
                    <input type="hidden" style="display:none;" name="month_id">
                  </form>
                  <form class="bg-light p-1 m-1 rounded-circle d-flex justify-content-center align-items-center month_toggle_btn">
                    <span class="p-1 text-center">8</span>
                    <input type="hidden" style="display:none;" name="cal_id">
                    <input type="hidden" style="display:none;" name="year_id">
                    <input type="hidden" style="display:none;" name="month_id">
                  </form>
                  <form class="bg-light p-1 m-1 rounded-circle d-flex justify-content-center align-items-center month_toggle_btn">
                    <span class="p-1 text-center">9</span>
                    <input type="hidden" style="display:none;" name="cal_id">
                    <input type="hidden" style="display:none;" name="year_id">
                    <input type="hidden" style="display:none;" name="month_id">
                  </form>
                  <form class="bg-light p-1 m-1 rounded-circle d-flex justify-content-center align-items-center month_toggle_btn">
                    <span class="p-1 text-center">10</span>
                    <input type="hidden" style="display:none;" name="cal_id">
                    <input type="hidden" style="display:none;" name="year_id">
                    <input type="hidden" style="display:none;" name="month_id">
                  </form>
                  <form class="bg-light p-1 m-1 rounded-circle d-flex justify-content-center align-items-center month_toggle_btn">
                    <span class="p-1 text-center">11</span>
                    <input type="hidden" style="display:none;" name="cal_id">
                    <input type="hidden" style="display:none;" name="year_id">
                    <input type="hidden" style="display:none;" name="month_id">
                  </form>
                  <form class="bg-light p-1 m-1 rounded-circle d-flex justify-content-center align-items-center month_toggle_btn">
                    <span class="p-1 text-center">12</span>
                    <input type="hidden" style="display:none;" name="cal_id">
                    <input type="hidden" style="display:none;" name="year_id">
                    <input type="hidden" style="display:none;" name="month_id">
                  </form>
                  <!-- change month buttons end -->
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-12 d-flex justify-content-center align-items-center bg_dimgray">
            <!-- Calendar display start -->
            <div class="calendar border border-dark p-2 mt-3 mb-5 container-fluid bg-white">
              <!-- week Titles row start -->
              <div class="d-flex p-2 cal_days_titles">
                <div class="flex-fill border border-light cal_card_cell">
                  <span class="full_day">Monday</span>
                  <span class="short_day" style="display:none;">Mon</span>
                </div>
                <div class="flex-fill border border-light cal_card_cell">
                  <span class="full_day">Tuesday</span>
                  <span class="short_day" style="display:none;">Tue</span>
                </div>
                <div class="flex-fill border border-light cal_card_cell">
                  <span class="full_day">Tuesday</span>
                  <span class="short_day" style="display:none;">Wed</span>
                </div>
                <div class="flex-fill border border-light cal_card_cell">
                  <span class="full_day">Thursday</span>
                  <span class="short_day" style="display:none;">Thu</span>
                </div>
                <div class="flex-fill border border-light cal_card_cell">
                  <span class="full_day">Friday</span>
                  <span class="short_day" style="display:none;">Fri</span>
                </div>
                <div class="flex-fill border border-light cal_card_cell">
                  <span class="full_day">Saturday</span>
                  <span class="short_day" style="display:none;">Sat</span>
                </div>
                <div class="flex-fill border border-light cal_card_cell">
                  <span class="full_day">Sunday</span>
                  <span class="short_day" style="display:none;">Sun</span>
                </div>
              </div>
              <!-- week Titles row end -->
              <!-- hidden week scroll buttons -->
              <div class="d-flex flex-column align-items-center p-2 " style="position: fixed;left:0;top:0;background: transparent;width:fit-content;max-width:100% !important; font-size:10px;margin:0;padding:0 !important;">
                <div class="scroll_to_btns flex-fill border border-primary btn  btn-secondary text-white  mt-1 mb-1" data-target="week_one">1</div>
                <div class="scroll_to_btns flex-fill border border-primary btn  btn-secondary mt-1 mb-1" data-target="week_two">2</div>
                <div class="scroll_to_btns flex-fill border border-primary btn  btn-secondary mt-1 mb-1" data-target="week_three">3</div>
                <div class="scroll_to_btns flex-fill border border-primary btn  btn-secondary mt-1 mb-1" data-target="week_four">4</div>
                <div class="scroll_to_btns flex-fill border border-primary btn  btn-secondary mt-1 mb-1" data-target="week_five">5</div>

                           <div class="flex-fill border border-primary btn btn-light mt-1 mb-1 aside_add_res" data-bs-toggle="modal" data-bs-target="#mapBookingModal">
                             <i class="fa fa-plus text-primary"></i>
                           </div>


              </div>
              <!-- week Titles row end -->
              <!-- week days row start -->
              <div class="d-flex p-2 cal_cards_row week_1" id="week_one">
                <!-- day start -->
                <div class="flex-fill border border-light cal_card_cell day_card">
                   <!-- day meta -->
                     <h6 class="text-center">Day 1</h6>

                   <!-- all periods start -->
                   <div class="all_periods">


                     <!-- period example start -->
                     <div class="period_background_default">
                        <!-- period title -->
                        <span class="badge bg-success mt-1 period_title_default" >Period 2</span>
                        <!-- all slots start -->
                          <!-- slot start -->
                          <div class="slot_background_default m-1 empty_slot">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary fa fa-calendar-o" style="font-size:1.1em;"></i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1 used_slot">
                           <div class="container">
                             <i class="fa fa-calendar-check-o" style="font-size:1.1em;"></i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                        <!-- all slots end -->
                     </div>
                     <!-- period example end -->



                     <!-- period example start -->
                     <div class="period_background_default">
                        <!-- period title -->
                        <span class="badge bg-success mt-1 period_title_default" >Period 2</span>
                        <!-- all slots start -->
                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                        <!-- all slots end -->
                     </div>
                     <!-- period example end -->



                     <!-- period example start -->
                     <div class="period_background_default">
                        <!-- period title -->
                        <span class="badge bg-success mt-1 period_title_default" >Period 2</span>
                        <!-- all slots start -->
                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                        <!-- all slots end -->
                     </div>
                     <!-- period example end -->
                   </div>
                   <!-- all periods end -->
                </div>
                <!-- day end -->












                <!-- day start -->
                <div class="flex-fill border border-dark cal_card_cell day_card">
                   <h6 class="text-center">Day 2</h6>
                   <!-- all periods start -->
                   <div class="all_periods">


                     <!-- period example start -->
                     <div class="period_background_default">
                        <!-- period title -->
                        <span class="badge bg-success mt-1 period_title_default" >Period 2</span>
                        <!-- all slots start -->
                        <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                        <!-- all slots end -->
                     </div>
                     <!-- period example end -->



                     <!-- period example start -->
                     <div class="period_background_default">
                        <!-- period title -->
                        <span class="badge bg-success mt-1 period_title_default" >Period 2</span>
                        <!-- all slots start -->
                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                        <!-- all slots end -->
                     </div>
                     <!-- period example end -->



                     <!-- period example start -->
                     <div class="period_background_default">
                        <!-- period title -->
                        <span class="badge bg-success mt-1 period_title_default" >Period 2</span>
                        <!-- all slots start -->
                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                        <!-- all slots end -->
                     </div>
                     <!-- period example end -->
                   </div>
                   <!-- all periods end -->
                </div>
                <!-- day end -->
                <!-- day start -->
                <div class="flex-fill border border-light cal_card_cell day_card">
                  <h6 class="text-center">Day 3</h6>
                   <!-- all periods start -->
                   <div class="all_periods">


                     <!-- period example start -->
                     <div class="period_background_default">
                        <!-- period title -->
                        <span class="badge bg-success mt-1 period_title_default" >Period 2</span>
                        <!-- all slots start -->
                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                        <!-- all slots end -->
                     </div>
                     <!-- period example end -->



                     <!-- period example start -->
                     <div class="period_background_default">
                        <!-- period title -->
                        <span class="badge bg-success mt-1 period_title_default" >Period 2</span>
                        <!-- all slots start -->
                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                        <!-- all slots end -->
                     </div>
                     <!-- period example end -->



                     <!-- period example start -->
                     <div class="period_background_default">
                        <!-- period title -->
                        <span class="badge bg-success mt-1 period_title_default" >Period 2</span>
                        <!-- all slots start -->
                               <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                        <!-- all slots end -->
                     </div>
                     <!-- period example end -->
                   </div>
                   <!-- all periods end -->
                </div>
                <!-- day end -->
                <!-- day start -->
                <div class="flex-fill border border-light cal_card_cell day_card">
                  <h6 class="text-center">Day 4</h6>
                   <!-- all periods start -->
                   <div class="all_periods">


                     <!-- period example start -->
                     <div class="period_background_default">
                        <!-- period title -->
                        <span class="badge bg-success mt-1 period_title_default" >Period 2</span>
                        <!-- all slots start -->
                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                        <!-- all slots end -->
                     </div>
                     <!-- period example end -->



                     <!-- period example start -->
                     <div class="period_background_default">
                        <!-- period title -->
                        <span class="badge bg-success mt-1 period_title_default" >Period 2</span>
                        <!-- all slots start -->
                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                        <!-- all slots end -->
                     </div>
                     <!-- period example end -->



                     <!-- period example start -->
                     <div class="period_background_default">
                        <!-- period title -->
                        <span class="badge bg-success mt-1 period_title_default" >Period 2</span>
                        <!-- all slots start -->
                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                        <!-- all slots end -->
                     </div>
                     <!-- period example end -->
                   </div>
                   <!-- all periods end -->
                </div>
                <!-- day end -->
                <!-- day start -->
                <div class="flex-fill border border-light cal_card_cell day_card">
                   <h6 class="text-center">Day 5</h6>
                   <!-- all periods start -->
                   <div class="all_periods">


                     <!-- period example start -->
                     <div class="period_background_default">
                        <!-- period title -->
                        <span class="badge bg-success mt-1 period_title_default" >Period 2</span>
                        <!-- all slots start -->
                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                        <!-- all slots end -->
                     </div>
                     <!-- period example end -->



                     <!-- period example start -->
                     <div class="period_background_default">
                        <!-- period title -->
                        <span class="badge bg-success mt-1 period_title_default" >Period 2</span>
                        <!-- all slots start -->
                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                        <!-- all slots end -->
                     </div>
                     <!-- period example end -->



                     <!-- period example start -->
                     <div class="period_background_default">
                        <!-- period title -->
                        <span class="badge bg-success mt-1 period_title_default" >Period 2</span>
                        <!-- all slots start -->
                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                            <div class="container ">1</div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container ">1</div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container ">1</div>
                          </div>
                          <!-- slot end -->

                        <!-- all slots end -->
                     </div>
                     <!-- period example end -->
                   </div>
                   <!-- all periods end -->
                </div>
                <!-- day end -->
                <!-- day start -->
                <div class="flex-fill border border-light cal_card_cell day_card">
                   <h6 class="text-center">Day 6</h6>
                   <!-- all periods start -->
                   <div class="all_periods">


                     <!-- period example start -->
                     <div class="period_background_default">
                        <!-- period title -->
                        <span class="badge bg-success mt-1 period_title_default" >Period 2</span>
                        <!-- all slots start -->
                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container ">1</div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container ">1</div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container ">1</div>
                          </div>
                          <!-- slot end -->

                        <!-- all slots end -->
                     </div>
                     <!-- period example end -->



                     <!-- period example start -->
                     <div class="period_background_default">
                        <!-- period title -->
                        <span class="badge bg-success mt-1 period_title_default" >Period 2</span>
                        <!-- all slots start -->
                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                        <!-- all slots end -->
                     </div>
                     <!-- period example end -->



                     <!-- period example start -->
                     <div class="period_background_default">
                        <!-- period title -->
                        <span class="badge bg-success mt-1 period_title_default" >Period 2</span>
                        <!-- all slots start -->
                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                            <div class="container ">1</div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container ">1</div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container ">1</div>
                          </div>
                          <!-- slot end -->

                        <!-- all slots end -->
                     </div>
                     <!-- period example end -->
                   </div>
                   <!-- all periods end -->
                </div>
                <!-- day end -->
                <!-- day start -->
                <div class="flex-fill border border-light cal_card_cell day_card">
                   <h6 class="text-center">Day 7</h6>
                   <!-- all periods start -->
                   <div class="all_periods">


                     <!-- period example start -->
                     <div class="period_background_default">
                        <!-- period title -->
                        <span class="badge bg-success mt-1 period_title_default" >Period 2</span>
                        <!-- all slots start -->
                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container ">1</div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container ">1</div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container ">1</div>
                          </div>
                          <!-- slot end -->

                        <!-- all slots end -->
                     </div>
                     <!-- period example end -->



                     <!-- period example start -->
                     <div class="period_background_default">
                        <!-- period title -->
                        <span class="badge bg-success mt-1 period_title_default" >Period 2</span>
                        <!-- all slots start -->
                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                            <div class="container ">1</div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container ">1</div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container ">1</div>
                          </div>
                          <!-- slot end -->

                        <!-- all slots end -->
                     </div>
                     <!-- period example end -->



                     <!-- period example start -->
                     <div class="period_background_default">
                        <!-- period title -->
                        <span class="badge bg-success mt-1 period_title_default" >Period 2</span>
                        <!-- all slots start -->
                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                          <!-- slot start -->
                          <div class="slot_background_default m-1">
                           <div class="container" data-bs-toggle="modal" data-bs-target="#bookingModal">
                             <i class="fa text-primary">&#xf271;</i>
                           </div>
                          </div>
                          <!-- slot end -->

                        <!-- all slots end -->
                     </div>
                     <!-- period example end -->
                   </div>
                   <!-- all periods end -->
                </div>
                <!-- day end -->
              </div>


            <!-- Calendar display end -->
          </div>
        </div>
      </div>
    </div>


<!-- Booking modal start -->
<div class="modal fade" id="bookingModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header ">
        <h5 class="modal-title ">Booking (02/16/2022) <i class="fa fa-calendar"></i></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
          <h6>Period Notes:</h6>
          <p>you must come with laptop.</p>
          <form action="/action_page.php">
            <div class="mb-3 mt-3">
              <label for="comment">Comments:</label>
              <textarea class="form-control" rows="3" id="comment" name="text"></textarea>
            </div>
            <p class="alert alert-info text-center">Please click 'Confirm' to confirm your booking start at  <br /> <span id="start_from_slot p-1" class="bg-primary text-white badge">12:00PM</span> and end at <span class="bg-success text-white badge" id="badge end_at_slot">02:00PM</span></p>
            <button type="submit" class="btn btn-primary">Confirm Booking </button>
          </form>

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
<!-- Booking modal end -->



<!-- Booking modal start -->
<div class="modal fade" id="mapBookingModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header ">
        <h5 class="modal-title "> Create new Booking <i class="fa fa-calendar-o"></i></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
          <form action="controllers/index_controller.php">
            <div class="form-group">
              <label>Pick a Date</label>
              <input class="form-control" name="map_reservation_date" id="map_reservation_date" type="date"  min="2022-01-01" max="2024-01-01">
            </div>
            <div class="mb-3 mt-3">
              <label for="comment">Booking Notes:</label>
              <textarea class="form-control" rows="3" id="comment" name="text" placeholder="Booking Notes.."></textarea>
            </div>
            <p class="alert alert-info text-center">Please click 'Confirm' to confirm your booking start at  <br /> <span id="start_from_slot p-1" class="bg-primary text-white badge">12:00PM</span> and end at <span class="bg-success text-white badge" id="badge end_at_slot">02:00PM</span></p>
            <button type="submit" class="btn btn-primary">Confirm Booking </button>
          </form>

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
<!-- Booking modal end -->


<!-- Cancel reservation modal start -->
<div class="modal" id="myModal">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title">Cancel the reservation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="alert alert-danger text-black text-center">Are you sure you want to cancel the reservation</div>

        <div class="d-grid">
          <button type="submit" class="btn btn-block btn-dark text-white" data-bs-dismiss="modal">
           Cancel Reservation</button>
        </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Back</button>
      </div>

    </div>
  </div>
</div>
<!-- Cancel reservation modal end -->

<!-- sound effects -->
<audio id="open_modal_sound">
  <source src="https://sndup.net/rp5j/d" type="audio/wav">
</audio>

<audio id="unable_open_modal">
  <source src="https://sndup.net/sbs9/d" type="audio/wav">
</audio>

<!-- HTML5 sounds -->

<script>


const playSound = (selector)=>{
  //open_modal_sound unable_open_modal
  const selectedSound = document.querySelector(`${selector}`);
  selectedSound.play();
  selectedSound.volume = 0.1;

}


      const allScrollBtns = document.querySelectorAll(".scroll_to_btns");

      allScrollBtns.forEach( (scrollBtn)=>{
        scrollBtn.addEventListener( "click", (event)=>{
          const toId = scrollBtn.getAttribute("data-target");
          const scrollToWeek = document.getElementById(toId);
          if (scrollToWeek){
            scrollToWeek.scrollIntoView({behavior: "smooth", block: "start", inline: "nearest"});
            return true;
          } else {
            return false;
          }
        })

      });

      // active link and section



const sections = [];

const allSections = document.querySelectorAll(".cal_cards_row");

let allLinks = document.querySelectorAll(".scroll_to_btns");

allSections.forEach( (currentSection)=>{
  sections.push(currentSection.clientHeight);
});

const min_elm_hieght = Math.min(...sections);
const nagtive_height = -1 * Number(min_elm_hieght);

const backToDefault = ()=>{
  const ActivesSecs = document.querySelectorAll(".active_section");
  ActivesSecs.forEach( (activeSec)=>{
    activeSec.classList.remove("active_section");
  });

  const ActivesLinks = document.querySelectorAll(".active_link");
  ActivesLinks.forEach( (activeLink)=>{
    activeLink.classList.remove("active_link");
  });

};
window.addEventListener( 'scroll', ()=>{
  let activeSection = null;
  let activeLink = null;

  if (!allLinks){
    allLinks = document.querySelectorAll(".scroll_to_btns");
  }

  if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
    // if small screen it will be hard to reach last section so manual handle it
    activeSection = allSections[allSections.length-1];
  } else {
    for (let i=0; i<allSections.length; i++){
       let top = allSections[i].getBoundingClientRect().top;
       let active = top > (nagtive_height + 50) && top < min_elm_hieght;

       if (active==false && i==0){
         console.log(top);
       }
       if (active){
         activeSection = allSections[i];
         break;
       }
    }
  }
  backToDefault();
  if (activeSection){
    activeSection.classList.add("active_section");
    const getLink = document.querySelector(`div.scroll_to_btns[data-target='${activeSection.id}']`);
    if (getLink){
      activeLink = getLink;
      getLink.classList.add("active_link");
    }
  }

});


/* sound effects not owned by current user */
const allUsedSlots = document.querySelectorAll(".used_slot");
allUsedSlots.forEach( (slot)=>{
  slot.addEventListener("click", ()=>{playSound("#unable_open_modal")});

});

const allEmptySlots = document.querySelectorAll(".empty_slot");
allEmptySlots.forEach( (slot)=>{
  slot.addEventListener("click", ()=>{playSound("#open_modal_sound")});
});

const addResAisde = document.querySelector(".aside_add_res");
addResAisde.addEventListener("click", ()=>{playSound("#open_modal_sound")});


    </script>
  </body>
</html>
