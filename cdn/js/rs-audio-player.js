/**
 * RaggieSoft Audio Player <rs-audio-player>
 *
 * This file defines a custom HTML element (a Web Component) that acts as a
 * site-wide audio player. It is designed to be self-contained, reusable,
 * and configurable via data attributes and a central JSON file.
 *
 * --- Core Concepts of a Web Component ---
 * 1.  Custom Element: A custom HTML tag (<rs-audio-player>) that we define.
 * 2.  Shadow DOM: An encapsulated DOM tree for the component. Its styles
 * and scripts don't leak out, and outside styles don't leak in.
 * 3.  Lifecycle Callbacks: Special methods that run at different points,
 * like `connectedCallback()` when the element is added to the page.
 *
 * --- Slots & Customization ---
 * This component uses named slots to allow for custom buttons and UI.
 * To replace a default control, provide an element with the corresponding `slot` attribute.
 * The component will automatically attach the correct event listener to any element
 * inside a slot that has a `data-action` attribute.
 *
 * To allow the component to update your custom icon, add a `data-role="icon"`
 * attribute to the icon element itself (e.g., <i class="fa-pro-play" data-role="icon">).
 * The component will then update its `name` (for <wa-icon>) or `class` (for <i>).
 *
 * Available Slots & Required `data-action` attributes:
 * - slot="prev-button": Needs an element with `data-action="prev"`
 * - slot="play-pause-button": Needs an element with `data-action="play-pause"`
 * - slot="next-button": Needs an element with `data-action="next"`
 * - slot="mute-button": Needs an element with `data-action="mute"`
 * - slot="volume-slider": Needs a range input/component that emits an `input` or `sl-input` event.
 *
 * If you do not provide an element for a slot, a default <wa-button> will be rendered.
 */
class RaggieSoftAudioPlayer extends HTMLElement {

    /**
     * The constructor is the first thing that runs when an instance of this
     * element is created (either by JavaScript or by the HTML parser).
     *
     * Its primary job is to:
     * - Set up the initial state of the component (e.g., creating the audio object).
     * - Attach the Shadow DOM, which provides encapsulation.
     * - Bind the `this` context for all event handler methods. This is crucial
     * to ensure that when a method like `togglePlayPause` is called by an
     * event listener, `this` still refers to the component instance.
     */
    constructor() {
        // `super()` MUST be the first call in the constructor. It calls the
        // constructor of the class we are extending (HTMLElement).
        super();

        // Attach a Shadow DOM tree to this element.
        // - `mode: 'open'` means you can access the shadow DOM from outside
        //   JavaScript (e.g., element.shadowRoot), which is useful for debugging.
        this.attachShadow({ mode: 'open' });

        // --- Internal State Properties ---
        // These properties will hold the state of our player throughout its lifecycle.

        // The core HTML <audio> element that will handle playback.
        this.audio = new Audio();

        // An array to hold the track objects from our JSON file.
        this.playlist = [];

        // The index of the currently loaded or playing track in the playlist array.
        this.currentTrackIndex = 0;

        // An object to hold the entire parsed JSON data (album title, artwork, etc.).
        this.albumData = {};
        
        // --- Project-Agnostic Local Storage ---
        // Get a unique ID from an attribute to create a project-specific key.
        // This makes the component reusable across different websites.
        const storageId = this.getAttribute('data-storage-key-id') || 'rs-audio-player';
        this.storageKey = `${storageId}MusicEnabled`;

        // The user's master opt-in preference, loaded from localStorage.
        this.musicEnabled = localStorage.getItem(this.storageKey) === 'true';

        // An object to hold the UI configuration (e.g., show/hide buttons).
        this.uiConfig = {};

        // Stores the volume level before muting, so we can restore it.
        this.lastVolume = 1;

        // --- Method Binding ---
        // We explicitly bind `this` for all methods that will be used as
        // event handlers. This ensures they don't lose their context.
        this.togglePlayPause = this.togglePlayPause.bind(this);
        this.toggleMasterMusic = this.toggleMasterMusic.bind(this);
        this.playNext = this.playNext.bind(this);
        this.playPrev = this.playPrev.bind(this);
        this.handleVolumeChange = this.handleVolumeChange.bind(this);
        this.toggleMute = this.toggleMute.bind(this);
    }

