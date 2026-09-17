{{-- Floating WhatsApp contact button --}}
@php
    $kwaNumber  = preg_replace('/\D/', '', (string) config('clinic.whatsapp'));
    $kwaMessage = rawurlencode('Hi KPZ Dentalcare, saya nak tanya pasal appointment.');
@endphp

@if ($kwaNumber)
    <a href="https://wa.me/{{ $kwaNumber }}?text={{ $kwaMessage }}"
       class="kwa-float"
       target="_blank"
       rel="noopener noreferrer"
       aria-label="Chat with KPZ Dentalcare on WhatsApp">
        <span class="kwa-float-icon"><i class='bx bxl-whatsapp'></i></span>
        <span class="kwa-float-label">Chat with us</span>
    </a>

    <style>
        .kwa-float {
            position: fixed;
            right: 22px;
            bottom: 26px;
            z-index: 1040; /* below Bootstrap modals (1055) on purpose */
            display: flex;
            align-items: center;
            gap: 0;
            height: 56px;
            padding: 0;
            border-radius: 999px;
            background: #25D366;
            color: #fff;
            text-decoration: none;
            box-shadow: 0 10px 24px rgba(37, 211, 102, .35),
                        0 2px 6px rgba(94, 68, 46, .18);
            overflow: hidden;
            transition: width .28s ease, box-shadow .28s ease, transform .2s ease;
            width: 56px;
        }

        .kwa-float-icon {
            flex: 0 0 56px;
            width: 56px;
            height: 56px;
            display: grid;
            place-items: center;
            font-size: 30px;
            line-height: 1;
        }

        .kwa-float-label {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            font-size: 15px;
            font-weight: 600;
            white-space: nowrap;
            padding-right: 22px;
            opacity: 0;
            transform: translateX(-6px);
            transition: opacity .22s ease .06s, transform .22s ease .06s;
        }

        .kwa-float:hover,
        .kwa-float:focus-visible {
            width: 186px;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(37, 211, 102, .42),
                        0 3px 8px rgba(94, 68, 46, .2);
        }

        .kwa-float:hover .kwa-float-label,
        .kwa-float:focus-visible .kwa-float-label {
            opacity: 1;
            transform: translateX(0);
        }

        .kwa-float:focus-visible {
            outline: 3px solid var(--powder, #BCD1DE);
            outline-offset: 3px;
        }

        /* soft pulse so it gets noticed without being annoying */
        .kwa-float::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 999px;
            border: 2px solid #25D366;
            animation: kwaPulse 2.6s ease-out infinite;
            pointer-events: none;
        }

        @keyframes kwaPulse {
            0%   { transform: scale(1);    opacity: .55; }
            70%  { transform: scale(1.45); opacity: 0;   }
            100% { transform: scale(1.45); opacity: 0;   }
        }

        @media (max-width: 575.98px) {
            .kwa-float {
                right: 16px;
                bottom: 18px;
                height: 52px;
                width: 52px;
            }
            .kwa-float-icon {
                flex-basis: 52px;
                width: 52px;
                height: 52px;
                font-size: 28px;
            }
            .kwa-float:hover,
            .kwa-float:focus-visible { width: 52px; }
            .kwa-float-label { display: none; }
        }

        @media (prefers-reduced-motion: reduce) {
            .kwa-float,
            .kwa-float-label { transition: none; }
            .kwa-float::before { animation: none; opacity: 0; }
        }
    </style>
@endif