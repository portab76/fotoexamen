package demoia;


import com.google.cloud.vertexai.VertexAI;
import com.google.cloud.vertexai.api.Content;
import com.google.cloud.vertexai.api.GenerateContentResponse;
import com.google.cloud.vertexai.generativeai.ContentMaker;
import com.google.cloud.vertexai.generativeai.GenerativeModel;
import com.google.cloud.vertexai.generativeai.PartMaker;
import com.google.cloud.vertexai.generativeai.ResponseHandler;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.Base64;
import java.util.concurrent.TimeUnit;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

public class PreguntasTest {

  String projectId = "elper-433408";
  String location = "us-central1";
  String modelName = "gemini-1.5-flash-001";
	
  String textPrompt1 = "Identifica los numeros de las preguntas , texto pregunta y respuesteas de la imagen adjunto"
		  + " y expresamente debes obten los resultados  formateados en json con este esquema: "
		  + "{ \"numero\":int,\"pregunta\": String,\"respuestas\":String}, "
		  + "no repitas las preguntas.";
  
	String textPrompt2 ="según el temario para la obtención del titulo de patrón de embarcaciones de recreo. "
	  		+ "averigua cual es la respuesta correcta. "
	  		+ "la salida la debes de formatear como json con el esquema: "
	  		+ "{ \"letra\":String,\"respuesta\": String,\"razonamiento\": String} " 
	  		+ "donde letra corresponde a la letra de la respuesta correcta y respuesta a las citaciones del temario "
	  		+ "donde aparece la respuesta, y un breve razonamiento: #pregunta# #respuestas# ";
	  

	
  String error = "";
	
  
  public static void main(String[] args) throws IOException {
  }
  
  public String fixJson(String json) {	  
	  String ret =   json.replace("```json","").replace("```","");
	  return ret;
  }

  
  // DEVUELVE EL TEXTO DE UNA IMAGEN
  public String getTextFromImage(Image image) 
  {		 
	byte[] imageb=null;
	String ret ="";
	try {
		if (image.getUrl() != null && image.getUrl().startsWith("http")) {
			imageb = readImageFile(image.getUrl());			
		}else if(image.getImageb64() != null ) {
			imageb = Base64.getDecoder().decode(image.getImageb64());	
		}
		if (imageb != null) {
			try (VertexAI vertexAI = new VertexAI(projectId, location)) 
		    {
		      GenerativeModel model = new GenerativeModel(modelName, vertexAI);

		      //System.out.println("[textPrompt] >> "+image.getPromp());
		      Logger.log("[textPrompt] >> "+image.getPromp());
		      
		      Content content = ContentMaker.fromMultiModalData( PartMaker.fromMimeTypeAndData("image/png", imageb), image.getPromp());
		      GenerateContentResponse response = model.generateContent(content);
		      ret = fixJson(ResponseHandler.getText(response));            
		      Logger.log(ret);

		      //return output;
		    }catch(IOException e) {
		    	Logger.log("[multimodalMultiImage] IOException >> "+e.getMessage());
		    	//e.printStackTrace();
		    }catch(Exception e) {
		    	Logger.log("[multimodalMultiImage] Exception >> "+e.getMessage());
		    	//e.printStackTrace();
		    }
		} else
			ret = "Selecciona una imagen.";
	} catch (IOException e) {
		e.printStackTrace();
	}
	return ret;
  }
  
