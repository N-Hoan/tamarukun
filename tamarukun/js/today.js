const today = new Date();
 function dateFormat(today, format){
    format = format.replace("YYYY", today.getFullYear());
    format = format.replace("MM", ("0"+(today.getMonth() + 1)).slice(-2));
    format = format.replace("DD", ("0"+ today.getDate()).slice(-2));
    return format;
 }
 const sdata = dateFormat(today,'YYYY-MM-DD');
 const sfield = document.getElementById("startDate");
 sfield.value = sdata;
 sfield.setAttribute("min", sdata);
//  const gdata = dateFormat(today,'YYYY-MM-DD');
//  const gfield = document.getElementById("goalDate");
//  gdatafield.value = gdata;
//  gfield.setAttribute("min", gdata);

//  const today2 = new Date();
//  function dateFormat(today, format){
//     format = format.replace("YYYY", today.getFullYear());
//     format = format.replace("MM", ("0"+(today.getMonth() + 1)).slice(-2));
//     format = format.replace("DD", ("0"+ today.getDate()).slice(-2));
//     return format;
//  }
//  const gdata = dateFormat(today,'YYYY-MM-DD');
//  const gfield = document.getElementById("goalDate");
//  gdatafield.value = gdata;
//  gfield.setAttribute("min", gdata);
// window.onload = function(){
//    var getToday = new Date();
//    var y = getToday.getFullYear();
//    var m = getToday.getMonth() + 1;
//    var d = getToday.getDate();
//    var today = y + "-" + m.toString().padStart(2,'0') + "-" + d.toString().padStart(2,'0');
//    document.getElementById("startDate").setAttribute("value",today);
// }
// window.onload = function(){
//    var getToday = new Date();
//    var y = getToday.getFullYear();
//    var m = getToday.getMonth() + 1;
//    var d = getToday.getDate();
//    var today = y + "-" + m.toString().padStart(2,'0') + "-" + d.toString().padStart(2,'0');
//    document.getElementById("startDate").setAttribute("value",today);
//    document.getElementById("startDate").setAttribute("min",today);
// }