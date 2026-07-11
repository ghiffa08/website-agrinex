Design & Tech Stack Documentation

Dokumen ini menguraikan konsep desain, arsitektur antarmuka pengguna (UI), dan pilihan teknologi (tech stack) yang digunakan dalam pengembangan Plantify Dashboard.

🎨 1. Konsep Desain (UI/UX)

Plantify Dashboard dirancang menggunakan pendekatan Neumorphism (atau Soft UI). Gaya desain ini berfokus pada penciptaan elemen-elemen antarmuka yang tampak seperti menonjol dari atau mencekung ke dalam latar belakang, menciptakan ilusi ruang 3D yang halus dan taktil tanpa menggunakan efek lapisan (layering) yang ekstrem.

1.1. Palet Warna (Color Palette)

Pemilihan warna pada desain Neumorphism sangat krusial karena efek pencahayaan bergantung pada kontras yang rendah.

Background (Latar Belakang Utama): #E0E5EC (Off-white/Light Grayish Blue) - Warna dasar tempat semua elemen diletakkan.

Shadow Light (Sorotan/Highlight): #FFFFFF - Digunakan untuk menciptakan efek cahaya yang datang dari sudut (biasanya kiri atas).

Shadow Dark (Bayangan Dalam): #A3B1C6 - Digunakan untuk menciptakan efek bayangan jatuh atau kedalaman (biasanya di kanan bawah).

Primary Brand Color (Hijau Plantify): #00D26A - Digunakan untuk aksen, ikon, teks penekanan, dan indikator positif/aktif (seperti progress bar).

Text Primary (Teks Gelap): #314051 - Digunakan untuk judul dan nilai data utama agar mudah dibaca (kontras tinggi).

Text Secondary (Teks Terang/Muted): #93A1B2 - Digunakan untuk label, satuan, dan informasi sekunder (kontras rendah).

Alert/Status Color: #EF4444 (Merah Tailwind) - Digunakan untuk indikator status kritis (misalnya, status "Offline").

1.2. Implementasi Bayangan (Shadows)

Efek Neumorphism dicapai melalui manipulasi bayangan box-shadow. Proyek ini mendefinisikan kelas kustom berikut:

Outset Shadow (Menonjol): shadow-[8px_8px_16px_#a3b1c6,-8px_-8px_16px_#ffffff]

Digunakan untuk wadah utama (kartu), tombol interaktif, dan elemen luar.

Inset Shadow (Mencekung): shadow-[inset_6px_6px_10px_#a3b1c6,inset_-6px_-6px_10px_#ffffff]

Digunakan untuk background ikon, tempat nilai sensor, dan jalur lintasan progress bar (track).

Small Outset (Tonjolan Kecil): shadow-[4px_4px_8px_#a3b1c6,-4px_-4px_8px_#ffffff]

Digunakan untuk elemen kecil yang memerlukan efek 3D halus, seperti wadah label pada grafik.

1.3. Tipografi (Typography)

Font Utama: Font sans-serif standar sistem (atau bisa menggunakan font geometris seperti Inter, Poppins, atau Montserrat jika diintegrasikan lebih lanjut).

Hierarki Visual:

Display/Headers: Dibuat tebal (font-black, font-extrabold) dengan pelacakan huruf yang disesuaikan (tracking-widest atau tracking-tight).

Data/Nilai (Value): Ukuran teks besar (misalnya text-3xl) dengan kontras tinggi.

Label/Unit: Ukuran kecil (text-sm, text-xs) dengan warna sekunder (muted).

📱 2. Arsitektur Antarmuka & Tata Letak (Layout)

Dasbor dibangun dengan pendekatan responsif "Mobile-First" yang disesuaikan untuk layar penuh (Web Dashboard).

2.1. Struktur Grid Responsif

Desktop/Tablet Large (Layar Lebar):

Layout menggunakan CSS Grid (grid-cols-12).

Kolom Kiri (Status Utama): Mengambil ruang 3-4 kolom. Menampilkan status vital tanaman (logo, status GROWING, indikator koneksi).

Kolom Kanan (Data Detail): Mengambil ruang 8-9 kolom yang tersisa. Berisi panel-panel detail (Sensor, Level Nutrisi).

Mobile/Tablet Small (Layar Sempit):

Layout Grid direduksi menjadi kolom tunggal (grid-cols-1).

Elemen-elemen bertumpuk secara vertikal (Header -> Status Utama -> Panel Sensor -> Panel Nutrisi).

2.2. Komponen Utama

Global Header:

Memuat Branding/Logo (kiri).

Memuat kontrol navigasi cepat (Notifikasi, Pengaturan) di sebelah kanan. Menu hamburger muncul hanya pada tampilan mobile.

Status Card (Left Panel):

Area fokus utama yang menunjukkan status global (misalnya, animasi/ikon statis "GROWING" dan indikator konektivitas "Offline/Online").

Realtime Sensors Panel (Top Right):

Menampilkan matriks 4 data sensor (Suhu, pH, TDS/ppm, Intensitas Cahaya/Lux).

Setiap data direpresentasikan dalam wadah berdesain inset (mencekung) untuk mensimulasikan "layar pembacaan".

Menyertakan stempel waktu "Last Update".

Nutrition & Liquid Levels Panel (Bottom Right):

Representasi visual untuk persentase cairan menggunakan Vertical Progress Bar.

Track (jalur latar) menggunakan bayangan inset, sedangkan Fill (isi level) menggunakan warna solid rata. Warna dibedakan berdasarkan jenis cairan (misalnya, air standar vs. nutrisi A/B).

Terdapat label di bawah setiap bar menggunakan desain tombol (outset kecil).

🛠️ 3. Pilihan Teknologi (Tech Stack)

Proyek ini dibangun menggunakan susunan teknologi modern yang umum untuk pengembangan frontend:

3.1. Core Framework

React (v18+): Digunakan sebagai pustaka UI utama berbasis komponen. Dipilih karena kemudahan pengelolaan state (jika ditambahkan nanti) dan pendekatan deklaratifnya.

3.2. Styling & Layout

Tailwind CSS (v3+): Kerangka kerja CSS utility-first.

Alasan Penggunaan: Memungkinkan perakitan desain Neumorphism secara cepat melalui kelas utilitas arbitrer (shadow-[...], nilai warna hex khusus). Ini menghindari penulisan file CSS terpisah yang panjang.

3.3. Ikonografi

Lucide-React: Pustaka ikon open-source.

Alasan Penggunaan: Ikon berbasis SVG yang bersih, dapat diubah ukurannya secara dinamis, dan warna struk (stroke) dapat disesuaikan langsung melalui properti komponen, sehingga mudah dicocokkan dengan warna "Hijau Plantify" (#00D26A).

Custom SVG: Digunakan khusus untuk Logo Utama agar memiliki identitas yang unik (ikon dua daun).

3.4. Konvensi Kode

Seluruh view saat ini dibundel dalam satu file utama (App.jsx atau serupa) untuk demonstrasi, namun pada tahap produksi (production), setiap panel dan elemen UI (seperti SensorCard, LiquidBar, Header) sebaiknya dipisahkan menjadi komponen yang berdiri sendiri (modular).

Dokumen ini dibuat secara otomatis untuk referensi desain Plantify Dashboard.