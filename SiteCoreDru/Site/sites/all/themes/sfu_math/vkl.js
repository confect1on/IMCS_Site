$(document).ready(function() {
    $("#vkhide div").css("display", "none"); 
    var vkl = (new RegExp("^.*#(.*)$")).exec(location.href);
    var vk="vkl1";
    if (vkl!=null) vk=vkl[1];
    $("#"+vk).css("display", "block");
    $("#"+vk.substr(1)).attr("class","active");

    $("#vk a").click(function(){
        $("#vk a").attr("class","");
        $(this).attr("class","active");
        $("#vkhide .hide").css("display", "none");
        $("#v"+this.id).css("display", "block");

		$("#vk li").removeClass("vk-active");
		$(this).parent().addClass("vk-active");
		$(this).blur();

		return false;
    });
}); // :-)
    