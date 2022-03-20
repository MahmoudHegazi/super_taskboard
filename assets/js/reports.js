
async function setupReportsData(url = '', data = {}) {

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
    //console.log("error", error);
  }
};

function getRandomRBGa(type='rgba'){
  let red = Math.floor((Math.random() * 256));
  let green = Math.floor((Math.random() * 256));
  let blue = Math.floor((Math.random() * 256));
  let alphaF1 = Math.floor((Math.random() * 10));
  let alphaF2 = Math.floor((Math.random() * 10));
  let alpha = 1;
  let isAlphaSmall = alphaF1.toString().length;
  if (alphaF1 == '0' && alphaF2 == '0'){
    alpha = 1 + '.' + '0';
    alpha = parseFloat(alpha).toFixed(1);
  } else {
    alpha = '0.' + (alphaF1 + '' + alphaF2);
    alpha = parseFloat(alpha).toFixed(2);
  }

  const randomRgb = `rgba(${red},${green},${blue})`;
  const randomRgbA = `rgba(${red},${green},${blue},${alpha})`;

  if (type == 'rgb'){
    return randomRgb;
  } else {
    return randomRgbA;
  }
  //alert(alphaF.toString().length);
  //alert(randomRgbA);
  return randomRgbA;
}

function generateRgbaList(count, type='rgba'){
  const rgbaList = [];
  for (let i=0; i<count; i++){
     rgbaList.push(getRandomRBGa(type));
  }
  return rgbaList;
}

function addPieLabel(elm_id, title='', color='black'){
  const canvCont = document.getElementById(elm_id);
  if (!canvCont){return false;}

  const normalLabel = document.createElement("div");
  normalLabel.classList.add('d-flex', 'justify-content-center', 'align-items-center');
  normalLabel.style.textAlign = 'center';
  normalLabel.id = 'label_for_' + elm_id;
  const newLabelBadge = document.createElement('div');
  newLabelBadge.classList.add('badge', 'd-flex', 'justify-content-center', 'align-items-center');
  newLabelBadge.innerText = title;
  newLabelBadge.style.color = color;
  normalLabel.appendChild(newLabelBadge);

  canvCont.insertBefore(normalLabel, canvCont.firstChild);
}



/* Chart 1 reservations per users */


const displayErrorAjaxMap = (error_msg,cont_d_id)=>{
  const mapErrorCont = document.querySelector(`[data-error-id='${cont_d_id}']`);
  if (!mapErrorCont){return false;}

  mapErrorCont.innerHTML = `
  <div class="alert alert-danger alert-dismissible fade show">
    <p>${error_msg}</p>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>`;
}

const assignDynamicColorsToDataSet = (currentData)=>{
  if (!currentData){return false;}
  let copyOfData = currentData;
  if (!currentData.datasets || currentData.datasets.length < 1){return false;}
  const allDatasets = currentData.datasets;
  for (let dset=0; dset<allDatasets.length; dset++){
    const singlDataset = allDatasets[dset];
    if (singlDataset.backgroundColor && singlDataset.borderColor && (singlDataset.backgroundColor == singlDataset.borderColor)){
      copyOfData.datasets[dset]['backgroundColor'] = generateRgbaList(singlDataset.backgroundColor, 'rgb');
      copyOfData.datasets[dset]['borderColor'] = generateRgbaList(singlDataset.borderColor, 'rgb');
    } else {
      // not valid i need labels same as data wrong in server sql
      continue;
    }
  }
  return copyOfData;
};

// this pure js function generate any type of chart easy with one call
// AJAX function that send load chart request to php controller who understood datasets and return the correct format, not iam using dynamic color generator to give always good results
async function loadAnyChartDataset(chart='reserv_per_users', canvas_id='reserv_per_users', chart_type='bar'){
  const usersResvData = await setupReportsData('',{chart: chart});
  if (!usersResvData && !usersResvData.code || !usersResvData.message || !usersResvData.labels){
    displayErrorAjaxMap('can not load chart unkown error',canvas_id);
  }

  if (usersResvData.code == 200 && usersResvData.title){
    const currentData = {
      labels: usersResvData.labels,
      datasets: usersResvData.datasets
    };



    const myDataSet = assignDynamicColorsToDataSet(currentData);
    const config3 = {
      type: chart_type,
      data: myDataSet,
      options: {}
    };

    const myChart3 = new Chart(
      document.getElementById(canvas_id),
      config3
    );
  } else {
    displayErrorAjaxMap(usersResvData.message,canvas_id);
    return false;
  }
}

// can load any ajax chart type and also switch types dynamic  first the ajax path in db (type) and second canvas id and last type can changed
loadAnyChartDataset('reserv_per_users', 'reserv_per_users', 'bar');
loadAnyChartDataset('reserv_per_year', 'reserv_per_year', 'line');
loadAnyChartDataset('reserv_per_month', 'reserv_per_month', 'line');
loadAnyChartDataset('reserv_per_periods', 'reserv_per_periods', 'doughnut');
loadAnyChartDataset('prefered_periods', 'perfered_user_period', 'bar');
loadAnyChartDataset('prefered_slots', 'perfered_user_slot', 'doughnut');
loadAnyChartDataset('reserv_top_period_slot', 'best_period_and_slot', 'doughnut');
loadAnyChartDataset('calendars_performance_review', 'calendars_performance_review', 'radar');


/*
  addPieLabel('doughnut_periods', 'Period Reservations', 'gray');
  const labels4 = [
    'Period 1',
    'Period 2',
    'Period 3',
  ];
const data4 = {
  labels: [
    'a',
    'b',
    'c'
  ],
  datasets: [{
    label: 'My First Dataset',
    data: [50, 50, 50],
    backgroundColor: generateRgbaList(3, 'rgb'),
    hoverOffset: 4
  }]

};
  const config4 = {
    type: 'doughnut',
    data: data4,
    options: {}
  };

    const myChart4 = new Chart(
    document.getElementById('reserv_per_periods'),
    config4
  );
*/
