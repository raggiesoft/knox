# RaggieSoft Audio Player (`<rs-audio-player>`) Documentation

The `<rs-audio-player>` is a project-agnostic, configurable, and stylable Web Component for adding ambient or album-style audio to any website. It is designed to be self-contained and controlled by a single JSON data file.

## Table of Contents

1. [Quick Start](#1-quick-start "null")
2. [The JSON Data File](#2-the-json-data-file "null")
3. [Usage Modes](#3-usage-modes "null")
    - [Album Mode](#album-mode "null")
    - [Ambient Mode](#ambient-mode "null")
4. [Customization](#4-customization "null") 
    - [Styling with CSS Parts](#styling-with-css-parts "null")
    - [Replacing Controls with Slots](#replacing-controls-with-slots "null")
5. [User Preferences & Local Storage](#5-user-preferences--local-storage "null")    

## 1. Quick Start

Setting up the player involves three steps: including the component's script, adding the master opt-in button to your site's chrome, and placing the `<rs-audio-player>` element itself.

### Step 1: Include the Script

Place this `<script>` tag in your site's main HTML file, ideally at the end of the `<body>` tag. This script defines the `<rs-audio-player>` element so the browser knows how to render it.

```
<!-- In your main layout or footer file -->
<script type="module" src="/path/to/cdn/js/rs-audio-player.js"></script>
```

### Step 2: Add the Master Opt-In Button

The player defaults to being off. You must provide a master toggle button somewhere in your UI (like the site footer) to allow users to opt-in to the audio experience.

**HTML:**

```
<!-- Place this in your site's footer, outside the player component -->
<button id="master-music-toggle">
    <!-- The icon inside will be updated by the player script -->
    <i class="fa-pro-solid fa-volume-slash" data-role="icon"></i>
</button>
```

**JavaScript:** This script connects your button to the player component. Place it just after you include the component's main script.

```
<script>
    const masterMusicToggle = document.getElementById('master-music-toggle');
    const audioPlayer = document.querySelector('rs-audio-player');
    const iconElement = masterMusicToggle.querySelector('[data-role="icon"]');

    // Listen for the player's custom event to update the button's icon
    audioPlayer.addEventListener('music-toggle', (event) => {
        const isEnabled = event.detail.enabled;
        iconElement.classList.toggle('fa-volume', isEnabled);
        iconElement.classList.toggle('fa-volume-slash', !isEnabled);
    });

    // Tell the player to toggle its state when the button is clicked
    masterMusicToggle.addEventListener('click', () => {
        audioPlayer.toggleMasterMusic();
    });
</script>
```

### Step 3: Place the Player Component

Finally, add the `<rs-audio-player>` element to your page. It will automatically be fixed to the bottom of the viewport. The attributes you set will determine what music it loads.

```
<!-- This can go anywhere in your <body>, but the footer is a good place. -->
<rs-audio-player
    data-storage-key-id="knox"
    data-json-base-url="https://assets.raggiesoft.com"
    data-album-name="/data/music/sounds-of-telsus-minor.json"
>
    <!-- This fallback content is shown if JavaScript is disabled -->
    <p>Audio player requires JavaScript.</p>
</rs-audio-player>
```

## 2. The JSON Data File

The player is driven entirely by a single JSON file. This file defines the album's metadata, tracklist, asset paths, and UI configuration.

### JSON Structure

|Key|Type|Description|
|---|---|---|
|`albumTitle`|String|The name of the album or soundtrack.|
|`artist`|String|The name of the artist.|
|`assetBaseUrl`|String|The **absolute URL** to the root folder where your music and artwork are stored. **No trailing slash.**|
|`artwork`|String|The relative path to the album artwork from the `assetBaseUrl`.|
|`ui`|Object|An object containing UI configurations for different modes.|
|`tracks`|Array|An array of track objects.|

### Track Object Structure

Each object inside the `tracks` array has the following structure:

|Key|Type|Description|
|---|---|---|
|`title`|String|The name of the track.|
|`sources`|Object|An object containing URLs for different audio formats.|

### Sources Object Structure

|Key|Type|Description|
|---|---|---|
|`stream`|String|The relative path to the **low-bandwidth streaming file** (e.g., a 96kbps OGG). The player uses this file for playback.|
|`downloads`|Array|An array of objects for high-quality download links. Each object has `format` (e.g., "MP3") and `file` (the relative path).|

### UI Object Structure

|Key|Type|Description|
|---|---|---|
|`albumMode`|Object|Configuration for when the player is in album mode. Contains `showNextPrev` (boolean) and `showVolume` (boolean).|
|`ambientMode`|Object|Configuration for when the player is in ambient mode.|

### Example `album.json`

```
{
  "albumTitle": "Sounds of Telsus Minor",
  "artist": "Knox Ambience",
  "assetBaseUrl": "https://assets.raggiesoft.com",
  "artwork": "images/knox-artwork-512.png",
  "ui": {
    "albumMode": {
      "showNextPrev": true,
      "showVolume": true
    },
    "ambientMode": {
      "showNextPrev": false,
      "showVolume": false
    }
  },
  "tracks": [
    {
      "title": "Ozone and Rot",
      "sources": {
        "stream": "music/stream/ozone-and-rot.ogg",
        "downloads": [
          { "format": "MP3", "file": "music/download/ozone-and-rot.mp3" },
          { "format": "OGG", "file": "music/download/ozone-and-rot.ogg" },
          { "format": "WAV", "file": "music/download/ozone-and-rot.wav" }
        ]
      }
    },
    {
      "title": "Cross-Way Neon",
      "sources": {
        "stream": "music/stream/cross-way-neon.ogg",
        "downloads": [
          { "format": "MP3", "file": "music/download/cross-way-neon.mp3" }
        ]
      }
    }
  ]
}
```

## 3. Usage Modes

The player has two distinct modes, controlled by the attributes on the `<rs-audio-player>` element.

### Album Mode

This is the default mode. It loads the entire playlist from the JSON file and enables full controls (next, previous, volume) as defined by the `ui.albumMode` configuration.

**To use Album Mode, simply omit the `data-track-index` attribute.**

```
<rs-audio-player
    data-storage-key-id="knox"
    data-json-base-url="https://assets.raggiesoft.com"
    data-album-name="/data/music/sounds-of-telsus-minor.json"
></rs-audio-player>
```

### Ambient Mode

This mode is for playing a single, looping background track for a specific page. It loads the full album JSON but only plays the track at the specified index. The UI is simplified based on the `ui.ambientMode` configuration.

**To use Ambient Mode, add the `data-track-index` attribute.** The index is 0-based.

```
<!-- This will play the first track (index 0) from the JSON file in a loop. -->
<rs-audio-player
    data-storage-key-id="knox"
    data-json-base-url="https://assets.raggiesoft.com"
    data-album-name="/data/music/sounds-of-telsus-minor.json"
    data-track-index="0"
></rs-audio-player>
```

## 4. Customization

You can customize both the appearance and the HTML of the player's controls.

### Styling with CSS Parts

The component exposes its internal elements as `part`s, which you can style from your global stylesheet using the `::part()` pseudo-element.

|Part Name|Target Element|
|---|---|
|`base`|The main container `<div>` of the player.|
|`prev-button`|The "previous" button.|
|`play-pause-button`|The main play/pause button.|
|`next-button`|The "next" button.|
|`track-info`|The `<div>` wrapping the title and artist.|
|`title`|The `<span>` for the track title.|
|`artist`|The `<span>` for the artist name.|
|`volume-controls`|The `<div>` wrapping the mute button and slider.|
|`mute-button`|The mute/unmute button.|
|`volume-slider`|The volume range slider.|

**Example CSS:**

```
/* In your global stylesheet */
rs-audio-player::part(base) {
  background: navy;
  border-top-color: steelblue;
}

rs-audio-player::part(title) {
  color: cyan;
}
```

### Replacing Controls with Slots

If you want to use your own HTML for the buttons (e.g., native `<button>`s instead of `<wa-button>`s), you can use `slot`s. Provide an element with a `slot="..."` attribute, and it will replace the default component.

**IMPORTANT:** To make your custom slotted element interactive, it **must** have a `data-action="..."` attribute. To allow the component to update your icon, the icon element itself **must** have a `data-role="icon"` attribute.

|Slot Name|Required `data-action`|
|---|---|
|`prev-button`|`prev`|
|`play-pause-button`|`play-pause`|
|`next-button`|`next`|
|`mute-button`|`mute`|
|`volume-slider`|(none)|

**Example using native `<button>` and Font Awesome `<i>` tags:**

```
<rs-audio-player ...>
    <!-- Replace the default play/pause button -->
    <button slot="play-pause-button" data-action="play-pause">
        <i class="fa-pro-solid fa-play" data-role="icon"></i>
    </button>
    
    <!-- Replace the default mute button -->
    <button slot="mute-button" data-action="mute">
        <i class="fa-pro-solid fa-volume" data-role="icon"></i>
    </button>

    <!-- Replace the default volume slider -->
    <input type="range" slot="volume-slider" min="0" max="1" step="0.01" value="1">
</rs-audio-player>
```

## 5. User Preferences & Local Storage

The player is designed to respect the user's choice.

- **Opt-In by Default:** The player is hidden and inactive until the user clicks the master opt-in button.
- **Persistence:** The user's preference (enabled or disabled) is saved to their browser's Local Storage.
- **Unique Storage Key:** The `data-storage-key-id` attribute is used to create a unique key in Local Storage. For `data-storage-key-id="knox"`, the key will be `knoxMusicEnabled`. This allows you to use the same component on different websites without their preferences colliding.