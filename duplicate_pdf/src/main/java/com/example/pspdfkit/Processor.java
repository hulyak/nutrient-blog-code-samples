package com.example.pspdfkit;

import java.io.File;
import java.io.IOException;
import java.nio.file.FileSystems;
import java.nio.file.Files;
import java.nio.file.StandardCopyOption;

import org.json.JSONArray;
import org.json.JSONObject;

import okhttp3.MediaType;
import okhttp3.MultipartBody;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;

public final class Processor {
  public static void main(final String[] args) throws IOException {
    final RequestBody body = new MultipartBody.Builder()
      .setType(MultipartBody.FORM)
      .addFormDataPart(
        "document",
        "document.pdf",
        RequestBody.create(
          MediaType.parse("application/pdf"),
          new File("input_documents/document.pdf")
        )
      )
      .addFormDataPart(
        "instructions",
        new JSONObject()
          .put("parts", new JSONArray()
            .put(new JSONObject()
              .put("file", "document")
              .put("pages", new JSONObject()
                .put("start", 0)
                .put("end", 0)
              )
            )
            .put(new JSONObject()
              .put("file", "document")
            )
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
      .url("https://api.nutrient.io/build")
      .method("POST", body)
      .addHeader("Authorization", "Bearer pdf_live_TK4VeBhzh4K7hq0wBjJuvfiekbOb62TqwjGycFYqA5Y")
      .build();

    final OkHttpClient client = new OkHttpClient()
      .newBuilder()
      .build();

    final Response response = client.newCall(request).execute();

    if (response.isSuccessful()) {
      Files.copy(
        response.body().byteStream(),
        FileSystems.getDefault().getPath("processed_documents/result.pdf"),
        StandardCopyOption.REPLACE_EXISTING
      );
    } else {
      // Handle the error.
      throw new IOException(response.body().string());
    }
  }
}
