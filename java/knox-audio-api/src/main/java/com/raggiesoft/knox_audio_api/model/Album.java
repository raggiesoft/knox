package com.raggiesoft.knox_audio_api.model;
import lombok.Data;
import java.util.List; // This line fixes the error


/**
 * Represents the top-level structure of the album.yaml file.
 * This is the main object that will be serialized into the final JSON response.
 * The @Data annotation from Lombok automatically generates getters, setters,
 * toString(), equals(), and hashCode() methods for all fields.
 */
@Data
public class Album {

    private String albumTitle;
    private String artist;
    private String assetBaseUrl;
    private String artwork;
    private UiConfig ui;
    private List<Track> tracks;

}