<x-filament-panels::page>
    {{-- Gunakan komponen card dari Filament agar tampilan konsisten --}}
    <x-filament::card>
        <div class="prose max-w-none dark:prose-invert">

            <p class="lead">
                Aplikasi ini dirancang untuk membantu dalam memonitor dan mengevaluasi
                kepatuhan terhadap prinsip 12 Benar dalam pemberian obat. Tujuannya adalah untuk meningkatkan
                keselamatan pasien dan kualitas pelayanan farmasi.
            </p>

            <h3 class="mt-8">Fitur Utama</h3>
            <ul>
                <li>Monitoring 12 Prinsip Benar secara real-time.</li>
                <li>Dashboard analytics untuk melihat tren kepatuhan.</li>
                <li>Manajemen data pasien dan transaksi farmasi.</li>
                <li>Rekapitulasi data dalam bentuk chart yang informatif.</li>
            </ul>

            {{-- BAGIAN BARU DARI GAMBAR --}}
            <h3 class="mt-8">Prinsip 12 Benar yang Dimonitor</h3>
            <p>Berikut adalah rincian dari 12 prinsip kepatuhan yang menjadi fokus monitoring dalam aplikasi ini:</p>
            <ol>
                <li><strong>Benar Pasien:</strong> Pengecekan nama, tanggal lahir, dan nomor registrasi melalui gelang
                    pasien.</li>
                <li><strong>Benar Obat:</strong> Kesesuaian obat dengan resep (nama obat, label, dan resep) per
                    pemberian.</li>
                <li><strong>Benar Dosis:</strong> Kesesuaian dosis obat dengan resep (jumlah sesuai, potensi) per
                    pemberian.</li>
                <li><strong>Benar Cara Pemberian Obat:</strong> Kesesuaian dengan rute pemberian (Oral, IV, IM).</li>
                <li><strong>Benar Waktu:</strong> Sesuai dengan penjadwalan (Pagi, Siang, Sore, Malam).</li>
                <li><strong>Benar Dokumentasi:</strong> Kesesuaian dokumentasi pemberi (pasien, dosis, obat, waktu,
                    rute) dan siapa pemberi.</li>
                <li><strong>Benar Evaluasi:</strong> Monitoring efek samping, alergi, dan efek terapi.</li>
                <li><strong>Benar Pengkajian:</strong> Pengecekan suhu dan tensi.</li>
                <li><strong>Benar Reaksi dengan Obat Lain:</strong> Monitoring efek samping, alergi, dan efek terapi
                    akibat interaksi obat.</li>
                <li><strong>Benar Reaksi terhadap Makanan:</strong> Monitoring efek makanan terhadap obat.</li>
                <li><strong>Hak Klien untuk Menolak:</strong> Pencatatan persetujuan atau penolakan (Informed Consent).
                </li>
                <li><strong>Benar Pendidikan Kesehatan:</strong> Pemberian edukasi ke pasien dan keluarga.</li>
            </ol>

            <div class="pt-4 mt-10 text-sm text-gray-500 border-t">
                <p><strong>Versi Aplikasi:</strong> 1.0.0</p>
            </div>

        </div>
    </x-filament::card>
</x-filament-panels::page>
