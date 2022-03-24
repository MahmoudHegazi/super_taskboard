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

  function add_required(event){
    const isoneNo = event.target.value;
    const allRequireds = document.querySelectorAll(".add_required_select");
    allRequireds.forEach( (reqSelect, index)=>{
      if (!isoneNo){
        if (index == 0){
          reqSelect.setAttribute('selected', true);
        }
        if (reqSelect.hasAttribute('required')){
          reqSelect.removeAttribute('required');
        }
      } else {
        if (!reqSelect.hasAttribute('required')){
          reqSelect.setAttribute('required', true);
        }
      }
    });
  }



  function get_last_index_dynamic(titles){
   if (titles.length < 1){
     return 1;
   }
   // np string will be unique
   const current_indexs_nums = [];
   let max = 1;
   titles.forEach( (title)=>{
     const splited_title = title.split("_");

     if (splited_title.length > 0){
       const target_numb = splited_title[splited_title.length-1];
       if (!isNaN(parseInt(target_numb))){
         current_indexs_nums.push(Number(target_numb));
       }
     }
   });
   if (current_indexs_nums.length < 1){
     return 1;
   } else {
     return Math.max(...current_indexs_nums)+1;
   }

}

  let proprties_index1 = 1;
  function getCSSPerioprtiesFormPeriod(data,periodIndex){
    let cssRulesContainers = '';
    data.forEach( (cssRule, index)=>{
      const pauseOrActive = cssRule.active == '1' ? 0 : 1;
      const currentActionBtn = cssRule.active == '1' ?'<i class="fa fa-pause"></i>' : '<i class="fa fa-play"></i>';
      cssRulesContainers += `
      <div class="slot_styles_container border border-secondary m-1 p-1 hover_cssrule">
         <h5 class="badge bg-success">${cssRule.title}</h5><br />
         <code>${cssRule.style}</code><br />
         <span class="pl-1 ml-1" title="Remove CSS rule" style="cursor:pointer;">
           <form style="display:inline;" action="controllers/setup_controller.php" method="POST">
             <input name="period_cremove_style_calid"  value="${cssRule.cal_id}" style="display:none;">
             <input name="period_cremove_style_title"  value="${cssRule.title}" style="display:none;">
             <input name="period_cremove_style_classname"  value="${cssRule.classname}" type="hidden" style="display:none;">
             <input name="period_cremove_style_index"  value="${periodIndex}" type="hidden" style="display:none;">
             <input class="bg-danger border border-light rounded  text-white" type="submit" value="X" />
           </form>
         </span>
         <span class="pl-1 ml-1" title="Pause/Enable CSS rule" style="cursor:pointer;">
             <form style="display:inline;"  action="controllers/setup_controller.php" method="POST">
             <input name="period_cpause_style_active"  value="${pauseOrActive}" style="display:none;">
             <input name="period_cpause_style_calid"  value="${cssRule.cal_id}" style="display:none;">
             <input name="period_cpause_style_title"  value="${cssRule.title}" style="display:none;">
             <input name="period_cpause_style_classname"  value="${cssRule.classname}" style="display:none;">
             <input name="period_cpause_style_index"  value="${periodIndex}" type="hidden" style="display:none;">
             <button class="bg-primary border border-light rounded text-white" type="submit">
               ${currentActionBtn}
             </button>
           </form>
         </span>

         <span class="pl-1 ml-1" title="Edit CSS rule" style="cursor:pointer;">
           <button data-index="period_${cssRule.id}"
             class="bg-warning border border-light rounded text-white" type="buttom"
             onclick="toggleEditCustomCSS(event)"
             >
             <i class="fa fa-edit"></i>
           </button>
           <form data-visible="false"  action="controllers/setup_controller.php" method="POST"
             style="display:none;" class="edit_customcss_form" data-index="period_${cssRule.id}">
             <input name="period_cedit_sample_id"  value="${cssRule.id}" style="display:none;" required>
             <input name="period_cedit_style_calid"  value="${cssRule.cal_id}" style="display:none;" required>
             <input name="period_cedit_style_title"  value="${cssRule.title}" style="display:none;" required>
             <input name="period_cedit_style_classname"  value="${cssRule.classname}" style="display:none;" required>
             <input name="period_cedit_style_index"  value="${periodIndex}" type="hidden" style="display:none;">
             <div class="d-flex p-1">
               <div>
                 <input name="period_cedit_style_style"  value="${cssRule.style}" required>
               </div>
               <div>
                  <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i></button>
               </div>
            </div>
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
  function getCSSPerioprtiesFormSlot(data,slotIndex){
    let cssRulesContainers = '';
    data.forEach( (cssRule, index)=>{
      const pauseOrActive = cssRule.active == '1' ? 0 : 1;
      const currentActionBtn = cssRule.active == '1' ?'<i class="fa fa-pause"></i>' : '<i class="fa fa-play"></i>';

      cssRulesContainers += `
      <div class="slot_styles_container border border-secondary m-1 p-1 hover_cssrule">
         <h5 class="badge bg-info">${cssRule.title}</h5><br />
         <code>${cssRule.style}</code><br />
         <span class="pl-1 ml-1" title="Remove CSS rule" style="cursor:pointer;">
           <form style="display:inline;" action="controllers/setup_controller.php" method="POST">
             <input name="slot_cremove_style_calid"  value="${cssRule.cal_id}" style="display:none;" required>
             <input name="slot_cremove_style_title"  value="${cssRule.title}" style="display:none;" required>
             <input name="slot_cremove_style_index"  value="${slotIndex}" type="hidden" style="display:none;" required>
             <input name="slot_cremove_style_classname"  value="${cssRule.classname}" type="hidden" style="display:none;" required>
             <input class="bg-danger border border-light rounded  text-white" type="submit" value="X" />
           </form>
         </span>
         <span class="pl-1 ml-1" title="Pause/Enable CSS rule" style="cursor:pointer;">
             <form style="display:inline;"  action="controllers/setup_controller.php" method="POST">
             <input name="slot_cpause_style_active"  value="${pauseOrActive}" style="display:none;" required>
             <input name="slot_cpause_style_calid"  value="${cssRule.cal_id}" style="display:none;" required>
             <input name="slot_cpause_style_title"  value="${cssRule.title}" style="display:none;" required>
             <input name="slot_cpause_style_index"  value="${slotIndex}" type="hidden" style="display:none;" required>
             <input name="slot_cpause_style_classname"  value="${cssRule.classname}" style="display:none;" required>
             <button class="bg-primary border border-light rounded text-white" type="submit">
               ${currentActionBtn}
             </button>
           </form>
         </span>

         <span class="pl-1 ml-1" title="Edit CSS rule" style="cursor:pointer;">
           <button data-index="slot_${cssRule.id}"
             class="bg-warning border border-light rounded text-white" type="buttom"
             onclick="toggleEditCustomCSS(event)"
             >
             <i class="fa fa-edit" data-style-target="style_value_${cssRule.cal_id}" class="edit_style_inputc"></i>
           </button>
           <form data-visible="false"  action="controllers/setup_controller.php" method="POST" style="display:none;"
             class="edit_customcss_form" data-index="slot_${cssRule.id}">
             <input name="slot_cedit_sample_id"  value="${cssRule.id}" style="display:none;" required>
             <input name="slot_cedit_style_calid"  value="${cssRule.cal_id}" style="display:none;" required>
             <input name="slot_cedit_style_index"  value="${slotIndex}" type="hidden" style="display:none;" required>
             <input name="slot_cedit_style_title"  value="${cssRule.title}" style="display:none;" required>
             <input name="slot_cedit_style_classname"  value="${cssRule.classname}" style="display:none;" required>
             <div class="d-flex p-1">
               <div>
                 <input class="form-control" name="slot_cedit_style_style"  value="${cssRule.style}" required>
               </div>
               <div>
                  <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i></button>
               </div>
            </div>
           </form>
         </span>
      </div>
      `;
      proprties_index += 1;
    });
    return cssRulesContainers;
}


let current_main_calues = [];
let ok = 1;
function getMainCSS(main_styles, elm_index, type='periods'){

  const periodNames = {
    color_name:'main_color_periods',
    background_name:'main_background_periods',
    fontsize_name:'main_font_size_periods',
    fontfamily_name:'main_font_family_periods',
    bordersize_name:'main_border_size_periods',
    borderType_name:'main_border_type_periods',
    borderColor_name:'main_border_color_periods',
    main_css_calid:'main_css_calid_p',
    main_css_classname:'main_css_classname_p',
    main_css_title:'main_css_title_p',
    form_class:'form_main_period'
  };
  const slotNames = {
    color_name:'main_color_slots',
    background_name:'main_background_slots',
    fontsize_name:'main_font_size_slots',
    fontfamily_name:'main_font_family_slots',
    bordersize_name:'main_border_size_slots',
    borderType_name:'main_border_type_slots',
    borderColor_name:'main_border_color_slots',
    main_css_calid:'main_css_calid_s',
    main_css_classname:'main_css_classname_s',
    main_css_title:'main_css_title_s',
    form_class:'form_main_slot'
  };
  const currentNames = type == 'slots' ? slotNames : periodNames


  let mainCSSFormHTML = '';

  let colorValue = '#ffffff';
  let backgroundColor = '#3190d8';
  let fontFamily = '';
  let fontSize = '';
  let borderSize = '';
  let borderType = '';
  let borderColor = '';
  let code_id = 'code_data_0';
  let main_css_calid_value = '';
  let main_css_classname_value =  '';
  let main_css_title_value = '';
  let borderSizeOptions = '';
  let borderTypeOptions = '';
  let borderColorOptions = '';

  const randDefTitleIndex = Math.floor((Math.random() * 50) + (Math.random() * 100) + (Math.random() * 80));
  let colorTitle = 'color_title_'+randDefTitleIndex;
  let backgroundColorTitle = 'background_title_'+randDefTitleIndex;
  let fontFamilyTitle = 'fontfamily_title_'+randDefTitleIndex;
  let fontSizeTitle = 'fontSize_title_'+randDefTitleIndex;
  let borderTitle = 'border_title_'+randDefTitleIndex;


  main_styles.forEach( (mainStyle)=>{
    if ( mainStyle.category == 'color' ){
        colorL = mainStyle.style.split(":");
        if (colorL.length > 1){
          colorValue = colorL[1].trim().replace(";", "");

        }
      colorTitle = mainStyle.title;
    } else if ( mainStyle.category == 'backgroundcolor' ) {

        backgroundColorL = mainStyle.style.split(":");
        if (backgroundColorL.length > 1){
          backgroundColor = backgroundColorL[1].trim().replace(";", "");
        }
        backgroundColorTitle = mainStyle.title;

    } else if ( mainStyle.category == 'fontfamily' ) {
      fontFamily = mainStyle.style;
      fontFamilyTitle = mainStyle.title;

    } else if ( mainStyle.category == 'fontsize' ) {
      fontSize = mainStyle.style;
      fontSizeTitle = mainStyle.title;

    } else if ( mainStyle.category == 'border' ) {
      border = mainStyle.style;
      borderSplited = border.split(" ");
      if (border != '' && borderSplited.length == 3){
        borderSize = borderSplited[0];
        borderType = borderSplited[1];
        borderColor = borderSplited[2];
      }
      borderTitle = mainStyle.title;
    }
    code_id = mainStyle.id ?  'code_data_' + mainStyle.id : code_id;
    main_css_calid_value = mainStyle.cal_id ? mainStyle.cal_id : main_css_calid_value;

    main_css_classname_value = mainStyle.classname ? mainStyle.classname : main_css_classname_value;

   });


    /* border size */
    const currentBSizes = ['border:1px', 'border:2px', 'border:3px', 'border:4px', 'border:5px'];
    borderSizeOptions = '<option value="" selected>No Border</option>';
    currentBSizes.forEach( (sizeOption, index)=>{

        if (index > 0 && sizeOption == borderSize){
          borderSizeOptions += `<option value="${sizeOption}" selected>${sizeOption.split(":")[1]}</option>`;
        } else {
          borderSizeOptions += `<option value="${sizeOption}" >${sizeOption.split(":")[1]}</option>`;
        }
    });

    /* border type */
    const currentBTypes = ['solid', 'dotted', 'dashed', 'double', 'groove', 'ridge', 'inset', 'outset', 'mix'];
    borderTypeOptions = '<option value="" selected>No Border</option>';
    currentBTypes.forEach( (typeOption, bindex)=>{
      if (bindex > 0 && typeOption == borderType){
        borderTypeOptions += `<option value="${typeOption}" selected>${typeOption}</option>`;
      } else {
        borderTypeOptions += `<option value="${typeOption}" >${typeOption}</option>`;
      }
    });


    /* border color */
    const currentBColors = ['black;', 'white;', 'red;', 'green;', 'gold;', 'blue;', 'lightblue;'];
    borderColorOptions = '<option value="" selected>No Border</option>';
    currentBColors.forEach( (colorOption, index)=>{
      if (index > 0 && colorOption == borderColor){
        borderColorOptions += `<option value="${colorOption}" selected>${colorOption.slice(0,colorOption.length-1)}</option>`;
      } else {
        borderColorOptions += `<option value="${colorOption}" >${colorOption.slice(0,colorOption.length-1)}</option>`;
      }
    });
    mainCSSFormHTML += `



    <form onsubmit="handleMainCss(event, '${code_id}'), displayCalendarEditWait(event)" class="row ${currentNames.form_class}"  action="controllers/setup_controller.php" method="POST">
      <div class="col-sm-3">
         <label for="${currentNames.color_name}">Font Color</label>
         <input name="${currentNames.color_name}" id="${currentNames.color_name}"
         data-index="1" type="color" value="${colorValue}"
         class="form-control period_style_color1 main_css" data-i="0">
      </div>
      <div class="col-sm-3">
         <label for="${currentNames.background_name}">Background color</label>
         <input name="${currentNames.background_name}" id="${currentNames.background_name}"
          data-index="1" type="color" value="${backgroundColor}"
          class="form-control period_style_bgcolor1 main_css" data-i="1">
      </div>
      <div class="col-sm-3">
         <label for="${currentNames.fontfamily_name}">Font Family</label>
         <select name="${currentNames.fontfamily_name}"
         id="${currentNames.fontfamily_name}" data-index="1" class="form-control period_font_family1 main_css"
         data-i="2">
            <option value="" selected>Default</option>
            <option style="font-family:Georgia;" value="font-family:Georgia;" ${fontFamily == 'font-family:Georgia;' ? 'selected' : ''}>Georgia</option>
            <option style="font-family:Palatino Linotype;" value="font-family:Palatino Linotype;"  ${fontFamily == 'font-family:Palatino Linotype;' ? 'selected' : ''} >Palatino Linotype</option>
            <option style="font-family:Book Antiqua;" value="font-family:Book Antiqua;" ${fontFamily == 'font-family:Book Antiqua;' ? 'selected' : ''} >Book Antiqua</option>
            <option style="font-family:Times New Roman;" value="font-family:Times New Roman;" ${fontFamily == 'font-family:Times New Roman;' ? 'selected' : ''} >Times New Roman</option>
            <option style="font-family:Arial;" value="font-family:Arial;" ${fontFamily == 'font-family:Arial;' ? 'selected' : ''} >Arial</option>
            <option style="font-family:Helvetica;" value="font-family:Helvetica;" ${fontFamily == 'font-family:Helvetica;' ? 'selected' : ''} >Helvetica</option>
            <option style="font-family:Impact;" value="font-family:Impact;" ${fontFamily == 'font-family:Impact;' ? 'selected' : ''} >Impact</option>
            <option style="font-family:Lucida Sans Unicode;" value="font-family:Lucida Sans Unicode;" ${fontFamily == 'font-family:Lucida Sans Unicode;' ? 'selected' : ''} >Lucida Sans Unicode</option>
            <option style="font-family:Tahoma;" value="font-family:Tahoma;" ${fontFamily == 'font-family:Tahoma;' ? 'selected' : ''} >Tahoma</option>
            <option style="font-family:Verdana;" value="font-family:Verdana;" ${fontFamily == 'font-family:Verdana;' ? 'selected' : ''} >Verdana</option>
            <option style="font-family:Courier New;" value="font-family:Courier New;" ${fontFamily == 'font-family:Courier New;' ? 'selected' : ''} >Courier New</option>
            <option style="font-family:Lucida Console;" value="font-family:Lucida Console;" ${fontFamily == 'font-family:Lucida Console;' ? 'selected' : ''} >Lucida Console</option>
            <option style="font-family:initial;" value="font-family:initial;" ${fontFamily == 'font-family:initial;' ? 'selected' : ''} >initial</option>
         </select>
      </div>

      <div class="col-sm-3">
         <label for="${currentNames.fontsize_name}">Font Size</label>
         <select id="${currentNames.fontsize_name}" name="${currentNames.fontsize_name}" data-index="1"
         class="form-control period_fontsize1 main_css" data-i="3">
            <option value="" selected>Default</option>
            <option style="font-size: 8px;" value="font-size: 8px;" ${fontSize == 'font-size: 8px;' ? 'selected' : ''}>8px</option>
            <option style="font-size: 10px;" value="font-size: 10px;" ${fontSize == 'font-size: 10px;' ? 'selected' : ''}>10px</option>
            <option style="font-size: 12px;" value="font-size: 12px;" ${fontSize == 'font-size: 12px;' ? 'selected' : ''}>12px</option>
            <option style="font-size: 14px;" value="font-size: 14px;" ${fontSize == 'font-size: 14px;' ? 'selected' : ''}>14px</option>
            <option style="font-size: 16px;" value="font-size: 16px;" ${fontSize == 'font-size: 16px;' ? 'selected' : ''}>16px</option>
            <option style="font-size: 18px;" value="font-size: 18px;" ${fontSize == 'font-size: 18px;' ? 'selected' : ''}>18px</option>
            <option style="font-size: 1rem;" value="font-size: 1rem;" ${fontSize == 'font-size: 1rem;' ? 'selected' : ''}>1 rem</option>
            <option style="font-size: 1rem;" value="font-size: 1rem;" ${fontSize == 'font-size: 1rem;' ? 'selected' : ''}>1.5 rem</option>
            <option style="font-size: 0.825em;" value="font-size: 0.825em;" ${fontSize == 'font-size: 0.825em;' ? 'selected' : ''}>0.825 em</option>
            <option style="font-size: 0.925em;" value="font-size: 0.925em;" ${fontSize == 'font-size: 0.925em;' ? 'selected' : ''}>0.925 em</option>
            <option style="font-size: 1em;" value="font-size: 1em;" ${fontSize == 'font-size: 1em;' ? 'selected' : ''}>1 em</option>
            <option style="font-size: 1.5em;" value="font-size: 1.5em;" ${fontSize == 'font-size: 1.5em;' ? 'selected' : ''}>1.5 em</option>
            <option style="font-size: 2em;" value="font-size: 2em;" ${fontSize == 'font-size: 2em;' ? 'selected' : ''}>2 em</option>
            <option style="font-size: 1rem;" value="font-size: 1rem;" ${fontSize == 'font-size: 1rem;' ? 'selected' : ''}>2 rem</option>
         </select>
      </div>

      <div class="row">
         <div class="col-sm-3">
          <label for="${currentNames.bordersize_name}">Border Size</label>
          <select id="${currentNames.bordersize_name}" name="${currentNames.bordersize_name}" data-index="1"
          class="form-control period_border_part1_1 period_border1 main_css add_required_select"
          data-i="4">
               ${borderSizeOptions}
          </select>
         </div>
            <div class="col-sm-3">
               <label for="${currentNames.borderType_name}">Border Type</label>
               <select id="${currentNames.borderType_name}" name="${currentNames.borderType_name}" data-index="1"
               class="form-control period_border_part2_1 period_border1 main_css add_required_select"
               data-i="5">
                 ${borderTypeOptions}
               </select>
            </div>
            <div class="col-sm-3">
               <label for="${currentNames.borderColor_name}">Border Color</label>
               <select id="${currentNames.borderColor_name}" name="${currentNames.borderColor_name}"
               data-index="1"
               class="form-control period_border_part3_1 period_border1 main_css add_required_select" data-i="6">
                 ${borderColorOptions}
               </select>
            </div>
        </div>
        <div class="row mt-2 mb-1">
           <p class="alert alert-warning col-sm-12 text-center" data-code="${code_id}" style="display:none;"></p>
        </div>
        <div class="d-grid">
          <input type="hidden" name="${currentNames.main_css_calid}" style="display:none;" value="${main_css_calid_value}">
          <input type="hidden" name="${currentNames.main_css_classname}" style="display:none;" value="${main_css_classname_value}">

          <input type="hidden" name="main_css_title1" style="display:none;" value="${colorTitle}">
          <input type="hidden" name="main_css_title2" style="display:none;" value="${backgroundColorTitle}">
          <input type="hidden" name="main_css_title3" style="display:none;" value="${fontFamilyTitle}">
          <input type="hidden" name="main_css_title4" style="display:none;" value="${fontSizeTitle}">
          <input type="hidden" name="main_css_title5" style="display:none;" value="${borderTitle}">
          <button type="submit"  class="btn btn-primary" >Edit Main Styles</button>
        </div>
    </form>`;







  const maincss_values = `
  <code style="display:none;" id="${code_id}">${colorValue},${backgroundColor},${fontFamily},${fontSize},${borderSize},${borderType},${borderColor}</code>
  `;
  return mainCSSFormHTML + maincss_values;
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
              if (data.total_periods > 0){

                /* Periods Display */
                const periods = data.period_data;
                let periodsHTML = '';
                $("#periods_edit_title").text(`(${data.total_periods})`);
                $("#total_periods_strong").text(`(${data.total_periods})`);
                periods.forEach( (period, index)=>{
                  let periodEnd = period.period_end;
                  if (period.period_end == null) {
                    periodEnd = '';
                  }
                  periodsHTML += getPeriodHTMLText(period.period_index, period.period_date, period.description, period.id, data.cal_id, period.element_id, period.element_class, period.main_styles, period.custom_styles, periodEnd);
                });
                $('#modal_periods_container').html(periodsHTML);

                const allRequireds = document.querySelectorAll(".add_required_select");
                allRequireds.forEach( (selectBorder)=>{
                  selectBorder.addEventListener("change", add_required);
                });

              } else {
                $("#periods_edit_title").text('(0)');
              }


              if (data.total_slots > 0){

                /* Slots Display */
                const slots = data.slot_data;
                let slotsHTML = '';
                $("#slots_edit_title").text(`(${data.total_slots})`);
                $("#total_slots_strong").text(`(${data.total_slots})`);
                let period_switcher = 0;
                slots.forEach( (slot, index)=>{
                  if (data.total_periods % index == 0){
                    period_switcher = 1;
                  } else {
                    period_switcher += 1;
                  }
                  slotsHTML += getSlotHTMLText(slot.slot_index, slot.start_from, slot.end_at, slot.id, slot.cal_id, slot.element_id, slot.element_class, slot.main_styles, slot.custom_styles, period_switcher);
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


function getPeriodHTMLText(period_index, period_date, period_description, period_id, cal_id, element_id, element_class, mainCSS, customCSS, periodEnd){
  // start
  const formated_time = period_date;
  // period end
  const period_endtime = formated_time;

  const customStyle = getCSSPerioprtiesFormPeriod(customCSS, period_index);

  let customTitles = customCSS.map(customObj => customObj.title);
  const lastCustomIndex = get_last_index_dynamic(customTitles);
  const periodHtml = `
  <div class="container border border-secondary p-2 rounded">

  <div class="badge bg-primary">Period Index: ${period_index}</div>
    <form  class="period_editform" action="controllers/setup_controller.php" method="POST"
    onsubmit="displayCalendarEditWait(event)"
    >
        <div class="form-group text-start">
          <label for="add_new_year_edit">Period Start: </label>
          <input type="time" data-old-value="${formated_time}"
          value="${formated_time}"
          class="form-control js_edit_input js_edit_input_time"
          name="period_date_edit">

          <label for="add_new_year_edit">Period End: </label>
          <input type="time" data-old-value="${period_endtime}"
          value="${period_endtime}"
          class="form-control js_edit_input js_edit_input_time"
          name="period_date_end_edit">
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
            <a onclick="toggle_show_css(event)" class="btn btn-secondary show_more_css" data-status="show" data-bs-toggle="collapse" href="#show_period_css${period_index}">
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
                <h3>Main CSS</h3>
              </div>
              <!-- main css start -->
              <div class="container">
                ${getMainCSS(mainCSS, period_index, 'periods')}
              </div>
              <!-- main css end -->
              <div class="container text-center mt-2">
                 <h3>Custom CSS Rules</h3>
              </div>
              <div class="d-flex justify-content-left bg-light mb-3 p-2 flex-wrap">
                ${customStyle}
              </div>
              <div class="d-flex justify-content-center align-items-center border border-secondary">
                <div class="col-sm-10">
                  <form action="controllers/setup_controller.php" method="POST" method="POST">
                    <div class="form-group">
                      <div class="container">
                        <h3 class="text-center mt-1">Add CSS Custom Style Rule</h3>
                      </div>
                      <div class="form-group p-2">
                        <label for="custom_style_period_active">Active</label>
                        <input class="from-control" id="custom_style_period_active" name="custom_style_period_active" type="checkbox" title="If this is not checked, the custom style rule will be ignored" checked/>
                      </div>
                      <div class="form-group p-2">
                        <label for="custom_style_period_title">Title</label>
                        <input  class="form-control" value="custom_period_${lastCustomIndex}"
                          id="custom_style_period_title" name="custom_style_period_title" type="text"
                          title="title is id to set all periods custom css so it must be unique the system will generate unique custom style title for you"
                          required>
                      </div>
                      <div class="form-group p-2">
                        <label for="custom_style_period_style">CSS rules</label>
                        <input placeholder="color:gold !important;" class="form-control" id="custom_style_period_style" name="custom_style_period_style" type="text" required title="add single css rule or group separated by | wrong formated rules will be ignored" />
                      </div>



                    </div>
                    <div class="form-group mt-2 d-grid">
                      <input type="submit" value="Add Custom Rule" class="btn btm-block btn-primary">
                      <input type="hidden" value="${cal_id}" class="form-control"
                      name="period_add_calid" style="display:none;">
                      <input type="hidden" value="${lastCustomIndex}" class="form-control"
                      name="custom_period_newindex" style="display:none;" required>
                      <input placeholder="color:gold !important;" class="form-control"
                      value="${period_index}" id="custom_style_period_index" name="custom_style_period_index" type="hidden"
                      required style="display:none;" />
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
    <form id="delete_period_form" action="controllers/setup_controller.php" method="POST" onsubmit="displayCalendarEditWait(event)">

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



function getSlotHTMLText(slot_index, start_from, end_at, slot_id, cal_id, element_id, element_class, mainCSS, customCSS, period_id) {
  const customStyle = getCSSPerioprtiesFormSlot(customCSS, slot_index);
  let customTitles = customCSS.map(customObj => customObj.title);
  const lastCustomIndex = get_last_index_dynamic(customTitles);

  const slotHtml = `
  <div class="container border border-secondary p-2 rounded">
  <div class="badge bg-primary">Slot Index: ${slot_index}</div>
    <form action="controllers/setup_controller.php" method="POST" onsubmit="displayCalendarEditWait(event)">
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
            <div class="card-header">
              <a onclick="toggle_show_css(event)" class="btn btn-secondary show_more_css" data-status="show" data-bs-toggle="collapse"
                href="#show_slot_css${slot_index}">
                Show CSS
              </a>
            </div>
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
              <div class="container text-center mt-2">
                <h3>Main CSS</h3>
              </div>
              <!-- main css start -->
              <div class="container">
                ${getMainCSS(mainCSS, slot_index, 'slots')}
              </div>
              <!-- main css end -->
              <div class="container text-center mt-2">
                 <h3>Custom CSS Rules</h3>
              </div>
              <div class="d-flex flex-wrap bg-light mb-3 p-2 flex-wrap">
                ${customStyle}
              </div>
              <hr />
              <div class="d-flex justify-content-center align-items-center border border-secondary">
                <div class="col-sm-10 p-2">
                  <form action="controllers/setup_controller.php" method="POST" method="POST">
                    <div class="form-group">
                      <div class="container">
                        <h3 class="text-center mt-1">Add CSS Custom Style Rule</h3>
                      </div>
                      <div class="form-group p-2">
                        <label for="custom_style_period_active">Active</label>
                        <input class="from-control" id="custom_style_slot_active" name="custom_style_slot_active" type="checkbox" title="If this is not checked, the custom style rule will be ignored" checked/>
                      </div>
                      <div class="form-group p-2">
                        <label for="custom_style_period_title">Title</label>
                        <input  class="form-control" value="custom_slot_${lastCustomIndex}"
                          id="custom_style_slot_title" name="custom_style_slot_title" type="text"
                          title="title is id to set all periods custom css so it must be unique the system will generate unique custom style title for you"
                          required>
                      </div>
                      <div class="form-group p-2">
                        <label for="custom_style_period_style">CSS rules</label>
                        <input placeholder="text-align:center;|color:gold;" class="form-control" id="custom_style_slot_style" name="custom_style_slot_style" type="text" required title="add single css rule or group separated by | wrong formated rules will be ignored" />
                      </div>
                    </div>
                    <div class="form-group mt-2 d-grid">
                      <input type="submit" value="Add Custom Rule" class="btn btm-block btn-primary">
                      <input type="hidden" value="${cal_id}" class="form-control"
                      name="slot_add_calid" style="display:none;">
                      <input type="hidden" name="custom_slot_index" value="${slot_index}" class="form-control" style="display:none !important;">
                      <input type="hidden" value="${lastCustomIndex}" class="form-control"
                      name="custom_slot_newindex" style="display:none;">
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <!-- style end-->
    <!-- remove slot form -->
    <form id="delete_slot_form" action="controllers/setup_controller.php" method="POST" onsubmit="displayCalendarEditWait(event)">

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
