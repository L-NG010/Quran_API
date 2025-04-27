import { writeFileSync } from 'fs';

const surahs = [
    { name: "Al-Fatiha", ayat: 7 },
    { name: "Al-Fatihah", ayat: 7 }, // Pertahankan entri Al-Fatihah
    { name: "Al-Baqarah", ayat: 286 },
    { name: "Al-Imran", ayat: 200 },
    { name: "An-Nisa", ayat: 176 },
    { name: "Al-Ma'idah", ayat: 120 },
    { name: "Al-An'am", ayat: 165 },
    { name: "Al-A'raf", ayat: 206 },
    { name: "Al-Anfal", ayat: 75 },
    { name: "At-Taubah", ayat: 129 },
    { name: "Yunus", ayat: 109 },
    { name: "Hud", ayat: 123 },
    { name: "Yusuf", ayat: 111 },
    { name: "Ar-Ra'd", ayat: 43 },
    { name: "Ibrahim", ayat: 52 },
    { name: "Al-Hijr", ayat: 99 },
    { name: "An-Nahl", ayat: 128 },
    { name: "Al-Isra", ayat: 111 },
    { name: "Al-Kahf", ayat: 110 },
    { name: "Maryam", ayat: 98 },
    { name: "Ta-Ha", ayat: 135 },
    { name: "Al-Anbiya", ayat: 112 },
    { name: "Al-Hajj", ayat: 78 },
    { name: "Al-Mu'minun", ayat: 118 },
    { name: "An-Nur", ayat: 64 },
    { name: "Al-Furqan", ayat: 77 },
    { name: "Ash-Shu'ara", ayat: 227 },
    { name: "An-Naml", ayat: 93 },
    { name: "Al-Qasas", ayat: 88 },
    { name: "Al-Ankabut", ayat: 69 },
    { name: "Ar-Rum", ayat: 60 },
    { name: "Luqman", ayat: 34 },
    { name: "As-Sajdah", ayat: 30 },
    { name: "Al-Ahzab", ayat: 73 },
    { name: "Saba", ayat: 54 },
    { name: "Fatir", ayat: 45 },
    { name: "Ya-Sin", ayat: 83 },
    { name: "As-Saffat", ayat: 182 },
    { name: "Sad", ayat: 88 },
    { name: "Az-Zumar", ayat: 75 },
    { name: "Ghafir", ayat: 85 },
    { name: "Fussilat", ayat: 54 },
    { name: "Ash-Shura", ayat: 53 },
    { name: "Az-Zukhruf", ayat: 89 },
    { name: "Ad-Dukhan", ayat: 59 },
    { name: "Al-Jathiyah", ayat: 37 },
    { name: "Al-Ahqaf", ayat: 35 },
    { name: "Muhammad", ayat: 38 },
    { name: "Al-Fath", ayat: 29 },
    { name: "Al-Hujurat", ayat: 18 },
    { name: "Qaf", ayat: 45 },
    { name: "Adh-Dhariyat", ayat: 60 },
    { name: "At-Tur", ayat: 49 },
    { name: "An-Najm", ayat: 62 },
    { name: "Al-Qamar", ayat: 55 },
    { name: "Ar-Rahman", ayat: 78 },
    { name: "Al-Waqi'ah", ayat: 96 },
    { name: "Al-Hadid", ayat: 29 },
    { name: "Al-Mujadila", ayat: 22 },
    { name: "Al-Hashr", ayat: 24 },
    { name: "Al-Mumtahanah", ayat: 13 },
    { name: "As-Saff", ayat: 14 },
    { name: "Al-Jumu'ah", ayat: 11 },
    { name: "Al-Munafiqun", ayat: 11 },
    { name: "At-Taghabun", ayat: 18 },
    { name: "At-Talaq", ayat: 12 },
    { name: "At-Tahrim", ayat: 12 },
    { name: "Al-Mulk", ayat: 30 },
    { name: "Al-Qalam", ayat: 52 },
    { name: "Al-Haqqah", ayat: 52 },
    { name: "Al-Ma'arij", ayat: 44 },
    { name: "Nuh", ayat: 28 },
    { name: "Al-Jinn", ayat: 28 },
    { name: "Al-Muzzammil", ayat: 20 },
    { name: "Al-Muddaththir", ayat: 56 },
    { name: "Al-Qiyamah", ayat: 40 },
    { name: "Al-Insan", ayat: 31 },
    { name: "Al-Mursalat", ayat: 50 },
    { name: "An-Naba", ayat: 40 },
    { name: "An-Nazi'at", ayat: 46 },
    { name: "Abasa", ayat: 42 },
    { name: "At-Takwir", ayat: 29 },
    { name: "Al-Infitar", ayat: 19 },
    { name: "Al-Mutaffifin", ayat: 36 },
    { name: "Al-Inshiqaq", ayat: 25 },
    { name: "Al-Buruj", ayat: 22 },
    { name: "At-Tariq", ayat: 17 },
    { name: "Al-A'la", ayat: 19 },
    { name: "Al-Ghashiyah", ayat: 26 },
    { name: "Al-Fajr", ayat: 30 },
    { name: "Al-Balad", ayat: 20 },
    { name: "Ash-Shams", ayat: 15 },
    { name: "Al-Lail", ayat: 21 },
    { name: "Ad-Duha", ayat: 11 },
    { name: "Ash-Sharh", ayat: 8 },
    { name: "At-Tin", ayat: 8 },
    { name: "Al-Alaq", ayat: 19 },
    { name: "Al-Qadr", ayat: 5 },
    { name: "Al-Bayyinah", ayat: 8 },
    { name: "Az-Zalzalah", ayat: 8 },
    { name: "Al-Adiyat", ayat: 11 },
    { name: "Al-Qari'ah", ayat: 11 },
    { name: "At-Takathur", ayat: 8 },
    { name: "Al-Asr", ayat: 3 },
    { name: "Al-Humazah", ayat: 9 },
    { name: "Al-Fil", ayat: 5 },
    { name: "Quraysh", ayat: 4 },
    { name: "Al-Ma'un", ayat: 7 },
    { name: "Al-Kawthar", ayat: 3 },
    { name: "Al-Kafirun", ayat: 6 },
    { name: "An-Nasr", ayat: 3 },
    { name: "Al-Masad", ayat: 5 },
    { name: "Al-Ikhlas", ayat: 4 },
    { name: "Al-Falaq", ayat: 5 },
    { name: "An-Nas", ayat: 6 }
];

