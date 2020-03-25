$( document ).ready(function() {

    $(document).on('click', '.character', function(){
        let id = $(this).data('id');
        if( ($(this).attr("aria-expanded") === 'false') || ($('#charDetail-'+id).find('.charInfo').html() != "") )
            return true;

        let loader = $('#charDetail-'+id).find('.charLoading');
        $(loader).removeClass('d-none');
        $.ajax({
            method: "POST",
            url: '/characters/',
            data: {
                id: id
            }
          })
            .done(function( result ) {
                $.get( "/templates/charInfo.html.handlebars", function( template ) {
                    let baseTemplate = template;
                    let templateScript = Handlebars.compile(baseTemplate);
                    let templateData = {
                        "gender" : result.data.gender,
                        "skin" : result.data.skin_color,
                        "hair" : result.data.hair_color,
                        "eyes": result.data.eye_color,
                        "birth": result.data.birth_year,
                        "height": result.data.height,
                        "mass": result.data.mass
                    };
                    let charInfoTemplate = templateScript(templateData);
                    $(loader).addClass('d-none');
                    $('#charDetail-'+id).find('.charInfo').html(charInfoTemplate);
                  });
            });
    });

    $(document).on('click', '.pageSelector', function(){
        let page = $(this).data('page');
        let loader = $('.mainLoading');
        $(loader).removeClass('d-none');
        $('#charListContainer').html('');
        $.ajax({
            method: "GET",
            url: '/',
            data: {
                page: page
            }
        })
        .done(function( result ) {
            $.get( "/templates/characterList.html.handlebars", function( template ) {
                let baseTemplate = template;
                let templateScript = Handlebars.compile(baseTemplate);
                let templateData = { "charList" : result.charList, "next" : result.next, "previous" : result.previous };
                let charListTemplate = templateScript(templateData);
                $(loader).addClass('d-none');
                $('#charListContainer').html(charListTemplate);
              });
        });
    });
});