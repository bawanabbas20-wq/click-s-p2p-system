<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order #{{ $purchaseRequest->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body class="bg-gray-100 p-8">

    <div class="max-w-4xl mx-auto bg-white p-8 shadow-lg print:shadow-none">
        
        <!-- Header -->
        <div class="flex justify-between items-start mb-8 border-b pb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">PURCHASE ORDER</h1>
                <p class="text-sm text-gray-500 mt-1">PO Number: PO-{{ date('Y') }}-{{ str_pad($purchaseRequest->id, 5, '0', STR_PAD_LEFT) }}</p>
                <p class="text-sm text-gray-500">Date: {{ date('F j, Y') }}</p>
            </div>
            <div class="text-right">
                <h2 class="text-xl font-bold text-gray-700">Click Company</h2>
                <p class="text-sm text-gray-600">Baghdad, Iraq</p>
                <p class="text-sm text-gray-600">procurement@click.iq</p>
            </div>
        </div>

        <!-- Vendor & Ship To -->
        <div class="grid grid-cols-2 gap-8 mb-8">
            <div>
                <h3 class="text-sm font-bold text-gray-500 uppercase mb-2">Vendor</h3>
                <p class="font-semibold text-lg">{{ $purchaseRequest->chosenOffer->vendor_name }}</p>
                {{-- If we had vendor address in DB, we would put it here --}}
                <p class="text-gray-600">Vendor ID: #{{ $purchaseRequest->chosenOffer->vendor_id }}</p>
            </div>
            <div class="text-right">
                <h3 class="text-sm font-bold text-gray-500 uppercase mb-2">Ship To / Request By</h3>
                <p class="font-semibold">{{ $purchaseRequest->user->name }}</p>
                <p class="text-gray-600">Click Company HQ</p>
                <p class="text-gray-600">Baghdad</p>
            </div>
        </div>

        <!-- Items Table -->
        <table class="w-full mb-8 border-collapse">
            <thead>
                <tr class="bg-gray-50 border-y border-gray-200">
                    <th class="py-3 px-4 text-left font-semibold text-gray-600">Description</th>
                    <th class="py-3 px-4 text-center font-semibold text-gray-600">Qty</th>
                    <th class="py-3 px-4 text-right font-semibold text-gray-600">Unit Price</th>
                    <th class="py-3 px-4 text-right font-semibold text-gray-600">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-gray-100">
                    <td class="py-4 px-4">
                        <p class="font-medium text-gray-800">{{ $purchaseRequest->item_name }}</p>
                        <p class="text-sm text-gray-500">{{ $purchaseRequest->justification }}</p>
                    </td>
                    <td class="py-4 px-4 text-center text-gray-600">1</td>
                    <td class="py-4 px-4 text-right text-gray-600">
                        {{ number_format($purchaseRequest->chosenOffer->price, 2) }} {{ $purchaseRequest->chosenOffer->currency }}
                    </td>
                    <td class="py-4 px-4 text-right font-bold text-gray-800">
                        {{ number_format($purchaseRequest->chosenOffer->price, 2) }} {{ $purchaseRequest->chosenOffer->currency }}
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="py-4 px-4 text-right font-bold text-gray-700">Total:</td>
                    <td class="py-4 px-4 text-right font-bold text-xl text-brand-blue">
                         {{ number_format($purchaseRequest->chosenOffer->price, 2) }} {{ $purchaseRequest->chosenOffer->currency }}
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- Footer / Approval -->
        <div class="mt-12 pt-8 border-t border-gray-200">
            <div class="grid grid-cols-2 gap-12">
                <div>
                    <p class="text-sm font-bold text-gray-500 mb-8">Authorized By</p>
                    <div class="border-b border-gray-400 w-3/4 mb-2"></div>
                    <p class="text-xs text-gray-500">Signature</p>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-500 mb-2">Terms & Conditions</p>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        1. Payment will be made upon delivery and inspection.<br>
                        2. Vendor must reference PO number on all invoices.<br>
                        3. Goods must meet standard quality requirements.
                    </p>
                </div>
            </div>
        </div>

        <!-- Print Actions -->
        <div class="mt-8 text-center no-print">
            <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 shadow-md">
                Print Purchase Order
            </button>
            <button onclick="window.close()" class="ml-4 text-gray-600 hover:text-gray-800 underline">
                Close Window
            </button>
        </div>

    </div>

    <script>
        // Auto print prompt when opened
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
