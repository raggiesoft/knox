package com.raggiesoft.knox_audio_api.model;

import lombok.Data;
import java.util.List;

/**
 * Represents the source file information for a single track,
 * including the streaming file and a list of downloadable files.
 */
@Data
public class SourceInfo {

    private String stream;
    private List<Download> downloads;

}
