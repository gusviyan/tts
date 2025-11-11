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
    <div class="tts-wrapper glass">
        <h2>🔊 Apps Pemanggilan Pasien</h2>

        <textarea id="ttsText" placeholder="Ketik teks yang ingin diubah menjadi suara..."></textarea>

        <div class="btn-row">
            <button id="generateBtn">▶ Play Audio</button>
            <button id="clearBtn" class="clear">メ Clear Text</button>
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

// Load suara
if (speechSynthesis.onvoiceschanged !== undefined) {
    speechSynthesis.onvoiceschanged = () => {};
}
</script>

</body>
</html>
