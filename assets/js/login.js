
   const displayRememberMeCont = document.getElementById("display_remember_me");
   const toggleRememberMe = (remeberMeDisplayed)=> {
     if (remeberMeDisplayed == 1){
       displayRememberMeCont.style.display = "block";
     } else {
       displayRememberMeCont.style.display = "none";
     }
   };


   async function postLoginData(url = '', data = {}) {

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

   /* secuirty js some code for secuirty and logins details */
   function detectBrowser(userAgent) {
       // fast way to get choice from user agent
       if((userAgent.indexOf("Opera") || user_agent.indexOf('OPR')) != -1 ) {
           return 'Opera';
       } else if(userAgent.indexOf("Chrome") != -1 ) {
           return 'Chrome';
       } else if(user_agent.indexOf("Safari") != -1) {
           return 'Safari';
       } else if(user_agent.indexOf("Firefox") != -1 ){
           return 'Firefox';
       } else if((navigator.userAgent.indexOf("MSIE") != -1 ) || (!!document.documentMode == true )) {
           return 'IE';//crap
       } else {
           return 'Unknown';
       }
   }

   function detectOS(appVersion){
       if (appVersion.indexOf("Win") != -1){
         return "Windows OS";
       } else if (appVersion.indexOf("Mac") != -1) {
         return "MacOS";
       } else if (appVersion.indexOf("X11") != -1){
         return "UNIX OS";
       }
       else if (appVersion.indexOf("Linux") != -1){
         return "Linux OS";
       } else {
         return "Unknown";
       }
   }


   async function getLoginData(){
     if (!ajax_token){return false;}
     const secuirtyData = await getLogSecuirties();
     const userLogObj = {
       'browser': detectBrowser(navigator.userAgent),
       'os': detectOS(navigator.appVersion),
       'cookies_enabled': navigator.cookieEnabled,
       'browser_language': navigator.language,
       'loc': secuirtyData['loc'],
       'login_ip': secuirtyData['login_ip'],
       'ajax_token': ajax_token
     }
     return userLogObj;
   }
   // send client data to server also display or not display remeber me if cookies not enabled for more ServerSX
   async function sendLoginData(){
     getLoginData().then( (data)=>{
       postLoginData('', data).then(
         (res)=>{
           if (res.code == 200 && res.cookies_enabled){
             // here server saved data and also can handle cookie
             toggleRememberMe(1);
           } else {
             toggleRememberMe(0);
           }
         }
       )
     });
     return true;
   }

   async function getLogSecuirties(){
     const result = {loc: 'Unknown', login_ip: 'Unknown'};
     const res = await fetch('https://www.cloudflare.com/cdn-cgi/trace');


       const resText = await res.text();
       const data = getSecuirtiesJSON(resText);
       result['loc'] = !data.loc || data.loc == '' ? 'Unknown' : data.loc;
       result['login_ip'] = !data.ip || data.ip == '' ? 'Unknown' : data.ip;
     return result;
   }

   function getSecuirtiesJSON(data){
     /* create js object from array accoriding to rule new line sperated */
     data = data.trim().split('\n').reduce(function(obj, pair) {
       pair = pair.split('=');
       return obj[pair[0]] = pair[1], obj;
     }, {});
     return data;
   }


   sendLoginData();
