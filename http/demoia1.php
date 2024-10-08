<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Imagen a Servidor Java</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        // Esta función se encarga de manejar el formulario y preparar los datos para el servidor Java
       
		async function enviarFormulario(event) {
			event.preventDefault(); // Evitar que el formulario se envíe de forma tradicional

			let form = document.getElementById('imageForm');
			let url = document.getElementById('url').value;
			let imageInput = document.getElementById('image').files[0];
			let imageb64 = null;

			// Si se selecciona una imagen del dispositivo, codificarla en base64
			if (imageInput) {
				imageb64 = await convertirABase64(imageInput);
			}

			// Preparar los datos en formato JSON
			let data = {
				url: url ? url : null,      // Si no se proporciona la URL, se envía null
				imageb64: imageb64 ? imageb64 : null // Si no hay imagen seleccionada, enviar null
			};
			
			
			if (data.imageb64) {
                document.getElementById('imagenBase64').src = "data:image/png;base64,"+data.imageb64;
            }

			// Marcar el tiempo de envío
			let envioTimestamp = new Date().getTime();
			console.log('Formulario enviado a las: ' + new Date(envioTimestamp).toLocaleString());
			document.getElementById('response').innerText = 'Formulario enviado a las: ' + new Date(envioTimestamp).toLocaleString();

			// Enviar la solicitud al servidor Java
			fetch('http://localhost:4567/json', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify(data)
			}).then(response => response.json()).then(data => {
				let respuestaTimestamp = new Date().getTime();
				console.log('Respuesta recibida a las: ' + new Date(respuestaTimestamp).toLocaleString());
				let tiempoTranscurrido = respuestaTimestamp - envioTimestamp;
				console.log('Tiempo transcurrido: ' + convertirMilisegundosAmmss(tiempoTranscurrido) );
				document.getElementById('response').innerText = data.message + '\nTiempo transcurrido: ' + convertirMilisegundosAmmss(tiempoTranscurrido) + ' min.';
			}).catch(error => document.getElementById('response').innerText = 'Error : '+ error);
		}

		function convertirMilisegundosAmmss(milisegundos) {
			let minutos = Math.floor(milisegundos / 60000);
			let segundos = ((milisegundos % 60000) / 1000).toFixed(0);
			return minutos + ":" + (segundos < 10 ? '0' : '') + segundos;
		}

        // Función para convertir una imagen a base64
        function convertirABase64(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onloadend = () => {
                    resolve(reader.result.split(',')[1]); // Retornar solo la codificación base64
                };
                reader.onerror = reject;
                reader.readAsDataURL(file);
            });
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Enviar Imagen al Servidor Java</h2>
        <form id="imageForm" onsubmit="enviarFormulario(event)">
            <div class="mb-3">
                <label for="url" class="form-label">URL de la imagen (opcional):</label>
                <input type="url" class="form-control" id="url" name="url" placeholder="Introduce la URL de la imagen">
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Seleccionar imagen (opcional):</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*" >
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>

        <!-- Mostrar la respuesta del servidor aquí -->
        <div class="mt-4" id="response"></div>
		<br>
		<img id="imagenBase64" alt="Imagen en Base64" width=400>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
