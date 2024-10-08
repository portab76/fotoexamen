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
			
			
			require 'OpenAIImageRequest.php';
			$api_key = "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";
            

			$openAIRequest = new OpenAIImageRequest($api_key, $data["imagen"]);
			$response = $openAIRequest->send_request();

			// *********************
			// Mostrar la respuesta
			echo "<pre>";
			print_r($response);
			echo "</pre>";
			


$contents = [];

			//$response = json_decode($response, true);
			// Verificar si el array contiene 'choices'
			if (isset($response['choices']) && is_array($response['choices'])) {
				// Recorrer cada entrada en 'choices'
				foreach ($response['choices'] as $choice) {
					// Verificar si existe 'content' en el mensaje
					if (isset($choice['message']['content'])) {
						$content = $choice['message']['content'];
						//echo "<br>Contenido extraído: " . $content . "\n";
						$content = str_replace("\n","",$content);
						$contents[] = $content;
						
					} else {
						echo "No se encontró la clave 'content'.\n";
					}
				}
			} else {
				echo "No se encontró la clave 'choices' o no es un array.\n";
			}


            
// Cadena JSON con formato escapado
$jsonString = $contents[0];

// Quitamos los caracteres de escape
$jsonString = stripslashes($jsonString);

// Decodificamos el JSON
$datos = json_decode($jsonString, true); // true para obtener un array asociativo

// Verificamos si la decodificación fue exitosa
if ($datos === null) {
    echo ">>>>>>>>>>>>>>>>>>> Error al decodificar el JSON >>>>>>>>>>>>>> " . $contents[0];
} else {
    // Recorremos cada objeto en el array
    foreach ($datos as $dato) {
        echo "Número: " . $dato['numero'] . "\n";
        echo "Pregunta: " . $dato['pregunta'] . "\n";
        echo "Respuestas: " . $dato['respuestas'] . "\n";
        echo "\n"; // Línea vacía entre preguntas
    }
}

/*
// *********************
// Array para contar las ocurrencias
$counts = [];

// Expresión regular para capturar números seguidos de una letra
$pattern = '/(\d+)\.\s*(\w)/';

// Recorrer cada línea del contenido
foreach ($contents as $line) {
    // Usamos preg_match_all para encontrar todos los pares número-letra
    preg_match_all($pattern, $line, $matches, PREG_SET_ORDER);
    
    // Recorrer los resultados de las coincidencias
    foreach ($matches as $match) {
        $number = $match[1];  // El número (puede tener 1, 2 o 3 caracteres)
        $letter = $match[2];  // La letra asociada al número
        
        // Incrementar el contador para ese número y letra
        if (!isset($counts[$number])) {
            $counts[$number] = [];
        }
        
        if (!isset($counts[$number][$letter])) {
            $counts[$number][$letter] = 0;
        }
        
        $counts[$number][$letter]++;
    }
}

// Mostrar los dos valores más repetidos por cada número
foreach ($counts as $number => $letters) {
    // Ordenar las letras por su conteo en orden descendente
    arsort($letters);
    
    // Obtener las dos letras más frecuentes
    $top_two = array_slice($letters, 0, 2, true);
    
    echo "<br>$number ";
    foreach ($top_two as $letter => $count) {
        echo strtoupper($letter).$count." ";
    }
    
}*/

			
            ?><img src="data:image/png;base64,<?=$data["imagen"]?>" alt="Imagen Subida" / width="500"><?php
			
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