    /**
     * This is a lifecycle callback that is automatically invoked when the
     * custom element is connected to the document's DOM.
     *
     * Think of it as the "start" or "initialize" function for the component.
     * It's the perfect place to fetch data and render the initial UI.
     */
    connectedCallback() {
        // Once the component is on the page, we start the data loading process.
        this.loadData();
    }

    /**
     * This static getter is part of the custom element specification.
     * It tells the browser which attributes to "watch" for changes.
     * If any of these attributes are added, removed, or changed, the
     * `attributeChangedCallback()` method will be invoked.
     *
     * @returns {string[]} An array of attribute names to observe.
     */
    static get observedAttributes() {
        // We are watching these attributes to know what music to load.
        return ['data-album-name', 'data-track-index', 'data-storage-key-id', 'data-json-base-url'];
    }

    /**
     * Asynchronously fetches and processes the album JSON data.
     * This is the main logic driver for the component. It determines
     * whether to run in "Album Mode" or "Ambient Mode".
     */
    async loadData() {
        // Get the path to the album JSON from the element's attribute.
        const albumPath = this.getAttribute('data-album-name');
        if (!albumPath) return; // Exit if the attribute isn't set.

        // Check for the track-index attribute to determine the mode.
        const trackIndexAttr = this.getAttribute('data-track-index');
        const isAmbientMode = trackIndexAttr !== null;
        const ambientTrackIndex = parseInt(trackIndexAttr, 10);
        
        // --- Dynamic Base URL ---
        // The base URL for the JSON file is now provided by an attribute,
        // making the component independent of any specific CDN.
        const jsonBaseUrl = this.getAttribute('data-json-base-url') || '';

        try {
            // Fetch the JSON file using the provided base URL and path.
            const response = await fetch(jsonBaseUrl + albumPath);
            const data = await response.json();
            this.albumData = data; // Store the entire dataset.

            // --- Mode Selection Logic ---
            if (isAmbientMode) {
                // AMBIENT MODE: The playlist is just a single track.
                this.playlist = [data.tracks[ambientTrackIndex]];
                this.audio.loop = true; // Ambient tracks should loop automatically.
                this.uiConfig = data.ui.ambientMode; // Load UI config for ambient mode.
            } else {
                // ALBUM MODE: The playlist is the full list of tracks.
                this.playlist = data.tracks;
                this.audio.loop = false; // Tracks in album mode play one after another.
                this.uiConfig = data.ui.albumMode; // Load UI config for album mode.
            }

            // Now that we have data, render the component's HTML.
            this.render();
            // After rendering, set up all the event listeners.
            this.setupPlayer();

        } catch (error) {
            console.error('Error loading audio data:', error);
            // If fetching fails, render the player with an error message.
            if (!this.shadowRoot.innerHTML) this.render();
            this.shadowRoot.querySelector('[part="title"]').textContent = 'Error Loading';
        }
    }

