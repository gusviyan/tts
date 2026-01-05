<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Patient Calling System - TTS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="style.css" />
</head>
<body>

  <!-- ===== HEADER ===== -->
  <header class="header">
    <img src="./assets/logo.png" alt="RS Permata Pamulang" class="logo" />
  </header>

  <!-- ===== MAIN ===== -->
  <main>

    <!-- Warning Browser -->
    <div class="notice-card">
      <span>⚠️</span>
      <div>
        Harap gunakan <b>Google Chrome</b> untuk hasil suara terbaik. Jangan gunakan Browser Lain.
      </div>
    </div>

    <!-- MAIN CARD -->
    <div class="tts-wrapper">
      <!-- dekorasi bubble -->
      <div class="decor-circle"></div>

      <div class="tts-header">
        <!-- <svg width="22" height="22" viewBox="0 0 30 330" aria-hidden="true">
          <path d="M3 10v4a1 1 0 0 0 1 1h4l5 4V6L8 10H4a1 1 0 0 0-1 1z" fill="#0288d1"/>
        </svg> -->
        <h2>🔊 Patient Calling System</h2>
      </div>

      <div class="content-grid">
        <!-- LEFT: Manual text -->
        <div class="manual-area">
          <!-- <label class="label" for="ttsText">Manual Text (ketik)</label> -->
          <textarea id="ttsText" placeholder="Ketik teks manual di sini..."></textarea>
          <!-- <p class="hint">Paste otomatis menjadi lowercase.</p> -->
        </div>

        <!-- RIGHT: Nama + Unit -->
        <div class="card-area">
          <label class="label" for="nameInput">Nama pasien</label>
          <input id="nameInput" type="text" placeholder="Masukkan nama pasien..." autocomplete="off" />

          <div class="unit-title">Unit</div>
          <div class="unit-grid">
            <button type="button" class="unit-btn" data-unit="Radiologi">Radiologi</button>
            <button type="button" class="unit-btn" data-unit="Laboratorium">Laboratorium</button>
            <button type="button" class="unit-btn" data-unit="Farmasi">Farmasi</button>
            <button type="button" class="unit-btn" data-unit="Poliklinik">Poliklinik</button>
            <button type="button" class="unit-btn" data-unit="Kasir">Kasir</button>
            <button type="button" class="unit-btn" data-unit="Fisioterapi">Fisioterapi</button>
          </div>

          <p class="card-note">
            Silahkan input nama pasien dan pilih unit anda.
          </p>
        </div>
      </div>

      <!-- ACTION BUTTONS -->
      <div class="actions">
        <button id="generateBtn" class="btn-play">
          <svg width="18" height="18" viewBox="0 0 24 24"><path d="M5 3v18l15-9L5 3z" fill="#fff"/></svg>
          Play Audio
        </button>

        <button id="clearBtn" class="btn-clear">✖ Clear Text</button>

        <button type="button" id="openSettings" class="btn-settings">
          ⚙ Settings
        </button>
      </div>
    </div>

    <!-- BETA INFO -->
    <div class="beta-card">
      📢 Apps ini masih dalam tahap <b>Uji Coba</b>. Jika ada masalah suara atau fitur, harap melapor ke
      Tim IT RS Permata Pamulang.
    </div>
  </main>

  <!-- ===== FOOTER ===== -->
  <footer class="footer">
    © 2025 Gusviyan - IT Dept RS Permata Pamulang | All Rights Reserved
  </footer>

  <!-- ===== MODAL PENGATURAN SUARA ===== -->
  <div id="settingsModal" class="modal-overlay" aria-hidden="true">
    <div class="modal-card">
      <div class="modal-header">
        <h3>Pengaturan Suara</h3>
        <button type="button" class="modal-close" id="closeSettings">&times;</button>
      </div>

      <div class="modal-body">

        <div class="slider-group">
          <label for="speedRange">
            Kecepatan: <span id="speedLabel">0%</span>
          </label>
          <input type="range" id="speedRange" min="-50" max="50" value="0">
        </div>

        <div class="slider-group">
          <label for="volumeRange">
            Volume: <span id="volumeLabel">0%</span>
          </label>
          <input type="range" id="volumeRange" min="-50" max="50" value="0">
        </div>

        <div class="slider-group">
          <label for="pitchRange">
            Nada: <span id="pitchLabel">0</span>
          </label>
          <input type="range" id="pitchRange" min="-50" max="50" value="0">
        </div>

        <label class="remember-settings">
          <input type="checkbox" id="rememberSettings">
          Simpan pengaturan (pertahankan pengaturan saat ini setelah menghasilkan audio)
        </label>
      </div>

      <div class="modal-footer">
        <button type="button" id="resetSettings" class="btn-modal-reset">
          Reset ke Default
        </button>
      </div>
    </div>
  </div>

  <!-- ===== SCRIPT TTS + PENGATURAN + JEDA ===== -->
  <script>
    const textArea    = document.getElementById('ttsText');
    const nameInput   = document.getElementById('nameInput');
    const generateBtn = document.getElementById('generateBtn');
    const clearBtn    = document.getElementById('clearBtn');
    const unitButtons = document.querySelectorAll('.unit-btn');

    // ==== auto lowercase saat paste / input ====
    textArea.addEventListener('input', () => {
      const curPos = textArea.selectionStart;
      textArea.value = textArea.value.toLowerCase();
      textArea.selectionStart = textArea.selectionEnd = curPos;
    });

    // unit → template text (pakai koma sebagai penanda jeda)
    unitButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        const unit = btn.dataset.unit || btn.textContent.trim();
        const name = nameInput.value.trim() || 'pasien';
        const template = `,atas nama, ${name}, di ${unit}`.toLowerCase();
        textArea.value = template;
        textArea.classList.remove('just-filled');
        void textArea.offsetWidth; // trigger reflow
        textArea.classList.add('just-filled');
      });
    });

    // ====== PENGATURAN SUARA ======
    const openSettingsBtn  = document.getElementById('openSettings');
    const settingsModal    = document.getElementById('settingsModal');
    const closeSettingsBtn = document.getElementById('closeSettings');
    const resetSettingsBtn = document.getElementById('resetSettings');
    const rememberCheckbox = document.getElementById('rememberSettings');

    const speedRange  = document.getElementById('speedRange');
    const volumeRange = document.getElementById('volumeRange');
    const pitchRange  = document.getElementById('pitchRange');

    const speedLabel  = document.getElementById('speedLabel');
    const volumeLabel = document.getElementById('volumeLabel');
    const pitchLabel  = document.getElementById('pitchLabel');

    const defaultSettings = {
      speed: 0,
      volume: 0,
      pitch: 0,
      remember: false
    };

    let ttsSettings = { ...defaultSettings };
    const SETTINGS_KEY = 'tts-settings-v1';

    function loadSettings() {
      try {
        const raw = localStorage.getItem(SETTINGS_KEY);
        if (!raw) return;
        const parsed = JSON.parse(raw);
        ttsSettings = { ...ttsSettings, ...parsed };
      } catch (e) {
        console.warn('Gagal load settings', e);
      }
    }

    function saveSettings() {
      if (!ttsSettings.remember) return;
      localStorage.setItem(SETTINGS_KEY, JSON.stringify(ttsSettings));
    }

    function syncUIFromState() {
      speedRange.value  = ttsSettings.speed;
      volumeRange.value = ttsSettings.volume;
      pitchRange.value  = ttsSettings.pitch;

      speedLabel.textContent  = `${ttsSettings.speed}%`;
      volumeLabel.textContent = `${ttsSettings.volume}%`;
      pitchLabel.textContent  = ttsSettings.pitch;

      rememberCheckbox.checked = ttsSettings.remember;
    }

    function syncStateFromUI() {
      ttsSettings.speed   = parseInt(speedRange.value, 10);
      ttsSettings.volume  = parseInt(volumeRange.value, 10);
      ttsSettings.pitch   = parseInt(pitchRange.value, 10);
      ttsSettings.remember = rememberCheckbox.checked;
      saveSettings();
    }

    function applySettingsToUtterance(utterance) {
      // speed -50..50 → rate ~0.6..1.4
      const speedFactor = 1 + (ttsSettings.speed / 100) * 0.8;
      utterance.rate = Math.min(2, Math.max(0.5, speedFactor));

      // volume -50..50 → 0.5..1
      const volFactor = 1 + (ttsSettings.volume / 100) * 0.6;
      utterance.volume = Math.min(1, Math.max(0, volFactor));

      // pitch -50..50 → 0.7..1.3
      const pitchFactor = 1 + (ttsSettings.pitch / 100) * 0.6;
      utterance.pitch = Math.min(2, Math.max(0.5, pitchFactor));
    }

    loadSettings();
    syncUIFromState();

    // buka / tutup modal
    openSettingsBtn.addEventListener('click', () => {
      settingsModal.classList.add('active');
      settingsModal.setAttribute('aria-hidden', 'false');
    });
    closeSettingsBtn.addEventListener('click', () => {
      settingsModal.classList.remove('active');
      settingsModal.setAttribute('aria-hidden', 'true');
    });
    settingsModal.addEventListener('click', (e) => {
      if (e.target === settingsModal) {
        settingsModal.classList.remove('active');
        settingsModal.setAttribute('aria-hidden', 'true');
      }
    });

    [speedRange, volumeRange, pitchRange].forEach(input => {
      input.addEventListener('input', () => {
        speedLabel.textContent  = `${speedRange.value}%`;
        volumeLabel.textContent = `${volumeRange.value}%`;
        pitchLabel.textContent  = pitchRange.value;
        syncStateFromUI();
      });
    });
    rememberCheckbox.addEventListener('change', syncStateFromUI);

    resetSettingsBtn.addEventListener('click', () => {
      ttsSettings = { ...defaultSettings };
      syncUIFromState();
      saveSettings();
    });

    // ====== TTS WEB SPEECH + JEDA ANTAR BAGIAN ======
    let cachedVoice = null;

    function getIndonesianVoice() {
      if (cachedVoice) return cachedVoice;
      const voices = speechSynthesis.getVoices();

      const exact = voices.find(v =>
        v.lang.toLowerCase().startsWith('id') &&
        /female|perempuan|woman/i.test(v.name)
      );
      if (exact) { cachedVoice = exact; return exact; }

      const anyId = voices.find(v => v.lang.toLowerCase().startsWith('id'));
      if (anyId) { cachedVoice = anyId; return anyId; }

      cachedVoice = voices[0] || null;
      return cachedVoice;
    }

    window.speechSynthesis.onvoiceschanged = () => {
      cachedVoice = null;
    };

    // Fungsi baru: baca teks dengan jeda berdasarkan koma / tanda |
    function speakWithPauseFromText(fullText, pauseMs = 600) {
      if (!fullText) return;

      // bagi berdasarkan koma atau |
      const segments = fullText
        .split(/[,|]+/g)
        .map(s => s.trim())
        .filter(Boolean);

      if (!segments.length) return;

      let index = 0;
      speechSynthesis.cancel();

      function speakNext() {
        if (index >= segments.length) return;

        const utter = new SpeechSynthesisUtterance(segments[index]);
        utter.lang = "id-ID";
        const v = getIndonesianVoice();
        if (v) utter.voice = v;

        applySettingsToUtterance(utter);

        utter.onend = () => {
          index++;
          if (index < segments.length) {
            setTimeout(speakNext, pauseMs);
          }
        };

        speechSynthesis.speak(utter);
      }

      speakNext();
    }

    generateBtn.addEventListener('click', () => {
      const text = textArea.value.trim();
      if (!text) return;
      speakWithPauseFromText(text, 300); // jeda 700ms antar bagian
    });

    clearBtn.addEventListener('click', () => {
      textArea.value = "";
      nameInput.value = "";
      textArea.focus();
    });
  </script>
</body>
</html>
