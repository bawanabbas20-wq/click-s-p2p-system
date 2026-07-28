<!DOCTYPE html>
<html lang="en" dir="{{ in_array(app()->getLocale(), ['ar', 'ku']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Purchase Order') }} - PO-{{ date('Y') }}-{{ str_pad($purchaseRequest->id, 5, '0', STR_PAD_LEFT) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        
        * { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
        
        @page { 
            size: A4; 
            margin: 10mm;
            /* Hide browser headers and footers */
            margin-top: 0;
            margin-bottom: 0;
        }
        
        @media print {
            .no-print { display: none !important; }
            html, body { 
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .print-container {
                box-shadow: none !important;
                max-width: 100% !important;
                border: none !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 p-4 md:p-8">

    <div class="print-container max-w-[750px] mx-auto bg-white shadow-xl border border-slate-200">
        
        {{-- Header --}}
        <div class="px-8 pt-6 pb-4 border-b border-slate-200">
            <div class="flex justify-between items-start">
                {{-- Company Info --}}
                <div class="flex items-center gap-4">
                    @if(!empty($siteSettings['company_logo']))
                        <img src="{{ asset('storage/' . $siteSettings['company_logo']) }}" alt="Logo" class="h-12 w-auto object-contain" />
                    @endif
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">{{ $siteSettings['company_name'] ?? 'Company Name' }}</h2>
                        <p class="text-xs text-slate-500">{{ $siteSettings['company_email'] ?? '' }}</p>
                    </div>
                </div>
                
                {{-- PO Title --}}
                <div class="text-right">
                    <h1 class="text-2xl font-bold text-slate-800">PURCHASE ORDER</h1>
                    <p class="text-sm mt-1"><span class="text-slate-400">{{ __('PO No.') }}:</span> <span class="font-semibold text-slate-700">PO-{{ date('Y') }}-{{ str_pad($purchaseRequest->id, 5, '0', STR_PAD_LEFT) }}</span></p>
                    <p class="text-sm"><span class="text-slate-400">{{ __('Date') }}:</span> <span class="font-medium text-slate-600">{{ date('d M Y') }}</span></p>
                </div>
            </div>
        </div>
        
        {{-- Vendor & Requester Section --}}
        <div class="px-8 py-4 grid grid-cols-2 gap-8 border-b border-slate-100">
            {{-- Vendor --}}
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('Vendor') }}</p>
                <p class="text-base font-bold text-slate-800">{{ $purchaseRequest->chosenOffer->vendor_name }}</p>
                @if($purchaseRequest->chosenOffer->vendor)
                    @if($purchaseRequest->chosenOffer->vendor->contact_person)
                        <p class="text-xs text-slate-600">{{ __('Attn') }}: {{ $purchaseRequest->chosenOffer->vendor->contact_person }}</p>
                    @endif
                    @if($purchaseRequest->chosenOffer->vendor->email)
                        <p class="text-xs text-slate-500">{{ $purchaseRequest->chosenOffer->vendor->email }}</p>
                    @endif
                    @if($purchaseRequest->chosenOffer->vendor->phone)
                        <p class="text-xs text-slate-500">{{ $purchaseRequest->chosenOffer->vendor->phone }}</p>
                    @endif
                @endif
            </div>
            
            {{-- Ship To / Requester --}}
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('Ship To / Requested By') }}</p>
                <p class="text-base font-bold text-slate-800">{{ $purchaseRequest->user->name }}</p>
                <p class="text-xs text-slate-600">{{ $siteSettings['company_name'] ?? '' }}</p>
                <p class="text-xs text-slate-500">{{ $purchaseRequest->user->email }}</p>
                @if($purchaseRequest->date_wanted)
                    <p class="text-xs text-slate-500">{{ __('Required by') }}: {{ \Carbon\Carbon::parse($purchaseRequest->date_wanted)->format('d M Y') }}</p>
                @endif
            </div>
        </div>
        
        {{-- Items Table --}}
        <div class="px-8 py-4">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-100 text-slate-600">
                        <th class="py-2 px-3 text-start text-xs font-semibold uppercase">#</th>
                        <th class="py-2 px-3 text-start text-xs font-semibold uppercase">{{ __('Item Description') }}</th>
                        <th class="py-2 px-3 text-center text-xs font-semibold uppercase w-16">{{ __('Qty') }}</th>
                        <th class="py-2 px-3 text-end text-xs font-semibold uppercase">{{ __('Unit Price') }}</th>
                        <th class="py-2 px-3 text-end text-xs font-semibold uppercase">{{ __('Amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-slate-100">
                        <td class="py-3 px-3 text-slate-500">1</td>
                        <td class="py-3 px-3">
                            <p class="font-semibold text-slate-800">{{ $purchaseRequest->item_name }}</p>
                            @if($purchaseRequest->justification)
                                <p class="text-xs text-slate-500 mt-0.5">{{ Str::limit($purchaseRequest->justification, 60) }}</p>
                            @endif
                        </td>
                        <td class="py-3 px-3 text-center text-slate-700">1</td>
                        <td class="py-3 px-3 text-end text-slate-700">
                            @if($purchaseRequest->chosenOffer->currency === 'USD')
                                ${{ number_format($purchaseRequest->chosenOffer->price, 2) }}
                            @else
                                {{ number_format($purchaseRequest->chosenOffer->price, 0) }} IQD
                            @endif
                        </td>
                        <td class="py-3 px-3 text-end font-semibold text-slate-800">
                            @if($purchaseRequest->chosenOffer->currency === 'USD')
                                ${{ number_format($purchaseRequest->chosenOffer->price, 2) }}
                            @else
                                {{ number_format($purchaseRequest->chosenOffer->price, 0) }} IQD
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            
            {{-- Totals --}}
            <div class="flex justify-end mt-2">
                <div class="w-56">
                    <div class="flex justify-between py-1.5 text-sm border-b border-slate-100">
                        <span class="text-slate-500">{{ __('Subtotal') }}</span>
                        <span class="font-medium text-slate-700">
                            @if($purchaseRequest->chosenOffer->currency === 'USD')
                                ${{ number_format($purchaseRequest->chosenOffer->price, 2) }}
                            @else
                                {{ number_format($purchaseRequest->chosenOffer->price, 0) }} IQD
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between py-2 bg-slate-50 px-2 -mx-2 rounded">
                        <span class="font-bold text-slate-800">{{ __('TOTAL') }}</span>
                        <span class="text-lg font-bold text-slate-800">
                            @if($purchaseRequest->chosenOffer->currency === 'USD')
                                ${{ number_format($purchaseRequest->chosenOffer->price, 2) }}
                            @else
                                {{ number_format($purchaseRequest->chosenOffer->price, 0) }} IQD
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Terms Section --}}
        <div class="px-8 py-4 border-t border-slate-100">
            <div class="grid grid-cols-2 gap-8 text-xs">
                <div>
                    <p class="font-bold text-slate-400 uppercase tracking-wider mb-2">{{ __('Terms & Conditions') }}</p>
                    <ol class="text-slate-600 space-y-1 list-decimal list-inside">
                        <li>{{ __('Payment upon delivery and inspection') }}</li>
                        <li>{{ __('Reference PO number on all invoices') }}</li>
                        <li>{{ __('Goods must meet quality requirements') }}</li>
                    </ol>
                </div>
                <div>
                    <p class="font-bold text-slate-400 uppercase tracking-wider mb-2">{{ __('Notes') }}</p>
                    <p class="text-slate-600">{{ __('This PO is subject to standard terms of') }} {{ $siteSettings['company_name'] ?? 'the company' }}.</p>
                </div>
            </div>
        </div>
        
        {{-- Signature Section --}}
        <div class="px-8 py-5 border-t border-slate-200">
            <div class="grid grid-cols-2 gap-12">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-8">{{ __('Authorized By') }}</p>
                    <div class="border-b border-slate-300 mb-1"></div>
                    <div class="flex justify-between text-xs text-slate-400">
                        <span>{{ __('Signature') }}</span>
                        <span>{{ __('Date') }}</span>
                    </div>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-8">{{ __('Received By') }}</p>
                    <div class="border-b border-slate-300 mb-1"></div>
                    <div class="flex justify-between text-xs text-slate-400">
                        <span>{{ __('Signature') }}</span>
                        <span>{{ __('Date') }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Footer --}}
        <div class="px-8 py-3 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-400">
                {{ __('Generated') }}: {{ date('d/m/Y H:i') }} &bull; {{ $siteSettings['company_name'] ?? '' }}
            </p>
        </div>
    </div>

    {{-- Print Actions --}}
    <div class="no-print max-w-[750px] mx-auto mt-6 flex justify-center items-center gap-6">
        <button onclick="window.print()" 
                class="inline-flex items-center gap-2 px-6 py-2.5 bg-slate-800 text-white font-semibold rounded-lg hover:bg-slate-700 shadow-lg transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            {{ __('Print Purchase Order') }}
        </button>
        <button onclick="window.close()" class="text-slate-500 hover:text-slate-700 font-medium">
            {{ __('Close') }}
        </button>
    </div>

</body>
</html>
