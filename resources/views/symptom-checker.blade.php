@extends('layouts.app')

@section('title', 'Symptom Checker - KPZ Dentalcare')

@section('content')
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<div class="kbk">

    <div class="kbk-topbar">
        <div class="kbk-wrap kbk-topbar-inner">
            <a href="{{ route('home') }}" class="kbk-back"><i class='bx bx-arrow-back'></i> Back</a>
            <span class="kbk-topbar-title">Symptom Checker</span>
            <span class="kbk-guest-badge"><i class='bx bx-user'></i> Guest mode</span>
        </div>
    </div>

    <div class="kbk-wrap kbk-main">

        <div class="kbk-intro">
            <span class="kauth-eyebrow">No account needed</span>
            <h1 class="kbk-h1">Not sure if it's urgent?</h1>
            <p class="kbk-sub">Tell us what's going on. We'll give you a preliminary read on urgency &mdash; this isn't a diagnosis, and a dentist always has the final say.</p>
        </div>

        <section class="kbk-panel active">
            <div class="kbk-card">
                <div class="kbk-chips" id="symptomChips">
                    <button type="button" class="kbk-chip" data-severity="routine">Toothache</button>
                    <button type="button" class="kbk-chip" data-severity="urgent">Bleeding gums</button>
                    <button type="button" class="kbk-chip" data-severity="urgent">Swollen gums</button>
                    <button type="button" class="kbk-chip" data-severity="routine">Tooth sensitivity</button>
                    <button type="button" class="kbk-chip" data-severity="critical">Broken / knocked-out tooth</button>
                    <button type="button" class="kbk-chip" data-severity="critical">Severe pain</button>
                    <button type="button" class="kbk-chip" data-severity="routine">Just a check-up</button>
                </div>

                <label class="kbk-textarea-wrap">
                    <span>Tell us more (optional)</span>
                    <div class="kbk-textarea-box">
                        <textarea id="symptomDetails" rows="3" placeholder="e.g. Pain started 2 days ago, worse when I bite down..."></textarea>
                        <div class="kbk-mic-controls">
                            <button type="button" class="kbk-lang-toggle" id="langToggle" title="Switch voice input language">EN</button>
                            <button type="button" class="kbk-mic" id="micBtn" title="Voice input"><i class='bx bx-microphone'></i></button>
                        </div>
                    </div>
                    <p class="kbk-voice-note" id="voiceNote" style="display:none;"></p>
                </label>

                <button type="button" class="kpz-btn kpz-btn-brown kbk-analyze" id="analyzeBtn">
                    <i class='bx bx-message-dots'></i> Analyze My Symptoms
                </button>
                <p class="kbk-analyzing" id="analyzingNote"><i class='bx bx-loader-alt bx-spin'></i> Checking with our assistant...</p>
                <p class="kbk-error" id="errorNote" style="display:none;"><i class='bx bx-error-circle'></i> Something went wrong. Please try again.</p>

                <div class="kbk-result" id="kbkResult">
                    <div class="kbk-result-card" id="resultCard">
                        <div class="kbk-result-tag" id="resultTag">Routine</div>
                        <h4 id="resultTitle">Looks like a routine case</h4>
                        <p id="resultText">A regular appointment slot should work well for this.</p>
                    </div>

                    <div class="kbk-gate">
                        <i class='bx bx-lock-alt'></i>
                        <div>
                            <strong>Want to book this appointment?</strong>
                            <p>Log in or create a free account &mdash; your symptom result carries over automatically.</p>
                        </div>
                        <div class="kbk-gate-actions">
                            <a href="{{ route('login') }}" class="kpz-btn kpz-btn-brown">Log In / Register</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <p class="kbk-disclaimer"><i class='bx bx-info-circle'></i> This tool gives general guidance only and does not replace professional dental advice. If you're in severe pain or bleeding heavily, please seek emergency care immediately.</p>
    </div>
</div>

