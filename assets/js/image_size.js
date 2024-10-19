document.addEventListener("DOMContentLoaded", function() {
    const fileInput = document.getElementById("imag_article");

    if (fileInput) {
        fileInput.addEventListener("change", function() {
            validateImageSize(fileInput);
        });
    }

    function validateImageSize(input) {
        const file = input.files[0];
        if (file && file.size > 2 * 1024 * 1024) {  // 2MB in bytes
            alert('La taille du fichier ne doit pas dépasser 2 Mo.');
            input.value = '';  // Resetting the "file" field
        }
    }
});
