<flux:modal name="tambah" class="md:w-[600px]">
    <form wire:submit="save" class="space-y-6">

        {{-- Header --}}
        <div>
            <flux:heading size="lg">
                Tambah Kategori Tugas
            </flux:heading>

            <flux:text class="mt-2">
                Tambahkan kategori tugas baru dan lengkapi informasi kategori
                agar tugas dapat dikelompokkan dengan lebih mudah.
            </flux:text>
        </div>

        {{-- Nama Kategori --}}
        <flux:input wire:model="name" label="Nama Kategori" placeholder="Contoh: Tugas Harian"
            description="Masukkan nama kategori yang singkat, jelas, dan mudah dikenali." required />

        {{-- Deskripsi --}}
        <flux:textarea wire:model="description" label="Deskripsi"
            placeholder="Jelaskan fungsi atau ruang lingkup kategori tugas..."
            description="Opsional. Gunakan untuk menjelaskan jenis tugas yang termasuk dalam kategori ini."
            rows="4" />

        {{-- Status Kategori --}}
        <flux:radio.group wire:model="is_active" label="Status Kategori"
            description="Tentukan apakah kategori ini dapat dipilih saat membuat tugas.">
            @foreach (\App\Enum\StatusCategory::cases() as $status)
                <flux:radio value="{{ $status->isActive() ? 1 : 0 }}" label="{{ $status->label() }}" />
            @endforeach
        </flux:radio.group>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 pt-2">

            <flux:modal.close>
                <flux:button type="button" variant="ghost">
                    Batal
                </flux:button>
            </flux:modal.close>

            <flux:button type="submit" variant="primary" color="blue" wire:click="save">
                Simpan Kategori
            </flux:button>

        </div>

    </form>
</flux:modal>

<flux:modal name="edit" class="md:w-[600px]">
    <form wire:submit="save" class="space-y-6">

        {{-- Header --}}
        <div>
            <flux:heading size="lg">
                Tambah Kategori Tugas
            </flux:heading>

            <flux:text class="mt-2">
                Tambahkan kategori tugas baru dan lengkapi informasi kategori
                agar tugas dapat dikelompokkan dengan lebih mudah.
            </flux:text>
        </div>

        {{-- Nama Kategori --}}
        <flux:input wire:model="name" label="Nama Kategori" placeholder="Contoh: Tugas Harian"
            description="Masukkan nama kategori yang singkat, jelas, dan mudah dikenali." required />

        {{-- Deskripsi --}}
        <flux:textarea wire:model="description" label="Deskripsi"
            placeholder="Jelaskan fungsi atau ruang lingkup kategori tugas..."
            description="Opsional. Gunakan untuk menjelaskan jenis tugas yang termasuk dalam kategori ini."
            rows="4" />

        {{-- Status Kategori --}}
        <flux:radio.group wire:model="is_active" label="Status Kategori"
            description="Tentukan apakah kategori ini dapat dipilih saat membuat tugas.">
            @foreach (\App\Enum\StatusCategory::cases() as $status)
                <flux:radio value="{{ $status->isActive() ? '1' : '0' }}" label="{{ $status->label() }}" />
            @endforeach
        </flux:radio.group>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 pt-2">

            <flux:modal.close>
                <flux:button type="button" variant="ghost">
                    Batal
                </flux:button>
            </flux:modal.close>

            <flux:button type="submit" variant="primary" color="blue" wire:click='save'>
                Ubah Kategori
            </flux:button>

        </div>

    </form>
</flux:modal>

<flux:modal name="detail" class="!w-[90vw] !max-w-[800px]">

    @if ($selectedCategory)

        <div class="space-y-6">

            {{-- Header --}}
            <div class="flex items-start gap-4">

                <div
                    class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                    <flux:icon.folder class="size-6" />
                </div>

                <div class="min-w-0 flex-1">

                    <flux:heading size="lg">
                        Detail Kategori
                    </flux:heading>

                    <flux:text class="mt-1">
                        Informasi lengkap mengenai kategori tugas.
                    </flux:text>

                </div>

            </div>

            {{-- Informasi utama --}}
            <flux:callout icon="information-circle">

                <div class="space-y-5">

                    {{-- Nama --}}
                    <div>
                        <flux:callout.heading variant="subtle" class="text-xs font-medium uppercase tracking-wide">
                            Nama Kategori
                        </flux:callout.heading>

                        <flux:callout.text variant="strong" class="mt-1 text-base">
                            {{ $selectedCategory->name }}
                        </flux:callout.text>
                    </div>

                    {{-- Slug --}}
                    <div>
                        <flux:text variant="subtle" class="text-xs font-medium uppercase tracking-wide">
                            Slug
                        </flux:text>

                        <flux:text class="mt-1 font-mono text-sm">
                            {{ $selectedCategory->slug }}
                        </flux:text>
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <flux:text variant="subtle" class="text-xs font-medium uppercase tracking-wide">
                            Deskripsi
                        </flux:text>

                        @if ($selectedCategory->description)
                            <flux:text class="mt-1 leading-relaxed">
                                {{ $selectedCategory->description }}
                            </flux:text>
                        @else
                            <flux:text variant="subtle" class="mt-1 italic">
                                Tidak ada deskripsi.
                            </flux:text>
                        @endif
                    </div>

                </div>

            </flux:callout>

            {{-- Statistik --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                {{-- Status --}}
                <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">

                    <flux:text variant="subtle" class="text-xs font-medium uppercase tracking-wide">
                        Status
                    </flux:text>

                    <div class="mt-2">

                        @if ($selectedCategory->is_active)
                            <flux:badge color="green" icon="check-circle">
                                Aktif
                            </flux:badge>
                        @else
                            <flux:badge color="zinc" icon="x-circle">
                                Tidak Aktif
                            </flux:badge>
                        @endif

                    </div>

                </div>

                {{-- Jumlah tugas --}}
                <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">

                    <flux:text variant="subtle" class="text-xs font-medium uppercase tracking-wide">
                        Jumlah Tugas
                    </flux:text>

                    <flux:text variant="strong" class="mt-2 text-xl">
                        {{ number_format($selectedCategory->tasks_count) }}
                        <span class="text-sm font-normal text-zinc-500">
                            tugas
                        </span>
                    </flux:text>

                </div>

            </div>

            {{-- Metadata --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                <div>
                    <flux:text variant="subtle" class="text-xs font-medium uppercase tracking-wide">
                        Dibuat
                    </flux:text>

                    <flux:text class="mt-1 text-sm">
                        {{ $selectedCategory->created_at?->translatedFormat('d F Y, H:i') ?? '-' }}
                    </flux:text>
                </div>

                <div>
                    <flux:text variant="subtle" class="text-xs font-medium uppercase tracking-wide">
                        Terakhir Diperbarui
                    </flux:text>

                    <flux:text class="mt-1 text-sm">
                        {{ $selectedCategory->updated_at?->translatedFormat('d F Y, H:i') ?? '-' }}
                    </flux:text>
                </div>

            </div>

            <div class="flex">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button type="button" variant="ghost" wire:click="closeDetail">
                        Tutup
                    </flux:button>
                </flux:modal.close>

                <flux:button type="button" variant="primary" color="blue" icon="pencil"
                    wire:click="edit('{{ $selectedCategory->id }}')">
                    Edit Kategori
                </flux:button>
            </div>

        </div>

    @endif

</flux:modal>
