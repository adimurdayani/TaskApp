<?php

use App\Models\Category;
use App\Models\Task;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Kategori Tugas')] class extends Component {
    use \Livewire\WithPagination;
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    public ?string $categoryId = null;
    public string $name;
    public ?string $description = null;
    public string $is_active = '1';

    public ?string $successMessage = '';
    public ?string $errorMessage = '';

    public ?Category $selectedCategory = null;

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
    public function categories()
    {
        return Category::query()->tap(fn($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)->paginate(5);
    }

    public function edit(string $id)
    {
        abort_unless(auth()->check(), 403);
        $category = Category::findOrFail($id);
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->description = $category->description;
        $this->is_active = $category->is_active ? '1' : '0';
        $this->resetValidation();
        $this->modal('edit')->show();
        $this->modal('detail')->close();
    }

    public function save(): void
    {
        $this->validate(
            [
                'name' => ['required', 'string', 'min:3', 'max:255', Rule::unique('categories', 'name')->ignore($this->categoryId)],
                'description' => ['nullable', 'string', 'max:1000'],
                'is_active' => ['boolean'],
            ],
            [
                'name.required' => 'Nama kategori wajib diisi.',
                'name.min' => 'Nama kategori minimal 3 karakter.',
                'name.max' => 'Nama kategori maksimal 255 karakter.',
                'name.unique' => 'Nama kategori sudah digunakan.',
                'description.max' => 'Deskripsi maksimal 1.000 karakter.',
                'is_active.boolean' => 'Status kategori tidak valid.',
            ],
        );

        $data = [
            'name' => trim($this->name),
            'description' => filled($this->description) ? trim($this->description) : null,
            'is_active' => $this->is_active,
        ];

        if ($this->categoryId) {
            $category = Category::query()->findOrFail($this->categoryId);

            $category->update($data);

            $this->successMessage = 'Kategori berhasil diperbarui.';

            $this->reset(['categoryId', 'name', 'description']);

            $this->is_active = true;

            $this->resetValidation();

            $this->modal('edit')->close();
            return;
        }

        Category::create($data);

        $this->successMessage = 'Kategori berhasil ditambahkan.';

        $this->reset(['name', 'description']);

        $this->is_active = true;

        $this->resetValidation();

        $this->modal('tambah')->close();
    }

    public function delete(string $id): void
    {
        abort_unless(auth()->check(), 403);

        $category = Category::query()
            ->findOrFail($id);

        $hasTasks = Task::query()
            ->where('category_id', $category->id)
            ->exists();

        if ($hasTasks) {
            $this->errorMessage = 'Kategori tidak dapat dihapus karena masih digunakan oleh satu atau lebih tugas.';
            return;
        }

        $category->delete();

        $this->successMessage = 'Kategori berhasil dihapus.';
    }

    public function showDetail(string $id): void
    {
        abort_unless(auth()->check(), 403);

        $category = Category::query()->findOrFail($id);

        $this->selectedCategory = $category;

        $this->modal('detail')->show();
    }

    public function closeDetail(): void
    {
        $this->selectedCategory = null;

        $this->modal('detail')->close();
    }
};
?>

<div>
    @include('partials.heading', [
    'title' => 'Daftar Kategori Tugas',
    'subtitle' => 'Kelola data kategori tugas',
    ])

    <flux:card class="space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <flux:heading size="lg">Tabel data kategori</flux:heading>
                <flux:text class="mt-1 text-sm">
                    Kelola dan pantau seluruh kategori tugas Anda dengan mudah.
                    Anda dapat menambahkan kategori tugas baru, memperbarui informasi kategori tugas,
                    mengubah status pengerjaan, serta mengelola kategori tugas sesuai prioritas.
                </flux:text>
            </div>
            <flux:modal.trigger name="tambah">
                <flux:button variant="primary" color="blue" icon="plus">Tambah Kategori</flux:button>
            </flux:modal.trigger>
        </div>

        @if ($successMessage)
        <div x-data x-init="setTimeout(() => $wire.set('successMessage', null), 4000)" class="mb-6">
            <flux:callout variant="success" icon="check-circle" heading="Berhasil" class="mb-6">
                {{ $successMessage }}
            </flux:callout>
        </div>
        @endif

        @if ($errorMessage)
        <div x-data x-init="setTimeout(() => $wire.set('errorMessage', null), 8000)" class="mb-6">
            <flux:callout variant="warning" icon="exclamation-circle" heading="Peringatan" class="mb-6">
                {{ $errorMessage }}
            </flux:callout>
        </div>
        @endif

        <flux:table :paginate="$this->categories" class="w-full">
            <flux:table.columns>
                <flux:table.column>Nama</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column class="w-[10%] whitespace-nowrap" sortable :sorted="$sortBy === 'created_at'"
                    :direction="$sortDirection" wire:click="sort('created_at')">Dibuat</flux:table.column>
                <flux:table.column class="w-[7%] text-right">Aksi</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->categories as $category)
                <flux:table.row :key="$category->id">
                    <flux:table.cell class="max-w-0">
                        {{ $category->name }}
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">
                        @switch($category->is_active)
                        @case(true)
                        <flux:badge color="green">
                            Aktif
                        </flux:badge>
                        @break

                        @case(false)
                        <flux:badge color="zinc">
                            Tidak Aktif
                        </flux:badge>
                        @break
                        @endswitch
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">
                        <div class="whitespace-nowrap text-sm">
                            {{ $category->created_at->format('d M Y') }}
                        </div>
                    </flux:table.cell>

                    <flux:table.cell class="whitespace-nowrap">
                        <flux:dropdown>
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />

                            <flux:menu>
                                <flux:menu.item wire:click="showDetail('{{ $category->id }}')" icon="eye">
                                    Lihat
                                </flux:menu.item>

                                <flux:menu.item wire:click="edit('{{ $category->id }}')" icon="pencil">
                                    Edit
                                </flux:menu.item>

                                <flux:menu.separator />

                                <flux:menu.item wire:click="delete('{{ $category->id }}')"
                                    wire:confirm="Apakah Anda yakin ingin menghapus tugas ini?" variant="danger"
                                    icon="trash">
                                    Hapus
                                </flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>

    @include('pages.category-task.includes.modal')
</div>
