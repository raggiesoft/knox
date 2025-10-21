class RaggieSoftAudioPlayer extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });

        // Internal state
        this.audio = new Audio();
        this.playlist = [];
        this.currentTrackIndex = 0;
        this.albumData = {};
        this.musicEnabled = localStorage.getItem('knoxMusicEnabled') === 'true';
        this.uiConfig = { showNextPrev: false, showVolume: false }; // Default UI
        this.lastVolume = 1; // For mute/unmute functionality

        // Bind 'this' for event handlers
        this.togglePlayPause = this.togglePlayPause.bind(this);
        this.toggleMasterMusic = this.toggleMasterMusic.bind(this);
        this.playNext = this.playNext.bind(this);
        this.playPrev = this.playPrev.bind(this);
        this.handleVolumeChange = this.handleVolumeChange.bind(this);
        this.toggleMute = this.toggleMute.bind(this);
    }

    connectedCallback() {
        // Render is called first to create the shadow DOM
        // loadData will then populate it.
        this.loadData();
    }

    static get observedAttributes() {
        return ['data-album-name', 'data-song-name'];
    }

    async loadData() {
        const albumPath = this.getAttribute('data-album-name');
        const songPath = this.getAttribute('data-song-name');
        const baseUrl = 'https://raggiesoft-assets.nyc3.digitaloceanspaces.com';

        try {
            let data;
            if (albumPath) {
                const response = await fetch(baseUrl + albumPath);
                data = await response.json();
                this.playlist = data.tracks;
                this.audio.loop = false;
            } else if (songPath) {
                const response = await fetch(baseUrl + songPath);
                data = await response.json();
                this.playlist = [data.track];
                this.audio.loop = true;
            }
            
            this.albumData = data;
            // Merge loaded UI config over defaults
            this.uiConfig = { ...this.uiConfig, ...data.ui };

            // Now that we have the UI config, we can render and setup the player
            this.render();
            this.setupPlayer();

        } catch (error) {
            console.error('Error loading audio data:', error);
            // We need to render the basic structure to show an error
            if (!this.shadowRoot.innerHTML) this.render();
            this.shadowRoot.querySelector('.track-info .title').textContent = 'Error Loading';
        }
    }

    setupPlayer() {
        if (!this.musicEnabled) {
            this.style.display = 'none';
            return;
        }

        this.style.display = 'block';

        // Attach event listeners
        this.playPauseBtn.addEventListener('click', this.togglePlayPause);
        this.audio.addEventListener('ended', this.playNext);
        this.audio.addEventListener('play', () => this.updatePlayPauseIcon(false));
        this.audio.addEventListener('pause', () => this.updatePlayPauseIcon(true));

        // Conditionally attach listeners for full controls
        if (this.prevBtn) this.prevBtn.addEventListener('click', this.playPrev);
        if (this.nextBtn) this.nextBtn.addEventListener('click', this.playNext);
        if (this.volumeSlider) this.volumeSlider.addEventListener('sl-input', this.handleVolumeChange);
        if (this.muteBtn) this.muteBtn.addEventListener('click', this.toggleMute);

        // Media Session API
        navigator.mediaSession.setActionHandler('play', this.togglePlayPause);
        navigator.mediaSession.setActionHandler('pause', this.togglePlayPause);
        if (this.playlist.length > 1) {
            navigator.mediaSession.setActionHandler('nexttrack', this.playNext);
            navigator.mediaSession.setActionHandler('previoustrack', this.playPrev);
        }
        
        if (this.playlist.length > 0) {
            this.loadTrack(this.currentTrackIndex, false); // Load first track, don't play
        }
    }
    
    loadTrack(index, shouldPlay = true) {
        if (index < 0 || index >= this.playlist.length) return;
        this.currentTrackIndex = index;
        const track = this.playlist[index];
        this.audio.src = track.url;
        this.updateUIText(track.title, this.albumData.artist);
        this.updateMediaSession(track);
        if (shouldPlay) {
            this.audio.play().catch(e => console.warn("Audio play prevented by browser."));
        }
    }
    
    togglePlayPause() {
        if (this.audio.paused) {
            if (!this.audio.src) {
                this.loadTrack(0, true);
            } else {
                this.audio.play().catch(e => console.warn("Audio play prevented by browser."));
            }
        } else {
            this.audio.pause();
        }
    }
    
    // This is now called from the EXTERNAL button in footer.php
    toggleMasterMusic() {
        this.musicEnabled = !this.musicEnabled;
        localStorage.setItem('knoxMusicEnabled', this.musicEnabled);
        
        if (this.musicEnabled) {
            this.style.display = 'block';
            if (!this.audio.src && this.playlist.length > 0) this.loadTrack(0, false);
        } else {
            this.audio.pause();
            this.style.display = 'none';
        }
        this.updateMasterToggleIcon(); // Dispatch event
    }
    
    playNext() {
        if (this.playlist.length <= 1 && !this.audio.loop) return;
        const newIndex = (this.currentTrackIndex + 1) % this.playlist.length;
        this.loadTrack(newIndex);
    }
    
    playPrev() {
        if (this.playlist.length <= 1) return;
        const newIndex = (this.currentTrackIndex - 1 + this.playlist.length) % this.playlist.length;
        this.loadTrack(newIndex);
    }

    handleVolumeChange(event) {
        const volume = event.target.value;
        this.audio.volume = volume;
        this.audio.muted = volume === 0;
        this.updateMuteIcon(volume === 0);
    }

    toggleMute() {
        if (this.audio.muted || this.audio.volume === 0) {
            // Unmute: restore to last volume or full if last was 0
            const newVolume = this.lastVolume > 0 ? this.lastVolume : 1;
            this.audio.volume = newVolume;
            this.volumeSlider.value = newVolume;
            this.audio.muted = false;
            this.updateMuteIcon(false);
        } else {
            // Mute: save current volume and set to 0
            this.lastVolume = this.audio.volume;
            this.audio.volume = 0;
            this.volumeSlider.value = 0;
            this.audio.muted = true;
            this.updateMuteIcon(true);
        }
    }

    updatePlayPauseIcon(isPaused) {
        this.playPauseBtn.querySelector('wa-icon').name = isPaused ? 'fa-pro-play' : 'fa-pro-pause';
    }

    updateMuteIcon(isMuted) {
        if (this.muteBtn) {
            this.muteBtn.querySelector('wa-icon').name = isMuted ? 'fa-pro-volume-slash' : 'fa-pro-volume';
        }
    }

    updateMasterToggleIcon() {
        const event = new CustomEvent('music-toggle', { detail: { enabled: this.musicEnabled }, bubbles: true, composed: true });
        this.dispatchEvent(event);
    }

    updateUIText(title, artist) {
        this.shadowRoot.querySelector('.track-info .title').textContent = title;
        this.shadowRoot.querySelector('.track-info .artist').textContent = artist;
    }
    
    updateMediaSession(track) {
        navigator.mediaSession.metadata = new MediaMetadata({
            title: track.title,
            artist: this.albumData.artist,
            album: this.albumData.albumTitle,
            artwork: [{ src: this.albumData.artworkUrl, sizes: '512x512', type: 'image/png' }]
        });
        navigator.mediaSession.playbackState = this.audio.paused ? "paused" : "playing";
    }

    render() {
        this.shadowRoot.innerHTML = `
            <style>
                :host { display: block; position: fixed; bottom: 0; left: 0; right: 0; background: rgba(10, 20, 30, 0.9); backdrop-filter: blur(10px); padding: 0.5rem 1rem; z-index: 1000; border-top: 1px solid rgba(100, 120, 140, 0.3); }
                .player-container { display: flex; align-items: center; justify-content: center; gap: 0.5rem; max-width: 600px; margin: 0 auto; }
                .track-info { flex-grow: 1; text-align: center; color: #e0e0e0; overflow: hidden; min-width: 120px; }
                .track-info .title { display: block; font-weight: bold; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
                .track-info .artist { display: block; font-size: 0.8em; color: #a0a0a0; }
                .volume-controls { display: flex; align-items: center; gap: 0.5rem; }
                wa-range { width: 100px; --thumb-size: 14px; }
            </style>
            <div class="player-container">
                ${this.uiConfig.showNextPrev ? `<wa-button id="prev-btn" variant="icon"><wa-icon name="fa-pro-backward-step"></wa-icon></wa-button>` : ''}
                
                <wa-button id="play-pause-btn" variant="icon" size="large">
                    <wa-icon name="fa-pro-play"></wa-icon>
                </wa-button>
                
                ${this.uiConfig.showNextPrev ? `<wa-button id="next-btn" variant="icon"><wa-icon name="fa-pro-forward-step"></wa-icon></wa-button>` : ''}

                <div class="track-info">
                    <span class="title">Music Paused</span>
                    <span class="artist">Knox Ambience</span>
                </div>

                ${this.uiConfig.showVolume ? `
                <div class="volume-controls">
                    <wa-button id="mute-btn" variant="icon">
                        <wa-icon name="fa-pro-volume"></wa-icon>
                    </wa-button>
                    <wa-range id="volume-slider" min="0" max="1" step="0.01" value="1"></wa-range>
                </div>
                ` : ''}
            </div>
        `;
        // Query for elements now that they are rendered
        this.playPauseBtn = this.shadowRoot.getElementById('play-pause-btn');
        this.prevBtn = this.shadowRoot.getElementById('prev-btn');
        this.nextBtn = this.shadowRoot.getElementById('next-btn');
        this.muteBtn = this.shadowRoot.getElementById('mute-btn');
        this.volumeSlider = this.shadowRoot.getElementById('volume-slider');
    }
}

customElements.define('rs-audio-player', RaggieSoftAudioPlayer);

