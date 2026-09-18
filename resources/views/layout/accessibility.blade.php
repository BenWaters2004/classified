<!-- Accessibility Button -->
<div id="accessibility-toggle-wrapper">
    <button id="accessibility-toggle" aria-label="Accessibility Menu">
        <i class="fa-solid fa-universal-access"></i>
    </button>
    <span id="accessibility-desc">Accessibility settings</span>
</div>

<!-- Accessibility Panel -->
<div id="accessibility-panel">
    <!-- Header -->
    <div class="accessibility-header">
        <button id="accessibility-close" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        <div class="accessibility-title">Accessibility</div>
    </div>

    <!-- Scrollable Content -->
    <div class="accessibility-body">
        <div class="accessibility-section">
            <label>Colour Adjustments:</label>
            <div class="color-adjustment-grid">
                <div class="color-option" onclick="setContrast('mono')">
                    <i class="fa-solid fa-eye-slash"></i>
                    <span class="color-title">Monochrome</span>
                    <div class="color-overlay">
                        <p>Switch to black and white</p>
                    </div>
                </div>
                <div class="color-option" onclick="setContrast('dark')">
                    <i class="fa-solid fa-moon"></i>
                    <span class="color-title">Dark HC</span>
                    <div class="color-overlay">
                        <p>High contrast dark background</p>
                    </div>
                </div>
                <div class="color-option" onclick="setContrast('light')">
                    <i class="fa-solid fa-sun"></i>
                    <span class="color-title">Light HC</span>
                    <div class="color-overlay">
                        <p>High contrast light background</p>
                    </div>
                </div>
                <div class="color-option" onclick="setSaturation('low')" id="saturationLowToggle">
                    <i class="fa-solid fa-droplet"></i>
                    <span class="color-title">Low Saturation</span>
                    <div class="color-overlay">
                        <p>Reduce color intensity</p>
                    </div>
                </div>
                <div class="color-option" onclick="setSaturation('high')" id="saturationHighToggle">
                    <i class="fa-solid fa-droplet"></i>
                    <span class="color-title">High Saturation</span>
                    <div class="color-overlay">
                        <p>Boost color intensity</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="accessibility-divider"></div>
        <div class="accessibility-section">
            <label>Content Adjustment:</label>

            <div class="content-adjustment-panel">
                <div class="adjustment-header">
                    <i class="fa-solid fa-text-height"></i>
                    <div>
                        <strong>Font Sizing</strong>
                        <div class="adjustment-description">Increase and Decrease the Font Size</div>
                    </div>
                </div>

                <div class="adjustment-tabs">
                    <button class="tab active" onclick="switchAdjustment('font')">Font Size</button>
                    <button class="tab" onclick="switchAdjustment('line')">Line Spacing</button>
                    <button class="tab" onclick="switchAdjustment('word')">Word Spacing</button>
                </div>

                <div class="adjustment-slider">
                    <button onclick="adjustSetting('decrease')">−</button>
                    <input type="range" id="adjustmentRange" min="50" max="200" value="100" oninput="applyAdjustment(this.value)">
                    <button onclick="adjustSetting('increase')">+</button>
                </div>
            </div>

            <div class="color-adjustment-grid" style="margin-top: 20px;">
                <div class="color-option" onclick="toggleImages()" id="imageToggle">
                    <i class="fa-solid fa-image"></i>
                    <span class="color-title">Toggle Images</span>
                    <div class="color-overlay">
                        <p>Hide or show all images</p>
                    </div>
                </div>
                <div class="color-option" onclick="toggleHighlightLinks()" id="linkHighlightToggle">
                    <i class="fa-solid fa-link"></i>
                    <span class="color-title">Highlight Links</span>
                    <div class="color-overlay">
                        <p>Highlight all links on the page</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="accessibility-divider"></div>

        <!-- Screen Reader Section -->
        <div class="accessibility-section">
            <label>Screen Reader:</label>
            <div class="screen-reader-panel">
                <div class="adjustment-header">
                    <i class="fa-solid fa-volume-high"></i>
                    <div>
                        <strong>Screen Reader</strong>
                        <div class="adjustment-description">Read page content aloud</div>
                    </div>
                </div>
                <!-- Voice selection dropdown -->
                <div class="screen-reader-controls">
                    <label for="voiceSelect">Voice:</label>
                    <select id="voiceSelect" aria-label="Select Voice" onchange="onVoiceChange()"></select>
                </div>
                <!-- Toggle button to enable/disable screen reader -->
                <button id="screenReaderToggleBtn" class="screen-reader-btn" onclick="toggleScreenReader()">
                    <i class="fa-solid fa-volume-high"></i>
                    <span id="screenReaderToggleText">Enable Screen Reader</span>
                </button>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <div class="accessibility-footer">
        <div class="accessibility-footer-links">
            <a href="#reset">Reset All</a>
            <a href="#hide">Hide Accessibility Button</a>
            <a href="#feedback">Send Feedback</a>
        </div>
    </div>
