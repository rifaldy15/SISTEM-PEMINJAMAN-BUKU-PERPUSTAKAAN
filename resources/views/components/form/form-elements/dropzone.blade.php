@props(['name' => 'file', 'multiple' => false, 'accept' => 'image/png,image/jpeg,image/webp,image/svg+xml', 'label' => 'Upload File'])

<div 
    x-data="{
        isDragging: false,
        files: [],
        handleDrop(e) {
            this.isDragging = false;
            const droppedFiles = Array.from(e.dataTransfer.files);
            this.handleFiles(droppedFiles);
        },
        handleFiles(selectedFiles) {
            const validTypes = '{{ $accept }}'.split(',').map(type => type.trim());
            // Simple validation if accept is set
            const validFiles = selectedFiles.filter(file => {
                 if (validTypes.length === 0 || validTypes.includes('*')) return true;
                 return validTypes.includes(file.type);
            });
            
            if (validFiles.length > 0) {
                if ({{ $multiple ? 'true' : 'false' }}) {
                    this.files = [...this.files, ...validFiles];
                } else {
                    this.files = validFiles;
                }
                
                // Update the hidden input for standard form submission
                this.updateInput();
            }
        },
        updateInput() {
            const dataTransfer = new DataTransfer();
            this.files.forEach(file => dataTransfer.items.add(file));
            $refs.fileInput.files = dataTransfer.files;
        },
        removeFile(index) {
            this.files.splice(index, 1);
            this.updateInput();
        }
    }"
    class="w-full"
>
    <div 
        @drop.prevent="handleDrop($event)"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @click="$refs.fileInput.click()"
        :class="isDragging 
            ? 'border-brand-500 bg-gray-100 dark:bg-gray-800' 
            : 'border-gray-300 bg-gray-50 dark:border-gray-700 dark:bg-gray-900'"
        class="dropzone relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 p-6 transition-colors hover:border-brand-500 cursor-pointer dark:border-gray-700 dark:hover:border-brand-500"
    >
        <!-- Hidden File Input -->
        <input 
            x-ref="fileInput"
            type="file" 
            name="{{ $name }}"
            @change="handleFiles(Array.from($event.target.files))"
            accept="{{ $accept }}"
            @if($multiple) multiple @endif
            class="hidden"
            @click.stop
        />

        <div class="flex flex-col items-center justify-center text-center">
            <!-- Icon -->
            <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
            </div>

            <!-- Text Content -->
            <h4 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white">
                <span x-show="!isDragging">{{ $label }}</span>
                <span x-show="isDragging" x-cloak>Drop file here</span>
            </h4>

            <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                Drag & drop or <span class="text-brand-500">browse</span>
            </p>
            
            <p class="text-xs text-gray-400">Supported: {{ $accept }}</p>
        </div>
    </div>

    <!-- File Preview List -->
    <div x-show="files.length > 0" class="mt-4 space-y-2" x-cloak>
        <template x-for="(file, index) in files" :key="index">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 text-gray-500 dark:bg-gray-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="file.name"></p>
                        <p class="text-xs text-gray-500" x-text="(file.size / 1024).toFixed(2) + ' KB'"></p>
                    </div>
                </div>
                <button 
                    @click="removeFile(index)"
                    type="button"
                    class="rounded-lg p-1 text-gray-400 hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-900/20"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </template>
    </div>
</div>
