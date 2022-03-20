const displayJQAjaxMessage = (error_msg, type='danger')=>{
  const mapErrorCont = document.querySelector("#asp_error_cont");
  mapErrorCont.innerHTML = `
  <div class="alert alert-${type} alert-dismissible fade show">
    <p>${error_msg}</p>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>`;
}

function canceleEdit(event){
  const submitASPBTNId = event.currentTarget.getAttribute("data-target-id");
  const currentSubmit = document.getElementById(submitASPBTNId);
  if (!currentSubmit){return false;}
  const currentStartId = currentSubmit.getAttribute("data-asp-slot-start-id");
  const currentEndId = currentSubmit.getAttribute("data-asp-slot-end-id");
  const currentStartElm = document.getElementById(currentStartId);
  const currentEndElm = document.getElementById(currentEndId);
  if (!currentStartElm || !currentEndElm){return false;}
  currentStartElm.style.display = "none";
  currentEndElm.style.display = "none";
  currentSubmit.style.display = "none";
  event.target.style.display = "none";
}
function ajaxJQueryRequest(event){
  const asp_pindex = $(event.currentTarget).data('asp-period-index');
  const asp_sindex = $(event.currentTarget).data('asp-slot-index');
  const asp_sstartID = $(event.currentTarget).data('asp-slot-start-id');
  const asp_sendID = $(event.currentTarget).data('asp-slot-end-id');
  const asp_cal_id = $(event.currentTarget).data('asp-cal-id');



  const asp_sstart = document.getElementById(asp_sstartID);
  const asp_send = document.getElementById(asp_sendID);
  if (!asp_pindex || !asp_sindex || !asp_sstart || !asp_send){
    displayJQAjaxMessage("Missing Period and SLot", 'danger');
    return false;
  }
  let real_start = asp_sstart.value;
  let real_end = asp_send.value;
  if (asp_sstart.hasAttribute("disabled")){
    real_start = '';
  } else {
    real_start = asp_sstart.value;
  }
  if (asp_send.hasAttribute("disabled")){
    real_end = '';
  } else {
    real_end = asp_send.value;
  }

  $.ajax({
  url: 'controllers/setup_controller.php',
  method: 'post',
  data: {
      asp_start: real_start,
      asp_end: real_end,
      asp_period_index: asp_pindex,
      asp_slot_index: asp_sindex,
      asp_cal_id: asp_cal_id
  },
  success: function(data){

       const jsDataObj = $.parseJSON(data);
       if (!jsDataObj || jsDataObj.code != 200){
         const errorMsg = jsDataObj.message ? jsDataObj.message : 'Unkown JS error';
         displayJQAjaxMessage(jsDataObj.message, 'danger');
       }

       if (real_start != ''){
         const asp_modifer_start = document.querySelector(`[data-modifer='${asp_sstartID}']`);
         if (asp_modifer_start){
           asp_modifer_start.innerText = jsDataObj.start;
         }
       }
       if (real_end != ''){
         const asp_end_start = document.querySelector(`[data-modifer='${asp_sendID}']`);
         if (asp_end_start){
           asp_end_start.innerText = jsDataObj.end;
         }
       }
      displayJQAjaxMessage(`Updated Slot (${asp_pindex},${asp_sindex}) Data`, 'success');
  }
});
}

const noneIndexToggle = (event)=>{
  // New toggle without index depend on methods index
  const cITEID = event.target.getAttribute("data-asp-target");
  const staticValueElm = document.querySelector(`div[data-modifer='${cITEID}']`);

  const dataASPSubmitId = event.target.getAttribute("data-asp-submit");
  const dataASPSubmitBTN = document.getElementById(dataASPSubmitId);
  const dataASPCancelBTN = document.querySelector(`[data-target-id='${dataASPSubmitId}']`);
  if (!dataASPSubmitBTN || !dataASPCancelBTN){return false;}
  dataASPSubmitBTN.style.display = "";
  dataASPCancelBTN.style.display = "";

  const currentInput = document.getElementById(cITEID);
  if (currentInput && staticValueElm){
    if (currentInput.hasAttribute("disabled")){
      currentInput.removeAttribute("disabled");
      currentInput.style.display = "block";
      // better UX when open element get the value of php first and also when edit update this static value
      // so if i empty input and close it next time it not empty
      currentInput.value = staticValueElm.innerText;
    } else {
      currentInput.setAttribute("disabled","disabled");
      currentInput.style.display = "none";
    }
  }
}

async function setupPostData (url = '', data = {}) {

  const response = await fetch(url, {
    method: 'POST',
    credentials: 'same-origin',
    headers: {
      'Content-Type': 'application/json',
    },
    // Body data type must match "Content-Type" header
    body: JSON.stringify(data),
  });
  try {
    const newData = await response.json();
    //console.log('Data Recived: ', newData)
    return newData;
  } catch (error) {
    console.log("error", error);
  }
};
/* Helpers Functions */
function displayCalendarEditWait(event) {
    event.preventDefault;
    const calWaitBody = document.querySelector("#edit_cal_body");
    const calWaitContainer = document.querySelector("#action_loading_container");
    calWaitBody.style.visibility = "hidden";
    calWaitContainer.style.display = "block";
    event.target.submit();
    return false;
}
const addedYearsForm = document.querySelector("#added_years_form");
addedYearsForm.addEventListener("submit", displayCalendarEditWait);


/* Mange Periods In form start */

const period_input = document.querySelector("#period_per_day");
const periodContainer = document.querySelector("#periods_container");



period_input.addEventListener("input", mange_period_inputs);

