<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario</title>
    <style>
        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .form-check {
            margin-right: 15px;
        }
        .hidden {
            display: none;
        }
        .fade {
            transition: opacity 0.5s ease-in-out;
        }
        .fade-in {
            opacity: 1;
        }
        .fade-out {
            opacity: 0;
        }
    </style>
</head>
<body>
    <div class="form-group">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="imageSource" value="url" onclick="disableOthers('url')" id="urlRadio" />
            <label class="form-check-label" for="urlRadio">Desde URL</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="imageSource" id="fileRadio" value="file" onclick="disableOthers('file')" />
            <label class="form-check-label" for="fileRadio">Archivo</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="imageSource" id="cameraRadio" value="camera" onclick="disableOthers('camera')" checked />
            <label class="form-check-label" for="cameraRadio">Cámara</label>
        </div>
    </div>
    <div class="form-group">
        <input type="text" id="urlInput" name="url" class="form-control hidden fade" style="margin-left: 15px;width: -webkit-fill-available;" />
        <input type="file" id="fileInput" name="file" class="form-control-file form-control hidden fade" accept="image/*" />
        <input type="file" id="cameraInput" name="camera" class="form-control-file form-control fade" accept="image/*" capture="camera" />
    </div>

    <script>
        function disableOthers(selected) {
            const urlInput = document.getElementById('urlInput');
            const fileInput = document.getElementById('fileInput');
            const cameraInput = document.getElementById('cameraInput');

            urlInput.classList.add('hidden', 'fade-out');
            fileInput.classList.add('hidden', 'fade-out');
            cameraInput.classList.add('hidden', 'fade-out');

            if (selected === 'url') {
                urlInput.classList.remove('hidden', 'fade-out');
                urlInput.classList.add('fade-in');
            } else if (selected === 'file') {
                fileInput.classList.remove('hidden', 'fade-out');
                fileInput.classList.add('fade-in');
            } else if (selected === 'camera') {
                cameraInput.classList.remove('hidden', 'fade-out');
                cameraInput.classList.add('fade-in');
            }
        }

        // Inicializar con la opción de cámara seleccionada
        disableOthers('camera');
    </script>
</body>
</html>
