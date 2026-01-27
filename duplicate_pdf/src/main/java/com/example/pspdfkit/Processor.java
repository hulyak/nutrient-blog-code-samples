package com.example.pspdfkit;

import java.io.File;
import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.nio.file.StandardCopyOption;

import org.json.JSONArray;
import org.json.JSONObject;

import okhttp3.MediaType;
import okhttp3.MultipartBody;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;
import okhttp3.ResponseBody;

public final class Processor {
  private static final String API_URL = "https://api.nutrient.io/build";
  private static final String INPUT_PATH = "input_documents/document.pdf";
  private static final String OUTPUT_PATH = "processed_documents/result.pdf";

  public static void main(final String[] args) throws IOException {
    // Get API key from environment variable
    final String apiKey = System.getenv("NUTRIENT_API_KEY");
    if (apiKey == null || apiKey.isEmpty()) {
      throw new IllegalStateException(
        "NUTRIENT_API_KEY environment variable is not set. " +
        "Set it with: export NUTRIENT_API_KEY=your_api_key"
      );
    }

    // Verify input file exists
    final File inputFile = new File(INPUT_PATH);
    if (!inputFile.exists()) {
      throw new IOException("Input file not found: " + INPUT_PATH);
    }

    // Build the request body with PDF and instructions
    final RequestBody body = new MultipartBody.Builder()
      .setType(MultipartBody.FORM)
      .addFormDataPart(
        "document",
        "document.pdf",
        RequestBody.create(
          MediaType.parse("application/pdf"),
          inputFile
        )
      )
      .addFormDataPart(
        "instructions",
        new JSONObject()
          .put("parts", new JSONArray()
            // First page only
            .put(new JSONObject()
              .put("file", "document")
              .put("pages", new JSONObject()
                .put("start", 0)
                .put("end", 0)
              )
            )
            // All pages
            .put(new JSONObject()
              .put("file", "document")
            )
            // Last page only
            .put(new JSONObject()
              .put("file", "document")
              .put("pages", new JSONObject()
                .put("start", -1)
                .put("end", -1)
              )
            )
          ).toString()
      )
      .build();

    final Request request = new Request.Builder()
      .url(API_URL)
      .method("POST", body)
      .addHeader("Authorization", "Bearer " + apiKey)
      .build();

    final OkHttpClient client = new OkHttpClient();

    // Use try-with-resources to ensure response is closed
    try (Response response = client.newCall(request).execute()) {
      final ResponseBody responseBody = response.body();

      if (response.isSuccessful()) {
        if (responseBody == null) {
          throw new IOException("Response body is null");
        }

        // Create output directory if it doesn't exist
        final Path outputPath = Paths.get(OUTPUT_PATH);
        Files.createDirectories(outputPath.getParent());

        // Save the processed PDF
        Files.copy(
          responseBody.byteStream(),
          outputPath,
          StandardCopyOption.REPLACE_EXISTING
        );

        System.out.println("PDF processed successfully: " + OUTPUT_PATH);
      } else {
        final String errorMessage = responseBody != null
          ? responseBody.string()
          : "Unknown error (no response body)";
        throw new IOException("API request failed: " + errorMessage);
      }
    }
  }
}
