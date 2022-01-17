$(document).on("click","#cuadro-closed", function(){
    $(".cuadro").css('display','none');
});
setTimeout(function() {
$(".cuadro").fadeOut(1000);
},5000);