// Fungsi untuk meng-escape tanda kutip tunggal dalam string SQL
function escapeSQLString(str) {
    return str.replace(/'/g, "''");
}

const lines = new Set(); // Gunakan Set untuk mencegah duplikasi
surahs.forEach((surah, i) => {
    // Tetapkan reference_id: Al-Fatiha dan Al-Fatihah dapat ID=1, lainnya bertambah dari 2
    const reference_id = i === 0 || i === 1 ? '1' : `${i}`; // Al-Fatiha (i=0) dan Al-Fatihah (i=1) dapat ID=1
    const baseName = surah.name.toLowerCase();
    const spaced = baseName.replace(/-/g, ' ');
    const contiguous = spaced.replace(/\s+/g, '');
    const hyphenated = baseName;
    const noPrefix = spaced.replace(/^al-|^an-|^as-|^ar-|^at-|^ash-|^ad-/i, '').trim();
    const noPrefixContiguous = noPrefix.replace(/\s+/g, '');

    const prefixes = ['surat', 'surah', 'sura'];
    const misspellings = {
        'al-fatiha': ['alfatiha', 'al fatiha', 'fatiha', 'fatihah', 'al-fatihah', 'alfatihah', 'al fatihah'], // Gabungkan semua variasi
        'al-baqarah': ['albaqarah', 'al baqarah', 'al-bakarah', 'al-baqara', 'al bakara', 'baqarah', 'baqara'],
        'al-imran': ['alimran', 'al imran', 'imran', 'al-imraan', 'al imraan'],
        'an-nisa': ['annisa', 'an nisa', 'nisa', 'an-nisaa', 'an nisaa'],
        'al-ma\'idah': ['almaidah', 'al maidah', 'maidah', 'al-maida', 'al maida'],
        'al-an\'am': ['alanam', 'al anam', 'anam', 'al-anaam', 'al anaam'],
        'al-a\'raf': ['alaraf', 'al araf', 'araf', 'al-araaf', 'al araaf'],
        'al-anfal': ['alanfal', 'al anfal', 'anfal', 'al-anfaal', 'al anfaal'],
        'at-taubah': ['attaubah', 'at taubah', 'taubah', 'at-tauba', 'at tauba'],
        'yunus': ['younus', 'yoonus', 'yunus'],
        'hud': ['houd', 'hood', 'huud'],
        'yusuf': ['yousuf', 'yoosuf', 'yusouf'],
        'ar-ra\'d': ['arra\'d', 'ar ra\'d', 'ra\'d', 'ar-rad', 'ar rad'],
        'ibrahim': ['ibraheem', 'ibraaheem', 'ibrahim'],
        'al-hijr': ['alhijr', 'al hijr', 'hijr', 'al-hijir', 'al hijir'],
        'an-nahl': ['annahl', 'an nahl', 'nahl', 'an-nahal', 'an nahal'],
        'al-isra': ['alisra', 'al isra', 'isra', 'al-israa', 'al israa'],
        'al-kahf': ['alkahf', 'al kahf', 'kahf', 'al-kahaf', 'al kahaf'],
        'maryam': ['mariam', 'maryam', 'mariyam'],
        'ta-ha': ['taha', 'ta ha', 'tahaa', 'ta haa'],
        'al-anbiya': ['alanbiya', 'al anbiya', 'anbiya', 'al-anbiyaa', 'al anbiyaa'],
        'al-hajj': ['alhajj', 'al hajj', 'hajj', 'al-haj', 'al haj'],
        'al-mu\'minun': ['almu\'minun', 'al mu\'minun', 'mu\'minun', 'al-muminun', 'al muminun'],
        'an-nur': ['annur', 'an nur', 'nur', 'an-nuur', 'an nuur'],
        'al-furqan': ['alfurqan', 'al furqan', 'furqan', 'al-furqaan', 'al furqaan'],
        'ash-shu\'ara': ['ashshu\'ara', 'ash shu\'ara', 'shu\'ara', 'ash-shuara', 'ash shuara'],
        'an-naml': ['annaml', 'an naml', 'naml', 'an-namal', 'an namal'],
        'al-qasas': ['alqasas', 'al qasas', 'qasas', 'al-qasaas', 'al qasaas'],
        'al-ankabut': ['alankabut', 'al ankabut', 'ankabut', 'al-ankaboot', 'al ankaboot'],
        'ar-rum': ['arrum', 'ar rum', 'rum', 'ar-room', 'ar room'],
        'luqman': ['luqmaan', 'luqman'],
        'as-sajdah': ['assajdah', 'as sajdah', 'sajdah', 'as-sajda', 'as sajda'],
        'al-ahzab': ['alahzab', 'al ahzab', 'ahzab', 'al-ahzaab', 'al ahzaab'],
        'saba': ['sabaa', 'saba'],
        'fatir': ['faatir', 'fathir', 'fatir'],
        'ya-sin': ['yasin', 'ya sin', 'yaseen', 'yasen', 'ya seen'],
        'as-saffat': ['assaffat', 'as saffat', 'saffat', 'as-saffaat', 'as saffaat'],
        'sad': ['saad', 'sad'],
        'az-zumar': ['azzumar', 'az zumar', 'zumar', 'az-zumarr', 'az zumarr'],
        'ghafir': ['ghaafir', 'ghafir'],
        'fussilat': ['fussilaat', 'fussilat', 'fusilat'],
        'ash-shura': ['ashshura', 'ash shura', 'shura', 'ash-shuraa', 'ash shuraa'],
        'az-zukhruf': ['azzukhruf', 'az zukhruf', 'zukhruf', 'az-zukruf', 'az zukruf'],
        'ad-dukhan': ['addukhan', 'ad dukhan', 'dukhan', 'ad-dukhaan', 'ad dukhaan'],
        'al-jathiyah': ['aljathiyah', 'al jathiyah', 'jathiyah', 'al-jathiya', 'al jathiya'],
        'al-ahqaf': ['alahqaf', 'al ahqaf', 'ahqaf', 'al-ahqaaf', 'al ahqaaf'],
        'muhammad': ['muhammad', 'muhamad', 'mohammad'],
        'al-fath': ['alfath', 'al fath', 'fath', 'al-faat', 'al faat'],
        'al-hujurat': ['alhujurat', 'al hujurat', 'hujurat', 'al-hujuraat', 'al hujuraat'],
        'qaf': ['qaaf', 'qaf'],
        'adh-dhariyat': ['adhdhariyat', 'adh dhariyat', 'dhariyat', 'adh-dhariyaat', 'adh dhariyaat'],
        'at-tur': ['attur', 'at tur', 'tur', 'at-tuur', 'at tuur'],
        'an-najm': ['annajm', 'an najm', 'najm', 'an-najam', 'an najam'],
        'al-qamar': ['alqamar', 'al qamar', 'qamar', 'al-qamaar', 'al qamaar'],
        'ar-rahman': ['arrahman', 'ar rahman', 'rahman', 'ar-rahmaan', 'ar rahmaan'],
        'al-waqi\'ah': ['alwaqi\'ah', 'al waqi\'ah', 'waqi\'ah', 'al-waqiah', 'al waqiah'],
        'al-hadid': ['alhadid', 'al hadid', 'hadid', 'al-hadiid', 'al hadiid'],
        'al-mujadila': ['almujadila', 'al mujadila', 'mujadila', 'al-mujadilah', 'al mujadilah'],
        'al-hashr': ['alhashr', 'al hashr', 'hashr', 'al-haashr', 'al haashr'],
        'al-mumtahanah': ['almumtahanah', 'al mumtahanah', 'mumtahanah', 'al-mumtahana', 'al mumtahana'],
        'as-saff': ['assaff', 'as saff', 'saff', 'as-saaf', 'as saaf'],
        'al-jumu\'ah': ['aljumu\'ah', 'al jumu\'ah', 'jumu\'ah', 'al-jumua', 'al jumua'],
        'al-munafiqun': ['almunafiqun', 'al munafiqun', 'munafiqun', 'al-munafiqoon', 'al munafiqoon'],
        'at-taghabun': ['attaghabun', 'at taghabun', 'taghabun', 'at-taghbun', 'at taghbun'],
        'at-talaq': ['attalaq', 'at talaq', 'talaq', 'at-talaaq', 'at talaaq'],
        'at-tahrim': ['attahrim', 'at tahrim', 'tahrim', 'at-tahreem', 'at tahreem'],
        'al-mulk': ['almulk', 'al mulk', 'mulk', 'al-mulak', 'al mulak'],
        'al-qalam': ['alqalam', 'al qalam', 'qalam', 'al-qalaam', 'al qalaam'],
        'al-haqqah': ['alhaqqah', 'al haqqah', 'haqqah', 'al-haqqa', 'al haqqa'],
        'al-ma\'arij': ['alma\'arij', 'al ma\'arij', 'ma\'arij', 'al-maarij', 'al maarij'],
        'nuh': ['nooh', 'nuuh', 'nuh'],
        'al-jinn': ['aljinn', 'al jinn', 'jinn', 'al-jin', 'al jin'],
        'al-muzzammil': ['almuzzammil', 'al muzzammil', 'muzzammil', 'al-muzammil', 'al muzammil'],
        'al-muddaththir': ['almuddaththir', 'al muddaththir', 'muddaththir', 'al-mudaththir', 'al mudaththir'],
        'al-qiyamah': ['alqiyamah', 'al qiyamah', 'qiyamah', 'al-qiyama', 'al qiyama'],
        'al-insan': ['alinsan', 'al insan', 'insan', 'al-insaan', 'al insaan'],
        'al-mursalat': ['almursalat', 'al mursalat', 'mursalat', 'al-mursalaat', 'al mursalaat'],
        'an-naba': ['annaba', 'an naba', 'naba', 'an-nabaa', 'an nabaa'],
        'an-nazi\'at': ['annazi\'at', 'an nazi\'at', 'nazi\'at', 'an-naziat', 'an naziat'],
        'abasa': ['abasa', 'abaasa', 'abaasah'],
        'at-takwir': ['attakwir', 'at takwir', 'takwir', 'at-takweer', 'at takweer'],
        'al-infitar': ['alinfitar', 'al infitar', 'infitar', 'al-infitar', 'al infitaar'],
        'al-mutaffifin': ['almutaffifin', 'al mutaffifin', 'mutaffifin', 'al-mutaffifeen', 'al mutaffifeen'],
        'al-inshiqaq': ['alinshiqaq', 'al inshiqaq', 'inshiqaq', 'al-inshiqaaq', 'al inshiqaaq'],
        'al-buruj': ['alburuj', 'al buruj', 'buruj', 'al-buruj', 'al buruuj'],
        'at-tariq': ['attariq', 'at tariq', 'tariq', 'at-tariiq', 'at tariiq'],
        'al-a\'la': ['alala', 'al a\'la', 'a\'la', 'al-alaa', 'al alaa'],
        'al-ghashiyah': ['alghashiyah', 'al ghashiyah', 'ghashiyah', 'al-ghashiya', 'al ghashiya'],
        'al-fajr': ['alfajr', 'al fajr', 'fajr', 'al-faajr', 'al faajr'],
        'al-balad': ['albalad', 'al balad', 'balad', 'al-balaad', 'al balaad'],
        'ash-shams': ['ashshams', 'ash shams', 'shams', 'ash-shamas', 'ash shamas'],
        'al-lail': ['allail', 'al lail', 'lail', 'al-layl', 'al layl'],
        'ad-duha': ['adduha', 'ad duha', 'duha', 'ad-duhaa', 'ad duhaa'],
        'ash-sharh': ['ashsharh', 'ash sharh', 'sharh', 'ash-sharah', 'ash sharah'],
        'at-tin': ['attin', 'at tin', 'tin', 'at-tiin', 'at tiin'],
        'al-alaq': ['alalaq', 'al alaq', 'alaq', 'al-alaaq', 'al alaaq'],
        'al-qadr': ['alqadr', 'al qadr', 'qadr', 'al-qadar', 'al qadar'],
        'al-bayyinah': ['albayyinah', 'al bayyinah', 'bayyinah', 'al-bayyina', 'al bayyina'],
        'az-zalzalah': ['azzalzalah', 'az zalzalah', 'zalzalah', 'az-zalzala', 'az zalzala'],
        'al-adiyat': ['aladiyat', 'al adiyat', 'adiyat', 'al-adiyaat', 'al adiyaat'],
        'al-qari\'ah': ['alqari\'ah', 'al qari\'ah', 'qari\'ah', 'al-qariah', 'al qariah'],
        'at-takathur': ['attakathur', 'at takathur', 'takathur', 'at-takathuur', 'at takathuur'],
        'al-asr': ['alasr', 'al asr', 'asr', 'al-asar', 'al asar'],
        'al-humazah': ['alhumazah', 'al humazah', 'humazah', 'al-humaazah', 'al humaazah'],
        'al-fil': ['alfil', 'al fil', 'fil', 'al-fiil', 'al fiil'],
        'quraysh': ['quraish', 'quraysh', 'qurayish'],
        'al-ma\'un': ['alma\'un', 'al ma\'un', 'ma\'un', 'al-maun', 'al maun'],
        'al-kawthar': ['alkawthar', 'al kawthar', 'kawthar', 'al-kawtharr', 'al kawtharr'],
        'al-kafirun': ['alkafirun', 'al kafirun', 'kafirun', 'al-kafiroon', 'al kafiroon'],
        'an-nasr': ['annasr', 'an nasr', 'nasr', 'an-nasar', 'an nasar'],
        'al-masad': ['almasad', 'al masad', 'masad', 'al-masaad', 'al masaad'],
        'al-ikhlas': ['alikhlas', 'al ikhlas', 'ikhlas', 'al-ikhlass', 'al ikhlass'],
        'al-falaq': ['alfalaq', 'al falaq', 'falaq', 'al-falaaq', 'al falaaq'],
        'an-nas': ['annas', 'an nas', 'nas', 'an-naas', 'an naas']
    }[baseName] || [];

    // Base aliases
    lines.add(`    ('${escapeSQLString(spaced)}', 'surat', '${reference_id}', NOW(), NOW())`);
    lines.add(`    ('${escapeSQLString(contiguous)}', 'surat', '${reference_id}', NOW(), NOW())`);
    lines.add(`    ('${escapeSQLString(hyphenated)}', 'surat', '${reference_id}', NOW(), NOW())`);

    // Prefix-based aliases
    prefixes.forEach(prefix => {
        lines.add(`    ('${escapeSQLString(prefix + ' ' + spaced)}', 'surat', '${reference_id}', NOW(), NOW())`);
        lines.add(`    ('${escapeSQLString(prefix + ' ' + contiguous)}', 'surat', '${reference_id}', NOW(), NOW())`);
        lines.add(`    ('${escapeSQLString(prefix + ' ' + hyphenated)}', 'surat', '${reference_id}', NOW(), NOW())`);
    });

    // No-prefix aliases
    lines.add(`    ('${escapeSQLString(noPrefix)}', 'surat', '${reference_id}', NOW(), NOW())`);
    lines.add(`    ('${escapeSQLString(noPrefixContiguous)}', 'surat', '${reference_id}', NOW(), NOW())`);

    // Misspelling aliases
    misspellings.forEach(mis => {
        lines.add(`    ('${escapeSQLString(mis)}', 'surat', '${reference_id}', NOW(), NOW())`);
        prefixes.forEach(prefix => {
            lines.add(`    ('${escapeSQLString(prefix + ' ' + mis)}', 'surat', '${reference_id}', NOW(), NOW())`);
        });
    });

    // Ayat references (limited to first 10 ayat or actual ayat count)
    const maxAyat = Math.min(surah.ayat, 10); // Change to surah.ayat for all ayat
    for (let ayat = 1; ayat <= maxAyat; ayat++) {
        lines.add(`    ('${escapeSQLString(spaced + ' ayat ' + ayat)}', 'ayat', '${reference_id}:${ayat}', NOW(), NOW())`);
        lines.add(`    ('${escapeSQLString(hyphenated + ' ayat ' + ayat)}', 'ayat', '${reference_id}:${ayat}', NOW(), NOW())`);
        prefixes.forEach(prefix => {
            lines.add(`    ('${escapeSQLString(prefix + ' ' + spaced + ' ayat ' + ayat)}', 'ayat', '${reference_id}:${ayat}', NOW(), NOW())`);
            lines.add(`    ('${escapeSQLString(prefix + ' ' + hyphenated + ' ayat ' + ayat)}', 'ayat', '${reference_id}:${ayat}', NOW(), NOW())`);
        });
    }

    // Page references (hanya untuk Al-Fatiha/Al-Fatihah, reference_id=1)
    if (reference_id === '1') {
        ['halaman', 'haklaman', 'page'].forEach(pageTerm => {
            lines.add(`    ('${escapeSQLString(pageTerm + ' 1')}', 'surat', '${reference_id}', NOW(), NOW())`);
        });
    }

    // Juz references (untuk Al-Fatiha/Al-Fatihah dan Al-Baqarah)
    if (reference_id === '1' || reference_id === '2') {
        lines.add(`    ('${escapeSQLString('juz 1')}', 'juz', '1', NOW(), NOW())`);
    }
});

// Tulis ke file dengan encoding UTF-8
writeFileSync('search_aliases_seed.sql', `BEGIN;\n\nINSERT INTO \`search_aliases\` (\`keyword\`, \`type\`, \`reference_id\`, \`created_at\`, \`updated_at\`) VALUES\n${Array.from(lines).join(',\n')}\n;\n\nCOMMIT;`, 'utf8');
