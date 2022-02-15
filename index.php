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
      width: 50px;
      max-width: 50px;
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
      }

       .period_title_default{

         width: fit-content;
         margin-left: auto !important;
         margin-right: auto !important;
       }
      .period_background_default {
        color: black;
        border: 1px solid white;
        border-radius: 8px;
        width: 95%;
        max-width: 95%;


      }
      .slot_background_default {
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
          font-size: .525rem !important;
          padding:0;
          overflow: hidden;
      }

      .period_background_default {
        padding: 0px !important;
        max-width: 100%;
        overflow: hidden;
                  display: flex !important;
          justify-content: center !important;
          align-items: center importat;
          flex-flow: column nowrap !important;
      }
      .slot_background_default {
        max-width: 80%;
        overflow: hidden;
      }


      }
      @media only screen and (max-width: 580px) {
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
                <div class="btn-group d-flex flex-wrap justify-content-center align-items-center month_small_btns">
                  <!-- change month by number better UX option for old man -->
                  <form class="bg-light p-2 m-1 rounded-circle d-flex justify-content-center align-items-center month_toggle_btn">
                    <span class="p-1 text-center">1</span>
                    <input type="hidden" style="display:none;" name="cal_id">
                    <input type="hidden" style="display:none;" name="year_id">
                    <input type="hidden" style="display:none;" name="month_id">
                  </form>
                  <form class="bg-light p-2 m-1 rounded-circle d-flex justify-content-center align-items-center month_toggle_btn">
                    <span class="p-1 text-center" >2</span>
                    <input type="hidden" style="display:none;" name="cal_id">
                    <input type="hidden" style="display:none;" name="year_id">
                    <input type="hidden" style="display:none;" name="month_id">
                  </form>
                  <form class="bg-light p-2 m-1 rounded-circle d-flex justify-content-center align-items-center month_toggle_btn">
                    <span class="p-1 text-center">4</span>
                    <input type="hidden" style="display:none;" name="cal_id">
                    <input type="hidden" style="display:none;" name="year_id">
                    <input type="hidden" style="display:none;" name="month_id">
                  </form>
                  <form class="bg-light p-2 m-1 rounded-circle d-flex justify-content-center align-items-center month_toggle_btn">
                    <span class="p-1 text-center">4</span>
                    <input type="hidden" style="display:none;" name="cal_id">
                    <input type="hidden" style="display:none;" name="year_id">
                    <input type="hidden" style="display:none;" name="month_id">
                  </form>
                  <form class="bg-light p-2 m-1 rounded-circle d-flex justify-content-center align-items-center month_toggle_btn">
                    <span class="p-1 text-center">5</span>
                    <input type="hidden" style="display:none;" name="cal_id">
                    <input type="hidden" style="display:none;" name="year_id">
                    <input type="hidden" style="display:none;" name="month_id">
                  </form>
                  <form class="bg-light p-2 m-1 rounded-circle d-flex justify-content-center align-items-center month_toggle_btn">
                    <span class="p-1 text-center">6</span>
                    <input type="hidden" style="display:none;" name="cal_id">
                    <input type="hidden" style="display:none;" name="year_id">
                    <input type="hidden" style="display:none;" name="month_id">
                  </form>
                  <form class="bg-light p-2 m-1 rounded-circle d-flex justify-content-center align-items-center month_toggle_btn">
                    <span class="p-1 text-center">7</span>
                    <input type="hidden" style="display:none;" name="cal_id">
                    <input type="hidden" style="display:none;" name="year_id">
                    <input type="hidden" style="display:none;" name="month_id">
                  </form>
                  <form class="bg-light p-2 m-1 rounded-circle d-flex justify-content-center align-items-center month_toggle_btn">
                    <span class="p-1 text-center">8</span>
                    <input type="hidden" style="display:none;" name="cal_id">
                    <input type="hidden" style="display:none;" name="year_id">
                    <input type="hidden" style="display:none;" name="month_id">
                  </form>
                  <form class="bg-light p-2 m-1 rounded-circle d-flex justify-content-center align-items-center month_toggle_btn">
                    <span class="p-1 text-center">9</span>
                    <input type="hidden" style="display:none;" name="cal_id">
                    <input type="hidden" style="display:none;" name="year_id">
                    <input type="hidden" style="display:none;" name="month_id">
                  </form>
                  <form class="bg-light p-2 m-1 rounded-circle d-flex justify-content-center align-items-center month_toggle_btn">
                    <span class="p-1 text-center">10</span>
                    <input type="hidden" style="display:none;" name="cal_id">
                    <input type="hidden" style="display:none;" name="year_id">
                    <input type="hidden" style="display:none;" name="month_id">
                  </form>
                  <form class="bg-light p-2 m-1 rounded-circle d-flex justify-content-center align-items-center month_toggle_btn">
                    <span class="p-1 text-center">11</span>
                    <input type="hidden" style="display:none;" name="cal_id">
                    <input type="hidden" style="display:none;" name="year_id">
                    <input type="hidden" style="display:none;" name="month_id">
                  </form>
                  <form class="bg-light p-2 m-1 rounded-circle d-flex justify-content-center align-items-center month_toggle_btn">
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
                <div class="scroll_to_btns flex-fill border border-light btn  btn-secondary text-white mt-1 mb-1" data-target="week_one">1</div>
                <div class="scroll_to_btns flex-fill border border-light btn  btn-secondary mt-1 mb-1" data-target="week_two">2</div>
                <div class="scroll_to_btns flex-fill border border-light btn  btn-secondary mt-1 mb-1" data-target="week_three">3</div>
                <div class="scroll_to_btns flex-fill border border-light btn  btn-secondary mt-1 mb-1" data-target="week_four">4</div>
                <div class="scroll_to_btns flex-fill border border-light btn  btn-secondary mt-1 mb-1" data-target="week_five">5</div>
              </div>
              <!-- week Titles row end -->
              <!-- week days row start -->
              <div class="d-flex p-2 cal_cards_row week_1" id="week_one">
                <div class="flex-fill border border-light cal_card_cell day_card">
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
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
              </div>
              <!-- week days row end -->
              <!-- week days row start -->
              <div class="d-flex p-2 cal_cards_row week_2" id="week_two">
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
              </div>
              <!-- week days row end -->
              <!-- week days row start -->
              <div class="d-flex p-2 cal_cards_row week_3" id="week_three">
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
              </div>
              <!-- week days row end -->
              <!-- week days row start -->
              <div class="d-flex p-2 cal_cards_row week_4" id="week_four">
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
              </div>
              <!-- week days row end -->
              <!-- week days row start -->
              <div class="d-flex p-2 cal_cards_row week_5" id="week_five">
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
                <div class="flex-fill border border-light cal_card_cell day_card">
                </div>
              </div>
              <!-- week days row end -->
            </div>
            <!-- Calendar display end -->
          </div>
        </div>
      </div>
    </div>
<script>

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
window.addEventListener( 'scroll', ()=>{
  let activeSection = null;
  let activeLink = null;

  if (!allLinks){
    allLinks = document.querySelectorAll(".scroll_to_btns");
  }
  for (let i=0; i<allSections.length; i++){
     let top = allSections[i].getBoundingClientRect().top;
     let active = top > nagtive_height && top < min_elm_hieght;

     if (active==false && i==0){
       console.log(top);
     }
     if (active){
       activeSection = allSections[i];
       break;
     }
  }
  const ActivesSecs = document.querySelectorAll(".active_section");
  ActivesSecs.forEach( (activeSec)=>{
    activeSec.classList.remove("active_section");
  });

  const ActivesLinks = document.querySelectorAll(".active_link");
  ActivesLinks.forEach( (activeLink)=>{
    activeLink.classList.remove("active_link");
  });


  if (activeSection){
    activeSection.classList.add("active_section");
    const getLink = document.querySelector(`div.scroll_to_btns[data-target='${activeSection.id}']`);
    if (getLink){
      activeLink = getLink;
      getLink.classList.add("active_link");
    }
  }

});




    </script>
  </body>
</html>
