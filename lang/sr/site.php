<?php

// Tekstovi landing stranice. Nelomljive razmake dodaje helper typo() pri ispisu.

return [

    'brand' => [
        'wordmark_accent' => 'brzi',
        'wordmark_rest' => 'sajt',
        'wordmark_by' => 'by otlr',
        'name' => 'Brzisajt',
    ],

    'meta' => [
        'title' => 'Brzisajt — sajt na Laravelu za 7 dana za :price | otlr.digital',
        'description' => 'Sajt agencijskog nivoa na Laravel frejmvorku: prilagođen mobilnim uređajima, sa preglednim admin panelom, do 7 stranica. Gotov za 7 radnih dana. :price.',
        'privacy_title' => 'Politika privatnosti — Brzisajt',
    ],

    'a11y' => [
        'skip' => 'Pređi na sadržaj',
        'menu_open' => 'Otvori meni',
        'menu_close' => 'Zatvori meni',
        'main_nav' => 'Glavna navigacija',
        'home' => 'Brzisajt — početna',
        'external' => 'otvara se u novoj kartici',
    ],

    'nav' => [
        'why' => 'Zašto mi',
        'how' => 'Kako funkcioniše',
        'examples' => 'Primeri',
        'pricing' => 'Cena',
        'faq' => 'Pitanja',
        'contacts' => 'Kontakt',
        'cta' => 'Sastavi sajt',
    ],

    'hero' => [
        'eyebrow' => 'Brzisajt by otlr.digital',
        'title_1' => 'Sajt agencijskog nivoa.',
        'title_2' => 'Za 7 dana.',
        'title_3' => 'Za :price.',
        'subtitle' => 'Pravimo sajtove za banke, hotele, restorane i premijum brendove. Brzisajt je isti tim i ista tehnologija — u formi gotovih rešenja za one kojima ne treba skup projekat po meri.',
        'chips' => ['7 radnih dana', 'Do 7 stranica', 'Laravel + admin panel', 'Prilagođeno mobilnim uređajima'],
        'cta_primary' => 'Sastavi svoj sajt',
        'cta_secondary' => 'Pogledaj primere',
        'note' => 'Bez online plaćanja: pošaljite upit — javićemo vam se i dogovoriti detalje.',
        'badge' => 'Nije konstruktor!',
    ],

    'clients' => [
        'title' => 'Sarađivali smo sa',
        'logos' => [
            'RŽD (Ruske železnice)', 'VK', 'Infiniti', 'Aurus', 'Rambler',
            'White Rabbit Family', 'STiS',
        ],
    ],

    'about' => [
        'title' => 'Agencija koja ume da napravi velike stvari. I male — brzo.',
        'p1' => 'otlr.digital razvija i promoviše sajtove od 2012. godine. Radili smo za velike kompanije, banke, automobilske i premijum brendove, hotele i restorane, a pokrenuli smo i nekoliko sopstvenih startapa.',
        'p2' => 'Ali ne treba svakom biznisu sajt takvih razmera — i to je sasvim u redu. Za takve potrebe napravili smo Brzisajt.',
        'what_title' => 'Šta je Brzisajt',
        'what_p1' => 'Ovo nije konstruktor sajtova. Vaš sajt sklapamo od naših proverenih rešenja — istih koja koristimo na velikim projektima — i prilagođavamo ga vama. U osnovi je Laravel, jedan od najpopularnijih PHP frejmvorka na svetu, sa preglednim admin panelom u kom sami menjate tekstove, fotografije i sekcije.',
        'what_p2' => 'Sajt možete da razvijate dalje bez ograničenja: da doradite dizajn, dodate funkcije, integracije i jezike. Kako vi rastete, raste i vaš sajt.',
        'mission' => 'Verujemo da dobar sajt treba da bude dostupan svakom biznisu — bez obzira na budžet.',
    ],

    'benefits' => [
        'title' => 'Zašto Brzisajt',
        'items' => [
            ['title' => 'Nije konstruktor — pravi kod.', 'text' => 'Sajt na Laravelu, a ne na platformi sa ograničenjima. Možete ga doraditi kako god želite.'],
            ['title' => 'Sopstveni admin panel.', 'text' => 'Tekstove, fotografije, meni i sekcije menjate sami — bez programera.'],
            ['title' => 'Odlično na svakom ekranu.', 'text' => 'Prilagođen telefonima, tabletima i računarima.'],
            ['title' => 'Sve je vaše.', 'text' => 'Kod, pristupi i admin panel pripadaju vama. Bez mesečne pretplate za platformu.'],
            ['title' => 'Brz i spreman za SEO.', 'text' => 'Čist kod, meta-tagovi, mapa sajta i brzo učitavanje.'],
            ['title' => 'Agencijski tim.', 'text' => 'Vaš sajt prave isti ljudi koji rade na velikim projektima.'],
        ],
    ],

    'compare' => [
        'title' => 'Gde je Brzisajt u odnosu na druge opcije',
        'columns' => [
            'builder' => 'Konstruktor (Wix, Squarespace)',
            'brzi' => 'Brzisajt',
            'custom' => 'Individualni projekat agencije',
        ],
        'rows' => [
            ['label' => 'Cena', 'builder' => 'Mesečna pretplata dok god sajt radi', 'brzi' => ':price jednom', 'custom' => 'od 4.500 €'],
            ['label' => 'Rok', 'builder' => 'Zavisi od vas', 'brzi' => '7 radnih dana', 'custom' => '1–3 meseca'],
            ['label' => 'Ko radi', 'builder' => 'Vi sami', 'brzi' => 'Agencijski tim', 'custom' => 'Agencijski tim'],
            ['label' => 'Kod', 'builder' => 'Vezan za platformu', 'brzi' => 'Potpuno vaš', 'custom' => 'Potpuno vaš'],
            ['label' => 'Razvoj', 'builder' => 'U okviru mogućnosti platforme', 'brzi' => 'Bez ograničenja', 'custom' => 'Bez ograničenja'],
            ['label' => 'Dizajn', 'builder' => 'Šablon kao kod hiljade drugih', 'brzi' => 'Proverena rešenja prilagođena vama', 'custom' => 'Jedinstven dizajn od nule'],
        ],
        'swipe_hint' => 'Prevucite da uporedite',
    ],

    'how' => [
        'title' => 'Kako funkcioniše',
        'steps' => [
            ['title' => 'Sastavite sajt.', 'text' => 'U konfiguratoru izaberite strukturu, stranice, meni, početni ekran i animacije. Traje 3–5 minuta.'],
            ['title' => 'Javljamo vam se.', 'text' => 'Dogovaramo detalje, odgovaramo na pitanja i usaglašavamo materijale: tekstove, fotografije, logo. Nemate fotografije? Izabraćemo ih iz foto-banaka.'],
            ['title' => 'Za 7 radnih dana — gotov sajt.', 'text' => 'Rok počinje da teče nakon uplate avansa i prijema materijala. Sajt pregledate preko linka i imate do dva besplatna kruga izmena.'],
            ['title' => 'Pokretanje.', 'text' => 'Nakon odobrenja i uplate prebacujemo sajt na vaš hosting i povezujemo domen.'],
        ],
    ],

    'examples' => [
        'title' => 'Primeri',
        'subtitle' => 'Ovo su sajtovi koje smo napravili za naše klijente. Stil bilo kog od njih može da bude osnova vašeg Brzisajta.',
        'filter_all' => 'Sve',
        'filter_label' => 'Filter po delatnosti',
        'view' => 'Pogledaj sajt',
        'use_style' => 'Želim ovaj stil',
        'empty' => 'Uskoro ovde stižu primeri.',
    ],

    'industries' => [
        'restaurants' => 'Restorani i barovi',
        'architecture' => 'Arhitektura i dizajn',
        'business' => 'Biznis i usluge',
    ],

    'pricing' => [
        'title' => 'Cena i uslovi',
        'price_label' => 'fiksna cena',
        'included_title' => 'Šta je uključeno',
        'included' => [
            'Do 7 stranica ili sekcija',
            'Prilagođeno mobilnim uređajima',
            'Admin panel na Laravelu',
            'Kontakt forma',
            'Osnovna SEO podešavanja (meta-tagovi, sitemap, robots.txt)',
            'Do dve jezičke verzije',
            'Izbor fotografija iz foto-banaka',
            '2 kruga izmena',
            'Prebacivanje na vaš hosting i povezivanje domena',
            'Garancija 1 mesec nakon pokretanja',
        ],
        'excluded_title' => 'Šta nije uključeno',
        'excluded' => [
            'Online prodavnica i online plaćanje',
            'Integracije sa ERP/CRM sistemima i eksternim servisima',
            'Kupovina hostinga i domena (pomažemo pri izboru)',
        ],
        'payment_title' => 'Plaćanje',
        'payment' => '50% avansa pre početka rada. 50% nakon što odobrite sajt.',
        'warranty_title' => 'Garancija',
        'warranty' => 'Garancija 1 mesec nakon pokretanja',
        'extras_title' => 'Dodatne usluge',
        'extras' => [
            'logo' => 'Dizajn logotipa — od :price',
            'seo' => 'SEO optimizacija i promocija — od :price/mes.',
        ],
        'custom' => 'Treba vam nešto drugačije? Rado ćemo razmotriti individualni zahtev.',
        'custom_cta' => 'Pišite nam',
        'cta' => 'Želim ovakav sajt',
    ],

    'faq' => [
        'title' => 'Pitanja',
        'items' => [
            ['q' => 'Po čemu se razlikujete od konstruktora sajtova?', 'a' => 'Konstruktor je zakup: plaćate pretplatu i ograničeni ste mogućnostima platforme. Brzisajt je pravi sajt na Laravelu koji pripada vama i koji možete razvijati kako god želite.'],
            ['q' => 'Zašto je tako brzo i povoljno?', 'a' => 'Ne crtamo dizajn od nule: koristimo proverena rešenja iz naših stvarnih projekata i prilagođavamo ih vama.'],
            ['q' => 'Da li mogu sam da menjam sajt?', 'a' => 'Da. U admin panelu menjate tekstove, fotografije, meni i sekcije bez programera.'],
            ['q' => 'Šta je potrebno od mene?', 'a' => 'Tekstovi, logo i fotografije (ako ih imate), kao i pristup hostingu i domenu. Ako nešto nedostaje — pomoći ćemo.'],
            ['q' => 'Nemam hosting i domen.', 'a' => 'Hosting i domen kupujete sami, a mi ćemo vam pomoći da izaberete odgovarajuće i posavetovati vas na šta da obratite pažnju. Nakon toga sami prebacujemo sajt i povezujemo domen.'],
            ['q' => 'Da li sajt može da bude na dva jezika?', 'a' => 'Da, do dve jezičke verzije su uključene u cenu.'],
            ['q' => 'Da li sajt kasnije može da se dogradi?', 'a' => 'Da. To je Laravel — sa njim možemo da radimo mi ili bilo koji drugi Laravel programer.'],
            ['q' => 'Treba mi više od 7 stranica ili online prodavnica.', 'a' => 'To je već individualni projekat — pišite nam i poslaćemo vam ponudu.'],
            ['q' => 'Da li postoji garancija?', 'a' => 'Da, mesec dana nakon pokretanja. Ako se u radu sajta pojavi greška za koju smo mi odgovorni, ispravićemo je besplatno.'],
            ['q' => 'Da li radite na daljinu?', 'a' => 'Da, sa klijentima iz cele Srbije, regiona i inostranstva.'],
        ],
    ],

    'final' => [
        'title' => 'Spremni? Sastavite sajt za 5 minuta.',
        'cta' => 'Sastavi sajt',
    ],

    'contact' => [
        'title' => 'Kontakt',
        'or_call' => 'Ili pozovite:',
        'email_label' => 'Email',
        'phone_label' => 'Telefon',
    ],

    'footer' => [
        'copy' => '© 2026 otlr.digital',
        'privacy' => 'Politika privatnosti',
        'cookies' => 'Podešavanja kolačića',
    ],

    'privacy' => [
        'title' => 'Politika privatnosti',
        'todo' => '[TODO] Tekst politike privatnosti (Zakon o zaštiti podataka o ličnosti) biće dostavljen.',
        'back' => 'Nazad na sajt',
    ],

    'consent' => [
        'text' => 'Koristimo Google Analytics da bismo razumeli kako se sajt koristi. Kolačiće za analitiku postavljamo samo uz vašu saglasnost.',
        'more' => 'Politika privatnosti',
        'accept' => 'Prihvatam',
        'decline' => 'Ne, hvala',
        'label' => 'Saglasnost za kolačiće',
    ],
];
