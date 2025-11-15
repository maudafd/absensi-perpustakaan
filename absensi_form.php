<!-- components/absensi_form.php -->
<div class="content" id="absensiSection">
    <form action="proses_absensi.php" method="post" class="grid gap-6 bg-white p-6 rounded-lg shadow-md border border-sage-100">
        <div class="bg-sage-300 rounded-lg overflow-hidden shadow mb-8 flex flex-col md:flex-row">
            <a class="nav-item"> Form Absensi Pengunjung</a>
        </div> 
        <div>
            <label for="nama" class="block mb-2 text-sm font-medium text-gray-700">Nama Lengkap</label>
            <input type="text" id="nama" name="nama" required
                placeholder="Masukkan nama lengkap anda"
                class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-sage-400" />
        </div>
        <div>
            <label for="nim" class="block mb-2 text-sm font-medium text-gray-700">NIM</label>
            <input type="text" id="nim" name="nim" required
                placeholder="Masukkan NIM"
                class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-sage-400" />
        </div>
        <div>
            <label for="jurusan" class="block mb-2 text-sm font-medium text-gray-700">Jurusan</label>
            <select id="jurusan" name="jurusan" required
                class="w-full px-4 py-2 border rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-sage-400">
                <option value="">Pilih Jurusan</option>
                <option value="Teknologi Informasi">Teknologi Informasi</option>
                <option value="Teknik Komputer Kontrol">Teknik Komputer Kontrol</option>
                <option value="Teknik Listrik">Teknik Listrik</option>
                <option value="Teknologi Rekayasa Perangkat Lunak">Teknologi Rekayasa Perangkat Lunak</option>
                <option value="Teknologi Rekayasa Otomotif">Teknologi Rekayasa Otomotif</option>
                <option value="Teknologi Rekayasa Otomasi">Teknologi Rekayasa Otomasi</option>
                <option value="Teknologi Rekayasa Elektronika">Teknologi Rekayasa Elektronika</option>
                <option value="Perkeretaapian">Perkeretaapian</option>
                <option value="Akuntansi">Akuntansi</option>
                <option value="Administrasi Bisnis">Administrasi Bisnis</option>
                <option value="Bahasa Inggris">Bahasa Inggris</option>
                <option value="Akuntansi Perpajakan">Akuntansi Perpajakan</option>
                <option value="Pemasaran Digital">Pemasaran Digital</option>
                <option value="Bahasa Inggris Untuk Komunikasi Bisnis Dan Profesional">Bahasa Inggris Untuk Komunikasi Bisnis Dan Profesional</option>
                <option value="Akuntansi Sektor Publik">Akuntansi Sektor Publik</option>
            </select>
        </div>
        <div>
            <label for="keperluan" class="block mb-2 text-sm font-medium text-gray-700">Keperluan</label>
            <select id="keperluan" name="keperluan" required
                class="w-full px-4 py-2 border rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-sage-400">
                <option value="">Pilih Keperluan</option>
                <option value="Baca Buku">Baca Buku</option>
                <option value="Pinjam Buku">Pinjam Buku</option>
                <option value="Kembalikan Buku">Kembalikan Buku</option>
                <option value="Belajar Kelompok">Belajar Kelompok</option>
                <option value="Lainnya">Lainnya</option>
            </select>
        </div>
        <div class="text-center">
            <button type="submit"
                class="bg-sage-400 hover:bg-sage-500 text-white font-medium px-6 py-3 rounded-md transition duration-200">
                Kirim Absensi
            </button>
        </div>
    </form>
</div>
