package demoia.app;

import static spark.Spark.*;
import com.google.gson.Gson;
import demoia.*;
import spark.Request;
import spark.Response;

public class MainPreguntasApp {
    public static void main(String[] args) {

        //secure("/home/debian/keystore.jks", "Papablopo532.", null, null);
        port(4567);
        enableCORS();
        // Definir una ruta POST que acepte un JSON
        post("/json", (Request req, Response res) -> {            
            try {
            	String body = req.body();
            	Gson gson = new Gson();
            	Image image = gson.fromJson(body, Image.class);
            	//System.out.println("url: " + image.getUrl());
            	//System.out.println("imageb64: " + image.getImageb64());
            	PreguntasTest pt = new PreguntasTest();
            	String ret =  pt.getAnswersFromImage(image);
            	res.type("application/json");
            	return gson.toJson(new ResponseMessage(ret));            	
            }catch(Exception e) {
            	e.printStackTrace();
            	return "Error " + e.getMessage();
            }            
        });
        //  RESPONDE PROMP 1
        post("/json2", (Request req, Response res) -> {            
            try {
            	String body = req.body();
            	Gson gson = new Gson();
            	Image image = gson.fromJson(body, Image.class);
            	//System.out.println("url: " + image.getUrl());
            	//System.out.println("imageb64: " + image.getImageb64());
            	PreguntasTest pt = new PreguntasTest();
            	String ret =  pt.getTextFromImage(image);
            	res.type("application/json");
            	return gson.toJson(new ResponseMessage(ret));            	
            }catch(Exception e) {
            	e.printStackTrace();
            	return "Error " + e.getMessage();
            }            
        });        
        //  RESPONDE PROMP 2        
        post("/json3", (Request req, Response res) -> {            
            try {
            	String body = req.body();            	
            	//System.out.println(body);
            	Gson gson = new Gson();
            	Pregunta pregunta = gson.fromJson(body, Pregunta.class);            	
            	//System.out.println(pregunta.getPromp());            	
            	PreguntasTest pt = new PreguntasTest();
            	String ret =  pt.getRespuestaFromPregunta(pregunta);
            	res.type("application/json");
            	return gson.toJson(new ResponseMessage(ret));
            }catch(Exception e) {
            	e.printStackTrace();
            	return "Error " + e.getMessage();
            }            
        });  
        
        get("/", (Request req, Response res) -> {
        	return "GET not allowed";            
        });      
        get("/json", (Request req, Response res) -> {
        	return "GET json not allowed";            
        });    
        get("/json2", (Request req, Response res) -> {
        	return "GET json2 not allowed";            
        }); 
        get("/json3", (Request req, Response res) -> {
        	return "GET json3 not allowed";            
        });         
    }// end main

  

    // Clase para enviar una respuesta en JSON
    static class ResponseMessage {
        private String message;

        public ResponseMessage(String message) {
            this.message = message;
        }

        public String getMessage() {
            return message;
        }
    }
    
 // Método para habilitar CORS
    private static void enableCORS() {
        // Filtro para manejar CORS
        before((request, response) -> {
            response.header("Access-Control-Allow-Origin", "*");
            response.header("Access-Control-Allow-Methods", "GET,POST,OPTIONS");
            response.header("Access-Control-Allow-Headers", "Content-Type,Authorization");
        });

        // Manejar la solicitud de preflight (opciones para CORS)
        options("/*", (request, response) -> {
            String accessControlRequestHeaders = request.headers("Access-Control-Request-Headers");
            if (accessControlRequestHeaders != null) {
                response.header("Access-Control-Allow-Headers", accessControlRequestHeaders);
            }

            String accessControlRequestMethod = request.headers("Access-Control-Request-Method");
            if (accessControlRequestMethod != null) {
                response.header("Access-Control-Allow-Methods", accessControlRequestMethod);
            }
            return "OK";
        });
    }
}

