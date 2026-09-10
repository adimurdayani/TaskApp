<flux:modal name="tambah" class="md:w-[600px]">
    <form wire:submit="save" class="space-y-6">

        {{-- Header --}}
        <div>
            <flux:heading size="lg">
                Tambah Akun Pengguna
            </flux:heading>

            <flux:text class="mt-2">
                Tambahkan akun pengguna baru dengan melengkapi informasi nama,
                email, password, dan hak akses pengguna.
            </flux:text>
        </div>

        {{-- Nama --}}
        <flux:input wire:model="name" label="Nama Lengkap" placeholder="Contoh: Adi Murdayani"
            description="Masukkan nama lengkap pengguna yang akan ditampilkan di dalam aplikasi." required />

        {{-- Email --}}
        <flux:input wire:model="email" type="email" label="Alamat Email" placeholder="Contoh: adi@example.com"
            description="Gunakan alamat email yang aktif dan belum digunakan oleh akun lain." required />

        {{-- Password --}}
        <flux:input wire:model="password" type="password" label="Password" placeholder="Masukkan password"
            description="Password minimal 8 karakter. Gunakan kombinasi yang sulit ditebak." viewable required />

        {{-- Role --}}
        <flux:radio.group wire:model="role" label="Hak Akses"
            description="Tentukan hak akses pengguna di dalam aplikasi.">
            @foreach (\App\Enum\Role::cases() as $role)
                <flux:radio value="{{ $role->value }}" label="{{ $role->label() }}" />
            @endforeach
        </flux:radio.group>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 pt-2">

            <flux:modal.close>
                <flux:button type="button" variant="ghost">
                    Batal
                </flux:button>
            </flux:modal.close>

            <flux:button type="submit" variant="primary" color="blue">
                Simpan Pengguna
            </flux:button>

        </div>

    </form>
</flux:modal>

<flux:modal name="edit" class="md:w-[600px]">
    <form wire:submit="save" class="space-y-6">

        {{-- Header --}}
        <div>
            <flux:heading size="lg">
                Edit Akun Pengguna
            </flux:heading>

            <flux:text class="mt-2">
                Perbarui informasi akun pengguna. Password bersifat opsional
                dan hanya perlu diisi jika ingin mengganti password pengguna.
            </flux:text>
        </div>

        {{-- Nama --}}
        <flux:input wire:model="name" label="Nama Lengkap" placeholder="Contoh: Adi Murdayani"
            description="Masukkan nama lengkap pengguna yang akan ditampilkan di dalam aplikasi." required />

        {{-- Email --}}
        <flux:input wire:model="email" type="email" label="Alamat Email" placeholder="Contoh: adi@example.com"
            description="Gunakan alamat email yang aktif dan belum digunakan oleh akun lain." required />

        {{-- Password Baru --}}
        <flux:input wire:model="password" type="password" label="Password Baru"
            placeholder="Kosongkan jika tidak ingin mengubah password"
            description="Isi hanya jika ingin mengganti password. Password lama akan tetap digunakan jika kolom ini dikosongkan."
            viewable />

        {{-- Role --}}
        <flux:radio.group wire:model="role" label="Hak Akses"
            description="Tentukan hak akses pengguna di dalam aplikasi.">
            @foreach (\App\Enum\Role::cases() as $role)
                <flux:radio value="{{ $role->value }}" label="{{ $role->label() }}" />
            @endforeach
        </flux:radio.group>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 pt-2">

            <flux:modal.close>
                <flux:button type="button" variant="ghost">
                    Batal
                </flux:button>
            </flux:modal.close>

            <flux:button type="submit" variant="primary" color="blue">
                Simpan Perubahan
            </flux:button>

        </div>

    </form>
</flux:modal>
