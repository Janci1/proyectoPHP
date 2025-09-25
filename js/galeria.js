document.addEventListener('DOMContentLoaded', function() {
    const galleryImages = document.querySelectorAll('.imagenesGaleria img');
    const galleryModal = document.getElementById('galleryModal');
    const closeButton = document.querySelector('.modal .closeButton'); 
    const modalMainImage = document.getElementById('modalMainImage');
    const modalTitle = document.getElementById('modalTitle');
    const modalDescription = document.getElementById('modalDescription');
    const prevButton = document.getElementById('prevButton');
    const nextButton = document.getElementById('nextButton');

    let currentCarouselImages = []; // Array para almacenar las SRCs de las imágenes del carrusel actual (principal + sub-imágenes)
    let currentCarouselIndex = 0; // Índice de la imagen actual dentro de currentCarouselImages
    let originalImageAlt = ''; // Para guardar el alt de la imagen original clicada

    // Función para actualizar la imagen principal del modal
    function updateMainImageDisplay(index) {
        if (currentCarouselImages.length > 0 && index >= 0 && index < currentCarouselImages.length) {
            modalMainImage.src = currentCarouselImages[index];
            modalMainImage.alt = originalImageAlt; // Mantener el alt de la imagen original
        }
    }

    // Función para abrir el modal
    function openModal(clickedImageElement) {
        // Reiniciar el array de imágenes del carrusel
        currentCarouselImages = [];
        
        // La primera imagen del carrusel siempre es la imagen principal clicada
        currentCarouselImages.push(clickedImageElement.src);
        originalImageAlt = clickedImageElement.alt; // Guardar el alt original

        // Obtener las sub-imágenes de los atributos data-subX
        for (let i = 1; i <= 4; i++) { // Asumiendo hasta 4 sub-imágenes (puedes ajustar este número)
            const subImageSrc = clickedImageElement.dataset[`sub${i}`];
            if (subImageSrc) {
                currentCarouselImages.push(subImageSrc);
            }
        }

        // Establecer el título y la descripción del modal desde la imagen principal clicada
        modalTitle.textContent = clickedImageElement.dataset.titulo || '';
        modalDescription.textContent = clickedImageElement.dataset.desc || '';

        // Empezar el carrusel con la primera imagen (la principal)
        currentCarouselIndex = 0;
        updateMainImageDisplay(currentCarouselIndex);

        // Mostrar el modal
        galleryModal.style.display = 'flex';
    }

    // Función para cerrar el modal
    function closeModal() {
        galleryModal.style.display = 'none'; 
    }

    // Navegación con flechas: imagen siguiente
    function showNextImage() {
        if (currentCarouselImages.length > 0) {
            currentCarouselIndex = (currentCarouselIndex + 1) % currentCarouselImages.length;
            updateMainImageDisplay(currentCarouselIndex);
        }
    }

    // Navegación con flechas: imagen anterior
    function showPrevImage() {
        if (currentCarouselImages.length > 0) {
            currentCarouselIndex = (currentCarouselIndex - 1 + currentCarouselImages.length) % currentCarouselImages.length;
            updateMainImageDisplay(currentCarouselIndex);
        }
    }

    // Añadir event listeners a cada imagen de la galería principal
    if (galleryImages.length > 0) {
        galleryImages.forEach(image => {
            image.addEventListener('click', function() {
                openModal(this); 
            });
        });
    }

    // Añadir event listeners a los botones de navegación del modal
    if (prevButton) {
        prevButton.addEventListener('click', showPrevImage);
    }
    if (nextButton) {
        nextButton.addEventListener('click', showNextImage);
    }

    // Añadir event listener al botón de cerrar
    if (closeButton) {
        closeButton.addEventListener('click', closeModal);
    }

    // Cerrar el modal si se hace clic fuera del contenido del modal
    if (galleryModal) {
        window.addEventListener('click', function(event) {
            if (event.target === galleryModal) {
                closeModal();
            }
        });
    }
});