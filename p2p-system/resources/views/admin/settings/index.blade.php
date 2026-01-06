<x-app-layout>
    <x-slot name="header">
        {{ __('Site Settings') }}
    </x-slot>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-6"
          x-data="{ 
              primaryColor: '{{ old('primary_color', $settings['primary_color'] ?? '#65C34A') }}',
              secondaryColor: '{{ old('secondary_color', $settings['secondary_color'] ?? '#1F6BFF') }}',
              brandingOpen: true,
              emailOpen: true,
              generalOpen: true,
              eyedropperMode: null,
              logoPreview: null,
              logoSelected: false,
              zoomVisible: false,
              zoomX: 0,
              zoomY: 0,
              hoverColor: '#000000',
              handleLogoSelect(event) {
                  const file = event.target.files[0];
                  if (file) {
                      this.logoSelected = true;
                      const reader = new FileReader();
                      reader.onload = (e) => {
                          this.logoPreview = e.target.result;
                      };
                      reader.readAsDataURL(file);
                  }
              },
              showZoom(event) {
                  if (!this.eyedropperMode) return;
                  this.zoomVisible = true;
                  this.updateZoomPreview(event);
              },
              hideZoom() {
                  this.zoomVisible = false;
              },
              updateZoomPreview(event) {
                  if (!this.eyedropperMode) return;
                  
                  const img = document.getElementById('logoImage');
                  const zoomEl = document.getElementById('eyedropperZoom');
                  if (!img || !zoomEl) return;
                  
                  // Position zoom circle near cursor
                  this.zoomX = event.clientX + 20;
                  this.zoomY = event.clientY - 80;
                  
                  // Get color at cursor
                  try {
                      const canvas = document.createElement('canvas');
                      const ctx = canvas.getContext('2d');
                      canvas.width = img.naturalWidth;
                      canvas.height = img.naturalHeight;
                      ctx.fillStyle = '#FFFFFF';
                      ctx.fillRect(0, 0, canvas.width, canvas.height);
                      ctx.drawImage(img, 0, 0);
                      
                      const rect = img.getBoundingClientRect();
                      const scaleX = img.naturalWidth / rect.width;
                      const scaleY = img.naturalHeight / rect.height;
                      const x = Math.floor((event.clientX - rect.left) * scaleX);
                      const y = Math.floor((event.clientY - rect.top) * scaleY);
                      
                      const pixel = ctx.getImageData(x, y, 1, 1).data;
                      this.hoverColor = '#' + [pixel[0], pixel[1], pixel[2]].map(c => c.toString(16).padStart(2, '0')).join('');
                      
                      // Draw zoomed preview
                      const zoomCanvas = document.getElementById('zoomCanvas');
                      if (zoomCanvas) {
                          const zoomCtx = zoomCanvas.getContext('2d');
                          const zoomSize = 60;
                          const zoomFactor = 4;
                          zoomCanvas.width = zoomSize;
                          zoomCanvas.height = zoomSize;
                          
                          // Clear and draw zoomed portion
                          zoomCtx.imageSmoothingEnabled = false;
                          zoomCtx.fillStyle = '#FFFFFF';
                          zoomCtx.fillRect(0, 0, zoomSize, zoomSize);
                          
                          const sourceSize = zoomSize / zoomFactor;
                          zoomCtx.drawImage(canvas, 
                              x - sourceSize/2, y - sourceSize/2, sourceSize, sourceSize,
                              0, 0, zoomSize, zoomSize
                          );
                          
                          // Draw crosshair
                          zoomCtx.strokeStyle = 'rgba(0,0,0,0.5)';
                          zoomCtx.lineWidth = 1;
                          zoomCtx.beginPath();
                          zoomCtx.moveTo(zoomSize/2, 0);
                          zoomCtx.lineTo(zoomSize/2, zoomSize);
                          zoomCtx.moveTo(0, zoomSize/2);
                          zoomCtx.lineTo(zoomSize, zoomSize/2);
                          zoomCtx.stroke();
                      }
                  } catch (e) {
                      console.error('Zoom preview error:', e);
                  }
              },
              pickColorFromLogo(event) {
                  if (!this.eyedropperMode) return;
                  
                  try {
                      const img = document.getElementById('logoImage');
                      if (!img) {
                          console.error('Logo image not found');
                          return;
                      }
                      
                      const canvas = document.createElement('canvas');
                      const ctx = canvas.getContext('2d');
                      canvas.width = img.naturalWidth;
                      canvas.height = img.naturalHeight;
                      
                      // Fill with white background first (so transparent areas don't become black)
                      ctx.fillStyle = '#FFFFFF';
                      ctx.fillRect(0, 0, canvas.width, canvas.height);
                      ctx.drawImage(img, 0, 0);
                      
                      const rect = img.getBoundingClientRect();
                      const scaleX = img.naturalWidth / rect.width;
                      const scaleY = img.naturalHeight / rect.height;
                      const x = Math.floor((event.clientX - rect.left) * scaleX);
                      const y = Math.floor((event.clientY - rect.top) * scaleY);
                      
                      // Get pixel data - try clicked point first
                      let pixel = ctx.getImageData(x, y, 1, 1).data;
                      
                      // If pixel is white (from our background fill), try to find a nearby colored pixel
                      // This helps when clicking near transparent edges
                      if (pixel[0] > 250 && pixel[1] > 250 && pixel[2] > 250) {
                          // Search in a small radius for a non-white pixel
                          const searchRadius = 5;
                          for (let dx = -searchRadius; dx <= searchRadius; dx++) {
                              for (let dy = -searchRadius; dy <= searchRadius; dy++) {
                                  const nx = x + dx;
                                  const ny = y + dy;
                                  if (nx >= 0 && nx < canvas.width && ny >= 0 && ny < canvas.height) {
                                      const nearPixel = ctx.getImageData(nx, ny, 1, 1).data;
                                      // Check if this pixel is not white
                                      if (!(nearPixel[0] > 250 && nearPixel[1] > 250 && nearPixel[2] > 250)) {
                                          pixel = nearPixel;
                                          break;
                                      }
                                  }
                              }
                              if (!(pixel[0] > 250 && pixel[1] > 250 && pixel[2] > 250)) break;
                          }
                      }
                      
                      const hex = '#' + [pixel[0], pixel[1], pixel[2]].map(c => c.toString(16).padStart(2, '0')).join('');
                      
                      if (this.eyedropperMode === 'primary') {
                          this.primaryColor = hex;
                      } else if (this.eyedropperMode === 'secondary') {
                          this.secondaryColor = hex;
                      }
                      
                      this.eyedropperMode = null;
                  } catch (error) {
                      console.error('Error picking color:', error);
                      alert('Could not pick color. The image may have CORS restrictions.');
                      this.eyedropperMode = null;
                  }
              }
          }">
        @csrf
        @method('PUT')

        <!-- Live Preview Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Live Preview') }}</h3>
                <span class="text-xs text-gray-400">{{ __('See how your brand looks in real-time') }}</span>
            </div>
            <div class="flex flex-wrap items-center gap-4">
                <!-- Preview Button -->
                <button type="button" class="px-6 py-2.5 rounded-lg font-semibold text-white text-sm transition-all" :style="{ backgroundColor: primaryColor }">
                    {{ __('Primary Button') }}
                </button>
                <button type="button" class="px-6 py-2.5 rounded-lg font-semibold text-white text-sm transition-all" :style="{ backgroundColor: secondaryColor }">
                    {{ __('Secondary Button') }}
                </button>
                <a href="#" class="text-sm font-medium underline" :style="{ color: primaryColor }">{{ __('Link Style') }}</a>
                <span class="px-3 py-1 rounded-full text-xs font-medium text-white" :style="{ backgroundColor: primaryColor }">{{ __('Badge') }}</span>
                <!-- Preview Card -->
                <div class="flex-1 min-w-[200px] p-4 rounded-xl border-2" :style="{ borderColor: primaryColor + '40' }">
                    <div class="w-8 h-8 rounded-lg mb-2" :style="{ backgroundColor: primaryColor + '20' }"></div>
                    <div class="text-sm font-semibold text-gray-800 dark:text-white">{{ __('Card Title') }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Card description text') }}</div>
                </div>
            </div>
        </div>

        <!-- Branding Section -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <button type="button" @click="brandingOpen = !brandingOpen" class="w-full p-6 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-brand-green/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                        </svg>
                    </div>
                    <div class="text-start">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Branding') }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Logo, colors, and company identity') }}</p>
                    </div>
                </div>
                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="brandingOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            
            <div x-show="brandingOpen" x-collapse class="px-6 pb-6 border-t border-gray-100 dark:border-gray-700">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 pt-6">
                    <!-- Left Column: Company Info -->
                    <div class="space-y-6">
                        <!-- Company Name -->
                        <div>
                            <label for="company_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Company Name') }}</label>
                            <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $settings['company_name'] ?? 'Click P2P') }}" 
                                class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-brand-green focus:border-brand-green">
                            @error('company_name')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Color Pickers -->
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Primary Color -->
                            <div>
                                <label for="primary_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Primary Color') }}</label>
                                <div class="relative">
                                    <input type="color" name="primary_color" id="primary_color" x-model="primaryColor"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    <div class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 cursor-pointer">
                                        <div class="w-10 h-10 rounded-lg shadow-inner" :style="{ backgroundColor: primaryColor }"></div>
                                        <div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Click to change') }}</div>
                                            <div class="font-mono text-sm font-medium text-gray-800 dark:text-white" x-text="primaryColor.toUpperCase()"></div>
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Buttons, links, accents') }}</p>
                            </div>

                            <!-- Secondary Color -->
                            <div>
                                <label for="secondary_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Secondary Color') }}</label>
                                <div class="relative">
                                    <input type="color" name="secondary_color" id="secondary_color" x-model="secondaryColor"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    <div class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 cursor-pointer">
                                        <div class="w-10 h-10 rounded-lg shadow-inner" :style="{ backgroundColor: secondaryColor }"></div>
                                        <div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Click to change') }}</div>
                                            <div class="font-mono text-sm font-medium text-gray-800 dark:text-white" x-text="secondaryColor.toUpperCase()"></div>
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Secondary elements') }}</p>
                            </div>
                        </div>

                        <!-- Color Presets -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Quick Presets') }}</label>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" @click="primaryColor = '#65C34A'; secondaryColor = '#1F6BFF'" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                    🌿 {{ __('Fresh Green') }}
                                </button>
                                <button type="button" @click="primaryColor = '#3B82F6'; secondaryColor = '#8B5CF6'" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                    💙 {{ __('Ocean Blue') }}
                                </button>
                                <button type="button" @click="primaryColor = '#F59E0B'; secondaryColor = '#EF4444'" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                    🔥 {{ __('Warm Sunset') }}
                                </button>
                                <button type="button" @click="primaryColor = '#6366F1'; secondaryColor = '#EC4899'" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                    💜 {{ __('Purple Magic') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Logo Upload & Eyedropper -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Company Logo') }}</label>
                        <div class="relative">
                            <!-- Logo Display Area -->
                            <div class="aspect-square max-w-[200px] rounded-2xl border-2 transition-all overflow-hidden relative"
                                 :class="eyedropperMode ? 'border-brand-green ring-4 ring-brand-green/30' : (logoSelected ? 'border-brand-green border-solid' : 'border-dashed border-gray-300 dark:border-gray-600 hover:border-brand-green')">
                                
                                @if(!empty($settings['company_logo']))
                                    <!-- Existing logo - always render, show/hide based on preview -->
                                    <div class="w-full h-full" x-show="!logoPreview">
                                        <img src="{{ asset('storage/' . $settings['company_logo']) }}" 
                                             alt="Current Logo" 
                                             id="logoImage"
                                             class="w-full h-full object-contain p-4 bg-gray-50 dark:bg-gray-700"
                                             crossorigin="anonymous"
                                             @click="pickColorFromLogo($event); hideZoom()"
                                             @mouseenter="showZoom($event)"
                                             @mousemove="updateZoomPreview($event)"
                                             @mouseleave="hideZoom()"
                                             :class="eyedropperMode ? 'cursor-crosshair' : 'cursor-pointer'"
                                             :style="eyedropperMode ? 'cursor: crosshair' : ''">
                                    </div>
                                    
                                    <!-- Preview of new image -->
                                    <div class="w-full h-full relative" x-show="logoPreview" x-cloak>
                                        <img :src="logoPreview" 
                                             alt="Logo Preview" 
                                             class="w-full h-full object-contain p-4 bg-gray-50 dark:bg-gray-700">
                                        <div class="absolute bottom-2 start-2 end-2 text-center">
                                            <span class="px-2 py-1 bg-brand-green text-white text-xs font-medium rounded-lg">
                                                {{ __('New - Click Save to apply') }}
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <!-- No logo - show upload prompt or preview -->
                                    <div class="w-full h-full" x-show="!logoPreview">
                                        <label for="company_logo" class="w-full h-full flex items-center justify-center bg-gray-50 dark:bg-gray-700 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                            <div class="text-center p-6">
                                                <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('Click to upload') }}</p>
                                            </div>
                                        </label>
                                    </div>
                                    
                                    <!-- Preview of new image when no existing logo -->
                                    <div class="w-full h-full relative" x-show="logoPreview" x-cloak>
                                        <img :src="logoPreview" 
                                             alt="Logo Preview" 
                                             class="w-full h-full object-contain p-4 bg-gray-50 dark:bg-gray-700">
                                        <div class="absolute bottom-2 start-2 end-2 text-center">
                                            <span class="px-2 py-1 bg-brand-green text-white text-xs font-medium rounded-lg">
                                                {{ __('New - Click Save to apply') }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Eyedropper Mode Indicator -->
                            <div x-show="eyedropperMode" 
                                 x-transition
                                 class="absolute -top-2 -end-2 px-2 py-1 bg-brand-green text-white text-xs font-bold rounded-lg shadow-lg flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v11a3 3 0 106 0V4a2 2 0 00-2-2H4zm1 14a1 1 0 100-2 1 1 0 000 2zm5-1.757l4.9-4.9a2 2 0 000-2.828L13.485 5.1a2 2 0 00-2.828 0L5.757 10H10v4.243z" clip-rule="evenodd"/>
                                </svg>
                                <span x-text="eyedropperMode === 'primary' ? '{{ __('Pick Primary') }}' : '{{ __('Pick Secondary') }}'"></span>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                <label for="company_logo" class="px-3 py-1.5 text-sm font-medium text-brand-green bg-brand-green/10 hover:bg-brand-green/20 rounded-lg cursor-pointer transition-colors">
                                    {{ __('Upload') }}
                                </label>
                                <input type="file" name="company_logo" id="company_logo" accept="image/*" class="hidden" @change="handleLogoSelect($event)">
                                
                                @if(!empty($settings['company_logo']))
                                    <a href="{{ route('admin.settings.remove-logo') }}" class="px-3 py-1.5 text-sm font-medium text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" onclick="return confirm('{{ __('Remove current logo?') }}')">
                                        {{ __('Remove') }}
                                    </a>
                                @endif
                            </div>

                            @if(!empty($settings['company_logo']))
                                <!-- Eyedropper Buttons -->
                                <div class="mt-4 p-3 bg-gradient-to-r from-gray-50 to-transparent dark:from-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v11a3 3 0 106 0V4a2 2 0 00-2-2H4zm1 14a1 1 0 100-2 1 1 0 000 2zm5-1.757l4.9-4.9a2 2 0 000-2.828L13.485 5.1a2 2 0 00-2.828 0L5.757 10H10v4.243z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Pick color from logo') }}</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="button" 
                                                @click="eyedropperMode = eyedropperMode === 'primary' ? null : 'primary'"
                                                :class="eyedropperMode === 'primary' ? 'ring-2 ring-brand-green ring-offset-2 dark:ring-offset-gray-800' : ''"
                                                class="flex-1 px-3 py-2 text-xs font-medium rounded-lg transition-all flex items-center justify-center gap-1.5"
                                                :style="{ backgroundColor: primaryColor + '20', color: primaryColor }">
                                            <span class="w-3 h-3 rounded-full" :style="{ backgroundColor: primaryColor }"></span>
                                            {{ __('Primary') }}
                                        </button>
                                        <button type="button" 
                                                @click="eyedropperMode = eyedropperMode === 'secondary' ? null : 'secondary'"
                                                :class="eyedropperMode === 'secondary' ? 'ring-2 ring-brand-green ring-offset-2 dark:ring-offset-gray-800' : ''"
                                                class="flex-1 px-3 py-2 text-xs font-medium rounded-lg transition-all flex items-center justify-center gap-1.5"
                                                :style="{ backgroundColor: secondaryColor + '20', color: secondaryColor }">
                                            <span class="w-3 h-3 rounded-full" :style="{ backgroundColor: secondaryColor }"></span>
                                            {{ __('Secondary') }}
                                        </button>
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 text-center" x-show="eyedropperMode">
                                        {{ __('Click on the logo to pick a color') }}
                                    </p>
                                </div>
                            @endif

                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('PNG, JPG up to 2MB. Recommended: 200x200px') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Email Section -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <button type="button" @click="emailOpen = !emailOpen" class="w-full p-6 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="text-start">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Email Settings') }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Sender name and email configuration') }}</p>
                    </div>
                </div>
                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="emailOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            
            <div x-show="emailOpen" x-collapse class="px-6 pb-6 border-t border-gray-100 dark:border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6">
                    <div>
                        <label for="email_from_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Sender Name') }}</label>
                        <input type="text" name="email_from_name" id="email_from_name" value="{{ old('email_from_name', $settings['email_from_name'] ?? '') }}" 
                            placeholder="Click P2P System"
                            class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-brand-green focus:border-brand-green">
                    </div>

                    <div>
                        <label for="email_from_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Sender Email') }}</label>
                        <input type="email" name="email_from_address" id="email_from_address" value="{{ old('email_from_address', $settings['email_from_address'] ?? '') }}" 
                            placeholder="noreply@company.com"
                            class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-brand-green focus:border-brand-green">
                    </div>
                </div>
                <div class="mt-4 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-amber-700 dark:text-amber-300">{{ __('SMTP settings should be configured in the .env file for security.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- General Section -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <button type="button" @click="generalOpen = !generalOpen" class="w-full p-6 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div class="text-start">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('General') }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Currency, language, and timezone') }}</p>
                    </div>
                </div>
                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="generalOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            
            <div x-show="generalOpen" x-collapse class="px-6 pb-6 border-t border-gray-100 dark:border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-6">
                    <div>
                        <label for="default_currency" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Default Currency') }}</label>
                        <select name="default_currency" id="default_currency" class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-brand-green focus:border-brand-green">
                            <option value="USD" {{ ($settings['default_currency'] ?? 'USD') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                            <option value="IQD" {{ ($settings['default_currency'] ?? '') == 'IQD' ? 'selected' : '' }}>IQD ({{ __('Iraqi Dinar') }})</option>
                        </select>
                    </div>

                    <div>
                        <label for="default_locale" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Default Language') }}</label>
                        <select name="default_locale" id="default_locale" class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-brand-green focus:border-brand-green">
                            <option value="en" {{ ($settings['default_locale'] ?? 'en') == 'en' ? 'selected' : '' }}>English</option>
                            <option value="ar" {{ ($settings['default_locale'] ?? '') == 'ar' ? 'selected' : '' }}>العربية</option>
                            <option value="ku" {{ ($settings['default_locale'] ?? '') == 'ku' ? 'selected' : '' }}>کوردی</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Timezone') }}</label>
                        <div x-data="{ 
                                open: false, 
                                search: '', 
                                selected: '{{ $settings['timezone'] ?? 'Asia/Baghdad' }}',
                                groups: {{ json_encode($timezones) }},
                                get filteredGroups() {
                                    if (this.search === '') return this.groups;
                                    let result = {};
                                    for (const [continent, cities] of Object.entries(this.groups)) {
                                        const filteredCities = cities.filter(c => c.toLowerCase().includes(this.search.toLowerCase()));
                                        if (filteredCities.length > 0) result[continent] = filteredCities;
                                    }
                                    return result;
                                }
                            }" 
                            @click.away="open = false" 
                            class="relative"
                            x-cloak>
                            
                            <input type="hidden" name="timezone" :value="selected">
                            
                            <button type="button" @click="open = !open" 
                                    class="relative w-full py-3 px-4 text-left bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm cursor-default focus:outline-none focus:ring-2 focus:ring-brand-green focus:border-brand-green sm:text-sm">
                                <span class="block truncate text-gray-900 dark:text-white" x-text="selected"></span>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </button>

                            <div x-show="open" 
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="absolute z-[60] mt-1 w-full bg-white dark:bg-gray-800 shadow-2xl max-h-80 rounded-xl py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
                                 style="display: none;">
                                
                                <div class="sticky top-0 z-10 bg-white dark:bg-gray-800 p-2 border-b border-gray-100 dark:border-gray-700">
                                    <input type="text" x-model="search" placeholder="Search timezone..." 
                                           @click.stop
                                           class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:ring-brand-green focus:border-brand-green placeholder-gray-400">
                                </div>

                                <template x-for="(cities, continent) in filteredGroups" :key="continent">
                                    <div>
                                        <div class="px-3 py-1 bg-gray-50 dark:bg-gray-700/50 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider" x-text="continent"></div>
                                        <template x-for="city in cities" :key="city">
                                            <div @click="selected = city; open = false; search = ''"
                                                 class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-brand-green/10 dark:hover:bg-brand-green/20 transition-colors"
                                                 :class="selected === city ? 'text-brand-green font-semibold' : 'text-gray-900 dark:text-gray-100'">
                                                <span class="block truncate" x-text="city"></span>
                                                <span x-show="selected === city" class="absolute inset-y-0 right-0 flex items-center pr-4 text-brand-green">
                                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                
                                <div x-show="Object.keys(filteredGroups).length === 0" class="px-3 py-4 text-center text-gray-500 dark:text-gray-400 text-sm">
                                    No results found
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Eyedropper Zoom Circle (Fixed Position) -->
        <div id="eyedropperZoom"
             x-show="zoomVisible && eyedropperMode"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90"
             class="fixed z-50 pointer-events-none"
             :style="'left: ' + zoomX + 'px; top: ' + zoomY + 'px;'">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl border-2 border-gray-200 dark:border-gray-600 overflow-hidden">
                <!-- Zoomed Canvas -->
                <canvas id="zoomCanvas" width="60" height="60" class="block rounded-t-lg"></canvas>
                <!-- Color Preview -->
                <div class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                    <div class="w-5 h-5 rounded-md border border-gray-300 dark:border-gray-500 shadow-inner" :style="'background-color: ' + hoverColor"></div>
                    <span class="font-mono text-xs font-medium text-gray-700 dark:text-gray-200" x-text="hoverColor.toUpperCase()"></span>
                </div>
            </div>
        </div>

        <!-- Sticky Save Button -->
        <div class="sticky bottom-4 flex justify-end">
            <button type="submit" class="inline-flex items-center px-8 py-3 bg-brand-green border border-transparent rounded-xl font-bold text-sm text-white uppercase tracking-widest hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-brand-green focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition-all">
                <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ __('Save Settings') }}
            </button>
        </div>
    </form>
</x-app-layout>
