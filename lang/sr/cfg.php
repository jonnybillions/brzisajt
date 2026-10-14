<?php

// Konfigurator (forma za upit).

return [
    'title' => 'Sastavite svoj sajt',
    'subtitle' => 'Izaberite osnovno — detalje dogovaramo telefonom. Ništa ne plaćate unapred.',
    'steps' => [
        1 => 'Struktura',
        2 => 'Izgled',
        3 => 'Sadržaj',
        4 => 'Kontakt',
    ],
    'step_of' => 'Korak :n od 4',
    'progress_label' => 'Koraci konfiguratora',
    'next' => 'Dalje',
    'back' => 'Nazad',
    'optional' => 'nije obavezno',
    'included' => 'uključeno',
    'reset' => 'Počni iznova',

    // Korak 1 — Struktura
    'type' => [
        'q' => 'Kakav sajt vam treba?',
        'onepage' => 'Jednostranični',
        'onepage_text' => 'Sve informacije na jednoj stranici, sekcije se smenjuju pri skrolovanju. Za manje projekte i promocije.',
        'multipage' => 'Višestranični',
        'multipage_text' => 'Svaka sekcija je posebna stranica. Bolje za SEO i veću količinu sadržaja.',
    ],
    'scroll' => [
        'q' => 'Kako se smenjuju sekcije?',
        'options' => [
            'classic' => 'Klasično skrolovanje',
            'slides' => 'Ekrani preko cele stranice',
            'horizontal' => 'Horizontalno skrolovanje',
        ],
    ],
    'pages' => [
        'q' => 'Koje stranice (sekcije) su vam potrebne?',
        'hint' => 'Početna je već uključena. Ukupno do 7.',
        'counter' => 'Izabrano :n od 7',
        'options' => [
            'home' => 'Početna',
            'about' => 'O nama',
            'services' => 'Usluge',
            'catalog' => 'Meni / Katalog',
            'gallery' => 'Galerija / Portfolio',
            'team' => 'Tim',
            'reviews' => 'Utisci klijenata',
            'blog' => 'Vesti / Blog',
            'contacts' => 'Kontakt',
            'custom' => 'Druga stranica',
        ],
        'custom_placeholder' => 'Naziv stranice',
        'custom_label' => 'Naziv druge stranice',
        'limit' => 'Više od 7 stranica je već individualni projekat. Napišite to u komentaru i poslaćemo vam ponudu.',
        'style_link' => 'Izaberi stil iz naših radova',
        'style_default' => 'Mi ćemo izabrati',
        'style_from' => 'Iz projekta „:project“',
        'locked' => 'Uvek uključena',
    ],

    // Korak 2 — Izgled
    'style' => [
        'q' => 'Opšti stil sajta',
        'trust' => 'Prepuštam vama',
        'trust_text' => 'Izabraćemo stil prema vašoj delatnosti',
        'like' => 'Kao u projektu…',
    ],
    'hero' => [
        'q' => 'Početni ekran',
        'options' => [
            'slider' => 'Slajder sa fotografijama',
            'video' => 'Video',
            'image' => 'Jedna velika fotografija',
        ],
    ],
    'menu' => [
        'type' => [
            'q' => 'Meni',
            'options' => [
                'regular' => 'Klasičan (uvek vidljiv)',
                'burger' => 'Skriven (dugme „hamburger“)',
            ],
        ],
        'position' => [
            'q' => 'Položaj menija',
            'options' => [
                'top' => 'Gore',
                'side' => 'Sa strane',
            ],
        ],
    ],
    'motion' => [
        'q' => 'Animacije',
        'options' => [
            'minimal' => ['title' => 'Minimalne', 'text' => 'mirno i elegantno'],
            'smooth' => ['title' => 'Blage', 'text' => 'nežna pojavljivanja'],
            'expressive' => ['title' => 'Izražajne', 'text' => 'efektni prelazi'],
        ],
    ],
    'colors' => [
        'q' => 'Boje brenda (ako ih imate)',
        'auto' => 'Izaberite vi',
        'add' => 'Dodaj boju',
        'remove' => 'Ukloni boju',
        'color_n' => 'Boja :n',
    ],
    'preview' => [
        'label' => 'Otprilike ovako će izgledati vaš sajt',
        'toggle_show' => 'Prikaži pregled',
        'toggle_hide' => 'Sakrij pregled',
        'url' => 'vas-sajt.rs',
    ],

    // Korak 3 — Sadržaj
    'industry' => [
        'q' => 'Delatnost',
        'placeholder' => 'Izaberite delatnost',
        'options' => [
            'restaurant' => 'Restoran ili kafić',
            'hotel' => 'Hotel',
            'realty' => 'Nekretnine',
            'beauty' => 'Lepota i zdravlje',
            'legal' => 'Pravne i finansijske usluge',
            'manufacturing' => 'Proizvodnja',
            'it' => 'IT i tehnologije',
            'education' => 'Obrazovanje',
            'other' => 'Drugo',
        ],
    ],
    'langs' => [
        'q' => 'Jezici sajta',
        'options' => [
            'one' => 'Jedan jezik',
            'two' => 'Dva jezika (uključeno u cenu)',
        ],
        'two_short' => 'Dva jezika',
        'second' => 'Drugi jezik',
        'second_placeholder' => 'Na primer, engleski',
    ],
    'features' => [
        'q' => 'Šta treba da bude na sajtu',
        'options' => [
            'form' => 'Kontakt forma',
            'catalog' => 'Katalog proizvoda ili usluga (bez online plaćanja)',
            'map' => 'Mapa sa adresom',
            'messengers' => 'Dugmad za mesindžere i društvene mreže',
        ],
    ],
    'texts' => [
        'q' => 'Tekstovi',
        'options' => [
            'have' => 'Imam tekstove',
            'help' => 'Treba mi pomoć sa tekstovima',
        ],
    ],
    'images' => [
        'q' => 'Fotografije',
        'options' => [
            'own' => 'Imam svoje',
            'stock' => 'Izaberite iz foto-banke',
        ],
    ],
    'logo' => [
        'q' => 'Logo',
        'options' => [
            'have' => 'Imam',
            'new' => 'Treba mi novi (od :price)',
        ],
    ],
    'seo' => [
        'q' => 'SEO promocija nakon pokretanja (od :price/mes.)',
        'label' => 'Da, zanima me',
    ],
    'files' => [
        'q' => 'Fajlovi (nije obavezno)',
        'hint' => 'Logo, tekstovi, fotografije — do 20 MB. Ili nalepite link ka oblaku.',
        'choose' => 'Izaberite fajlove',
        'drop' => 'ili ih prevucite ovde',
        'uploading' => 'Otpremanje…',
        'remove' => 'Ukloni fajl',
        'total' => 'Ukupno: :size od 20 MB',
        'link' => 'Link ka oblaku',
        'link_placeholder' => 'Link ka Google Drive-u, Dropbox-u…',
    ],

    // Korak 4 — Kontakt
    'contact' => [
        'name' => 'Ime',
        'company' => 'Kompanija',
        'phone' => 'Telefon',
        'email' => 'Email',
        'hint' => 'Unesite telefon ili email — bar jedno od ta dva.',
        'required' => 'obavezno polje',
    ],
    'channel' => [
        'q' => 'Kako da vas kontaktiramo?',
        'options' => [
            'call' => 'Poziv',
            'viber' => 'Viber',
            'whatsapp' => 'WhatsApp',
            'email' => 'Email',
        ],
    ],
    'comment' => [
        'label' => 'Komentar',
        'placeholder' => 'Recite nam nešto o projektu, rokovima ili posebnim željama',
    ],
    'consent' => [
        'before' => 'Saglasan/na sam sa obradom podataka o ličnosti u skladu sa ',
        'link' => 'politikom privatnosti',
        'after' => '.',
    ],
    'submit' => 'Pošalji upit',
    'submitting' => 'Šaljemo…',
    'submit_note' => 'Ništa ne plaćate sada. Javićemo vam se u roku od jednog radnog dana.',
    'honeypot' => 'Ostavite ovo polje prazno',

    // Panel sa pregledom
    'sum' => [
        'title' => 'Vaš sajt',
        'base' => 'Brzisajt — :price',
        'extra_logo' => 'logo od :price',
        'extra_seo' => 'SEO od :price/mes.',
        'note' => 'Konačnu cenu potvrđujemo nakon razgovora sa vama.',
        'type' => 'Tip',
        'pages' => 'Stranice',
        'style' => 'Stil',
        'hero' => 'Početni ekran',
        'menu' => 'Meni',
        'motion' => 'Animacije',
        'colors' => 'Boje',
        'industry' => 'Delatnost',
        'langs' => 'Jezici',
        'features' => 'Na sajtu',
        'empty' => '—',
        'open' => 'Prikaži izbor',
        'close' => 'Skupi',
    ],

    // Uspeh
    'ok' => [
        'title' => 'Upit je poslat!',
        'text' => 'Hvala, :name. Javićemo vam se u toku radnog dana. Broj vašeg upita je :number.',
        'back' => 'Nazad na sajt',
    ],

    'errors' => [
        'too_fast' => 'Izgleda da je forma popunjena prebrzo. Proverite podatke i pošaljite ponovo.',
        'rate_limited' => 'Previše upita sa vaše adrese. Pokušajte kasnije ili nas pozovite.',
        'captcha' => 'Potvrdite da niste robot.',
        'generic' => 'Upit nije poslat. Pokušajte ponovo ili nas pozovite.',
        'max_pages' => 'Možete izabrati najviše 7 stranica, uključujući početnu.',
        'contact_required' => 'Unesite telefon ili email — bar jedno od ta dva.',
        'files_total' => 'Ukupna veličina fajlova može biti najviše 20 MB.',
    ],
];
