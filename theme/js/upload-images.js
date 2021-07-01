$(document).ready(function () {
    // Upload profile image

    $(document).on('change', '.photo-input', function () {

        var data = new FormData();
        var ins = document.getElementById($(this).attr('id')).files.length;

        data.append($(this).attr('class'), document.getElementById($(this).attr('id')).files[0]);  // Append an image
        data.append('path', $(this).attr('path'));                                   // Append source of image - place where to save !

        if (typeof $(this).attr('object-id') !== typeof undefined && $(this).attr('object-id') !== false) {
            data.append('id', $(this).attr('object-id'));
        }

        let photoID    = $(this).attr('photo-name');
        let previewID = $(this).attr('id') + '-title';
        let src       = $(this).attr('path');
        // document.getElementById("loading_wrapper").style.display = 'block'; /** show loading part **/


        var xml = new XMLHttpRequest();
        xml.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                let response = JSON.parse(this.responseText);

                let image = document.getElementById(photoID);
                image.setAttribute('src', src + response['name']);

                console.log(response);
            }
        };
        xml.open('POST', $(this).attr('url'));

        // ** Postavi tokene ** //
        var metas = document.getElementsByTagName('meta');
        for (var i=0; i<metas.length; i++) {
            if (metas[i].getAttribute("name") == "csrf-token") {
                xml.setRequestHeader("X-CSRF-Token", metas[i].getAttribute("content"));
            }
        }
        xml.send(data); // napravi http
    });
});