</div>



<script>
// Existing state variables
let currentMode = 'font';
let activeContrast = null;
let imagesHidden = false;
let activeSaturation = null;
// Screen reader state variables
let screenReaderEnabled = false;
let selectedVoiceURI = null;
let pendingSpeak = false;


function toggleImages(fromLoad = false) {
    imagesHidden = !imagesHidden;
    document.querySelectorAll('img').forEach(img => {
        if (!img.closest('#accessibility-panel')) {
            img.style.visibility = imagesHidden ? 'hidden' : 'visible';
        }
    });
    document.getElementById('imageToggle').classList.toggle('active', imagesHidden);
    if (!fromLoad) saveSettings();
}

function toggleHighlightLinks() {
    const active = document.body.classList.toggle('highlight-links');
    document.getElementById('linkHighlightToggle').classList.toggle('active', active);
    saveSettings();
}



// Store last-used values separately
let adjustmentState = {
    font: 100,
    line: 100,
    word: 100
};

function isInAccessibilityUI(el) {
    return el.closest('#accessibility-panel') || el.closest('#accessibility-toggle-wrapper');
}

document.getElementById('accessibility-toggle').addEventListener('click', () => {
    document.getElementById('accessibility-panel').classList.add('open');
    document.getElementById('accessibility-toggle-wrapper').classList.add('d-none');
});
document.getElementById('accessibility-close').addEventListener('click', () => {
    document.getElementById('accessibility-panel').classList.remove('open');
    document.getElementById('accessibility-toggle-wrapper').classList.remove('d-none');
});


function switchAdjustment(mode) {
    currentMode = mode;
    document.querySelectorAll('.tab').forEach(btn => btn.classList.remove('active'));
    document.querySelector(`.tab[onclick*="${mode}"]`).classList.add('active');

    const slider = document.getElementById('adjustmentRange');
    slider.min = 50;
    slider.max = mode === 'word' ? 300 : 200;
    slider.value = adjustmentState[mode] || 100;
}

function applyAdjustment(val) {
    const scale = parseFloat(val) / 100;
    adjustmentState[currentMode] = parseInt(val); // Persist the new value

    document.querySelectorAll('body *').forEach(el => {
        if (!el.dataset.accessAdjust || isInAccessibilityUI(el)) return;

        if (currentMode === 'font' && el.dataset.adjustfont) {
            el.style.fontSize = `${parseFloat(el.dataset.adjustfont) * scale}px`;
        } else if (currentMode === 'line' && el.dataset.adjustline) {
            el.style.lineHeight = `${parseFloat(el.dataset.adjustline) * scale}px`;
        } else if (currentMode === 'word') {
            if (parseInt(val) !== 100) {
                const spacingEm = ((parseInt(val) - 100) / 100).toFixed(2); // e.g. 150 => 0.50em
                el.style.wordSpacing = `${spacingEm}em`;
            } else {
                el.style.wordSpacing = '';
            }
        }
    });

}


