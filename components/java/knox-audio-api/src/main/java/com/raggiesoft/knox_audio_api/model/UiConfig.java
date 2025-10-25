package com.raggiesoft.knox_audio_api.model;

import lombok.Data;

/**
 * Represents the UI configuration, containing settings for
 * both album and ambient modes.
 */
@Data
public class UiConfig {

    private ModeConfig albumMode;
    private ModeConfig ambientMode;

}
