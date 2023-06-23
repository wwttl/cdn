// 每年12月13日全站变灰
var date = new Date();
var year = date .getFullYear();
var month = date .getMonth()+1;
var day = date.getDate();
if(month=='12' && day=='13'){
   $("html").css({
       "filter":"progid:DXImageTransform.Microsoft.BasicImage(grayscale=1)",
       "-webkit-filter":"grayscale(100%)"
   });
  console.log("昭昭前事，惕惕后人，铭记历史，吾辈奋进。此刻，南京！");
}