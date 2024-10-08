<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reconocimiento de Imagen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        // Esta función se encarga de manejar el formulario y preparar los datos para el servidor Java
		let textPrompt1 = "Identifica los numeros de las preguntas , texto pregunta y respuesteas de la imagen adjunto"
		+ " y expresamente debes obten los resultados  formateados en json con este esquema: "
		+ "{ \"numero\":int,\"pregunta\": String,\"respuestas\":String}, "
		+ "no repitas las preguntas.";

		let textPrompt2 ="según el temario para la obtención del titulo de patrón de embarcaciones de recreo. "
		+ "averigua cual es la respuesta correcta. "
		+ "la salida la debes de formatear como json con el esquema: "
		+ "{ \"letra\":String,\"respuesta\": String,\"razonamiento\": String} " 
		+ "donde letra corresponde a la letra de la respuesta correcta, el campo respuesta del json indica una breve  referencia al temario donde se responde a respuesta correcta"
		+ "y en el campo razonamiento si tienes dudas de otra respuesta que pudiera ser correcta: #pregunta# #respuestas# ";

		let dataPromp1; let dataPromp2;
		async function enviarPromp1(event) 
		{
			event.preventDefault(); // Evitar que el formulario se envíe de forma tradicional
			document.getElementById('responsePrompt1').innerText = '';
			document.getElementById('responsePrompt2').innerText = '';
			document.getElementById('response').innerText =  document.getElementById('response').innerText + '\n';
			let form = document.getElementById('imageForm');
			let url = document.getElementById('urlInput').value;
			let imageFile = document.getElementById('fileInput').files[0];
			let imageCamara = document.getElementById('cameraInput').files[0];
			const urlRadio = document.getElementById('urlRadio');
			const fileRadio = document.getElementById('fileRadio');
			const cameraRadio = document.getElementById('cameraRadio');
			let imageb64 = null;
		    // Verifica cuál está seleccionado
			if (urlRadio.checked) {
				console.log('Seleccionado: URL');
				url = "";
			} else if (fileRadio.checked) {
				console.log('Seleccionado: Archivo');
				imageb64 = await convertirABase64(imageFile);
			} else if (cameraRadio.checked) {
				console.log('Seleccionado: Cámara');
				imageb64 = await convertirABase64(imageCamara);
			} else {
				document.getElementById('responsePrompt1').innerText = 'No se ha seleccionado ninguna Imagen'
				return;				
			}
			// Preparar los datos en formato JSON
			let data = {
				promp: document.getElementById('textPrompt1').value,
				url: url ? url : null,      // Si no se proporciona la URL, se envía null
				imageb64: imageb64 ? imageb64 : null // Si no hay imagen seleccionada, enviar null
			};
			//muestra la imagen en pantalla
			if (data.imageb64) {
					var imagen = document.getElementById('imagenBase64');
                    imagen.src = "data:image/png;base64,"+data.imageb64;
					imagen.classList.add('show');
            }
			// Marcar el tiempo de envío
			let envioTimestamp = new Date().getTime();
			console.log('Formulario enviado a las: ' + new Date(envioTimestamp).toLocaleString());
			document.getElementById('responsePrompt1').innerText = 'Formulario enviado a las: ' + new Date(envioTimestamp).toLocaleString();
			fetch('http://localhost:4567/json2', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify(data)
			})
			.then(response => response.json())
			.then(data => {
				// Marcar el tiempo de recepción
				let respuestaTimestamp = new Date().getTime();
				let tiempoTranscurrido = respuestaTimestamp - envioTimestamp;
                console.log('Tiempo transcurrido: ' + convertirMilisegundosAmmss(tiempoTranscurrido) );
				console.log(data.message);
				// Mostrar la respuesta en la consola o en la página				
				document.getElementById('responsePrompt1').innerText = 
					'Respuesta en ' + convertirMilisegundosAmmss(tiempoTranscurrido) +' sg. Recibido OK'
					+'\n'+document.getElementById('responsePrompt1').innerText; //data.message ;
				dataPromp1 = data.message;
				document.getElementById('boton2').click();
			}).catch(error => document.getElementById('responsePrompt1').innerText = 'Error : '+ error);
		}
		async function enviarPromp2(event) 
		{
			event.preventDefault(); // Evitar que el formulario se envíe de forma tradicional			
			document.getElementById('responsePrompt2').innerText = '';
			let jsonp = "";			
			try {
				jsonp = JSON.parse(dataPromp1);
			} catch (error) {
				try {
					jsonp = JSON.parse("[" + dataPromp1 + "]");
				} catch (error) {
					document.getElementById('responsePrompt2').innerText = error;
				}
			}
			console.log("[promp] >> "+document.getElementById('textPrompt2').value);
			console.log(jsonp);	
			let envioTimestamp = new Date().getTime();
			document.getElementById('responsePrompt2').innerText = 'Formulario enviado a las: ' + new Date(envioTimestamp).toLocaleString();
			try{
				for (const { numero, pregunta, respuestas } of jsonp) 
				{
					console.log(`Número: ${numero}`);
					console.log(`Pregunta: ${pregunta}`);
					console.log(`Respuestas: ${respuestas}`);				
					console.log('----------------');
					let data2 = {
						promp: document.getElementById('textPrompt2').value,
						numero:`${numero}`,
						pregunta:`${pregunta}`,
						respuesta:`${respuestas}`
					};
					// Marcar el tiempo de envío				
					var n = `${numero}`;
					var p = `${pregunta}`;
					var r = `${respuestas}`;
					fetch('http://localhost:4567/json3', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json'
					},
					body: JSON.stringify(data2)
					}).then(response => response.json()).then(data => {
						let respuestaTimestamp = new Date().getTime();
						let tiempoTranscurrido = respuestaTimestamp - envioTimestamp;
						console.log('Respuesta recibida a las: ' + new Date(respuestaTimestamp).toLocaleString()+' Tiempo transcurrido: ' + convertirMilisegundosAmmss(tiempoTranscurrido) );					
						console.log(data);					
						document.getElementById('responsePrompt2').innerText=
						'\nPREGUNTA '+n+':\n' + p
						+'\n\nRESPUESTAS:\n' + r
						+'\n\nSOLUCION: ' + data.message 
						+ document.getElementById('responsePrompt2').innerText;
						try{
						var respu = JSON.parse(data.message);
						document.getElementById('response').innerText = document.getElementById('response').innerText + '\n'+ n + " " + respu.letra;
						}catch(error){ console.log(error); }
						
					}).catch(error => document.getElementById('responsePrompt2').innerText = 'Error : '+ error);
					await sleep(3000);
				}
			}catch(error){
				document.getElementById('responsePrompt2').innerText = 'Error : '+ error;
			}
			document.getElementById('responsePrompt2').innerText = 'FIN\n'+document.getElementById('responsePrompt2').innerText;
		}
		function sleep(ms) {
			return new Promise(resolve => setTimeout(resolve, ms));
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
                try{
					reader.readAsDataURL(file);
				}catch(error) {
				 document.getElementById('responsePrompt1').innerText = 'Error : '+ error;
				}
            });
        }
		function disableOthers(selectedOption) {
            document.getElementById('urlInput').disabled = selectedOption !== 'url';
            document.getElementById('fileInput').disabled = selectedOption !== 'file';
            document.getElementById('cameraInput').disabled = selectedOption !== 'camera';
        }
    </script>
    <style>
        .button {
			margin-left: 5px
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }		
		#imagenBase64 {
            display: none;
            opacity: 0;
            transition: opacity 3s;
        }
        #imagenBase64.show {
            display: block;
            opacity: 1;
			transition: opacity 3s;
        }		
    </style>	