function create_period_inputs(count, start_index = 1, type='add') {
    if (count < 1) {
        return false;
    }
    let index = start_index;
    let html_inputs = '';

    let period_date = type == 'add' ? 'time' : 'time';
    for (let i = 0; i < count; i++) {
        html_inputs += `
  <div class="form-group border border-dark mt-1 p-2 bg-light text-black">
     <h5 class="text-center badge bg-success text-white">Period ${index}</h5>
     <div class="row">
        <div class="col-sm-6">
           <label for="period_date_${index}">Period DateTime: </label>
           <input type="hidden" name="slot_add_index_${index}" value="${index}">
           <input required name="period_date_${index}" data-index="${index}" type="${period_date}" class="form-control period-date period_input" />
        </div>
        <div class="col-sm-6">
           <label for="period_description_${index}">Period Description</label>
           <input maxlength="50" size="50" name="period_description_${index}" type="text" class="form-control period-description period_input" data-index="${index}" placeholder="Enter Period Description" />
        </div>
     </div>
     <!-- styles -->
     <div class="row">
        <div class="col-sm-3">
           <label for="period_color_${index}">Font Color</label>
           <input required name="period_color_${index}" id="period_color_${index}"
              data-index="${index}" type="color" value="#ffffff" class="form-control period_style_color1" />
        </div>
        <div class="col-sm-3">
           <label for="period_background_${index}">Background color</label>
           <input required name="period_background_${index}" id="period_background_${index}"
              data-index="${index}" type="color" value="#3190d8" class="form-control period_style_bgcolor1" />
        </div>
        <div class="col-sm-3">
           <label for="period_fontfamily_${index}">Font Family</label>
           <select name="period_fontfamily_${index}" data-index="${index}" class="form-control period_font_family1">
              <option value="">Default</option>
              <option style="font-family:Georgia;" value="font-family:Georgia;" checked>Georgia</option>
              <option style="font-family:Palatino Linotype;" value="font-family:Palatino Linotype;">Palatino Linotype</option>
              <option style="font-family:Book Antiqua;" value="font-family:Book Antiqua;">Book Antiqua</option>
              <option style="font-family:Times New Roman;" value="font-family:Times New Roman;">Times New Roman</option>
              <option style="font-family:Arial;" value="font-family:Arial;">Arial</option>
              <option style="font-family:Helvetica;" value="font-family:Helvetica;">Helvetica</option>
              <option style="font-family:Impact;" value="font-family:Impact;">Impact</option>
              <option style="font-family:Lucida Sans Unicode;" value="font-family:Lucida Sans Unicode;">Lucida Sans Unicode</option>
              <option style="font-family:Tahoma;" value="font-family:Tahoma;">Tahoma</option>
              <option style="font-family:Verdana;" value="font-family:Verdana;">Verdana</option>
              <option style="font-family:Courier New;" value="font-family:Courier New;">Courier New</option>
              <option style="font-family:Lucida Console;" value="font-family:Lucida Console;">Lucida Console</option>
              <option style="font-family:initial;" value="font-family:initial;">initial</option>
           </select>
        </div>
        <div class="col-sm-3">
           <label for="period_fontsize_${index}">Font Size</label>
           <select id="period_fontsize_${index}" name="period_fontsize_${index}" data-index="${index}"
            class="form-control period_fontsize1">
              <option value="" checked>Default</option>
              <option style="font-size: 8px;" value="font-size: 8px;">8px</option>
              <option style="font-size: 10px;" value="font-size: 10px;">10px</option>
              <option style="font-size: 12px;" value="font-size: 12px;">12px</option>
              <option style="font-size: 14px;" value="font-size: 14px;">14px</option>
              <option style="font-size: 16px;" value="font-size: 16px;">16px</option>
              <option style="font-size: 18px;" value="font-size: 18px;">18px</option>
              <option style="font-size: 1rem;" value="font-size: 1rem;">1 rem</option>
              <option style="font-size: 1rem;" value="font-size: 1rem;">1.5 rem</option>
              <option style="font-size: 0.825em;" value="font-size: 0.825em;">0.825 em</option>
              <option style="font-size: 0.925em;" value="font-size: 0.925em;">0.925 em</option>
              <option style="font-size: 1em;" value="font-size: 1em;">1 em</option>
              <option style="font-size: 1.5em;" value="font-size: 1.5em;">1.5 em</option>
              <option style="font-size: 2em;" value="font-size: 2em;">2 em</option>
              <option style="font-size: 1rem;" value="font-size: 1rem;">2 rem</option>
           </select>
        </div>
     </div>
     <div class="row">
        <div class="col-sm-3">
           <label for="period_border_color_${index}">Border Color</label>
           <select id="period_border_color_${index}" name="period_border_color_${index}"
            data-index="${index}" class="form-control period_border_part1_1 period_border1">
              <option value="" checked>No Border</option>
              <option value="black;">Black</option>
              <option value="white;">White</option>
              <option value="red;">Red</option>
              <option value="green;">Green</option>
              <option value="gold;">Gold</option>
              <option value="blue;">Blue</option>
           </select>
        </div>
        <div class="col-sm-3">
           <label for="period_border_size_${index}">Border Size</label>
           <select id="period_border_size_${index}" name="period_border_size_${index}"
           data-index="${index}" class="form-control period_border_part2_1 period_border1">
              <option value="" checked>No Border</option>
              <option value="border:1px">1px</option>
              <option value="border:1px">2px</option>
              <option value="border:1px">3px</option>
              <option value="border:1px">4px</option>
              <option value="border:1px">5px</option>
           </select>
        </div>
        <div class="col-sm-3">
           <label for="period_border_type_${index}">Border Type</label>
           <select id="period_border_type_${index}" name="period_border_type_${index}"
           data-index="${index}" class="form-control period_border_part3_1 period_border1">
               <option value="" checked>No Border</option>
               <option value="solid">solid</option>
               <option value="dotted">dotted</option>
               <option value="dashed">dashed</option>
               <option value="double">double</option>
               <option value="groove">groove</option>
               <option value="ridge">ridge</option>
               <option value="inset">inset</option>
               <option value="outset">outset</option>
               <option value="mix">mix</option>
           </select>
        </div>
     </div>
     <div class="container mt-3" id="accordion_period1_${index}">
        <div class="card">
           <div class="card-header">
              <a class="collapsed btn" data-bs-toggle="collapse" href="#collapseTwoPeriod${index}">
              Advanced Style
              </a>
           </div>
           <div id="collapseTwoPeriod${index}" class="collapse" data-bs-parent="#accordion_period1_${index}">
              <div class="card-body">
                 <div class="row">
                    <div class="col-sm-12">
                       <label for="period_customcss_${index}">Custom CSS</label>
                       <input id="period_customcss_${index}" placeholder="Custom CSS" name="period_customcss_${index}"
                          data-index="${index}" type="text" class="form-control" />
                    </div>
                    <div class="col-sm-12 mt-2">
                       <div class="d-grid">
                         <button type="button" class="btn btn-primary btn-block" data-bs-toggle="collapse"
                          data-bs-target="#pread_more_${index}">
                          What Is Custom CSS and How I use it
                         </button>
                       </div>

                       <div id="pread_more_${index}" class="collapse">
                          <p>custom css will change the style of specific period or slot you can add any css you need but it has two uses first you can group some css rules together in this case no need to use comma so all activated will be one unit , second use , whenever it is It's better to create your own css rules with a comma so that you can only delete the given rule, note that the maximum length of one unit without a comma is 250 characters. So adding a comma after each css rule would be better to avoid that and have more control</p>
                       </div>

                    </div>
                 </div>
              </div>
           </div>
        </div>
     </div>
  </div>
<hr />
  `;
        index += 1;
    }
    return html_inputs;

}

