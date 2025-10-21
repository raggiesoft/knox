package com.raggiesoft.knox_audio_api.model;
import lombok.Data;
import java.util.List;

/**
 * Represents a single track in the album.
 */
@Data
public class Track {

    private String title;
    private SourceInfo sources;

}