function adjustSetting(action) {
    const slider = document.getElementById('adjustmentRange');
    const step = currentMode === 'word' ? 20 : 10;
    const newVal = Math.max(parseInt(slider.min), Math.min(parseInt(slider.max),
        action === 'increase' ? parseInt(slider.value) + step : parseInt(slider.value) - step
    ));
    slider.value = newVal;
    applyAdjustment(newVal);
}

// Auto-store base values once
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('body *').forEach(el => {
        if (isInAccessibilityUI(el)) return;

        const cs = window.getComputedStyle(el);
        const fontSize = parseFloat(cs.fontSize);
        const lineHeight = cs.lineHeight === 'normal' ? fontSize * 1.2 : parseFloat(cs.lineHeight);
        const wordSpacing = parseFloat(cs.wordSpacing);

        if (!isNaN(fontSize)) {
            el.dataset.accessAdjust = true;
            el.dataset.adjustfont = fontSize;
        }
        if (!isNaN(lineHeight)) {
            el.dataset.adjustline = lineHeight;
        }
        el.dataset.adjustword = (!isNaN(wordSpacing) && wordSpacing >= 0) ? wordSpacing : 1;
    });

    loadSettings(); // <-- make sure settings load *after* that
});


function setSaturation(level, fromLoad = false) {
    if (!fromLoad && activeSaturation === level) {
        // Remove current
        document.body.classList.remove(`saturation-${level}`);
        document.getElementById(`saturation${capitalize(level)}Toggle`).classList.remove('active');
        activeSaturation = null;
        saveSettings();
        return;
    }

    // Clear all first
    document.body.classList.remove('saturation-low', 'saturation-high');
    document.getElementById('saturationLowToggle').classList.remove('active');
    document.getElementById('saturationHighToggle').classList.remove('active');

    // Set new
    document.body.classList.add(`saturation-${level}`);
    document.getElementById(`saturation${capitalize(level)}Toggle`).classList.add('active');
    activeSaturation = level;

    if (!fromLoad) saveSettings();
}


function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}


// Contrast helpers
function setContrast(mode, fromLoad = false) {
    const className = mode === 'mono' ? 'mono' : mode === 'dark' ? 'dark-hc' : 'light-hc';

    if (!fromLoad && activeContrast === mode) {
        resetContrast();
        activeContrast = null;
        document.querySelectorAll('.color-option').forEach(el => el.classList.remove('active'));
        saveSettings(); // saves null contrast
        return;
    }

    resetContrast();
    document.body.classList.add(className);
    activeContrast = mode;

    document.querySelectorAll('.color-option').forEach(el => el.classList.remove('active'));
    const selected = document.querySelector(`.color-option[onclick*="${mode}"]`);
    if (selected) selected.classList.add('active');

    saveSettings(); // saves contrast
}



function resetContrast() {
    document.body.classList.remove('mono', 'dark-hc', 'light-hc');
}

switchAdjustment('font');

const SETTINGS_KEY = 'accessibilitySettings';

/** Update the screen reader toggle button UI text and style */
function updateScreenReaderToggleUI() {
    const btn = document.getElementById('screenReaderToggleBtn');
    const textSpan = document.getElementById('screenReaderToggleText');
    if (!btn || !textSpan) return;
    if (screenReaderEnabled) {
        btn.classList.add('active');
        textSpan.textContent = 'Disable Screen Reader';
    } else {
        btn.classList.remove('active');
        textSpan.textContent = 'Enable Screen Reader';
    }
}

/** Handle voice selection change from dropdown */
function onVoiceChange() {
    const select = document.getElementById('voiceSelect');
    if (!select) return;
    selectedVoiceURI = select.value;
    // If currently reading, restart with the new voice
    if (screenReaderEnabled && window.speechSynthesis.speaking) {
        window.speechSynthesis.cancel();
        speakPageText();
    }
    saveSettings();
}