function mange_period_inputs(event) {
    let period_inputs = create_period_inputs(event.target.value, 1, 'add');
    if (period_inputs) {
        periodContainer.innerHTML = period_inputs;
        periodContainer.style.display = "block";
    } else {
        periodContainer.innerHTML = '';
        periodContainer.style.display = "none";
    }
}

let period_inputs = create_period_inputs(period_input.value, 1, 'add');
if (period_inputs) {
    periodContainer.innerHTML = period_inputs;
    periodContainer.style.display = "block";
}


/* Mange Periods In form end */




/* Mange Slots In form start */

const slot_input = document.querySelector("#slots_per_period");
const slotContainer = document.querySelector("#slots_container");


slot_input.addEventListener("input", mange_slot_inputs);

function create_slot_inputs(count, start_index = 1) {
    if (count < 1) {
        return false;
    }
    let index = start_index;
    let html_inputs = '';
    for (let i = 0; i < count; i++) {
        html_inputs += `
<div class="form-group border border-dark mt-1 p-2 bg-light text-black">
  <h5 class="text-center badge bg-success text-white">Slot ${index}</h5>
  <div class="row">
        <div class="col-sm-6">
      <label for="start_at_slot">Start At: </label>
      <input required name="start_at_slot_${index}" data-index="${index}" type="time" class="form-control slot-start-at slot_input" />
    </div>
    <div class="col-sm-6">
      <label for="end_at_slot">End At: </label>
      <input required name="end_at_slot_${index}" data-index="${index}" type="time" class="form-control slot-end-at slot_input" />
    </div>
  </div>

  <!-- styles -->
  <div class="row">
     <div class="col-sm-3">
        <label for="slot_color_${index}">Font Color</label>
        <input required name="slot_color_${index}" id="slot_color_${index}"
           data-index="${index}" type="color" value="#000000" class="form-control slot_style_color1" />
           <input value="${index}" type="hidden" style="display:none;" name="slot_add_index_${index}">
     </div>
     <div class="col-sm-3">
        <label for="slot_background_${index}">Background color</label>
        <input required name="slot_background_${index}" id="slot_background_${index}"
           data-index="${index}" type="color" value="#ffffff" class="form-control slot_style_bgcolor1" />
     </div>
     <div class="col-sm-3">
        <label for="slot_fontfamily_${index}">Font Family</label>
        <select name="slot_fontfamily_${index}" data-index="${index}" class="form-control slot_font_family1">
           <option value="">Default</option>
           <option style="font-family:Georgia;" value="font-family:Georgia;" checked>Georgia</option>
           <option style="font-family:Palatino Linotype;" value="font-family:Palatino Linotype;">Palatino Linotype</option>
           <option style="font-family:Book Antiqua;" value="font-family:Book Antiqua;">Book Antiqua</option>
           <option style="font-family:Times New Roman;" value="font-family:Times New Roman;">Times New Roman</option>
           <option style="font-family:Arial;" value="font-family:Arial;">Arial</option>
           <option style="font-family:Helvetica;" value="font-family:Helvetica;">Helvetica</option>
           <option style="font-family:Impact;" value="font-family:Impact;">Impact</option>
           <option style="font-family:Lucida Sans Unicode;" value="font-family:Lucida Sans Unicode;">Lucida Sans Unicode</option>
           <option style="font-family:Tahoma;" value="font-family:Tahoma;">Tahoma</option>
           <option style="font-family:Verdana;" value="font-family:Verdana;">Verdana</option>
           <option style="font-family:Courier New;" value="font-family:Courier New;">Courier New</option>
           <option style="font-family:Lucida Console;" value="font-family:Lucida Console;">Lucida Console</option>
           <option style="font-family:initial;" value="font-family:initial;">initial</option>
        </select>
     </div>
     <div class="col-sm-3">
        <label for="slot_fontsize_${index}">Font Size</label>
        <select id="slot_fontsize_${index}" name="slot_fontsize_${index}" data-index="${index}"
         class="form-control slot_fontsize1">
           <option value="" checked>Default</option>
           <option style="font-size: 8px;" value="font-size: 8px;">8px</option>
           <option style="font-size: 10px;" value="font-size: 10px;">10px</option>
           <option style="font-size: 12px;" value="font-size: 12px;">12px</option>
           <option style="font-size: 14px;" value="font-size: 14px;">14px</option>
           <option style="font-size: 16px;" value="font-size: 16px;">16px</option>
           <option style="font-size: 18px;" value="font-size: 18px;">18px</option>
           <option style="font-size: 1rem;" value="font-size: 1rem;">1 rem</option>
           <option style="font-size: 1rem;" value="font-size: 1rem;">1.5 rem</option>
           <option style="font-size: 0.825em;" value="font-size: 0.825em;">0.825 em</option>
           <option style="font-size: 0.925em;" value="font-size: 0.925em;">0.925 em</option>
           <option style="font-size: 1em;" value="font-size: 1em;">1 em</option>
           <option style="font-size: 1.5em;" value="font-size: 1.5em;">1.5 em</option>
           <option style="font-size: 2em;" value="font-size: 2em;">2 em</option>
           <option style="font-size: 1rem;" value="font-size: 1rem;">2 rem</option>
        </select>
     </div>
     <div class="col-sm-3">
        <label for="slot_border_color_${index}">Border Color</label>
        <select id="slot_border_color_${index}" name="slot_border_color_${index}"
         data-index="${index}" class="form-control slot_border_part1_1 slot_border1">
           <option value="" checked>No Border</option>
           <option value="black;">Black</option>
           <option value="white;">White</option>
           <option value="red;">Red</option>
           <option value="green;">Green</option>
           <option value="gold;">Gold</option>
           <option value="blue;">Blue</option>
        </select>
     </div>
     <div class="col-sm-3">
        <label for="slot_border_size_${index}">Border Size</label>
        <select id="slot_border_size_${index}" name="slot_border_size_${index}"
        data-index="${index}" class="form-control slot_border_part2_1 slot_border1">
           <option value="" checked>No Border</option>
           <option value="border:1px">1px</option>
           <option value="border:1px">2px</option>
           <option value="border:1px">3px</option>
           <option value="border:1px">4px</option>
           <option value="border:1px">5px</option>
        </select>
     </div>
     <div class="col-sm-3">
        <label for="slot_border_type_${index}">Border Type</label>
        <select id="slot_border_type_${index}" name="slot_border_type_${index}"
        data-index="${index}" class="form-control slot_border_part3_1 slot_border1">
           <option value="" checked>No Border</option>
           <option value="solid">solid</option>
           <option value="dotted">dotted</option>
           <option value="dashed">dashed</option>
           <option value="double">double</option>
           <option value="groove">groove</option>
           <option value="ridge">ridge</option>
           <option value="inset">inset</option>
           <option value="outset">outset</option>
           <option value="mix">mix</option>
        </select>
     </div>

  </div>
  <div class="container mt-3" id="accordion_slot1_${index}">
     <div class="card">
        <div class="card-header">
           <a class="collapsed btn" data-bs-toggle="collapse" href="#collapseTwoSlot${index}">
           Advanced Style
           </a>
        </div>
        <div id="collapseTwoSlot${index}" class="collapse" data-bs-parent="#accordion_slot1_${index}">
           <div class="card-body">
              <div class="row">
                 <div class="col-sm-12">
                    <label for="slot_customcss_${index}">Custom CSS</label>
                    <input id="slot_customcss_${index}" placeholder="Custom CSS" name="slot_customcss_${index}"
                       data-index="${index}" type="text" class="form-control" />

                 </div>
                 <div class="col-sm-12 mt-2">
                    <div class="d-grid">
                      <button type="button" class="btn btn-primary btn-block" data-bs-toggle="collapse"
                       data-bs-target="#slot_more_${index}">
                       What Is Custom CSS and How I use it
                      </button>
                    </div>



                    <div id="slot_more_${index}" class="collapse">
                       <p>custom css will change the style of specific period or slot you can add any css
                        you need but it has two uses first you can group
                        some css rules together in this case no need
                        vertical bar example <code>font-weight:bold;text-align:center;</code>
                        or make each rule as single unit to easy control it
                        <code>font-weight:bold;|text-align:center;</code>
                        or make mix <code>font-weight:bold;text-align:center;|background:red !important;</code>
                        in last example we have 2 group of rules one has 2 rules together and the second spreated rule
                        not the app can deatect some invalid css and ignore it but make sure to avoid wrong css
                    </div>
                 </div>
              </div>
           </div>
        </div>
     </div>
  </div>
</div>
    `;
        index += 1;
    }
    return html_inputs;

}

