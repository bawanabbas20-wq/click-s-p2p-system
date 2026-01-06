<section>
    <header class="p-6 sm:p-8 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-brand-green/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    {{ __('Personal Information') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __("Update your name, email, and avatar.") }}
                </p>
            </div>
        </div>
    </header>

    <!-- Cropper.js Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6"
        x-data="avatarHandler()">
        @csrf
        @method('patch')

        <!-- Avatar Upload Area -->
        <div>
            <x-input-label for="avatar" :value="__('Avatar')" />
            <div class="mt-3 flex items-center gap-4">
                <!-- Avatar Preview -->
                <div class="relative group cursor-pointer" @click="openLightbox">
                    <img class="h-20 w-20 rounded-xl object-cover ring-2 ring-gray-200 dark:ring-gray-600 transition-transform duration-200 group-hover:scale-105" 
                         :src="previewUrl" 
                         alt="Current avatar">
                    <div class="absolute inset-0 rounded-xl bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                        </svg>
                    </div>
                </div>

                <div class="flex-1">
                    <!-- Hidden File Input -->
                    <input id="avatar" name="avatar" type="file" class="hidden" accept="image/*" @change="handleFileSelect" x-ref="fileInput">
                    
                    <!-- Upload Button -->
                    <button type="button" @click="$refs.fileInput.click()" 
                            class="px-4 py-2 bg-brand-green/10 hover:bg-brand-green/20 text-brand-green rounded-lg font-medium text-sm transition-colors">
                        {{ __('Change Avatar') }}
                    </button>
                    
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" x-show="!fileName">
                        {{ __('JPG, PNG up to 2MB') }}
                    </p>
                    <p class="mt-1 text-xs text-brand-green font-medium" x-show="fileName" x-text="fileName"></p>
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg">
                    <p class="text-sm text-amber-700 dark:text-amber-300 flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ __('Your email is unverified.') }}
                        <button form="send-verification" class="underline font-medium hover:text-amber-800">
                            {{ __('Resend verification') }}
                        </button>
                    </p>
                </div>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 text-sm text-green-600 dark:text-green-400 font-medium">
                        {{ __('A new verification link has been sent.') }}
                    </p>
                @endif
            @endif
        </div>

        <!-- Save Button -->
        <div class="flex items-center gap-4 pt-2">
            <x-primary-button>{{ __('Save Changes') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-green-600 dark:text-green-400 font-medium flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ __('Saved!') }}
                </p>
            @endif
        </div>

        <!-- Lightbox Modal -->
        <div x-show="isLightboxOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-600/80 backdrop-blur-2xl"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @keydown.escape.window="closeLightbox">
            
            <div class="relative max-w-4xl max-h-screen p-4">
                <button type="button" @click="closeLightbox" class="absolute -top-10 right-0 text-white hover:text-gray-300 focus:outline-none">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                <img :src="previewUrl" class="max-w-full max-h-[80vh] rounded-lg shadow-2xl" alt="Full size avatar">
            </div>
        </div>

        <!-- Crop Modal -->
        <div x-show="isCropModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-600/80 backdrop-blur-2xl"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 w-full max-w-md mx-4 flex flex-col">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ __('Crop & Zoom') }}
                </h3>
                
                <div class="relative bg-gray-100 dark:bg-gray-700 rounded-xl overflow-hidden mb-4 h-64 w-full">
                    <img x-ref="cropImage" class="max-w-full" style="display: block; max-width: 100%;">
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="cancelCrop" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition text-sm font-medium">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" @click="saveCrop" class="px-4 py-2 bg-brand-green text-white rounded-lg hover:bg-opacity-90 transition text-sm font-medium">
                        {{ __('Apply') }}
                    </button>
                </div>
            </div>
        </div>

    </form>

    <!-- Cropper.js Script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('avatarHandler', () => ({
                previewUrl: "{{ auth()->user()->avatar ? Storage::url(auth()->user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&size=128&background=10b981&color=ffffff&bold=true&format=svg' }}",
                fileName: '',
                isLightboxOpen: false,
                isCropModalOpen: false,
                cropper: null,
                
                openLightbox() {
                    this.isLightboxOpen = true;
                },
                
                closeLightbox() {
                    this.isLightboxOpen = false;
                },

                handleFileSelect(e) {
                    const file = e.target.files[0];
                    if (!file) return;

                    this.fileName = file.name;
                    
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.$refs.cropImage.src = e.target.result;
                        this.isCropModalOpen = true;
                        
                        this.$nextTick(() => {
                            if (this.cropper) {
                                this.cropper.destroy();
                            }
                            this.cropper = new Cropper(this.$refs.cropImage, {
                                aspectRatio: 1,
                                viewMode: 1,
                                autoCropArea: 1,
                            });
                        });
                    };
                    reader.readAsDataURL(file);
                },

                cancelCrop() {
                    this.isCropModalOpen = false;
                    this.$refs.fileInput.value = '';
                    this.fileName = '';
                    if (this.cropper) {
                        this.cropper.destroy();
                        this.cropper = null;
                    }
                },

                saveCrop() {
                    if (!this.cropper) return;

                    const canvas = this.cropper.getCroppedCanvas({
                        width: 400,
                        height: 400,
                    });

                    this.previewUrl = canvas.toDataURL();
                    
                    canvas.toBlob((blob) => {
                        const file = new File([blob], this.fileName, { type: "image/png" });
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        this.$refs.fileInput.files = dataTransfer.files;
                    });

                    this.isCropModalOpen = false;
                    if (this.cropper) {
                        this.cropper.destroy();
                        this.cropper = null;
                    }
                }
            }));
        });
    </script>
</section>