/** Toggle the screen reader on/off */
function toggleScreenReader() {
    screenReaderEnabled = !screenReaderEnabled;
    updateScreenReaderToggleUI();
    if (screenReaderEnabled) {
        // Start reading immediately when enabled
        if (window.speechSynthesis.speaking) {
            window.speechSynthesis.cancel();
        }
        speakPageText();
    } else {
        // Stop any ongoing speech when disabled
        window.speechSynthesis.cancel();
    }
    saveSettings();
}

/** Use Web Speech API to speak the page text (excluding the accessibility UI and <style> tags) */
function speakPageText() {
    if (!screenReaderEnabled) return;
    const synth = window.speechSynthesis;
    
    // Clone the body to remove accessibility UI elements and <style> tags from the text
    const bodyClone = document.body.cloneNode(true);
    
    // Remove elements that shouldn't be read by the screen reader (e.g., the accessibility panel, buttons, etc.)
    const panelElem = bodyClone.querySelector('#accessibility-panel');
    if (panelElem) panelElem.remove();
    const toggleWrapper = bodyClone.querySelector('#accessibility-toggle-wrapper');
    if (toggleWrapper) toggleWrapper.remove();
    const header = bodyClone.querySelector('header');
    if (header) header.remove();
    const footer = bodyClone.querySelector('footer');
    if (footer) footer.remove();
    
    // Remove <style> tags to prevent them from being read
    const styleTags = bodyClone.querySelectorAll('style');
    styleTags.forEach(style => style.remove());
    
    const scriptTags = bodyClone.querySelectorAll('script');
    scriptTags.forEach(script => script.remove());


    // Get the remaining text content of the page
    let text = bodyClone.innerText || '';
    text = text.trim();
    if (!text) return;
    
    // Prepare utterance for speech synthesis
    const utterance = new SpeechSynthesisUtterance(text);
    
    // Use selected voice if available
    if (selectedVoiceURI) {
        const voice = synth.getVoices().find(v => v.voiceURI === selectedVoiceURI);
        if (voice) {
            utterance.voice = voice;
        }
    }
    
    // Set language of utterance to page language (if specified in HTML)
    if (document.documentElement.lang) {
        utterance.lang = document.documentElement.lang;
    }
    
    // Cancel any ongoing speech and speak the text
    synth.cancel();
    synth.speak(utterance);
}


/** Populate the voice selection dropdown with available voices */
function populateVoiceList() {
    const synth = window.speechSynthesis;
    const voiceSelect = document.getElementById('voiceSelect');
    if (!voiceSelect) return;
    const voices = synth.getVoices();
    if (voices.length === 0) return;  // voices not loaded yet

    // Filter voices to only include English ones
    const englishVoices = voices.filter(voice => voice.lang.startsWith('en'));

    voiceSelect.innerHTML = '';
    englishVoices.forEach(voice => {
        const option = document.createElement('option');
        option.value = voice.voiceURI;
        option.textContent = `${voice.name} (${voice.lang})`;
        voiceSelect.appendChild(option);
    });

    // Select a default voice option
    let defaultIndex = 0;
    if (selectedVoiceURI) {
        // If a voice was previously saved, use that
        const idx = englishVoices.findIndex(v => v.voiceURI === selectedVoiceURI);
        if (idx !== -1) defaultIndex = idx;
    } else {
        // Auto-detect voice based on page language
        const pageLang = document.documentElement.lang || navigator.language || 'en';
        const baseLang = pageLang.split('-')[0];
        let foundIdx = englishVoices.findIndex(v => v.lang.startsWith(pageLang));
        if (foundIdx === -1) {
            foundIdx = englishVoices.findIndex(v => v.lang.startsWith(baseLang));
        }
        if (foundIdx !== -1) defaultIndex = foundIdx;
        // Store the auto-selected voice (but user can change it)
        selectedVoiceURI = englishVoices[defaultIndex].voiceURI;
    }

    voiceSelect.selectedIndex = defaultIndex;

    // If screen reader was enabled and pending auto-read, start speaking now
    if (screenReaderEnabled && pendingSpeak) {
        speakPageText();
        pendingSpeak = false;
    }
}