function mange_slot_inputs(event) {
    let slots_inputs = create_slot_inputs(event.target.value);
    if (slots_inputs) {
        slotContainer.innerHTML = slots_inputs;
        slotContainer.style.display = "block";
    } else {
        slotContainer.innerHTML = '';
        slotContainer.style.display = "none";
    }

}

let slots_inputs = create_slot_inputs(period_input.value);
if (slots_inputs) {
    slotContainer.innerHTML = slots_inputs;
    slotContainer.style.display = "block";
}


/* Mange Slots In form end */

/* remove calendar */
const removeCalendarBtns = document.querySelectorAll(".remove_calendar");
const calendarIdDelete = document.querySelector("#remove_calendar_id");
removeCalendarBtns.forEach((removeBtn) => {
    removeBtn.addEventListener("click", (event) => {
        calendarIdDelete.value = event.target.getAttribute("data-calendar");
    });
});

/* remove user */
const removeUserBtns = document.querySelectorAll(".delete_user");
const removeUserId = document.querySelector("#remove_user_id");
removeUserBtns.forEach((removeBtn) => {
    removeBtn.addEventListener("click", (event) => {
        removeUserId.value = event.target.getAttribute("data-user");
    });
});


function returnSelectedIndex(options, value){
  let index = -1;
  options.forEach( (opt, i)=>{
    if (opt.value.toLowerCase() == value.toLowerCase()){
      index = i;
    }
  });
  return index;
}

