package com.raggiesoft.knox_audio_api.model;

import lombok.Data;

/**
 * Represents a single downloadable file for a track,
 * specifying its format and file path.
 */
@Data
public class Download {

    private String format;
    private String file;

}