function saveSettings() {
    const settings = {
        adjustmentState,
        activeContrast,
        hideButton: document.getElementById('accessibility-toggle').classList.contains('d-none'),
        lastMode: currentMode,
        imagesHidden,
        highlightLinks: document.body.classList.contains('highlight-links'),
        activeSaturation,
        screenReaderEnabled,
        screenReaderVoice: selectedVoiceURI
    };
    localStorage.setItem(SETTINGS_KEY, JSON.stringify(settings));
}



function loadSettings() {
    const stored = localStorage.getItem(SETTINGS_KEY);
    if (!stored) return;
    try {
        const settings = JSON.parse(stored);
        // Restore zoom/contrast settings (existing code)
        if (settings.adjustmentState) {
            Object.assign(adjustmentState, settings.adjustmentState);
            const lastMode = settings.lastMode || 'font';
            switchAdjustment(lastMode);
            applyAdjustment(adjustmentState[lastMode]);
        }
        if (settings.activeContrast) setContrast(settings.activeContrast, true);
        if (settings.hideButton) document.getElementById('accessibility-toggle').classList.add('d-none');
        if (settings.imagesHidden) toggleImages(true);
        if (settings.highlightLinks) {
            document.body.classList.add('highlight-links');
            document.getElementById('linkHighlightToggle').classList.add('active');
        }
        if (settings.activeSaturation) setSaturation(settings.activeSaturation, true);
        // Restore screen reader settings
        if (settings.screenReaderEnabled) {
            screenReaderEnabled = true;
        }
        if (settings.screenReaderVoice) {
            selectedVoiceURI = settings.screenReaderVoice;
        }
        if (screenReaderEnabled) {
            updateScreenReaderToggleUI();
            // Set flag to auto-read after voices load
            pendingSpeak = true;
        }
        // Populate voice list (will auto-start reading if pendingSpeak is true and voices are ready)
        populateVoiceList();
    } catch (e) {
        console.warn('Failed to load accessibility settings:', e);
    }
}

function resetAccessibility() {
    // Reset all settings to default
    adjustmentState = { font: 100, line: 100, word: 100 };
    activeContrast = null;
    resetContrast();
    localStorage.removeItem(SETTINGS_KEY);
    // Remove all style adjustments
    document.querySelectorAll('body *').forEach(el => {
        if (isInAccessibilityUI(el)) return;
        el.style.fontSize = '';
        el.style.lineHeight = '';
        el.style.wordSpacing = '';
    });
    document.querySelectorAll('.color-option').forEach(el => el.classList.remove('active'));
    document.getElementById('adjustmentRange').value = 100;
    document.querySelectorAll('.tab').forEach(btn => btn.classList.remove('active'));
    document.querySelector('.tab[onclick*="font"]').classList.add('active');
    // Reset screen reader UI and state
    screenReaderEnabled = false;
    selectedVoiceURI = null;
    updateScreenReaderToggleUI();
    // Restore visibility of accessibility button and close panel
    document.getElementById('accessibility-toggle').classList.remove('d-none');
    document.getElementById('accessibility-panel').classList.remove('open');
    location.reload();
}


document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('.accessibility-footer-links a:nth-child(1)').addEventListener('click', (e) => {
        e.preventDefault();
        resetAccessibility();
    });
    document.querySelector('.accessibility-footer-links a:nth-child(2)').addEventListener('click', (e) => {
        e.preventDefault();
        document.getElementById('accessibility-toggle').classList.add('d-none');
        document.getElementById('accessibility-panel').classList.remove('open');
        saveSettings();
    });
    document.querySelector('.accessibility-footer-links a:nth-child(3)').addEventListener('click', (e) => {
        e.preventDefault();
        window.location.href = '/Contact/accessibility-feedback';
    });
    // Load settings (includes screen reader state)
    loadSettings();
});

// Initialize voice list and handle asynchronous loading of voices
if ('speechSynthesis' in window) {
    window.speechSynthesis.onvoiceschanged = populateVoiceList;
    window.speechSynthesis.getVoices();  // Trigger loading of voice list
}