/* edit user */
const editUserBtns = document.querySelectorAll(".edit_user");
const fullnameEdit = document.querySelector("#fullname_edit");
const usernameEdit = document.querySelector("#username_edit");
const emailEdit = document.querySelector("#email_edit");
const useridEdit = document.querySelector("#userid_edit");
const passwordInputEdit = document.querySelector("#password_edit");

const roleEdit = document.querySelector("#role_edit");
const activeEdit = document.querySelector("#active_edit");
const closeEditUser = document.querySelector("#close_edit_user");

const editRoleOptions = Array.from(roleEdit.options);
const editActiveOptions = Array.from(activeEdit.options);

editUserBtns.forEach((editBtn) => {
    editBtn.addEventListener("click", (event) => {
        const userRole = event.target.getAttribute("data-role");
        const userActive = event.target.getAttribute("data-active");
        const selectedRoleIndex = returnSelectedIndex(editRoleOptions, userRole);
        const selectedActiveIndex = returnSelectedIndex(editActiveOptions, userActive);

        // incase php not laod data for some reason
        if (editRoleOptions == -1 || editActiveOptions == -1){
          closeEditUser.click();
          alert("Unkown Error User Data Can not loaded");
          return false;
        }

        fullnameEdit.value = event.target.getAttribute("data-name");
        usernameEdit.value = event.target.getAttribute("data-username");
        emailEdit.value = event.target.getAttribute("data-email");
        useridEdit.value = event.target.getAttribute("data-user");

        editRoleOptions.forEach((roleOpt)=>{
          if (roleOpt.hasAttribute('selected')){
            roleOpt.removeAttribute("selected");
          }
        });
        editActiveOptions.forEach((activeOpt)=>{
          if (activeOpt.hasAttribute('selected')){
            activeOpt.removeAttribute("selected");
          }
        });
        roleEdit.options[selectedRoleIndex].setAttribute("selected", true);
        activeEdit.options[selectedActiveIndex].setAttribute("selected", true);
        passwordInputEdit.value = "";
    });
});
/* edit calendar */
const editCalendarBtns = document.querySelectorAll(".edit_calendar");
const calendarTitleEdit = document.querySelector("#calendar_title_edit");
const calendarDescriptionEdit = document.querySelector("#calendar_description_edit");
const calendarUseridEdit = document.querySelector("#calendar_userid_edit");
const addedYearCalId = document.querySelector("#years_added_calid");
const addNewYearEdit = document.querySelector("#add_new_year_edit");

/* page 2 vars*/
const addPeriodsCalId = document.querySelector("#add_periods_cal_id");
const addSlotsCalId = document.querySelector("#add_slots_cal_id");

const t_periodsInputPage2 = document.querySelector("#period_per_day_page2");
const t_slotsInputPage2 = document.querySelector("#slots_per_day_page2");


editCalendarBtns.forEach((editBtn) => {
    editBtn.addEventListener("click", (event) => {
      /*

      */

        /* page 1 */
        calendarTitleEdit.value = event.target.getAttribute("data-title");
        calendarDescriptionEdit.value = event.target.getAttribute("data-description");
        calendarUseridEdit.value = event.target.getAttribute("data-calendar");
        addedYearCalId.value = event.target.getAttribute("data-calendar");
        addNewYearEdit.setAttribute("placeholder", event.target.getAttribute("data-added-years"));

        /* page 2*/
        addPeriodsCalId.value = event.target.getAttribute("data-calendar");
        addSlotsCalId.value = event.target.getAttribute("data-calendar");
        t_periodsInputPage2.setAttribute("data-total-periods", event.target.getAttribute("data-total-periods"));
        t_slotsInputPage2.setAttribute("data-total-slots", event.target.getAttribute("data-total-slots"));
        t_slotsInputPage2.setAttribute("data-total-periods", event.target.getAttribute("data-total-periods"));

    });
});
/* edit calendar end */


const toggleEditPass = document.querySelector("#toggle_edit_pass");

toggleEditPass.addEventListener("change", (event) => {
    if (event.target.checked == true) {
        if (passwordInputEdit.hasAttribute("disabled")) {
            passwordInputEdit.removeAttribute("disabled");
        }
        if (!passwordInputEdit.hasAttribute("required")) {
            passwordInputEdit.setAttribute("required", true);
        }
    } else {
        if (!passwordInputEdit.hasAttribute("disabled")) {
            passwordInputEdit.setAttribute("disabled", true);
        }
        if (passwordInputEdit.hasAttribute("required")) {
            passwordInputEdit.removeAttribute("required");
        }
    }



});