</head>

<body>
    <div class="container mt-5">
	
		<div class="mt-5 d-flex justify-content-center">    
			<h4 class="mb-4">Enviar Imagen:</h4>
		</div>
		
		
		<div class="form-group">
			<div class="form-check">
				<input class="form-check-input" type="radio" name="imageSource" value="url" onclick="disableOthers('url')" id="urlRadio" />
				<label class="form-check-label" for="urlRadio" >Desde URL</label>
			</div>
			<input type="text" id="urlInput" name="url" class="form-control" disabled="disabled" 
			onclick="disableOthers('url')" style="margin-left: 15px;width: -webkit-fill-available;"/>
		</div>
		<br>
		<div class="form-group">
			<div class="form-check">
				<input class="form-check-input" type="radio" name="imageSource" id="fileRadio" value="file" onclick="disableOthers('file')" />
				<label class="form-check-label" for="fileRadio">Archivo</label>
			</div>
			<input type="file" id="fileInput" name="file" class="form-control-file form-control" accept="image/*" disabled="disabled" />
		</div>
		<br>
		<div class="form-group">
			<div class="form-check">
				<input class="form-check-input" type="radio" name="imageSource" id="cameraRadio" value="camera" onclick="disableOthers('camera')" />
				<label class="form-check-label" for="cameraRadio">Cámara</label>
			</div>
			<input type="file" id="cameraInput" name="camera" class="form-control-file form-control" accept="image/*" capture="camera" disabled="disabled" />
		</div>
		<!------------------------------------------------------------->
		<!------------------------------------------------------------->
		
		<!------------------------------------------------------------->


		
		<!-- RESPUESTA PREGUNTA 2 -->
        <div class="mt-4" id="response"></div>
		
		<form id="promp1Form" onsubmit="enviarPromp1(event)">
			<button type="button" class="btn btn-secondary mt-3 button" data-bs-toggle="modal" data-bs-target="#pregunta1Modal">PROMPS</button>
			<button type="submit" class="btn btn-primary mt-3 button">SEND</button>
		</form>
		
		
		<form id="promp2Form" onsubmit="enviarPromp2(event)" style="display:false;">			
			<button type="submit" id="boton2" class="btn btn-primary mt-3 button" style="display:none;">SEND</button>
		</form>	


		<!-- RESPUESTA PREGUNTA 2 -->
        <div class="mt-4" id="responsePrompt2"></div>
        <!-- RESPUESTA PREGUNTA 1 -->
		<div class="mt-4" id="responsePrompt1"></div>	
		<!-- IMAGEN -->
        <img id="imagenBase64" alt="Imagen en Base64" width=400 style="width: -webkit-fill-available;">

        <!-- Modal para Pregunta 1 -->
        <div class="modal fade" id="pregunta1Modal" tabindex="-1" aria-labelledby="pregunta1ModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="pregunta1ModalLabel">Preguntas:</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <textarea class="form-control" id="textPrompt1" name="textPrompt1" rows="5" style="min-height:250px"></textarea>
                        <textarea class="form-control" id="textPrompt2" name="textPrompt2" rows="5" style="min-height:250px"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>




    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
	
	<script>
		document.getElementById('textPrompt1').value = textPrompt1;
		document.getElementById('textPrompt2').value = textPrompt2;
	</script>
	
	
</body>


