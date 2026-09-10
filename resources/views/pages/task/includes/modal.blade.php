<flux:modal name="tambah" class="md:w-[600px]">
    <form wire:submit="save" class="space-y-6">

        {{-- Header --}}
        <div>
            <flux:heading size="lg">
                Tambah Tugas
            </flux:heading>

            <flux:text class="mt-2">
                Tambahkan tugas baru dan lengkapi informasi tugas yang diperlukan.
            </flux:text>
        </div>

        {{-- Judul --}}
        <flux:input wire:model="title" label="Judul Tugas" placeholder="Contoh: Menyusun laporan kegiatan"
            description="Masukkan judul singkat dan jelas agar tugas mudah dikenali." required />

        {{-- Deskripsi --}}
        <flux:textarea wire:model="description" label="Deskripsi"
            placeholder="Jelaskan detail atau informasi tambahan mengenai tugas..."
            description="Opsional. Gunakan untuk menjelaskan ruang lingkup atau instruksi tugas." rows="4" />

        {{-- Kategori --}}
        <flux:select wire:model="category_id" label="Kategori" placeholder="Pilih kategori"
            description="Pilih kategori yang paling sesuai dengan tugas.">
            <flux:select.option>
                Pilih
            </flux:select.option>
            @foreach ($this->categories as $category)
                <flux:select.option value="{{ $category->id }}">
                    {{ $category->name }}
                </flux:select.option>
            @endforeach
        </flux:select>

        {{-- Priority & Status --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

            <flux:select wire:model="priority" label="Prioritas" placeholder="Pilih Prioritas"
                description="Tentukan tingkat kepentingan tugas." required>
                <flux:select.option>
                    Pilih
                </flux:select.option>
                @foreach (\App\Enum\Priority::cases() as $priority)
                    <flux:select.option value="{{ $priority->value }}">
                        {{ $priority->label() }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model="status" label="Status" placeholder="Pilih status"
                description="Tentukan kondisi tugas saat ini." required>
                <flux:select.option>
                    Pilih
                </flux:select.option>
                @foreach (\App\Enum\Status::cases() as $status)
                    <flux:select.option value="{{ $status->value }}">
                        {{ $status->label() }}
                    </flux:select.option>
                @endforeach
            </flux:select>

        </div>

        {{-- Tenggat Waktu --}}
        <flux:input wire:model="due_date" label="Tenggat Waktu" type="date"
            description="Tanggal maksimal tugas harus diselesaikan. Opsional." />

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 pt-2">

            <flux:modal.close>
                <flux:button type="button" variant="ghost">
                    Batal
                </flux:button>
            </flux:modal.close>

            <flux:button type="submit" variant="primary" color="blue" wire:loading.attr="disabled">
                <span wire:loading.remove>
                    Simpan Tugas
                </span>

                <span wire:loading>
                    Menyimpan...
                </span>
            </flux:button>

        </div>

    </form>
</flux:modal>

<flux:modal name="edit" class="md:w-[600px]">
    <form wire:submit="save" class="space-y-6">

        {{-- Header --}}
        <div>
            <flux:heading size="lg">
                Tambah Tugas
            </flux:heading>

            <flux:text class="mt-2">
                Tambahkan tugas baru dan lengkapi informasi tugas yang diperlukan.
            </flux:text>
        </div>

        {{-- Judul --}}
        <flux:input wire:model="title" label="Judul Tugas" placeholder="Contoh: Menyusun laporan kegiatan"
            description="Masukkan judul singkat dan jelas agar tugas mudah dikenali." required />

        {{-- Deskripsi --}}
        <flux:textarea wire:model="description" label="Deskripsi"
            placeholder="Jelaskan detail atau informasi tambahan mengenai tugas..."
            description="Opsional. Gunakan untuk menjelaskan ruang lingkup atau instruksi tugas." rows="4" />

        {{-- Kategori --}}
        <flux:select wire:model="category_id" label="Kategori" placeholder="Pilih kategori"
            description="Pilih kategori yang paling sesuai dengan tugas.">
            <flux:select.option>
                Pilih
            </flux:select.option>
            @foreach ($this->categories as $category)
                <flux:select.option value="{{ $category->id }}">
                    {{ $category->name }}
                </flux:select.option>
            @endforeach
        </flux:select>

        {{-- Priority & Status --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

            <flux:select wire:model="priority" label="Prioritas" placeholder="Pilih Prioritas"
                description="Tentukan tingkat kepentingan tugas." required>
                <flux:select.option>
                    Pilih
                </flux:select.option>
                @foreach (\App\Enum\Priority::cases() as $priority)
                    <flux:select.option value="{{ $priority->value }}">
                        {{ $priority->label() }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model="status" label="Status" placeholder="Pilih status"
                description="Tentukan kondisi tugas saat ini." required>
                <flux:select.option>
                    Pilih
                </flux:select.option>
                @foreach (\App\Enum\Status::cases() as $status)
                    <flux:select.option value="{{ $status->value }}">
                        {{ $status->label() }}
                    </flux:select.option>
                @endforeach
            </flux:select>

        </div>

        {{-- Tenggat Waktu --}}
        <flux:input wire:model="due_date" label="Tenggat Waktu" type="date"
            description="Tanggal maksimal tugas harus diselesaikan. Opsional." />

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 pt-2">

            <flux:modal.close>
                <flux:button type="button" variant="ghost">
                    Batal
                </flux:button>
            </flux:modal.close>

            <flux:button type="submit" variant="primary" color="blue" wire:click='save'>
                Simpan Tugas
            </flux:button>

        </div>

    </form>
</flux:modal>

<flux:modal name="detail" class="md:w-[700px]">
    @if ($selectedTask)
        <div class="space-y-6">

            {{-- Header --}}
            <div>
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <flux:heading size="lg">
                            Detail Tugas
                        </flux:heading>

                        <flux:text class="mt-1">
                            Informasi lengkap mengenai tugas yang dipilih.
                        </flux:text>
                    </div>

                    <flux:badge
                        :color="$selectedTask->status->value === 'selesai'
                                                                                                                                                                                                                                                                                                                                                    ? 'green'
                                                                                                                                                                                                                                                                                                                                                    : ($selectedTask->status->value === 'dikerjakan'
                                                                                                                                                                                                                                                                                                                                                        ? 'blue'
                                                                                                                                                                                                                                                                                                                                                        : 'zinc')">
                        {{ $selectedTask->status->label() }}
                    </flux:badge>
                </div>
            </div>

            {{-- Judul --}}
            <div class="space-y-1">
                <flux:text variant="subtle" class="text-xs font-medium uppercase tracking-wide">
                    Judul Tugas
                </flux:text>

                <flux:heading size="md">
                    {{ $selectedTask->title }}
                </flux:heading>
            </div>

            {{-- Deskripsi --}}
            <div class="space-y-1">
                <flux:text variant="subtle" class="text-xs font-medium uppercase tracking-wide">
                    Deskripsi
                </flux:text>

                @if ($selectedTask->description)
                    <flux:text class="whitespace-pre-line">
                        {{ $selectedTask->description }}
                    </flux:text>
                @else
                    <flux:text variant="subtle">
                        Tidak ada deskripsi.
                    </flux:text>
                @endif
            </div>

            {{-- Informasi utama --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                {{-- Kategori --}}
                <div class="space-y-1">
                    <flux:text variant="subtle" class="text-xs font-medium uppercase tracking-wide">
                        Kategori
                    </flux:text>

                    @if ($selectedTask->category)
                        <flux:text>
                            {{ $selectedTask->category->name }}
                        </flux:text>
                    @else
                        <flux:text variant="subtle">
                            Tanpa kategori
                        </flux:text>
                    @endif
                </div>

                {{-- Prioritas --}}
                <div class="space-y-1">
                    <flux:text variant="subtle" class="text-xs font-medium uppercase tracking-wide">
                        Prioritas
                    </flux:text>

                    <flux:badge
                        :color="$selectedTask->priority->value === 'tinggi'? 'red': ($selectedTask->priority->value === 'sedang'? 'amber': 'green')">
                        {{ $selectedTask->priority->label() }}
                    </flux:badge>
                </div>

                {{-- Status --}}
                <div class="space-y-1">
                    <flux:text variant="subtle" class="text-xs font-medium uppercase tracking-wide">
                        Status
                    </flux:text>

                    <flux:badge
                        :color="$selectedTask->status->value === 'selesai' ? 'green' : ($selectedTask->status->value === 'dikerjakan' ? 'blue': 'zinc')">
                        {{ $selectedTask->status->label() }}
                    </flux:badge>
                </div>

                {{-- Tenggat --}}
                <div class="space-y-1">
                    <flux:text variant="subtle" class="text-xs font-medium uppercase tracking-wide">
                        Tenggat Waktu
                    </flux:text>

                    @if ($selectedTask->due_date)
                        <flux:text>
                            {{ $selectedTask->due_date->translatedFormat('d F Y') }}
                        </flux:text>
                    @else
                        <flux:text variant="subtle">
                            Tidak ada tenggat
                        </flux:text>
                    @endif
                </div>

            </div>

            {{-- Pemilik tugas --}}
            @if (auth()->user()->role === 'admin' && $selectedTask->user)
                <flux:callout icon="information-circle">
                    <flux:callout.heading>
                        Pemilik Tugas
                    </flux:callout.heading>

                    <div class="mt-1">
                        <flux:text variant="strong">
                            {{ $selectedTask->user->name }}
                        </flux:text>

                        @if ($selectedTask->user->email)
                            <flux:text variant="subtle" class="text-sm">
                                {{ $selectedTask->user->email }}
                            </flux:text>
                        @endif
                    </div>
                </flux:callout>
            @endif

            <flux:separator text="Metadata" />

            {{-- Metadata --}}
            <div class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">

                <div>
                    <flux:text variant="subtle">
                        Dibuat
                    </flux:text>

                    <flux:text>
                        {{ $selectedTask->created_at?->translatedFormat('d F Y, H:i') }}
                    </flux:text>
                </div>

                <div>
                    <flux:text variant="subtle">
                        Terakhir diperbarui
                    </flux:text>

                    <flux:text>
                        {{ $selectedTask->updated_at?->translatedFormat('d F Y, H:i') }}
                    </flux:text>
                </div>

            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:modal.close>
                    <flux:button type="button" variant="ghost">
                        Tutup
                    </flux:button>
                </flux:modal.close>

                @if ((string) auth()->id() === (string) $selectedTask->user_id || auth()->user()->role === 'admin')
                    <flux:button variant="primary" color="blue" icon="pencil"
                        wire:click="edit('{{ $selectedTask->id }}')">
                        Edit Tugas
                    </flux:button>
                @endif
            </div>

        </div>
    @endif
</flux:modal>
