<?php

/*
|--------------------------------------------------------------------------
| Landing page strings — English
|--------------------------------------------------------------------------
|
| Every key here must also exist in lang/ms/site.php. If a key is missing
| from one file, Laravel prints the raw key (e.g. "site.nav.services")
| instead of falling back, so keep the two files in step.
|
*/

return [

    'nav' => [
        'services'         => 'Services',
        'about'            => 'About Us',
        'faqs'             => 'FAQs',
        'contact'          => 'Contact',
        'book_now'         => 'Book Now',
        'book_appointment' => 'Book appointment',
        'account_settings' => 'Account settings',
        'admin_dashboard'  => 'Admin dashboard',
        'my_schedule'      => 'My schedule',
        'logout'           => 'Log out',
        'switch_language'  => 'Tukar ke Bahasa Melayu',
        'other_language'   => 'Bahasa Melayu',
    ],

    'hero' => [
        'title_1'     => 'Dental care',
        'title_2'     => 'that fits',
        'title_em'    => 'your day',
        'lead'        => 'Check your symptoms, see real open slots, and book in minutes — no phone queues, no walk-in guesswork.',
        'cta_symptom' => 'Check my symptoms',
        'cta_book'    => 'Book an appointment',
        'trust_label' => 'Built on real patient research',
        'trust_1'     => '69 patients surveyed',
        'trust_2'     => '87% want online booking',
        'trust_3'     => '80% want symptom advice',
        'chip_title'  => 'Next available slot',
        'chip_sub'    => 'Tomorrow, 10:30 AM',
        'chip_today'    => 'Today, :time',
        'chip_tomorrow' => 'Tomorrow, :time',
        'chip_date'     => ':date, :time',
        'chip_none'     => 'Fully booked for the next 2 weeks',
    ],

    'treat' => [
        'eyebrow'         => 'Treatments',
        'title'           => 'Fifteen treatments, one clinic.',
        'sub'             => "From routine cleaning to full tooth replacement. Not sure which one you need? Run a symptom check first, or just ask us.",
        'filter_label'    => 'Filter treatments by category',
        'all'             => 'All',
        'cat_routine'     => 'Routine & preventive',
        'cat_restore'     => 'Restorative',
        'cat_replace'     => 'Replacing teeth',
        'cat_appearance'  => 'Appearance & alignment',
        'empty'           => 'No treatments in this category.',
        'foot_text'       => "Which treatment suits you depends on an examination by the dentist. Book a slot and we'll take a proper look.",
        'foot_cta'        => 'Book an appointment',
        'price_from'  => 'From RM:amount',
        'price_ask'   => 'Price after check-up',
        'price_note'  => "Prices are estimates from the clinic's price list (updated :date). The final cost depends on the dentist's examination.",

        'scaling_name'    => 'Scaling & polishing',
        'scaling_desc'    => 'Full removal of plaque and tartar, and treatment for persistent bad breath.',

        'fluoride_name'   => 'Fluoride application',
        'fluoride_desc'   => "Supports healthy, cavity-free growth in children's teeth. Can begin as early as age one.",

        'extract_name'    => 'Tooth extraction',
        'extract_desc'    => 'Removal of baby and permanent teeth, both routine and surgical.',

        'filling_name'    => 'Tooth-coloured filling',
        'filling_desc'    => 'Seals the cavity that causes pain and lets food get trapped.',

        'rootcanal_name'  => 'Root canal',
        'rootcanal_desc'  => 'Cleans the root canal and removes damaged nerve, so a painful tooth can be kept rather than pulled.',

        'post_name'       => 'Post and core',
        'post_desc'       => 'Strengthens a filling by placing a fibre post permanently inside the tooth.',

        'crown_name'      => 'Crown',
        'crown_desc'      => 'A cap covering the whole tooth. Usually suggested when:',
        'crown_opt_1'     => 'More than half the tooth has broken',
        'crown_opt_2'     => 'The tooth sits slightly crooked',
        'crown_opt_3'     => 'Following a root canal',

        'bridge_name'     => 'Bridge',
        'bridge_desc'     => 'Permanently replaces one or two missing teeth.',

        'denture_name'    => 'Dentures, partial & full',
        'denture_desc'    => 'Two materials, chosen for comfort and how many teeth are missing:',
        'denture_opt_1'   => 'Acrylic — firmer, more affordable',
        'denture_opt_2'   => 'Flexible — softer, more comfortable',

        'frc_name'        => 'Fiber reinforced composite',
        'frc_desc'        => 'Replaces a missing tooth using strong fibre resin.',

        'implant_name'    => 'Implant',
        'implant_desc'    => 'A replacement tooth anchored directly into the jawbone.',

        'veneer_name'     => 'Veneer',
        'veneer_desc'     => 'A thin layer bonded to the front of the tooth. Two options:',
        'veneer_opt_1'    => 'Composite veneer',
        'veneer_opt_2'    => 'Porcelain veneer',

        'whitening_name'  => 'Teeth whitening',
        'whitening_desc'  => 'Whitens the full set of teeth.',

        'braces_name'     => 'Braces',
        'braces_desc'     => 'Corrects how the teeth line up. Two types:',
        'braces_opt_1'    => 'Conventional',
        'braces_opt_2'    => 'Self ligating',

        'retainer_name'   => 'Retainer',
        'retainer_desc'   => 'Holds teeth in position after braces. Three types:',
        'retainer_opt_1'  => 'Hawley',
        'retainer_opt_2'  => 'Essix',
        'retainer_opt_3'  => 'Fixed',
    ],

    'features' => [
        'eyebrow'       => 'How it works',
        'title'         => 'Three modules, one visit.',
        'sub'           => 'Everything the clinic front desk used to handle by phone, now in one place.',

        'booking_title' => 'Online appointment booking',
        'booking_desc'  => 'See genuinely open slots, pick a date and time, and get confirmation without waiting on a callback.',

        'ai_title'      => 'AI symptom checker',
        'ai_desc'       => "Describe what's wrong and get a preliminary read on urgency — before you commit to a visit.",
        'ai_routine'    => 'Routine',
        'ai_urgent'     => 'Urgent',
        'ai_critical'   => 'Critical',
        'ai_link'       => 'Try it free',

        'info_title'    => 'Clinic information',
        'info_desc'     => 'Location, opening hours, and contact details, always one tap away.',

        'remind_title'  => 'Email confirmation',
        'remind_desc'   => 'An email as soon as the clinic confirms your slot, with the date, time and dentist.',
    ],

    'stats' => [
        'cap_1' => 'still book by walking in',
        'cap_2' => 'blocked by limited slots',
        'cap_3' => 'self-medicate after hours',
        'note'  => 'Findings from a pre-development survey of 69 dental patients.',
    ],

    'about' => [
        'eyebrow'        => 'About us',
        'title'          => 'The same clinic. A better front desk.',

        'flow_title_1'   => 'From phone tag',
        'flow_title_2'   => 'to one clear flow',
        'flow_desc'      => "Booking used to mean a phone call, a WhatsApp message, or turning up and hoping. Now it's one path: check, pick, confirm.",
        'flow_phone'     => 'Phone',
        'flow_whatsapp'  => 'WhatsApp',
        'flow_walkin'    => 'Walk-in',
        'flow_booked'    => 'Booked',

        'faster_tag'     => 'Faster',
        'faster_note'    => 'Less time queuing, more time treating',

        'records_1'      => 'Your records,',
        'records_2'      => 'protected',
        'records_desc'   => 'Secure login and role-based access mean only the right people see your details.',

        'guidance_1'     => 'Guidance before',
        'guidance_2'     => 'you even book',
        'guidance_desc'  => "Half of patients don't know what's causing their pain. The symptom checker gives them a starting point instead of a search engine.",

        'map_title'      => 'Find us',
        'directions'     => 'Open in Google Maps',
        'gallery_title'     => 'Inside the clinic',
        'gallery_reception' => 'Front desk',
        'gallery_lounge'    => 'Waiting lounge',
        'gallery_kids'      => "Kids' corner",
        'gallery_room'      => 'Treatment room',
        'shopfront'         => 'Look for this shopfront',
    ],

    'faqs' => [
        'eyebrow' => 'FAQs',
        'title'   => 'Before you book.',

        'q_1' => 'Do I need an account to use the symptom checker?',
        'a_1' => 'No. The symptom checker is open to everyone. You only need an account when you want to reserve an actual appointment slot.',

        'q_2' => 'Is the symptom checker a real diagnosis?',
        'a_2' => 'No. It gives preliminary guidance on urgency only. A licensed dentist confirms any diagnosis and treatment during your visit.',

        'q_3' => 'Can I reschedule or cancel?',
        'a_3' => 'Yes. Message the clinic on WhatsApp or give us a call before your slot, and the front desk will change or cancel it for you.',

        'q_4' => 'What if my case is urgent?',
        'a_4' => 'Urgent cases get flagged and the earliest available slots are highlighted for you. For severe pain or heavy bleeding, please seek emergency care immediately.',
    ],

    'footer' => [
        'title_1'     => "Tell us what's hurting.",
        'title_2'     => "We'll find you a slot.",
        'sub'         => 'Message the front desk on WhatsApp, or head straight to booking.',
        'placeholder' => 'you@example.com',
        'cta'         => 'Chat on WhatsApp',
        'book'        => 'Book a slot',
        'hours'       => 'Daily, 8:30AM–5PM & 8PM–10PM',
        'copy'        => '© 2026 KPZ Dentalcare · FYP by Nur Aqilah Binti Rus Kenedi',

    ],
    'to_top'      => 'Back to top',
    'logout' => [
        'title'   => 'Log out?',
        'text'    => "You'll need to sign in again to continue.",
        'cancel'  => 'Stay signed in',
        'confirm' => 'Log out',
    ],
];
