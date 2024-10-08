package demoia;

import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;

public class HttpClient {
    public static void main(String[] args) {
        try {
            // URL del servicio
            URL url = new URL("http://localhost:4567/json");
            HttpURLConnection conn = (HttpURLConnection) url.openConnection();
            
            // Configurar la solicitud
            conn.setRequestMethod("POST");
            conn.setRequestProperty("Content-Type", "application/json; utf-8");
            conn.setDoOutput(true);

            
            // Crear el JSON para enviar
            String jsonInputString = "{\"name\": \"Juan\", \"age\": 30}";

            // Enviar el JSON
            try (OutputStream os = conn.getOutputStream()) {
                byte[] input = jsonInputString.getBytes("utf-8");
                os.write(input, 0, input.length);
            }

            // Leer la respuesta
            int responseCode = conn.getResponseCode();
            
            
            //System.out.println(conn.getURL());
            //System.out.println("Response Code: " + responseCode);

            // Aquí puedes agregar código para leer la respuesta del servidor si es necesario

        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}

