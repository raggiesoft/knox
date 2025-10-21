package com.raggiesoft.knox_audio_api.model;

import lombok.Data;

/**
 * Represents the specific UI settings for a single player mode,
 * determining which controls are visible.
 */
@Data
public class ModeConfig {

    private boolean showNextPrev;
    private boolean showVolume;

}