  public String getRespuestaFromPregunta(Pregunta opregunta) 
  {	
      int numero = opregunta.getNumero();
      
      String pregunta = opregunta.getPegunta();
      String respuestas = opregunta.getRespuesta();      
      String textPrompt3 =   opregunta.getPromp();
      
      
      
      //System.out.println("[textPrompt] >>  "+ textPrompt3 );
      //System.out.println("[pregunta] >>  "+ pregunta );
      //System.out.println("[repuestas] >>  "+ respuestas );
      
      
      
      textPrompt3 = textPrompt3.replace("#pregunta#", pregunta).replace("#respuestas#", respuestas);
      Logger.log("[textPrompt] >> "+numero+ " " + textPrompt3);
      String output="";
      try {
    	  output=fixJson(textInput(projectId, location, modelName, textPrompt3));
      } catch (IOException e1) {
    	  output="";
    	  e1.printStackTrace();
      }
      return output;
  }
  
  
  //* PROCESA TODAS LAS PREGUNTAS A LA VEZ 
  public String getAnswersFromImage(Image image) 
  {		 
	byte[] imageb=null;
	String ret ="";
	try {
		if (image.getUrl() != null && image.getUrl().startsWith("http")) {
			imageb = readImageFile(image.getUrl());			
		}else if(image.getImageb64() != null ) {
			imageb = Base64.getDecoder().decode(image.getImageb64());	
		}
		
		if (imageb != null)
			ret = getAnswersFromImage(this.projectId, this.location, this.modelName, imageb );
		else
			ret = "Selecciona una imagen.";
		
	} catch (IOException e) {
		e.printStackTrace();
	}
	return ret;
  }
  
  
  public String getAnswersFromImage(String projectId, String location, String modelName, byte[] image )
  {
	  
	String output = "", ret = "";
	
    // DESDE LA IMAGEN SE OBTIENE UN JSON CON LAS PREGUNTAS
	
	try (VertexAI vertexAI = new VertexAI(projectId, location)) 
    {
      GenerativeModel model = new GenerativeModel(modelName, vertexAI);

      //System.out.println("[textPrompt] >> "+this.textPrompt1);
      Logger.log("[textPrompt] >> "+this.textPrompt1);
      
      Content content = ContentMaker.fromMultiModalData( PartMaker.fromMimeTypeAndData("image/png", image), this.textPrompt1 );
      GenerateContentResponse response = model.generateContent(content);
      output = fixJson(ResponseHandler.getText(response));            
      Logger.log(output);

      //return output;
    }catch(IOException e) {
    	Logger.log("[multimodalMultiImage] IOException >> "+e.getMessage());
    	//e.printStackTrace();
    }catch(Exception e) {
    	Logger.log("[multimodalMultiImage] Exception >> "+e.getMessage());
    	//e.printStackTrace();
    }
    
    // RESUELVE UNA POR UNA LAS PREGUNTAS 

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
        String textPrompt3 = textPrompt2.replace("#pregunta#", pregunta).replace("#respuestas#", respuestas);
        //System.out.println("[textPrompt] >> "+ textPrompt3);
        Logger.log("[textPrompt] >> "+numero+ " " + textPrompt3);
        try {
        
	        TimeUnit.SECONDS.sleep(10);
	          
        } catch (InterruptedException e) {
            e.printStackTrace();
        }		
		try {
			output=fixJson(textInput(projectId, location, modelName, textPrompt3));
		} catch (IOException e1) {
			output="";
			e1.printStackTrace();
		}
	  
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
	            ret +="\n"+numero+" "+letra;
	    	}		    
	    }catch (Exception e) {
	    	e.printStackTrace();
	    	return ret;
	    }
		Logger.log("Respuesta ok: "+numero+" "+output);		  
		Logger2.log(numero+" "+output);
    }// end for preguntas
	return ret;    
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
  public static byte[] readImageFile(String url) throws IOException 
  {
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
 
  
  
  public String getJsonFromImage(Image image) 
  {
	  	// extrae el texto de la imagen y devuelve json con las preguntas y respuestas	  
		byte[] imageb=null;
		String ret ="";
		try (VertexAI vertexAI = new VertexAI(projectId, location))
		{
			if (image.getUrl() != null && image.getUrl().startsWith("http")) 
			{
				imageb = readImageFile(image.getUrl());			
			}else if(image.getImageb64() != null ) {
				imageb = Base64.getDecoder().decode(image.getImageb64());	
			}			
			if (imageb != null) 
			{				
				GenerativeModel model = new GenerativeModel(modelName, vertexAI);		        
		        Logger.log("[textPrompt] >> "+this.textPrompt1);	        
		        Content content = ContentMaker.fromMultiModalData( PartMaker.fromMimeTypeAndData("image/png", image), this.textPrompt1 );
		        GenerateContentResponse response = model.generateContent(content);
		        ret = fixJson(ResponseHandler.getText(response));  
			}else {
				this.error = "Selecciona una imagen.";
			}
		} catch (IOException e) {
			e.printStackTrace();
			ret = "IOException >> " + e.getMessage();
		}catch(Exception e) {
			e.printStackTrace();
			this.error = "Exception >> " + e.getMessage();
	    }
		return ret;
  }
    
}




