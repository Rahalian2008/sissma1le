<nav aria-label="Navigasi Menu Data Master" class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-2 sm:p-2.5 shadow-xs mb-6 overflow-x-auto scrollbar-thin">
    <div class="flex items-center gap-1.5 sm:gap-2 min-w-max sm:min-w-0 sm:flex-wrap">
        <!-- 1. Identitas Sekolah -->
        <a href="{{ route('master.school_profile') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('master.school_profile') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>🏫</span>
            <span>Identitas Sekolah</span>
        </a>

        <!-- 2. Logo & KOP Surat -->
        <a href="{{ route('master.branding') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('master.branding') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>🎨</span>
            <span>Logo & KOP Surat</span>
        </a>

        <!-- 3. Data Kelas -->
        <a href="{{ route('master.classes') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('master.classes*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>🏛️</span>
            <span>Data Kelas</span>
        </a>

        <!-- 5. Data Siswa & RFID -->
        <a href="{{ route('master.students') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('master.students*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>👨‍🎓</span>
            <span>Data Siswa & RFID</span>
        </a>

        <!-- 6. Data Guru & PTK -->
        <a href="{{ route('master.teachers') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('master.teachers*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>👩‍🏫</span>
            <span>Data Guru & PTK</span>
        </a>

        <!-- 6b. Kelola Akun Pengguna -->
        <a href="{{ route('master.users') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('master.users*') || request()->routeIs('users.*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>🔐</span>
            <span>Akun Pengguna</span>
        </a>

        <!-- 7. Data Ekstrakurikuler -->
        <a href="{{ route('master.extracurriculars') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('master.extracurriculars*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>⚽</span>
            <span>Data Ekstrakurikuler</span>
        </a>

        <!-- 8. Tahun Pelajaran -->
        <a href="{{ route('master.academic_years') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('master.academic_years*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>📅</span>
            <span>Tahun Pelajaran</span>
        </a>

        <!-- 9. Hari Libur & Tanggal Merah -->
        <a href="{{ route('master.holidays') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('master.holidays*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>🗓️</span>
            <span>Hari Libur & Tanggal Merah</span>
        </a>

        <!-- 10. Pengaturan & Geofence -->
        <a href="{{ route('master.settings') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('master.settings*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>⚙️</span>
            <span>Pengaturan & Geofence</span>
        </a>

        <!-- 11. Master Butir Tatib -->
        <a href="{{ route('master.categories') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('master.categories*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>🏷️</span>
            <span>Master Butir Tatib</span>
        </a>

        <!-- 12. Mata Pelajaran & LMS -->
        <a href="{{ route('lms.master.subjects') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ request()->routeIs('lms.master.subjects*') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>📚</span>
            <span>Mata Pelajaran & LMS</span>
        </a>
    </div>
</nav>
