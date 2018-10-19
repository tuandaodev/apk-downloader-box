/* 
 Remove red notice
 */

jQuery(document).ready(function($) {
    
    $("#apk-downloader").submit(function(event){
        event.preventDefault();
        $("#apkmsg").hide();
        var values = $(this).serialize();
        $.ajax({
            url: global.ajax,
            type: "post",
            data: values,
            dataType: 'json',
            success: function (res) {
                response = res.data;
                console.log(response);
                console.log(response.status);
                console.log(response.html);
                if (response.status === "1") {
                    
                } else {
                    $("#apkmsg").show();
                    $("#apkmsg").html(response.html);
                }
                
            },
            error: function(jqXHR, textStatus, errorThrown) {
               console.log(textStatus, errorThrown);
            } 
        });
    });

});

