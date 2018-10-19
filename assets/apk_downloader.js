/* 
 Remove red notice
 */

jQuery(document).ready(function($) {
    
    $("#apk-downloader").submit(function(event){
        event.preventDefault();
        $("#apkmsg_success").hide();
        $("#apkmsg_error").hide();
        var values = $(this).serialize();
        $.ajax({
            url: global.ajax,
            type: "post",
            data: values,
            dataType: 'json',
            success: function (res) {
                response = res.data;
//                console.log(response);
//                console.log(response.status);
//                console.log(response.html);
                if (response.status === "1") {
                    $("#apkmsg_success").show();
                    $("#apkmsg_success").html(response.html);
                    
                    var url = $("#server_url").val() + response.download_url;
//                    console.log(url);
                    window.open(url);
                } else {
                    $("#apkmsg_error").show();
                    $("#apkmsg_error").html(response.html);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
               console.log(textStatus, errorThrown);
            } 
        });
    });

});