/* change edit calendar content modal */
function goToLevel(event) {
    const levelSpan = document.querySelector("#selected_page");
    const selectedLevel = event.target.getAttribute("data-level");
    if (selectedLevel) {
        const selectedContainer = document.querySelector(`div[data-content-level='${selectedLevel}']`);
        if (selectedContainer) {
            const allLevelsContainers = document.querySelectorAll(".level_container");
            allLevelsContainers.forEach((levelContainer) => {
                levelContainer.style.display = "none";
            });
            selectedContainer.style.display = "block";
            levelSpan.innerText = selectedLevel;
        }
    }
}

const editCalLevelBtns = document.querySelectorAll(".edit_cal_level");
editCalLevelBtns.forEach((levelBtn) => {
    levelBtn.addEventListener("click", goToLevel);
});


/* display periods in add periods */
const periodInputPage2 = document.querySelector("#period_per_day_page2");
const periodContainerPage2 = document.querySelector("#periods_container_page2");
periodInputPage2.addEventListener("input", display_periods_page2);


function display_periods_page2(event) {
    let start_index = Number(event.target.getAttribute("data-total-periods")) + 1;
    let period_inputs = create_period_inputs(event.target.value, start_index, 'edit');
    if (period_inputs) {
        periodContainerPage2.innerHTML = period_inputs;
        periodContainerPage2.style.display = "block";
    } else {
        periodContainerPage2.innerHTML = '';
        periodContainerPage2.style.display = "none";
    }
}


/* add slots page 2*/

const slotsInputPage2 = document.querySelector("#slots_per_day_page2");
const slotsContainerPage2 = document.querySelector("#slots_container_page2");
slotsInputPage2.addEventListener("input", display_slots_page2);

function display_slots_page2(event) {
    let totalPeriods = Number(event.target.getAttribute("data-total-periods"));
    slotsContainerPage2.innerHTML = "<div class='alert alert-info mt-2'>Please Add Periods First</div>";
    if (totalPeriods == 0) {
        return false;
    }

    let start_index = Number(event.target.getAttribute("data-total-slots")) + 1;

    // add the slots start from new index
    let slots_inputs = create_slot_inputs(event.target.value, start_index);
    if (slots_inputs) {
        slotsContainerPage2.innerHTML = slots_inputs;
        slotsContainerPage2.style.display = "block";
    } else {
        slotsContainerPage2.innerHTML = '';
        slotsContainerPage2.style.display = "none";
    }

}

/* */

// set inital input for add periods and slots to not let user add empty input by wrong
const allEditCalbtns = document.querySelectorAll(".edit_calendar");
allEditCalbtns.forEach((calbtn) => {
    periodInputPage2.value = 1;
    slotsInputPage2.value = 1;

    calbtn.addEventListener("click", () => {


        let start_index = Number(calbtn.getAttribute("data-total-periods")) + 1;
        let start_index_slots = Number(calbtn.getAttribute("data-total-slots")) + 1;
        let period_inputs = create_period_inputs(1, start_index, 'edit');
        if (period_inputs) {
            periodContainerPage2.innerHTML = period_inputs;
            periodContainerPage2.style.display = "block";
        } else {
            periodContainerPage2.innerHTML = '';
            periodContainerPage2.style.display = "none";
        }

        let totalPeriods = Number(calbtn.getAttribute("data-total-periods"));

        if (totalPeriods < 1) {
            slotsContainerPage2.innerHTML = "<div class='alert alert-info mt-2'>Please Add Periods First</div>";
        } else {
            if (Number(calbtn.getAttribute("data-total-periods")) > 0) {

                let slots_inputs = create_slot_inputs(1, start_index_slots);
                if (slots_inputs) {
                    slotsContainerPage2.innerHTML = slots_inputs;
                    slotsContainerPage2.style.display = "block";
                } else {
                    slotsContainerPage2.innerHTML = '';
                    slotsContainerPage2.style.display = "none";
                }
            }
        }


    });

});

/*  delete periods toggle */

function startDeletePeriod(event) {
    event.preventDefault();
    const deletePeriodForm = document.querySelector("#delete_period_form");
    deletePeriodForm.addEventListener("submit", displayCalendarEditWait);
    const container1ID = event.target.getAttribute("data-level-1");
    const container2ID = event.target.getAttribute("data-level-2");
    const container1 = document.querySelector(`#${container1ID}`);
    const container2 = document.querySelector(`#${container2ID}`);
    container1.style.display = "none";
    container2.style.display = "block";

    container2.setAttribute("data-level-1", container1ID);

    container2.classList.add('active-delete-period');

}


function backDefaultDelete() {
    const deletePeriods1 = document.querySelectorAll(".delete_periods_s1");
    const deletePeriods2 = document.querySelectorAll(".delete_periods_s2");
    const deleteSlots1 = document.querySelectorAll(".delete_slots_s1");
    const deleteSlots2 = document.querySelectorAll(".delete_slots_s2");
    deletePeriods1.forEach((period1) => {
        period1.style.display = "block";
    });
    deletePeriods2.forEach((period2) => {
        period2.style.display = "none";
        if (period2.classList.add('active-delete-period')) {
            period2.classList.remove('active-delete-period');
        }
    });
    deleteSlots1.forEach((slot1) => {
        slot1.style.display = "block";
    });
    deleteSlots2.forEach((slot2) => {
        if (slot2.classList.add('active-delete-slot')) {
            slot2.classList.remove('active-delete-slot');
        }
        slot2.style.display = "none";
    });
}

