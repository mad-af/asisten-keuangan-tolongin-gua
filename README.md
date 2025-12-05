<p align="center"><img src="public/assets/aktg/logo.webp" width="250" alt="Project Logo"></p>

<p align="center">
  <span>
    <img src="https://img.shields.io/badge/coverage-110%25-brightgreen" alt="imphnen (Organizer)">
  </span>
  <a href="https://www.facebook.com/groups/1032515944638255">
    <img src="https://img.shields.io/badge/imphnen-Organizer-blueviolet" alt="imphnen (Organizer)">
  </a>
  <a href="https://kolosal.ai">
    <img src="https://img.shields.io/badge/kolosal.ai-AI%20Tool-0EA5E9" alt="KOLOSAL.AI (AI Tool)">
  </a>
</p>

<p align="center">
  <a href="https://aktg.emdefstudio.com">Demo Aplikasi Asisten Keuangan Tolongin Gua</a>
</p>

## Tentang

AKTG (Asisten Keuangan Tolongin Gua) adalah asisten pembukuan berbasis percakapan untuk pelaku usaha lokal — membantu pencatatan pemasukan/pengeluaran, ringkasan, dan insight singkat secara mudah. Banyak UMKM belum memiliki pembukuan memadai: ~74% tidak bisa membuat laporan keuangan formal ([Media Indonesia, 2025](https://epaper.mediaindonesia.com/detail/74-umkm-tidak-bisa-buat-laporan-keuangan)), ~80% masih mencatat manual ([OCBC Business Fitness Index 2023 via Emiten News](https://emitennews.com/news/80-pelaku-umkm-indonesia-masih-melakukan-pencatatan-keuangan-secara-manual)), dan hanya ±46% yang memisahkan keuangan bisnis/pribadi ([Neraca](https://www.neraca.co.id/article/204139/hanya-46-umkm-yang-pisahkan-keuangan-bisnis-dan-personal-riset-ocbc-business-fitness-index)).

Asistan ini berjalan dalam simulasi WhatsApp agar langsung menyasar kebiasaan pengguna dan memudahkan adopsi. Melalui chat, pengguna dapat mencatat pemasukan/pengeluaran, melihat ringkasan keuangan, dan mendapatkan insight singkat tanpa perlu memahami istilah akuntansi yang rumit.

## Fitur Utama

- Pencatatan transaksi
- Ringkasan harian/mingguan
- Mudah dipakai tanpa pengetahuan akuntansi

## Tech Stack

- 🛠️ Backend: Laravel 11 (PHP), Inertia.js
- 🎨 Frontend: React, DaisyUI, Tailwind CSS, Vite
- ⚙️ Tooling: Bun (install, dev, build)
- 🤖 Integrasi AI: Kolosal.AI

## Cara Menjalankan

1) Siapkan file `.env`

```bash
cp .env.example .env
```

- Opsi SQLite (paling cepat):
  - Set `DB_CONNECTION=sqlite`
  - Set `DB_DATABASE=database/database.sqlite`
  - Buat file database:
    ```bash
    mkdir -p database && touch database/database.sqlite
    ```
- Opsi MySQL/Postgres: sesuaikan `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.

2) Generate `APP_KEY`

```bash
php artisan key:generate
```

3) Install dan build frontend (Bun)

```bash
bun install
bun run build
```

4) Migrasi dan seeding database

```bash
php artisan migrate --seed
```

5) Jalankan aplikasi (pengembangan)

```bash
# Terminal 1: jalankan server Laravel
php artisan serve

# Terminal 2: jalankan Vite dev server
bun run dev
```

6) Produksi (opsional)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
``` 

## Tim

- 🧙‍♂️ Grand Strategist & Arcane AI Alchemist: [@mad-af](https://github.com/mad-af)
- ⚔️ Backend Knight of Laravel Keep: [@mftlhn](https://github.com/mftlhn)
- 🏹 Frontend Ranger of React Woods: [@appledark-corei9XH](https://github.com/appledark-corei9XH)


## Lisensi

Aplikasi "Asisten Keuangan Tolongin Gua" ini dirilis di bawah lisensi [MIT](https://opensource.org/licenses/MIT).
