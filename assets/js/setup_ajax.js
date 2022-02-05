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

  $('.edit_calendar').each((index, editbtn) => {
    $(editbtn).click(async function(){
      emptyPeriodsAndSlots();
      $.ajax({
        type: "POST",
        data: {ajax_calid_editcal:$(editbtn).data('calendar')},
        url: 'controllers/setup_controller.php',
        dataType: "json",
        success: function(data){
          console.log(data);
            if (data.code == 200){
              //console.log(data);
              if (data.total_periods > 0){

                /* Periods Display */
                const periods = data.period_data;
                let periodsHTML = '';
                $("#periods_edit_title").text(`(${data.total_periods})`);
                $("#total_periods_strong").text(`(${data.total_periods})`);
                periods.forEach( (period, index)=>{
                  periodsHTML += getPeriodHTMLText(period.period_index, period.period_date, period.description, period.id, data.cal_id);
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
                  slotsHTML += getSlotHTMLText(slot.slot_index, slot.start_from, slot.end_at, slot.id, data.cal_id);
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


function getPeriodHTMLText(period_index, period_date, period_description, period_id, cal_id){

  const periodHtml = `
  <div class="container border border-secondary p-2 rounded">

  <div class="badge bg-primary">Period Index: ${period_index}</div>
    <form  class="period_editform" action="controllers/setup_controller.php" method="POST"
    onsubmit="displayCalendarEditWait(event)"
    >
        <div class="form-group text-center">
          <label for="add_new_year_edit">Period date: </label>
          <input type="datetime-local" data-old-value="${getLocalDate(period_date)}"
          value="${getLocalDate(period_date)}"
          class="form-control js_edit_input"
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


function getSlotHTMLText(slot_index, start_from, end_at, slot_id, cal_id){

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
