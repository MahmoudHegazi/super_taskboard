window.addEventListener('DOMContentLoaded', (event) => {

async function postData (url = '', data = {}) {

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
    console.log('Data Recived: ', newData)
    return newData;
  } catch (error) {
    console.log("error", error);
  }

};

function formatDate(adate){
  if (adate){
    formattedDate = new Date(adate);
    let day = formattedDate.getDate();
    let month= formattedDate.getMonth() + 1;
    let year=  formattedDate.getFullYear();
    month = (month < 10) ? "0"+ month : month;
    day = (day < 10) ? "0"+ day : day;
    date = `${year}-${month}-${day}`;
    return date
  } else {
    return formattedDate;
  }
}

});
