$(document).ready(function() {
    $(".epa a").click(function(){
        if ($("#d"+this.id).css("display")=="none")
        {
            $("#d"+this.id).css("display", "block");
            $(this).attr("class","active");
        }
        else
        {
            $("#d"+this.id).css("display", "none");
            $(this).attr("class","");
        }
        return false;
    });   
});
