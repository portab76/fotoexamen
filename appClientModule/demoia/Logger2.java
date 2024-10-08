package demoia;


import java.io.BufferedWriter;
import java.io.FileWriter;
import java.io.IOException;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;

public class Logger2 {
    // Variable a nivel de clase para el archivo de log
    private static BufferedWriter writer;

    static {
        try {
            writer = new BufferedWriter(new FileWriter("log2.txt", true));
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    // Método para escribir en el archivo con marca de tiempo
    public static void log(String message) {
        try {            
            writer.write(message);
            writer.flush();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    // Método para cerrar el BufferedWriter
    public static void close() {
        try {
            if (writer != null) {
                writer.close();
            }
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    public static void main(String[] args) {
        // Ejemplo de uso
        Logger2.log("Este es un mensaje de prueba.");
        Logger2.log("Otro mensaje de prueba.");
        Logger2.close();
    }
}