<style>
    :root {
        --vanilla: #FFF8E2;
        --buttercream: #FFEFB9;
        --powder: #BCD1DE;
        --walnut: #5E442E;
        --walnut-dark: #3f2e1f;
        --ink: #3a2c20;
    }

    .kbk {
        min-height: 100vh;
        background: var(--vanilla);
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: var(--ink);
        padding-bottom: 60px;
    }

    .kbk-wrap {
        max-width: 680px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .kbk-topbar {
        background: rgba(255, 248, 226, .9);
        backdrop-filter: blur(8px);
        border-bottom: 1px solid rgba(94, 68, 46, .1);
        position: sticky;
        top: 0;
        z-index: 20;
    }

    .kbk-topbar-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 66px;
    }

    .kbk-back {
        display: flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        color: var(--walnut);
        font-weight: 700;
        font-size: .9rem;
    }

    .kbk-back:hover {
        color: var(--walnut-dark);
    }

    .kbk-topbar-title {
        font-family: 'Fraunces', serif;
        font-weight: 600;
        color: var(--walnut);
        font-size: 1.05rem;
    }

    .kbk-guest-badge {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: .75rem;
        font-weight: 700;
        color: var(--walnut);
        background: var(--powder);
        padding: 5px 12px;
        border-radius: 999px;
    }

    .kbk-main {
        padding-top: 40px;
    }

    .kbk-intro {
        text-align: center;
        margin-bottom: 30px;
    }

    .kauth-eyebrow {
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: var(--walnut);
        opacity: .65;
    }

    .kbk-h1 {
        font-family: 'Fraunces', serif;
        font-size: 2.1rem;
        color: var(--walnut);
        margin: 8px 0 12px;
    }

    .kbk-sub {
        color: #5c4d3f;
        font-size: .94rem;
        line-height: 1.6;
        max-width: 520px;
        margin: 0 auto;
    }

    .kbk-card {
        background: #fff;
        border-radius: 22px;
        padding: 36px 32px;
        box-shadow: 0 20px 45px rgba(94, 68, 46, .1);
    }

    .kbk-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 24px;
    }

    .kbk-chip {
        padding: 10px 18px;
        border-radius: 999px;
        border: 1.5px solid rgba(94, 68, 46, .3);
        background: var(--vanilla);
        color: var(--walnut);
        font-weight: 600;
        font-size: .85rem;
        cursor: pointer;
        transition: all .18s ease;
    }

    .kbk-chip:hover {
        border-color: var(--walnut);
    }

    .kbk-chip.selected {
        background: var(--walnut);
        border-color: var(--walnut);
        color: var(--vanilla);
    }

    .kbk-textarea-wrap {
        display: block;
        margin-bottom: 24px;
    }

    .kbk-textarea-wrap span {
        display: block;
        font-size: .8rem;
        font-weight: 700;
        color: var(--walnut);
        margin-bottom: 6px;
    }

    .kbk-textarea-box {
        position: relative;
    }

    .kbk-textarea-box textarea {
        width: 100%;
        border: 1px solid rgba(94, 68, 46, .25);
        border-radius: 14px;
        padding: 14px 92px 14px 16px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: .92rem;
        color: var(--ink);
        background: var(--vanilla);
        resize: vertical;
    }

    .kbk-textarea-box textarea:focus {
        outline: none;
        border-color: var(--powder);
        box-shadow: 0 0 0 3px rgba(188, 209, 222, .4);
    }

    .kbk-mic-controls {
        position: absolute;
        right: 10px;
        top: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .kbk-lang-toggle {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: 1.5px solid rgba(94, 68, 46, .25);
        background: var(--vanilla);
        color: var(--walnut);
        font-size: .68rem;
        font-weight: 800;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background .2s ease, border-color .2s ease;
    }

    .kbk-lang-toggle:hover {
        border-color: var(--walnut);
    }

    .kbk-mic {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: none;
        background: var(--powder);
        color: var(--walnut);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 1.05rem;
        transition: background .2s ease;
        flex-shrink: 0;
    }

    .kbk-mic:hover {
        background: var(--buttercream);
    }

    .kbk-mic:disabled {
        opacity: .4;
        cursor: not-allowed;
    }

    .kbk-mic.listening {
        background: var(--walnut);
        color: var(--vanilla);
        animation: kbkPulse 1s ease infinite;
    }

    .kbk-voice-note {
        font-size: .78rem;
        color: #8a7864;
        margin: 6px 0 0;
    }

    @keyframes kbkPulse {

        0%,
        100% {
            box-shadow: 0 0 0 0 rgba(94, 68, 46, .4);
        }

        50% {
            box-shadow: 0 0 0 8px rgba(94, 68, 46, 0);
        }
    }

    .kpz-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 12px 24px;
        border-radius: 999px;
        font-weight: 700;
        font-size: .92rem;
        border: 2px solid transparent;
        cursor: pointer;
        transition: transform .18s ease, box-shadow .18s ease, opacity .2s ease;
        text-decoration: none;
    }

    .kpz-btn-brown {
        background: var(--walnut);
        color: var(--vanilla);
    }

    .kpz-btn-brown:hover {
        background: var(--walnut-dark);
        box-shadow: 0 8px 20px rgba(94, 68, 46, .3);
        transform: translateY(-2px);
    }

    .kpz-btn-brown:disabled {
        opacity: .4;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .kbk-analyze {
        width: 100%;
        padding: 14px;
    }

    .kbk-analyzing {
        display: none;
        align-items: center;
        gap: 8px;
        justify-content: center;
        font-size: .85rem;
        color: var(--walnut);
        margin-top: 14px;
    }

    .kbk-analyzing.show {
        display: flex;
    }

    .kbk-error {
        display: flex;
        align-items: center;
        gap: 8px;
        justify-content: center;
        font-size: .85rem;
        color: #a13d3d;
        margin-top: 14px;
    }

    .kbk-result {
        display: none;
        margin-top: 26px;
        animation: kbkFadeIn .4s ease both;
    }

    .kbk-result.show {
        display: block;
    }

    @keyframes kbkFadeIn {
        from {
            opacity: 0;
            transform: translateY(14px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .kbk-result-card {
        border-radius: 16px;
        padding: 22px 24px;
        background: var(--buttercream);
        margin-bottom: 18px;
        border-left: 5px solid var(--walnut);
    }

    .kbk-result-card.urgent {
        background: #fde3d0;
        border-left-color: #b5651d;
    }

    .kbk-result-card.critical {
        background: #fbdada;
        border-left-color: #a13d3d;
    }

    .kbk-result-card.off-topic {
        background: #eee8de;
        border-left-color: #8a7864;
    }

    .kbk-result-card.off-topic .kbk-result-tag {
        background: #8a7864;
    }

    .kbk-result-tag {
        display: inline-block;
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        background: var(--walnut);
        color: var(--vanilla);
        padding: 4px 12px;
        border-radius: 999px;
        margin-bottom: 10px;
    }

    .kbk-result-card.urgent .kbk-result-tag {
        background: #b5651d;
    }

    .kbk-result-card.critical .kbk-result-tag {
        background: #a13d3d;
    }

    .kbk-result-card h4 {
        font-family: 'Fraunces', serif;
        color: var(--walnut);
        font-size: 1.1rem;
        margin: 0 0 6px;
    }

    .kbk-result-card p {
        font-size: .88rem;
        color: #5c4d3f;
        margin: 0;
        line-height: 1.5;
    }

    .kbk-gate {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        background: var(--vanilla);
        border: 1px dashed rgba(94, 68, 46, .35);
        border-radius: 16px;
        padding: 20px;
    }

    .kbk-gate i {
        font-size: 1.4rem;
        color: var(--walnut);
        flex-shrink: 0;
        margin-top: 2px;
    }

    .kbk-gate strong {
        display: block;
        color: var(--walnut);
        font-size: .95rem;
        margin-bottom: 4px;
    }

    .kbk-gate p {
        font-size: .85rem;
        color: #5c4d3f;
        margin: 0 0 12px;
        line-height: 1.5;
    }

    .kbk-disclaimer {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        font-size: .8rem;
        color: #8a7864;
        margin-top: 24px;
        line-height: 1.5;
    }

    .kbk-disclaimer i {
        flex-shrink: 0;
        margin-top: 2px;
    }

    @media (max-width:600px) {
        .kbk-card {
            padding: 26px 20px;
        }

        .kbk-gate {
            flex-direction: column;
        }
    }
</style>

<script>
    (function() {
        var state = {
            symptoms: []
        };

        var chips = document.querySelectorAll('.kbk-chip');
        var analyzeBtn = document.getElementById('analyzeBtn');

        chips.forEach(function(chip) {
            chip.addEventListener('click', function() {
                chip.classList.toggle('selected');
                var label = chip.textContent.trim();
                var idx = state.symptoms.indexOf(label);
                if (chip.classList.contains('selected') && idx === -1) {
                    state.symptoms.push(label);
                } else if (!chip.classList.contains('selected') && idx > -1) {
                    state.symptoms.splice(idx, 1);
                }
            });
        });

        /**
         * Calls the real backend endpoint. The controller tries the Gemini
         * API first and falls back to a rule-based check server-side if
         * Gemini is unavailable — this page never talks to Gemini directly.
         */
        function getCsrfHeaders() {
            var metaToken = document.querySelector('meta[name="csrf-token"]');
            if (metaToken) {
                return { 'X-CSRF-TOKEN': metaToken.content };
            }
            // Fallback: Laravel's VerifyCsrfToken middleware also accepts the
            // XSRF-TOKEN cookie value via this specific header name (it
            // decrypts it automatically). Only used if no meta tag exists.
            var match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
            return match ? { 'X-XSRF-TOKEN': decodeURIComponent(match[1]) } : {};
        }

        function analyzeSymptoms() {
            var details = document.getElementById('symptomDetails').value;

            return fetch('{{ route('symptom-checker.analyze') }}', {
                method: 'POST',
                headers: Object.assign({
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                }, getCsrfHeaders()),
                body: JSON.stringify({
                    symptoms: state.symptoms,
                    description: details
                })
            }).then(function(res) {
                if (!res.ok) throw new Error('Request failed: ' + res.status);
                return res.json();
            }).then(function(data) {
                console.log('Symptom check source:', data.source); // 'ai' or 'rule-based'
                return {
                    severity: data.severity,
                    title: data.verdict,
                    text: data.advice,
                    source: data.source,
                    onTopic: data.on_topic !== false
                };
            });
        }

        analyzeBtn.addEventListener('click', function() {
            document.getElementById('analyzingNote').classList.add('show');
            document.getElementById('errorNote').style.display = 'none';
            document.getElementById('kbkResult').classList.remove('show');
            analyzeBtn.disabled = true;

            analyzeSymptoms().then(function(result) {
                document.getElementById('analyzingNote').classList.remove('show');
                analyzeBtn.disabled = false;

                var card = document.getElementById('resultCard');
                card.classList.remove('urgent', 'critical', 'off-topic');

                if (!result.onTopic) {
                    card.classList.add('off-topic');
                    document.getElementById('resultTag').textContent = 'Not dental-related';
                } else {
                    if (result.severity !== 'routine') card.classList.add(result.severity);
                    document.getElementById('resultTag').textContent = result.severity.charAt(0).toUpperCase() + result.severity.slice(1);
                }

                document.getElementById('resultTitle').textContent = result.title;
                document.getElementById('resultText').textContent = result.text;
                document.getElementById('kbkResult').classList.add('show');

                // Only invite the patient to book when this was a genuine dental concern.
                document.querySelector('.kbk-gate').style.display = result.onTopic ? 'flex' : 'none';
            }).catch(function(err) {
                console.error(err);
                document.getElementById('analyzingNote').classList.remove('show');
                document.getElementById('errorNote').style.display = 'flex';
                analyzeBtn.disabled = false;
            });
        });

        /**
         * Voice input via the browser's built-in Web Speech API.
         * Free, no backend/API key needed. Only Chrome/Edge support it
         * reliably at time of writing — Firefox/Safari fall back gracefully
         * by disabling the mic button with an explanatory note.
         */
        var micBtn = document.getElementById('micBtn');
        var langToggle = document.getElementById('langToggle');
        var voiceNote = document.getElementById('voiceNote');
        var detailsBox = document.getElementById('symptomDetails');

        var currentLang = 'en-US'; // 'en-US' or 'ms-MY'
        var recognition = null;
        var isListening = false;
        var baseText = ''; // text already in the box before this recording session

        var SpeechRecognitionImpl = window.SpeechRecognition || window.webkitSpeechRecognition;

        function showVoiceNote(msg) {
            voiceNote.textContent = msg;
            voiceNote.style.display = 'block';
        }

        langToggle.addEventListener('click', function() {
            currentLang = currentLang === 'en-US' ? 'ms-MY' : 'en-US';
            langToggle.textContent = currentLang === 'en-US' ? 'EN' : 'BM';
            if (recognition) recognition.lang = currentLang;
        });

        if (!SpeechRecognitionImpl) {
            micBtn.disabled = true;
            showVoiceNote('Voice input isn\'t supported in this browser. Try Chrome or Edge, or just type instead.');
        } else {
            recognition = new SpeechRecognitionImpl();
            recognition.continuous = true;
            recognition.interimResults = true;
            recognition.lang = currentLang;

            recognition.addEventListener('result', function(event) {
                var finalTranscript = '';
                var interimTranscript = '';

                for (var i = event.resultIndex; i < event.results.length; i++) {
                    var transcript = event.results[i][0].transcript;
                    if (event.results[i].isFinal) {
                        finalTranscript += transcript + ' ';
                    } else {
                        interimTranscript += transcript;
                    }
                }

                var separator = baseText && !baseText.endsWith(' ') ? ' ' : '';
                detailsBox.value = baseText + separator + finalTranscript + interimTranscript;

                if (finalTranscript) {
                    baseText = detailsBox.value.trim();
                }
            });

            recognition.addEventListener('error', function(event) {
                isListening = false;
                micBtn.classList.remove('listening');
                if (event.error === 'not-allowed' || event.error === 'permission-denied') {
                    showVoiceNote('Microphone access was blocked. Please allow it in your browser settings to use voice input.');
                } else if (event.error === 'no-speech') {
                    showVoiceNote('Didn\'t catch that — try again, or type instead.');
                } else {
                    showVoiceNote('Voice input had a problem. Please try again or type instead.');
                }
            });

            recognition.addEventListener('end', function() {
                isListening = false;
                micBtn.classList.remove('listening');
            });

            micBtn.addEventListener('click', function() {
                if (isListening) {
                    recognition.stop();
                    isListening = false;
                    micBtn.classList.remove('listening');
                    return;
                }

                baseText = detailsBox.value.trim();
                voiceNote.style.display = 'none';
                recognition.lang = currentLang;

                try {
                    recognition.start();
                    isListening = true;
                    micBtn.classList.add('listening');
                } catch (e) {
                    showVoiceNote('Could not start voice input. Please try again.');
                }
            });
        }
    })();
</script>
@endsection