<?php
// Array con los datos
$contents = [
    "23. a 24. d 25. d 26. c 27. c",
    "23. a 24. c 25. d 26. c 27. b",
    "23. a 24. c 25. d 26. c 27. c",
    "23. a 24. d 25. d 26. c 27. c",
    "23. a 24. c 25. d 26. c 27. b",
    "23. a 24. c 25. d 26. c 27. c",
    "23. a 24. c 25. d 26. c 27. c",
    "23. a 24. d 25. d 26. c 27. c",
    "23. a 24. d 25. d 26. c 27. b"
];

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
    
}
