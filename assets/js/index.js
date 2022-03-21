
/* ############## AJAX ############## */
const postData = async function (url="", data={}){
  const response = await fetch(url, {
        method: "POST",
        credentials: "same-origin",
        headers:{
         "Content-Type": "application/json"
       },
       body: JSON.stringify(data)
     }
   );
   try{
      const res = await response.json();
      //console.error(res);
      return res;
    }catch(err){
      console.error(err);
    }
}

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

const resetAlertCont = document.querySelector("#bootstrap_reset_alert");
const displayAJAXRestMsg = (status, message, status_text,display=1)=>{
  if (display != 1){return false;}
  resetAlertCont.innerHTML = `
  <div class="alert alert-${status} alert-dismissible">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    <strong>${status_text}!</strong> <span>${message}</span>
  </div>
  `;
};




window.addEventListener('DOMContentLoaded', (event) => {
const playSound = (selector)=>{
  //open_modal_sound unable_open_modal
  const selectedSound = document.querySelector(`${selector}`);
  selectedSound.volume = 0.1;
  // important for on time sound like it play from begning and ignore previous
  // Show loading animation.
  selectedSound.currentTime = 0;
  var playPromise = selectedSound.play();

  if (playPromise !== undefined) {
      playPromise.then(_ => {
      // Automatic playback started!
      // Show playing UI.
      // We can now safely pause video...
      video.pause();
    })
    .catch(error => {
      // Auto-play was prevented
      // Show paused UI.
    });
  }
};

// aside menu setting screen width
// body width
let bigAsideWidth = "78%";
let smallAsideWidth = "60%";
let activeAsideWidth = "22%";
const increaseAsideSize = ()=>{
}

//python king
let containerInViewData = {'elm_id': '', 'last_update': '', 'type': '', 'html_id':'', 'html_class':'', 'cal_id': ''};

const emptyEditorsSelectors = (type='container')=>{
  const elmIdtxtStyle = document.querySelector("#container_curent_elmid");
  const elmlastUpdateStyle = document.querySelector("#container_curent_lastupdate");
  const containerCurentType = document.querySelector("#container_curent_type");
  const containerCurentHtmlid = document.querySelector("#container_curent_htmlid");
  const containerCurentHtmlclass = document.querySelector("#container_curent_htmlclass");
  const containerCurentGroup = document.querySelector("#container_curent_group");
  elmIdtxtStyle.innerText= '';
  elmlastUpdateStyle.innerText = '';
  containerCurentType.innerText= '';
  containerCurentHtmlid.innerText = '';
  containerCurentHtmlclass.innerText= '';
  containerCurentGroup.innerText = '';
  applyOnGroup.innerHTML = '';
  containerInViewData = {'elm_id': '', 'last_update': '', 'type': '', 'html_id':'', 'html_class':''};


  if (type == 'element'){
    const allSelectElmOption = document.querySelectorAll("div#bs_element_editor select");
    if (allSelectElmOption){
      allSelectElmOption.forEach( (selectInp)=>{
        const currentOptions = Array.from(selectInp.options);
        currentOptions.forEach( (op)=>{
           if (op.hasAttribute("selected")){
             op.removeAttribute("selected");
           }
        });
      });
    }
  } else {
    const allSelectContOption = document.querySelectorAll("div#bs_containers_editor select");
    if (allSelectContOption){
      allSelectContOption.forEach( (selectInp)=>{
        const currentOptions = Array.from(selectInp.options);
        currentOptions.forEach( (op)=>{
           if (op.hasAttribute("selected")){
             op.removeAttribute("selected");
           }
        });
      });
    }
  }
};
const updateElmBSClasses = (oldClass, newClass, elm)=>{
  if (!elm){return false;}
  if (oldClass.trim() != ''){
    if (elm.classList.contains(oldClass)){
      elm.classList.remove(oldClass);
    }
  }
  if (newClass.trim() != ''){
    if (!elm.classList.contains(newClass)){
      elm.classList.add(newClass);
    }
  }
};

const updateBSClassesGroup = (oldClass, newClass, group)=>{
  const allElements = document.querySelectorAll(`[data-editor-group='${group}']`);
  allElements.forEach( (bsElm)=>{
    if (bsElm){
      if (oldClass.trim() != ''){
        if (bsElm.classList.contains(oldClass)){
          bsElm.classList.remove(oldClass);
        }
      }
      if (newClass.trim() != ''){
        if (!bsElm.classList.contains(newClass)){
          bsElm.classList.add(newClass);
        }
      }
    }
  });
};


const toggleEditorWait = (editorWaitParm)=>{
  const editEnableBtn = document.querySelector('#edit_mode_container button');
  const editEnableSpan = document.querySelector('#edit_mode_container span');
  const editEnableBtnElm = document.querySelector('#edit_mode_elm button');
  const editEnableSpanElm = document.querySelector('#edit_mode_elm span');

  const setupElmConts = document.getElementById('setup_elm_conts');
  const editorWaitGif = document.getElementById('editor_wait_gif');
  if (editorWaitParm == true){
    editEnableBtn.style.display = "none";
    editEnableSpan.style.display = "none";
    editEnableBtnElm.style.display = "none";
    editEnableSpanElm.style.display = "none";
    setupElmConts.style.display = "none";
    editorWaitGif.style.display = "block";
  } else {
    editEnableBtn.style.display = "block";
    editEnableSpan.style.display = "block";
    editEnableBtnElm.style.display = "block";
    editEnableSpanElm.style.display = "block";
    setupElmConts.style.display = "block";
    editorWaitGif.style.display = "none";
  }
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
const applyOnGroup = document.querySelector("#apply_on_group");

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

  if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
    // if small screen it will be hard to reach last section so manual handle it
    activeSection = allSections[allSections.length-1];
  } else {
    for (let i=0; i<allSections.length; i++){
       let top = allSections[i].getBoundingClientRect().top;
       let active = top > (nagtive_height + 50) && top < min_elm_hieght;

       if (active==false && i==0){
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

// switch month form
const monthForms = document.querySelectorAll(".month_form");
monthForms.forEach( (monthForm)=>{
  monthForm.addEventListener("click", (event)=>{
    if (event.target.nodeName.toLowerCase() == 'form'){
      event.target.submit();
    } else {
      if (event.currentTarget.nodeName.toLowerCase() == 'form'){
        event.currentTarget.submit();
      } else {
        let current_parent = event.target.parentElement;
        for (let i=0; i<4; i++){
          if (current_parent.nodeName.toLowerCase() == 'form'){
            current_parent.submit();
            break;
          } else {
            current_parent = event.target.parentElement;
          }
        }
      }

    }
  });
});




/* open booking modal */
const startFromSlotA = document.getElementById('start_from_slot_a');
const endAtSlotA = document.getElementById('end_at_slot_a');
const bookingDateA = document.getElementById('booking_date_a');
const periodDescriptionA = document.getElementById('period_description_a');
const periodDateTimeA = document.getElementById('period_date_time_a');
const reservationSlotId = document.getElementById('reservation_slot_id');
const slotIndexA = document.getElementById('slot_index_a');
const loggedUId = document.getElementById('loggeduid');
const addBookingInput1 = document.getElementById('add_booking_input1');
const addBookingInput2 = document.getElementById('add_booking_input2');
const addReservationBtn1 = document.querySelector("#add_reservation");


const allBookingOpenBtns = document.querySelectorAll(".book_open_btn");
allBookingOpenBtns.forEach( (bookOpen)=>{
  bookOpen.addEventListener("click", (event)=>{
    const targetElement = event.currentTarget;
    if (!targetElement.classList.contains('book_open_btn')){
      return false;
    }
    const SlotEmpty = targetElement.getAttribute("data-slot-empty");
    if (SlotEmpty != '0'){
      startFromSlotA.innerText = targetElement.getAttribute("data-slot-start_from");
      endAtSlotA.innerText = targetElement.getAttribute("data-slot-end_at");
      bookingDateA.innerText = targetElement.getAttribute("data-period-date");
      periodDescriptionA.innerText = targetElement.getAttribute("data-period-description");
      periodDateTimeA.innerText = targetElement.getAttribute("data-period-date");
      reservationSlotId.value = targetElement.getAttribute("data-slot-id");
      loggedUId.value = targetElement.getAttribute("data-uid");
      slotIndexA.innerText = targetElement.getAttribute("data-slot-index");
      // IMportant point this how recover reservation
    }
  });
});


/* sound effects not owned by current user */
const allUsedSlots = document.querySelectorAll(".used_slot");
allUsedSlots.forEach( (slot)=>{
  slot.addEventListener("click", ()=>{
    playSound("#unable_open_modal");
    return true;
  });
});

const allEmptySlots = document.querySelectorAll(".empty_slot");
allEmptySlots.forEach( (slot)=>{
  slot.addEventListener("click", ()=>{
    playSound("#open_modal_sound");
    return true;
});
});

const addResAisde = document.querySelector(".aside_add_res");
addResAisde.addEventListener("click", ()=>{playSound("#open_modal_sound")});


function emptyAllBSInputs(){
  const allBsElementsSelectors = document.querySelectorAll(".edit_bs_selectelm");
  allBsElementsSelectors.forEach( (elmSelect)=>{
    const elmOptions = Array.from(elmSelect.options);
    elmOptions.forEach((op)=>{
      if (op.selected){
        op.removeAttribute('selected');
      }
    });
  });
}

/*
const reservationName = document.getElementById('reservation_name');
const reservationComment = document.getElementById('reservation_comment');
const supcalToken = document.getElementById('supcal_token');


async function bookingFunction(event){
  const res = await postData('', {
    reservation_slot_id: reservationSlotId.value,
    reservation_name: reservationName.value,
    reservation_comment: reservationComment.value,
    secuirty_token: supcalToken.value
  });
}
addReservationBtn.addEventListener("click", bookingFunction );
*/


/* AJAX Map New Reservation advanced UX */


const mapDayLevel2 = document.querySelector("#map_day_level2");
const mapDayLevel3 = document.querySelector("#map_day_level3");
const level3StartFrom = document.querySelector("#level3_start_from");
const level3EndAt = document.querySelector("#level3_end_at");
const reservationSlotIdMap = document.querySelector("#reservation_slot_id_map");
const reservationPTitleMap = document.querySelector("#reservation_ptitle_map");
const mapNewPeriodsCont = document.querySelector("#map_reservation_periods_container");
const mapBookingModalOpen = document.querySelector("#map_booking_modal_open");
let periodIndex = 0;

function backEveryThingMap(){
  if (mapDayLevel3){
    mapDayLevel3.style.display = "none";
  }
  if (mapDayLevel2){
    mapDayLevel2.style.display = "none";
  }
  if (mapNewPeriodsCont){
    mapNewPeriodsCont.innerHTML = '';
  }
  if (adminResOwner){
    adminResOwner.style.display = "none";
  }
  periodIndex = 0;
}
mapBookingModalOpen.addEventListener("click", backEveryThingMap);

function goTomapLevel2(){
  mapDayLevel3.style.display = "none";
  mapDayLevel2.style.display = "block";
  mapNewPeriodsCont.innerHTML = '';
}


const adminResOwner = document.querySelector("#admin_reservation_owner");
function goTomapLevel3(event){
  mapNewPeriodsCont.innerHTML = '';
  const slotId = event.target.value;
  const slotStartFrom = event.target.getAttribute('data-start');
  const slotEndAt = event.target.getAttribute('data-end');
  const periodTitle = event.target.getAttribute('data-period-title');
  if (adminResOwner){
    // async php secuirty with es6
    adminResOwner.style.display = "block";
  }

  displayAddReservationForm(slotId, slotStartFrom,  slotEndAt, periodTitle);
}


function displayAddReservationForm(slot_id, period_title, start_at, end_from){
  mapDayLevel3.style.display = "block";
  reservationSlotIdMap.value = slot_id;
  level3StartFrom.innerText = period_title;
  level3EndAt.innerText = start_at;
  reservationPTitleMap.innerText = end_from;
}


const displayErrorAjaxMap = (error_msg)=>{
  const mapErrorCont = document.querySelector("#map_error_cont");
  mapErrorCont.innerHTML = `
  <div class="alert alert-danger alert-dismissible fade show">
    <p>${error_msg}</p>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>`;
}

const displayAjaxEditorMsg = (msg, type='succss')=>{
  const mapErrorCont = document.querySelector("#editor_error_cont");
  mapErrorCont.innerHTML = `
  <div class="alert alert-${type} alert-dismissible fade show">
    <p>${msg}</p>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>`;
}


function addSlot(slot_id, start_from, end_it, period_title, empty){
  let slot_input = '';
  if (empty == '1'){
    slot_input =  `
    <div class="container">
      <input type="radio" value="${slot_id}"  class="map_select_slot_id"
                         data-start="${start_from}" data-end="${end_it}" data-period-title="${period_title}">
    </div>`;
  } else {
    slot_input = `
     <div class="container">
      <div class="badge bg-info">Not Avail</div>
     </div>
    `;
  };
  const startPMorAM = Number(start_from.slice(0, 2)) > 12 ? 'PM' : 'AM';
  const endPMorAM = Number(end_it.slice(0, 2)) > 12 ? 'PM' : 'AM';

  const slotHTML =
  `     <!-- slot start -->
        <div class="border border-primary">
         <div class="d-flex text-center flex-fill p-2 mb-1">
            <div class="d-flex justify-content-center align-items-between text-center flex-fill border border-primary p-2">
               <div class="badge bg_cornflowerblue flex-fill max_width_30 fontbold">${start_from.slice(0, 5)} ${startPMorAM}</div>
               <div class="badge bg_palevioletred flex-fill max_width_30 fontbold">${end_it.slice(0, 5)} ${endPMorAM}</div>
               <div class="badge bg-light flex-fill max_width_30">
                 ${slot_input}
               </div>
            </div>
         </div>
        </div>
        <!-- slot end -->
  `;
 return slotHTML;
}


function addPeriod(period_id, period_title){
  const newPeriod = document.createElement("div");
  newPeriod.classList.add("d-flex", "flex-wrap", "flex-column", "border", "border-secondary", "mt-2");
  const periodId = `new_period_${period_id}`;
  newPeriod.setAttribute("id", periodId);
  mapNewPeriodsCont.appendChild(newPeriod);
  newPeriod.innerHTML = "<h5 class='text-center p-2 text-white bg_darkkhaki fontbold'>"+period_title+"</h5>";
  return periodId;
}
async function getDayPeriodsAndSlots(event){
  backEveryThingMap();
  // send ajax request to get periods and slots data
  const selectedDay = event.target.value;
  const currentCalId = event.target.getAttribute("data-cal-id");
  if (!selectedDay || !currentCalId){return false;}

  const periodsAndSlotsData = await postData('',{map_reservation_date:selectedDay, map_cal_id:currentCalId});
  // incase unknown problem like calendar open unavail years that not happend without break db and code but when it handled friendly
  if (periodsAndSlotsData.code != 200){
    displayErrorAjaxMap(periodsAndSlotsData.message);
    backEveryThingMap();
    return false;
  }
  if (periodsAndSlotsData.data.length < 1){
    displayErrorAjaxMap("No Periods Found For selected Day");
    backEveryThingMap();
    return false;
  }
  const periodsData = periodsAndSlotsData.data;
  goTomapLevel2();
  for (let i=0; i<periodsData.length; i++){
    const currentPeriod = periodsData[i].period;
    const currentSlots = periodsData[i].slots;

    const periodId = addPeriod(currentPeriod.id, currentPeriod.period_title);
    const getPeriod = document.getElementById(periodId);
    // slots data
    for (let s=0; s<currentSlots.length; s++){
      getPeriod.innerHTML += addSlot(currentSlots[s].id, currentSlots[s].start_from, currentSlots[s].end_at, currentPeriod.period_title, currentSlots[s].empty);
    }
  }

  const slotmapIdInputs = document.querySelectorAll(".map_select_slot_id");
  slotmapIdInputs.forEach( (inputElm)=>{
    inputElm.addEventListener( "change", goTomapLevel3 );
  });
}
const mapRservationDate = document.querySelector("#map_reservation_date");
mapRservationDate.addEventListener( "change", getDayPeriodsAndSlots );


// effects for owned
const reservationIdInp = document.querySelector("#cancel_reservation_id");
const reservationSlotIdInp = document.querySelector("#cancel_reservation_slotid");

function openCancelReservation(event){
  event.preventDefault();
  if (reservationIdInp){
      reservationIdInp.value = event.target.getAttribute("data-id");
  }
  if (reservationSlotIdInp){
      reservationSlotIdInp.value = event.target.getAttribute("data-slot-id");
  }

}
function showOwnedEffect(event){
  if (event.target.classList.contains("fa fa-envelope-o")){
    event.target.classList.add("fa-envelope-open-o");
    event.target.classList.remove("fa fa-envelope-o");
    return true;
  }
}
tooltip = null;
let tooltips = [];
function clearthem(){
  tooltips.forEach( (tolTip)=>{
    tolTip.hide();
  });
}
function showOwnedEffectOpen(event, selector='i.owned_byLoged', title='View Your Reservation Data', placement='bottom'){
  event.preventDefault();
  if (event.target.classList.contains("fa-envelope-o")){
    event.target.classList.remove("fa-envelope-o");
    event.target.classList.add("fa-envelope-open-o");
  }
  event.target.setAttribute("data-bs-toggle", "tooltip");
  event.target.setAttribute("data-bs-placement", placement);
  event.target.setAttribute("title", title);

  var tooltipTriggerList = [].slice.call(document.querySelectorAll(`${selector}[data-bs-toggle="tooltip"]`));
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    tooltip = new bootstrap.Tooltip(tooltipTriggerEl);
    tooltip.show();
    tooltips.push(tooltip);
    return tooltip;
  });
}

function showOwnedEffectClose(event, selector='i.owned_byLoged'){
  if (event.target.classList.contains("fa-envelope-open-o")){
    event.target.classList.add("fa-envelope-o");
    event.target.classList.remove("fa-envelope-open-o");
  }
  clearthem();
  const allTooltips = document.querySelectorAll(`${selector}[data-bs-toggle="tooltip"]`);
  allTooltips.forEach( (toolTip)=>{
    if (toolTip.hasAttribute("data-bs-toggle")){
      toolTip.removeAttribute("data-bs-toggle")
    }
    if (toolTip.hasAttribute("data-bs-placement")){
      toolTip.removeAttribute("data-bs-placement")
    }
    if (toolTip.hasAttribute("title")){
      toolTip.removeAttribute("title")
    }
  });
}

// effect for open owned reservation
const allOwnedRes = document.querySelectorAll(".owned_byLoged");

allOwnedRes.forEach( (resBtn)=>{
  resBtn.addEventListener("mouseenter", (event)=>{showOwnedEffectOpen(event, 'i.owned_byLoged', 'View Your Reservation Data', 'right')});
  resBtn.addEventListener("mouseout", (event)=>{showOwnedEffectClose(event, 'i.owned_byLoged')});
});

const emptyViewSlots = document.querySelectorAll(".view_empty_slot");
emptyViewSlots.forEach( (emptySlot)=>{
  emptySlot.addEventListener("mouseenter", (event)=>{showOwnedEffectOpen(event, 'i.view_empty_slot', 'View Empter Slot Data', 'right')});
  emptySlot.addEventListener("mouseout", (event)=>{showOwnedEffectClose(event, 'i.view_empty_slot')});
});

const togglesAsside = document.querySelectorAll(".toggle_asside");
togglesAsside.forEach( (toggleAsside)=>{
  toggleAsside.addEventListener("mouseenter", (event)=>{showOwnedEffectOpen(event, 'div.toggle_asside', 'Close The Style Editor', 'bottom')});
  toggleAsside.addEventListener("mouseout", (event)=>{showOwnedEffectClose(event, 'div.toggle_asside', 'Close The Style Editor')});
});


const removeOwnedReservations = document.querySelectorAll(".owned_byLoged_remove");
removeOwnedReservations.forEach( (removeResrv)=>{
  removeResrv.addEventListener("click", openCancelReservation);
});







const allEditReservations = document.querySelectorAll(".edit_owned_slot");

allEditReservations.forEach( (editResrv)=>{
  editResrv.addEventListener("click",editHandler);
});


const editReservIDInput = document.querySelector("#edit_reservation_id");
const editReservNameInput = document.querySelector("#edit_reservation_name");
const editReservCommentInput = document.querySelector("#edit_reservation_comment");
const editViewStartAt = document.querySelector("#edit_viewstart_at");
const editViewEndAt = document.querySelector("#edit_viewend_at");
function editHandler(event){
  event.preventDefault();
  editReservIDInput.value = event.target.getAttribute("data-id");
  editReservNameInput.value = event.target.getAttribute("data-name");
  editReservCommentInput.value = event.target.getAttribute("data-notes");
  editViewStartAt.innerText = event.target.getAttribute("data-start");
  editViewEndAt.innerText = event.target.getAttribute("data-end");
}

const allViewReservations = document.querySelectorAll(".view_reservation");

allViewReservations.forEach( (viewReserv)=>{
  viewReserv.addEventListener("click",viewHandler);
});

const viewReservName = document.querySelector("#view_reservation_name");
const viewViewComment = document.querySelector("#view_reservation_notes");
const viewReservStart = document.querySelector("#view_viewstart_at");
const viewReservEnd = document.querySelector("#view_viewend_at");

const viewReservationUname = document.querySelector("#view_reservation_uname");
const viewReservationUusername = document.querySelector("#view_reservation_uusername");
const viewReservationUrole = document.querySelector("#view_reservation_urole");
const viewReservationEmail = document.querySelector("#view_reservation_email");
function viewHandler(event){
  event.preventDefault();
  const btnId = event.target.getAttribute("data-show");
  const btn = document.getElementById(btnId);
  if (btn){
    viewReservName.innerText = event.target.getAttribute("data-name");
    viewViewComment.innerText = event.target.getAttribute("data-notes");
    viewReservStart.innerText = event.target.getAttribute("data-start");
    viewReservEnd.innerText = event.target.getAttribute("data-end");

    /* user */
    viewReservationUname.innerText = event.target.getAttribute("data-user-name");
    viewReservationUusername.innerText = event.target.getAttribute("data-username");
    viewReservationUrole.innerText = event.target.getAttribute("data-user-role");
    viewReservationEmail.innerText = event.target.getAttribute("data-user-email");
    btn.click();
  }
}

// month arrow toggle
const allMonthArrows = Array.from(document.querySelectorAll(".month_arrow"));
function handleMonthArrowJs(event){
  // handle arrow smoth with same buttons to keep the results easy and less query parameters
  const targetMonth = event.target.getAttribute("data-month");
  if (targetMonth){
    const targetForm = document.querySelector(`.month_form[data-month='${targetMonth}']`);
    if (targetForm){
      targetForm.submit();
    }
  }
}
allMonthArrows.forEach( (monthArrow)=>{
  monthArrow.addEventListener("click", handleMonthArrowJs);
});

// submit year form

const yearSelector = document.getElementById("year");
const yearForm = document.getElementById("year_select_form");
yearSelector.addEventListener("change", (event)=>{
  if (event.target.value){
    yearForm.submit();
    return false;
  }
})






const allViewEmptySlots = document.querySelectorAll(".view_empty_slot");

allViewEmptySlots.forEach( (viewEmptySlot)=>{
  viewEmptySlot.addEventListener("click",viewSlotHandler);
});

const viewSlotStartAt = document.querySelector("#view_slotstart_at");
const viewSlotendAt = document.querySelector("#view_slotend_at");
const slotIndexView = document.querySelector("#slot_indexview");
const periodDateslotView = document.querySelector("#period_dateslot_view");
const periodDescriptionViewslot = document.querySelector("#period_description_viewslot");
function viewSlotHandler(event){
  event.preventDefault();
  const slotBtnId = event.target.getAttribute("data-show-view");
  const slotBtn = document.getElementById(slotBtnId);
  if (slotBtn){
    viewSlotStartAt.innerText = event.target.getAttribute("data-start");
    viewSlotendAt.innerText = event.target.getAttribute("data-end");
    periodDateslotView.innerText = event.target.getAttribute("data-period-date");
    slotIndexView.innerText = event.target.getAttribute("data-slot-index");
    periodDescriptionViewslot.innerText = event.target.getAttribute("data-period-description");
    slotBtn.click();
  }
}








/* AJAX MAP new Reservation end */

/*  UX jquery for aside nav for style controller */
let body_max_width = 1;
/*  UX jquery for aside nav for style controller */
// fix some effect in resisze important Advanced UX small details
$(window).resize(function(){

  if ($(window).width() > 900 && document.body.style.width.trim() == '60%'){
    document.body.style.background = "lightgray";
    document.body.style.width = bigAsideWidth;
  }

  if ($(window).width() < 900 && document.body.style.width.trim() == '78%'){
    document.body.style.width = smallAsideWidth;

  }

});

function fit_body_for_aisde(){
  // add css class to body for nice ux
    document.body.style.overflow = "auto";
    if (toggAsside.classList.contains("active_toggle")){
      toggAsside.classList.remove("active_toggle");
      // sound effects
      playSound("#open_modal_sound");
    }
    //toggAsside.style.display = "";
    if (body_max_width == 1){
      body_max_width = 0;
      if ($(window).width() < 900){
        document.body.style.width = smallAsideWidth;
      } else {
        document.body.style.width = bigAsideWidth;
      }
      document.body.style.backgroundRepeat = "no-repeat";
      document.body.style.backgroundPosition = "right";
      document.body.style.backgroundAttachment = "fixed";
   }
   else{
      body_max_width = 1;
      document.body.style.width = "100%";
      document.body.style.background = "lightgray";
      // this for make small detail effect show scrollbar after 75mili from all animation end give it ncie ux

   }

}

const toggAsside = document.querySelector("div.toggle_asside");



// advanced styles for control UX and aside and animation
$(document).ready(function(){
  $(".toggle_asside").each(function(i, c){
    const magicBtn = $(this);
    $(magicBtn).click(function(event){
      //toggAsside.style.display = "none";
      if (!toggAsside.classList.contains("active_toggle")){
        toggAsside.classList.add("active_toggle");
      } else {
        return false;
      }
      if (body_max_width == 1){
        /* !!!!!!!!!!!!!!!! --- size of aside must set async with css  ---!!!!!!!!!!!!!! */
        document.body.style.width = bigAsideWidth;
        document.body.style.backgroundRepeat = "no-repeat";
        document.body.style.backgroundPosition = "right";
        document.body.style.backgroundAttachment = "fixed";
        // note this will cast first then the callback so it will will have auto when end 35 smaller alot than effect
        document.body.style.overflow = "hidden";
      } else {
        document.body.style.overflow = "hidden";
      }
      $("#aside_style_controller").slideToggle(null, 'swing', fit_body_for_aisde);
    });
  })

});

/* UX jquery for aside nav for style controller My WP */

const styleViewer = document.querySelector("#style_viewer");
// container
const enableEditModeMsg = document.querySelector("#enable_edit_modemsg");
const enableEditMode = document.querySelector("#enable_edit_mode");
const editModeContainer = document.querySelector("#edit_mode_container");
const the_bsContainersEditor = document.querySelector("#bs_containers_editor");
const containerCurentElmid = document.querySelector("#container_curent_elmid");
const containerCurentLastupdate = document.querySelector("#container_curent_lastupdate");

const containerCurentType = document.querySelector("#container_curent_type");
const containerCurentHtmlid = document.querySelector("#container_curent_htmlid");
const containerCurentHtmlclass = document.querySelector("#container_curent_htmlclass");
const containerCurentGroup = document.querySelector("#container_curent_group");

// element
const enableEditModeMsgElm = document.querySelector("#enable_edit_modemsg_elm");
const enableEditModeElm = document.querySelector("#enable_edit_mode_elm");
const editModeElm = document.querySelector("#edit_mode_elm");
const the_bsElementEditor = document.querySelector("#bs_element_editor");




const toggleStartContainers = (stauts)=>{
  if (stauts == 'on'){
    if (editModeContainer.classList.contains('invisible')){
      editModeContainer.classList.remove("invisible");
    }
    if (!editModeElm.classList.contains('invisible')){
      editModeElm.classList.add("invisible");
    }

  } else {
    if (editModeContainer.classList.contains('invisible')){
      editModeContainer.classList.remove("invisible");
    }
    if (editModeElm.classList.contains('invisible')){
      editModeElm.classList.remove("invisible");
    }
  }
};


const toggleStartElements = (stauts)=>{
  if (stauts == 'on'){
    if (editModeElm.classList.contains('invisible')){
      editModeElm.classList.remove("invisible");
    }
    if (!editModeContainer.classList.contains('invisible')){
      editModeContainer.classList.add("invisible");
    }

  } else {
    if (editModeElm.classList.contains('invisible')){
      editModeElm.classList.remove("invisible");
    }
    if (editModeContainer.classList.contains('invisible')){
      editModeContainer.classList.remove("invisible");
    }
  }
};



let editModeStatus = 'off';
let toggleContBtn = document.getElementById("enable_edit_mode");
function startEditMode(event, button=false){
  let currentElm  = null
  if (button){
    currentElm = toggleContBtn;
  } else if (event && !button) {
    currentElm = event.target;
  } else {
    return false;
  }


  if (editModeStatus == 'on'){
    enableEditModeMsg.innerText = "(OFF)";
    currentElm.innerText = "Start Edit Containers";
    removeStyleViewerEvent();
    toggleStartContainers('off');
    the_bsContainersEditor.style.display = "none";
    editModeStatus = 'off';
    emptyEditorsSelectors('container');
    styleViewer.innerHTML = '';
  } else if (editModeStatus == 'off') {
    enableEditModeMsg.innerText = "(ON)";
    currentElm.innerText = "Stop Edit Containers";
    addStyleViewerEvent();
    toggleStartContainers('on');
    the_bsContainersEditor.style.display = "block";
    the_bsElementEditor.style.display = "none";
    editModeStatus = 'on';
    styleViewer.innerHTML = '';
  } else if (editModeStatus == 'pause'){
    removeStyleViewerEvent();
    currentElm.innerText = "Resume";
    enableEditModeMsg.innerText = "(PAUSE)";
    editModeStatus = 'resume';
  } else if (editModeStatus == 'resume'){
    enableEditModeMsg.innerText = "(ON)";
    currentElm.innerText = "Stop Edit Containers";
    removeStyleViewerEvent();
    addStyleViewerEvent();
    the_bsContainersEditor.style.display = "block";
    the_bsElementEditor.style.display = "none";
    emptyEditorsSelectors('container');
    editModeStatus = 'on';
  } else {
    return false;
  }
}



// elements edit mode

let editModeElmStatus = 'off';
let toggleElmBtn = document.getElementById("enable_edit_mode_elm");
function startEditModeElm(event, button=false){
  let currentElm  = null
  if (button){
    currentElm = toggleElmBtn;
  } else if (event && !button) {
    currentElm = event.target;
  } else {
    return false;
  }
  if (editModeElmStatus == 'on'){
    ///// here it was on and now go of
    // if editmode true so it was false and become true so time to close it
    enableEditModeMsgElm.innerText = "(OFF)";
    currentElm.innerText = "Start Edit Elements";
    removeStyleViewerEventElm();
    toggleStartElements('off');
    the_bsElementEditor.style.display = "none";
    editModeElmStatus = 'off';
    emptyEditorsSelectors('element');
    styleViewer.innerHTML = '';
  } else if (editModeElmStatus == 'off') {
    enableEditModeMsgElm.innerText = "(ON)";
    currentElm.innerText = "Stop Edit Elements";
    addStyleViewerEventElm();
    toggleStartElements('on');
    the_bsElementEditor.style.display = "block";
    the_bsContainersEditor.style.display = "none";
    editModeElmStatus = 'on';
  } else if (editModeElmStatus == 'pause') {
    removeStyleViewerEventElm();
    enableEditModeMsgElm.innerText = "(PAUSE)";
    currentElm.innerText = "Resume";
    editModeElmStatus = 'resume';

  } else if (editModeElmStatus == 'resume'){
    enableEditModeMsgElm.innerText = "(ON)";
    currentElm.innerText = "Stop Edit Elements";
    the_bsElementEditor.style.display = "block";
    the_bsContainersEditor.style.display = "none";
    addStyleViewerEventElm();
    emptyEditorsSelectors('element');
    editModeElmStatus = 'on';
  } else {
    return false;
  }
}

enable_edit_mode_elm.addEventListener("click", startEditModeElm);


let currentElmIdPHP = null;
let currentLastUpdate = null;
let currentCalIdEdit = null;
function mapObjectData(obj, type='container'){
  currentElmIdPHP = null;
  currentLastUpdate = null;
  for(const prop in obj) {
      if (!obj[prop]){continue;}
      const currentProp = prop.toLowerCase().trim();
      const value = obj[prop].toLowerCase().trim();
      if (currentProp == 'last_update'){
        currentLastUpdate = value;
        continue;
      }
      if (currentProp == 'element_id'){
        currentElmIdPHP = value;
        continue;
      }
      if (currentProp == 'cal_id'){
        currentCalIdEdit = value;
        continue;
      }
      if (currentProp == 'id'){
        currentCalIdEdit = value;
        continue;
      }
      // now it dynamic with async with php it select all unkowns selects so you can add milions like all frameworks for frontend and more options
      let x = selectNeededOption(prop, value, type);
  }
  return {cal_id: currentCalIdEdit, elm_id: currentElmIdPHP, last_update: currentLastUpdate};
}

let currentDataObj = {};
// this for both element and container
async function startEditContainer(e) {
  emptyAllBSInputs();
  // only alow edit or load container/element  data if not edit container on
  if (editModeStatus != 'on' && editModeElmStatus != 'on'){return false;}

  e.preventDefault();
  // element id not bs container
  const elmId = e.target.getAttribute("data-bs-id");
  const elmType = e.target.getAttribute("data-editor-type");
  const elmGroup = e.target.getAttribute("data-editor-group");
  const elmHTMLID = e.target.getAttribute("id");
  const elmHTMLClass = e.target.getAttribute("data-editor-class");

  //containerInViewData['last_update'] =
  if (!elmType || !elmId){return false;}

  // get container bs classes for selectors ultra dynamic
  if (elmType == 'container'){
    //do something differant context menu
    const bsContainerData = await postData('',{bs_container_load_elmid:elmId});

    // incase unknown problem like calendar open unavail years that not happend without break db and code but when it handled friendly
    if (!bsContainerData || bsContainerData.code != 200 || !bsContainerData.data){
      console.log("error", bsContainerData);
      return false;
    }

    // load the new container bs classes on the selector do not forget in begning I do not use this bs container table and I deal with string
    const currentEditBsData = mapObjectData(bsContainerData.data, 'container');
    currentDataObj = currentEditBsData;

    if (elmId){
      containerInViewData['elm_id'] = elmId;
    } else {
      containerInViewData['elm_id'] = '';
    }
    if (elmType){
      containerInViewData['type'] =  elmType;
    } else {
      containerInViewData['type'] =  '';
    }
    if (elmGroup){
      containerInViewData['group'] = elmGroup;
    } else {
      containerInViewData['group'] =  '';
    }
    if (elmHTMLID){
      containerInViewData['html_id'] = elmHTMLID;
    } else {
      containerInViewData['html_id'] =  '';
    }
    if (elmHTMLClass){
      containerInViewData['html_class'] = elmHTMLClass;
    } else {
      containerInViewData['html_class'] =  '';
    }

    if (currentEditBsData.last_update){
      containerInViewData['last_update'] = currentEditBsData.last_update;
    } else {
      containerInViewData['last_update'] = '';
    }

    if (currentEditBsData.id){
      containerInViewData['bsid'] = currentEditBsData.id;
    } else {
      containerInViewData['bsid'] = '';
    }



    containerCurentLastupdate.innerText = containerInViewData['last_update'];
    containerCurentElmid.innerText = containerInViewData['elm_id'];
    containerCurentType.innerText = containerInViewData['type'];
    containerCurentHtmlid.innerText = containerInViewData['html_id'];
    containerCurentHtmlclass.innerText = containerInViewData['html_class'];
    containerCurentGroup.innerText = containerInViewData['group'];
    const checkGroupIdelm = 'group_' + containerInViewData['elm_id'];
    if (containerInViewData['group']){
      applyOnGroup.innerHTML = `<label>Group Apply</label>  <input id="${checkGroupIdelm}" data-value="off"  type="checkbox" class="applyongroup">`;
      const checkContainer = document.getElementById(`${checkGroupIdelm}`);
      if (checkContainer){
        checkContainer.addEventListener("change",(ev)=>{
          if (ev.target.nodeName.toLowerCase() == 'input'){
            const currentValue = ev.target.getAttribute("data-value");
            if (currentValue && currentValue.toLowerCase() == 'on'){
              ev.target.setAttribute("data-value", 'off');
            } else {
              ev.target.setAttribute("data-value", 'on');
            }
          }
        });
      }
    } else {
      applyOnGroup.innerHTML = '';
    }

    // pause the element in the view to keep it image and not intrupt user with good ux way he will now not need alot of descion and go to hidden setting option to add something

    if (editModeStatus == 'on'){
      editModeStatus = 'pause';
      startEditMode(null, true);
    }

    return true;
  } else {


    const bsElementData = await postData('',{bs_element_load_elmid:elmId});
    if (!bsElementData || bsElementData.code != 200 || !bsElementData.data){
      console.log("error", bsElementData);
      return false;
    }
    // load the new container bs classes on the selector do not forget in begning I do not use this bs container table and I deal with string
    const currentEditBsData = mapObjectData(bsElementData.data, 'element');
    currentDataObj = currentEditBsData;

    if (elmId){
      containerInViewData['elm_id'] = elmId;
    } else {
      containerInViewData['elm_id'] = '';
    }
    if (elmType){
      containerInViewData['type'] =  elmType;
    } else {
      containerInViewData['type'] =  '';
    }
    if (elmGroup){
      containerInViewData['group'] = elmGroup;
    } else {
      containerInViewData['group'] =  '';
    }
    if (elmHTMLID){
      containerInViewData['html_id'] = elmHTMLID;
    } else {
      containerInViewData['html_id'] =  '';
    }
    if (elmHTMLClass){
      containerInViewData['html_class'] = elmHTMLClass;
    } else {
      containerInViewData['html_class'] =  '';
    }

    if (currentEditBsData.last_update){
      containerInViewData['last_update'] = currentEditBsData.last_update;
    } else {
      containerInViewData['last_update'] = '';
    }

    if (currentEditBsData.elm_id){
      containerInViewData['id'] = currentEditBsData.id;
    } else {
      containerInViewData['id'] = '';
    }

    if (currentEditBsData.cal_id){
      containerInViewData['calid'] = currentEditBsData.cal_id;
    } else {
      containerInViewData['calid'] = '';
    }

    // elm and cont
    containerCurentLastupdate.innerText = containerInViewData['last_update'];
    containerCurentElmid.innerText = containerInViewData['elm_id'];
    containerCurentType.innerText = containerInViewData['type'];
    containerCurentHtmlid.innerText = containerInViewData['html_id'];
    containerCurentHtmlclass.innerText = containerInViewData['html_class'];
    containerCurentGroup.innerText = containerInViewData['group'];
    const checkGroupId = 'group_' + containerInViewData['elm_id'];
    if (containerInViewData['group'] != ''){
            applyOnGroup.innerHTML = `<label>Group Apply</label>  <input id="${checkGroupId}" data-value="off"  type="checkbox" class="applyongroup">`;
            const checkElem = document.getElementById(`${checkGroupId}`);
            if (checkElem){
              checkElem.addEventListener("change",(ev)=>{
                if (ev.target.nodeName.toLowerCase() == 'input'){
                  const currentValue = ev.target.getAttribute("data-value");
                  if (currentValue && currentValue.toLowerCase() == 'on'){
                    ev.target.setAttribute("data-value", 'off');
                  } else {
                    ev.target.setAttribute("data-value", 'on');
                  }
                }
              });
            }
    } else {
      applyOnGroup.innerHTML = '';
    }


    // pause part
    if (editModeElmStatus == 'on'){
      editModeElmStatus = 'pause';
      startEditModeElm(null, true);
    }
    return true;

  }

}


/* Style Editor start */
const allElements = document.querySelectorAll("[data-editor-type='element']");
const allContainers = document.querySelectorAll("[data-editor-type='container']");
const setupLoadedContainers = document.querySelector("#setup_loaded_containers");
const setupLoadedElements = document.querySelector("#setup_element_btn");


const calidStyle = document.querySelector("#calid_editor_style");

// send container data ajax
async function sendContainerData(){
  const containersData = [];
  const notSavedConts = document.querySelectorAll(".not_saved[data-editor-type='container']");
  notSavedConts.forEach((cont)=>{
      let dataGroup = '';
      if (cont.hasAttribute("data-editor-group")){
        dataGroup = cont.getAttribute("data-editor-group");
      }
      containerObj = {
        element_id: cont.getAttribute("id"),
        html_class: cont.getAttribute("data-editor-class"),
        data_group: dataGroup,
        c_cal_id: calidStyle.value
      };
      containersData.push(containerObj);
    });
  toggleEditorWait(true);
  const serverResponse = await postData('',{setup_containers: containersData});
  toggleEditorWait(false);
  if (serverResponse.hasOwnProperty('code') && serverResponse.hasOwnProperty('total') && serverResponse.code == 200){
    const messageR = serverResponse.message  + ' Total: ' + serverResponse.total + ' Will restart after 5 seconds to load the new style';
    displayAjaxEditorMsg(messageR, 'success');
    setTimeout(()=>{
      location.reload();
    }, 5000);
    return true;
  } else {
    displayAjaxEditorMsg(serverResponse.message, 'danger');
    return false;
  }
}

setupLoadedContainers.addEventListener("click", sendContainerData);
// elements data ajax
// send container data ajax
let element_counter = 7;

async function sendElementData(){
  const elementObjects = [];
  const notSavedConts = document.querySelectorAll(".not_saved_element[data-editor-type='element']");
  notSavedConts.forEach((cont)=>{
      let dataGroup = '';
      if (cont.hasAttribute("data-editor-group")){
        dataGroup = cont.getAttribute("data-editor-group");
      }
      elemObject = {
        element_id: cont.getAttribute("id"),
        html_class: cont.getAttribute("data-editor-class"),
        data_group: dataGroup,
        c_cal_id: calidStyle.value
      };
      elementObjects.push(elemObject);
    });
  toggleEditorWait(true);
  const serverResponse = await postData('',{setup_elements: elementObjects});
  toggleEditorWait(false);
  if (!serverResponse){
    displayAjaxEditorMsg('Unkown Error', 'danger');
    return false;
  }
  if (serverResponse.hasOwnProperty('code') && serverResponse.hasOwnProperty('total') && serverResponse.code == 200){
    element_counter = 7;
    let servMessage = serverResponse.message  + ' Total: ' + serverResponse.total  + ' Will restart after 5 seconds to load the new style';
    displayAjaxEditorMsg(servMessage, 'success');
    setTimeout(()=>{
      location.reload();
    }, 7000);
    return true;
  } else {
    displayAjaxEditorMsg(serverResponse.message, 'danger');
    return false;
  }
}

setupLoadedElements.addEventListener("click", sendElementData);

enableEditMode.addEventListener("click", startEditMode);

/* view element in viewer */
const updateElementInView = ()=>{
  styleViewer.innerHTML = '';
  const newViewChildCont = document.createElement("div");
  newViewChildCont.classList.add("container-fluid", "w-100", "obj_in_view");
  newViewChildCont.innerHTML = elementInView.outerHTML;
  styleViewer.appendChild(newViewChildCont);
}
let elementInView = null;
function loadContainerInView(event){
  if (elementInView){
    // remove old event
    elementInView.removeEventListener("contextmenu", startEditContainer);
  }



  elementInView = event.target;
  // add event to the new inview element smoothly
  elementInView.addEventListener("contextmenu", startEditContainer);
  updateElementInView();

}
const allLoadedContainers = document.querySelectorAll("div[data-editor-type='container']:not(.not_saved)");
const allLoadedContainersx = document.querySelectorAll("[data-editor-type]:not([data-bs-id])");

// this for good preformance as it only make this heavy event when edit on and remove it total not only return it false when edit of
function addStyleViewerEvent(){
  allLoadedContainers.forEach( (loadedCont)=>{
    loadedCont.addEventListener("mouseenter", loadContainerInView);
  });
}

function removeStyleViewerEvent(){
  allLoadedContainers.forEach( (loadedCont)=>{
    loadedCont.removeEventListener("mouseenter", loadContainerInView);
  });
}


const allLoadedElements = document.querySelectorAll("[data-editor-type='element']:not(.not_saved_element)");

function addStyleViewerEventElm(){
  allLoadedElements.forEach( (loadedElm)=>{
    loadedElm.addEventListener("mouseenter", loadContainerInView);
  });
}

function removeStyleViewerEventElm(){
  allLoadedElements.forEach( (loadedElm)=>{
    loadedElm.removeEventListener("mouseenter", loadContainerInView);
  });
}





/* view element in viewer end */

/* style editor end */
// get message from php bs function to know the undefined elements
function loadUndefinedContainerScore(setupBtnselector, targetTxtid, targetSelector, type){
  const setupBtn = document.querySelector(setupBtnselector);
  const undefinedContTxt = document.querySelector(targetTxtid);
  const totalUndefined = document.querySelectorAll(targetSelector).length;
  let appStatusColor = 'bg-primary';
  if (totalUndefined == 0){ appStatusColor = 'bg-success'; }
  if (totalUndefined > 0 && totalUndefined < 100 ){ appStatusColor = 'bg-primary'; }
  if (totalUndefined > 100 && totalUndefined < 700 ){ appStatusColor = 'bg-warning'; }
  if (totalUndefined > 700 ){ appStatusColor = 'bg-danger'; }
  undefinedContTxt.innerText = totalUndefined;
  if (!undefinedContTxt.classList.contains('bg-primary')){
    undefinedContTxt.classList.remove('bg-primary');
  }
  if (!undefinedContTxt.classList.contains('bg-success')){
    undefinedContTxt.classList.remove('bg-success');
  }
  if (!undefinedContTxt.classList.contains('bg-primary')){
    undefinedContTxt.classList.remove('bg-primary');
  }
  if (!undefinedContTxt.classList.contains('bg-warning')){
    undefinedContTxt.classList.remove('bg-warning');
  }
  if (!undefinedContTxt.classList.contains('bg-danger')){
    undefinedContTxt.classList.remove('bg-danger');
  }
  undefinedContTxt.classList.add(appStatusColor);

  if (totalUndefined > 0){
    setupBtn.style.display = "block";
  }

  if (totalUndefined == 1){
    const messageTxt = type == 'container' ? 'Setup This Container' : 'Setup This Element';
    setupBtn.innerText = messageTxt;
  } else {
    const messageTxt = type == 'container' ? 'Setup These Containers' : 'Setup These Elements';
    setupBtn.innerText = messageTxt;
  }

  if (totalUndefined == 0){
    editModeContainer.style.display = "block";
  }
}

loadUndefinedContainerScore("#setup_loaded_containers", "#total_undefined_containers", ".not_saved[data-editor-type='container']");
loadUndefinedContainerScore("#setup_element_btn", "#total_undefined_elements_txt", ".not_saved_element[data-editor-type='element']");

/* edit bs styles event container */
/* this advanced ES6 function with async with php code to result in fastest dyanmic easy edit toons of unkown select box but follow the creation rules like wordpress if u not create element like needed it will not accept even here it will handled better if some small issues */
function selectNeededOption(column_name, value, type='container'){
  let currentSelect = document.querySelector(`div#bs_containers_editor select[name='${column_name}']`);
  if (type == 'element'){
    currentSelect = document.querySelector(`div#bs_element_editor select[name='${column_name}']`);
  }
  if (!currentSelect){return false;}
  const selectOptions = Array.from(currentSelect.options);
  selectOptions.forEach( (opRemove)=>{
    if (opRemove.hasAttribute("selected")){
      opRemove.removeAttribute("selected");
    }
  });
  for (let o=0; o<selectOptions.length; o++){
    const lowerOpValue = selectOptions[o].value.toLowerCase().trim();
    const lowerTargetValue = value.toLowerCase().trim();
    if ( lowerOpValue === lowerTargetValue ){
      if (!selectOptions[o].hasAttribute("selected")){
        selectOptions[o].setAttribute("selected", true);
      }
      break;
    }
  }
}
//selectNeededOption('bg', 'bg-success');
//selectNeededOption('text_color', 'text-info');


/*
let select1 = document.querySelector("div#bs_containers_editor select[name='bg']");
let select2 = document.querySelector("div#bs_containers_editor select[name='text_color']");
selectNeededOption(select, value)
selectNeededOption(select1, 'bg-info');
selectNeededOption(select2, 'text-muted');

*/

const phpNormalCalId = document.getElementById("calid_editor_style").value;
const allBsContainerSelectors = document.querySelectorAll(".edit_bs_select");
async function updateBsContainerColumn(event){
  let newBsValue = event.target.value;
  const columnName = event.target.getAttribute("name");
  const currentElm = elementInView;
  const currentBSID = currentElm.getAttribute("data-bs-id");
  const currentGroup = currentElm.getAttribute("data-editor-group");

  // this update for js and html to get the value dynamic of the current checkgroup input so u can just apply only single or on group
  const groupStatusInputId = 'group_'+currentBSID;
  const currentCheckBox = document.querySelector(`input#${groupStatusInputId}`);
  let updateOnGroup = 'off';
  if (currentCheckBox){
    const getUpdateGroupStatus = currentCheckBox.getAttribute("data-value");
    if (getUpdateGroupStatus.toLowerCase() == 'on'){
      updateOnGroup = 'on';
    } else {
      updateOnGroup = 'off';
    }
  }

  if (!currentBSID || !currentElm){return false;}
  if (newBsValue){
    newBsValue = newBsValue.trim();
  }

  const updateRequestData = {updateBsId: currentBSID, updateBsname: columnName.trim(), updateBSvalue: newBsValue, updateBSGroupStatus: updateOnGroup, updateBSGroup: currentGroup};
  const updateBSResponse = await postData('',updateRequestData);

  if (!updateBSResponse || !updateBSResponse.code || !updateBSResponse.data){
    displayAjaxEditorMsg('can not update unkown error', type='danger');
    return false;
  }

  if (updateBSResponse.code == 200){
    //data-editor-group
    if (!updateBSResponse.data){
      displayAjaxEditorMsg('could not update the container data', type='danger');
      return false;
    }
    if (updateBSResponse.data.group_on && updateBSResponse.data.group){
      // update group html bs togther
      updateBSClassesGroup(updateBSResponse.data.old, updateBSResponse.data.new, updateBSResponse.data.group);
      displayAjaxEditorMsg('updated containers successfully', type='success');
      updateElementInView();
      return true;
    } else {
      updateElmBSClasses(updateBSResponse.data.old, updateBSResponse.data.new, currentElm);
      displayAjaxEditorMsg('updated container successfully', type='success');
      updateElementInView();
      return true;
    }
  } else {
    displayAjaxEditorMsg(updateBSResponse.message, type='danger');
    return false;
  }


}

allBsContainerSelectors.forEach( (contSelect)=>{
  contSelect.addEventListener("change", updateBsContainerColumn);
});




async function updateThisBsElement(event){
  let newBsValue = event.target.value;
  const columnName = event.target.getAttribute("name");
  const currentElm = elementInView;
  const currentBSID = currentElm.getAttribute("data-bs-id");
  const currentGroup = currentElm.getAttribute("data-editor-group");

  // this update for js and html to get the value dynamic of the current checkgroup input so u can just apply only single or on group
  const groupStatusInputId = 'group_'+currentBSID;
  const currentCheckBox = document.querySelector(`input#${groupStatusInputId}`);
  let updateOnGroup = 'off';
  if (currentCheckBox){
    const getUpdateGroupStatus = currentCheckBox.getAttribute("data-value");
    if (getUpdateGroupStatus.toLowerCase() == 'on'){
      updateOnGroup = 'on';
    } else {
      updateOnGroup = 'off';
    }
  }
  if (!currentBSID || !currentElm){return false;}
  if (newBsValue){
    newBsValue = newBsValue.trim();
  }

  const updateRequestData = {updateElmBsId: currentBSID, updateElmBsname: columnName.trim(), updateElmBSvalue: newBsValue, updateElmGroupStatus: updateOnGroup, updateElmGroup: currentGroup};
  const updateBSResponse = await postData('',updateRequestData);

  if (!updateBSResponse || !updateBSResponse.code || !updateBSResponse.data){
    displayAjaxEditorMsg('can not update unkown error', type='danger');
    return false;
  }

  if (updateBSResponse.code == 200){
    if (!updateBSResponse.data){
      displayAjaxEditorMsg('could not update the element data', type='danger');
      return false;
    }
    if (updateBSResponse.data.group_on && updateBSResponse.data.group && updateBSResponse.data.group_on == true){
      // update group html bs togther
      updateBSClassesGroup(updateBSResponse.data.old, updateBSResponse.data.new, updateBSResponse.data.group);
      displayAjaxEditorMsg('updated elements by group successfully', type='success');
      updateElementInView();
      return true;
    } else {
      updateElmBSClasses(updateBSResponse.data.old, updateBSResponse.data.new, currentElm);
      displayAjaxEditorMsg('updated element successfully', type='success');
      updateElementInView();
      return true;
    }

  } else {
    displayAjaxEditorMsg(updateBSResponse.message, type='danger');
    return false;
  }
  //console.log(updateBSResponse);
}
const allBsElementsSelectors = document.querySelectorAll(".edit_bs_selectelm");
allBsElementsSelectors.forEach( (elmSelect)=>{
  elmSelect.addEventListener("change", updateThisBsElement);
});

const backFactoryBtn = document.getElementById("back_bs_to_default");
async function backBStoDefault(event){
  if (!phpNormalCalId){
    return false;
  }
  const updateBSResponse = await postData('',{backbs_default_id: phpNormalCalId});
  if (!updateBSResponse || !updateBSResponse.code || !updateBSResponse.message){
    displayAJAXRestMsg('danger', 'Could not reset calendar unkown error', 'Crtical Error',display=1);
    return false;
  }
  if (updateBSResponse.code == 200){
    displayAJAXRestMsg('success', 'Reset calendar bootstrap styles classes successfully the page will restart after 5 seconds you can restart it now', 'Success',display=1);
    setTimeout(()=>{
      location.reload();
    }, 5000);
    return true;
  } else {
    displayAJAXRestMsg('warning', updateBSResponse.message, 'Warning',display=1);
    return false;
  }
}
backFactoryBtn.addEventListener("click",backBStoDefault);

// update elements background color

async function updateElementBgColor(event){
  if (!currentDataObj){return false}
  if (!currentDataObj.elm_id){return false;}
  const newColor = event.target.value;
  if (!newColor){return false;}
  if (!elementInView){return false;}

  const currentElm = elementInView;
  const currentElmGroup = currentElm.getAttribute("data-editor-group");
  let groupOn = false;
  let group = false;
  if (currentElmGroup){
    const idToUpdate = currentElm.getAttribute("data-bs-id");
    const groupOnCheck = document.getElementById(`group_${idToUpdate}`);
    if (groupOnCheck){
      const currentElmGroupOn = groupOnCheck.getAttribute("data-value");
      if (currentElmGroupOn == 'on' && currentElmGroup){
        groupOn = true;
        group = currentElmGroup;
      } else {
        groupOn = false;
        group = false;
      }
    }
  }
  // send request to update bg color style
  const currentElmid = currentDataObj.elm_id;
  const bgCss = 'background: ' + newColor + ';';
  elementInView.style.background = newColor + '!important';
  const updateElmBgData = await postData('',{styleUpdateElmid:currentElmid,styleUpdateGroupOn:groupOn, styleUpdateGroup:group, styleUpdatebg: bgCss});
  if (!updateElmBgData || !updateElmBgData.code || !updateElmBgData.data){
    displayAjaxEditorMsg('could not update background unkown error', type='danger');
    return false;
  }
  if (updateElmBgData.code == 200 && updateElmBgData.data){
    if (!updateElmBgData.data.bg){
      displayAjaxEditorMsg('could not update background background not found', type='danger');
      return false;
    }
    const datagroup = updateElmBgData.data.group;
    const isGroupOn = updateElmBgData.data.group_on;
    if (isGroupOn && datagroup && group){
      if (event.target.value && updateElmBgData.data.bg){
        const getAllGroup = document.querySelectorAll(`[data-editor-group='${group}']`);
        getAllGroup.forEach( (elm)=>{
          elm.style.background = event.target.value;
          return true;
        });
        updateElementInView();
      } else {
        displayAjaxEditorMsg('could not update background background invalid', type='danger');
        return false;
      }
    } else {
      if (event.target.value && updateElmBgData.data.bg){

        displayAjaxEditorMsg('updated background successfully', type='success');
        updateElementInView();
      } else {
        displayAjaxEditorMsg('could not update background background invalid', type='danger');
        return false;
      }
    }

  }

}


const elementBgColorChanger = document.getElementById('sys_elm_bg');
if (elementBgColorChanger){
  elementBgColorChanger.addEventListener("change", updateElementBgColor);
}

});
