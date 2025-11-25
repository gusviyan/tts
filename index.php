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
            <h2>🔊 Apps Pemanggilan Pasien</h2>

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

// Tombol Play
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

// Clear
btnClear.addEventListener('click', () => {
    synth.cancel();
    textArea.value = "";
    textArea.focus();
});

// Notice Dismiss
document.getElementById('dismissNotice').addEventListener('click', () => {
    document.getElementById('browserNotice').style.display = 'none';
});
</script>

</body>
</html>
