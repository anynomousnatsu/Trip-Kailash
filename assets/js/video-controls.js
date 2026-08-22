/**
 * Video Controls JavaScript
 * Handles hero video sound toggle
 */

(function() {
    'use strict';

    /**
     * Minimum viewport width at which a full-motion hero earns its bytes.
     * Below this the poster image is the whole experience.
     */
    var VIDEO_MIN_WIDTH = 768;

    document.addEventListener('DOMContentLoaded', function() {
        initHeroVideoLoading();
        initVideoControls();
    });

    /**
     * Attach the hero video source only when it is worth downloading.
     *
     * The markup ships the URL in data-tk-video-src rather than src, because
     * an autoplaying <video> with a real src downloads the entire file before
     * first paint no matter what preload says. Deciding here means a phone on
     * mobile data gets the poster image instead of tens of megabytes.
     */
    function initHeroVideoLoading() {
        var videos = document.querySelectorAll('.tk-hero-video__video[data-tk-video-src]');

        if (!videos.length) {
            return;
        }

        Array.prototype.forEach.call(videos, function(video) {
            if (!shouldLoadVideo(video)) {
                // Poster attribute already renders; nothing further to do.
                video.classList.add('tk-hero-video__video--poster-only');
                return;
            }

            var source = document.createElement('source');
            source.src = video.getAttribute('data-tk-video-src');
            source.type = 'video/mp4';
            video.appendChild(source);

            video.autoplay = true;
            video.muted = true;
            video.load();

            var playAttempt = video.play();
            if (playAttempt && typeof playAttempt.catch === 'function') {
                // Autoplay refusal is not an error worth logging; the poster
                // stays visible and the sound toggle still works.
                playAttempt.catch(function() {});
            }
        });
    }

    /**
     * @param {HTMLVideoElement} video The hero video element.
     * @return {boolean} Whether this visitor should download the hero video.
     */
    function shouldLoadVideo(video) {
        /*
         * Skipping the video is only safe when there is a poster to show in
         * its place. With no poster configured in Elementor, opting out would
         * leave an empty hero, which is a worse outcome than the download.
         */
        if (!video.getAttribute('poster')) {
            return true;
        }

        // Respect an explicit request for less motion.
        if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return false;
        }

        // Respect an explicit request to save data, and skip slow connections.
        var conn = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
        if (conn) {
            if (conn.saveData) {
                return false;
            }
            if (/(^|-)(slow-2g|2g|3g)$/.test(conn.effectiveType || '')) {
                return false;
            }
        }

        return window.innerWidth >= VIDEO_MIN_WIDTH;
    }

    function initVideoControls() {
        const videos = document.querySelectorAll('.tk-hero-video__video');
        
        videos.forEach(video => {
            const container = video.closest('.tk-hero-video');
            if (!container) return;

            const soundToggle = container.querySelector('.tk-video-sound-toggle');
            if (!soundToggle) return;

            // Ensure video starts muted
            video.muted = true;

            // Add click handler for sound toggle
            soundToggle.addEventListener('click', function() {
                toggleVideoSound(video, soundToggle);
            });

            // Handle video load errors
            video.addEventListener('error', function() {
                console.error('Video failed to load');
                container.classList.add('video-error');
            });

            // Update button state on load
            updateButtonState(video, soundToggle);
        });
    }

    function toggleVideoSound(video, button) {
        video.muted = !video.muted;
        updateButtonState(video, button);
    }

    function updateButtonState(video, button) {
        if (video.muted) {
            button.setAttribute('aria-label', 'Unmute video');
            button.classList.remove('unmuted');
            button.classList.add('muted');
            button.textContent = '🔇';
        } else {
            button.setAttribute('aria-label', 'Mute video');
            button.classList.remove('muted');
            button.classList.add('unmuted');
            button.textContent = '🔊';
        }
    }

})();
