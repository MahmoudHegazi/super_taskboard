window.addEventListener('DOMContentLoaded', (event) => {

  /* random color btn */
  const selected_bgs = [];
  function getRandomBtnBG(difculty){
    const avail = ['btn-success', 'btn-primary', 'btn-dark', 'btn-secondary', 'btn-success', 'btn-success', 'btn-warning'];
    let btnBg = avail[Math.floor(Math.random() * avail.length)];
    for (let i=0; i<avail.length*difculty; i++){
      if (selected_bgs.includes(btnBg)){
        btnBg = avail[Math.floor(Math.random() * avail.length)];
      }
    }
    selected_bgs.push(btnBg);
    return btnBg
  }

  function getLocalDate(adate){
    if (adate){
      formattedDate = new Date(adate);
      let day = formattedDate.getDate();
      let month= formattedDate.getMonth() + 1;
      let year=  formattedDate.getFullYear();
      let hours=  formattedDate.getHours();
      let minutes=  formattedDate.getMinutes();
      month = (month < 10) ? "0"+ month : month;
      day = (day < 10) ? "0"+ day : day;
      hours = (hours < 10) ? "0"+ hours : hours;
      minutes = (minutes < 10) ? "0"+ minutes : minutes;
      date = `${year}-${month}-${day}T${hours}:${minutes}`;
      return date
    } else {
      return formattedDate;

      //yyyy-MM-ddThh:mm
    }
  }

  let proprties_index1 = 1;
  function getCSSPerioprtiesFormPeriod(data){
    let cssRulesContainers = '';
    data.forEach( (cssRule, index)=>{
      const pauseOrActive = cssRule.active == '1' ? 0 : 1;
      const currentActionBtn = cssRule.active == '1' ?'<i class="fa fa-pause"></i>' : '<i class="fa fa-play"></i>';
      cssRulesContainers += `
      <div class="slot_styles_container border border-secondary m-1 p-1 hover_cssrule">
         <code>${cssRule.style}</code><br />
         <span class="pl-1 ml-1" title="Remove CSS rule" style="cursor:pointer;">
           <form style="display:inline;" action="controllers/setup_controller.php" method="POST">
             <input name="period_cremove_style_calid"  value="${cssRule.cal_id}" style="display:none;">
             <input name="period_cremove_style_title"  value="${cssRule.title}" style="display:none;">
             <input name="period_cremove_style_classname"  value="${cssRule.classname}" style="display:none;">
             <input class="bg-danger border border-light rounded  text-white" type="submit" value="X" />
           </form>
         </span>
         <span class="pl-1 ml-1" title="Pause/Enable CSS rule" style="cursor:pointer;">
             <form style="display:inline;"  action="controllers/setup_controller.php" method="POST">
             <input name="period_cpause_style_active"  value="${pauseOrActive}" style="display:none;">
             <input name="period_cpause_style_calid"  value="${cssRule.cal_id}" style="display:none;">
             <input name="period_cpause_style_title"  value="${cssRule.title}" style="display:none;">
             <input name="period_cpause_style_classname"  value="${cssRule.classname}" style="display:none;">
             <button class="bg-primary border border-light rounded text-white" type="submit">
               ${currentActionBtn}
             </button>
           </form>
         </span>

         <span class="pl-1 ml-1" title="Edit CSS rule" style="cursor:pointer;">
           <button data-index="${proprties_index1}"
             class="bg-warning border border-light rounded text-white" type="buttom"
             onclick="toggleEditCustomCSS(event)"
             >
             <i class="fa fa-edit"></i>
           </button>
           <form data-visible="false"  action="controllers/setup_controller.php" method="POST" style="display:none;" class="edit_customcss_form" data-index="${proprties_index}">
             <input name="period_cedit_style_calid"  value="${cssRule.cal_id}" style="display:none;">
             <input name="period_cedit_style_title"  value="${cssRule.title}" style="display:none;">
             <input name="period_cedit_style_classname"  value="${cssRule.classname}" style="display:none;">
             <input name="period_cedit_style_style"  value="${cssRule.style}" >
           </form>
         </span>
      </div>
      `;
      proprties_index1 += 1;
    });
    return cssRulesContainers;
}


  let proprties_index = 1;
  // get HTML containers for css rules forms dynamic period and slot
  function getCSSPerioprtiesFormSlot(data){
    let cssRulesContainers = '';
    data.forEach( (cssRule, index)=>{
      const pauseOrActive = cssRule.active == '1' ? 0 : 1;
      const currentActionBtn = cssRule.active == '1' ?'<i class="fa fa-pause"></i>' : '<i class="fa fa-play"></i>';

      cssRulesContainers += `
      <div class="slot_styles_container border border-secondary m-1 p-1 hover_cssrule">
         <code>${cssRule.style}</code><br />
         <span class="pl-1 ml-1" title="Remove CSS rule" style="cursor:pointer;">
           <form style="display:inline;" action="controllers/setup_controller.php" method="POST">
             <input name="slot_cremove_style_calid"  value="${cssRule.cal_id}" style="display:none;">
             <input name="slot_cremove_style_title"  value="${cssRule.title}" style="display:none;">
             <input name="slot_cremove_style_classname"  value="${cssRule.classname}" style="display:none;">
             <input class="bg-danger border border-light rounded  text-white" type="submit" value="X" />
           </form>
         </span>
         <span class="pl-1 ml-1" title="Pause/Enable CSS rule" style="cursor:pointer;">
             <form style="display:inline;"  action="controllers/setup_controller.php" method="POST">
             <input name="slot_cpause_style_active"  value="${pauseOrActive}" style="display:none;">
             <input name="slot_cpause_style_calid"  value="${cssRule.cal_id}" style="display:none;">
             <input name="slot_cpause_style_title"  value="${cssRule.title}" style="display:none;">
             <input name="slot_cpause_style_classname"  value="${cssRule.classname}" style="display:none;">
             <button class="bg-primary border border-light rounded text-white" type="submit">
               ${currentActionBtn}
             </button>
           </form>
         </span>

         <span class="pl-1 ml-1" title="Edit CSS rule" style="cursor:pointer;">
           <button data-index="${proprties_index}"
             class="bg-warning border border-light rounded text-white" type="buttom"
             onclick="toggleEditCustomCSS(event)"
             >
             <i class="fa fa-edit" data-style-target="style_value_${cssRule.cal_id}" class="edit_style_inputc"></i>
           </button>
           <form data-visible="false"  action="controllers/setup_controller.php" method="POST" style="display:none;" class="edit_customcss_form" data-index="${proprties_index}">
             <input name="slot_cedit_style_calid"  value="${cssRule.cal_id}" style="display:none;">
             <input name="slot_cedit_style_title"  value="${cssRule.title}" style="display:none;">
             <input name="slot_cedit_style_classname"  value="${cssRule.classname}" style="display:none;">
             <input data-style-target="style_value_${cssRule.cal_id}" name="slot_cedit_style_style"  value="${cssRule.style}" >
           </form>
         </span>
      </div>
      `;
      proprties_index += 1;
    });
    return cssRulesContainers;
}


  $('.edit_calendar').each((index, editbtn) => {
    $(editbtn).click(async function(){
      emptyPeriodsAndSlots();
      $.ajax({
        type: "POST",
        data: {ajax_calid_editcal:$(editbtn).data('calendar')},
        url: 'controllers/setup_controller.php',
        dataType: "json",
        success: function(data){

            if (data.code == 200){
              //console.log(data);

              //console.log(data);
              if (data.total_periods > 0){

                console.log(data);
                /* Periods Display */
                const periods = data.period_data;
                let periodsHTML = '';
                $("#periods_edit_title").text(`(${data.total_periods})`);
                $("#total_periods_strong").text(`(${data.total_periods})`);
                periods.forEach( (period, index)=>{
                  periodsHTML += getPeriodHTMLText(period.period_index, period.period_date, period.description, period.id, data.cal_id, period.element_id, period.element_class, period.main_styles, period.custom_styles);
                });
                $('#modal_periods_container').html(periodsHTML);

              } else {
                $("#periods_edit_title").text('(0)');
              }



              if (data.total_slots > 0){

                /* Slots Display */
                const slots = data.slot_data;
                let slotsHTML = '';
                $("#slots_edit_title").text(`(${data.total_slots})`);
                $("#total_slots_strong").text(`(${data.total_slots})`);
                slots.forEach( (slot, index)=>{
                  slotsHTML += getSlotHTMLText(slot.slot_index, slot.start_from, slot.end_at, slot.id, data.cal_id, slot.element_id, slot.element_class, slot.main_styles, slot.custom_styles);
                });
                $('#modal_slots_container').html(slotsHTML);

              } else {
                $("#slots_edit_title").text('(0)');
              }
            }
        }
      });
    });
  });

function emptyPeriodsAndSlots(){
  $('#modal_periods_container').html('');
  $('#modal_slots_container').html('');
  $("#periods_edit_title").text('');
  $("#slots_edit_title").text('');
}

function getFormatedTime(period_date){
  const d = new Date(period_date);
  const hours = d.getHours() <= 9 ? "0" + String(d.getHours()) : d.getHours();
  const minutes = d.getMinutes() <= 9 ? ("0" + String(d.getMinutes())) : d.getMinutes();
  const formated_time = `${hours}:${minutes}`;
  return formated_time;
}


function getPeriodHTMLText(period_index, period_date, period_description, period_id, cal_id, element_id, element_class, mainCSS, customCSS){

  const formated_time = getFormatedTime(period_date);

  let periodMainStyles = '';
  let periodCustomStyles = '';
  const mainStyle = '';
  const customStyle = getCSSPerioprtiesFormPeriod(customCSS);

  const periodHtml = `
  <div class="container border border-secondary p-2 rounded">

  <div class="badge bg-primary">Period Index: ${period_index}</div>
    <form  class="period_editform" action="controllers/setup_controller.php" method="POST"
    onsubmit="displayCalendarEditWait(event)"
    >
        <div class="form-group text-center">
          <label for="add_new_year_edit">Period date: </label>
          <input type="time" data-old-value="${formated_time}"
          value="${formated_time}"
          class="form-control js_edit_input js_edit_input_time"
          name="period_date_edit">
        </div>
        <div class="form-group text-center">
          <label for="add_new_year_edit">Period Description: </label>
          <textarea name="period_description_edit"
          maxlength="50"
          class="form-control js_edit_input"
          data-old-value="${period_description}"

          >${period_description}</textarea>
        </div>
        <div class="form-group mt-2">
          <input type="hidden" value="${cal_id}" class="form-control"
          name="period_calid_edit" style="display:none;">
          <input type="hidden" value="${period_index}" class="form-control"
          name="period_index_edit" style="display:none;">

      </div>
      <div class="form-group text-center mt-2 d-grid">
        <input type="submit" class="form-control text-white btn ${getRandomBtnBG(1)}" value='Change Period ${period_index}'>
      </div>
    </form>
    <!-- style start -->
      <div id="accordion_period_${period_index}">
        <div class="card">
          <div class="card-header">
            <a class="btn" data-bs-toggle="collapse" href="#show_period_css${period_index}">
              Show CSS
            </a>
          </div>
          <div id="show_period_css${period_index}" class="collapse" data-bs-parent="#accordion_period_${period_index}">
            <div class="card-body">
              <div class="row">
                <div class="col-sm-12 d-flex justify-content-between bg-secondary mb-3 p-2">
                  <div class="p-2 badge bg-light text-black">Class Selector: ${element_class}</div>
                  <div class="p-2 badge bg-light text-black">ID Selector: ${element_id}</div>
                </div>
              </div>
              <div class="container text-center mt-2">
                 <h3>Custom CSS Rules</h3>
              </div>
              <div class="d-flex justify-content-left bg-light mb-3 p-2">
                ${customStyle}
              </div>
              <div class="d-flex justify-content-center align-items-center">
                <div class="col-sm-10">
                  <form>
                    <div class="form-group d-flex flex-wrap">
                      <label>Rule Title (unique)</label>
                      <input name="slot_customrule_add_title" type="text" class="form-control">
                      <label>CSS code</label>
                      <input name="period_customrule_add_code" type="text" class="form-control">
                      <input name="period_customrule_add_index" value="${period_index}" type="hidden" class="form-control">
                    </div>
                    <div class="form-group mt-2 d-grid">
                      <input type="submit" value="Add Custom Rule" class="btn btm-block btn-primary">
                    </div>
                  </form>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    <!-- style end-->
    <!-- remove period form -->
    <form id="delete_period_form" action="controllers/setup_controller.php" method="POST">

      <div class="form-group">
        <input type="hidden" value="${cal_id}" class="form-control"
        name="period_delete_calid" style="display:none;">
        <input type="hidden" value="${period_index}" class="form-control"
        name="period_delete_index" style="display:none;">

        <div class="form-group delete_periods_s1 text-center mt-2 d-grid" id="delete_period_${period_index}_step1">
          <button type="button" data-level-1="delete_period_${period_index}_step1"
          data-level-2="delete_period_${period_index}_step2"
          onclick="startDeletePeriod(event)"
          class="btn btn-danger btn-block">Delete Period ${period_index}</button>
        </div>

        <div class="container delete_periods_s2 mt-2" id="delete_period_${period_index}_step2" style="display:none;">
          <span class="badge bg-warning text-black">Are You Sure You Want Delete Period With Index: [${period_index}]</span>
          <button class="btn btn-danger" type="submit">Remove Period [${period_index}]</button>
          <button class="btn btn-primary" data-level-1="delete_period_${period_index}_step1"
          data-level-2="delete_period_${period_index}_step2"
          onclick="endDeletePeriod(event)" id="cancel_delete_period">Cancel</button>
        </div>
      </div>
    </form>
  </div>
  </div>
  `;
  return periodHtml;
}



function getSlotHTMLText(slot_index, start_from, end_at, slot_id, cal_id, element_id, element_class, mainCSS, customCSS){


  const customStyle = getCSSPerioprtiesFormSlot(customCSS);




  const slotHtml = `
  <div class="container border border-secondary p-2 rounded">
  <div class="badge bg-primary">Slot Index: ${slot_index}</div>
    <form action="controllers/setup_controller.php" method="POST">
        <div class="form-group text-center">
          <label for="add_new_year_edit">Start From: </label>
          <input type="time" name="start_from_edit" class="form-control" value="${start_from}">
        </div>
        <div class="form-group text-center">
          <label for="add_new_year_edit">end At: </label>
          <input type="time" name="end_at_edit" class="form-control" value="${end_at}">
        </div>
        <div class="form-group mt-2">
          <input type="hidden" value="${cal_id}" class="form-control"
          name="slot_calid_edit" style="display:none;">
          <input type="hidden" value="${slot_index}" class="form-control"
          name="slot_index_edit" style="display:none;">
          <input type="submit" class="form-control text-white btn ${getRandomBtnBG(1)}"
          value='Change Slot ${slot_index}'>
      </div>
    </form>
    <!-- style start -->
      <div id="accordion_slot_${slot_index}">
        <div class="card">
          <div class="card-header">
            <a class="btn" data-bs-toggle="collapse" href="#show_slot_css${slot_index}">
              Show CSS
            </a>
          </div>
          <div id="show_slot_css${slot_index}" class="collapse" data-bs-parent="#accordion_slot_${slot_index}">
            <div class="card-body">
              <div class="row">
                <div class="col-sm-12 d-flex justify-content-between bg-secondary mb-3 p-2">
                  <div class="p-2 badge bg-light text-black">Grouped BY Index Class Selector: ${element_class}</div>
                  <div class="p-2 badge bg-light text-black">First ID Selector: ${element_id}</div>
                </div>
              </div>
            </div>
            <div class="container text-center mt-2">
               <h3>Custom CSS Rules</h3>
            </div>
            <div class="d-flex flex-wrap bg-light mb-3 p-2">
              ${customStyle}
            </div>
            <div class="d-flex justify-content-center align-items-center">
              <div class="col-sm-10">
                <form>
                  <div class="form-group d-flex flex-wrap">
                    <label>Rule Title (unique)</label>
                    <input name="slot_customrule_add_title" type="text" class="form-control">
                    <label>CSS code</label>
                    <input name="slot_customrule_add_code" type="text" class="form-control">
                    <input name="slot_customrule_add_index" value="${slot_index}" type="hidden" class="form-control">
                  </div>
                  <div class="form-group mt-2 d-grid">
                    <input type="submit" value="Add Custom Rule" class="btn btm-block btn-primary">
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    <!-- style end-->
    <!-- remove period form -->
    <form id="delete_slot_form" action="controllers/setup_controller.php" method="POST">

      <div class="form-group">
        <input type="hidden" value="${cal_id}" class="form-control"
        name="slot_delete_calid" style="display:none;">
        <input type="hidden" value="${slot_index}" class="form-control"
        name="slot_delete_index" style="display:none;">

        <div class="form-group delete_slots_s1 text-center mt-2 d-grid delete_slot_level1"
         id="delete_slot_${slot_index}_step1">
          <button type="button" data-level-1="delete_slot_${slot_index}_step1"
          data-level-2="delete_slot_${slot_index}_step2"
          onclick="startDeleteSlot(event)"
          class="btn btn-danger btn-block">Delete Slot ${slot_index}</button>
        </div>

        <div class="container delete_slots_s2 delete_slot_level2 mt-2"
          id="delete_slot_${slot_index}_step2" style="display:none;">
          <span class="badge bg-warning text-black">Are You Sure You Want Delete Slot With Index: [${slot_index}]</span>
          <button class="btn btn-danger" type="submit">Remove Slot [${slot_index}]</button>
          <button class="btn btn-primary" data-level-1="delete_slot_${slot_index}_step1"
          data-level-2="delete_slot_${slot_index}_step2"
          onclick="endDeleteSlot(event)" id="cancel_delete_slot">Cancel</button>
        </div>
      </div>
    </form>


  </div>
  </div>
  `;
  return slotHtml;
}




});


/*
$.ajax({
  type: "POST",
  data: {ajax_calid_editcal:1},
  url: 'controllers/setup_controller.php',
  success: function(data){
      console.log(data);
    }
  });*/
