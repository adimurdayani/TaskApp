<?php

use App\Enum\Priority;
use App\Enum\Status;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Tugas')] class extends Component {
    use \Livewire\WithPagination;
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    public ?string $taskId = null;
    public ?string $category_id = null;
    public string $title;
    public ?string $description;
    public string $priority = 'rendah';
    public string $status = 'belum_dimulai';
    public ?string $due_date = null;

    public ?string $successMessage = null;
    public ?Task $selectedTask = null;

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    #[\Livewire\Attributes\Computed]
    public function tasks()
    {
        return Task::query()
            ->when(auth()->user()->role?->value !== 'admin', fn($query) => $query->where('user_id', auth()->id()))
            ->tap(fn($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate(5);
    }

    #[\Livewire\Attributes\Computed]
    public function categories()
    {
        return Category::orderBy('created_at', 'desc')->get();
    }

    public function edit(string $id): void
    {
        abort_unless(auth()->check(), 403);

        $task = Task::query()->with('category')->findOrFail($id);

        if (!auth()->user()->role?->value === 'user') {
            abort_unless((string) $task->user_id === (string) auth()->id(), 403);
        }

        $this->taskId = $task->id;
        $this->title = $task->title;
        $this->description = $task->description;
        $this->category_id = $task->category_id;
        $this->priority = $task->priority instanceof Priority ? $task->priority->value : $task->priority;
        $this->status = $task->status instanceof Status ? $task->status->value : $task->status;
        $this->due_date = $task->due_date?->format('Y-m-d');
        $this->resetValidation();
        $this->modal('edit')->show();
    }

    public function save(): void
    {
        $this->validate(
            [
                'title' => ['required', 'string', 'min:3', 'max:255'],

                'description' => ['nullable', 'string', 'max:5000'],

                'category_id' => ['required', 'string', Rule::exists('categories', 'id')->where('is_active', true)],

                'priority' => ['required', Rule::enum(Priority::class)],

                'status' => ['required', Rule::enum(Status::class)],

                'due_date' => ['nullable', 'date', 'after_or_equal:today'],
            ],
            [
                'title.required' => 'Judul tugas wajib diisi.',
                'title.min' => 'Judul tugas minimal 3 karakter.',
                'title.max' => 'Judul tugas maksimal 255 karakter.',

                'description.max' => 'Deskripsi maksimal 5.000 karakter.',

                'category_id.required' => 'Kategori tugas wajib dipilih.',
                'category_id.exists' => 'Kategori yang dipilih tidak valid atau sudah tidak aktif.',

                'priority.required' => 'Prioritas wajib dipilih.',
                'priority.enum' => 'Prioritas yang dipilih tidak valid.',

                'status.required' => 'Status tugas wajib dipilih.',
                'status.enum' => 'Status tugas yang dipilih tidak valid.',

                'due_date.date' => 'Format tanggal tenggat tidak valid.',
                'due_date.after_or_equal' => 'Tenggat waktu tidak boleh sebelum hari ini.',
            ],
        );

        $userId = auth()->id();

        abort_unless($userId, 403);

        $categoryExists = Category::query()->whereKey($this->category_id)->where('is_active', true)->exists();

        if (!$categoryExists) {
            $this->addError('category_id', 'Kategori tidak tersedia atau sudah dinonaktifkan.');

            return;
        }

        $data = [
            'category_id' => $this->category_id,
            'title' => trim($this->title),
            'description' => filled($this->description) ? trim($this->description) : null,
            'priority' => $this->priority,
            'status' => $this->status,
            'due_date' => filled($this->due_date) ? $this->due_date : null,
        ];

        if ($this->taskId) {
            $task = Task::query()->findOrFail($this->taskId);

            if (auth()->user()->role?->value !== 'admin' && (string) $task->user_id !== (string) $userId) {
                abort(403, 'Anda tidak memiliki akses untuk mengubah tugas ini.');
            }

            $task->update($data);

            $this->successMessage = 'Tugas berhasil diperbarui.';

            $this->reset(['taskId', 'title', 'description', 'category_id', 'due_date']);

            $this->priority = Priority::RENDAH->value;
            $this->status = Status::BELUM_DIMULAI->value;

            $this->resetValidation();

            $this->modal('edit')->close();

            return;
        }

        Task::create([
            'user_id' => $userId,
            ...$data,
        ]);

        $this->successMessage = 'Tugas berhasil ditambahkan.';

        $this->reset(['title', 'description', 'category_id', 'due_date']);

        $this->priority = Priority::RENDAH->value;
        $this->status = Status::BELUM_DIMULAI->value;

        $this->resetValidation();

        $this->modal('tambah')->close();
    }

    public function delete(string $id): void
    {
        abort_unless(auth()->check(), 403);

        $task = Task::query()->findOrFail($id);

        if (auth()->user()->role?->value !== 'admin' && (string) $task->user_id !== (string) auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus tugas ini.');
        }

        $task->delete();

        $this->successMessage = 'Tugas berhasil dihapus.';
    }

    public function showDetail(string $id): void
    {
        abort_unless(auth()->check(), 403);

        $task = Task::query()
            ->with(['category', 'user'])
            ->findOrFail($id);

        if (auth()->user()->role?->value !== 'admin' && (string) $task->user_id !== (string) auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk melihat tugas ini.');
        }

        $this->selectedTask = $task;

        $this->modal('detail')->show();
    }
};
?>

<div>
    @include('partials.heading', ['title' => 'Daftar Tugas', 'subtitle' => 'Kelola data tugas'])
    <flux:card class="space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <flux:heading size="lg">Tabel data tugas</flux:heading>
                <flux:text class="mt-1 text-sm">
                    Kelola dan pantau seluruh tugas Anda dengan mudah.
                    Anda dapat menambahkan tugas baru, memperbarui informasi tugas,
                    mengubah status pengerjaan, serta mengelola tugas sesuai prioritas dan kategori.
                </flux:text>
            </div>

            <flux:modal.trigger name="tambah">
                <flux:button variant="primary" color="blue" icon="plus">Tambah Tugas</flux:button>
            </flux:modal.trigger>

        </div>

        @if ($successMessage)
        <div x-data x-init="setTimeout(() => $wire.set('successMessage', null), 4000)" class="mb-6">
            <flux:callout variant="success" icon="check-circle" heading="Berhasil" class="mb-6">
                {{ $successMessage }}
            </flux:callout>
        </div>
        @endif

        <flux:table :paginate="$this->tasks" class="w-full">
            <flux:table.columns>
                <flux:table.column class="w-[34%] min-w-70">Judul Tugas</flux:table.column>
                <flux:table.column class="w-[16%]">Kategori</flux:table.column>
                <flux:table.column class="w-[10%]">Priority</flux:table.column>
                <flux:table.column class="w-[12%]">Status</flux:table.column>
                <flux:table.column class="w-[12%] whitespace-nowrap" sortable :sorted="$sortBy === 'status'"
                    :direction="$sortDirection" wire:click="sort('status')">Tenggat Waktu</flux:table.column>
                <flux:table.column class="w-[10%] whitespace-nowrap" sortable :sorted="$sortBy === 'created_at'"
                    :direction="$sortDirection" wire:click="sort('created_at')">Dibuat</flux:table.column>
                <flux:table.column class="w-[7%] text-right">Aksi</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->tasks as $task)
                <flux:table.row :key="$task->id">
                    <flux:table.cell class="max-w-0">
                        <div class="min-w-0 max-w-[320px]">
                            <flux:text class="block truncate" variant="strong">
                                {{ $task->title }}
                            </flux:text>

                            @if ($task->description)
                            <flux:text variant="subtle" class="truncate" title="{{ $task->description }}">
                                {{ $task->description }}
                            </flux:text>
                            @else
                            <flux:text variant="subtle">
                                Tidak ada deskripsi
                            </flux:text>
                            @endif
                        </div>
                    </flux:table.cell>
                    <flux:table.cell class="min-w-0">
                        @if ($task->category)
                        <div class="min-w-0 max-w-full" title="{{ $task->category->name }}">
                            <flux:text class="block truncate text-sm">
                                {{ $task->category->name }}
                            </flux:text>
                        </div>
                        @else
                        <flux:text variant="subtle" class="block truncate text-sm">
                            Tanpa kategori
                        </flux:text>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">
                        @switch($task->priority->value)
                        @case('rendah')
                        <flux:badge color="green">
                            Rendah
                        </flux:badge>
                        @break

                        @case('sedang')
                        <flux:badge color="yellow">
                            Sedang
                        </flux:badge>
                        @break

                        @case('tinggi')
                        <flux:badge color="red">
                            Tinggi
                        </flux:badge>
                        @break
                        @endswitch
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">
                        @switch($task->status->value)
                        @case('belum_dimulai')
                        <flux:badge color="zinc">
                            Belum Dimulai
                        </flux:badge>
                        @break

                        @case('dikerjakan')
                        <flux:badge color="blue">
                            Dikerjakan
                        </flux:badge>
                        @break

                        @case('selesai')
                        <flux:badge color="green">
                            Selesai
                        </flux:badge>
                        @break
                        @endswitch
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">
                        @if ($task->due_date)
                        <div class="whitespace-nowrap">
                            {{ $task->due_date->format('d M Y') }}
                        </div>
                        @else
                        <span class="text-sm text-zinc-400">
                            Tidak ada
                        </span>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell class="whitespace-nowrap">
                        <div class="whitespace-nowrap text-sm">
                            {{ $task->created_at->format('d M Y') }}
                        </div>
                    </flux:table.cell>

                    <flux:table.cell class="whitespace-nowrap">
                        <flux:dropdown>
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />

                            <flux:menu>
                                <flux:menu.item wire:click="showDetail('{{ $task->id }}')" icon="eye">
                                    Lihat
                                </flux:menu.item>

                                @if ((string) auth()->id() === (string) $task->user_id || auth()->user()->role === 'admin')
                                <flux:menu.item icon="pencil" wire:click="edit('{{ $task->id }}')">
                                    Edit
                                </flux:menu.item>

                                <flux:menu.separator />

                                <flux:menu.item wire:click="delete('{{ $task->id }}')"
                                    wire:confirm="Apakah Anda yakin ingin menghapus tugas ini?" variant="danger"
                                    icon="trash">
                                    Hapus
                                </flux:menu.item>
                                @endif
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
    @include('pages.task.includes.modal')
</div>
