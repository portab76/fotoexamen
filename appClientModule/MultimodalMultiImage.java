


import com.google.cloud.vertexai.VertexAI;
import com.google.cloud.vertexai.api.Content;
import com.google.cloud.vertexai.api.GenerateContentResponse;
import com.google.cloud.vertexai.generativeai.ContentMaker;
import com.google.cloud.vertexai.generativeai.GenerativeModel;
import com.google.cloud.vertexai.generativeai.PartMaker;
import com.google.cloud.vertexai.generativeai.ResponseHandler;

import demoia.Logger;
import demoia.Logger2;

import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

public class MultimodalMultiImage {

  final String projectId = "elper-433408";
  final String location = "us-central1";
  final String modelName = "gemini-1.5-flash-001";
	
  final String textPrompt1 = "Identifica los numeros de las preguntas , texto pregunta y respuesteas de la imagen adjunto"
		  + " y expresamente obten los resultados en este formato json: "
		  + "{ \"numero\":int,\"pregunta\": String,\"respuestas\":String}, "
		  + "no repitas las preguntas.";
  
  final String textPrompt2 ="según el temario para la obtención del titulo de patrón de embarcaciones de recreo. "
  		+ "averigua cual es la respuesta correcta. "
  		+ "la salida la debes de formatear como json con el formato "
  		+ "{ \"letra\":String,\"respuesta\": String,\"razonamiento\": String} " 
  		+ "donde letra corresponde a la letra de la respuesta correcta y respuesta a las citaciones del temario "
  		+ "donde aparece la respuesta, y un breve razonamiento: #pregunta# #respuestas# ";
	
  public static void main(String[] args) throws IOException {
    

    
    Logger.log(">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>Goo<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<");
    Logger.log("");
    Logger2.log(">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>Goo<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<");
    Logger2.log("");
    
    MultimodalMultiImage a = new MultimodalMultiImage();
    
    
    byte[] image = readImageFile("https://elper.es/enlaces/7.jpeg");
    
    String output = a.multimodalMultiImage(a.projectId, a.location, a.modelName, a.textPrompt1, image);

	JSONArray jsonArray = null;
    try {
    	jsonArray = new JSONArray(output);    	
    }catch (JSONException e) {
    	jsonArray = new JSONArray("["+output+"]");
    }
    
    
    for (int i = 0; i < jsonArray.length(); i++) 
    {
        JSONObject jsonObject = jsonArray.getJSONObject(i);
        int numero = jsonObject.getInt("numero");
        String pregunta = jsonObject.getString("pregunta");
        String respuestas = jsonObject.getString("respuestas");
        
        // Imprimir los valores
        
        String textPrompt = a.textPrompt2.replace("#pregunta#", pregunta).replace("#respuestas#", respuestas);
        
        //System.out.println("[textPrompt] >> "+ textPrompt);
        Logger.log("[textPrompt] >> "+numero+ " " + textPrompt);
        
		try{if(i > 1)Thread.sleep(10 * 5000);}catch (InterruptedException e){e.printStackTrace();}
		
		output=a.fixJson(a.textInput(a.projectId, a.location, a.modelName, a.textPrompt2));
		  
		try {
			JSONArray jsonArray2 = null;
		    try {
		    	jsonArray2 = new JSONArray(output);    	
		    }catch (JSONException e) {
		    	jsonArray2 = new JSONArray("["+output+"]");
		    }
	    
	    	for (int e = 0; e < jsonArray2.length(); e++) 
	    	{
	    		JSONObject jsonObject2 = jsonArray2.getJSONObject(e);	    		
	    		String letra = jsonObject2.getString("letra");
	            String respuesta = jsonObject2.getString("respuesta");	            
	            String razonamiento = jsonObject2.getString("razonamiento");	            
	            //System.out.println("OK >> "+numero+" "+letra);	            
	    	}	    
	    
	    }catch (Exception e) {
	    	e.printStackTrace();
	    }
	    
		Logger.log("Respuesta ok: "+numero+" "+output);		  
		Logger2.log(numero+" "+output);
				
    }
  }
  
  public String fixJson(String json) {	  
	  String ret =   json.replace("```json","").replace("```","");
	  return ret;
  }

  // Generates content from multiple input images.
  public String multimodalMultiImage(String projectId, String location, String modelName, String textPrompt, byte[] image )
  {

    try (VertexAI vertexAI = new VertexAI(projectId, location)) {
      GenerativeModel model = new GenerativeModel(modelName, vertexAI);

      
      //System.out.println("[textPrompt] >> "+textPrompt);
      Logger.log("[textPrompt] >> "+textPrompt);
      
      Content content = ContentMaker.fromMultiModalData(
          PartMaker.fromMimeTypeAndData("image/png", image),
          textPrompt
      );
      GenerateContentResponse response = model.generateContent(content);
      String output = fixJson(ResponseHandler.getText(response));            
      Logger.log(output);

      return output;
    }catch(IOException e) {
    	Logger.log("[multimodalMultiImage] IOException >> "+e.getMessage());
    	e.printStackTrace();
    	return "";
    }catch(Exception e) {
    	Logger.log("[multimodalMultiImage] Exception >> "+e.getMessage());
    	e.printStackTrace();
    	return "";
    }
  }
  
  
  public static String textInput( String projectId, String location, String modelName, String textPrompt  ) throws IOException 
	  {
		String output =""; 
	    try (VertexAI vertexAI = new VertexAI(projectId, location)) 
	    {
	      GenerativeModel model = new GenerativeModel(modelName, vertexAI);
	      GenerateContentResponse response = model.generateContent(textPrompt);
	      output = ResponseHandler.getText(response);
	    }catch(Exception e) {
	    	Logger.log("[textInput] Exception >> "+e.getMessage());
	    	e.printStackTrace();
	    	return "";
	    }
		return output;
	  }

  // Reads the image data from the given URL.
  public static byte[] readImageFile(String url) throws IOException {
    URL urlObj = new URL(url);
    HttpURLConnection connection = (HttpURLConnection) urlObj.openConnection();
    connection.setRequestMethod("GET");
    int responseCode = connection.getResponseCode();
    if (responseCode == HttpURLConnection.HTTP_OK) {
      InputStream inputStream = connection.getInputStream();
      ByteArrayOutputStream outputStream = new ByteArrayOutputStream();
      byte[] buffer = new byte[1024];
      int bytesRead;
      while ((bytesRead = inputStream.read(buffer)) != -1) {
        outputStream.write(buffer, 0, bytesRead);
      }
      return outputStream.toByteArray();
    } else {
      Logger.log("[readImageFile] Error fetching file >> " + responseCode);
      throw new RuntimeException("Error fetching file: " + responseCode);
    }
  }
}