    /**
     * Attaches all necessary event listeners to the player's UI elements
     * and the browser's Media Session API.
     */
    setupPlayer() {
        // If the user has opted out of music, hide the component entirely.
        if (!this.musicEnabled) {
            this.style.display = 'none';
            return;
        }
        this.style.display = 'block'; // Make sure it's visible if enabled.

        // --- Attach Event Listeners to Shadow DOM Elements ---
        this.playPauseControl.addEventListener('click', this.togglePlayPause);
        this.audio.addEventListener('ended', this.playNext); // For playlist progression
        this.audio.addEventListener('play', () => this.updatePlayPauseIcon(false));
        this.audio.addEventListener('pause', () => this.updatePlayPauseIcon(true));

        // Conditionally add listeners for controls that might not exist.
        if (this.prevControl) this.prevControl.addEventListener('click', this.playPrev);
        if (this.nextControl) this.nextControl.addEventListener('click', this.playNext);
        // Listen for both native `input` and Web Awesome's `sl-input`
        if (this.volumeSlider) this.volumeSlider.addEventListener('input', this.handleVolumeChange);
        if (this.volumeSlider) this.volumeSlider.addEventListener('sl-input', this.handleVolumeChange);
        if (this.muteControl) this.muteControl.addEventListener('click', this.toggleMute);

        // --- Set up Media Session API Handlers ---
        // These connect the player to the OS (lock screen, media keys).
        navigator.mediaSession.setActionHandler('play', this.togglePlayPause);
        navigator.mediaSession.setActionHandler('pause', this.togglePlayPause);
        
        // Only register next/prev handlers if we are in Album Mode.
        if (this.albumData.tracks.length > 1 && !this.audio.loop) {
            navigator.mediaSession.setActionHandler('nexttrack', this.playNext);
            navigator.mediaSession.setActionHandler('previoustrack', this.playPrev);
        }
        
        // If we have tracks, load the first one but don't play it yet.
        // This preloads the metadata for a faster start.
        if (this.playlist.length > 0) {
            this.loadTrack(0, false);
        }
    }
    
    /**
     * Loads a specific track into the audio element and updates the UI.
     * @param {number} index - The index of the track to load from the playlist.
     * @param {boolean} [shouldPlay=true] - Whether to start playing immediately.
     */
    loadTrack(index, shouldPlay = true) {
        if (index < 0 || index >= this.playlist.length) return;
        this.currentTrackIndex = index;
        const track = this.playlist[index];
        
        // Construct the full URL using the base URL and the dedicated stream file.
        this.audio.src = `${this.albumData.assetBaseUrl}/${track.sources.stream}`;
        
        // Update the player's text display and the OS media session.
        this.updateUIText(track.title, this.albumData.artist);
        this.updateMediaSession(track);

        if (shouldPlay) {
            // The `.catch()` is important to prevent console errors if the browser
            // blocks autoplay before the user has interacted with the page.
            this.audio.play().catch(e => console.warn("Audio play prevented by browser."));
        }
    }
    
    /**
     * Toggles the playback state between play and pause.
     */
    togglePlayPause() {
        if (this.audio.paused) {
            // If the src isn't set, this is the first play action.
            if (!this.audio.src) this.loadTrack(0, true);
            else this.audio.play().catch(e => console.warn("Audio play prevented by browser."));
        } else {
            this.audio.pause();
        }
    }
    
    /**
     * Toggles the master music enabled/disabled state. This is called
     * by the external button in the site's footer.
     */
    toggleMasterMusic() {
        this.musicEnabled = !this.musicEnabled;
        // Use the generic, project-agnostic storage key.
        localStorage.setItem(this.storageKey, this.musicEnabled);
        
        if (this.musicEnabled) {
            // If music was just enabled, show the player.
            this.style.display = 'block';
            // If no track is loaded, load the first one (paused).
            if (!this.audio.src && this.playlist.length > 0) this.loadTrack(0, false);
        } else {
            // If music was just disabled, pause and hide the player.
            this.audio.pause();
            this.style.display = 'none';
        }
        // Notify the external button to update its icon.
        this.updateMasterToggleIcon();
    }
    
    /**
     * Plays the next track in the playlist. Does nothing in Ambient Mode.
     */
    playNext() {
        if (this.audio.loop) return; // In ambient mode, `ended` event will just loop.
        const newIndex = (this.currentTrackIndex + 1) % this.playlist.length;
        this.loadTrack(newIndex);
    }
    
    /**
     * Plays the previous track in the playlist. Does nothing in Ambient Mode.
     */
    playPrev() {
        if (this.audio.loop) return;
        // The `+ this.playlist.length` handles the case where the index is 0.
        const newIndex = (this.currentTrackIndex - 1 + this.playlist.length) % this.playlist.length;
        this.loadTrack(newIndex);
    }

    /**
     * Handles input from the volume slider.
     * @param {Event} event - The `sl-input` or `input` event from the range control.
     */
    handleVolumeChange(event) {
        const volume = event.target.value;
        this.audio.volume = volume;
        this.audio.muted = (volume == 0);
        this.updateMuteIcon(volume == 0);
    }

