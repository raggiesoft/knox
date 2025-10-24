package com.raggiesoft.knox_audio_api.service;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.fasterxml.jackson.dataformat.yaml.YAMLFactory;
import com.raggiesoft.knox_audio_api.model.Album;
import com.raggiesoft.knox_audio_api.model.Download;
import com.raggiesoft.knox_audio_api.model.SourceInfo;
import com.raggiesoft.knox_audio_api.model.Track;
import org.springframework.stereotype.Service;

import java.io.File;
import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;
import java.util.regex.Matcher;
import java.util.regex.Pattern;
import java.util.stream.Collectors;
import java.util.stream.Stream;

/**
 * The Service layer containing the core business logic for the audio API.
 * The @Service annotation tells Spring Boot that this is a component
 * that can be injected into other parts of the application, like the controller.
 */
@Service
public class MusicService {

    // TODO: Make this configurable via application.properties
    private final Path musicLibraryPath = Paths.get("/path/to/your/music-library");

    // Regex to parse track filenames like "1-01-song-name.ogg"
    private final Pattern trackFilePattern = Pattern.compile("^(\\d+)-(\\d+)-(.+)\\.ogg$");

    /**
     * Main public method to get a fully constructed Album object.
     * @param albumName The name of the album directory to process.
     * @return A complete Album object, or null if not found.
     * @throws IOException If there's an error reading files.
     */
    public Album getAlbum(String albumName) throws IOException {
        Path albumPath = musicLibraryPath.resolve(albumName);
        if (!Files.isDirectory(albumPath)) {
            // Or throw a custom "NotFound" exception
            return null;
        }

        // 1. Read the manifest file to get the base album metadata.
        Album album = readManifest(albumPath);

        // 2. Scan the directories and build the list of tracks.
        List<Track> tracks = buildTrackList(albumPath);
        album.setTracks(tracks);

        return album;
    }

    /**
     * Reads and parses the album.yaml file into an Album object.
     */
    private Album readManifest(Path albumPath) throws IOException {
        ObjectMapper mapper = new ObjectMapper(new YAMLFactory());
        File manifestFile = albumPath.resolve("album.yaml").toFile();
        return mapper.readValue(manifestFile, Album.class);
    }

    /**
     * Scans the "streaming" directory to build the list of Track objects.
     */
    private List<Track> buildTrackList(Path albumPath) throws IOException {
        Path streamingPath = albumPath.resolve("streaming");
        List<Track> tracks = new ArrayList<>();

        if (!Files.isDirectory(streamingPath)) {
            return tracks; // Return empty list if 'streaming' folder doesn't exist
        }

        try (Stream<Path> stream = Files.list(streamingPath)) {
            tracks = stream
                    .filter(file -> !Files.isDirectory(file))
                    .map(Path::getFileName)
                    .map(Path::toString)
                    .map(this::createTrackFromFile) // Convert each filename to a Track object
                    .collect(Collectors.toList());
        }

        return tracks;
    }

    /**
     * Creates a single Track object from a filename.
     */
    private Track createTrackFromFile(String filename) {
        Matcher matcher = trackFilePattern.matcher(filename);
        if (!matcher.matches()) {
            return null; // Or handle malformed filenames
        }

        // String discNumber = matcher.group(1); // Currently unused, but available
        String trackNumber = matcher.group(2);
        String webSafeTitle = matcher.group(3);

        Track track = new Track();
        track.setTitle(formatTitle(webSafeTitle));

        SourceInfo sources = new SourceInfo();
        String baseFilename = filename.substring(0, filename.lastIndexOf('.'));

        // Set the streaming path
        sources.setStream("music/" + musicLibraryPath.relativize(this.musicLibraryPath.resolve(baseFilename.substring(0, baseFilename.lastIndexOf('/'))).resolve("streaming").resolve(filename)));

        // Find and add download links
        sources.setDownloads(findDownloads(baseFilename));
        track.setSources(sources);

        return track;
    }

    /**
     * Converts a web-safe-title into a human-readable Title Case title.
     * e.g., "ozone-and-rot" becomes "Ozone And Rot"
     */
    private String formatTitle(String webSafeTitle) {
        return Arrays.stream(webSafeTitle.split("-"))
                .map(word -> Character.toUpperCase(word.charAt(0)) + word.substring(1))
                .collect(Collectors.joining(" "));
    }

    /**
     * Looks in the "download" folder for matching high-quality files.
     */
    private List<Download> findDownloads(String baseFilename) {
        List<Download> downloads = new ArrayList<>();
        Path downloadPath = musicLibraryPath.resolve(baseFilename.substring(0, baseFilename.lastIndexOf('/'))).resolve("download");

        String[] formats = {"mp3", "ogg", "wav"};
        for (String format : formats) {
            Path filePath = downloadPath.resolve(baseFilename + "." + format);
            if (Files.exists(filePath)) {
                Download download = new Download();
                download.setFormat(format.toUpperCase());
                download.setFile("music/" + musicLibraryPath.relativize(filePath));
                downloads.add(download);
            }
        }
        return downloads;
    }
}
