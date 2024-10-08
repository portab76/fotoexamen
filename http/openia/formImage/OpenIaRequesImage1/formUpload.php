<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener el contenido del cuerpo de la solicitud en formato JSON
    $jsonData = file_get_contents('php://input');
    
    // Decodificar el JSON
    $data = json_decode($jsonData, true);

	//print_r($data);


    // Verificar si los datos fueron enviados correctamente
    if ($data) {
        // Verificar que se haya enviado la imagen en formato base64
        if (isset($data['imagen'])) {
            $imagenBase64 = $data['imagen'];
            // Decodificar la imagen base64
            $imagen = base64_decode($imagenBase64);
            // Crear un nombre de archivo único con formato yyyymmddhhmmss
            $nombreArchivo = "../uploads/".date('YmdHis') . '.png';
            // Guardar la imagen en el servidor
            file_put_contents($nombreArchivo, $imagen);
            // Mostrar la imagen guardada
            echo "Imagen guardada correctamente con el nombre: " . $nombreArchivo . "<br>";
            ?><img src="data:image/png;base64,<?=$data["imagen"]?>" alt="Imagen Subida" /><?php
        } else {
            echo "No se envió ninguna imagen.";
        }
    } else {
        echo "No se recibió un JSON válido.";
    }
} else {
    echo "No se recibió ninguna solicitud POST.";
}
?>
