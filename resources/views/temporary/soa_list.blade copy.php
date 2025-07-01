<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                <button onclick="window.history.back();" class="px-4 py-2 text-blue-600 rounded-md hover:text-blue-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    ←
                </button>
                {{ __('Statement of Account for: ') . (!empty($customer->first_name) || !empty($customer->last_name) ? $customer->first_name . ' ' . $customer->last_name : $customer->company_name) }}
            </h2>
        </div>
    </x-slot>

    <div class="p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        @if($groupedOrders->isEmpty())
            <div class="text-center py-4">
                <p class="text-gray-600 dark:text-gray-400">No orders found for this customer.</p>
            </div>
        @else
            <!-- Ship Tabs -->
            <div class="tabs">
                <ul class="flex border-b">
                    @foreach($groupedOrders as $ship => $voyageGroups)
                        <li class="mr-1">
                            <a href="#tab-{{ $ship }}" class="tab-link inline-block py-2 px-4 text-blue-500 hover:text-blue-800 rounded-t-md">M/V Everwin Star {{ $ship }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Ship Content -->
            <div class="tab-content">
                @foreach($groupedOrders as $ship => $voyageGroups)
                    <div id="tab-{{ $ship }}" class="hidden">
                        <h3 class="text-lg font-semibold mt-8 mb-4 dark:text-gray-200">Ship: M/V Everwin Star {{ $ship }}</h3>
                        <div class="accordion">
                            @foreach($voyageGroups as $voyage => $orders)
                                @php
                                    $firstOrder = $orders->first();
                                    $origin = $firstOrder ? $firstOrder->origin : '';
                                    $destination = $firstOrder ? $firstOrder->destination : '';
                                @endphp
                                <div class="accordion-item border-b">
                                    <button class="accordion-header w-full text-left py-2 px-4 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 dark:text-gray-200" onclick="toggleAccordion('voyage-{{ $ship }}-{{ $voyage }}')">
                                        Voyage: {{ $voyage }} ({{ $origin }} to {{ $destination }})
                                    </button>
                                    <div id="voyage-{{ $ship }}-{{ $voyage }}" class="accordion-content hidden">
                                        <div class="overflow-x-auto mt-4">
                                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                <thead class="bg-gray-100 dark:bg-gray-800">
                                                    <tr>
                                                        <th class="px-4 py-2">BL No.</th>
                                                        <th class="px-4 py-2">Consignee</th>
                                                        <th class="px-4 py-2">Shipper</th>
                                                        <th class="px-4 py-2">Description</th>
                                                        <th class="px-4 py-2">Freight</th>
                                                        <th class="px-4 py-2">Valuation</th>
                                                        <th class="px-4 py-2">Padlock Fee</th>
                                                        <th class="px-4 py-2">Total Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                    @php 
                                                        $voyageTotal = 0;
                                                        $voyageFreight = 0;
                                                        $voyageValuation = 0;
                                                    @endphp
                                                    @foreach($orders as $order)
                                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                            <td class="px-4 py-2 text-center">{{ $order->orderId }}</td>
                                                            <td class="px-4 py-2 text-center">{{ $order->recName }}</td>
                                                            <td class="px-4 py-2 text-center">{{ $order->shipperName }}</td>
                                                            <td class="px-4 py-2 text-center">
                                                                @foreach ($order->parcels as $parcel)
                                                                    <span>{{ $parcel->quantity }} {{ $parcel->unit }} {{ $parcel->itemName }} {{$parcel->desc}}</span><br>
                                                                @endforeach
                                                            </td>
                                                            <td class="px-4 py-2 text-right">{{ number_format($order->freight, 2) }}</td>
                                                            <td class="px-4 py-2 text-right">{{ number_format($order->valuation, 2) }}</td>
                                                            <td class="px-4 py-2 text-right">
                                                                <input style="width: 100px; border: none; outline: none; text-align:center;" 
                                                                    class="freight-input p-2 border rounded bg-white text-black dark:bg-gray-700 dark:text-white"/>
                                                            </td>
                                                            <td class="px-4 py-2 text-right">{{ number_format($order->totalAmount, 2) }}</td>
                                                        </tr>
                                                        @php $voyageTotal += $order->totalAmount;
                                                             $voyageFreight += $order->freight; 
                                                             $voyageValuation += $order->valuation; 
                                                        @endphp
                                                    @endforeach
                                                    <tr class="bg-gray-50 dark:bg-gray-900 font-semibold">
                                                        <td colspan="4" class="px-4 py-2 text-right">Grand Total:</td>
                                                        <td class="px-4 py-2 text-right">{{ number_format($voyageFreight, 2) }}</td>
                                                        <td class="px-4 py-2 text-right">{{ number_format($voyageValuation, 2) }}</td>
                                                        <td class="px-4 py-2 text-right"></td>
                                                        <td class="px-4 py-2 text-right">{{ number_format($voyageTotal, 2) }}</td>
                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <style>
        .tab-link.active {
            color: #1D4ED8;
            border-bottom: 2px solid #1D4ED8;
            background-color: #F3F4F6;
        }
        
        .dark .tab-link.active {
            color: #60A5FA;
            border-bottom: 2px solid #60A5FA;
            background-color: #374151;
        }

        .accordion-header {
            position: relative;
        }

        .accordion-header::after {
            content: '+';
            position: absolute;
            right: 1rem;
            transition: transform 0.2s ease-in-out;
        }

        .accordion-header.active::after {
            content: '-';
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab functionality
            const tabLinks = document.querySelectorAll('.tab-link');
            const tabContents = document.querySelectorAll('.tab-content > div');

            // Show first tab by default
            if (tabLinks.length > 0) {
                tabLinks[0].classList.add('active');
                tabContents[0].classList.remove('hidden');
            }

            tabLinks.forEach(tab => {
                tab.addEventListener('click', (e) => {
                    e.preventDefault();
                    
                    // Remove active class from all tabs and hide content
                    tabLinks.forEach(t => t.classList.remove('active'));
                    tabContents.forEach(c => c.classList.add('hidden'));
                    
                    // Add active class to clicked tab and show content
                    tab.classList.add('active');
                    const content = document.querySelector(tab.getAttribute('href'));
                    content.classList.remove('hidden');
                });
            });

            // Accordion functionality
            window.toggleAccordion = function(id) {
                const content = document.getElementById(id);
                const header = content.previousElementSibling;
                const isExpanded = !content.classList.contains('hidden');
                
                // Close all accordion items in the same ship tab
                const shipTab = content.closest('.tab-content > div');
                shipTab.querySelectorAll('.accordion-content').forEach(c => {
                    c.classList.add('hidden');
                    c.previousElementSibling.classList.remove('active');
                });
                
                // Toggle the clicked accordion item
                if (!isExpanded) {
                    content.classList.remove('hidden');
                    header.classList.add('active');
                }
            };
        });
    </script>
</x-app-layout>