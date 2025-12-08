<?php
// index.php
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Patient Calling System - RS Permata Pamulang</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>

<?php include 'header.php'; ?>

<main>
  <!-- Notice Card -->
  <div class="notice-card">
    <span class="emoji">⚠️</span>
    <div>
      Harap gunakan Google Chrome / Microsoft Edge untuk hasil suara terbaik. Jangan gunakan Mozilla Firefox.
    </div>
  </div>

  <!-- MAIN CARD -->
  <div class="tts-wrapper glass">
    <div class="tts-header">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false"
           xmlns="http://www.w3.org/2000/svg">
        <path d="M3 10v4a1 1 0 0 0 1 1h4l5 4V6L8 10H4a1 1 0 0 0-1 1z" fill="#0288d1"/>
      </svg>
      <h2>Patient Calling System</h2>
    </div>

    <div class="content-grid">
      <!-- LEFT: Manual Textarea (smaller) -->
      <div class="manual-area">
        <!-- <label class="label">Label Text Area</label> -->
        <textarea id="ttsText" placeholder="Ketik teks manual di sini..."></textarea>
        <p class="hint" style="margin-top:8px; color:rgba(0,0,0,0.45);"></p>
      </div>

      <!-- RIGHT: Card untuk name + unit -->
      <div class="card-area">
        <label class="label">Nama pasien</label>
        <input id="nameInput" type="text" placeholder="Masukkan nama pasien..." autocomplete="off" />

        <div class="unit-title" style="margin-top:6px; font-weight:700; color:rgba(0,0,0,0.6);">Unit</div>
        <div class="unit-grid" role="list">
          <button class="unit-btn" data-unit="Radiologi" type="button">Radiologi</button>
          <button class="unit-btn" data-unit="Laboratorium" type="button">Laboratorium</button>
          <button class="unit-btn" data-unit="Farmasi" type="button">Farmasi</button>
          <button class="unit-btn" data-unit="Poliklinik" type="button">Poliklinik</button>
          <button class="unit-btn" data-unit="Kasir" type="button">Kasir</button>
          <button class="unit-btn" data-unit="Fisioterapi" type="button">Fisioterapi</button>
        </div>

        <div class="card-note">Silahkan input nama pasien dan pilih unit anda.</div>
      </div>
    </div>

    <!-- ACTIONS -->
    <div class="actions">
      <button id="generateBtn" class="btn-play" type="button" aria-label="Play Audio">
        <!-- small play icon -->
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="margin-right:8px;">
          <path d="M5 3v18l15-9L5 3z" fill="#fff"/>
        </svg>
        Play Audio
      </button>

      <button id="clearBtn" class="btn-clear" type="button" aria-label="Clear Text">✖ Clear Text</button>
    </div>
  </div>
</main>

  <!-- Notice Card -->
  <div class="beta-card">
    <span class="emoji">📣</span>
    <div>
      Apps ini masih dalam tahap <strong>Uji Coba</strong>. Jika ada masalah suara atau fitur, harap melapor ke Tim IT RS Permata Pamulang.
    </div>
  </div>

<?php include 'footer.php'; ?>

<script>
/* =========================
   TTS + UI Script
   - Lowercase paste
   - Unit buttons fill textarea with template
   - Play & Clear functionality
   - Voice loading handling
   ========================= */

(function () {
  const synth = window.speechSynthesis;
  const textArea = document.getElementById('ttsText');
  const nameInput = document.getElementById('nameInput');
  const generateBtn = document.getElementById('generateBtn');
  const clearBtn = document.getElementById('clearBtn');
  const unitBtns = document.querySelectorAll('.unit-btn');

  // Ensure voices are available (some browsers load asynchronously)
  let voices = [];
  function loadVoices(){
    voices = synth.getVoices() || [];
  }
  loadVoices();
  if (speechSynthesis.onvoiceschanged !== undefined) {
    speechSynthesis.onvoiceschanged = loadVoices;
  }

  // Speak function with id-ID preference and female fallback where possible
  function speakText(text) {
    if (!text || !text.trim()) return;
    const utter = new SpeechSynthesisUtterance(text);
    utter.lang = 'id-ID';

    // prefer id-ID female-ish voice if available
    let sel = voices.find(v => v.lang === 'id-ID' && /female|woman|ayu|siti|rina|indonesia/i.test(v.name));
    if (!sel) sel = voices.find(v => v.lang === 'id-ID');
    if (!sel) sel = voices.find(v => v.lang && v.lang.startsWith('en')) || voices[0];

    if (sel) try { utter.voice = sel; } catch (e) { /* ignore */ }

    // cancel previous and speak
    try { synth.cancel(); } catch(e){}
    synth.speak(utter);
  }

  // PLAY button
  generateBtn.addEventListener('click', () => {
    const text = textArea.value.trim();
    if (!text) {
      alert('Silakan isi teks atau pilih unit terlebih dahulu.');
      textArea.focus();
      return;
    }
    speakText(text);
  });

  // CLEAR button
  clearBtn.addEventListener('click', () => {
    try { synth.cancel(); } catch(e){}
    textArea.value = '';
    nameInput.value = '';
    textArea.focus();
  });

  // Paste -> lowercase only for textarea
  textArea.addEventListener('paste', (e) => {
    e.preventDefault();
    const pasted = (e.clipboardData || window.clipboardData).getData('text') || '';
    const lower = pasted.toLowerCase();
    const start = textArea.selectionStart || 0;
    const end = textArea.selectionEnd || 0;
    const cur = textArea.value || '';
    textArea.value = cur.slice(0, start) + lower + cur.slice(end);
    const caret = start + lower.length;
    textArea.selectionStart = textArea.selectionEnd = caret;
  });

  // Unit buttons -> fill textarea using name (lowercase)
  unitBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const unit = (btn.getAttribute('data-unit') || '').trim();
      const name = (nameInput.value || '').trim();
      const line1 = name ? `atas nama ${name}` : 'atas nama';
      const line2 = `di ${unit}`;
      const full = (line1 + '\n' + line2).toLowerCase();

      // Insert into textarea (replace)
      textArea.value = full;

      // smooth visual feedback: focus & small highlight
      textArea.focus();
      textArea.selectionStart = textArea.selectionEnd = textArea.value.length;

      // optional: small animation (add class then remove)
      textArea.classList.add('just-filled');
      setTimeout(()=> textArea.classList.remove('just-filled'), 400);
    });
  });

  // Optional: press Ctrl+Enter to play
  textArea.addEventListener('keydown', (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
      e.preventDefault();
      generateBtn.click();
    }
  });

})();
</script>

</body>
</html>
