// assets/js/tts.js
// Fungsi Text-to-Speech dengan queue agar tidak mendengung / dobel

let speakQueue = [];
let isSpeaking = false;

function speakIndo(text){
  speakQueue.push(text);
  processQueue();
}

function processQueue(){
  const synth = window.speechSynthesis;
  if (!synth) {
    console.warn('SpeechSynthesis tidak tersedia di browser ini.');
    return;
  }

  if(isSpeaking) return;

  const nextText = speakQueue.shift();
  if(!nextText) return;

  isSpeaking = true;

  const u = new SpeechSynthesisUtterance(nextText);

  // Cari voice perempuan Bahasa Indonesia; fallback ke voice Indo lainnya
  const voices = synth.getVoices();
  const idnVoices = voices.filter(v => /id[-_]|indones/i.test(v.lang));
  const femaleVoice = idnVoices.find(v => /female|woman|wanita|sri|sari|nessa/i.test(v.name))
    || idnVoices.find(v => !/male|man|pria/i.test(v.name))
    || idnVoices[0];
  if (femaleVoice) u.voice = femaleVoice;

  // pitch mendekati 1.0 agar suara tidak bergetar; rate sedikit lambat agar jelas
  u.rate   = 0.92;
  u.pitch  = 1.05;
  u.volume = 1.0;

  u.onend = () => {
    isSpeaking = false;
    processQueue();
  };

  u.onerror = (e) => {
    console.error('TTS error:', e.error);
    isSpeaking = false;
    processQueue();
  };

  synth.speak(u);
}

/**
 * Pecah kode antrian jadi per karakter dengan jeda koma agar TTS
 * tidak menembak cepat. Contoh: "P0001" → "P, 0, 0, 0, 1"
 */
function ucapkanKode(kode){
  if(!kode) return '';
  return kode.split('').join(', ');
}