const origApplyAdjustment = applyAdjustment;
applyAdjustment = function(val) {
    origApplyAdjustment(val);
    saveSettings();
};
</script>


<style>
:host {
    font-size: 16px;
}
.d-none {
    display: none;
}
#accessibility-toggle-wrapper {
    position: fixed;
    right: 15px;
    z-index: 9999;
    transition: top 0.3s ease;
}
body.is-frontend #accessibility-toggle-wrapper {
    top: 15px;
}

body.is-portal #accessibility-toggle-wrapper {
    bottom: 15px; /* or whatever offset suits the portal */
}
#accessibility-toggle {
    background: white;
    color: #C55359;
    border: none;
    border-radius: 50%;
    border: 2px solid #C55359;
    width: 50px;
    height: 50px;
    font-size: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    position: relative;
}
#accessibility-desc {
    position: absolute;
    top: 50%;
    right: 60px;
    transform: translateY(-50%);
    background-color: #2C3C64;
    color: white;
    padding: 6px 10px;
    border-radius: 5px;
    font-size: 15px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: all 0.3s ease;
}
#accessibility-toggle-wrapper:hover #accessibility-desc {
    opacity: 1;
    right: 70px;
}

/* PANEL BASE */
#accessibility-panel {
    position: fixed;
    top: 0;
    right: -600px;
    width: 520px;
    height: 100%;
    background: white;
    color: #C55359;
    z-index: 9998;
    box-accessibilityShadow: -5px 0 10px rgba(0,0,0,0.3);
    transition: right 0.4s ease;
    display: flex;
    flex-direction: column;
}
#accessibility-panel.open {
    right: 0;
}
@media (max-width: 520px) {
    #accessibility-panel {
        width: 100%;
    }
}

body.highlight-links a {
    background-color: black !important;
    color: yellow !important;
    text-decoration: underline !important;
}

body.saturation-low> *:not(#accessibility-panel):not(#accessibility-toggle-wrapper) {
    filter: saturate(50%) !important;
}
body.saturation-high> *:not(#accessibility-panel):not(#accessibility-toggle-wrapper) {
    filter: saturate(200%) !important;
}

/* HEADER */
.accessibility-header {
    background: #2C3C64;
    color: white;
    padding: 10px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
}
#accessibility-close {
    font-size: 29px;
    font-weight: bold;
    color: white;
    background: none;
    border: none;
    cursor: pointer;
    line-hight: 0.8;
}
.accessibility-title {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    bottom: -25px;
    background: #2C3C64;
    padding: 10px 40px;
    border-radius: 15px 15px;
    font-size: 24px;
    font-weight: 500;
}
.accessibility-icons i {
    margin-left: 12px;
}

/* BODY SCROLLABLE */
.accessibility-body {
    overflow-y: auto;
    flex: 1;
    padding: 20px;
    margin-top: 25px;
}
/* SECTIONS */
.accessibility-section {
    margin-bottom: 25px;
}
.accessibility-section label {
    font-weight: bold;
    display: block;
    margin-bottom: 8px;
}

/* FOOTER */
.accessibility-footer {
    background: #2C3C64;
    padding: 12px 0;
    text-align: center;
}
.accessibility-footer-links {
    display: flex;
    justify-content: center;
    gap: 30px;
    flex-wrap: wrap;
}
.accessibility-footer-links a {
    color: white;
    text-decoration: none;
    font-size: 16px;
}
.accessibility-footer-links a:hover {
    text-decoration: underline;
}

.color-adjustment-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 30px;
}