    /**
     * Toggles the mute state of the audio.
     */
    toggleMute() {
        if (this.audio.muted || this.audio.volume === 0) {
            // Unmuting: restore to the last known volume, or 100% if unknown.
            const newVolume = this.lastVolume > 0 ? this.lastVolume : 1;
            this.audio.volume = newVolume;
            if (this.volumeSlider) this.volumeSlider.value = newVolume;
            this.audio.muted = false;
            this.updateMuteIcon(false);
        } else {
            // Muting: store the current volume, then set to 0.
            this.lastVolume = this.audio.volume;
            this.audio.volume = 0;
            if (this.volumeSlider) this.volumeSlider.value = 0;
            this.audio.muted = true;
            this.updateMuteIcon(true);
        }
    }

    // --- UI Update Helper Methods ---

    /**
     * Updates the play/pause icon based on playback state.
     * It intelligently handles <wa-icon> and <i> tags.
     * @param {boolean} isPaused - Whether the audio is currently paused.
     */
    updatePlayPauseIcon(isPaused) { 
        const iconElement = this.playPauseControl.querySelector('[data-role="icon"]');
        if (!iconElement) return;

        const playIcon = 'fa-pro-play';
        const pauseIcon = 'fa-pro-pause';

        if (iconElement.tagName === 'WA-ICON') {
            iconElement.name = isPaused ? playIcon : pauseIcon;
        } else { // Assume it's an <i> tag or similar
            iconElement.classList.toggle(playIcon, isPaused);
            iconElement.classList.toggle(pauseIcon, !isPaused);
        }
    }
    
    /**
     * Updates the mute/volume icon based on mute state.
     * @param {boolean} isMuted - Whether the audio is currently muted.
     */
    updateMuteIcon(isMuted) { 
        if (!this.muteControl) return;
        const iconElement = this.muteControl.querySelector('[data-role="icon"]');
        if (!iconElement) return;

        const volumeIcon = 'fa-pro-volume';
        const muteIcon = 'fa-pro-volume-slash';

        if (iconElement.tagName === 'WA-ICON') {
            iconElement.name = isMuted ? muteIcon : volumeIcon;
        } else {
            iconElement.classList.toggle(muteIcon, isMuted);
            iconElement.classList.toggle(volumeIcon, !isMuted);
        }
    }
    
    /**
     * Dispatches a custom event to notify the external opt-in button
     * that the music enabled state has changed.
     */
    updateMasterToggleIcon() {
        const event = new CustomEvent('music-toggle', {
            detail: { enabled: this.musicEnabled },
            bubbles: true,  // Allows the event to bubble up through the DOM
            composed: true // Allows the event to cross the Shadow DOM boundary
        });
        this.dispatchEvent(event);
    }
    
    updateUIText(title, artist) { 
        const titleEl = this.shadowRoot.querySelector('[part="title"]');
        const artistEl = this.shadowRoot.querySelector('[part="artist"]');
        if (titleEl) titleEl.textContent = title;
        if (artistEl) artistEl.textContent = artist;
    }
    
    /**
     * Updates the browser's Media Session with the current track's metadata.
     * This controls what is shown on the OS lock screen, media controls, etc.
     * @param {object} track - The current track object.
     */
    updateMediaSession(track) {
        const fullArtworkUrl = `${this.albumData.assetBaseUrl}/${this.albumData.artwork}`;

        navigator.mediaSession.metadata = new MediaMetadata({
            title: track.title,
            artist: this.albumData.artist,
            album: this.albumData.albumTitle,
            artwork: [
                { src: fullArtworkUrl, sizes: '512x512', type: 'image/png' }
            ]
        });
        navigator.mediaSession.playbackState = this.audio.paused ? "paused" : "playing";
    }

