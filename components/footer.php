    </div> <?php // Closes <div class="site-container"> from header.php ?>
    
    <footer class="site-footer">
        <wa-button id="master-music-toggle" variant="primary" circle style="position: fixed; bottom: 1rem; right: 1rem; z-index: 1001;">
            <wa-icon name="fa-pro-volume-slash"></wa-icon>
        </wa-button>

        <?php
        // The page file defines these variables.
        $strDataAlbumJson = $strDataAlbumJson ?? null;
        $intAmbientTrackIndex = $intAmbientTrackIndex ?? null;

        if ($strDataAlbumJson) {
            if ($intAmbientTrackIndex !== null) {
                // Ambient Mode: include data-track-index
                echo "<rs-audio-player data-album-name=\"{$strDataAlbumJson}\" data-track-index=\"{$intAmbientTrackIndex}\">Ambient audio requires JavaScript.</rs-audio-player>";
            } else {
                // Album Mode: omit data-track-index
                echo "<rs-audio-player data-album-name=\"{$strDataAlbumJson}\">The soundtrack requires JavaScript.</rs-audio-player>";
            }
        }
        ?>
    </footer>

    <?php // Scripts section is unchanged from before ?>
    <script type="module" src="<?php echo ASSET_URL; ?>/js/rs-audio-player.js"></script>
    <script>
        const masterToggleBtn = document.getElementById('master-music-toggle');
        const musicEnabled = localStorage.getItem('knoxMusicEnabled') === 'true';
        masterToggleBtn.querySelector('wa-icon').name = musicEnabled ? 'fa-pro-volume' : 'fa-pro-volume-slash';
        masterToggleBtn.addEventListener('click', () => {
            const player = document.querySelector('rs-audio-player');
            if (player) player.toggleMasterMusic(); 
        });
        document.addEventListener('music-toggle', (event) => {
            masterToggleBtn.querySelector('wa-icon').name = event.detail.enabled ? 'fa-pro-volume' : 'fa-pro-volume-slash';
        });
    </script>
</body>
</html>