.color-option {
    background: white;
    border: 1px solid #2C3C64;
    border-radius: 4px;
    width: 140px;
    height: 100px;
    text-align: center;
    position: relative;
    cursor: pointer;
    transition: all 0.3s ease;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.color-option i {
    font-size: 32px;
    color: #2C3C64;
    transition: transform 0.3s;
}

.color-title {
    font-size: 16px;
    color: #2C3C64;
    margin-top: 6px;
    font-weight: 600;
}

/* Hover effect */
.color-option:hover i {
    transform: scale(1.2);
}

.color-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(90, 79, 80, 0.9);
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 8px;
    font-size: 16px;
    text-align: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.color-option:hover .color-overlay {
    opacity: 1;
    z-index: 1;
}
.color-option.active {
    background-color: #C55359;
    border-color: #C55359;
}
.color-option.active i,
.color-option.active .color-title {
    color: white;
}

.accessibility-divider {
    width: 100%;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}


/* Contrast Modes */
body.mono > *:not(#accessibility-panel):not(#accessibility-toggle-wrapper) {
    filter: grayscale(100%) !important;
}

.dark-hc :not(#accessibility-panel):not(#accessibility-panel *) { background-color: #000 !important; color: #fff !important; }
.light-hc :not(#accessibility-panel):not(#accessibility-panel *) { background-color: #fff !important; color: #000 !important; }
/* DARK HIGH CONTRAST MODE */
body.dark-hc a:not(#accessibility-panel *),
body.dark-hc button:not(#accessibility-panel *),
body.dark-hc input[type="submit"]:not(#accessibility-panel *) {
    outline: 2px solid #ffd700 !important;
    border: 2px solid #ffd700 !important;
    background-color: transparent !important;
    color: #fff !important;
}

/* LIGHT HIGH CONTRAST MODE */
body.light-hc a:not(#accessibility-panel *),
body.light-hc button:not(#accessibility-panel *),
body.light-hc input[type="submit"]:not(#accessibility-panel *) {
    outline: 2px solid #2C3C64 !important;
    border: 2px solid #2C3C64 !important;
    background-color: transparent !important;
    color: #000 !important;
}

body.mono,
body.dark-hc,
body.light-hc {
    transition: all 0.3s ease;
}


.content-adjustment-panel {
    border: 1px solid #2C3C64;
    border-radius: 10px;
    padding: 15px;
}

.adjustment-header {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.adjustment-header i {
    font-size: 30px;
    color: #2C3C64;
    margin-right: 10px;
}

.adjustment-description {
    font-size: 14px;
    color: #555;
}

.adjustment-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}

.tab {
    padding: 5px 15px;
    border: 2px solid #2C3C64;
    background: white;
    color: #2C3C64;
    border-radius: 20px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.tab.active {
    background: #2C3C64;
    color: white;
}

.adjustment-slider {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.adjustment-slider button {
    background: #C55359;
    color: white;
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 20px;
    font-size: 20px;
    cursor: pointer;
}

.adjustment-slider input[type="range"] {
    flex-grow: 1;
    margin: 0 10px;
    accent-color: #C55359;
}


/* Screen Reader Panel Styles */
.screen-reader-panel {
    border: 1px solid #2C3C64;
    border-radius: 10px;
    padding: 15px;
    margin-top: 10px;
    overflow: hidden;
}
.screen-reader-controls {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}
.screen-reader-controls label {
    font-weight: 600;
    margin-right: 10px;
}
.screen-reader-controls select {
    flex: 1;
    padding: 5px;
    border: 1px solid #ccc;
    border-radius: 5px;
    max-width: 350px;
}
.screen-reader-btn {
    background: white;
    color: #2C3C64;
    border: 2px solid #2C3C64;
    border-radius: 4px;
    padding: 6px 12px;
    font-size: 16px;
    display: inline-flex;
    align-items: center;
    cursor: pointer;
    transition: background 0.3s, color 0.3s;
}
.screen-reader-btn i {
    margin-right: 8px;
}
.screen-reader-btn.active {
    background: #C55359;
    border-color: #C55359;
    color: #fff;
}
.screen-reader-btn.active i {
    color: #fff;
}

.highlighted {
    background-color: rgba(255, 255, 0, 0.3); /* Light yellow background */
    border: 2px solid #C55359; /* Red border to make it stand out */
    padding: 2px;
    transition: background-color 0.3s ease;
}

</style>