function endDeletePeriod(event) {
    event.preventDefault();
    const container1ID = event.target.getAttribute("data-level-1");
    const container2ID = event.target.getAttribute("data-level-2");
    const container1 = document.querySelector(`#${container1ID}`);
    const container2 = document.querySelector(`#${container2ID}`);
    container1.style.display = "block";
    container2.style.display = "none";
    backDefaultDelete();
}

/*  delete slots toggle */

function startDeleteSlot(event) {
    event.preventDefault();
    const deletePeriodForm = document.querySelector("#delete_period_form");
    deletePeriodForm.addEventListener("submit", displayCalendarEditWait);
    const container1ID = event.target.getAttribute("data-level-1");
    const container2ID = event.target.getAttribute("data-level-2");
    const container1 = document.querySelector(`#${container1ID}`);
    const container2 = document.querySelector(`#${container2ID}`);
    container1.style.display = "none";
    container2.style.display = "block";

    container2.setAttribute("data-level-1", container1ID);
    container2.classList.add('active-delete-period');

}


function endDeleteSlot(event) {
    event.preventDefault();
    const container1ID = event.target.getAttribute("data-level-1");
    const container2ID = event.target.getAttribute("data-level-2");
    const container1 = document.querySelector(`#${container1ID}`);
    const container2 = document.querySelector(`#${container2ID}`);
    container1.style.display = "block";
    container2.style.display = "none";
    backDefaultDelete();
}

function startDeleteSlot(event) {
    event.preventDefault();

    const deleteSlotForm = document.querySelector("#delete_slot_form");
    deleteSlotForm.addEventListener("submit", displayCalendarEditWait);

    const container1ID = event.target.getAttribute("data-level-1");
    const container2ID = event.target.getAttribute("data-level-2");
    const container1 = document.querySelector(`#${container1ID}`);
    const container2 = document.querySelector(`#${container2ID}`);

    container1.style.display = "none";
    container2.style.display = "block";

    container2.setAttribute("data-level-1", container1ID);
    container2.classList.add('active-delete-period');
}

// toggle show and hide custom css style period
function toggleEditCustomCSS(event){

  let dataIndex = event.target.getAttribute("data-index");
  dataIndex = !dataIndex ? event.target.parentElement.getAttribute("data-index") : dataIndex;
  const currentForm = document.querySelector(`form.edit_customcss_form[data-index='${dataIndex}']`);
  if (currentForm){
    const currentStatus  = currentForm.getAttribute("data-visible");
    if (currentStatus == 'false'){
      currentForm.style.display = "block";
      currentForm.setAttribute("data-visible", "true");
    } else {
      currentForm.style.display = "none";
      currentForm.setAttribute("data-visible", "false");
    }
  }
}


function toggle_show_css(event){

  const currentBtn = event.target;
  if (!currentBtn.hasAttribute("href")){
    return false;
  }
  const btnHrefSplited = currentBtn.getAttribute("href").split("#");
  if (btnHrefSplited.length < 2){
    return false;
  } else {
    event.preventDefault();
  }
  const currentContainerId = btnHrefSplited[1];
  const currentContainer = document.getElementById(currentContainerId);
  if (currentContainer){
    if (currentBtn.getAttribute("data-status") == 'show'){
      if (!currentBtn.classList.contains("active_shadow")){
        currentBtn.classList.add("active_shadow");
      }
      if (!currentContainer.classList.contains("active_shadow")){
        currentContainer.classList.add("active_shadow");
      }
      currentBtn.setAttribute("data-status", "hide");
    } else {
      if (currentBtn.classList.contains("active_shadow")){
        currentBtn.classList.remove("active_shadow");
      }
      if (currentContainer.classList.contains("active_shadow")){
        currentContainer.classList.remove("active_shadow");
      }
      currentBtn.setAttribute("data-status", "show");
    }

  }
}


// handle main css edit
function handleMainCss(event, code_id){

  const cssIntialValuesElm = document.querySelector(`#${code_id}`);
  const errorElm = document.querySelector(`p[data-code='${code_id}']`);

  if (!cssIntialValuesElm){
    alert("Unexcpted Error");
    event.preventDefault();
    return false;
  }
  event.preventDefault();
  const intialValues = cssIntialValuesElm.innerText.split(",");

  const allMainCSSInputs = document.querySelectorAll(".main_css");
  let currentDisabled = 0;
  allMainCSSInputs.forEach( (inp)=>{
    const inputPeviousValue = intialValues[Number(inp.getAttribute("data-i"))];
    const inputCurentValue = inp.value;
    if (inputPeviousValue == inputCurentValue){
      inp.setAttribute("disabled", "disabled");
      inp.classList.add("disabled_inp");
      currentDisabled += 1;
    }
  });



  if (currentDisabled < allMainCSSInputs.length){
    errorElm.innerText = "";
    errorElm.style.display = "none";
    event.target.submit();
  } else {
     errorElm.style.display = "block";
     errorElm.innerText = "No Changes Were detected.";
     errorElm.style.border = "2px solid red";
     setTimeout(()=>{errorElm.style.border = "";},300);
     const allDisabled = document.querySelectorAll(".disabled_inp");
     allDisabled.forEach( (dis)=>{
       dis.classList.remove("disabled_inp");
       dis.removeAttribute("disabled");
     });
  }
}

