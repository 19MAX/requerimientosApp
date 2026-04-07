document.addEventListener('DOMContentLoaded', function() {
    // Buscar todos los inputs de tipo file en la vista
    const fileInputs = document.querySelectorAll('input[type="file"]');
    const MAX_SIZE_MB = 5; // Límite de peso en MB (acorde a las reglas de backend de 5120KB)
    const MAX_SIZE_BYTES = MAX_SIZE_MB * 1024 * 1024;

    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = this.files[0];
            
            if (file) {
                if (file.size > MAX_SIZE_BYTES) {
                    // Mostrar alerta con SweetAlert2
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Archivo demasiado grande',
                            text: `El archivo "${file.name}" supera el límite permitido de ${MAX_SIZE_MB}MB.`,
                            confirmButtonText: 'Entendido',
                            confirmButtonColor: '#0056b3'
                        });
                    } else {
                        alert(`El archivo "${file.name}" supera el límite permitido de ${MAX_SIZE_MB}MB.`);
                    }
                    
                    // Limpiar el input para evitar que se envíe el formulario con este archivo
                    this.value = '';
                    
                    // Si el input tiene un evento o texto asociado en un "dropzone", también podrías restablecerlo aquí.
                    // Generalmente, limpiar `this.value` dispara el cambio o previene su envío en submits tradicionales.
                }
            }
        });
    });
});