    /**
     * Finds the interactive element for a given control, whether it was
     * provided by the user via a slot or is the default fallback content.
     * @param {string} slotName - The name of the slot to check.
     * @returns {HTMLElement | null} The interactive element or null if not found.
     */
    _getControlElement(slotName) {
        const slot = this.shadowRoot.querySelector(`slot[name="${slotName}"]`);
        if (!slot) return null;

        // Check if the user has provided their own element(s) in the slot.
        const assignedElements = slot.assignedElements({ flatten: true });
        if (assignedElements.length > 0) {
            // Return the first element the user provided.
            return assignedElements[0];
        }

        // If no element was provided, find the default element within the slot.
        return slot.querySelector('[data-action]');
    }

    /**
     * Renders the component's internal HTML structure into the Shadow DOM.
     * It uses the `this.uiConfig` object to conditionally show/hide controls.
     */
    render() {
        // This uses a template literal to build the HTML and CSS string.
        this.shadowRoot.innerHTML = `
            <style>
                /* Scoped CSS: These styles ONLY apply inside this component. */
                :host { /* ':host' selects the <rs-audio-player> element itself */
                    display: block;
                    position: fixed;
                    bottom: 0;
                    left: 0;
                    right: 0;
                    background: rgba(10, 20, 30, 0.9);
                    backdrop-filter: blur(10px);
                    padding: 0.5rem 1rem;
                    z-index: 1000;
                    border-top: 1px solid rgba(100, 120, 140, 0.3);
                }
                .player-container {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 0.5rem;
                    max-width: 600px;
                    margin: 0 auto;
                }
                .track-info {
                    flex-grow: 1;
                    text-align: center;
                    color: #e0e0e0;
                    overflow: hidden;
                    min-width: 120px;
                }
                .track-info .title { display: block; font-weight: bold; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
                .track-info .artist { display: block; font-size: 0.8em; color: #a0a0a0; }
                .volume-controls { display: flex; align-items: center; gap: 0.5rem; }
                /* Default styling for a slotted range input */
                ::slotted(input[type="range"]) {
                    width: 100px;
                }
            </style>
            <div class="player-container" part="base">
                ${this.uiConfig.showNextPrev ? `
                    <slot name="prev-button">
                        <wa-button part="prev-button" data-action="prev">
                            <wa-icon name="fa-pro-backward-step" data-role="icon"></wa-icon>
                        </wa-button>
                    </slot>` : ''}
                
                <slot name="play-pause-button">
                    <wa-button part="play-pause-button" size="large" data-action="play-pause">
                        <wa-icon name="fa-pro-play" data-role="icon"></wa-icon>
                    </wa-button>
                </slot>
                
                ${this.uiConfig.showNextPrev ? `
                    <slot name="next-button">
                        <wa-button part="next-button" data-action="next">
                            <wa-icon name="fa-pro-forward-step" data-role="icon"></wa-icon>
                        </wa-button>
                    </slot>` : ''}
                
                <div class="track-info" part="track-info">
                    <span class="title" part="title">Music Paused</span>
                    <span class="artist" part="artist">Knox Ambience</span>
                </div>
                
                ${this.uiConfig.showVolume ? `
                <div class="volume-controls" part="volume-controls">
                    <slot name="mute-button">
                        <wa-button part="mute-button" data-action="mute">
                            <wa-icon name="fa-pro-volume" data-role="icon"></wa-icon>
                        </wa-button>
                    </slot>
                    <slot name="volume-slider">
                        <wa-range part="volume-slider" min="0" max="1" step="0.01" value="1" style="width: 100px; --thumb-size: 14px;"></wa-range>
                    </slot>
                </div>
                ` : ''}
            </div>
        `;

        // --- Element Caching ---
        // After rendering, we find the interactive controls (slotted or default)
        // and store references for easy access.
        this.playPauseControl = this._getControlElement('play-pause-button');
        this.prevControl = this._getControlElement('prev-button');
        this.nextControl = this._getControlElement('next-button');
        this.muteControl = this._getControlElement('mute-button');
        this.volumeSlider = this._getControlElement('volume-slider');
    }
}

/**
 * Finally, we register our custom element with the browser.
 * This tells the browser that whenever it encounters the tag '<rs-audio-player>',
 * it should use our `RaggieSoftAudioPlayer` class to power it.
 * The tag name MUST contain a hyphen.
 */
customElements.define('rs-audio-player', RaggieSoftAudioPlayer);