function addCalWait(event){
  event.preventDefault();
  const currentWait = document.querySelector("#action_loading_container2");
  const formActionsContainer = document.querySelector("#add_newcal_container");
  formActionsContainer.style.display = "none";
  currentWait.style.display = "block";
  event.target.submit();
}

const advancedModalBody = document.querySelector("#modal_body_advanced");
const allAdvancedBtns = document.querySelectorAll("button[data-bs-target='#advancedCalMange']");
allAdvancedBtns.forEach((advancedBtn)=>{
  advancedBtn.addEventListener("click", async (event)=>{
    let index = 0;
    let modalHTML = '';
    event.preventDefault();
    const selectedCal = event.target.getAttribute("data-cal-id");
    if (!selectedCal){return false;}
    const updateCalIdMiniAJAX = await setupPostData('controllers/setup_controller.php',{advancedCalid: selectedCal});
    if (updateCalIdMiniAJAX && updateCalIdMiniAJAX.code && updateCalIdMiniAJAX.code == 200 && updateCalIdMiniAJAX.data){
      updateCalIdMiniAJAX.data.forEach( (currentPeriodContainer)=>{
        if (currentPeriodContainer.period){
          const thePeriod = currentPeriodContainer.period;
          const theSlots = currentPeriodContainer.period_slots;
          modalHTML += `
          <!-- new Period end -->
            <div class="advanced_period_slots container border border-dark bg-light">
            <div>
              <h5 class="text-center p-2 mt-2">(${thePeriod.description}, ${thePeriod.period_date})</h5>
            </div>
          `;
          theSlots.forEach( (adavnedSlot)=>{
            index += 1;
              let start = adavnedSlot.start_from;
            let startList = start.split(":");
            if (startList.length > 2){
              start = startList.slice(0, startList.length-1).join(":");
            }

            let end = adavnedSlot.end_at;
            let endList = end.split(":");
            if (endList.length > 1){
              end = endList.slice(0, endList.length-1).join(":");
            }
            modalHTML += `
            <!-- new Slot Start -->
            <div class="d-flex justify-content-center align-items-center flex-column">
              <div class="w-100 border border-black bg-darkseagreen border border-dark rounded text-white d-flex justify-content-center align-items-center flex-column p-2 m-2" style="width:100%;">
                <div class="text-center">
                <h5 class="badge bg-light text-black" style="width:fit-content;margin-left:auto;margin-right:auto;">
                    Slot ${index}
                </h5>
                </div>
                <div class="w-100 border border-black bg-secondary text-white d-flex justify-content-center align-items-center">
                  <div class="border border-light flex-fill d-flex justify-content-center align-items-center">Start</div>
                  <div class="border border-light flex-fill d-flex justify-content-center align-items-center">End</div>
                </div>
                <div class="w-100 border text-white d-flex justify-content-center align-items-center">
                  <div style="max-width: 50%;min-height:100px;" class="bg-primary border border-light flex-fill d-flex justify-content-start align-items-center flex-column p-2">
                    <div class="badge bg-light text-black" data-modifer="asp_slot_start_${adavnedSlot.id}">${start}</div>
                    <div class="w-75 d-grid">
                      <button class="bg-warning border border-light w-100 hoverable" onclick="noneIndexToggle(event)" data-asp-target="asp_slot_start_${adavnedSlot.id}" data-asp-submit="submit_asp_${adavnedSlot.id}">Edit</button>
                      <div>
                        <input style="display:none;" class="asp_input" type="time" name="asp_start" value="${adavnedSlot.start_from}" disabled="disabled" id="asp_slot_start_${adavnedSlot.id}" />
                      </div>
                    </div>
                  </div>
                  <div style="max-width: 50%;min-height:100px;" class="bg-primary border border-light flex-fill d-flex justify-content-start align-items-center flex-column p-2">
                    <div data-modifer="asp_slot_end_${adavnedSlot.id}" class="badge bg-light text-black">${end}</div>
                    <div class="w-75 d-grid">
                      <button class="bg-warning border border-light w-100 hoverable" onclick="noneIndexToggle(event)" data-asp-target="asp_slot_end_${adavnedSlot.id}" data-asp-submit="submit_asp_${adavnedSlot.id}">Edit</button>
                        <div>
                          <input style="display:none;" class="asp_input  border border-primary rounded" type="time" name="asp_end" value="${adavnedSlot.end_at}" disabled="disabled" id="asp_slot_end_${adavnedSlot.id}" />
                        </div>
                    </div>
                  </div>
                </div>
                <div class="form-group container border border-secondary rounded bg-light d-flex justify-content-between p-2 m-2">
                  <input style="display:none;" id="submit_asp_${adavnedSlot.id}" class="btn btn-success" type="button" onclick="ajaxJQueryRequest(event)" class="asp_edit_submit"
                     data-asp-period-index="${thePeriod.period_index}"
                     data-asp-slot-index="${adavnedSlot.slot_index}"
                     data-asp-slot-start-id="asp_slot_start_${adavnedSlot.id}"
                     data-asp-slot-end-id="asp_slot_end_${adavnedSlot.id}"
                     data-asp-cal-id="${selectedCal}"
                   value="Submit"/>
                  <input style="display:none;" data-target-id="submit_asp_${adavnedSlot.id}" onclick="canceleEdit(event)" class="btn btn-danger" type="button"  value="Cancel"/>
                </div>
              </div>
            </div>
            `
            modalHTML += '<!-- new Slot Start -->';
          });
          modalHTML += `;
          </div>
          <!-- new Period end -->
          `;
        }
      });
    }
    advancedModalBody.innerHTML = modalHTML;
  });
});
