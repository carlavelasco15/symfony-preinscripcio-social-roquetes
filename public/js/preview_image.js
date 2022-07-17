/*
 * Previsualització d'una imatge
 *
 * (2022) Robert Sallent <robertsallent@gmail.com>
 * 
 */

window.onload = function() {
    document.getElementById("activity_form_picture").onchange = function(e) {
        if(!e.target.files[0].name.match(/\.(jpe?g|png|gif)$/i)) {
            alert('El tipus de fitxer ha de ser JPG, PNG o GIF');
        } else {
            let reader = new FileReader();
            reader.readAsDataURL(e.target.files[0]);

            reader.onload = function() {
                let image = document.getElementById('preview');
                image.src = reader.result;
            }
        }
    }
}


