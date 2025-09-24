// assets/js/tts.js
// Fungsi Text-to-Speech dengan queue agar tidak mendengung / dobel

let speakQueue = [];
let isSpeaking = false;

function speakIndo(text){
  // Tambahkan teks ke antrian
  speakQueue.push(text);
  processQueue();
}

function processQueue(){
  const synth = window.speechSynthesis;
  if (!synth) {
    console.warn('SpeechSynthesis tidak tersedia di browser ini.');
    return;
  }

  // Kalau sedang bicara, jangan ganggu → tunggu selesai
  if(isSpeaking) return;

  const nextText = speakQueue.shift();
  if(!nextText) return; // antrian kosong

  isSpeaking = true;

  const u = new SpeechSynthesisUtterance(nextText);

  // Cari voice Bahasa Indonesia kalau ada
  const voices = synth.getVoices();
  let idn = voices.find(v => /id-|indones/i.test(v.lang));
  if(idn) u.voice = idn;

  // Atur properti suara agar natural
  u.rate = 0.95;   // sedikit pelan
  u.pitch = 1.0;   // normal
  u.volume = 1.0;  // penuh

  // Event selesai bicara
  u.onend = () => {
    isSpeaking = false;
    processQueue(); // lanjut ke teks berikutnya
  };

  u.onerror = (e) => {
    console.error('TTS error:', e.error);
    isSpeaking = false;
    processQueue();
  };

  synth.speak(u);
}

/**
 * Fungsi tambahan: memecah kode jadi huruf per huruf agar lebih jelas
 * Contoh: "P0003" → "P 0 0 0 3"
 */
function ucapkanKode(kode){
  if(!kode) return '';
  return kode.split('').join(' ');
}