const today2 = new Date();
function dateFormat(today, format){
   format = format.replace("YYYY", today.getFullYear());
   format = format.replace("MM", ("0"+(today.getMonth() + 1)).slice(-2));
   format = format.replace("DD", ("0"+ today.getDate()).slice(-2));
   return format;
}
const gdata = dateFormat(today,'YYYY-MM-DD');
const gfield = document.getElementById("goalDate");
gdatafield.value = gdata;
gfield.setAttribute("min", gdata);