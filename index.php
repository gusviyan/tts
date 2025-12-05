<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apps Pemanggilan Pasien</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'header.php'; ?>

<main>
    <div class="main-center">

        <!-- NOTICE CARD -->
<div id="browserNotice" class="notice-card glass">
    <span class="emoji">📣</span>
    <strong>Harap Gunakan Google Chrome / Ms Edge untuk hasil suara terbaik.</strong>
    <span>Jangan gunakan Mozilla Firefox</span>
</div>


        <!-- MAIN TTS CARD -->
        <div class="tts-wrapper glass">
            <h2>🔊 Patient Calling System</h2>

            <textarea id="ttsText" placeholder="Ketik teks yang ingin diubah menjadi suara..."></textarea>

            <div class="btn-row">
                <button id="generateBtn">▶ Play Audio</button>
                <button id="clearBtn" class="clear">✖ Clear Text</button>
            </div>
        </div>

    </div>
</main>

<?php include 'footer.php'; ?>

<script>
const synth = window.speechSynthesis;
const textArea = document.getElementById('ttsText');
const btnSpeak = document.getElementById('generateBtn');
const btnClear = document.getElementById('clearBtn');

// Tombol "Dengarkan"
btnSpeak.addEventListener('click', () => {
    const text = textArea.value.trim();
    if (!text) {
        alert('Silakan masukkan teks terlebih dahulu.');
        return;
    }

    const utter = new SpeechSynthesisUtterance(text);
    utter.lang = "id-ID";

    const voices = synth.getVoices();
    let selectedVoice = voices.find(v => v.lang === "id-ID" && v.name.toLowerCase().includes("female"));
    if (!selectedVoice) selectedVoice = voices.find(v => v.lang === "id-ID") || voices[0];
    utter.voice = selectedVoice;

    synth.cancel();
    synth.speak(utter);
});

// Tombol "Clear Text"
btnClear.addEventListener('click', () => {
    synth.cancel();
    textArea.value = "";
    textArea.focus();
});

// 🔠 Ubah teks yang di-paste menjadi lowercase
textArea.addEventListener('paste', (event) => {
    event.preventDefault(); // cegah paste default
    const pastedText = (event.clipboardData || window.clipboardData).getData('text');
    const lowercaseText = pastedText.toLowerCase();
    
    // Masukkan teks ke posisi kursor
    const start = textArea.selectionStart;
    const end = textArea.selectionEnd;
    const currentValue = textArea.value;
    textArea.value = currentValue.slice(0, start) + lowercaseText + currentValue.slice(end);
    
    // Kembalikan fokus ke posisi setelah teks yang di-paste
    textArea.selectionStart = textArea.selectionEnd = start + lowercaseText.length;
});

// Load suara
if (speechSynthesis.onvoiceschanged !== undefined) {
    speechSynthesis.onvoiceschanged = () => {};
}
</script>


</body>
</html>
